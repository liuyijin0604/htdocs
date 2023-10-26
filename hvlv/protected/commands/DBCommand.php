<?php
class DBCommand extends CConsoleCommand {
	private $db;
	private $args;
	private $tmp;
	private $debug = false;

	public function run($args) {
		$this->db = Yii::app()->getDb();
		$this->args = $args;
		$this->tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
		foreach($this->args as $ag){
			if($ag == '-d') $this->debug = true;
		}
		if(!empty($args[0]) && method_exists($this, $args[0])){
			$this->{$args[0]}();
		}
	}

	public function _loadXlsData($f){
		if(!is_file($f)) die($f." not exist\n");
		return oExcel::getAllData($f, false, true);
	}

	public function fixBillingDescForTollStartrack(){
		// for toll
		$billings = BillingLine::model()->findAll('org_id = :oid AND charge_code = :ccode AND dpmt = :dpmt',
				[':oid' => Org::ORGID_COURIER_TOLL,
				':ccode' => Consol::AU_LOCAL_DELIVERY_COST_GL_CODE,
				':dpmt' => Invoice::DPMT_IMPORT]);
		if ( !empty($billings) ) {
			foreach ( $billings as $billing ) {
				if ( $billing->link_id > 0 ) {
					$p = ImParcel::model()->findByPk($billing->link_id);
					if ( !empty($p) ) {
						$billing->desc = $p->ref;
						$billing->update('desc');
						echo 'done for billing '. $billing->id . PHP_EOL;
					}
				}
			}
		}

		// for startrack
		$billings = BillingLine::model()->findAll('org_id = :oid AND charge_code = :ccode AND dpmt = :dpmt',
			[':oid' => Org::ORGID_COURIER_STARTRACK,
				':ccode' => Consol::AU_LOCAL_DELIVERY_COST_GL_CODE,
				':dpmt' => Invoice::DPMT_IMPORT]);
		if ( !empty($billings) ) {
			foreach ( $billings as $billing ) {
				if ( $billing->link_id > 0 ) {
					$p = ImParcel::model()->findByPk($billing->link_id);
					if ( !empty($p) ) {
						$billing->desc = $p->ref;
						$billing->update('desc');
						echo 'done for billing '. $billing->id . PHP_EOL;
					}
				}
			}
		}

		echo 'all done' . PHP_EOL;
	}

	public function removeDuplicateAupostBilling(){
		$imconsoles = ImcoConsol::model()->findAll('created >= :ldate',[':ldate' => '2017-07-01']);
		echo 'all ' . count($imconsoles) . ' to be processed' . PHP_EOL;

		$cid = Org::ORGID_COURIER_AUPOST;
		foreach ( $imconsoles as $console ) {

			$billings = BillingLine::model()->findAll('billing_ref = :cref AND org_id = :oid AND charge_code = :ccode', [':cref' => $console->no,':oid' => $cid,':ccode' => Consol::AU_LOCAL_DELIVERY_COST_GL_CODE ]);
			if ( count($billings) > 1 ) {
				foreach ( $billings as $billing ) {
					if ( $billing->billing_ref == $billing->billing_cref && $billing->actual_amount == 0.0 ) {
						echo 'got duplicate for : ' . $console->no . ' removed ' . PHP_EOL;
						$billing->delete();
						break;
					}
				}
			}
			echo 'processed for : ' . $console->no . PHP_EOL;
		}
		echo 'all done' . PHP_EOL;
	}

	public function unlinkShipmentsFromConsole(){
		$id = $this->prompt('console id: ');
		if ( empty($id) ) {
			echo 'invalid console id : ' . $id . PHP_EOL;
			return;
		}

		$id = trim($id);
		$consol = Consol::model()->findByPk($id);
		if ( empty($consol) ) {
			echo 'console not found : ' . $id . PHP_EOL;
			return;
		}

		$sql = "UPDATE shipment SET consol_id = 0 WHERE consol_id = ".$id;
		$c = $this->db->createCommand($sql);
		$c->execute();

	}

	/**
	 * close last months invoices
	 */
	public function closeLastMonthInvoices(){
		$rowCount = Invoice::closeLastMonthInvoices();
		echo $rowCount . ' invoices have been closed' . PHP_EOL;
	}

	public function fixJobLineBillingNoChargeCode(){
		$jobLines = JobLine::model()->findAll('id > 0 ');
		$allCount = 0;
		foreach ( $jobLines as $jobLine ) {
			$job = EdiJob::model()->findByPk($jobLine->job_id);

			$billing = BillingLine::model()->find('link_id = :pid AND type = :type',[':pid' => $jobLine->id,':type' => BillingLine::BILLING_TYPE_AIR_SEA ]);
			if ( !empty($billing) ) {
				if ( empty($billing->charge_code) ) {
					$billing->charge_code = EdiJob::getSupplierCostCode($jobLine->ccode, ($job->dpmt == Job::DPMT_3PL) );
					$billing->update('charge_code');
					echo 'billing line : ' . $billing->id . ' updated' . PHP_EOL;
					$allCount++;
				}
			}
		}
		echo 'all ' . $allCount  . ' updated' . PHP_EOL;
	}

	public function getTollCostForShipment(){
		$id = $this->prompt('shipment id: ');
		$p = ImParcel::model()->findByPk($id);
		$tollApi = new TollAPI(true,true);
		$cost = $tollApi->getCost($p);

		echo json_encode( $cost);

	}

	// try to fix toll shipment accrual cost
	public function fixTollCostIssue(){
		$ps = ImParcel::model()->findAll(' (ref like :ref OR ref like :refp) AND created > :ldate',[':ref' => '%AWUJ%',':refp' => '%AWNL%',':ldate' => '2017-02-01']);
		echo 'found ' . count($ps) . ' to be fixed' . PHP_EOL;
		$tollApi = new TollAPI(true,true);
		$consoleIds = array();
		foreach ( $ps as $p ) {
			$cost = $tollApi->getCost($p);
			$ts = Tranship::model()->find('pid = :pid AND org_id = :oid AND type = 80',[':pid' => $p->id,':oid' => Org::ORGID_COURIER_TOLL]);
			if ( empty($ts) ) {
				$ts = new Tranship;
				$ts->pid = $p->id;
				$ts->org_id = Org::ORGID_COURIER_TOLL;  // for toll
				$ts->type = 80;  // shipment transfer to a different delivery courier
				$ts->status = 19; // in finally moving status
				$ts->man_id = $p->man_id;
				$ts->connote = $p->ref;
				$ts->time = date('Y-m-d H:i:s');
			}
			if ( $cost > 0 ) {
				$ts->cost = round($cost, 2);
				$ts->save();
				$consoleIds[] = $p->consol_id;
			}


			echo  $p->ref .  'be fixed' . PHP_EOL;

		}

		// update related consol billing
		$consoleIds = array_unique($consoleIds);
		echo 'found ' . count($consoleIds) . ' console to be fixed' . PHP_EOL;
		ImcoConsol::updateImportConsoleBilling($consoleIds);

		echo 'Al done' . PHP_EOL;

	}

	// try to fix aupost shipment accrual cost
	public function fixAupostCostIssue(){
		$importConsoles = ImcoConsol::model()->findAll('created >= :hdate',[':hdate' => '2017-07-01']);

		// update related consol billing

		echo 'found ' . count($importConsoles) . ' console to be fixed' . PHP_EOL;

		foreach ( $importConsoles as $consol ) {
			echo 'processed for : ' . $consol->no . PHP_EOL;
			ImcoConsol::updateImportConsoleBilling([$consol->id]);
		}

		echo 'Al done' . PHP_EOL;

	}

	// try to fix aupost shipment accrual cost
	public function shrinkFastwayCostIssue(){
		$importConsoles = Consol::model()->findAll('(type = 15 or type = 70 ) and created >= :hdate',[':hdate' => '2017-07-01']);

		// update related consol billing
		echo 'found ' . count($importConsoles) . ' console to be fixed' . PHP_EOL;
		$cid = Org::ORGID_COURIER_FASTWAY;
		foreach ( $importConsoles as $console ) {
			$billings = BillingLine::model()->find('billing_ref = :cref AND org_id = :oid AND charge_code = :ccode', [':cref' => $console->no,':oid' => $cid,':ccode' => Consol::AU_LOCAL_DELIVERY_COST_GL_CODE ]);
			if ( !empty($billings) ) {
				$billings->accrual_amount = round($billings->accrual_amount * 0.9075,2);
				$billings->update('accrual_amount');
			}
			echo 'processed for : ' . $console->no . PHP_EOL;
		}
		echo 'Al done' . PHP_EOL;
	}

	// try to fix aupost shipment accrual cost
	public function removeDupFastwayCostIssue(){
		$importConsoles = ImcoConsol::model()->findAll('created >= :hdate',[':hdate' => '2017-07-01']);

		// update related consol billing
		echo 'found ' . count($importConsoles) . ' console to be fixed' . PHP_EOL;
		$cid = Org::ORGID_COURIER_FASTWAY;
		foreach ( $importConsoles as $console ) {
			$billings = BillingLine::model()->findAll('billing_ref = :cref AND org_id = :oid AND charge_code = :ccode', [':cref' => $console->no,':oid' => $cid,':ccode' => Consol::AU_LOCAL_DELIVERY_COST_GL_CODE ]);
			if ( count($billings) > 1 ) {
				foreach ( $billings as $billing ) {
					if ( $billing->billing_ref == $billing->billing_cref && $billing->actual_amount == 0.0 ) {
						echo 'got duplicate for : ' . $console->no . ' removed ' . PHP_EOL;
						$billing->delete();
						break;
					}
				}
			}
			echo 'processed for : ' . $console->no . PHP_EOL;
		}
		echo 'Al done' . PHP_EOL;
	}

	public function fixPlledgerAirFreightRevenueType(){
		$pls = PlLedger::model()->findAll("model = :model",[':model' => 'JobLine']);
		$allCount = 0;
		foreach ( $pls as $pl ) {
			if ( $pl->dpmt == 40 || $pl->dpmt == 30 ) {
				if ( !empty($pl->grp1) ) {
					$job = EdiJob::model()->findByPk( $pl->grp1 );
					if ( !empty($job) ) {
						$pl->dpmt = !empty($job) ? $job->dpmt : Invoice::DPMT_AIRSEA;
						$pl->update('dpmt');
						$allCount++;
						echo 'Pl Ledger : ' . $pl->id . ' done' . PHP_EOL;
					}
				}
			}
		}
		echo 'ALL : ' . $allCount . ' done' . PHP_EOL;
	}

	public function pushOldJobLine2Plledger(){
		$jobLines = JobLine::model()->findAll('id > 0 ');
		$allCount = 0;
		foreach ( $jobLines as $jobLine ) {

			$billing = BillingLine::model()->find('link_id = :pid AND sync_xero = 0 AND type = :type',[':pid' => $jobLine->id,':type' => BillingLine::BILLING_TYPE_AIR_SEA ]);

			// remove actual amount not existing billing line and create a new one again
			if ( !empty($billing) && $billing->actual_amount == 0 ) {

				// delete related ledger
				$chargeCodeModel = Chargecode::model()->find('status = 1 AND code = :code', [':code' => $billing->charge_code]);
				$chargeCodeId = 0;
				if (!empty($chargeCodeModel)) $chargeCodeId = $chargeCodeModel->id;
				$plLedger = PlLedger::model()->find('fid = :fid AND model = :model AND gl = :gl', [':fid' => $billing->id, ':model' => 'BillingLine', ':gl' => $chargeCodeId]);
				if (!empty($plLedger)) $plLedger->delete();
				$billing->delete();
				$billing = null;

				echo 'remove old one : ' . $billing->no . PHP_EOL;
			}

			if ( empty($billing) ) {
				BillingLine::saveExportAirFreightBilling($jobLine);
				echo 'recreate for ' . $jobLine->id . ' done' . PHP_EOL;
			}
		}
		echo 'all ' . $allCount  . ' updated' . PHP_EOL;
	}

	public function fixBFEAgentShipments(){
		$ps = ImParcel::model()->findAll('created >= :fdate',[':fdate' => '2017-05-22']);
		$index = 1;
		foreach ( $ps as $p ) {
			if ( empty($p->trans)) continue;
			$t = $p->trans[0];
			if ( $t->org_id = Org::ORGID_COURIER_FASTWAY ) {
				$p->agent_id = 1206;
				$p->update('agent_id');
				echo 'done for : ' . $p->ref . PHP_EOL;
				$index++;
			}
		}
		echo $index .  'all done';
	}

	public function updateStartrackShipment(){
		$id = $this->prompt('shipment id: ');
		$p = ImParcel::model()->findByPk($id);
		if ( !empty($p) &&  $p->type == 10 ) {
			if ( isset($p->mdata['ss_shipment_id']) && !empty($p->mdata['ss_shipment_id']) ) {
							if(empty($p->mdata['ss_shipment_items'])){
								echo 'failed';
								return;
							}
				$ss = new StarTrackAPI('syd', true);
				$result = $ss->updateShipment($p->mdata['ss_shipment_id'], $p);
								$result=(array) $result;
				if ( !empty($result) ) {
					echo ' failed';
				} else {
					echo ' good finished';
				}
			} else {
				echo 'not a found';
			}
		} else {
			echo 'imparcel : ' . $id . ' not found';
		}
		echo 'done';
	}

	public function fixAu2014LabelByConsole(){
		$id = $this->prompt('Consol ID: ');
		$c = ImcoConsol::model()->findByPk($id);
		$allCount = 0;
		foreach ( $c->shipments as $p ) {
			// ignore eparcel label
			echo 'Found : ' . $p->ref . PHP_EOL;
			if ( empty($p->ref) ) continue;
			if ( stripos($p->ref, 'AMQ') === false ) continue;
			$p->mdata['test_aupost_2014'] = 1;
			$p->updateMeta();
			$allCount++;
		}
		echo 'All  : ' . $allCount . ' done' . PHP_EOL;
	}

	public function fixAu2014LabelTestIssue(){
		$shipments = Shipment::model()->findAll('type = 10 AND created >= :tdate',[':tdate' => '2017-05-29 17:00:00']);
		foreach ( $shipments as $p ) {
			// ignore eparcel label
			echo 'Found : ' . $p->ref . PHP_EOL;
			if ( empty($p->ref) ) continue;
			if ( stripos($p->ref, 'AMQ') !== false ) continue;
			if ( isset($p->mdata['test_aupost_2014']))  {
				echo  $p->ref . 'Processed ' . PHP_EOL;
				// unset($p->mdata['test_aupost_2014']);
				// $p->updateMeta();
			}
		}
	}

	public function reformatOldImportOthersInvoiceGSTMissingIssue(){
		$invoices = Invoice::model()->findAll('type = 40 AND status < 10 AND dpmt = 10 AND date >= :ldate',[':ldate' => '2017-07-01']);
		$all = count($invoices);
		echo 'total invoices ' . $all . PHP_EOL;
		$finished = 0;
		foreach ( $invoices as $k =>  $invoice ) {
			$finished++;
			// try to find tax field is empty and total amount not equal need to be fixed
			$needFixed = true;
			$totalAmt = 0;
			foreach ( $invoice->lines as $line ) {
				$totalAmt += $line->amount * $line->qty;
				if ( !empty($line->tax) ) {
					$needFixed = false;
					break;
				}
			}
			if ( $needFixed && $invoice->total >  $totalAmt ) {
				foreach ( $invoice->lines as $line ) {
					$line->tax = 'OUTPUT';
					$line->gst = $line->amount * 10 / 100;
					$line->update('gst','tax');
				}
				echo 'invoice ' . $invoice->no . ' fixed' . PHP_EOL;
			}

			echo $finished . ' / ' . $all . ' done' . PHP_EOL;
			unset($invoices[$k]->lines);
			unset($invoices[$k]);
		}

	}
	
	public function regetAupostCostByImportConsole(){
		$id = 9085;
		$orderId = 'AP01795169';
		$aupostApi = new AusPostAPI('syd', true);
				$orderInfo = $aupostApi->getOrder($orderId);
				if ( !empty($orderInfo->order) ) {

					// update shipment related cost and aupost shipment id

					// because aupost returned shipment is not the same order with our sending order
					// so here we need to order by HBN again
					$auPostShipments = array();
					foreach ($orderInfo->order->shipments as $aushipment) {

						$ims = ImParcel::model()->find('hbn = :hbn', [':hbn' => $aushipment->shipment_reference]);
												$ims->consol_id=$id;
												$ims->save();
						if (!empty($ims)) {
							$ts = Tranship::model()->find('org_id = 101 AND pid = :pid', [':pid' => $ims->id]);
							if (empty($ts)) {
								$ts = new Tranship;
								$ts->pid = $ims->id;
								$ts->org_id = 101;  // for Australia post office
								$ts->man_id = $ims->man_id;
								$ts->type = 80;  // shipment transfer to a different delivery courier
								$ts->status = 19; // in finally moving status
								$ts->connote = $ims->ref;
								$ts->time = date('Y-m-d H:i:s');
							}

							$ts->mdata['oid'] = $orderId;
							$ts->mdata['sid'] = $aushipment->shipment_id;
							$costValue = floatval($aushipment->shipment_summary->total_cost - $aushipment->shipment_summary->total_gst);
							$ts->cost = round($costValue, 2);
							$ts->save();
						}
						echo 'done for shipment : ' . $ims->ref . PHP_EOL;
					}

					// update all console's Australia post office cost billing
					ImcoConsol::updateImportConsoleBilling([$id]);

				}
	}

	/**
	 * re-get all import console aupost cost from 2017.01.01
	 */
	private function regetImportConsoleAupostCost(){
		$allConsols = ImcoConsol::model()->findAll('created >= :date',[':date' => '2017-08-01']);

		$allCount = count($allConsols);
		echo 'consol count: ' . $allCount  . ' to be processed' . PHP_EOL;
		$aupostApi = new AusPostAPI('syd', true);

		$successCount = 0;
		// check aupost cost existing or not
		foreach ( $allConsols as $consol ) {

			// only for not existing
			// we try to get from aupost again now
			$orderId = '';
			if ( empty($aupostCost) ) {
				echo 'consol : ' . $consol->no . ' in progress' . PHP_EOL;
				// from shipment tranship get order_id for aupost
				$aupostFound = false;
				foreach ( $consol->shipments as $shipment ) {
					foreach ( $shipment->trans as $ts ) {
						if ( $ts->org_id == 101 ) {
							$orderId = isset($ts->mdata['oid']) ? $ts->mdata['oid'] : '';
							echo 'order id : ' . $orderId . ' found' . PHP_EOL;
							$aupostFound = true;
							break;
						}
					}
					if ( $aupostFound ) break;
				}
			}

			if ( !empty($orderId) ) {
				$orderInfo = $aupostApi->getOrder($orderId);
				if ( !empty($orderInfo->order) ) {
					echo 'consol : ' . $consol->no . ' aupost order info got '. PHP_EOL;
					// update shipment related cost and aupost shipment id

					// because aupost returned shipment is not the same order with our sending order
					// so here we need to order by HBN again
					$auPostShipments = array();
					foreach  ( $orderInfo->order->shipments as $aushipment ) {

						$ims = ImParcel::model()->find('hbn = :hbn',[':hbn'=> $aushipment->shipment_reference]);
						if ( !empty($ims) ) {
							$ts = Tranship::model()->find('org_id = 101 AND pid = :pid',[':pid' => $ims->id]);
							if ( empty($ts) ) {
								$ts = new Tranship;
								$ts->pid = $ims->id;
								$ts->org_id = 101;  // for Australia post office
								$ts->man_id = $ims->man_id;
								$ts->type = 80;  // shipment transfer to a different delivery courier
								$ts->status = 19; // in finally moving status
								$ts->connote = $ims->ref;
								$ts->time = date('Y-m-d H:i:s');
							}

							$ts->mdata['oid'] = $orderId;
							$ts->mdata['sid'] = $aushipment->shipment_id;
							$costValue = floatval($aushipment->shipment_summary->total_cost - $aushipment->shipment_summary->total_gst);
							$ts->cost = round($costValue,2);
							$ts->save();

						}

						echo 'done for shipment : ' . $ims->ref . PHP_EOL;

					}

					$successCount++;
					echo 'done for  ' . $successCount . '/ ' . $allCount . PHP_EOL;

					// update all console's Australia post office cost billing
					ImcoConsol::updateImportConsoleBilling([$consol->id]);

					echo 'related ledger saved for consol: ' . $consol->no . PHP_EOL;
				}
			}
		}
		echo 'All Done success : ' . $successCount . PHP_EOL;
	}

	/**
	 * try to get all old AuPost shipment cost information
	 * after 07.12.2016 we should retrieve them because we have got the cost information by send order
	 */
	private function fixImportAupostBilling(){
		// get all old aupost order tranship history
		$tships = Tranship::model()->findAll('type = 80 AND org_id = 101 AND time < :dtime AND meta <> ""',[':dtime' => '2016-12-08 00:00:00']);

		// loop all tranships to get all AuPost Order ID
		$aupostOrders = array();
		foreach ( $tships as $tship ) {
			if ( isset($tship->mdata['sync_cost']) ) continue;
			$oid = $tship->mdata['oid'];
			if ( isset($aupostOrders[$oid]) ) {
				$aupostOrders[$oid][] = $tship;
			} else {
				$aupostOrders[$oid] = array($tship);
			}
		}

		$total = count($aupostOrders);
		echo 'found old orders : ' . $total . PHP_EOL;

		$left = $total;
		// get all order information from AuPost by API now
		$apa = new AusPostAPI('syd');
		foreach ( $aupostOrders as $k => $v ) {
			echo 'Process for order : ' . $k . PHP_EOL;
			$r = $apa->getOrder($k);
			// update aupost cost for each shipment
			$consoleIds = array();
			$sModel = Shipment::model();
			if ( isset($r->order->shipments) ) {
				foreach ($r->order->shipments as $shipment) {
					$costValue = floatval($shipment->shipment_summary->total_cost - $shipment->shipment_summary->total_gst);
					if (isset($shipment->shipment_reference)) {
						$consoleId = $sModel->addCost2Shipment($shipment->shipment_reference, $costValue, 'hbn');
						if ($consoleId > 0) {
							array_push($consoleIds, $consoleId);
						}
					}
				}
				echo 'done for shipments ' . PHP_EOL;
				// update all console's aupost cost billing
				if (!empty($consoleIds)) {
					ImcoConsol::updateImportConsoleBilling($consoleIds);
					echo 'set sync cost flag for tranships : ' . count($v) . PHP_EOL;
					foreach ( $v as $t ) {
						$t->mdata['sync_cost'] = 1;
						$t->update('meta');
					};
				}
				echo 'done for console ' . PHP_EOL;
				$left--;
				echo 'Finished ' . $left . ' / ' . $total . PHP_EOL;
			}
		}

		echo ' All Done ' . PHP_EOL;
	}

	public function testFwRec(){
		$model = Reconciliation::model();

		$invFile =  Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'fwinv.xlsx';
		$xls = new oExcel;
		$xls->load($invFile);
		$data = $xls->getAll();

		// remove title
		unset($data[1]);

		$total = 0;
		$ourTotal = 0;
		$invoiceNo = '';
		$invoiceDate = '';
		$lines = array();
		foreach ( $data as $k => $line) {
			$no = $line[2];
			$invoice = trim($line[3]);
			if ( empty($invoiceNo) ) $invoiceNo = $invoice;
			$weight = $line[6];
			$value = $line[10];
			if ( empty($invoiceDate) ) {
				$invoiceDate = date('Y-m-d', (intval($line[8]) - 25569) * 86400);
			}
			$postcode = $line[19];
			$total += floatval($value);
			$ourRated = $this->getCourierCostByShipment($no);
			$ourTotal += $ourRated['price'];
			$lines[] = array($no, $invoice,$postcode,$weight,$value,$ourRated);
		}

		// check to see if the same reconciliation existing or not
		$existing = Reconciliation::model()->find('invoice_no = :invno', [':invno' => $invoiceNo]);
		if ( empty($existing) ) {
			// create reconsiliation now
			$recModel = new Reconciliation();
			$recModel->client_type = 0; // for fastway only currently
			$recModel->invoice_no = $invoiceNo;
			$recModel->invoice_date = $invoiceDate;
			$recModel->invoice_total = $total;
			$recModel->my_total = $ourTotal;
			$recModel->save();

			// create reconciliation related lines
			$errors = '';
			foreach ($lines as $line) {
				$recData = new ReconciliationLine();
				$recData->parent_id = $recModel->id;
				$recData->invoice_no = $line[1];
				$recData->postcode = $line[2];
				$recData->shipment_no = $line[0];

				// check to see if has been charged before
				$firstCharge = ReconciliationLine::model()->find('shipment_no = :sno',[':sno' =>  $line[0] ]);
				if ( !empty($firstCharge) ) {
					$errors .= $line[0] . ' has been charged again' . PHP_EOL;
				}
				$recData->weight = $line[3];
				$recData->value = $line[4];
				$recData->my_value = $line[5]['price'];
				$recData->consol_id = $line[5]['consol_id'];
				$recData->save();
			}
			if ( !empty($errors) ) echo $errors . PHP_EOL;

		} else {
			echo 'Sorry the invoice : ' . $invoiceNo . ' has been reconciled before' . PHP_EOL;
		}

		echo 'all done' . PHP_EOL;
	}

	private function getCourierCostByShipment($shipmentRef){
		$price = 0;
		$consolId = 0;
		$shipment = Shipment::model()->find('ref = :ref',[':ref' => $shipmentRef]);
		if ( !empty($shipment) ) {
			$consolId = $shipment->consol_id;
			$weight = $shipment->weight;

			// base on postcode to get related charge code
			$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code',
				[':oid' => Org::ORGID_COURIER_FASTWAY, ':zoneid' => 0, ':code' => $shipment->cnee->postcode]);

			// if not found we set as NSW country
			$chargeCode = 'N1';
			if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
				$chargeCode = $zoneMap['z1'];
			}

			// base charge code to get zone rate
			$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ",
				[':s' => $chargeCode, ':w' => $weight, ':rateid' => 52]);

			if (!empty($zrs)) {

				// get maximum one
				foreach ($zrs as $zr) {
					$temp = $zr['base'] + $zr['item'];
					if ($zr['nkg'] > 0) {
						$wl = $weight - ($zr['base'] > 0 ? $zr['nkg'] : 0);
						$temp += ceil($wl / $zr['nkg']) * $zr['perkg'];
					} else {
						$temp += $weight * $zr['perkg'];
					}
					if ($zr['minimum'] > 0 && $temp < $zr['minimum']) $temp = $zr['minimum'];
					$price = max($price, $temp);
				}
			}
		}
		return array('price' => $price, 'consol_id' => $consolId);
	}

	/**
	 * get all aupost 2014 label format test parcels
	 */
	public function getJcexAu2014Test(){
		$ss = Shipment::model()->findAll('agent_id = 838 and type = 10 and created >= :date', [':date' => '2017-05-22']);
		$tests = '';
		$allCount = 0;
		foreach ( $ss as $s ) {
			if ( isset($s->mdata['test_aupost_2014']) ) {
				$tests .= 'ID:' . $s->hbn . '  ' . $s->ref . PHP_EOL;
				$allCount++;
			}
		}
		echo 'All Count : ' . $allCount . PHP_EOL;
		echo $tests;
	}

	/**
	 * test aupost label 2014 format logement
	 */
	public function testAupost2014Format(){

		$ss = array();
		$ss[] = ImParcel::model()->findByPk(2829547);
		$apa = new AusPostAPI('syd');
		$chargeCode = AusPostAPI::CHARGE_CODE_POD;
		$r = $apa->createOrderIncludingShipments2014($ss, 'JUSTTESTYX', $chargeCode);
		$error = '';
		if(!empty($r->order)){
			$oid = $r->order->order_id;
			foreach($ss as $i => $s){
				$ts = new Tranship;
				$ts->pid = $s->id;
				$ts->org_id = 101;  // for Australia post office
				$ts->man_id = $s->man_id;
				$ts->type = 80;  // shipment transfer to a different delivery courier
				$ts->status = 19; // in finally moving status
				$ts->connote = $s->ref;
				$ts->time = date('Y-m-d H:i:s');
				$ts->mdata['oid'] = $oid;
				$ts->mdata['sid'] = $r->order->shipments[$i]->shipment_id;

				// currently we save cost with a single field
				// $ts->mdata['cost'] = $r->order->shipments[$i]->shipment_summary->total_cost;
				$costValue = floatval($r->order->shipments[$i]->shipment_summary->total_cost - $r->order->shipments[$i]->shipment_summary->total_gst);
				$ts->cost = round($costValue,2);
				$ts->save();
			}

		}else{
			foreach($apa->err as $e){
				$error  .= $e->message;
				if(!empty($e->field) && preg_match('/shipments\[(\d+)\]/', $e->field, $m)){
					$error .= ': '.$ss[$m[1]]->hbn;
				}
			}
		}

		if ( empty($error) ) {
			echo 'All Done';
		} else {
			echo 'Failed : ' . $error;
		}
	}

	public function addCTracking(){
		$id = $this->prompt('Consol No: ');
		$dt = $this->prompt('Date time: ');
		$act = $this->prompt('Activity: ');
		$dpt = $this->prompt('Depot: ');
		$yn = $this->prompt('Continue? ');
		if($yn != 'y') die("Exiting\n");
		$criteria = new CDbCriteria();
		$criteria->addInCondition("no", preg_split('/[\s;,]+/', $id));
		$cs = Consol::model()->findAll($criteria);
		
		if(!$cs) die("Consol Not found\n");
		foreach($cs as $c){
			foreach($c->shipments as $p){
				$p->addTracking(40, $act, $dpt, $dt);
			}
		}
		echo "Done\n";
	}
	
	public function loadZoneMap(){
		$org_id = $this->args[1];
		$rs = file($this->args[2]);
		$s = 0;
		unset($rs[0]);
		
		//remove duplicate
		$m = array();
		foreach($rs as $i=>$r){
			$r = AppHelper::csvLine($r);
			if(!isset($m[$r[0]])) $m[$r[0]] = array();
			if(!in_array($r[1], $m[$r[0]])) $m[$r[0]][] = $r[1];
		}
		
		if(sizeof($rs) > sizeof($m)){
			unset($rs);
			$rs = array();
			
			foreach($m as $k=>$zs){
				sort($zs);
				$pz = 0;
				$oz = 0;
				$la = '';
				foreach($zs as $i=>$z){
					if($z != $pz+1){
						if($pz != $oz && $pz > 0){
							$la .= '-'.$pz;
						}
						$la = &$rs[];
						$la = $k;
						$la .= ','.$z;
						$oz = $z;
					}
					$pz = $z;
				}
			}
		}
		
		foreach($rs as $i=>$l){
			$r = AppHelper::csvLine($l);
			$pcs = explode(',', $r[1]);
			foreach($pcs as $pc){
				$zm = new ZoneMap;
				$zm->org_id = $org_id;
				$zm->z1 = $r[0];
				$pr = explode('-', trim($pc));
				$zm->pc_lo = $pr[0];
				$zm->pc_hi = empty($pr[1])? $pr[0] : $pr[1];
				$zm->save();
				$s++;
			}
		}
		echo $s." zones added\n";
	}

	public function zoneMap2(){
		$data = $this->_loadXlsData($this->args[1]);
		unset($data[1]);
		$range = [0,0];
		$addZone = function($org_id, $z1, $z2='', $lo, $hi){
				$zm = new ZoneMap;
				$zm->org_id = $org_id;
				$zm->z1 = $z1;
				if(!empty($z2) && $z2 != $z1){
					$zm->z2 = $z2;
				}
				$zm->pc_lo = $lo;
				$zm->pc_hi = $hi;
				$zm->save();
		};

		foreach($data as $i => $r){
			if($i > 2 && ($r[2] != $data[$i-1][2] || $r[3] != $data[$i-1][3])){
				$addZone(100, $data[$i-1][2], $data[$i-1][3], $range[0], $range[1]);
				echo $data[$i-1][2],'/', $data[$i-1][3], ': ', $range[0],'-',$range[1],"\n";
				$range = [0, 0];
			}

			if($r[1] != $range[1] + 1){
				if($i > 2 && !empty($range[1])){
					$addZone(100, $data[$i-1][2], $data[$i-1][3], $range[0], $range[1]);
					echo $data[$i-1][2],'/', $data[$i-1][3], ': ', $range[0],'-',$range[1],"\n";
				}
				$range[0] = $r[1];
			}

			$range[1] = $r[1];
		}
		$addZone(100, $r[2], $r[3], $range[0], $range[1]);
		echo $r[2],'/', $r[3], ': ', $range[0],'-',$range[1],"\n";
	}
	
	public function loadZoneName(){
		$org_id = $this->args[1];
		$rs = file($this->args[2]);
		$s = 0;
		unset($rs[0]);
		
		foreach($rs as $i=>$l){
			$r = AppHelper::csvLine($l);
			$sql = "UPDATE zone_rate SET zname = '".$r[0]."' WHERE org_id = ".$org_id." AND zone = '".$r[1]."'";
			$c = $this->db->createCommand($sql);
			$c->execute();
			$s++;
		}
		echo $s." zones name updated\n";
	}
	
	private function _loadRate_eParcel($or, $data){
		if(implode(',', $data[1]) != 'Destination Zone,Min Weight,Max Weight,Basic,Subsequent**,Per Kg') die("Column mismatch!\n");
		unset($data[1]);
		$i = 0;
		foreach($data as $r){
			preg_match('/^(.+)\s+[\[\(]{1}([^\]\)]+)[\]\)]{1}(\**)/', $r[1], $m);
			$data = array(
				'rate_id' => $or->id,
				'zone' => $m[2],
				'zone_name' => $m[1],
				'weight_lo' => $r[2],
				'weight_hi' => $r[3],
			);
			$zr = ZoneRate::model()->findByAttributes($data);
			if(empty($zr)){
				$zr = new ZoneRate;
				$zr->setAttributes($data);
			}
			$zr->base = $r[4];
			$zr->item = $r[5];
			$zr->perkg = $r[6];
			$zr->minimum = 0;
			$zr->gst = empty($m[3])? 1:3;
			$zr->save();
			$i++;
		}
		echo $i." Zone Rates loaded!\n";
	}
	
	private function _loadRate_cpParcel($or, $data){
		if(implode(',', $data[2]) != 'Ex Sydney to:,,Zone,Minimum,Base,kilo,Flat rate per 25kg') die("Column mismatch!\n");
		$i = 0;
		foreach($data as $l=>$r){
			if($l < 3) continue;
			$zr = new ZoneRate;
			$zr->rate_id = $or->id;
			$zr->zone = $r[3];
			$zr->zone_name = $r[2];
			if($r[7] == 'N/A'){
				$zr->weight_lo = 0;
				$zr->weight_hi = 999;
				$zr->base = $r[5];
				$zr->item = 0;
				$zr->perkg = $r[6];
				$zr->minimum = $r[4];
				$zr->levy = 1;
			}else{
				$zr->weight_lo = 0;
				$zr->weight_hi = 25;
				$zr->base = $r[7];
				$zr->item = 0;
				$zr->perkg = 0;
				$zr->minimum = 0;
			}
			$zr->gst = 2;
			//var_dump($zr->attributes);
			$zr->save();
			$i++;
		}
		echo $i." Zone Rates loaded!\n";
		
	}
	
	private function _loadRate_cpSatchel($or, $data){
		if(implode(',', $data[1]) != 'Ex All to:,,Zone,500g ATL Satchel,1kg Satchel,3kg Satchel,5kg Satchel') die("Column mismatch!\n");
		$i = 0;
		unset($data[1]);
		foreach($data as $l=>$r){
			if(empty($r[3])) break;
			$zr = ZoneRate::model()->find('rate_id = :rid AND zone = :z AND weight_lo = 0 AND weight_hi = 0.5', array(':rid' => $or->id, ':z' => $r[3]));
			if(empty($zr))	$zr = new ZoneRate;
			$zr->rate_id = $or->id;
			$zr->zone = $r[3];
			$zr->zone_name = $r[2];
			$zr->weight_lo = 0;
			$zr->weight_hi = 0.5;
			$zr->base = $r[4];
			$zr->gst = 2;
			$zr->save();
			
			
			$szr = ZoneRate::model()->find('rate_id = :rid AND zone = :z AND weight_lo = 0.5 AND weight_hi = 1', array(':rid' => $or->id, ':z' => $r[3]));
			if(empty($szr)){
				$zr->isNewRecord = true;
				$zr->id = null;
			}else{
				$zr = $szr;
			}
			$zr->weight_lo = 0.5;
			$zr->weight_hi = 1;
			$zr->base = $r[5];
			$zr->save();
			
			$szr = ZoneRate::model()->find('rate_id = :rid AND zone = :z AND weight_lo = 1 AND weight_hi = 3', array(':rid' => $or->id, ':z' => $r[3]));
			if(empty($szr)){
				$zr->isNewRecord = true;
				$zr->id = null;
			}else{
				$zr = $szr;
			}
			$zr->weight_lo = 1;
			$zr->weight_hi = 3;
			$zr->base = $r[6];
			$zr->save();
			
			$szr = ZoneRate::model()->find('rate_id = :rid AND zone = :z AND weight_lo = 3 AND weight_hi = 5', array(':rid' => $or->id, ':z' => $r[3]));
			if(empty($szr)){
				$zr->isNewRecord = true;
				$zr->id = null;
			}else{
				$zr = $szr;
			}
			$zr->weight_lo = 3;
			$zr->weight_hi = 5;
			$zr->base = $r[7];
			$zr->save();
			
			$i++;
		}
		echo $i." Zone Rates loaded!\n";
	}
	
	private function _loadRate_sell($or, $data){
		if(implode(',', $data[1]) != '0-5KG RATES,AUS POST(eparcel),LOCAL COURIER,') die("Column mismatch!\n");
		if(implode(',', $data[8]) != '5KG+ RATES,BASIC,SUBSEQUENT**,PER KILO') die("Column mismatch!\n");
		$i = 0;
		for($l = 2; $l < 7; $l++){
			$r = $data[$l];
			$zr = new ZoneRate;
			$zr->rate_id = $or->id;
			$zr->zone = '';
			$zr->zone_name = '';
			switch($r[1]){
				case '0-500G':
					$wl = 0;
					$wh = 0.5;
				break;
				case '500G-1KG':
					$wl = 0.5;
					$wh = 1;
				break;
				case '1-2KG':
					$wl = 1;
					$wh = 2;
				break;
				case '2-3KG':
					$wl = 2;
					$wh = 3;
				break;
				case '3-5KG':
					$wl = 3;
					$wh = 5;
				break;
			}
			$zr->weight_lo = $wl;
			$zr->weight_hi = $wh;
			$zr->base = $r[2];
			$zr->gst = 3;
			$zr->save();
			
			//courier rate
			$zr = new ZoneRate;
			$zr->rate_id = 5;
			$zr->zone = '';
			$zr->zone_name = '';
			$zr->weight_lo = $wl;
			$zr->weight_hi = $wh;
			$zr->base = $r[3];
			$zr->gst = 3;
			$zr->save();
		}
		
		foreach($data as $l=>$r){
			if($l < 9) continue;
			if(empty($r[2])) break;
			$zr = new ZoneRate;
			$zr->rate_id = $or->id;
			$zr->zone = '';
			$zr->zone_name = '';
			$zr->weight_lo = 5;
			$zr->weight_hi = 999;
			$zr->base = $r[2];
			$zr->item = $r[3];
			$zr->perkg = $r[4];
			$zr->gst = 3;
			$zr->save();
		}
		echo "Sell Rate Loaded!\n";
	}
	
	public function loadRate(){
		$rid = $this->args[1];
		$or = OrgRate::model()->findByPk($rid);
		if(empty($or))	die("Sorry rate not found!\n");
		$data = $this->_loadXlsData($this->args[2]);
		
		if($rid == 1){
			$this->_loadRate_eParcel($or, $data);
		}elseif($rid == 2){
			$this->_loadRate_cpParcel($or, $data);
		}elseif($rid == 3){
			$this->_loadRate_cpSatchel($or, $data);
		}elseif($rid == 4){
			$this->_loadRate_sell($or, $data);
		}
	}

	public function smsDelay(){
		$ls = file($this->args[1]);
		$send = isset($this->args[2]) && $this->args[2] == 'send';
		$ns =[];
		foreach($ls as $l){
			$r = explode(',',trim($l));
			foreach($r as $i=>$c){
				$r[$i] = trim($c, '"\'');
			}
			$ns[] = $r;
		}

		$i = 0;
		foreach($ns as $r){
			if(!preg_match('/^1\d{10}$/', $r[1])) continue;
			$sms = new Sms;
			$sms->type = 10;
			$sms->no = $r[1];
			$sms->msg = "您在PCA快递托寄的包裹".$r[0]."，由于清关口岸海关系统升级，会造成延误。您可通过微信公众号 wz0577td 进行追踪查询。给您带来的不便，我们深表歉意。";
			if($send) $sms->send_yp();
			echo 'Sent: '.$r[1]."\n";
			$i++;
		}
		echo "Total ".$i." SMS sent\n";
	}

	public function prodWeight(){
		$rs = ExProdb::model()->findAll('weight = 0');
		foreach($rs as $p){
			if(preg_match('/(\d+)(g|片|ml|粒|克)/i', $p->name_zh, $m)){
				$p->weight = round($m[1]/10)/100;
				$p->note .= ' w='.$m[1].$m[2];
			}elseif(preg_match('/(\d+)(kg|l)/i', $p->name_zh, $m)){
				$p->weight = round($m[1]*100)/100;
				$p->note .= ' w='.$m[1].$m[2];
			}
			echo $p->note;
			$p->save();
		}
		echo "Done\n";
	}

	public function expLog2Track(){
		$stc = [
			'New' => 10,
			'Picked Up' => 12,
			'Rcvd. No Info' => 14,
			'Received' => 15,
			'Info Ready' => 18,
			'Consolidated' => 20,
		];
		$rs = ExParcel::model()->findAll('status IN (10, 12, 15, 18, 20)');
		foreach($rs as $r){
			$logs = Log::model()->findAll('model = :m AND lid = :id', array(':m' => 'ExParcel', ':id' => $r->id));
			foreach($logs as $log){
				if(empty($log->extra) || empty($log->extra['status']) || empty($stc[$log->extra['status']])) continue;
				$r->status = $stc[$log->extra['status']];
				$ht = Tracking::model()->count('pid = :pid AND type = :t', array(':pid' => $r->id, ':t' => $r->status));
				if(empty($ht)){
					$dpt = empty($r->odepot)? '悉尼' : $r->odepot->suburb;
					switch($r->status){
						case 10:
							$r->addTracking(10, '收到运单信息', $dpt, $log->time);
						break;
						case 12:
							$r->addTracking(12, 'Consignment Picked Up', $dpt, $log->time);
						break;
						case 14:
							$r->addTracking(14, '货物入库，等待运单信息', $dpt, $log->time);
						break;
						case 15:
							$r->addTracking(15, '运单信息已输入，身份证信息未上传，留库待发', $dpt, $log->time);
						break;
						case 18:
							$r->addTracking(18, '身份证信息已匹配成功，等待发运', $dpt, $log->time);
						break;
						case 20:
							$r->addTracking(20, '理货打板已毕，发离悉尼仓库', $dpt, $log->time);
						break;
						case 25:
							$r->addTracking(25, '中国口岸进口预申报已完成，等待清关处理', $dpt, $log->time);
						break;
						default:
						break;
					}
				}
			}
		}
		echo "Done\n";
	}

	public function AclObjAudit(){
		$cd = Yii::app()->basePath.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR;
		$cs = [];
		foreach(glob($cd.'*Controller.php') as $f){
			$c = str_replace('Controller.php', '', basename($f));
			$c = 'C:'.strtolower($c[0]).substr($c,1);
			$d = file_get_contents($f);
			preg_match_all('/ function action(.+)\(/', $d, $m);
			foreach($m[1] as $a){
				$a = $c.'/'.strtolower($a[0]).substr($a,1);
				$cs[] = $a;
			}
			preg_match_all('/hasAccess\(["\']{1}(B:[^"\']+)["\']{1}\)/', $d, $m);
			if(!empty($m[1])) $cs = array_merge($cs, $m[1]);
		}
		$cs = array_unique($cs);
		sort($cs);
		print_r($cs);
	}

	public function createStartrackLabel(){
		$id = $this->prompt('Shipment ID: ');
		$shipment = Shipment::model()->findByPk($id);
		if ( !empty($shipment) ) {
			// clear all old tranship
			Tranship::model()->deleteAll('pid = :pid',[':pid' => $id]);

			$ss = new StarTrackAPI('syd', true);
			$result = $ss->createShipments($shipment);
			if ( $result && isset($result->shipments) && is_array($result->shipments) ) {
				$result = $result->shipments[0];
				$cost = $result->shipment_summary->total_cost;

				$shipment->mdata['ss_shipment_id'] = $result->shipment_id;
								  foreach($result->items as $item){
							if(!empty($item->tracking_details->article_id)){
								if(preg_match('/7RFZ\d{8}EXP00001/i', $item->tracking_details->article_id)){
								  $shipment->mdata['ss_shipment_items'][]=$item->item_id; 
								  break;
								}
							}
						}

				// startrack label format: 7RFZ50000002EXP00001, we only need 7RFZ50000002
				// EXP00001 is index
				$shipment->ref = substr($result->items[0]->tracking_details->article_id,0,12);
				$shipment->update('ref');

				// create label
				$respLabel = $ss->createLabels([$result->shipment_id]);
				$lblRequestId = '';
				if ( $respLabel ) {
					// get label  request ID
					// then we can print label based on request ID
					$lblRequestId = $respLabel->labels[0]->request_id;
					$shipment->mdata['ss_lbl_request_id'] = $lblRequestId;
					$shipment->updateMeta();

					// save tranship information for startrack courier
					$ts = new Tranship;
					$ts->pid = $shipment->id;
					$ts->org_id = Org::ORGID_COURIER_STARTRACK;  // for StarTrack
					$ts->man_id = $shipment->man_id;
					$ts->type = 80;  // shipment transfer to a different delivery courier
					$ts->status = 19; // in finally moving status
					$ts->connote = $shipment->ref;
					$ts->time = date('Y-m-d H:i:s');
					$ts->mdata['ss_shipment_id'] = $result->shipment_id;
					$ts->mdata['ss_lbl_request_id'] = $lblRequestId;
					$ts->cost = round($cost,2);
					$ts->save();
					echo 'created successfully' . PHP_EOL;
				}
			} else {
				echo 'failed to created label' . PHP_EOL;
			}

		} else {
			echo 'shipment not found' . PHP_EOL;
		}
		echo 'done' . PHP_EOL;

	}

	public function consolMapGoods(){
		$id = $this->prompt('Consol ID: ');
		$c = ExcoConsol::model()->findByPk($id);
		foreach($c->shipments as $s){
			if(($s->bwf & 24) > 0){
				$s->cleanItems();
				echo 'Mapping goods for '.$s->hbn."\n";
				if($s->mapGoods(true)) $s->save();
			}
		}
		echo "Done\n";
	}

	public function bulkAddStorage(){
		//$shelves = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];
		$shelves = ['G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];
		//$shelves = [ 'C', 'D', 'E', 'F', 'G'];
		$rows = ['07', '08', '09'];
		$lvls = 6;
		$cells = 8;
		$i = 0;
		$wid = 106; //warehouse 106:syd, 218:mel
		$type = 60; //type
		$pid = 1; //parent location 1:syd, 210: mel
		$cap = 1; //capacity
		
		foreach($shelves as $s){
			foreach($rows as $r){
				for($l = 1; $l <= $lvls; $l++){
					for($c = 1; $c <= $cells; $c++){
						$sl = new Storage;
						$sl->setAttributes([
							'wid' => $wid,
							'pid' => $pid,
							'type' => $type,
							'status' => 1,
							'name' => $s.$r.'-'.$l.'-'.$c,
							'code' => 'ES-'.$s.$r.'-'.$l.'-'.$c,
							'cap_item' => $cap,
						]);
						$sl->save();
						$i++;
					}
				}
			}
		}
		echo $i." storage created.\n";
	}

	public function storageAddWt(){
		$rss = [];
		$rss[0] = Storage::model()->findAll("wid = 106 AND status = 1 AND type = 60 AND name REGEXP '^[A-Z]+0[1-3]{1}' ORDER BY name");
		$rss[1] = Storage::model()->findAll("wid = 106 AND status = 1 AND type = 60 AND name REGEXP '^[A-Z]+0[4-6]{1}' ORDER BY name");
		$rss[2] = Storage::model()->findAll("wid = 106 AND status = 1 AND type = 60 AND name REGEXP '^[A-Z]+0[7-9]{1}' ORDER BY name");
		$i = 1;
		foreach($rss as $rs){
			foreach($rs as $r){
				$r->wt = $i++;
				$r->save();
			}
		}
		echo "Done\n";
	}

	public function dumpExparcels(){
		$fd = $this->prompt('Start Date: ', date('Y-m-d', strtotime('-90 day')));
		$tss = ExParcel::model()->count('created >= :fd AND odpt_id = 106 AND status >= 18', [':fd' => $fd]);
		$qos = 10000;
		$qns = ceil($tss / $qos);
		echo 'Total '.$tss." shipments to analyse\n";

		$xls = new oExcel;
		$xls->setColWidth([15,10,10,10]);
		$i = 1;
		$xls->addRow($i++, ['HBN','Dest','Weight','Type', 'Goods']);

		for($qc = 0; $qc < $qns; $qc++){
			$rs = ExParcel::model()->findAll([
				'condition' => 'created >= :fd AND odpt_id = 106 AND status >= 18', 
				'params' => [':fd' => $fd],
				'offset' => $qc * $qos,
				'limit' => $qos,
				]);
			foreach($rs as $s){
				$s->state = mb_substr($s->state,0,2);
				$gt = $s->goodsType();
				$xls->addRow($i++, [$s->hbn, $s->state, $s->weight, $gt, empty($s->eitems['g'])? '' : implode(',', $s->eitems['g'])]);
			}
			echo ($i-2)." done\n";
		}
		$xls->output('dumpExparcels_'.time().'.xlsx', null, false);
		echo "Done\n";
	}

	public function bestReport(){
		ini_set('memory_limit','2G');
		$dpt = $this->prompt('Deport: ', 'AUSYD');
		$fd = $this->prompt('Start Date: ', date('Y-m-01', strtotime('-1 month')));
		$td = $this->prompt('End Date: ', date('Y-m-d', strtotime(date('Y-m-01').' -1 day')));
		$exrate = $this->prompt('Exchange Rate: ', 4.6);

		$getRates = function(&$s) use (&$exrate){
			$rsn = ['IE_GZ', 'IE_BJ', 'IE_QD', 'WTK_KM', 'IE_CS'];
			$rs = [0, 0, 0, 0, 0, 0];
			$wt = $s->weight;
			$typ = $s->goodsType();

			$brs = $s->bestRates();
			$p2i = ['CNCAN' => 0, 'CNPEK' => 1, 'CNTA2' => 2, 'CNKMG' => 3, 'CNCSX' => 4];

			foreach($brs as $p=>$c){
				$rs[$p2i[$p]] = $c == 999? '-' : $c;
			}

			$ps = array_keys($brs);
			$cs = array_values($brs);

			$rs[6] = $rsn[$p2i[$ps[0]]];
			$rs[7] = $cs[0];

			return array_merge([$typ], $rs);
		};

		//$tss = $this->db->createCommand("SELECT COUNT(id) FROM shipment WHERE consol_id IN (SELECT id FROM consol WHERE pol = '".$dpt."' AND pod != 'HKHKG' AND created >= '".$fd."' AND created < '".$td."')")->queryScalar();
		$tss = ExParcel::model()->with('consol')->count('consol.pol = :dpt AND consol.pod != :hk AND consol.pod != :sto AND consol.created >= :fd AND consol.created <= :td', [':dpt' => $dpt, ':hk' => 'HKHKG', ':sto' => 'STO', ':fd' => $fd, ':td' => $td]);
		$qos = 10000;
		$qns = ceil($tss / $qos);
		echo 'Total '.$tss." shipments to analyse\n";

		$xls = new oExcel;
		$xls->setColWidth([15,10,10,10]);
		$i = 1;
		$xls->addRow($i++, ['HBN','Dest','Weight','Type','IE_GZ','IE_BJ','IE_QD','WTK_KM','IE_CS','','Best Port','Cost']);
		$sum = [];
		for($qc = 0; $qc < $qns; $qc++){
			$rs = ExParcel::model()->with('consol')->findAll([
				'condition' => 'consol.pol = :dpt AND consol.pod != :hk AND consol.pod != :sto AND consol.created >= :fd AND consol.created <= :td', 
				'params' => [':dpt' => $dpt, ':hk' => 'HKHKG', ':sto' => 'STO', ':fd' => $fd, ':td' => $td],
				'offset' => $qc * $qos,
				'limit' => $qos,
				]);

			//$rs = $this->db->createCommand("SELECT s.hbn, s.state, s.weight, s.items, s.tariff, s.value FROM shipment s WHERE consol_id IN (SELECT id FROM consol WHERE pol = '".$dpt."' AND pod != 'HKHKG' AND created >= '".$fd."' AND created < '".$td."') LIMIT ".$qc*$qos.",".$qos)->queryAll();
			foreach($rs as $s){
				$rates = $getRates($s);
				$s->state = substr($s->state,0,6);
				$xls->addRow($i++, array_merge([$s->hbn, $s->state, $s->weight], $rates));
				if(!isset($sum[$rates[7]])) $sum[$rates[7]] = [0, 0, 0];
				$sum[$rates[7]][0] += $s->weight;
				$sum[$rates[7]][1]++;
				$sum[$rates[7]][2] += $rates[8];
			}
			echo ($i-2)." done\n";
		}
		$j = $i-1;
		$xls->addRow($i, ['Total', '', '=SUM(C2:C'.$j.')', ($j-1), '','','','','','','', '=SUM(L2:L'.$j.')', '=L'.$i.'/C'.$i]);
		$j = $i;
		$i += 2;
		$xls->addRow($i++, ['','','','','IE_GZ','IE_BJ','IE_QD','WTK_KM','IE_CS','']);
		@$xls->addRow($i++, ['','','','Weight', $sum['IE_GZ'][0], $sum['IE_BJ'][0], $sum['IE_QD'][0], $sum['WTK_KM'][0], $sum['IE_CS'][0], '']);
		@$xls->addRow($i++, ['','','','%', '=E'.($i-2).'/C'.$j.'*100', '=F'.($i-2).'/C'.$j.'*100', '=G'.($i-2).'/C'.$j.'*100', '=H'.($i-2).'/C'.$j.'*100', '=I'.($i-2).'/C'.$j.'*100', '=J'.($i-2).'/C'.$j.'*100']);
		@$xls->addRow($i++, ['','','','Packs', $sum['IE_GZ'][1], $sum['IE_BJ'][1], $sum['IE_QD'][1], $sum['WTK_KM'][1], $sum['IE_CS'][1], '']);
		@$xls->addRow($i++, ['','','','%', '=E'.($i-2).'/D'.$j.'*100', '=F'.($i-2).'/D'.$j.'*100', '=G'.($i-2).'/D'.$j.'*100', '=H'.($i-2).'/D'.$j.'*100', '=I'.($i-2).'/D'.$j.'*100', '=J'.($i-2).'/D'.$j.'*100']);
		@$xls->addRow($i++, ['','','','Cost', $sum['IE_GZ'][2], $sum['IE_BJ'][2], $sum['IE_QD'][2], $sum['WTK_KM'][2], $sum['IE_CS'][2], '']);
		@$xls->addRow($i++, ['','','','%', '=E'.($i-2).'/L'.$j.'*100', '=F'.($i-2).'/L'.$j.'*100', '=G'.($i-2).'/L'.$j.'*100', '=H'.($i-2).'/L'.$j.'*100', '=I'.($i-2).'/L'.$j.'*100', '=J'.($i-2).'/L'.$j.'*100']);
		@$xls->addRow($i++, ['','','','/KG', '=E'.($i-3).'/E'.($i-7), '=F'.($i-3).'/F'.($i-7), '=G'.($i-3).'/G'.($i-7), '=H'.($i-3).'/H'.($i-7), '=I'.($i-3).'/I'.($i-7), '=J'.($i-3).'/J'.($i-7)]);
		$xls->output('Best_report_'.time().'.xlsx', null, false);
	}

	/**
	 * in order to fix some scan all issues we need to scan all for specified shipment mannually
	 */
	public function scanAllByShipment(){
		$id = $this->prompt('Shipment ID: ');
		$p = Shipment::model()->findByPk($id);
		$st = time() - rand(2000, 6000);
		if ( !empty($p) ) {
			for ($j = 1; $j <= $p->pkg; $j++) {
				if (!empty($p->scan_data[50][$p->hbn . '-' . $j])) continue;
				$pt = $st + rand(10, 100 * $j);
				$p->scan_data[50][$p->hbn . '-' . $j] = date('Y-m-d H:i:s', $pt);
			}
			if (empty($p->mdata['scan_time'])) $p->mdata['scan_time'] = date('Y-m-d H:i:s', $st);
			$p->scan_no = 0;
			$p->save();
			echo "Done";
		} else {
			echo 'Not found shipment!';
		}
	}

	public function clearScanAllByConsol(){
		$id = $this->prompt('Consol ID: ');
		$c = ImcoConsol::model()->findByPk($id);
		foreach($c->shipments as $p){
			$p->scan_data[50] = [];
			unset($p->mdata['scan_time']);
			$p->scan_no = 1;
			$p->nolog = true;
			$p->save();
		}
		echo "Done", PHP_EOL;
	}

	public function copyShipment(){
		$id = $this->prompt('Shipment ID: ');
		$hbn = $this->prompt('New HBN: ');
		$p = Shipment::model()->findByPk($id);
		$p->id = null;
		$p->cnee->id = null;
		$p->cnee->isNewRecord = true;
		$p->cnee->save();
		$p->cnee_id = $p->cnee->id;
		$p->cnor->id = null;
		$p->cnor->isNewRecord = true;
		$p->cnor->save();
		$p->cnor_id = $p->cnor->id;
		if(empty($hbn)){
			$p->hbn = '';
			$p->consol_id = 0;
			$p->genHbn();
		}else{
			$p->hbn = empty($hbn);
		}
		$p->isNewRecord = true;
		$p->save();
		echo "Done\n";
	}

	public function RTS(){
		$id = $this->prompt('Shipment ID: ');
		$p = Shipment::model()->findByPk($id);
		$p->status = 80;
		$p->save();
		//new
		$p->id = null;
		$p->cnee->id = null;
		$p->cnee->isNewRecord = true;
		$p->cnee->save();
		$p->cnee_id = $p->cnee->id;
		$p->cnor->id = null;
		$p->cnor->isNewRecord = true;
		$p->cnor->save();
		$p->cnor_id = $p->cnor->id;
		$p->isNewRecord = true;
		$p->status = 65;
		$p->hbn = '';
		$p->bwf = 1;
		$p->mdata['rtsid'] = $id;
		$p->save();
		$p->ref = 'AMQ'.sprintf('%07d',$p->id);
		$p->nolog = true;
		$p->save();
		echo $p->hbn.':'.$p->ref."\n";
		echo "Done\n";
	}

	public function updateBarcodeWithAMQByConsol(){
		$no = $this->prompt('Console Id: ');
		$rs = ImParcel::model()->findAll('consol_id = :cid',[':cid' => $no]);
		echo 'ImParcel : ' . count($rs) . ' found' . PHP_EOL;
		foreach ( $rs as $p ) {
			$amqNo = 'AMQ' . sprintf('%07s', substr($p->id, -7));
			if ( empty($p->hbn) ) $p->hbn = $amqNo ;
			$p->nolog = true;
			$p->ref = $amqNo;
			// currently we use 2014 format short label
			$p->mdata['test_aupost_2014'] = 1;
			// save long aupost tracking number in note field
			if (isset($p->mdata['test_aupost_2014'])) {
				$aid = $p->ref . sprintf('%02s', 1) . '50' . '2';
				$aid .= AusPostAPI::aidChkDgt($aid) . '0' . sprintf('%04s', $p->cnee->postcode);

			} else {
				$aid = $p->ref . sprintf('%02s', 1) . '00093' . '02' . '0'; // latest format
				$aid .= AusPostAPI::aidChkDgt($aid);
			}
			$p->note = $aid;
			$p->updateMeta();

			// update insurrance currently
		//    $p->insurance = round( $p->dvalue / 0.75, 2);
		//  $p->update(['hbn', 'ref', 'note','insurance']);

			$p->nolog = false;
			echo 'Done for : ' . $p->hbn . PHP_EOL;
		}
		echo 'All Done' . PHP_EOL;
	}

	protected function prepAddrLines($addr){
		$ls = preg_split('/[\n\r;]+/', $addr);
		if(sizeof($ls) == 1 && strlen($addr) > 40) $ls = str_split($addr, 40);
		$ls = array_slice($ls, 0, 3);
		$ls[0] = substr($ls[0], 0, 40);
		if(!empty($ls[1])) $ls[1] = substr($ls[1], 0, 40);
		if(!empty($ls[2])) $ls[2] = substr($ls[2], 0, 40);

		return $ls;
	}
	/**
	 * based on real weight to get our virtual weight and relate cube values
	 * we will post to aupost with these virtual values
	 * @param $weight
	 * @return array
	 */
	private function getVirtualWeightDim($weight){
		$weight =  round($weight * 75) / 100; // shrink to 75% firstly

		// divide by 250 to get virtual cube
		$averageCube = floor((Utility::croot3($weight/250) * 100));
		if ( $averageCube <= 5 ) {  // aupost can accept cube with minimum 5cm
			$width = 5;
			$height = 5;
			$length = 5;
		} else {
			$adjust = $averageCube - 5;
			$adjustValue = rand(1,$adjust);
			if ( rand(1,100) > 50 ) {
				$width = $averageCube - $adjustValue;
				$height = $averageCube;
				$length = $averageCube + $adjustValue;
			} else {
				$height = $averageCube - $adjustValue;
				$width = $averageCube;
				$length = $averageCube + $adjustValue;
			}
		}

		$weight = sprintf('%.02f', $weight);
		$width =  sprintf('%.1f',$width);
		$height = sprintf('%.1f',$height);
		$length = sprintf('%.1f',$length);

		return array(
			'weight' => $weight,
			'width' => $width,
			'height' => $height,
			'length' => $length
		);
	}

	public function eParcelLodgeByConsol(){
		$no = $this->prompt('Console Id: ');
		$apa = new AusPostAPI('syd'); //TMA
		$rs = ImParcel::model()->findAll('consol_id = :cid',[':cid' => $no]);
		$ss = [];
		foreach($rs as $s){
			if(preg_match('/^AMQ\d{7}/', $s->ref) && empty($s->trans)){
				$s->cnee->checkPostcode();
				// we have shrink to 75% already
			/*
				if(in_array($s->agent_id, [838])){
					$s->weight = round($s->weight * 75) / 100;
					if(!empty($s->mdata['dim'])) $s->mdata['dim'] = json_decode(strtolower(json_encode($s->mdata['dim'])), true);
					if(empty($s->mdata['dim']) || empty($s->mdata['dim']['w'] * $s->mdata['dim']['h'] * $s->mdata['dim']['d']) || $s->mdata['dim']['w']){
						if(!empty($s->cbm) && $s->cbm > 0.0001 && $s->cbm <= $s->weight / 180){
							$vol = $s->cbm * 750000;
						}else{
							$vol = $s->weight * rand(2625, 3000);
						}
						$s->mdata['dim']['w'] = round($vol / 1000, 1);
						$s->mdata['dim']['d'] = round(sqrt($vol / $s->mdata['dim']['w']) * rand(10,20) / 10, 1);
						$s->mdata['dim']['h'] = round($vol / $s->mdata['dim']['w'] / $s->mdata['dim']['d'], 1);
					}else{
						$s->mdata['dim']['w'] = round($s->mdata['dim']['w'] * 0.9);
						$s->mdata['dim']['h'] = round($s->mdata['dim']['h'] * 0.91);
						$s->mdata['dim']['d'] = round($s->mdata['dim']['d'] * 0.92);
					}
				}*/
				$ss[] = $s;
			}
		}
		echo 'Total '.sizeof($ss)." shipments to lodge\n";
		$r = $apa->createOrderIncludingShipments($ss, $this->prompt('Ref: '), AusPostAPI::CHARGE_CODE_POD);

		if(!empty($r->order)){
			$oid = $r->order->order_id;

			$auPostShipments = array();
			foreach  ( $r->order->shipments as $aushipment ) {
				$auPostShipments[$aushipment->shipment_reference] = $aushipment;
			}

			foreach($ss as $i => $s){

				$aushipment = $auPostShipments[$s->hbn];

				$ts = new Tranship;
				$ts->pid = $s->id;
				$ts->org_id = 101;
				$ts->type = 80;
				$ts->status = 19;
				$ts->connote = $s->ref;
				$ts->time = date('Y-m-d H:i:s');
				$ts->mdata['oid'] = $oid;
				$ts->mdata['sid'] = $aushipment->shipment_id;
				$ts->save();
			}
			echo 'order id : ' . $oid .  " Done\n";
		}else{

			echo "create order failed \n";

			foreach($apa->err as $e){
				if(!empty($e->field) && preg_match('/shipments\[(\d+)\]/', $e->field, $m)){
					echo $ss[$m[1]]->hbn.': '.$e->message."\n";
				}
			}
		}
	}

	public function eParcelLodge(){
		$apa = new AusPostAPI('syd'); //TMA
		$criteria = new CDbCriteria();
		$criteria->addInCondition("ref", preg_split('/[\s;,]+/', $this->prompt('Connotes: ')));
		$rs = ImParcel::model()->findAll($criteria);
		$ss = [];
		foreach($rs as $s){
			if(preg_match('/^AMQ\d{7}/', $s->ref) && empty($s->trans)){
				$s->cnee->checkPostcode();

				// we shave shrinked to 75% already
				/*
				if(in_array($s->agent_id, [838])){
					$s->weight = round($s->weight * 75) / 100;
					if(!empty($s->mdata['dim'])) $s->mdata['dim'] = json_decode(strtolower(json_encode($s->mdata['dim'])), true);
					if(empty($s->mdata['dim']) || empty($s->mdata['dim']['w'] * $s->mdata['dim']['h'] * $s->mdata['dim']['d']) || $s->mdata['dim']['w']){
						if(!empty($s->cbm) && $s->cbm > 0.0001 && $s->cbm <= $s->weight / 180){
							$vol = $s->cbm * 750000;
						}else{
							$vol = $s->weight * rand(2625, 3000);
						}
						$s->mdata['dim']['w'] = round($vol / 1000, 1);
						$s->mdata['dim']['d'] = round(sqrt($vol / $s->mdata['dim']['w']) * rand(10,20) / 10, 1);
						$s->mdata['dim']['h'] = round($vol / $s->mdata['dim']['w'] / $s->mdata['dim']['d'], 1);
					}else{
						$s->mdata['dim']['w'] = round($s->mdata['dim']['w'] * 0.9);
						$s->mdata['dim']['h'] = round($s->mdata['dim']['h'] * 0.91);
						$s->mdata['dim']['d'] = round($s->mdata['dim']['d'] * 0.92);
					}
				}*/
				$ss[] = $s;
			}
		}
		echo 'Total '.sizeof($ss)." shipments to lodge\n";
		$r = $apa->createOrderIncludingShipments($ss, $this->prompt('Ref: '), AusPostAPI::CHARGE_CODE_POD);

		if(!empty($r->order)){
			$oid = $r->order->order_id;

			$auPostShipments = array();
			foreach  ( $r->order->shipments as $aushipment ) {
				$auPostShipments[$aushipment->shipment_reference] = $aushipment;
			}

			foreach($ss as $i => $s){
				$ts = new Tranship;
				$ts->pid = $s->id;
				$ts->org_id = 101;
				$ts->type = 80;
				$ts->status = 19;
				$ts->connote = $s->ref;
				$ts->time = date('Y-m-d H:i:s');
				$ts->mdata['oid'] = $oid;
				$ts->mdata['sid'] = $auPostShipments[$s->hbn]->shipment_id;
				$ts->save();
			}
			echo "Done\n";
		}else{
			foreach($apa->err as $e){
				if(!empty($e->field) && preg_match('/shipments\[(\d+)\]/', $e->field, $m)){
					echo $ss[$m[1]]->hbn.': '.$e->message."\n";
				}
			}
		}
	}

	public function testAPI(){
		$d = array(
			'api_id' => 'd2zsub',
			'method' => 'create',
			'data' => '{"cbm":0.001,"chargecode":"7585","consignee":{"address":"Test Only","city":"","email":"","name":"Test","phone":"0412345678","postcode":"2208","state":"NSW","suburb":"Kingsgrove"},"currency":"AUD","cust_ref":"Test 111","items":[{"brand":"","hs":"","model":"","name":"Car Speaker","name_zh":"","price":"26.9","qty":"1","type":"O"}],"packages":[{"height":"10","length":"10","weight":"1.6","width":"10"}],"packs":"1","shipper":{"address":"xcite   ","city":"","email":"","phone":"","postcode":"","state":"","suburb":"Malaysia","name":"SW4"},"type":"10","weight":"1.6"}',
			//'test' => 'test',
		);

		ksort($d);
		$key = 'ed6be1fef34fff630f180817036c520278f646276707';
		//var_dump(json_decode($d['data']));

		$s = $key;
		foreach($d as $k=>$v){
			if(empty($v)) continue;
			$s.=$k.$v;
		}
		$s .= $key;
		var_dump($s);
		$d['sign'] = strtoupper(md5($s));

		//$c = new curl('http://office.orite.com/hvlv/api/shipment');
		// $c = new curl('https://api.pcaex.com/tracking');
		$c = new curl('https://api.pcaex.com/shipment');
		//$c = new curl('http://localhost/hvlv/api/shipment');
		$data_string = $c->asPostString($d);
		echo "Request:\n";
		var_dump($d);
		$c->setopt(CURLOPT_CUSTOMREQUEST, "POST");
		$c->setopt(CURLOPT_POSTFIELDS, $data_string);
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		//$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		//$c->setopt(CURLOPT_SSL_VERIFYHOST, false);
		$c->exec();
		echo "Response:\n".$c->result."\n";
		var_dump($c->err);
		//$r = json_decode($c->result);
		//print_r($r);
	}

	public function bestRates(){
		$p = ExParcel::model()->find('hbn = :n', [':n' => $this->args[1]]);
		if($p){
			Yii::app()->cache->delete('EPBR_'.$p->id);
			var_dump($p->bestRates(true));
			var_dump($p->getCost());
		}
	}

	public function exRating(){
		$p = ExParcel::model()->findByPk($this->args[1]);
		if(!$p) $p = ExParcel::model()->find('hbn = :n', [':n' => $this->args[1]]);
		if($p) print_r($p->getAgentRate(true));
		else echo "Not Found\n";
	}

	public function exTariff(){
		$p = ExParcel::model()->findByPk($this->args[1]);
		if(!$p) $p = ExParcel::model()->find('hbn = :n', [':n' => $this->args[1]]);
		if($p) print_r($p->calTariff(empty($this->args[2])? false : $this->args[2], false));
		else echo "Not Found\n";
	}

	public function imPerform(){
		$rs = ImParcel::model()->findAll('status = 90');
		foreach($rs as $r){
			$f = $r->getFirstTrack();
			$l = $r->getLastTrack();
			$d = strtotime($l->dt) - strtotime($f->dt);
			if($d < 86400*4){
				echo $r->hbn.','.$r->state.','.$r->postcode.','.(round($d/360)/10)."\n";
			}
		}
	}

	public function heldReport(){
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, array('Location', 'WBN', 'Agent', 'Weight', 'Sender', 'Sender Tel', 'Cnee Name', 'Cnee Tel', 'State', 'Date'));
		$rs = ExParcel::model()->findAll('status = 35 AND consol_id = 0');
		foreach($rs as $r){
			$loc = $r->getLocation();
			if(empty($loc)){
				$pls = [];
				foreach($r->logs as $l){
					if(preg_match('/stored at|moved out/', $l->extra['note'])){
						$pls[] = $l->extra['note'];
					}
				}
				$loc = implode(', ', $pls);
			}
			$xls->addRow($i++, array($loc, '="'.$r->hbn.'"', empty($r->agent)? '' : $r->agent->name, $r->weight, $r->cnor->name, '="'.$r->cnor->tel.'"', $r->cnee->name, '="'.$r->cnee->tel.'"', $r->cnee->state, $r->created));
		}
		$xls->output('held_'.date('ymd').'.xlsx', null, false);
		echo "Done\n";
	}

	public function clearAccr(){
		$t = ExParcel::model()->count(['condition' => 'meta LIKE :m', 'params' => [':m' => '%accr_%']]);
		$ppg = 20000;
		$c = 0;
		for($i = 0; $i < ceil($t/$ppg); $i++){
			$rs = ExParcel::model()->findAll(['condition' => 'meta LIKE :m', 'params' => [':m' => '%accr_%'], 'offset' => $ppg*$i, 'limit' => $ppg]);
			foreach($rs as $r){
				$r->nolog = true;
				unset($r->mdata['accr_tariff']);
				unset($r->mdata['accr_dcdc']);
				$r->updateMeta();
				$c++;
			}
		}
		echo $c." Done\n";
	}

	public function prodPriceList(){
		$poc = $this->prompt('POC LOCODE: ', 'CNCAN');

		$xls = new oExcel;
		$i = 1;
		$xls->setColWidth(array(20,10,20,10,15,10,10,10,15,15,15));
		$xls->addRow($i++, array('ID', 'Type', 'SKU', 'Name', '品名', 'Brand', 'Model', 'Weight', 'Unit', 'Price'.($poc == 'CNCAN'? ' (USD)' : ''), 'HS'));

		$rs = ExProdb::model()->findAll();
		
		foreach($rs as $r){
			$pp = $r->getPocPrice($poc);
			$hs = empty($pp->hs)? $r->hs : $pp->hs;
			$price = empty($pp->price)? $r->price : $pp->price;
			if($poc == 'CNCAN') $price = $r->tax;
			$name = empty($pp->name)? $r->name_zh : $pp->name;
			$xls->addRow($i++, array($r->id, $r->type, $r->sku, $r->name, $name, $r->brand, $r->model, $r->weight, $r->unit, $price, '="'.$hs.'"'));
		}
		$xls->output('priceList_'.$poc.'.xlsx', null, false);
		echo "Done\n";
	}

	public function prodbLoadPrice(){
		//ID, Name, Price, HS, SKU
		$poc = $this->args[1];
		$pocs =  ExChannel::getPocs(true);
		if(!isset($pocs[$poc])) die("POC incorrect\n");
		$data = $this->_loadXlsData($this->args[2]);
		ini_set('precision', 12);
		$c = 0;
		unset($data[0]);
		foreach($data as $r){
			$p = ExProdb::model()->findByPk($r[1]);
			if(empty($p)){
				echo $r[0].':'.$r[1]." not found\n";
				continue;
			}

			$pp = ExProdbPrice::model()->find('pid = :id AND poc = :poc', [':id' => $p->id, ':poc' => $poc]);
			if(empty($pp)){
				$pp = new ExProdbPrice;
				$pp->type = 10;
				$pp->pid = $p->id;
				$pp->poc = $poc;
				$pp->status = 1;
			}
			$pp->price = round($r[3]*100)/100;
			$pp->name = '';
			if(!empty($r[2]) && $r[2] != $p->name) $pp->name = $r[2];
			if(!empty($r[4]) && $r[4] != $p->hs) $pp->hs = $r[4];
			if(!empty($r[5]) && $r[5] != $p->sku) $pp->sn = $r[5];
			$pp->save();
			$c++;
		}
		echo $c." products updated\n";
	}

	public function updateProdPriceByName(){
		//Name, Brand, Price
		$poc = $this->args[1];
		$data = $this->_loadXlsData($this->args[2]);
		ini_set('precision', 12);
		unset($data[1]);
		$c = 0;
		foreach($data as $r){
			if(empty($r[1])) continue;
			$p = ExProdb::model()->find('name_zh = :n AND brand = :b', [':n' => $r[1], ':b'=> $r[2]]);
			if(empty($p)){
				echo $r[1]." not found\n";
				continue;
			}
			$p->mdata['price_'.$poc] = round($r[3]*100)/100;
			$p->noaup = true;
			$p->save();
			$c++;
		}
		echo $c." products updated\n";
	}

	public function testYto(){
		$p = ExParcel::model()->findByPk(260480);
		//$yto = new YtoAPI('K280257267', 'H7arz630',true,false);
		$yto = new YtoAPI('K75005171', '1wISkv31', true, false); //TJ
		$no = $yto->getNumber($p);
		if(empty($no)){
			var_dump($yto->err);
		}else{
			$p->ref = $no;
			if(!empty($yto->result->shortAddress)) $p->mdata['dtb'] = $yto->result->shortAddress;
			$p->save();
			echo $no."\n";
			echo $yto->result->distributeInfo->shortAddress."\n";
		}
	}

	public function loadAgtProdList(){
		//SKU,SN,英文名,中文名,品牌,规格,重量
		$data = $this->_loadXlsData($this->args[2]);
		ini_set('precision', 12);
		$c = 0;
		unset($data[1]);
		foreach($data as $r){
			$p = ExProdb::model()->find('sku = :sku', [':sku' => $r[1]]);
			if(empty($p)){
				$p = new ExProdb;
				$p->type = 90;
				$p->price = '0.00';
				$p->sku = $r[1];
				$p->name = $r[3];
				$p->name_zh = $r[4];
				$p->brand = $r[5];
				$p->model = $r[6];
				$p->weight = $r[7];
				$p->noaup = true;
				$p->save();
			}
			$pp = ExProdbPrice::model()->find('pid = :pid AND agt_id = :aid', [':pid' => $p->id, ':aid' => $this->args[1]]);
			if(empty($pp)){
				$pp = new ExProdbPrice;
				$pp->type = 20;
				$pp->agt_id = $this->args[1];
				$pp->pid = $p->id;
			}
			$pp->sn = $r[2];
			if($r[4] != $p->name_zh) $pp->name = $r[4];
			$pp->status = 1;
			$pp->save();
			$c++;
		}
		echo $c." products loaded\n";
	}

	public function checkAPRates(){
		$apa = new AusPostAPI('syd', false);
		$rs = ImParcel::model()->findAll('status IN (70, 90) AND ref LIKE :amq', [':amq' => 'AMQ%']);
		echo "HBN,Ref,Order,Weight,Postcode,Postage,Revenue\n";
		foreach($rs as $r){
			if(empty($r->trans[0]->mdata['sid'])) continue;
			if(empty($r->trans[0]->mdata['cost'])){
				$a = $apa->getShipments($r->trans[0]->mdata['sid']);
				$r->trans[0]->mdata['cost'] = (string) $a->shipments[0]->items[0]->item_summary->total_cost;
				$r->trans[0]->save();
			}

			$rev = round((5.33 + 7.5 * $r->weight) * 100) / 100;
			echo $r->hbn, ',', $r->ref, ',', $r->trans[0]->mdata['oid'], ',', $r->weight, ',', $r->postcode, ',', $r->trans[0]->mdata['cost'], ',', $rev, "\n";
		}
	}

	public function fixYtoNo(){
		$p = ExParcel::model()->find('hbn = :h', [':h' => $this->args[1]]);
		switch($p->consol->poc){
			case 'CNCA2':
				$yto = new YtoAPI('K76968438', '0DxpUn9Z'); //GZ
			break;
			case 'CNTSN':
				$yto = new YtoAPI('K220122569', 'mSKTWw49'); //TJ
			break;
		}
		$no = (string) $yto->getNumber($p);
		if(empty($no)){
			echo "Error getting No\n";
		}else{
			if($p->ref == $no){
				$p = ExParcel::model()->find('ref = :r AND id != :id', [':r' => $p->ref, ':id' => $p->id]);
				if(!empty($p)){
					$this->args[1] = $p->hbn;
					$this->fixYtoNo();
					return;
				}
			}
			$p->ref = $no;
			if(!empty($yto->result->distributeInfo->shortAddress)) $p->mdata['dtb'] = (string) $yto->result->distributeInfo->shortAddress;
			$p->save();
			oPDF::renderPDF('../expLabel/label_yto', array('p' => $p), 2, $p->hbn.'.pdf');
			echo 'New ref: '.$no."\n";
		}
	}

	public function getConsoleInvoiceAmount(){
		$cid = $this->prompt('Consol ID: ');
		$console = ImcoConsol::model()->findByPk($cid);

		$tot = 0;
		$gst = 0;
		$awb_wt = 0.0;
		$cgb_wt = 0.0;
		if ( isset($console->mdata['awb_wt'] ) )  $awb_wt = $console->mdata['awb_wt'];
		if ( isset($console->mdata['cgb_wt'] ) )  $cgb_wt = $console->mdata['cgb_wt'];

		$owner = NULL;
		foreach($console->shipments as $shipment ) {
			$owner = $shipment->agent;
			break;
		}
		if (empty($owner)) {
			echo  'Console shipment owner is invalid' . PHP_EOL;
			return;
		}

		// check to see if we should including GST (default 10%)
		$includingGST = false;
		if ( isset( $owner->extra['incl_gst'] ) && $owner->extra['incl_gst'] == 1 ) {
			$includingGST = true;
		}

		// try to get org price rate
		$orgRate = OrgRate::model()->find('org_id = :oid AND type = 80', [':oid' => $owner->id]);
		if ( empty($orgRate) ) {
			echo 'Owner: ' . $owner->id . ' price rate is not available yet'  . PHP_EOL ;
			return;
		}

		// get organizaton price rate
		$orgRate2 = OrgRate::model()->find('org_id = :oid AND type = 40',[':oid' => $owner->id]);
		if ( empty($orgRate2) )  {
			echo 'Owner: ' . $owner->id . ' price rate is not available yet' . PHP_EOL;
			return;
		}

		// get org rate details
		$rateInfo = json_decode($orgRate2['meta']);

		// based on basic selected service to calculate the price
		$allServices = OrgRate::getServiceTypes();
		$kg_rate = 0.0; // price rate by kg
		$pc_rate = 0.0; // price rate by piece
		$fixRates = array();
		$includeDelivery = false;
		foreach ( $allServices as $code => $name ) {
			if ( $code != 'DR') { // for local delivery service, we need to calculate price by Australia post price rate
				if ( $rateInfo->$code->selected == 1 ) {
					$kg_rate += floatval( $rateInfo->$code->rate);
					if ( isset($rateInfo->$code->prate) ) {
						$pc_rate += floatval( $rateInfo->$code->prate);
					}

					// for australia special fixed fee
					$docRate = $thcRate = $dlvRate = $sacRate = $sacPrate = $strRate = $strTerms = 0;
					if ( isset($rateInfo->$code->doc_rate) ) {
						$docRate = floatval( $rateInfo->$code->doc_rate);
					}
					if ( isset($rateInfo->$code->thc_rate) ) {
						$thcRate = floatval( $rateInfo->$code->thc_rate);
					}
					if ( isset($rateInfo->$code->dlv_rate) ) {
						$dlvRate = floatval( $rateInfo->$code->dlv_rate);
					}
					if ( isset($rateInfo->$code->sac_rate) ) {
						$sacRate = floatval( $rateInfo->$code->sac_rate);
					}
					if ( isset($rateInfo->$code->sac_prate) ) {
						$sacPrate = floatval( $rateInfo->$code->sac_prate);
					}
					if ( isset($rateInfo->$code->str_rate) ) {
						$strRate = floatval( $rateInfo->$code->str_rate);
					}
					if ( isset($rateInfo->$code->str_terms) ) {
						$strTerms = floatval( $rateInfo->$code->str_terms);
					}
					$fixRates = array(
						'doc_rate' => $docRate,
						'thc_rate' => $thcRate,
						'dlv_rate' => $dlvRate,
						'sac_rate' => $sacRate,
						'sac_prate' => $sacPrate,
						'str_rate' => $strRate,
						'str_terms' => $strTerms
					);
				}
			}

			if ( $code == 'DR' && $rateInfo->$code->selected == 1 ) {
				$includeDelivery = true;
			}

		}
		$rate = array($kg_rate,$pc_rate,$fixRates,$includeDelivery);

		echo var_dump($rate);
		echo PHP_EOL;

		if ( $rate[3] ) {

			foreach($console->shipments as $p){
				$amt = $p->getChargePro($owner, $orgRate);

				// in case parcel with insurance
				// we add insurance value to total value
				if ( $p->insurance > 0 ) {
					$insuranceRatio = 1;
					if ( isset($owner->extra['insurance_invoice_ratio']) ) {
						$insuranceRatio = floatval($owner->extra['insurance_invoice_ratio']);
					}
					$invInsurance = round($p->insurance * ($insuranceRatio / 100),2);
					$items[] = array($p->hbn, 'Insurance Fee',0, 0, 0, $invInsurance, 0, 0, 0);
					$tot += $invInsurance;
				}
				$tot += $amt[0];
			}

		} else {
			$tot = $rate[0] * $awb_wt + $rate[1];
		}

		// append another fixed fee
		// for example : DOC fee, THC fee, DLV fee and SAC fee
		$fixdRates = $rate[2];
		$packages = $console->totShipments();

		// document fee, calculate by per AWB document
		if ( isset($fixdRates['doc_rate']) && $fixdRates['doc_rate'] > 0 ) {
			$docRate = round( $fixdRates['doc_rate'], 2);
			$tot += $docRate;
		}

		// THC fee, calculate by Chargable weight
		if ( isset($fixdRates['thc_rate']) && $fixdRates['thc_rate'] > 0 && $cgb_wt > 0 ) {
			$thcRate = round( $fixdRates['thc_rate'] * $cgb_wt, 2);
			$tot += $thcRate ;
		}

		// DLV fee, calculate by awb weight
		if ( isset($fixdRates['dlv_rate']) && $fixdRates['dlv_rate'] > 0 && $awb_wt > 0 ) {
			$dlvRate = round( $fixdRates['dlv_rate'] * $awb_wt, 2);
			$tot += $dlvRate ;
		}

		// SAC fee, calculate by awb weight and piece
		if ( isset($fixdRates['sac_rate']) && $fixdRates['sac_rate'] > 0 && $awb_wt > 0 ) {
			$sacRate = round( $fixdRates['sac_rate'] * $awb_wt + $fixdRates['sac_prate'] * $packages, 2);
			$tot += $sacRate ;
		}

		// if including GST we add GST
		if ( $includingGST ) {
			$gst = round($tot * 10 / 100,2);
			$tot += $gst;
		}

		echo 'Invoice total is : ' . $tot . PHP_EOL;
		echo 'GST is : ' . $gst .PHP_EOL ;

	}

	public function consolMvPlt(){
		$fid = $this->prompt('From Consol ID: ');
		$fpn = $this->prompt('From Plt#: ');
		$fm = Manifest::model()->find('ref = :n AND consol_id = :c AND type = 60', [':n' => trim($fpn), ':c' => $fid]);
		if(empty($fm)) die("Pallet not found!\n");

		$tid = $this->prompt('To Consol ID: ');
		$tpn = $this->prompt('To Plt#: ');
		$tm = Manifest::model()->find('ref = :n AND consol_id = :c AND type = 60', [':n' => trim($tpn), ':c' => $tid]);
		if(empty($tm)){
			if(!empty($tpn) && strtoupper($this->prompt('Pallet not found, create new?', 'N')) == 'Y'){
				$tm = new Manifest;
				$tm->type = 60;
				$tm->consol_id = $tid;
				$tm->ref = $tpn;
				$tm->save();
			}else{
				die("Goodbye\n");
			}
		}

		if(strtoupper($this->prompt('Continue?', 'N')) == 'Y'){
			$sql = "UPDATE shipment SET consol_id = ".$tid." WHERE id IN (SELECT fid FROM mani_map WHERE mani_id = ".$fm->id.")";
			$this->db->createCommand($sql)->execute();
			$sql = "UPDATE mani_map SET mani_id = ".$tm->id." WHERE mani_id = ".$fm->id;
			$this->db->createCommand($sql)->execute();
			echo "Done\n";
		}else{
			echo "Cancelled\n";
		}
	}

	//vip price
	public function vipCosts(){
		$xls = new oExcel;
		$i = 1;
		$xls->setColWidth(array(20,10,20,10,15,10,10,10,15,15,15));
		$xls->addRow($i++, array('WBN', 'Depot', 'Status', 'Agent', 'Weight', 'State', 'Type', 'Rating Code', 'Rate', 'Charge', 'Cost', 'P/L'));
		for($j = 0; $j < 100; $j++){
			$rs = ExParcel::model()->findAll([
				'condition' => 'status > 20 AND status < 100',
				'offset' => $j*1000,
				'limit' => '1000',
				'order' => 't.id DESC',
				]);
			foreach($rs as $p){
				if(!$p->serviceGrade()) continue;
				$rate = $p->getAgentRate();
				if(empty($p->consol->exrate)){
					echo $p->consol->no." no exrate\n";
					continue;
				}
				$cost = round($p->getCost() / $p->consol->exrate * 100) / 100;
				$typ = $p->goodsType();
				$xls->addRow($i, array($p->hbn, $p->odepot->shortName(1), $p->getStatus(), empty($p->agent)? '' : $p->agent->name, $p->weight, $p->state, $typ, $rate[0], $rate[1]->perkg, $rate[2], $cost, '=J'.$i.'-K'.$i));
				$i++;
			}
		}
		$xls->output('vip_pnl_report.xlsx', null, false);
		echo "Done\n";
	}

	public function prodlist(){
		$xls = new oExcel;
		$i = 1;
		$xls->setColWidth(array(20, 20));
		$xls->addRow($i++, array('ID', 'Name', '品名', 'Brand', 'Model', 'HS', 'Weight', 'Unit', 'Price', 'SKU', 'Rank'));
		$db = [];
		for($j = 0; $j < 100; $j++){
			$rs = ExParcel::model()->findAll([
				'condition' => 'status > 20 AND status < 100',
				'offset' => $j*1000,
				'limit' => '1000',
				'order' => 't.id DESC',
				]);
			foreach($rs as $p){
				if(empty($p->eitems['pid'])) continue;
				foreach($p->eitems['pid'] as $pid){
					if(!isset($db[$pid])) $db[$pid] = 0;
					$db[$pid]++;
				}
			}
		}
		
		foreach($db as $id=>$w){
			$pd = ExProdb::model()->findByPk($id);
			$xls->addRow($i, array($id, $pd->name, $pd->name_zh, $pd->brand, $pd->model, $pd->hs, $pd->weight, $pd->unit, $pd->price, $pd->sku, $w));
			$i++;
		}
		$xls->output('prod_report.xlsx', null, false);
		echo "Done\n";	
	}

	/**
	 * update all org sell rate as the following
	 *  R0 +0.5
	 *  R1 +1.0
	 *  +SR2 35
	 */
	public function updateOrgSellRate(){
		// status 1 : active
		// type : 60 -> export agent
		// type : 65 -> export subagent
		$rs = Org::model()->findAll('type = 60 or type = 65 and status = 1');
		foreach ( $rs as $r ) {
			$r0 = SellRate::model()->find('org_id = :aid AND code = :c AND vto IS NULL', [':aid' => $r->id, ':c' => 'R0']);
			if(empty($r0)) continue;
			$r0_pkg = $r0->perkg;

			$r0 = SellRate::model()->find('org_id = :aid AND code = :c AND vfrom = "2017-05-22"', [':aid' => $r->id, ':c' => 'T2']);
			if(empty($r0)){
				$r0 = new SellRate;
				$r0->type = 20;
				$r0->currency = 1;
				$r0->vfrom = '2017-05-22';
				$r0->org_id = $r->id;
				$r0->code = 'T2';
				$r0->perkg = $r0_pkg;
				$r0->item = 8;
				$r0->save();
			}else{
				$r0->perkg = $r0_pkg;
				$r0->save();
			}

			$r0 = SellRate::model()->find('org_id = :aid AND code = :c AND vfrom = "2017-05-22"', [':aid' => $r->id, ':c' => 'T3']);
			if(empty($r0)){
				$r0 = new SellRate;
				$r0->type = 20;
				$r0->currency = 1;
				$r0->vfrom = '2017-05-22';
				$r0->org_id = $r->id;
				$r0->code = 'T3';
				$r0->perkg = $r0_pkg;
				$r0->item = 12;
				$r0->save();
			}else{
				$r0->perkg = $r0_pkg;
				$r0->save();
			}

			$r0 = SellRate::model()->find('org_id = :aid AND code = :c AND vfrom = "2017-05-22"', [':aid' => $r->id, ':c' => 'T4']);
			if(empty($r0)){
				$r0 = new SellRate;
				$r0->type = 20;
				$r0->currency = 1;
				$r0->vfrom = '2017-05-22';
				$r0->org_id = $r->id;
				$r0->code = 'T4';
				$r0->perkg = $r0_pkg;
				$r0->item = 18;
				$r0->save();
			}else{
				$r0->perkg = $r0_pkg;
				$r0->save();
			}

			/*
			if(!in_array('R0', $cr)){
				$r0 = SellRate::model()->find('org_id = :aid AND code = :c AND vfrom = "2017-03-01"', [':aid' => $r->id, ':c' => 'R0']);
				if(empty($r0)){
					$r0 = new SellRate;
					$r0->type = 20;
					$r0->currency = 1;
					$r0->vfrom = '2017-03-01';
					$r0->org_id = $r->id;
					$r0->code = 'R0';
					$r0->perkg = '6';
					$r0->save();
				}
			}

			if(!in_array('R1', $cr)){
				$r0 = SellRate::model()->find('org_id = :aid AND code = :c AND vfrom = "2017-03-01"', [':aid' => $r->id, ':c' => 'R1']);
				if(empty($r0)){
					$r0 = new SellRate;
					$r0->type = 20;
					$r0->currency = 1;
					$r0->vfrom = '2017-03-01';
					$r0->org_id = $r->id;
					$r0->code = 'R1';
					$r0->perkg = '5.5';
					$r0->save();
				}
			}

			if(!in_array('SR2', $cr)){
				$r0 = SellRate::model()->find('org_id = :aid AND code = :c AND vfrom = "2017-03-01"', [':aid' => $r->id, ':c' => 'SR2']);
				if(empty($r0)){
					$r0 = new SellRate;
					$r0->type = 20;
					$r0->currency = 1;
					$r0->vfrom = '2017-03-01';
					$r0->org_id = $r->id;
					$r0->code = 'SR2';
					$r0->item = 35;
					$r0->save();
				}
			}
			*/
			echo "Done for org - " . $r->id . "\n";
		}
		echo "Done\n";
	}

	public function U12storage(){
		$shelves = ['A' => [7,5], 'B' => [7,5], 'C' => [8,5], 'D' => [8,5], 'E' => [9,5], 'F' => [9,5], 'G' => [9,5], 'H' => [9,5], 'I' => [9,6], 'J' => [9,6], 'K' => [9,6], 'L' => [9,6]];
		$row_format = '%02d';
		$lvl_format = '%1d';
		$cells = 8;
		$i = 1;
		
		foreach($shelves as $s=>$p){
			for($r = 1; $r <= $p[0]; $r++){
				if($s == 'H' && $r == 7) $p[1] = 6; //H07+ 6 lvls;
				for($l = 1; $l <= $p[1]; $l++){
					for($c = 1; $c <= $cells; $c++){
						$name = $s.sprintf($row_format, $r).'-'.sprintf($lvl_format, $l).'-'.$c;
						$stg = Storage::model()->find('wid = 106 AND name = :n', [':n' => $name]);
						if(empty($stg)){
							$stg = new Storage;
							$stg->wid = 106;
							$stg->pid = 1;
							$stg->type = 60;
							$stg->name = $name;
							$stg->code = 'ES-'.$name;
							$stg->cap_item = 1;
						}
						$stg->status = 1;
						$stg->wt = $i;
						$stg->save();
						//echo $name."\n";
						$i++;
					}
				}
			}
		}
		echo ($i-1)." spaces\n";
	}

	public function loadBillingContacts(){
		$tsv = file($this->args[1]);
		foreach($tsv as $l){
			list($id, $ems) = explode("\t", trim($l));
			if(empty($id)) continue;
			$ems = explode(",", $ems);
			foreach($ems as $e){
				$oc = OrgContact::model()->find('email = :e and org_id = :id', [':e' => $e, ':id' => $id]);
				if(empty($oc)){
					$oc = new OrgContact;
					$oc->org_id = $id;
					$oc->name = 'Billing';
				}
				$oc->email = $e;
				$oc->func = 1;
				$oc->status = 1;
				$oc->save();
			}
		}
		echo "Done\n";
	}

	public function loadHS48(){
		$data = $this->_loadXlsData($this->args[1]);
		$sql = 'UPDATE hs SET active = 9';
		$this->db->createCommand($sql)->execute();

		$sql = 'SELECT distinct(unit),uc FROM `hs`';
		$us = $this->db->createCommand($sql)->queryAll();
		$ucm = [];
		foreach($us as $r){
			if(!empty($r['unit']))	$ucm[$r['unit']] = $r['uc'];
		}
		$ucm['套'] = '006';
		$ucm['件、套'] = '011';
		$ucm['盒'] = '140';
		$c = 0;
		foreach($data as $r){
			if(empty($r[1])) continue;
			$h = sprintf('%08s', $r[1]);
			$u = preg_replace('/（.+）/', '', $r[3]);
			$hs = HS::model()->find('hs = :h', [':h' => $h]);
			if(empty($hs)){
				if(!empty($u) && !isset($ucm[$u])) echo $u." unit unknown\n";
				$hs = new HS;
				$hs->hs = $h;
				$hs->name = $r[2];
				$hs->unit = $u;
				$hs->uc = empty($u)? '' : $ucm[$u];
			}
			$hs->price = $r[4];
			$hs->rate = empty($r[5])? 0 : $r[5];
			$hs->active = empty($hs->rate)? 0 : 1;
			$hs->save();
			$c++;
		}
		echo $c." HS updated\n";
	}

	public function batchRevokeInvoice(){
		$rs = Invoice::model()->findAll("dpt_id = 106 AND date > '2016-03-31'");
		$c = 0;
		foreach($rs as $r){
			$r->revoke();
			$c++;
		}
		echo $c." invoices revoked\n";
	}

	public function fixInvoicePaid(){
		$rs = Invoice::model()->findAll("status IN (3, 7, 9)");
		$c = 0;
		foreach($rs as $r){
			$bs = $r->status;
			$r->checkPaid();
			if($bs != $r->status){
				$r->save();
				$c++;
			}
		}
		echo $c." invoices fixed\n";
	}

	public function exportCNids(){
		$zf = 'Chinese_IDs_'.date('YmdHi').'.zip';
		$zip = new ZipArchive;
		$zip->open($zf, ZipArchive::CREATE);
		$joint = $this->prompt('Joint: ', 'N');
		$criteria = new CDbCriteria();
		$criteria->addInCondition("hbn", explode(",", $this->prompt('HBNs: ')));
		$rs = ExParcel::model()->findAll($criteria);
		foreach($rs as $p){
			$cnid = $p->cnee->cnid;
			if(!empty($cnid)){
				if(strtoupper($joint) == 'Y'){
					if(empty($cnid->joint)) $cnid->joinPhoto();
					if($cnid->photo_joint){
						$jn = $cnid->no.'.jpg';
						if(in_array($p->consol->poc, ['CNKMG'])) $jn = iconv('UTF-8', 'GB18030', $p->cnee->name.$jn);
						$zip->addFile($cnid->photo_joint->getFile(), $jn);
					}
				}else{
					if($cnid->photo_front){
						$zip->addFile($cnid->photo_front->getFile(), $cnid->no.'_1.jpg');
						$_sum[] = $cnid->no.'_1.jpg';
					}
					if($cnid->photo_back){
						$backname = $cnid->no.'_2.jpg';
						if($cnid->photo_front->hash == $cnid->photo_back->hash){//add same file by string
							$zip->addFromString($backname, file_get_contents($cnid->photo_back->getFile()));
						}else{
							$zip->addFile($cnid->photo_back->getFile(), $backname);
						}
					}
				}
			}else{
				echo $p->hbn." No CNID\n";
			}
		}
		$zip->close();
		echo "Done\n";
	}

	public function checkInv(){ // check any missing shipments in invoice
		$rs = Invoice::model()->findAll('type = 20 AND date > :d', [':d' => '2016-04-01']);
		foreach($rs as $r){
			foreach($r->lines as $il){
				$ps = [];
				foreach($il->mdata['items'] as $p){
					$ps[] = $p[0];
				}
				$pl = $il->mm();
				foreach($pl->lines as $sl){
					$s = $sl->mm();
					if(in_array($s->status, [100,102])) continue;
					if(!in_array($s->hbn, $ps)){
						echo 'Inv: '.$r->no."\t PL:".$pl->ref." \t HBN:".$s->hbn." Status: ".$s->getStatus()."\n";
					}
				}
			}
		}
	}

	public function cqm2p(){
		$cid = $this->prompt('Consol ID: ');
		$data = $this->_loadXlsData($this->args[1]);
		$ppu = [];
		foreach($data as $rid => $d){
			if($rid < 4) continue;
			if(!empty($d[18])){
				$gi = 0;
				$p = ExParcel::model()->find('hbn = :h AND consol_id = :cid', [':h' => $d[18], ':cid' => $cid]);
				if(empty($p)) echo $d[18]." not found in consol\n";
			}

			if(!$p) continue;
			$pd = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
			$gi++;
			if(empty($pd)){
				echo 'Unable to find product #'.$gi.' in '.$d[18]."\n";
				continue;
			}
			$price = empty($pd->mdata['price_CNCHQ'])? $pd->price : $pd->mdata['price_CNCHQ'];
			$up = round($d[7] / $d[8] * 100) / 100;
			if($price != $up){
				if(in_array($pd->id, $ppu)){
					echo $pd->id." already updated\n";
					if($price < $up) continue;
				}
				$tr = HS::getUpr($pd->hs, 'rate') * 100;
				if($d[11] != $tr.'%') echo $pd->id." rate mismatch\n";
				$pd->mdata['price_CNCHQ'] = $up;
				$pd->noaup = true;
				$pd->save();
				$ppu[] = $pd->id;
				echo $pd->id." updated ".$price." => ".$up."\n";
			}
		}
		echo "Done\n";
	}

	public function pnl(){
		$dpt = $this->prompt('Depot: ', 106);
		$sd = $this->prompt('Start Date: ', date('Y-m-01'));
		$ed = $this->prompt('End Date: ', date('Y-m-d'));
		$rs = ExcoConsol::model()->findAll("created >= :sd AND created <= :ed AND status > 20", [':sd' => $sd, ':ed' => $ed]);
		$tw = 0;
		$tt = 0;
		$ttw = 0;
		$awnr = [];
		foreach($rs as $r){
			$tw += $r->totWeight();
			if($r->dpt_id != $dpt) continue;
			foreach($r->shipments as $p){
				if($p->odpt_id != $dpt) continue;
				$mm = ManiMap::model()->with('manifest')->find("fid = :id AND manifest.type = 40", [':id' => $p->id]);
				if(empty($mm)){
					echo $p->hbn, " not in pickup list\n";
					continue;
				}
				if($mm->manifest->bwf != 1){
					echo $p->hbn, " ", $mm->manifest->ref, " not invoiced\n";
				}
				$ar = $p->getAgentRate();
				if(empty($ar[1])){
					if(!in_array($p->agent_id, $awnr)){
						echo 'Agent '.$p->agent_id.' '.$p->agent->name." has no rate!\n";
						$awnr[] = $p->agent_id;
					}
				}else{
					$tt += $ar[2];
					$ttw += $p->weight;
				}
			}
		}
		echo 'Total weight: '.$tw."\n";
		echo 'Total AR: '.$tt."\n";
		echo 'AR Weight: '.$ttw."\n";
	}

	public function ExGoodsDist(){
		$sd = $this->prompt('Start Date: ', date('Y-m-01'));
		$ed = $this->prompt('End Date: ', date('Y-m-d'));
		$rs = ExcoConsol::model()->findAll('created >= :sd AND created < :ed AND status > 10', [':sd' => $sd, ':ed' => $ed]);
		$o = ['B' => [0, 0], 'M' => [0, 0], 'O' => [0, 0], 'X' => [0, 0]];
		foreach($rs as $r){
			foreach($r->shipments as $p){
				$t = $p->goodsType();
				if(isset($o[$t])){
					$o[$t][0]++;
					$o[$t][1]+= $p->weight;
				}
			}
		}
		echo "Type, Packs, Weight\n";
		foreach($o as $t=>$d){
			echo $t,',',$d[0],',',$d[1],"\n";
		}
	}

	function exportEpsInvoice(){
		$sd = $this->prompt('Start Date: ', date('Y-m-d', strtotime('-1 week')));
		$ed = $this->prompt('End Date: ', date('Y-m-d'));
		//invoices
		$rs = Invoice::model()->findAll('to_id = 1002 AND status NOT IN (1,10) AND `date` >= :sd AND `date` <= :ed', [':sd' => $sd, ':ed' => $ed]);
		$zf = 'EPS_Invoice_'.$ed.'.zip';
		$zip = new ZipArchive;
		$zip->open($zf, ZipArchive::CREATE);
		foreach($rs as $r){
			$zip->addFromString($r->no.'.pdf', oPDF::renderPDF('invoice', array('inv'=>$r), 0));
			$xls = $this->invoiceXls($r);
			$zip->addFromString($r->no.'.xlsx', $xls->output(false, 'Excel2007', false));
		}

		//statement
		$rs = Invoice::model()->findAll('to_id = 1002 AND status NOT IN (1,9,10)');
		$zip->addFromString('Statement.pdf', oPDF::renderPDF('statement', array('invs'=>$rs), 0));
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, array('Number', 'Bill to', 'Agent ID', 'Status', 'Type', 'Branch', 'Date', 'Due', 'Currency', 'Total', 'Paid', 'Balance','Total Weight'));
		$xls->setColWidth(array(10,25,10,15,10,15,15,15,10,15,15,15,15));
		$sr = $i;
		foreach($rs as $r){
			$xls->addRow($i++, array('="'.$r->no.'"', empty($r->to_id)? "" : $r->cust->name, $r->to_id, $r->getStatus(), $r->getType(), $r->getBranch(), $r->date, $r->due, $r->getCurrency(), $r->total, $r->paid(), $r->getBalance(), $r->totWeight()));
		}
		$xls->addRow($i++, ['', 'Sub Total', '', '', '', '', '', '', '', '=SUM(J'.$sr.':J'.($i-2).')', '=SUM(K'.$sr.':K'.($i-2).')', '=SUM(L'.$sr.':L'.($i-2).')', '=SUM(M'.$sr.':M'.($i-2).')']);
		$zip->addFromString('Statement.xlsx', $xls->output(false, 'Excel2007', false));
		$zip->close();
		echo "Done\n";
	}

	public function invoiceXls($model){
		$xls = new oExcel;
		$i = 1;
		switch($model->type){
			case 10:
			$xls->addRow($i++, array('HBN', 'Detail', 'Packages', 'Weight', 'CBM', 'Base Rate', 'Rate', 'Unit', 'Amount'));
			$xls->setFont('A1:I1', array('bold' => true));
			$qty = 0;
			$wei = 0;
			$cbm = 0;
			$tot = 0;
			foreach($model->lines as $il){
				if(empty($il->mdata['items'])) continue;
				foreach($il->mdata['items'] as $si=>$r){
					$xls->addRow($i++, array($r[0], $r[1], $r[2], $r[3], $r[4], $r[6], $r[7], $r[8], $r[5]));
					$tot += $r[5];
					$qty += $r[2];
					$wei += $r[3];
					$cbm += $r[4];
				}
			}
			$xls->addRow($i, array('', 'Total:', $qty, $wei, $cbm, '', '', '', $tot));
			$xls->setFont('A'.$i.':I'.$i, array('bold' => true));
			break;
			case 20:
			$xls->addRow($i++, array('HBN', 'Shipper', 'Packages', 'Weight', 'Type', 'Rate', 'Amount'));
			$xls->setFont('A1:G1', array('bold' => true));
			$qty = 0;
			$wei = 0;
			$cbm = 0;
			$tot = 0;
			foreach($model->lines as $il){
				if(empty($il->mdata['items'])) continue;
				foreach($il->mdata['items'] as $si=>$r){
					$xls->addRow($i++, array($r[0], $r[1], 1, $r[2], $r[4].'('.$r[5].')', $r[6], $r[7]));
					$tot += $r[7];
					$qty ++;
					$wei += $r[2];
				}
			}
			$xls->addRow($i, array('', 'Total:', $qty, $wei, '', '', $tot));
			$xls->setFont('A'.$i.':G'.$i, array('bold' => true));
			break;
			case 30:
			case 40:
				$xls->addRow($i++, array('Code', 'Description', 'Amount', 'Qty', 'Sub Total'));
				$xls->setFont('A1:G1', array('bold' => true));
				$tot = 0;
				foreach($model->lines as $il){
					$xls->addRow($i++, array($il->ccode, $il->det, AppHelper::money_format('%i',$il->amount), $il->qty, AppHelper::money_format('%i',$il->amount * $il->qty)));
					$tot += $il->amount * $il->qty;
				}
				$xls->addRow($i, array('', 'Total:', '', '', $tot));
				$xls->setFont('A'.$i.':G'.$i, array('bold' => true));
			break;
		}
		$i++;
		return $xls;
	}

	public function yc2pca(){
		$data = $this->_loadXlsData($this->args[1]);
		$xls = new oExcel;
		$xls->setColWidth([10,15,15,12,12,40,10,10,10,10,12,12,40,10,10,10,10,10,10,10,10,10,40,15,15,10,10,10,15,10,30]);
		$i = 1;
		$sender = $this->prompt('Sender: ');
		$sender_tel = $this->prompt('Sender Tel: ');
		$xls->addRow($i++, ['序号','运单号','参考号','发货人','电话','地址','市/区','洲/省','邮编','国家','收货人','电话','地址','区','市','洲/省','邮编','国家','包裹数量','毛重(kg)','体积(m3)','分类','中文品名','品牌','规格','申报货币','申报单价','件数','HS编码','保费','备注']);
		//map data
		$map = ['序号' => -1, '商家名称' => 3, '店铺名称' => 3, '收件人' => 10, '收件地址' => 12, '收件人手机' => 11, '包裹重量' => 19, '快递单号' => 1, '货品名称' => 22, '货品数量' => 27, '收件省' => 15, '收件市' =>14, '收件区' => 13, '省' => 15, '市' => 14, '区' => 13, '地址' => 12, '手机' => 11, '货品重量' => -1];
		$mapped = [];
		$kc = 0;
		foreach($data[1] as $c => $h){
			if(isset($map[$h])){
				$mapped[$c] = $map[$h];
				if($map[$h] == 1) $kc = $c;
			}else{
				echo $h." cannot be mapped\n";
			}
		}
		unset($data[1]);
		$pc = 1;
		foreach($data as $j => $r){
			$rr = ['', '','','','','','','','','','','','','','','','','','','','','','','','','','','','','',''];
			foreach($mapped as $k=>$t){
				if($t < 0) continue;
				$rr[$t] = in_array($t, [2,4,11])? '="'.$r[$k].'"' : $r[$k];
			}
			$rr[21] = 'O';
			if(preg_match('/奶粉/', $rr[22])){
				$rr[21] = preg_match('/成人/', $rr[22])? 'M' : 'B';
			}
			if($j == 2 || $data[$j][$kc] != $data[$j-1][$kc]){
				$rr[0] = $pc++;
				if(empty($rr[3])) $rr[3] = $sender;
				if(empty($rr[4])) $rr[4] = '="'.$sender_tel.'"';
				$rr[19] = round($rr[19] / 100) / 10;
				$xls->addRow($i++, $rr);
			}else{
				$xls->addRow($i++, ['', '','','','','','','','','','','','','','','','','','','','', $rr[21], $rr[22],'','','','', $rr[27],'','','']);
			}
		}
		$xls->output(preg_replace('/\.xls(x)?/', '_pca.xlsx', $this->args[1]), null, false);
	}

	public function wms2pca(){
		$data = $this->_loadXlsData($this->args[1]);
		$xls = new oExcel;
		$xls->setColWidth([10,15,15,12,12,40,10,10,10,10,12,12,40,10,10,10,10,10,10,10,10,10,40,15,15,10,10,10,15,10,30]);
		$i = 1;
		//$sender = $this->prompt('Sender: ');
		//$sender_tel = $this->prompt('Sender Tel: ');
		$xls->addRow($i++, ['序号','运单号','参考号','发货人','电话','地址','市/区','洲/省','邮编','国家','收货人','电话','地址','区','市','洲/省','邮编','国家','包裹数量','毛重(kg)','体积(m3)','分类','中文品名','品牌','规格','申报货币','申报单价','件数','HS编码','保费','备注']);
		//map data
		$map = ['订单流水' => -1, '订单编号' => 2, '委托客户' => 10, '收货地址' => 12, '电话' => 11, '包裹重量' => 19, '快递单号' => 1, '发货人电话' => 4, '收货人' => 10, '省' => 15, '市' => 14, '区' => 13, '地址' => 12, '委托客户' => 3, '商品情况' => 22];
		$mapped = [];
		$kc = 0;
		foreach($data[1] as $c => $h){
			if(isset($map[$h])){
				$mapped[$c] = $map[$h];
				if($map[$h] == 1) $kc = $c;
			}else{
				//echo $h." cannot be mapped\n";
			}
		}
		unset($data[1]);
		$pc = 1;
		foreach($data as $j => $r){
			$rr = ['', '','','','','','','','','','','','','','','','','','','','','','','','','','','','','',''];
			foreach($mapped as $k=>$t){
				if($t < 0) continue;
				if($t == 22){
					$itms = explode("\n", $r[$k]);
					continue;
				}
				$rr[$t] = $r[$k];
			}
			$rr[0] = $pc++;
			//$rr[3] = $sender;
			$rr[4] = '="0286669222"';
			$rr[19] = round($rr[19] / 100) / 10;
			foreach($itms as $ii => $itm){
				if($ii > 0){
					$rr = ['', '','','','','','','','','','','','','','','','','','','','', '', '', '','','','', '','','',''];
				}
				if(!preg_match('/^(.+)\s*X\s+(\d+)/', trim($itm), $m)) continue;
				$g = $m[1];
				$q = $m[2];
				$t = 'O';
				if(preg_match('/奶粉/', $g)){
					$t = preg_match('/成人/', $g)? 'M' : 'B';
				}
				$rr[21] = $t;
				$rr[22] = $g;
				$rr[27] = $q;
				$xls->addRow($i++, $rr);
			}
		}
		$xls->output(preg_replace('/\.xls(x)?/', '_pca.xlsx', $this->args[1]), null, false);
	}

	public function fixNoItemType(){
		$id = $this->prompt('Consol No: ');
		$c = ExcoConsol::model()->findByPk($id);
		foreach($c->shipments as $p){
			$up = false;
			foreach($p->eitems['g'] as $gi=>$g){
				if(empty($p->eitems['type'][$gi])){
					$p->eitems['type'][$gi] = 'O';
					if(preg_match('/成人奶粉/', $g)){
						$p->eitems['type'][$gi] = 'M';
					}elseif(preg_match('/奶粉/', $g)){
						$p->eitems['type'][$gi] = 'B';
					}
					$up = true;
				}
			}
			if($up){
				$p->nolog = true;
				$p->save();
			}
		}
		echo "Done\n";
	}

	public function eparcelRating(){
		$rates = [
			'N0' => [4.5, 4.5, 0],
			'N1' => [5.62, 5.89, 0],
			'GF' => [5.62, 5.89, 0.45],
			'WG' => [5.62, 5.89, 0.45],
			'NC' => [5.62, 6.65, 0.58],
			'CB' => [5.62, 6.65, 0.58],
			'N3' => [5.62, 7.34, 1.23],
			'N4' => [5.62, 7.34, 1.23],
			'N2' => [5.62, 7.34, 1.23],
			'V0' => [5.62, 6.09, 0.82],
			'V1' => [5.62, 6.09, 0.82],
			'GL' => [5.62, 7.27, 1.17],
			'BR' => [5.62, 8.24, 1.6],
			'V3' => [5.62, 6.49, 1.12],
			'V2' => [5.62, 8.24, 1.6],
			'Q0' => [5.62, 6.09, 0.82],
			'Q1' => [5.62, 6.09, 0.82],
			'IP' => [5.62, 7.27, 1.5],
			'GC' => [5.62, 6.09, 1.42],
			'Q5' => [5.62, 6.49, 1.12],
			'SC' => [5.62, 7.27, 1.5],
			'Q2' => [5.62, 8.24, 2.03],
			'Q3' => [5.62, 8.24, 3.12],
			'Q4' => [5.62, 8.24, 3.58],
			'S0' => [5.62, 6.09, 1.08],
			'S1' => [5.62, 6.09, 1.08],
			'S2' => [5.62, 8.24, 2.78],
			'W0' => [5.62, 7.27, 2.52],
			'W1' => [5.62, 7.27, 2.52],
			'W2' => [5.62, 8.24, 5.9],
			'W3' => [5.62, 8.24, 6.08],
			'T0' => [5.62, 7.27, 2],
			'T1' => [5.62, 7.27, 2],
			'NT1' => [5.62, 8.24, 6.02],
			'NT2' => [5.62, 8.24, 6.02],
			'NF' => [5.62, 7.54, 4.18],
			'W4' => [5.62, 8.24, 5.52],
			'AAT' => [5.62, 7.54, 2.45],
		];

		$bpa_5 = [
			'N0' => [
				['BPA Sydney Metro 0-5kg', 0, 5, 3.7],
			],
			'ALL' => [
				['BPA Rest of AU 0-500g', 0, 0.5, 4.6],
				['BPA Rest of AU 0.5-1kg', 0.5, 1, 4.67],
				['BPA Rest of AU 1-2kg', 1, 2, 4.73],
				['BPA Rest of AU 2-3kg', 2, 3, 4.93],
				['BPA Rest of AU 3-5kg', 3, 5, 5.25],
			],
		];

		$bpa_22 = [
			'AAT' => [['BPA AAT 5-22kg', 5, 22, 9.21, 1.33]],
			'N1' => [['BPA N1 5-22kg', 5, 22, 4.6, 0]],
			'N2' => [['BPA N2 5-22kg', 5, 22, 8.84, 0.32]],
			'NF' => [['BPA NF 5-22kg', 5, 22, 9.27, 2.84]],
			'NT1' => [['BPA NT1 5-22kg', 5, 22, 7.65, 2.25]],
			'NT2' => [['BPA NT2 5-22kg', 5, 22, 9.27, 2.25]],
			'Q1' => [['BPA Q1 5-22kg', 5, 22, 7.65, 0.36]],
			'Q2' => [['BPA Q2 5-22kg', 5, 22, 9.27, 0.63]],
			'Q3' => [['BPA Q3 5-22kg', 5, 22, 9.27, 1.16]],
			'S1' => [['BPA S1 5-22kg', 5, 22, 7.65, 0.51]],
			'S2' => [['BPA S2 5-22kg', 5, 22, 9.27, 0.71]],
			'T1' => [['BPA T1 5-22kg', 5, 22, 9.27, 1.33]],
			'V1' => [['BPA V1 5-22kg', 5, 22, 7.65, 0.36]],
			'V2' => [['BPA V2 5-22kg', 5, 22, 9.27, 0.51]],
			'W1' => [['BPA W1 5-22kg', 5, 22, 7.65, 1.42]],
			'W2' => [['BPA W2 5-22kg', 5, 22, 9.27, 1.81]],
			'W3' => [['BPA W3 5-22kg', 5, 22, 9.27, 2.43]],
			'W4' => [['BPA W4 5-22kg', 5, 22, 9.27, 2.43]],
		];

		$bpa_zone = [
			'CB' => 'N2',
			'SC' => 'Q2',
			'GC' => 'Q1',
			'WG' => 'N1',
			'GF' => 'N1',
			'N4' => 'Q1',
			'NC' => 'N2',
			'Q4' => 'Q3',
		];

		$bpa_rd = ['BPA Receipted Delivery', 1.6];

		$data = $this->_loadXlsData($this->args[1]);
		$data[1][6] = 'eParcel';
		$data[1][7] = 'Cost';
		$data[1][8] = 'BPA';
		$data[1][9] = 'Cost';
		foreach($data as $i=>&$r){
				if($i < 2) continue;
				if(empty($r[2])){
					echo 'Line '.$i." no postcode error\n";
					continue;
				}
				$pc = trim($r[2]);
				$zm = ZoneMap::model()->find('org_id = 100 AND pc_lo <= :p AND pc_hi >= :p', [':p' => $pc]);
				if(empty($zm)){
					echo $pc." has no Zone\n";
					continue;
				}

				//eparcel
				$z = '';
				if(!empty($rates[$zm->z2])){
					$z = $zm->z2;
				}elseif(!empty($rates[$zm->z1])){
					$z = $zm->z1;
				}else{
					echo $zm->z1.'/'.$zm->z2." has no rate\n";
					continue;
				}
				$amt = $r[3] > 0.5? $rates[$z][1] + $rates[$z][2] *  $r[3] : $rates[$z][0];
				$r[6] = $z;
				$r[7] = $amt;

				//bpa
				$ps = [];
				if($r[3] <= 5){// <5kg
					$ps =  empty($bpa_5[$zm->z2])? $bpa_5['ALL'] : $bpa_5[$zm->z2];
				}elseif($r[3] <= 22){// <22kg
					if(!empty($bpa_22[$zm->z1])){
						$ps = $bpa_22[$zm->z1];
					}elseif(!empty($bpa_zone[$zm->z1]) && !empty($bpa_22[$bpa_zone[$zm->z1]])){
						$ps = $bpa_22[$bpa_zone[$zm->z1]];
					}else{
						echo $zm->z1.'/'.$zm->z2." has no rate\n";
						continue;
					}
				}else{
					echo 'Line '.$i.' '.$r[3]."kg too heavy for BPA\n";
				}

				if(empty($ps)) continue;
				$amt = 0;
				$z = '';
				foreach($ps as $p){
					if($r[3] > $p[1] && $r[3] <= $p[2]){
						$amt = $p[3] + $bpa_rd[1] + (empty($p[4])? 0 : $p[4] * $r[3]);
						$z = $p[0];
						break;
					}
				}

				$r[8] = $z;
				$r[9] = $amt;
		}
		$xls = new oExcel;
		$i = 1;
		foreach($data as $r){
			$xls->addRow($i++, $r);
		}

		$xls->output(preg_replace('/\.xls(x)?/', '_cost.xlsx', $this->args[1]), null, false);
	}

	public function fixWeight(){
		$rs = ExParcel::model()->findAll('agent_id = :aid AND created >= :d', [':aid' => $this->prompt('Agent ID: '), ':d' => $this->prompt('Start Date (yyyy-mm-dd): ')]);
		$c = 0;
		foreach($rs as $r){
			if(empty($r->eitems['q'])) continue;
			$t = $r->goodsType();
			$q = array_sum($r->eitems['q']);
			$up = false;
			if($t == 'B'){
				$wt = 0;
				switch($q){
					case 1:
						$wt = 1.2;
					break;
					case 2:
						$wt = 2.5;
					break;
					case 3:
						$wt = 3.6;
					break;
					case 4:
						$wt = 5;
					break;
					case 6:
						$wt = 7.5;
					break;
				}
				if($r->wtck != $wt){
					$r->wtck = $wt;
					$up = true;
				}
			}elseif($t == 'M' && max($r->weight, $r->wtck) < $q * 1.1){
				$r->wtck = $q * 1.1 + 0.2;
				$up = true;
			}
			if($up){
				echo 'Update weight '.$r->hbn."\n";
				$r->nolog = true;
				$r->save();
				$c++;
			}
		}
		echo $c." shipments updated\n";
	}

	public function reIssueInvoices() {
		// $ids = $this->prompt('Invoices: ');
		// if (strpos($ids, 'to')) {
		// 	$start = explode(' to ', $ids)[0];
		// 	$end = explode(' to ', $ids)[1];
		// 	$ids = array();
		// 	for ($i = $start; $i <= $end; $i++) {
		// 		$ids[] = $i;
		// 	}
		// }	else {
		// 	$ids = explode(' ', $ids);
		// }
		$invoices = Invoice::model()->findAll(array(
			'condition' => 'date >= "2018-05-15" AND type = 20'
		));
		$confirm = strtoupper($this->prompt('Are you sure to reissue invoices? ', 'n')) == 'Y';
		foreach ($invoices as $r) {
			// $r = Invoice::model()->findByPk($id);

			//foreach($rs as $r)
			if ( !empty($r) )
			{
				$r->revoke();
				if ( $r->mayReissue() || $confirm == true) {

					$tot = 0;
					foreach ($r->lines as $il) {
						$pl = $il->mm();
						$items = array();
						$stot = 0;
						foreach ($pl->lines as $l) {
							$p = $l->mm();
							if (in_array($p->status, [100])) continue;
							$rate = $p->getAgentRate(false, false);
							$weight = $p->chargeWeight();
							$typ = $p->goodsType();
							$items[] = array($p->hbn, $p->cnor->name, $weight, $p->cnee->state, $typ, $rate[0], $rate[1]->perkg, $rate[2]);
							$stot += $rate[2];
						}
						$il->amount = $stot;
						$il->mdata['items'] = $items;
						$il->save();
						$tot += $stot;
						$pl->bwf = $pl->bwf | 1;
						$pl->save();
					}
					$r->mdata['name'] = $r->cust->name;
					$r->mdata['address'] = $r->cust->getAddress();
					$r->mdata['payterm'] = empty($r->cust->extra['payterm']) ? 'COD' : $r->cust->extra['payterm'] . ' days';
					$r->due = $r->date;//Invoice::calcDue($r->date, $r->mdata['payterm']);
					$r->status = 2;
					$r->total = $tot;

					// need re-sync with xero
					$r->sync_xero = 0;
					$r->checkPaid();
					$r->save();
					echo $r->no . " reissued" . PHP_EOL;
				} else {
					echo 'can not be reissued ' . PHP_EOL;
				}
			} else {
				echo 'invoice ' . $id . ' not found!'. PHP_EOL;
			}
		}
	}

	public function reIssueInvoice(){
		$id = $this->prompt('Invoice ID: ');
		echo 'got invoice ID  : ' . $id . PHP_EOL;

		$r = Invoice::model()->findByPk($id);

		//foreach($rs as $r)
		if ( !empty($r) )
		{
			$r->revoke();
			if ( $r->mayReissue() || strtoupper($this->prompt('Are you sure to reissue invoice? ', 'n')) == 'Y') {

				$tot = 0;
				foreach ($r->lines as $il) {
					$pl = $il->mm();
					$items = array();
					$stot = 0;
					foreach ($pl->lines as $l) {
						$p = $l->mm();
						if (in_array($p->status, [100])) continue;
						$rate = $p->getAgentRate(false, false);
						$weight = $p->chargeWeight();
						$typ = $p->goodsType();
						$items[] = array($p->hbn, $p->cnor->name, $weight, $p->cnee->state, $typ, $rate[0], $rate[1]->perkg, $rate[2]);
						$stot += $rate[2];
					}
					$il->amount = $stot;
					$il->mdata['items'] = $items;
					$il->save();
					$tot += $stot;
					$pl->bwf = $pl->bwf | 1;
					$pl->save();
				}
				$r->mdata['name'] = $r->cust->name;
				$r->mdata['address'] = $r->cust->getAddress();
				$r->mdata['payterm'] = empty($r->cust->extra['payterm']) ? 'COD' : $r->cust->extra['payterm'] . ' days';
				$r->due = $r->date;//Invoice::calcDue($r->date, $r->mdata['payterm']);
				$r->status = 2;
				$r->total = $tot;

				// need re-sync with xero
				$r->sync_xero = 0;
				$r->checkPaid();
				$r->save();
				echo $r->no . " reissued" . PHP_EOL;
			} else {
				echo 'can not be reissued ' . PHP_EOL;
			}
		} else {
			echo 'invoice not found!'. PHP_EOL;
		}
	}

	public function fixInvoiceWt(){
		$rs = Invoice::model()->findAll("to_id = :aid AND type = 20 AND date >= :d", [':aid' => $this->prompt('Agent ID: '), ':d' => $this->prompt('Start Date (yyyy-mm-dd): ')]);
		foreach($rs as $r){
			$r->revoke();
			//if(!$r->mayReissue()) continue;

			$tot = 0;
			foreach($r->lines as $il){
				$pl = $il->mm();
				$items = array();
				$stot = 0;
				foreach($pl->lines as $l){
					$p = $l->mm();
					if(in_array($p->status, [100])) continue;
					$rate = $p->getAgentRate(false, false);
					$weight = $p->chargeWeight();
					$typ = $p->goodsType();
					$items[] = array($p->hbn, $p->cnor->name, $weight, $p->cnee->state, $typ, $rate[0], $rate[1]->perkg, $rate[2]);
					$stot += $rate[2];
				}
				$il->amount = $stot;
				$il->mdata['items'] = $items;
				$il->save();
				$tot += $stot;
				$pl->bwf = $pl->bwf | 1;
				$pl->save();
			}
			$r->mdata['name'] = $r->cust->name;
			$r->mdata['address'] = $r->cust->getAddress();
			$r->mdata['payterm'] = empty($r->cust->extra['payterm'])? 'COD' : $r->cust->extra['payterm'].' days';
			$r->due = $r->date;//Invoice::calcDue($r->date, $r->mdata['payterm']);
			$r->status = 2;
			$r->total = $tot;

			// need re-sync with xero
			$r->sync_xero = 0;

			$r->save();
			echo $r->no." reissued\n";
		}
	}

	public function rcImportInvoiceByConsole(){
		$no = $this->prompt('Console No: ');
		echo 'got console  : ' . $no . PHP_EOL;
		$consol = ImcoConsol::model()->find('no = :id', [':id' => $no ]);
		if ( !empty($consol) ) {
			//$errors = $consol->genSpecialInvoice();
			$errors = array();

			$errors = array();
			// create invoice based on chargeable or AWB weight
			$awb_wt = 0.0;
			$cgb_wt = 0.0;
			if ( isset($consol->mdata['awb_wt'] ) )  $awb_wt = $consol->mdata['awb_wt'];
			if ( isset($consol->mdata['cgb_wt'] ) )  $cgb_wt = $consol->mdata['cgb_wt'];
			if ( $awb_wt > 0.0 || $cgb_wt > 0.0 ) {

				// because multiple orgs in console
				// we need to create invoice for each org
				// following the below steps
				// step 1
				// split shipments by Org
				$orgShipments = array();
				foreach($consol->shipments as $shipment ) {
					if ( !isset($orgShipments[$shipment->agent->id]) ){
						$orgShipments[$shipment->agent->id] = array(
							'owner' => $shipment->agent,
							'shipments' => array()
						);
					}
					$orgShipments[$shipment->agent->id]['shipments'][] = $shipment;
				}

				echo '$orgShipments : ' . count($orgShipments) . PHP_EOL;

				foreach ( $orgShipments  as $id =>  $org ) {
					$owner = $org['owner'];
					// check to see if we should including GST (default 10%)
					$includingGST = false;
					if (isset($owner->extra['incl_gst']) && $owner->extra['incl_gst'] == 1) {
						$includingGST = true;
					}

					// if existing or not checking
					$inv = Invoice::model()->with('lines')->find('type = 10 AND to_id = :id AND lines.model = :m AND lines.fid = :fid', [':id' => $owner->id, ':m' => 'ImcoConsol', ':fid' => $consol->id]);

					// invoice existing and has been paid
					if (!empty($inv) && $inv->status == 9) {
						$errors[] = 'Console invoice ' . $inv->no . 'has been paid before, please contact account for more';
						continue;
					}

					// if not existing or not pending(1) or posted(2) or overdue(3) we create a new one
					$isUpdate = true;
					if ( empty($inv) ) {
						$inv = new Invoice;
						$isUpdate = false;
						$inv->date = date('Y-m-d'); // for update we shouldn't change invoice data
						$inv->status = 2; // set as posted which means will send to client for paying
					}

					$inv->type = 10; // for import parcels
					$inv->dpmt = Invoice::DPMT_IMPORT;
					$inv->to_id = $owner->id;
					$inv->consol_id = $consol->id;
					$inv->man_id = 0; // in case there is no manifest now
					$inv->dpt_id = $consol->dpt_id;

					// try to get org price rate
					$orgRate = OrgRate::model()->find('org_id = :oid AND type = 80', [':oid' => $owner->id]);
					if (empty($orgRate)) {
						$errors[] = 'Owner: ' . $owner->id . ' price rate is not available yet';
					}

					$inv->currency = $orgRate['currency'];

					// calculate price based on awb weight
					$items = array();

					// get rate returned as [kg_rate, pc_rate,fixedRates,includingDelivery, airfreightRates]
					$rate = $consol->getChargeRate($owner);

					$tot = 0;
					// in case need to charge for Australia local delivery fee
					if ( $rate[3]) {
						$shipments = $org['shipments'];
						$result = $consol->genShipmentsInvoicePro($owner, $orgRate,$shipments);
						if ( $result[0] > 0 ) {
							$tot = $result[0];
							$items = $result[1];
						}
						echo 'local delivery fee created : ' . $tot . PHP_EOL;
					}

					// else
					// we should check for all services
					{

						// for local australia charge
						$showDetails = 0;
						if ( isset($rate[2]) && isset($rate[2]['showdetails']) ) {
							$showDetails = $rate[2]['showdetails'];
						}
						$totWeight = 0;
						if ( $showDetails ) {
							foreach ( $org['shipments'] as $shipment ) {
								$single = $rate[0] * $shipment->weight + $rate[1];
								$totWeight += $shipment->weight;
								//  return array($amt, 0, $kg_rate, $this->weight);
								if ( $single > 0) {
									$items[] = array($shipment->hbn, 'Australia Local Charge Fee', 1, $shipment->weight, 0, $single, 0, $rate[0], $shipment->weight);
									$tot += $single;
								}
							}
						} else {
							$localFee = $rate[0] * $awb_wt + $rate[1];
							//  return array($amt, 0, $kg_rate, $this->weight);
							if ( $localFee > 0) {
								$items[] = array($consol->awb, 'Australia Local Charge Fee', 1, $awb_wt, 0, $localFee, 0, $rate[0], $awb_wt);
								$tot += $localFee;
							}
						}

						// for air freight charge
						if ( !empty($rate[4]) && $rate[4]['rate'] > 0 ) {

							// in case no real weight, we need to calculate again here
							if ( $totWeight <= 0 ) {
								foreach ($org['shipments'] as $shipment) {
									$totWeight += $shipment->weight;
								}
							}

							if ( isset($consol->mdata['org_'.$id]) && isset($consol->mdata['org_'.$id]['cgb_wt']) ) {
								$cw = $consol->mdata['org_'.$id]['cgb_wt'];
								if ( $cw > 0 ) {
									$extraAmount =  $rate[4]['rate'] * $cw;
									$tot += $extraAmount;
									$items[] = array('Chargeable Weight', 'Air Freight', 1,$cw, 0, $extraAmount, 0, $rate[4]['rate'],$cw);
								}
							}

						}

					}

					echo 'others fee created : ' . $tot . PHP_EOL;

					// append another fixed fee
					// for example : DOC fee, THC fee, DLV fee and SAC fee
					$fixdRates = $rate[2];
					$packages = count($org['shipments']);

					// document fee, calculate by per AWB document
					if (isset($fixdRates['doc_rate']) && $fixdRates['doc_rate'] > 0) {
						$docRate = round($fixdRates['doc_rate'], 2);
						$tot += $docRate;
						$items[] = array('DOC', 'DOC Fee', 1, 0, 0, $docRate, 0, $docRate, 0);
					}

					// THC fee, calculate by Chargable weight
					if (isset($fixdRates['thc_rate']) && $fixdRates['thc_rate'] > 0 && $cgb_wt > 0) {
						$thcRate = round($fixdRates['thc_rate'] * $cgb_wt, 2);
						$tot += $thcRate;
						$items[] = array('THC', 'THC Fee', $packages, $cgb_wt, 0, $thcRate, 0, $fixdRates['thc_rate'], 0);
					}

					// DLV fee, calculate by awb weight
					if (isset($fixdRates['dlv_rate']) && $fixdRates['dlv_rate'] > 0 && $awb_wt > 0) {
						$dlvRate = round($fixdRates['dlv_rate'] * $awb_wt, 2);
						$tot += $dlvRate;
						$items[] = array('DLV', 'Delivery Charge', $packages, $awb_wt, 0, $dlvRate, 0, $fixdRates['dlv_rate'], 0);
					}

					// SAC fee, calculate by awb weight and piece
					if (isset($fixdRates['sac_rate']) && $fixdRates['sac_rate'] > 0 && $awb_wt > 0) {
						$sacRate = round($fixdRates['sac_rate'] * $awb_wt + $fixdRates['sac_prate'] * $packages, 2);
						$tot += $sacRate;
						$items[] = array('SAC', 'SAC Scan Fee', $packages, $awb_wt, 0, $sacRate, 0, $fixdRates['sac_rate'], 0);
					}

					// if including GST we add GST
					$gst = 0;
					if ($includingGST) {
						$gst = round($tot * 10 / 100, 2);
						$tot += $gst;
					}
					echo 'gst maybe fee created : ' . $tot . PHP_EOL;

					$tot = round($tot, 2);
					$inv->mdata['awb'] = $consol->awb;
					$inv->mdata['name'] = $owner->name;
					$inv->mdata['address'] = $owner->getAddress();
					$inv->mdata['payterm'] = empty($owner->extra['payterm']) ? '2 days' : $owner->extra['payterm'] . ' days';
					$inv->due = Invoice::calcDue($inv->date, $inv->mdata['payterm']);
					$inv->total = $tot;
					$inv->gst = $gst;

					if ( $inv->total > 0 ) { // avoid create invoice with zero amount
						if ($isUpdate) {
							$inv->sync_xero = 0; // in order to sync with xero again
						}
						$inv->save();

						// remove old invoice lines if existing
						if ($isUpdate) {
							// in case update, we should remove all old invoice lines
							InvLine::model()->deleteAll('inv_id = :lid', [':lid' => $inv->id]);
						}

						$il = new InvLine;
						$il->inv_id = $inv->id;
						$il->amount = $inv->total;
						$il->gst = $gst;
						$il->mdata['items'] = $items;
						$il->model = 'ImcoConsol';
						$il->fid = $consol->id;
						$il->save();
						echo 'invoice created : ' . $inv->no . PHP_EOL;
					}
				}
			} else {
				$errors[] = 'Please set console AWB weight and Chargeable weight firstly';
			}


			if ( !empty($errors) ) {
				echo 'Failed : ' . implode(',', $errors) . PHP_EOL;
			} else {
				echo 'invoice created' . PHP_EOL;
			}
		} else {
			echo 'consol not found'. PHP_EOL;
		}
	}

	/**
	 * recreate EPS invoice by console
	 */
	public function rcEpsInvoiceByConsole(){
		$no = $this->prompt('Console No: ');
		echo 'got console  : ' . $no . PHP_EOL;
		$consol = ImcoConsol::model()->find('no = :id' , [':id' => $no ]);

		$owner = Org::model()->findByPk(1206);

		if ( !empty($consol) ) {
			$inv = Invoice::model()->find('consol_id = :cid',[':cid' => $consol->id]);
			if ( empty($inv) ) {
				$inv = new Invoice;
				$inv->type = 10;
				$inv->dpmt = Invoice::DPMT_IMPORT;
				$inv->to_id = $consol->owner_id;
				$inv->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY; // default set Sydney as warehouse
				$inv->ref = 'ck1-ep' . date('Ymd', strtotime($consol->created));
				$inv->currency = 1;
				$inv->status = 2;
				$inv->consol_id = $consol->id;
				$inv->date = date('Y-m-d', strtotime($consol->created));
				$inv->due = $inv->date;

				$inv->mdata['name'] = $owner->name;
				$inv->mdata['address'] = $owner->getAddress();
				$inv->mdata['payterm'] = empty($owner->extra['payterm'])? 'COD' : $owner->extra['payterm'].' days';
				$inv->save();

			}

			// delete all invoice line
			InvLine::model()->deleteAll('inv_id = :ivid',[':ivid' => $inv->id]);

			// add new lines
			$items = [];
			$tot = 0;
			foreach ( $consol->shipments as $p ) {

				$pc = trim($p->cnee->postcode);
				/*
				$pc = trim($p->cnee->postcode);
				$zoneMap = ZoneMap::model()->find('org_id = 101 AND zone_id = 0 AND pc_lo <= :p AND pc_hi >= :p', [':p' => $pc]);
				$zoneCode = 'N1';
				if (!empty($zoneMap) && !empty($zoneMap['z1']) ) {
					$zoneCode = $zoneMap['z1'];
				}

				// don't calculate weight based on cube again, just use weight directly
				$wt = $p->weight; //$p->chargeWeight();

				// base charge code to get zone rate
				$zr = ZoneRate::model()->with('orgrate')->find("orgrate.type = 80 AND zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND base+item+perkg > 0 AND orgrate.org_id = :oid ", [':s' => $zoneCode, ':w' => $wt,':oid' => 1206]);

				if (empty($zr)) {
					// with a default charge rate
					// normally we shouldn't use default charge
					$amt = 0.0;
					if ( $wt >= 5) {
						$amt = 8.0 + ( $wt - 5) * 0.36; // default only
					} else {
						$amt = 8.0;
					}
				} else {
					// amount = price / piece * quantity + price/pkg * weight
					// quantity is one
					$amt = $zr['item']  + $wt * $zr['perkg'];
				}
				*/
				$amt = $p->getChargeByChargecode(5813,true);
				$zoneMap = ZoneMap::model()->find('chargecode_id = 15 AND zone_id = 1 AND pc_lo <= :p AND pc_hi >= :p', [':p' => $pc]);
				$zoneCode = 'N1';
				if (!empty($zoneMap) && !empty($zoneMap['z1']) ) {
					$zoneCode = $zoneMap['z1'];
				}

				$items[] = [$p->ref, $p->getDesc().'    ' . $zoneCode, $p->pkg, $p->weight, $p->cbm, $amt,$p->cnee->postcode];
				$tot += $amt;

				echo 'Item : ' . $p->ref . ' invoice : ' . $amt . PHP_EOL;
				// in case parcel with insurance
				// we add insurance value to total value
				if ( $p->insurance > 0 ) {
					$insuranceRatio = 1;
					if ( isset($owner->extra['insurance_invoice_ratio']) ) {
						$insuranceRatio = floatval($owner->extra['insurance_invoice_ratio']);
					}
					$invInsurance = round($p->insurance * ($insuranceRatio / 100),2);
					$items[] = array($p->ref, 'Insurance Fee',0, 0, 0, $invInsurance);
					$tot += $invInsurance;
				}

			}

			$il = new InvLine;
			$il->inv_id = $inv->id;
			$il->ccode = 'EPA';
			$il->mdata['items'] = $items;
			$il->det = $consol->no;
			$il->fid = $consol->id;
			$il->model = 'ImcoConsol'; // invoice connected with console directly
			$il->amount = round($tot * 1000) / 1000;
			$il->qty = 1;
			$il->save();

			$inv->getTotal();

			// need re-sync with xero
			$inv->sync_xero = 0;

			$inv->save();

		} else {
			echo 'console not found' . PHP_EOL;
		}

		echo 'All Done ' . PHP_EOL;
	}

	/**
	 * recreate fastway invoice by console
	 */
	public function rcFastwayInvoiceByConsole(){
		$no = $this->prompt('Console No: ');
		echo 'got console  : ' . $no . PHP_EOL;
		$consol = ImcoConsol::model()->find('no = :id' , [':id' => $no ]);
		if ( !empty($consol) ) {
			$inv = Invoice::model()->find('consol_id = :cid',[':cid' => $consol->id]);
			if ( empty($inv) ) {
				$inv = new Invoice;
				$inv->type = 10;
				$inv->dpmt = Invoice::DPMT_IMPORT;
				$inv->to_id = $consol->owner_id;
				$inv->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY; // default set Sydney as warehouse
				$inv->ref = 'fw' . date('Ymd', strtotime($inv->created));
				$inv->currency = 1;
				$inv->status = 2;
				$inv->date = date('Y-m-d', strtotime($inv->created));
				$inv->due = $inv->date;
				$inv->save();
			}

			// delete all invoice line
			InvLine::model()->deleteAll('inv_id = :ivid',[':ivid' => $inv->id]);

			// add new lines
			$items = [];
			$tot = 0;
			foreach ( $consol->shipments as $p ) {
				$amt = $p->getChargeByChargecode('8271',true);
				echo $p->ref . ' amount -> ' . $amt . PHP_EOL;
				$items[] = [$p->ref, $p->getDesc() , $p->pkg, $p->weight, $p->cbm, $amt,$p->cnee->postcode];
				$tot += $amt;
			}
			$inv = Invoice::model()->findByPk($inv->id);
			$il = new InvLine;
			$il->inv_id = $inv->id;
			$il->ccode = 'EPA';
			$il->mdata['items'] = $items;
			$il->det = $consol->no;
			$il->fid = $consol->id;
			$il->model = 'ImcoConsol'; // invoice connected with console directly
			$il->amount = round($tot * 100) / 100;
			$il->qty = 1;
			$il->save();
			$inv->getTotal();
			$inv->consol_id = $consol->id;

			$inv->mdata['name'] = $consol->owner->name;
			$inv->mdata['address'] = $consol->owner->getAddress();
			$inv->mdata['payterm'] = empty($consol->owner->extra['payterm'])? 'COD' : $consol->owner->extra['payterm'].' days';
			$inv->no = 'FW'.$inv->id;

			// need re-sync with xero
			$inv->sync_xero = 0;

			$inv->save();

		} else {
			echo 'console not found' . PHP_EOL;
		}

	}

	public function rcImportOtherInvoiceById(){
		$no = $this->prompt('Invoice ID: ');
		echo 'got invoice ID : ' . $no . PHP_EOL;
		$rs = Invoice::model()->findByPk($no);
		if ( empty($rs) ) {
			echo 'invoice not found' . PHP_EOL;
			return;
		}

		if ( $rs->type != 40 ) {
			echo 'only for others invoice' . PHP_EOL;
			return;
		}

		if ( !$rs->isInvoiceClosed() ) {
			echo 'only for invoice closed' . PHP_EOL;
			return;
		}

		echo 'create credit for the invoice ' . PHP_EOL;
		$oldPayments = $rs->createCreditForMe();

		// create a new invoice the for this month
		$invRef = 'Original Invoice : ' . $rs->no;
		$inv = new Invoice;
		$inv->ref = $invRef;
		$inv->date = date('Y-m-d');
		$inv->posted = $inv->date;
		$inv->status = Invoice::INVOICE_STATUS_POSTED; // set as posted which means will send to client for paying

		$inv->type = $rs->type;
		$inv->dpmt = $rs->dpmt;
		$inv->to_id = $rs->to_id;
		$inv->consol_id = $rs->consol_id;
		$inv->man_id = $rs->man_id;
		$inv->dpt_id = $rs->dpt_id;

		$inv->mdata = $rs->mdata;
		$inv->total = $rs->total;
		$inv->gst = $rs->gst;
		$inv->save();

		echo 'new invoice : ' . $inv->no . 'created ' . PHP_EOL;

		$il = new InvLine;
		$il->inv_id = $inv->id;
		$il->amount = $inv->total;
		$il->gst = $rs->gst;
		$il->model = 'ImcoConsol';
		$il->fid = $rs->consol_id;
		$il->save();

		// apply old payments for the new created invoice
		if ( !empty($oldPayments) ) {
			$inv->applyPayments($oldPayments);
		}

		echo 'all done' . PHP_EOL;

	}

	/**
	 * recreate export invoice
	 */
	public function rcExInvoiceById(){
		$no = $this->prompt('Invoice ID: ');
		echo 'got invoice ID : ' . $no . PHP_EOL;
		$rs = Invoice::model()->findAllByPk($no);
		foreach($rs as $r){
			$tot = 0;
			foreach($r->lines as $il){
				$pl = $il->mm();
				$items = array();
				$stot = 0;
				foreach($pl->lines as $l){
					$p = $l->mm();
					if(in_array($p->status, [100])) continue;
					$rate = $p->getAgentRate(false, false);
					$weight = $p->chargeWeight();
					$typ = $p->goodsType();
					$items[] = array($p->hbn, $p->cnor->name, $weight, $p->cnee->state, $typ, $rate[0], $rate[1]->perkg, $rate[2]);
					$stot += $rate[2];
				}
				$il->amount = $stot;
				$il->mdata['items'] = $items;
				$il->save();
				$tot += $stot;
				$pl->bwf = $pl->bwf | 1;
				$pl->save();
			}
			$r->mdata['name'] = $r->cust->name;
			$r->mdata['address'] = $r->cust->getAddress();
			$r->mdata['payterm'] = empty($r->cust->extra['payterm'])? 'COD' : $r->cust->extra['payterm'].' days';
			$r->due = $r->date;//Invoice::calcDue($r->date, $r->mdata['payterm']);
			$r->status = 2;
			$r->total = $tot;

			// need re-sync with xero
			$r->sync_xero = 0;
			$r->save();
			echo $r->no." reissued\n";
		}
	}

	public function updateInvoiceTo(){
		$rs = Invoice::model()->findAll("to_id = :aid AND date >= :d AND date < date_add(:e, INTERVAL 1 DAY)", [':aid' => $this->prompt('Agent ID: '), ':d' => $this->prompt('Start Date (yyyy-mm-dd): '), ':e' => $this->prompt('End Date (yyyy-mm-dd): ')]);
		foreach($rs as $r){
			$r->mdata['name'] = $r->cust->name;
			$r->mdata['address'] = $r->cust->getAddress();
			$r->mdata['payterm'] = empty($r->cust->extra['payterm'])? 'COD' : $r->cust->extra['payterm'].' days';
			$r->save();
			echo $r->no." updated\n";
		}
	}

	public function fixCnAddr(){
		$rs = ExParcel::model()->findAll('agent_id = 130 AND bwf & 4 > 1');
		foreach($rs as $r){
			if(empty($r->cnee->postcode)){
				$r->cnee->setCnAddr($r->cnee->address);
				$r->cnee->save();
				$r->nolog;
				$r->save();
				echo $r->hbn." fixed\n";
			}
		}
	}

	public function unmaniMatch(){
		$rs = file($this->args[1]);
		$les = file('lodge_error.csv');
		$ers = [];
		foreach($les as $e){
			$es = explode(',',trim($e));
			$ers[$es[0]] = $es;
		}
		$c_e = 0;
		$c_m = 0;
		$c_u = 0;
		foreach($rs as $i => $r){
			$r = trim($r);
			if(empty($r)) continue;
			$rs[$i] = $r;
			if($i == 0) continue;
			if($i > 1){
				$cs = explode(',', $r);
				$cs[0] = substr($cs[0], 0, 10);
				if(isset($ers[$cs[0]])){
					$p = ImParcel::model()->find('ref = :r', [':r' => $cs[0]]);
					$rs[$i] = $r.','.$ers[$cs[0]][1].','.$ers[$cs[0]][2].',,'.(empty($p)? '' : $p->weight.','.$p->postcode);
					$c_e++;
				}else{
					$t = Tranship::model()->find('connote = :c', [':c' => $cs[0]]);
					if(!empty($t) && !empty($t->mdata['oid'])){
						$rs[$i] = $r.','.$t->mdata['oid'].','.$t->mdata['sid'].','.$t->mdata['cost'];
						$c_m++;
					}else{
						$p = ImParcel::model()->find('ref = :r', [':r' => $cs[0]]);
						$rs[$i] = $r.',,,,'.(empty($p)? '' : $p->weight.','.$p->postcode);
						echo $cs[0]." unmanifested\n";
						$c_u++;
					}
				}
			}else{
				$rs[1] = $r.',Manifest No.,Shipment ID,Cost,Weight,Postcode';
			}
		}
		file_put_contents(str_replace('.csv', '_matched.csv', $this->args[1]), implode("\n", $rs));
		echo substr($this->args[1], -7,3)."\n";
		echo 'Matched: '.$c_m."\n";
		echo 'Error Lodgement: '.$c_e."\n";
		echo 'Unmanifested: '.$c_u."\n";
	}

	public function captainHealthImport(){
		$data = $this->_loadXlsData($this->args[1]);
		if(implode(',', $data[1]) != '新运单号,老运单号,身份证姓名,手机,身份证号码,地址,规格,数量,订单号') die("Invalid headings \n");
		unset($data[1]);
		$con = ExcoConsol::model()->findByPk($this->prompt('Consol ID: '));
		if(empty($con)) dir("Invalid consol ID\n");
		$c = 0;
		foreach($data as $d){
			if(empty($d[1])) continue;
			$p = ExParcel::model()->find('hbn = :h', [':h' => $d[2]]);
			if(empty($p)){
				$p = new ExParcel('create');
				$p->cnor = new Addr('create');
				$p->cnee = new Addr('create');
			}
			$p->status = 20;
			$p->hbn = $d[2];
			$p->ref = $d[1];
			$p->cref = $d[9];
			$p->pkg = 1;
			$p->agent_id = 846;
			$p->consol_id = $con->id;
			$p->weight = 1.5;
			$p->cnor->attributes = [
				'name' => 'Captain Health',
			];
			$p->cnor->save();
			$p->cnor_id = $p->cnor->id;

			$p->cnee->attributes = [
				'name' => $d[3],
				'tel' => $d[4],
				'cnid_no' => $d[5],
				'address' => $d[6],
			];
			$p->cnee->setCnAddr($d[6]);
			$p->cnee->save();
			$p->cnee_id = $p->cnee->id;
			$p->eitems['type'][0] = 'O';
			$p->eitems['g'][0] = $d[7];
			$p->eitems['q'][0] = $d[8];
			$p->eitems['pid'][0] = 864;
			$p->save();
			var_dump($p->getErrors());
			$c++;
		}
		echo $c." shipments imported\n";
	}

	public function consolAltCnee(){
		$cid = $this->prompt('Consol ID: ');
		$con = ExcoConsol::model()->findByPk($cid);
		if(empty($con)) die("Consol not found\n");
		$ids = [0];
		$err = [];
		$c = 0;

		foreach($con->shipments as $p){
			//$plt = Yii::app()->db->createCommand('SELECT f.ref FROM mani_map m INNER JOIN manifest f ON f.id = m.mani_id WHERE f.consol_id = '.$cid.' AND f.type = 60 AND m.fid = '.$p->id)->queryScalar();
			//if(empty($plt)) continue;
			if(!empty($p->mdata['AltCnee'])) continue;

			if(empty($p->cnee->cnid_id) || in_array($p->cnee->cnid_id, $ids)){ //dup
				$ss = [];
				if(strlen($p->cnee->name) > 9){
					$ss[] = substr($p->cnee->name, 0, 9).'%';
					$ss[] = substr($p->cnee->name, 0, 6).'%'.substr($p->cnee->name, 9);
				}
				if(strlen($p->cnee->name) > 6){
					$ss[] = substr($p->cnee->name, 0, 6).'%';
					$ss[] = substr($p->cnee->name, 0, 3).'%'.substr($p->cnee->name, 6);
				}
				$ss[] = substr($p->cnee->name, 0, 3).'%';

				if(strlen($p->cnee->name) > 6)	$ss[] = '%'.substr($p->cnee->name, 6);
				$ss[] = '%'.substr($p->cnee->name, 3);

				foreach($ss as $s){
					$nc = Addr::model()->with('cnid')->find(['condition' => 'cnid.status IN (15, 18, 20) AND t.cnid_id > 0 AND t.name LIKE :n AND t.cnid_id NOT IN (SELECT cnid_id FROM addr a INNER JOIN shipment s ON s.cnee_id = a.id WHERE consol_id = :cid) AND t.cnid_id NOT IN ('.implode(',', $ids).')', 'params' => [':n' => $s, ':cid' => $cid], 'order' => 'RAND()']);
					if(!empty($nc)) break;
				}

				if(empty($nc)){
					$err[] = "Unable to find cnee: ".$p->cnee->name;
				}else{
					$p->mdata['AltCnee'] = $nc->id;
					$p->nolog = true;
					$p->save();
					echo $p->cnee->name.' => '.$nc->name."\n";
					$c++;
					$ids[] = $nc->cnid_id;
				}
			}else{
				$ids[] = $p->cnee->cnid_id;
			}
		}
		echo implode("\n", $err);
		echo $c." cnee altered\n";
	}

	public function addT1rate(){
		// status 1 : active
		// type : 60 -> export agent
		// type : 65 -> export subagent
		$rs = Org::model()->findAll('type = 60 or type = 65 and status = 1');
		foreach ( $rs as $r ) {
			$r0 = SellRate::model()->find('org_id = :aid AND code = :cd AND vto IS NULL', [':aid' => $r->id,':cd' => 'R0']);

			$t1 = SellRate::model()->find('org_id = :aid AND code = :cd AND vto IS NULL', [':aid' => $r->id,':cd' => 'T1']);

			if(empty($t1) && !empty($r0)){ //T1
				$sr = new SellRate;
				$sr->type = 20;
				$sr->currency = 1;
				$sr->vfrom = $r0->vfrom;
				$sr->org_id = $r->id;
				$sr->code = 'T1';
				$sr->perkg = $r0->perkg;
				$sr->item = 10;
				$sr->save();

				echo "Done for org - " . $r->id . "\n";
			}
		}
	}

	public function fixTranshipTime(){
		$rs = Tranship::model()->findAll('status IN (11,19) AND org_id IN (101,102,103) AND `time` > DATE_SUB(NOW(), INTERVAL 20 DAY) AND `time` < DATE_SUB(NOW(), INTERVAL 2 HOUR)');

		foreach($rs as $r){
			$lt = $r->getLastTrack();
			if($lt) $r->time = $lt->dt;
			$r->save();
		}
		echo "Done\n";
	}

	public function testRest(){
		//['GET', 'POST', 'PUT', 'DELETE'];
		$method = 'PUT';
		$api_id = 'API_USER';
		$api_key = 'API_KEY';
		$data = json_encode(['order_id' => '2001234']);
		$c = new curl('http://localhost/test.php');
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->setopt(CURLOPT_TIMEOUT, 600);
		$c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
		$hdr = ['Content-Type: application/json', 'Content-Length: ' . strlen($data)];
		$c->setopt(CURLOPT_HTTPHEADER, $hdr);
		$c->setopt(CURLOPT_USERPWD, $api_id.':'.md5($data.'|'.$api_key));
		$c->setopt(CURLOPT_POSTFIELDS, $data);
		$c->exec();
		echo $c->rcode, "\n", $c->result;
	}

	public function tryGoodsMap(){
		$rs = ExParcel::model()->findAll('status IN (12,15,18,20) AND bwf & 8 > 0');
		$c = 0;
		$c++;
		foreach($rs as $r){
			$r->nolog = true;
			$r->save();
			if(($r->bwf & 8) == 0) $c++;
		}
		echo $c." updated\n";
	}

	public function fix0wt(){
		$rs = ExParcel::model()->findAll('status IN (18,20) AND weight = 0');
		foreach($rs as $r){
			$cw = $r->chargeWeight();
			if($cw > 1){
				$r->weight = $cw;
				$r->save();
			}else{
				if(preg_match('/UGG|鞋/', implode($r->eitems['g']))){
					$tq = array_sum($r->eitems['q']);
					$r->weight = (empty($tq)? 1: $tq) * 1.5;
					$r->save();
				}
			}

		}
		echo "Done\n";
	}

	public function chargewt(){
		$r = ExParcel::model()->find('hbn = :r', [':r' => $this->prompt('HBN: ')]);
		if (empty($r) ) echo 'parcel not found'. "\n";
		echo $r->chargeWeight()."\n";
	}

	public function testSfAPI(){
		//$a = new ShunfengAPI('PCAE', 'd8tQy22K5OhR', true, true);
		$a = new ShunfengAPI('ADD', 'eP3V5cOyE4Jt', '5924619087', true, true);
		//$a->addOrder(ExParcel::model()->find('hbn = :r', [':r' => $this->prompt('HBN: ')]), '厦门市湖里区象屿保税区长虹路33号 跨境电商产业园', $this->prompt('Express Type: ', 38));
		$r = $a->track('444829723727');
		var_dump($a->result);
		var_dump($a->err);
		echo $a->result->saveXML();
	}

	public function fixMI(){
		$rs = ExParcel::model()->findAll('status >= 18 AND bwf & 32 > 0');
		foreach($rs as $r){
			$d = CnID::model()->find('status = 20 AND bwf = 0 AND name = :n ORDER BY RAND()', [':n' => $r->cnee->name]);
			$r->cnee->cnid_id = $d->id;
			$r->cnee->save();
			$r->bwf = $r->bwf & (~ 32);
			$r->nolog = true;
			$r->save();
		}
		echo "Done\n";
	}

	public function fixCA(){
		$rs = ExParcel::model()->findAll('status IN (12, 15, 18, 20, 25) AND bwf & 4 > 0');
		foreach($rs as $r){
			if(empty($r->cnee->address)) continue;
			echo $r->hbn, "\n";
			if((empty($r->cnee->state) || empty($r->cnee->city)) && !empty($r->cnee->address)){
				$r->cnee->setCnAddr($r->cnee->address);
			}elseif(empty($r->cnee->postcode)){
				$r->cnee->getZip(true);
			}
			if(!empty($r->cnee->postcode)) $r->cnee->save();
			$r->nolog = true;
			$r->save();
		}
		echo "Done\n";

	}

	public function updateCSPrice(){
		$rs = ExProdb::model()->findAll('type = 10');
		foreach($rs as $r){
			$r->mdata['price_CNCSX'] = preg_match('/1段|2段|一段|二段/', $r->name_zh)? 88 : 80;
			$r->save();
		}
		$rs = ExProdb::model()->findAll('type = 20');
		foreach($rs as $r){
			if(preg_match('/Caprilac|羊奶|Maxigenes|美可卓|蓝胖/i', $r->name_zh)){
				$r->mdata['price_CNCSX'] = 50;
			}elseif(preg_match('/sustagen|孕妇|Pediasure|小安素|糖尿病|Glucerna/i', $r->name_zh)){
				$r->mdata['price_CNCSX'] = 60;
			}else{
				$r->mdata['price_CNCSX'] = 40;
			}
			$r->save();
		}
		$rs = ExProdb::model()->findAll('type = 90');
		foreach($rs as $r){
			$r->mdata['price_CNCSX'] = 100;
			$r->save();
		}

		echo "Done\n";
	}

	public function reissueEparcelInvoice(){
		//invoice
		$rates = [
			'N0' => [4.73, 4.73, 0],
			'N1' => [5.90, 5.90, 0],
			'GF' => [6.18, 6.48, 0.19],
			'WG' => [6.18, 6.48, 0.19],
			'NC' => [6.18, 7.32, 0.24],
			'CB' => [6.18, 7.32, 0.24],
			'N3' => [6.18, 8.07, 0.51],
			'N4' => [6.18, 8.07, 0.51],
			'N2' => [6.18, 8.07, 0.51],
			'V0' => [5.90, 6.39, 0.34],
			'V1' => [5.90, 6.39, 0.34],
			'GL' => [6.18, 8.00, 0.49],
			'BR' => [6.18, 9.06, 0.66],
			'V3' => [6.18, 7.14, 0.47],
			'V2' => [6.18, 9.06, 0.66],
			'Q0' => [5.90, 6.39, 0.34],
			'Q1' => [6.18, 6.39, 0.34],
			'IP' => [6.18, 8.00, 0.62],
			'GC' => [6.18, 6.70, 0.59],
			'Q5' => [6.18, 7.14, 0.47],
			'SC' => [6.18, 8.00, 0.62],
			'Q2' => [6.18, 9.06, 0.84],
			'Q3' => [6.18, 9.06, 1.30],
			'Q4' => [6.18, 9.06, 1.49],
			'S0' => [5.90, 6.39, 0.45],
			'S1' => [5.90, 6.39, 0.45],
			'S2' => [6.18, 9.06, 1.15],
			'W0' => [5.90, 7.63, 1.05],
			'W1' => [5.90, 7.63, 1.05],
			'W2' => [6.18, 9.06, 2.45],
			'W3' => [6.18, 9.06, 2.52],
			'T0' => [6.18, 8.00, 0.83],
			'T1' => [6.18, 8.00, 0.83],
			'NT1' => [6.18, 9.06, 2.50],
			'NT2' => [6.18, 9.06, 2.50],
			'NF' => [6.18, 8.29, 1.74],
			'W4' => [6.18, 9.06, 2.30],
			'AAT' => [6.18, 8.29, 1.02],
		];

		$id = $this->prompt('Invoice ID: ');

		$inv = Invoice::model()->findByPk($id);

		$consol = ImcoConsol::model()->findByPk($inv->consol_id);
		$owner = Org::model()->findByPk(1002);

		$items = [];
		$tot = 0;
		foreach($consol->shipments as $i=>$p){
			$pc = trim($p->cnee->postcode);
			$zm = ZoneMap::model()->find('org_id = 100 AND pc_lo <= :p AND pc_hi >= :p', [':p' => $pc]);
			if(!empty($rates[$zm->z2])){
				$z = $zm->z2;
			}elseif(!empty($rates[$zm->z1])){
				$z = $zm->z1;
			}else{
				echo $zm->z1.'/'.$zm->z2." has no rate<br />";
				continue;
			}
			$amt = $p->weight > 0.5? $rates[$z][1] + $rates[$z][2] * $p->weight : $rates[$z][0];
			$items[] = [$p->ref, $p->getDesc().'    '.$z, $p->pkg, $p->weight, $p->cbm, $amt];
			$tot += $amt;
		}
		
		$il = InvLine::model()->find('inv_id = :id AND ccode = :cc', [':id' => $id, ':cc' => 'EPA']);
		$il->mdata['items'] = $items;
		$il->amount = round($tot * 100) / 100;
		$il->qty = 1;
		$il->save();
		$inv->getTotal();

		$inv->mdata['name'] = $owner->name;
		$inv->mdata['address'] = $owner->getAddress();
		$inv->mdata['payterm'] = empty($owner->extra['payterm'])? 'COD' : $owner->extra['payterm'].' days';

		// need re-sync with xero
		$inv->sync_xero = 0;
		$inv->save();
		echo "Invoice Updated\n";
	}

	public function eparcelInvoice(){
		//invoice
		$rates = [
			'N0' => [4.73, 4.73, 0],
			'N1' => [5.90, 5.90, 0],
			'GF' => [6.18, 6.48, 0.19],
			'WG' => [6.18, 6.48, 0.19],
			'NC' => [6.18, 7.32, 0.24],
			'CB' => [6.18, 7.32, 0.24],
			'N3' => [6.18, 8.07, 0.51],
			'N4' => [6.18, 8.07, 0.51],
			'N2' => [6.18, 8.07, 0.51],
			'V0' => [5.90, 6.39, 0.34],
			'V1' => [5.90, 6.39, 0.34],
			'GL' => [6.18, 8.00, 0.49],
			'BR' => [6.18, 9.06, 0.66],
			'V3' => [6.18, 7.14, 0.47],
			'V2' => [6.18, 9.06, 0.66],
			'Q0' => [5.90, 6.39, 0.34],
			'Q1' => [6.18, 6.39, 0.34],
			'IP' => [6.18, 8.00, 0.62],
			'GC' => [6.18, 6.70, 0.59],
			'Q5' => [6.18, 7.14, 0.47],
			'SC' => [6.18, 8.00, 0.62],
			'Q2' => [6.18, 9.06, 0.84],
			'Q3' => [6.18, 9.06, 1.30],
			'Q4' => [6.18, 9.06, 1.49],
			'S0' => [5.90, 6.39, 0.45],
			'S1' => [5.90, 6.39, 0.45],
			'S2' => [6.18, 9.06, 1.15],
			'W0' => [5.90, 7.63, 1.05],
			'W1' => [5.90, 7.63, 1.05],
			'W2' => [6.18, 9.06, 2.45],
			'W3' => [6.18, 9.06, 2.52],
			'T0' => [6.18, 8.00, 0.83],
			'T1' => [6.18, 8.00, 0.83],
			'NT1' => [6.18, 9.06, 2.50],
			'NT2' => [6.18, 9.06, 2.50],
			'NF' => [6.18, 8.29, 1.74],
			'W4' => [6.18, 9.06, 2.30],
			'AAT' => [6.18, 8.29, 1.02],
		];

		$consol = ImcoConsol::model()->findByPk($this->prompt('Consol ID: '));
		$owner = Org::model()->findByPk(1002);
		$inv = new Invoice;
		$inv->type = 10;
		$inv->dpmt = Invoice::DPMT_IMPORT;
		$inv->to_id = 1002; // for EPS client
		$inv->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY; // default set Sydney as warehouse
		$inv->ref = 'ck1-ep'.date('Ymd', strtotime($consol->created));
		$inv->currency = 1;
		$inv->status = 2;
		$inv->date = date('Y-m-d', strtotime($consol->created));
		$inv->due = $inv->date;
		$inv->save();
		$items = [];
		$tot = 0;
		foreach($consol->shipments as $i=>$p){
			$pc = trim($p->cnee->postcode);
			$zm = ZoneMap::model()->find('org_id = 100 AND pc_lo <= :p AND pc_hi >= :p', [':p' => $pc]);
			if(!empty($rates[$zm->z2])){
				$z = $zm->z2;
			}elseif(!empty($rates[$zm->z1])){
				$z = $zm->z1;
			}else{
				echo $zm->z1.'/'.$zm->z2." has no rate<br />";
				continue;
			}
			$amt = $p->weight > 0.5? $rates[$z][1] + $rates[$z][2] * $p->weight : $rates[$z][0];
			$items[] = [$p->ref, $p->getDesc().'    '.$z, $p->pkg, $p->weight, $p->cbm, $amt];
			$tot += $amt;
		}
		$inv = Invoice::model()->findByPk($inv->id);
		$il = new InvLine;
		$il->inv_id = $inv->id;
		$il->ccode = 'EPA';
		$il->mdata['items'] = $items;
		$il->det = $consol->no;
		$il->fid = $consol->id;
		$il->model = 'ImcoConsol'; // invoice connected with console directly
		$il->amount = round($tot * 100) / 100;
		$il->qty = 1;
		$il->save();
		$inv->getTotal();
		$inv->consol_id = $consol->id;

		$inv->mdata['name'] = $owner->name;
		$inv->mdata['address'] = $owner->getAddress();
		$inv->mdata['payterm'] = empty($owner->extra['payterm'])? 'COD' : $owner->extra['payterm'].' days';

		// need re-sync with xero
		$inv->sync_xero = 0;

		$inv->save();
		echo 'Invoice '.$inv->no." issued<br />";
	}

	public function updatePBAInvoice(){
		$rs = Invoice::model()->findAll('to_id = :t AND type = 40 AND date > :d', [':t' => 1002, ':d' => '2016-12-01']);
		foreach($rs as $r){
			foreach($r->lines as $l){
				if($l->det == 'BPA Receipted Delivery' && $l->amount == 1.6){
					$l->amount = 1.8;
					$l->save();
					$r->getTotal();

					// need re-sync with xero
					$r->sync_xero = 0;

					$r->save();
					break;
				}
			}
		}
		echo "Done\n";
	}

	public function updateGoods(){
		$rs = ExParcel::model()->findAll('agent_id = 1021');
		if(empty($pd)) $pd = ExProdb::model()->findByPk(1421);
		foreach($rs as $r){
			$up = false;
			foreach($r->eitems['pid'] as $gi => $pid){
				if($pid != 1421){
					$r->eitems['pid'][$gi] = 1421;
					$r->eitems['g'][$gi] = $pd->name_zh;
					$r->eitems['b'][$gi] = $pd->brand;
					$r->eitems['m'][$gi] = $pd->model;
					$r->eitems['u'][$gi] = $pd->unit;
					$r->eitems['hs'][$gi] = $pd->hs;
					$up = true;
				}
			}
			if($up){
				echo "Update ".$r->hbn."\n";
				$r->nolog = true;
				$r->save();
			}
		}
		echo "Done\n";
	}

	public function closeNameID(){
		$c = ExcoConsol::model()->find('no = :n', [':n' => $this->prompt('Consol No: ')]);
		$ids = [];
		foreach($c->shipments as $r){
			if($r->cnee->cnid_id == 0) continue;
			$ids[] = $r->cnee->cnid_id;
		}
		foreach($c->shipments as $r){
			if($r->cnee->cnid_id > 0) continue;
			$n = trim($r->cnee->name);
			$id = CnID::model()->find('name = :n AND status IN (15, 18, 20) AND id NOT IN ('.implode(',', $ids).')', [':n' => $n]);
			if(empty($id)){
				$id = CnID::model()->find('name LIKE :n AND status IN (15, 18, 20) AND id NOT IN ('.implode(',', $ids).')', [':n' => substr($n,0,6).'%']);
			}
			if(empty($id)){
				$id = CnID::model()->find('name LIKE :n AND status IN (15, 18, 20) AND id NOT IN ('.implode(',', $ids).')', [':n' => substr($n,0,3).'%']);
			}
			if(!empty($id)){
				$ids[] = $id->id;
				$r->cnee->cnid_id = $id->id;
				$r->cnee->cnid = null;
				echo 'Matched '.$r->cnee->name.' => '.$id->name."\n";
				$r->cnee->update('cnid_id');
			}else{
				echo 'Cannot find ID for '.$r->hbn."\n";
			}
		}
		echo "Done\n";
	}

	public function jmUnused(){
		$sinc = include(Yii::app()->basePath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'jm_connotes.php');
		foreach($sinc as $k=>$d){
			$b = $d[1] - $d[2];
			$rs = ExParcel::model()->findAll(['condition' => 'ref > :b AND ref < :e', 
				'params' => [':b' => $b, ':e' => $d[0]],
				'order' => 'ref',
				]);
			$p = $b;
			foreach($rs as $r){
				if($r->ref != $p+1){
					while($p < $r->ref - 1){
						echo ++$p."\n";
					}
				}
				$p = $r->ref;
			}
		}
	}

	public function auParcelSum(){
		$xls = new oExcel;
		$xls->setColWidth([15,10,10,10]);
		$i = 1;
		$xls->addRow($i++, ['Date','Weight','Postcode']);
		$xls->setTitle('eParcel');

		$rs = ImParcel::model()->findAll('created >= :f AND created < DATE_ADD(:t, INTERVAL 1 DAY) AND ref LIKE :amq AND weight > 0', [':f' => '2016-09-01', ':t' => '2016-11-30', ':amq' => 'AMQ%']);
		foreach($rs as $r){
		break;
			$xls->addRow($i++, [$r->created, $r->weight, $r->postcode]);
		}
		echo 'eParcel: '.sizeof($rs)."\n";

		$xls->createSheet('BPA');
		$xls->goSheet(1);
		$i = 1;
		$xls->addRow($i++, ['Date','Weight','Postcode']);

		$invs = Invoice::model()->findAll('to_id = 709 AND type = 40 AND date >= :f AND date < DATE_ADD(:t, INTERVAL 1 DAY)', [':f' => '2016-09-01', ':t' => '2016-11-30']);
		$bpa = 0;
		$ltr = 0;
		$rzm = ZoneMap::model()->findAll('org_id = 100');
		$zms = [];
		foreach($rzm as $r){
			$zms[$r->z1][] = [$r->pc_lo, $r->pc_hi];
			if(!empty($r->z2)){
				$zms[$r->z2][] = [$r->pc_lo, $r->pc_hi];
			}
		}

		$randPostcode = function($z)use($zms){
			if(is_array($z)) $z = $z[array_rand($z)];
			if(empty($zms[$z])) return '2000';
			$rg = $zms[$z][array_rand($zms[$z])];
			return rand($rg[0], $rg[1]);
		};

		foreach($invs as $inv){
			foreach($inv->lines as $l){
				if($l->ccode == 'BPA' && !preg_match('/per KG|Receipted/', $l->det)){
					if(strpos($l->det, 'Letters')){
						$ltr += $l->qty;
					}else{
						$bpa += $l->qty;
						continue;
						if(preg_match('/Sydney Metro 0\-/',$l->det)){
							for($j = 0; $j < $l->qty; $j++){
								$wt = round(rand(100, 500) / 10)/100;
								$xls->addRow($i++, [$inv->date, $wt, $randPostcode('N0')]);
							}
						}elseif(preg_match('/Sydney Metro 0\.5/',$l->det)){
							for($j = 0; $j < $l->qty; $j++){
								$wt = round(rand(501, 5000) / 10)/100;
								$xls->addRow($i++, [$inv->date, $wt, $randPostcode('N0')]);
							}
						}elseif(preg_match('/Rest of AU 0\-/',$l->det)){
							for($j = 0; $j < $l->qty; $j++){
								$wt = round(rand(100, 500) / 10)/100;
								$xls->addRow($i++, [$inv->date, $wt, $randPostcode(['N1','N2','V1','Q1','Q2','Q3','S1','V2','W1','T1'])]);
							}
						}elseif(preg_match('/Rest of AU ([\d\.]+)\-(\d+)kg/', $l->det, $m)){
							for($j = 0; $j < $l->qty; $j++){
								$wt = round(rand($m[1]*1000, $m[2]*1000) / 10) /100;
								$xls->addRow($i++, [$inv->date, $wt, $randPostcode(['N1','N2','V1','Q1','Q2','Q3','S1','V2','W1','T1'])]);
							}
						}elseif(preg_match('/BPA (.+) 5-22kg Base/', $l->det, $m)){
							for($j = 0; $j < $l->qty; $j++){
								$wt = round(rand(5000, 10000) / 10) /100;
								$xls->addRow($i++, [$inv->date, $wt, $randPostcode($m[1])]);
							}
						}
					}
					
				}
			}
		}
		$xls->output('eparcels_'.time().'.xlsx', null, false);
		echo 'BPA: '.$bpa."\n";
		echo 'Letters: '.$ltr."\n";
	}

	public function emptyRef(){
		$c = ExcoConsol::model()->find('status = 10 AND id = :r OR no = :r', [':r' => $this->prompt('Consol ID/No: ')]);
		$pt = $this->prompt('Ref pattern to Remove: ');
		foreach($c->shipments as $p){
			if(!empty($p->ref) && preg_match($pt, $p->ref)){
				$p->ref = '';
				$p->save();
			}
		}
		echo "Done\n";
	}

	public function emptyAltCnee(){
		$c = ExcoConsol::model()->find('status = 10 AND id = :r OR no = :r', [':r' => $this->prompt('Consol ID/No: ')]);
		foreach($c->shipments as $p){
			if(!empty($p->mdata['AltCnee'])){
				unset($p->mdata['AltCnee']);
				$p->updateMeta();
			}
		}
		echo "Done\n";
	}

	public function testWwApi(){
		$awb = ['08186368144', '11214315814', '16037171610', '20310057655', '20565301821', '21709091821', '29760269090', '40688233541', '61836496806', '73135518836', '78429326570', '88032205725', '99940851322'];
		$wwapi = new WinWebAPI;
		$wwapi->auth();
		$r = $wwapi->getCarriers();
		return;
		
		//foreach($awb as $a){
		$a = '78425804166';
		$wwapi->addAwbs($a);
		echo 'AWB: '.$a."\n";
		$r = $wwapi->getTracking($a);
		var_dump($r);
			//maybe pending
			foreach($r->Queries as $q){
				foreach($q->Results->Data->Statuses as $d){
					echo $d->AirportCode."\n";
					foreach($d->Details as $s){
						echo $s->StatusDateTime,"\n",
						$s->Code,':',$s->Name,"\n";
						if(!empty($s->FlightNumber)) echo $s->FlightNumber,"\n";
					}
					echo "\n";
				}
				
				foreach($q->Results->Data->RoutingDetails as $d){
					//print_r($d);
					echo $d->Transport->TransportNumber,"\n",
					$d->Origin->Code, ':', $d->Origin->Name,"\n",
					$d->Origin->DepartureTime,"\n",
					$d->Destination->Code, ':', $d->Destination->Name,"\n",
					$d->Destination->ArrivalTime,"\n\n";
				}
			}
		//}
	}

	public function checkEparcelCost(){
		$ms = ['AP0000125276', 'AP0000124209', 'AP0000122815', 'AP0000121723', 'AP0000121722', 'AP0000121707', 'AP0000120592', 'AP0000120469', 'AP0000119725', 'AP0000118302'];

		$rates = [
			'N0' => [4.20, 4.20, 0.00],
			'N1' => [5.32, 5.59, 0.00],
			'GF' => [5.32, 5.59, 0.19],
			'WG' => [5.32, 5.59, 0.19],
			'NC' => [5.32, 6.35, 0.24],
			'CB' => [5.32, 6.35, 0.24],
			'N3' => [5.32, 7.04, 0.51],
			'N4' => [5.32, 7.04, 0.51],
			'N2' => [5.32, 7.04, 0.51],
			'V0' => [5.32, 5.79, 0.34],
			'V1' => [5.32, 5.79, 0.34],
			'GL' => [5.32, 6.97, 0.49],
			'BR' => [5.32, 7.94, 0.66],
			'V3' => [5.32, 6.19, 0.47],
			'V2' => [5.32, 7.94, 0.66],
			'Q0' => [5.32, 5.79, 0.34],
			'Q1' => [5.32, 5.79, 0.34],
			'IP' => [5.32, 6.97, 0.62],
			'GC' => [5.32, 5.79, 0.59],
			'Q5' => [5.32, 6.19, 0.47],
			'SC' => [5.32, 6.97, 0.62],
			'Q2' => [5.32, 7.94, 0.84],
			'Q3' => [5.32, 7.94, 1.30],
			'Q4' => [5.32, 7.94, 1.49],
			'S0' => [5.32, 5.79, 0.45],
			'S1' => [5.32, 5.79, 0.45],
			'S2' => [5.32, 7.94, 1.15],
			'W0' => [5.32, 6.97, 1.05],
			'W1' => [5.32, 6.97, 1.05],
			'W2' => [5.32, 7.94, 2.45],
			'W3' => [5.32, 7.94, 2.52],
			'T0' => [5.32, 6.97, 0.83],
			'T1' => [5.32, 6.97, 0.83],
			'NT1' =>[5.32, 7.94, 2.50],
			'NT2' =>[5.32, 7.94, 2.50],
			'NF' => [5.32, 7.24, 1.74],
			'W4' => [5.32, 7.94, 2.30],
			'AAT' =>[5.32, 7.24, 1.02],
		];

		$xls = new oExcel;
		$xls->setColWidth([15,15,10,10,10,10]);
		$i = 1;
		$xls->addRow($i++, ['Manif. ID', 'HBN', 'Dest', 'Zone', 'Weight', 'Cost']);
		foreach($ms as $m){
			$ts = Tranship::model()->findAll('org_id = 101 AND meta LIKE :m', [':m' => '%'.$m.'%']);
			foreach($ts as $t){
				$p = ImParcel::model()->findByPk($t->pid);
				$pc = trim($p->cnee->postcode);
				$zm = ZoneMap::model()->find('org_id = 100 AND pc_lo <= :p AND pc_hi >= :p', [':p' => $pc]);
				if(!empty($rates[$zm->z2])){
					$z = $zm->z2;
				}elseif(!empty($rates[$zm->z1])){
					$z = $zm->z1;
				}else{
					echo $p->hbn.' - '.$pc.': '.$zm->z1.'/'.$zm->z2." has no rate\n";
					continue;
				}
				$amt = $p->weight > 0.5? $rates[$z][1] + $rates[$z][2] * $p->weight : $rates[$z][0];
				$xls->addRow($i++, [$m, $p->ref, $pc, $z, $p->weight, $amt]);
			}
		}
		$xls->output('eparcel_costs.xlsx', 'Excel2007', false);
		echo "Done\n";
	}

	public function ydwtck(){
		$xls = new oExcel;
		$xls->load('YD_weight_check.xlsx');
		$sheetsArray = $xls->xls->getAllSheets();
		foreach ($sheetsArray as $n=>$sheet){
			$xls->goSheet($n);
			$data = $xls->getAll();
			foreach($data as $i=>$r){
				if($i == 1 || empty($r[2])) continue;
				$p = ExParcel::model()->find('ref = :r', [':r' => $r[2]]);
				if(empty($p)) continue;
				$swt = $p->shipWeight(true);
				$xls->setCell('G'.$i, $swt);
				if(ceil($r[6]) > ceil($swt)){
					$xls->setCell('H'.$i, $r[6] - $swt);
				}
			}
		}
		$xls->output('YD_wtckd.xlsx', 'Excel2007', false);
		echo "Done\n";
	}

	public function testTCPdf(){
		Yii::import('application.libs.tcpdf.tcpdf', true);
		Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
		Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
		$aid = 'AMQ122901601000930209';

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, 'pt', [288, 432], true, 'UTF-8', false);
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->SetFont('helvetica', '', 11);

		// add a page
		$pdf->AddPage();
		$style = array(
			'align' => 'L',
			'stretch' => true,
			'fitwidth' => true,
			'cellfitalign' => '',
			'border' => false,
			'hpadding' => 'auto',
			'vpadding' => 'auto',
			'fgcolor' => array(0,0,0),
			'bgcolor' => false,
			'text' => false,
		);
		$pdf->setXY(87, 194);
		$pdf->StartTransform();
		$pdf->Rotate(-90);
		$pdf->write1DBarcode(chr(241).'019931265099999891'.'AMQ'.'022324401000935002', 'C128', 0, 0, 900, 58, 0.4, $style, 'N');
		$pdf->StopTransform();
		$style = array(
			'border' => 0,
			'stretch' => false,
			'vpadding' => 'auto',
			'hpadding' => 'auto',
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255)
			'module_width' => 2, // width of a single module in points
			'module_height' => 2, // height of a single module in points
		);
		$pdf->write2DBarcode(chr(232).'019931265099999891'.$aid.chr(29).'42020178008160519111959', 'DATAMATRIX', 220, 46, 0, 0, $style, 'N');
		$pdf->Output('test.pdf', 'I');
	}

	public function rfcCheck(){
		$rs = ExParcel::model()->findAll('status IN (15,18)');
		foreach($rs as $r){
			$c = $r->rfcProblem(4);
			if($r->status > 100) echo $r->hbn.' rfc '.$c." times\n";
			$r->save();
		}
		echo "Done\n";	
	}

	public function ebayAPI(){
		$c = new curl('http://svcs.ebay.com/services/search/FindingService/v1?OPERATION-NAME=findItemsAdvanced&SERVICE-VERSION=1.0.0&SECURITY-APPNAME=FrankLiu-WMS-PRD-28ad35b38-aba7f9ed&RESPONSE-DATA-FORMAT=JSON&REST-PAYLOAD&globalId=EBAY-AU&keywords=9311770592505');
		$c->exec();
		$r = json_decode($c->result);
		print_r($r);
	}

	public function upcLookup(){
		$bc = '9327693000317';
		$c = new curl('https://api.upcitemdb.com/prod/trial/lookup?upc='.$bc);
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_TIMEOUT, 5);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_SSL_VERIFYHOST, false);
		$c->exec();
		$r = json_decode($c->result);
		print_r($r);
	}

	public function batchLoadIDPhoto(){
		$c = 0;
		foreach(glob('sfz/*A*.jpg') as $ff){
			$n = basename($ff);
			preg_match('/^([\dXx]{18})\s*A(.+)?\.jpg$/', $n, $m);
			$no = $m[1];
			$name = empty($m[2])? '' : $m[2];
			$bf = str_replace('A', 'B', $ff);
			if(empty($no)){
				echo $ff." number not correct\n";
				continue;
			}

			$cnid = CnID::model()->find('no = :no AND status IN (10, 15, 18, 20)', array(':no' => $no));
			if(empty($cnid)){
				if(is_file($bf)){
					$cnid = new CnID;
					$cnid->status = 14;
					$cnid->no = strtoupper($no);

					$cnee = Addr::model()->find('cnid_no = :idn', array(':idn' => $no));
					if(empty($cnee)){
						if(empty($name)){
							echo $ff." no name\n";
							continue;
						}
						$cnid->name = $name;
					}else{
						$cnid->name = $cnee->name;
						$cnid->mobile = $cnee->tel;
						$cnid->city = $cnee->city;
					}

					if(!$cnid->save()) $err[] = 'Saving error, '.print_r($cnid->getErrors(), true);
				
					//save images
					foreach(['front' => $ff, 'back' => $bf] as $side => $f){
						$img = AppHelper::resizeImg($f, 600);
						$tf = tempnam($this->tmp, "idp");
						if($img) @imagejpeg($img, $tf);
						$cnid->{$side} = FileRepo::storeFile($tf, $cnid->no.'_'.($side == 'front'? 1 : 2).'.jpg', 60, $cnid->id);
						unlink($tf);
					}

					$cnid->nolog = true;
					$cnid->save();
					$cnid->refresh();

					if($cnid->front > 0 && $cnid->back > 0){
						$cnid->nolog = false;
						$cnid->joinPhoto();
						$cnid->status = 15;
						$cnid->save();
						$cnid->matchCnee();
					}
					echo $no." added\n";
					$c++;
				}else{
					echo $ff." no back photo\n";
				}
			}else{
				unlink($ff);
				if(is_file($bf)) unlink($bf);
			}
		}
		echo $c." ID added\n";
	}

	public function idno2rcvd(){
		$rs = ExParcel::model()->findAll('status = 15');
		$c = 0;
		foreach($rs as $r){
			if(empty($r->cnee) || $r->cnee->cnid_id > 0 || !empty($r->cnee->cnid_no)) continue;
			$a = Addr::model()->find('cnid_no != "" AND name = :n', [':n' => $r->cnee->name]);
			if($a){
				$r->cnee->cnid_no = $a->cnid_no;
				$r->cnee->save();
				echo $r->cnee->name." found ID no\n";
				$c++;
			}
		}
		echo $c." ID no found\n";
	}

	public function idnophoto462(){
		$rs = ExParcel::model()->findAll('agent_id = 462');

		foreach($rs as $r){
			if($r->cnee->cnid_id > 0){
				if($r->cnee->cnid->status == 14){
					$r->cnee->cnid_id = 0;
					$r->cnee->save();
					continue;
				}

				if(substr(mime_content_type($r->cnee->cnid->photo_front->getFile()), 0, 5) != 'image' || substr(mime_content_type($r->cnee->cnid->photo_back->getFile()), 0, 5) != 'image'){
					$r->cnee->cnid_id = 0;
					$r->cnee->save();
					$r->cnee->cnid->status = 14;
					$r->cnee->cnid->save();
					echo $r->cnee->cnid->no." is invalid\n";
				}
			}
		}
		echo "Done\n";
	}

	public function EPSinvDiff(){
		$rs = Invoice::model()->findAll("to_id = 1002 AND type = 10 AND date >= '2016-12-01'");

		$rates = [
			'N0' => [4.73, 4.73, 0],
			'N1' => [5.90, 5.90, 0],
			'GF' => [6.18, 6.48, 0.19],
			'WG' => [6.18, 6.48, 0.19],
			'NC' => [6.18, 7.32, 0.24],
			'CB' => [6.18, 7.32, 0.24],
			'N3' => [6.18, 8.07, 0.51],
			'N4' => [6.18, 8.07, 0.51],
			'N2' => [6.18, 8.07, 0.51],
			'V0' => [5.90, 6.39, 0.34],
			'V1' => [5.90, 6.39, 0.34],
			'GL' => [6.18, 8.00, 0.49],
			'BR' => [6.18, 9.06, 0.66],
			'V3' => [6.18, 7.14, 0.47],
			'V2' => [6.18, 9.06, 0.66],
			'Q0' => [5.90, 6.39, 0.34],
			'Q1' => [6.18, 6.39, 0.34],
			'IP' => [6.18, 8.00, 0.62],
			'GC' => [6.18, 6.70, 0.59],
			'Q5' => [6.18, 7.14, 0.47],
			'SC' => [6.18, 8.00, 0.62],
			'Q2' => [6.18, 9.06, 0.84],
			'Q3' => [6.18, 9.06, 1.30],
			'Q4' => [6.18, 9.06, 1.49],
			'S0' => [5.90, 6.39, 0.45],
			'S1' => [5.90, 6.39, 0.45],
			'S2' => [6.18, 9.06, 1.15],
			'W0' => [5.90, 7.63, 1.05],
			'W1' => [5.90, 7.63, 1.05],
			'W2' => [6.18, 9.06, 2.45],
			'W3' => [6.18, 9.06, 2.52],
			'T0' => [6.18, 8.00, 0.83],
			'T1' => [6.18, 8.00, 0.83],
			'NT1' => [6.18, 9.06, 2.50],
			'NT2' => [6.18, 9.06, 2.50],
			'NF' => [6.18, 8.29, 1.74],
			'W4' => [6.18, 9.06, 2.30],
			'AAT' => [6.18, 8.29, 1.02],
		];
		$csv = [];
		$csv[] = 'Inv #,Date,Connote,Zone,Charge Wt,Dead Wt,Invoiced,Charge DWT';
		foreach($rs as $r){
			foreach($r->lines as $l){
				foreach($l->mdata['items'] as $p){
					$s = ImParcel::model()->find('ref = :r', [':r' => $p[0]]);
					preg_match('/\s+(.+)$/', $p[1], $m);
					$z = $m[1];
					$wt = $s->weight;
					$nc = $wt > 0.5? $rates[$z][1] + $rates[$z][2] * $wt : $rates[$z][0];
					$csv[] = $r->no.','.$r->date.','.$p[0].','.$z.','.$p[3].','.$s->weight.','.$p[5].','.$nc;
				}
			}
		}
		echo implode("\n", $csv);
	}

	public function read4pxid(){
		foreach(glob('4px/*.xls') as $f){
			$data = $this->_loadXlsData($f);
			unset($data[1]);
			if(!isset($data[2][13])) continue;
			foreach($data as $r){
				if(empty($r[8]) || !preg_match('/E|DAU\d{7,10}/', $r[13])) continue;
				echo $r[13],"\t",$r[8],"\n";
			}
		}
	}

	public function wmsTaskRef(){
		$rs = WmsTask::model()->findAll();
		foreach($rs as $r){
			if(!empty($r->mdata['refno'])){
				$r->ref = $r->mdata['refno'];
				unset($r->mdata['refno']);
				$r->save();
			}
		}
		echo "Done\n";
	}

	public function checkIDNo(){
		$rs = $this->db->createCommand('SELECT no,mobile,name FROM cn_id t WHERE status > 14 AND status < 98 AND bwf = 0')->queryAll();
		foreach($rs as $r){
			if(!CnID::validCnIDNo($r['no'])) echo $r['no']."\t".$r['name']."\t".$r['mobile']."\n";
		}
		echo "Done\n";
	}

	public function mlgReclear(){
		$data = $this->_loadXlsData($this->args[1]);
		$cid = $this->args[2];
		unset($data[1]);
		$ss = [];
		foreach($data as $r){
			if(empty($r[2])) continue;
			if(!isset($ss[$r[2]])) $ss[$r[2]] = [];
			$b = preg_replace('/\s+/', ' ', $r[4]);
			$m = '';
			if(preg_match('/^([^\d]+)(\d+.+)$/', $b, $mr)){
				$b = trim($mr[1]);
				$m = trim($mr[2]);
			}
			$ss[$r[2]][] = ['type' => 'O', 'g' => $r[3], 'b' => $b, 'm' => $m, 'q' => $r[8], 'hs' => $r[5], 'u' => $r[9], 'w' => $r[7], 'v' => (empty($r[11]) || empty($r[8]))? 0 : ($r[11] / $r[8])];
		}
		$c =  0;
		foreach($ss as $ref => $s){
			$p = ExParcel::model()->find('consol_id = :cid AND ref = :ref', [':ref' => $ref, ':cid' => $cid]);
			if(empty($p)) continue;
			if(empty($p->mdata['items_o'])) $p->mdata['items_o'] = $p->eitems;
			$t = [];
			foreach($s as $it){
				foreach($it as $k=>$v){
					$t[$k][] = $v;
				}
			}
			$p->eitems = $t;
			//$p->save();
			echo $ref." updated\n";
			$c++;
		}
		echo $c." shipments updated\n";
	}

	public function loadJointIDphoto(){
		$c = 0;
		foreach(glob('sfz_j/*.jpg') as $ff){
			$n = basename($ff);
			$no = substr($n, 0, -4);

			if(empty($no)){
				echo $ff." number not correct\n";
				continue;
			}

			$cnid = CnID::model()->find('no = :no AND status IN (10, 15, 18, 20)', array(':no' => $no));
			if(empty($cnid)){
				$cnee = Addr::model()->find('cnid_no = :idn', array(':idn' => $no));
				if(empty($cnee)){
					echo $no." not found\n";
					continue;
				}

				$cnid = new CnID;
				$cnid->status = 14;
				$cnid->name = $cnee->name;
				$cnid->no = $no;
				$cnid->mobile = $cnee->tel;
				$cnid->city = $cnee->city;

				if(!$cnid->save()){
					echo 'Saving error, '.print_r($cnid->getErrors(), true);
					continue;
				}
			
				//save images
				$pf = $this->tmp.$no.'_1.jpg';
				AppHelper::exec('magick '.$ff.' -crop 515x365+0+0 '.$pf);
				$pb = $this->tmp.$no.'_2.jpg';
				AppHelper::exec('magick '.$ff.' -crop 515x365+0+395 '.$pb);

				foreach(['front' => $pf, 'back' => $pb] as $side => $f){
					$cnid->{$side} = FileRepo::storeFile($f, $cnid->no.'_'.($side == 'front'? 1 : 2).'.jpg', 60, $cnid->id);
					unlink($f);
				}

				$cnid->nolog = true;
				$cnid->save();
				$cnid->refresh();

				if($cnid->front > 0 && $cnid->back > 0){
					$cnid->nolog = false;
					$cnid->joinPhoto();
					$cnid->status = 15;
					$cnid->save();
					$cnid->matchCnee();
				}
				echo $no." added\n";
				$c++;
			}
		}
		echo $c." ID added\n";
	}

	public function resetProblem(){
		$hbns = $this->prompt('HBNs separated by ,: ');
		$hbns = preg_split('/[,\s]+/', $hbns);
		echo 'Total '.sizeof($hbns)." shipments\n";
		$c = $this->prompt('Continue? ', 'N/y');
		if(strtoupper($c) != 'Y') return;
		foreach($hbns as $h){
			$p = ExParcel::model()->find('hbn = :h AND status = 101', [':h' => $h]);
			if(empty($p)){
				echo $h." not found\n";
				continue;
			}
			$p->ref = '';
			$p->consol_id = 0;
			$p->status = 15;
			$p->save();
		}
		echo "Done\n";
	}

	public function wmstaskitems(){
		$rs = WmsTask::model()->findAll();
		foreach($rs as $r){
			if(empty($r->mdata['items'])) continue;
			$r->new_items = $r->mdata['items'];
			unset($r->mdata['items']);
			$r->save();
		}
		echo "Done\n";
	}

	public function stockUpdate(){
		$rs = WmsStock::model()->findAll('id = :id', [':id' => $this->prompt('Stock ID:')]);
		foreach($rs as $r){
			$r->updateStock();
		}
		echo "Done\n";
	}

	public function wmsPltOut(){
		$rs = WmsTask::model()->findAll('is_request = 0 AND type IN (3010)');
		foreach($rs as $r){
			if(empty($r->items)) continue;
			$pout = false;
			foreach($r->items as $itm){
				if(empty($itm->mdata['pli'])) continue;
				$l = WmsLocation::model()->findByPk($itm->mdata['pli']);
				if($l->pid == 2){
					$pout = true;
					break;
				}
			}
			if($pout){
				$ot = new WmsTask;
				$ot->job_id = $r->job_id;
				$ot->type = 2110;
				$ot->ref = $r->ref;
				$ot->link_id = $r->link_id;
				$ot->is_request = 0;
				$ot->status = 99;
				$ot->new_items = $r->getItemsArray();
				$ot->save();
			}
			echo 'Finish task: '.$r->getNo()."\n";
		}
		echo "Done\n";
	}

	public function wmsUnitOut(){
		$rs = WmsTask::model()->findAll('is_request = 0 AND type IN (3030)');
		foreach($rs as $r){
			echo $r->getNo()."\n";
			if(empty($r->items)) continue;
			$r->save();

			//pack task
			$ot = WmsTask::model()->find('job_id = :jid AND type = 3210 AND ref = :ref', [':jid' => $r->job_id, ':ref' => $r->ref]);
			if(empty($ot)){
				$ot = new WmsTask;
				$ot->type = 3210;
				$ot->job_id = $r->job_id;
			}
			$ot->ref = $r->ref;
			$ot->link_id = $r->link_id;
			$ot->is_request = 0;
			$ot->status = 99;
			$ot->new_items = $r->getItemsArray();
			$ot->save();

			//delivery out
			$ot = WmsTask::model()->find('job_id = :jid AND type = 2120 AND ref = :ref', [':jid' => $r->job_id, ':ref' => $r->ref]);
			if(empty($ot)){
				$ot = new WmsTask;
				$ot->type = 2120;
			}
			$ot->job_id = $r->job_id;
			$ot->ref = $r->ref;
			$ot->link_id = $r->link_id;
			$ot->is_request = 0;
			$ot->status = 99;
			$ot->new_items = $r->getItemsArray();
			$ot->save();
			echo 'Finish task: '.$r->getNo()."\n";
		}
		echo "Done\n";
	}

	public function stockExpMonthOnly(){
		$rs = WmsStock::model()->findAll(['condition' => 'qty > 0 AND expiry != "" AND expiry IS NOT NULL', 'group' => 't.prod_id, MONTH(expiry)', 'order' => 't.expiry']);
		foreach($rs as $r){
			$exm = date('Y-m-01', strtotime($r->expiry));
			$ss = WmsStock::model()->findAll(['condition' => 'qty > 0 AND MONTH(expiry) = MONTH(:ex) AND t.id != :id AND prod_id = :pid', 'params' => [':id' => $r->id, ':ex' => $r->expiry, ':pid' => $r->prod_id]]);
			foreach($ss as $s){
				$sql = 'UPDATE wms_stock_ledger SET stock_id = '.$r->id.' WHERE stock_id = '.$s->id;
				$this->db->createCommand($sql)->execute();
				echo 'Merge '.$r->id.': '.$r->prod->name.' Exp: '.$s->expiry. ' to '.$exm."\n";
				$s->expiry = null;
				$s->save();
				$s->updateStock();
			}
			if($r->expiry != $exm){
				$r->expiry = $exm;
				$r->save();
				echo 'Update '.$r->prod->name.' Exp: '.$r->expiry."\n";
			}
			$r->updateStock();
		}
		echo "Done\n";
	}

	public function stockMerge(){
		$rs = WmsStock::model()->findAll(['condition' => 'org_id = :oid', 'params' => [':oid' => $this->prompt('Org ID:')], 'group' => 'prod_id, expiry, batch', 'order' => 't.id']);
		foreach($rs as $r){
			if(empty($r->expiry)){
				$ss = WmsStock::model()->findAll(['condition' => 'org_id = :oid AND t.id != :id AND prod_id = :pid AND expiry IS NULL AND batch = :bat', 'params' => [':oid' => $r->org_id, ':id' => $r->id, ':bat' => $r->batch, ':pid' => $r->prod_id]]);
			}else{
				$ss = WmsStock::model()->findAll(['condition' => 'org_id = :oid AND t.id != :id AND prod_id = :pid AND expiry = :exp AND batch = :bat', 'params' => [':oid' => $r->org_id, ':id' => $r->id, ':exp' => $r->expiry, ':bat' => $r->batch, ':pid' => $r->prod_id]]);
			}
			foreach($ss as $s){
				$sql = 'UPDATE wms_stock_ledger SET stock_id = '.$r->id.' WHERE stock_id = '.$s->id;
				$this->db->createCommand($sql)->execute();
				echo 'Merge '.$s->id.' to '.$r->id."\n";
				$s->expiry = null;
				$s->batch = null;
				$s->save();
				$s->updateStock();
			}
			$r->updateStock();
		}
		echo "Done\n";
	}

	public function wmsTask2Ledger(){
		//truncate ledger tabels
		$sql = 'TRUNCATE TABLE wms_stock_ledger; TRUNCATE TABLE wms_stock_location; UPDATE wms_stock SET qty=0;';
		//$this->db->createCommand($sql)->execute();
		$rs = WmsTask::model()->findAll(['condition' => 'id > 154 AND is_request = 0 AND status < 100', 'order' => 'id ASC']);
		foreach($rs as $r){
			echo 'Task '.$r->getNo().' ('.$r->getStatus()."): \n";
			foreach($r->items as $i=>$itm){
				if(isset($itm->mdata['plt'])){
					$itm->mdata['pl'] = $itm->mdata['plt'];
					unset($itm->mdata['plt']);
				}
				if(isset($itm->mdata['plt_id'])){
					$itm->mdata['pli'] = $itm->mdata['plt_id'];
					unset($itm->mdata['plt_id']);
				}

				if(!empty($itm->mdata['pl']) && empty($itm->mdata['pli'])){
					if(preg_match('/^Pallet (\d+)$/', $itm->mdata['pl'], $m)){
						$itm->mdata['pl'] = 'PLT1702'.sprintf('%05d', $m[1]);
					}
					$pl = WmsLocation::model()->find('code = :c', [':c' => $itm->mdata['pl']]);
					if(!empty($pl)) $itm->mdata['pli'] = $pl->id;
				}

				$itm->save();
				$err = $itm->getErrors();
				if(!empty($err) && in_array($r->type, [3210, 3020, 3030])){
					var_dump($err);
					break 2;
				}
			}
		}
		echo "Done\n";
	}

	public function cleanStock(){
		$rs = WmsStock::model()->findAll('qty = 0');
		$c = 0;
		foreach($rs as $r){
			$t = WmsStockLedger::model()->count('stock_id = :sid', [':sid' => $r->id]);
			if(empty($t)){
				$r->delete();
				$c++;
			}
		}
		echo $c." stock cleaned\n";
	}

	public function stock2rb(){
		$rs = WmsStockLocation::model()->with('loc')->findAll('loc.code = :c AND qty > 0', [':c' => $this->prompt('Pallet Code:')]);
		foreach($rs as $r){
			$t = new WmsStockLedger;
			$t->stock_id = $r->stock_id;
			$t->location_id = $r->location_id;
			$t->qty_out = $r->qty;
			$t->save();
			$t = new WmsStockLedger;
			$t->stock_id = $r->stock_id;
			$t->location_id = 7;
			$t->qty_in = $r->qty;
			$t->save();
			WmsStock::countAll($r->stock_id);
		}

		echo "Done\n";
	}

	public function rb2request(){
		$j = WmsJob::model()->findByPk(52);
		foreach($j->tasks as $t){
			if($t->is_request != 1 || !empty($t->items)) continue;
			echo $t->getNo()."\n";

			$l = WmsLocation::model()->find('code = :c', [':c' => $t->ref]);
			$rs = WmsStockLedger::model()->findAll('ts > "2017-03-17" AND qty_out > 0 AND location_id = :lid', [':lid' => $l->id]);

			foreach($rs as $r){
				$rb = WmsStockLedger::model()->find('ts >= :ts AND qty_in = :q AND stock_id = :sid AND location_id = 7', [':sid' => $r->stock_id, ':ts' => $r->ts, ':q' => $r->qty_out]);
				if(empty($rb)) continue;
				$m = new WmsTaskItem;
				$m->task_id = $t->id;
				$m->ts = $r->ts;
				$m->mdata = ['gi' => $r->stock->prod_id, 'gn' => $r->stock->prod->name, 'cq' => '', 'uq' => $r->qty_out, 'ex' => $r->stock->expiry, 'bn' => $r->stock->batch, 'nt' => ''];
				$m->save();

				$rb->ti_id = $m->id;
				$rb->save();
				$r->ti_id = $m->id;
				$r->save();
			}
		}
		echo "done\n";
	}

	public function wmsLocaitonAddWt(){
		//sorting rack
		$rs = WmsLocation::model()->findAll('wid = 106 AND type = 30 AND status = 1');
		$ls = [];
		foreach($rs as $r){
			$c = $r->code;
			if(strlen($r->code) == 10) $c = substr($r->code, 0, -1).'0'.substr($r->code, -1);
			$ls[$c] = $r;
		}

		krsort($ls);
		$i = 10000;
		foreach($ls as $r){
			$r->wt = $i++;
			$r->save();
		}

		//g pallet rack
		$rs = WmsLocation::model()->findAll(['condition' => 'wid = 106 AND type = 20 AND status = 1 AND code LIKE "%-1"', 'order' => 'code']);
		$i = 5000;
		foreach($rs as $r){
			$r->wt = $i++;
			$r->save();
		}

		//pallet rack
		$rs = WmsLocation::model()->findAll(['condition' => 'wid = 106 AND type = 20 AND status = 1 AND code NOT LIKE "%-1"', 'order' => 'code']);
		$i = 1000;
		foreach($rs as $r){
			$r->wt = $i++;
			$r->save();
		}

		echo "Done\n";
	}

	public function bulkOnlyStock(){
		$rs = WmsStockLocation::model()->with('loc.parent')->findAll(['condition' => 'qty > 0 AND parent.type = 20 AND parent.code NOT LIKE "%-1"']);

		$ss = [];
		foreach($rs as $r){
			if(in_array($r->stock_id, $ss)) continue;
			$oq = 0;
			foreach($r->stock->locs as $sl){
				if(empty($sl->loc->pid)) continue;
				if($sl->loc->parent->type == 30 || preg_match('/-1$/', $sl->loc->parent->code)){
					$oq += $sl->qty;
				}
			}

			if($oq < 20){
				$ss[] = $r->stock_id;
				echo $r->loc->parent->code."\t".$r->loc->code."\t".$r->stock->customer->name,"\t",$r->stock->stockName()."\t".$r->qty."\t".$oq."\n";
			}
		}
		echo "Done\n";
	}

	public function st2delivery(){
		$rs = WmsTask::model()->findAll('type = 3030 AND status < 100');
		foreach($rs as $r){
			if(empty($r->mdata['st_co'])) continue;
			$t = WmsTask::model()->find('link_id = :id AND type IN (2110, 2120)', [':id' => $r->id]);
			if(empty($t)){
				$t = new WmsTask;
				$t->status = 10;
				$t->link_id = $r->id;
				$t->job_id = $r->job_id;
				$t->save();
			}
			$t->type = 2120;
			$t->mdata['cnee'] = [
				'name' => empty($r->mdata['st_cp'])? '' : $r->mdata['st_cp'],
				'company' => empty($r->mdata['st_co'])? '' : $r->mdata['st_co'],
				'tel' => empty($r->mdata['st_tel'])? '' : $r->mdata['st_tel'],
				'address' => empty($r->mdata['st_addr'])? '' : $r->mdata['st_addr'],
			];
			$t->save();
		}
		echo "Done\n";
	}

	public function cwstocknoexp(){
		$oid = 1084;
		$rs = WmsStock::model()->findAll('org_id = '.$oid);

		foreach($rs as $r){
			$po = WmsProdOrg::model()->find('org_id = :oid AND prod_id = :pid', [':oid' => $oid, ':pid' => $r->prod_id]);
			if(empty($po)){
				$po = new WmsProdOrg;
				$po->prod_id = $r->prod_id;
				$po->org_id = $oid;
			}
			$po->mdata['exp_bat'] = 9;
			$po->save();
		}
		echo "Done\n";
	}

	public function stockAllOut(){
		$task = WmsTask::model()->findByPk($this->prompt('Task ID:'));
		$rs = WmsStockLocation::model()->with('stock')->together()->findAll('t.qty > 0 AND stock.org_id = :oid AND t.updated < :b', [':oid' => $task->job->org_id, ':b' => $this->prompt('Before Date: ', date('Y-m-d'))]);
		$actask = $task->actionTask;
		// $pls = [];
		foreach($rs as $sl){
			if(!in_array($sl->loc->type, [30,50,60])) continue;
			$itm = new WmsTaskItem;
			$itm->task_id = $actask->id;
			if($task->type == '3010'){
				// if(in_array($sl->location_id, $pls)) continue;
				$itm->mdata = ['si' => $sl->stock_id, 'sn' => $sl->stock->stockName(), 'pq' => 1, 'pli' => $sl->location_id, 'pl' => $sl->loc->code, 'nt' => ''];
				// $pls[] = $sl->loc->id;
			}else{
				$itm->mdata = ['si' => $sl->stock_id, 'sn' => $sl->stock->stockName(), 'uq' => $sl->qty, 'pli' => $sl->location_id, 'pl' => $sl->loc->code, 'nt' => ''];
			}
			$itm->save();
		}
		$task->refresh();
		$task->save();
		echo "Done\n";
	}

	public function testYtoWs(){
		//$y = new YtoAPI('enczmtxa8s2gkhu9', 'doilp1vtqxfhbygujak8c0z5w3nrs7m2'); //PCA
		$y = new YtoAPI('tnbrwdhz7e4kvx1g', '75gfn3uw6osjr2z4ly9xap1k0icvdqht'); // Jack
		//$y = new YtoAPI('ae7040e1df5bdabd64e9e568e4312635', 'd7013e389be1824cb7ad698dc5eeac16d7013e389be1824cb7ad698dc5eeac16', true, true);// Tom
		$p = ExParcel::model()->find('hbn = :h', [':h' => $this->prompt('HBN: ')]);
		$r = $y->createOrder($p, $this->prompt('PID: ', 'AU0007'));
		var_dump($r);
		//$r = json_decode($r->ServiceEntranceResult);
		echo "Done\n";
	}

	public function ytoTracking(){
		$yto = new YtoAPI('PCAE_PAC', '', true, false);
		$p = ExParcel::model()->find('hbn = :h', [':h' => $this->prompt('HBN: ')]);
		$ts = [];
		foreach($p->tracks as $t){
			if($t->type < 60){
				$ts[] = $t;
			}
		}
		$yto->sendTracking($ts);
		var_dump($yto->result);
	}

	public function consolChargableWt(){
		$rs = ExParcel::model()->findAll('consol_id = :cid', [':cid' => $this->prompt('Consol ID:')]);
		$wt = 0;
		foreach($rs as $r){
			$wt += $r->chargeWeight();
		}
		echo sizeof($rs).' parcels, total '.round($wt, 2)." kg\n";
	}

	public function pbxEms(){
		function emsCheckSum($a){
			$a = str_pad((string) $a, 8, "0", STR_PAD_LEFT);
			$cs = $a[0] * 8 + $a[1] * 6 + $a[2] * 4 + $a[3] * 2 + $a[4] * 3 + $a[5] * 5 + $a[6] * 9 + $a[7] * 7;
			$cs = 11 - ($cs % 11);
			if($cs == 10) $cs = 0;
			if($cs == 11) $cs = 5;
			return $a.$cs;
		}

		function newEmsNo(&$pool, $b=false){
			$n = '';
			foreach($pool as $k=>$rg){
				if($rg[1] == $rg[2]) continue;
				if(!$b || $rg[0] == $b){
					$n = $rg[0].emsCheckSum($rg[1]).$rg[3];
					$pool[$k][1]++;
					break;
				}
			}
			return $n;
		}

		/* 100 for ems testing
		for($n = 59780701; $n <= 59780800; $n++){
			echo '11'.emsCheckSum($n)."81\n";
		}
		return;
		*/

		$pd = Yii::app()->basePath.DIRECTORY_SEPARATOR.'pbx_ems'.DIRECTORY_SEPARATOR;
		$sinc = include($pd.'ems_nos.php');
		foreach(glob($pd.'*.xlsx') as $df){
			echo 'Read '.basename($df)."\n";
			$xls = new oExcel;
			$xls->load($df);

			$sheetsArray = $xls->xls->getAllSheets();
			foreach($sheetsArray as $n=>$sheet){
				$xls->goSheet($n);
				$data = $xls->getAll();
				$hdr = preg_replace('/\s+/', '', trim(implode(',', $data[1]),','));
				$hdr = str_replace(',EMS单号', '', $hdr);
				echo $hdr."\n";
				if('订单号,下单时间,商品名称,顾客姓名,性别,顾客身份证号,邮寄地址,电话号码,手机号码,商品数' == $hdr){
					$m = ['ref' => 1, 'g' => 3, 'q' => 10, 'cn' => 4, 'ct' => 9, 'ca' => 7, 'ems' => 11,];
					$ems_col = 'K';
				}elseif('订单号(15位),买家昵称,收货人（必填）,手机号（必填）,支付id,身份证（必填）,收货人地址（必填）,省(必填),市(必填),县区(必填),邮编,商品id（必填）,商品价格（必填）,商品数量（必填）,是否到付（STO_DF:表示到付）' == $hdr){
					$m = ['ref' => 1, 'g' => 12, 'q' => 14, 'cn' => 3, 'ct' => 4, 'ca' => [8,9,10,7], 'ems' => 16,];
					$ems_col = 'P';
				}else{
					echo $df," sheet ".$n." header unknown\n";
					continue;
				}

				$xls->setCell($ems_col.'1', 'EMS 单号');
				$c = 0;
				foreach($data as $i=>$r){
					if($i < 2 || empty($r[$m['ref']]) || empty($r[$m['q']]))continue;
					$p = new StdClass;
					$p->hbn = $r[$m['ref']];
					$p->swt = 0;
					$p->eitems = ['g' => [$r[$m['g']]], 'q' => [$r[$m['q']]]];
					$p->cnee = new StdClass;
					$p->cnee->name = $r[$m['cn']];
					$p->cnee->tel = $r[$m['ct']];
					if(is_array($m['ca'])){
						$fa = '';
						foreach($m['ca'] as $v){
							$fa .= $r[$v];
						}
					}else{
						$fa = $r[$m['ca']];
					}
					$p->cnee->fullAddress = $fa;
					if(empty($r[$m['ems']])){
						$p->ref = newEmsNo($sinc, 11);
						$xls->setCell($ems_col.$i, '="'.$p->ref.'"');
					}else{
						$p->ref = $r[$m['ems']];
					}
					ob_start();
					include($pd.'zpl.php');
					$z = ob_get_contents();
					ob_end_clean();
					file_put_contents(str_replace('.xlsx', '-t'.$n.'.zpl', $df), $z."\n\n", FILE_APPEND);
					$c++;
				}
				echo 'Sheet '.$n.' - '.$c." labels generated\n";
			}
			$xls->output($pd.'processed'.DIRECTORY_SEPARATOR.basename($df), 'Excel2007', false);
			unset($xls);
			unlink($df);
		}
		file_put_contents($pd.'ems_nos.php', "<?php\nreturn ".var_export($sinc, true).';');
		echo "Done\n";
	}

	public function fixPRChina(){
		$rs = ExParcel::model()->with('cnee')->findAll('t.status IN (12, 15) AND cnee.country = "" AND cnee.cnid_id = 0');
		foreach($rs as $r){
			$r->cnee->country = 'PR China';
			$r->cnee->save();
			$r->save();
		}
		echo "Done\n";
	}

	public function fixRsvStock(){
		$rs = WmsStock::model()->findAll('qty_res > 0');
		foreach($rs as $r){
			$r->updateStock();
		}
		echo "Done\n";
	}

	public function fixPltStatus(){
		$cid = $this->prompt('Consol ID: ');
		$cp = ExParcel::model()->find('consol_id = :cid AND status >= 60', [':cid' => $cid]);
		$rs = ExParcel::model()->findAll('consol_id = :cid AND status < 60', [':cid' => $cid]);
		$t2s = [38 => 60, 40 => 70, 50 => 80];
		foreach($rs as $r){
			foreach($cp->tracks as $t){
				if(in_array($t->type, [38,40,50])){
					$t->id = null;;
					$t->isNewRecord = true;
					$t->pid = $r->id;
					$t->save();
					$r->status = $t2s[$t->type];
				}
			}
			$r->save();
		}
		echo "Done";
	}

	public function top10(){
		$sql = 'SELECT COUNT(id) as c, SUM(weight) as w, agent_id FROM shipment WHERE type = 20 AND status IN (80, 90, 99) AND created >= "2015-12-01" AND created < "2017-01-20" GROUP BY agent_id ORDER BY w DESC, c DESC LIMIT 20';
		$us = $this->db->createCommand($sql)->queryAll();
		foreach($us as $u){
			$rs = ExParcel::model()->findAll('agent_id = :aid AND created >= "2015-12-01" AND created < "2017-01-20" AND status IN (80, 90, 99)', [':aid' => $u['agent_id']]);
			echo $u['agent_id'], "\t", $u['c'], "\t", $u['w'], "\t";
			$t = 0;
			foreach($rs as $r){
				$c = $r->getAgentRate();
				$t += $c[2];
			}
			echo $t."\n";
		}
	}

	public function ytoG90(){
		$rs = ExParcel::model()->findAll(['condition' => "consol_id = 4233 AND ref = ''"]);
		$y = new YtoAPI('nv6btgudyqz4c80p', 'pmlqasc21nw07eftrxybh3vzj5g4o8d9');			
		foreach($rs as $p){
			$r = $y->createOrder($p);
			echo $p->hbn."\n";
			$r = json_decode($r->ServiceEntranceResult);
			if(empty($r->success)){
				var_dump($r);
				break;
				continue;
			}
			$p->ref = $r->data->shipping_method_no;
			$p->save();
		}
		echo "Done\n";
	}

	public function ytoG90dtb(){
		$rs = ExParcel::model()->findAll(['condition' => "consol_id = 5675 AND ref != ''"]);
		$y = new YtoAPI('tnbrwdhz7e4kvx1g', '75gfn3uw6osjr2z4ly9xap1k0icvdqht');			
		foreach($rs as $p){
			if(empty($p->ref)) continue;
			$r = $y->getDistribute($p->ref);
			$r = json_decode($r->ServiceEntranceResult);
			if(!empty($r->data->distribute_code)){
				$p->mdata['dtb'] = $r->data->distribute_code;
				var_dump($p->mdata['dtb']);
				$p->save();
			}else{
				var_dump($r);
				continue;
			}
		}
		echo "Done\n";
	}

	public function ytoG90Tracking(){
		$yto = new YtoAPI('PCAE_PAC', '', true, false);
		$rs = ExParcel::model()->findAll(['condition' => 'consol_id = '.$this->prompt('Consol ID: ')]);
		$ts = [];
		foreach($rs as $p){
			foreach($p->tracks as $t){
				if(!in_array($t->type,[38,40,50]) && $t->type < 70){
					$ts[] = $t;
				}
			}
		}
		$yto->sendTracking($ts);
		var_dump($yto->result);
	}

	public function ytoGet3dm(){
		$y = new YtoAPI('nv6btgudyqz4c80p', 'pmlqasc21nw07eftrxybh3vzj5g4o8d9');
		$r = $y->getDistribute('G20100050013');
		$r = json_decode($r->ServiceEntranceResult);
		var_dump($r);
	}

	public function sfLabel(){
		$p = ExParcel::model()->find('hbn = :r', [':r' => $this->prompt('HBN: ')]);
		ob_start();
		include('label_sf.php');
		$z = ob_get_contents();
		ob_end_clean();
		file_put_contents('sf.zpl', $z."\n\n", FILE_APPEND);
	}

	public function wmsStorageInvoice(){
		$aid = $this->prompt('Org ID: ', '1083');
		$fd = $this->prompt('From Date: ', '2017-01-21');
		$ttl =  strtotime($this->prompt('To Date: ', '2017-06-16'));
		$cbm =  strtolower($this->prompt('CBM: ', 'N')) == 'y';
		$up =  $this->prompt('Rate/Week: ', 5.0);
		$freewk =  $this->prompt('Free Week: ', 0);
		$mpm =  strtolower($this->prompt('Mixed Pallet as Multiple: ', 'N')) == 'Y';
		$trun = strtolower($this->prompt('Trial Run: ', 'Y'));
		$owner = Org::model()->findByPk($aid);
		
		$fdts = strtotime($fd);
		$wd = date('N', $fdts);
		if($wd != 6){
			$fdts = strtotime($fd.' -'.($wd+1).' day');
			$fd = date('Y-m-d', $fdts);
		}

		while(strtotime($fd) < $ttl){
			$td = date('Y-m-d', strtotime($fd.' +6 day'));
			$bd = date('Y-m-d', strtotime($fd.' +9 day'));
			$inv = Invoice::model()->find('type = 60 AND to_id = :aid AND date = :bd', [':aid' => $aid, ':bd' => $bd]);
			if(empty($inv)){
				$inv= new Invoice;
				$inv->type = 60;
				$inv->dpmt = Invoice::DPMT_3PL;
				$inv->currency = 1;
				$inv->to_id = $aid;
				$inv->date = $bd;
			}
			$inv->status = 2;
			$inv->mdata['name'] = $owner->name;
			$inv->mdata['address'] = $owner->getAddress();
			$inv->mdata['payterm'] = empty($owner->extra['payterm'])? 'COD' : $owner->extra['payterm'].' days';
			$inv->mdata['paytype'] = empty($owner->extra['paytype'])? '' : $owner->extra['paytype'];
			$inv->mdata['billfrom'] = $fd;
			$inv->mdata['billto'] = $td;
			$inv->due = $inv->date;
			$inv->total = 0;

			$rs = WmsStock::model()->findAll('org_id = :oid', [':oid' => $aid]);
			$items = [];
			$stot = 0;
			$locs = $mpm? false : [];
			foreach($rs as $r){
				$q = $r->chargeUnit($td, $freewk, $cbm, $locs);
				if($q[0] == 0) continue;
				$items[] = [$r->stockName(), $q[0], $q[1], $up];
				$stot += $q[1] * $up;
			}
			if($stot == 0){
				echo 'No charge for week ending '.$td."\n";
			}else{
				if($trun != 'y') $inv->save();

				$stot = round($stot * 110)/100;

				$il = InvLine::model()->find('inv_id = :id', [':id' => $inv->id]);
				if(empty($il)){
					$il = new InvLine;
					$il->inv_id = $inv->id;
				}
				$il->amount = $stot;
				$il->gst = round($stot * 100 / 11) / 100;
				$il->mdata['items'] = $items;
				$il->ccode = WmsOrgQuote::QUOTE_PALLET_STORAGE_WEEK;
				$il->det = 'Warehouse Storage';
				$il->qty = 1;
				
				if($trun != 'y'){
					$il->fid = $r->id;
					$il->save();
				}
				$inv->dpt_id = 106;
				$inv->lines = [$il];
				$inv->total = $stot;
				$inv->gst = round($stot * 100 / 11) / 100;
				if($trun != 'y'){
					$inv->sync_xero = 0;
					$inv->save();
					echo $inv->no." created\n";
				}else{
					$ifn = $aid.'_'.$td.'storage_invoice.pdf';

					oPDF::renderPDF('invoice', array('inv'=>$inv), 2, 's1.pdf');
					oPDF::renderPDF('invoice_detail', array('inv'=>$inv), 2, 's2.pdf');
					oPDF::mergePDF(['s1.pdf', 's2.pdf'], 2, true, $ifn);
					echo $ifn." created\n";
				}
			}
			$fd = date('Y-m-d', strtotime($fd.' +7 day'));
		}

		echo "Done\n";
	}

	public function archiveShipments(){
		function arcShip($c){
			$sq = 'SELECT `id` FROM `shipment` WHERE '.$c;

			$sql = [];
			//tracking
			$sql[] = 'INSERT INTO `tracking_archive` SELECT * FROM `tracking` WHERE pid IN ('.$sq.')';
			$sql[] = 'DELETE FROM `tracking` WHERE pid IN ('.$sq.')';

			//logs
			$sql[] = 'INSERT INTO `log_archive` SELECT * FROM `log` WHERE `model` IN ("ExParcel", "ImParcel", "ExAfs", "CoParcel", "ExDirect") AND `lid` IN ('.$sq.')';
			$sql[] = 'DELETE FROM `log` WHERE `model` IN ("ExParcel", "ImParcel", "ExAfs", "CoParcel", "ExDirect") AND `lid` IN ('.$sq.')';

			//address
			$sql[] = 'INSERT INTO `addr_archive` SELECT * FROM `addr` WHERE `id` IN ('.preg_replace('/^SELECT `id`/', 'SELECT `cnor_id`', $sq).')';
			$sql[] = 'DELETE FROM `addr` WHERE `id` IN ('.preg_replace('/^SELECT `id`/', 'SELECT `cnor_id`', $sq).')';

			$sql[] = 'INSERT INTO `addr_archive` SELECT * FROM `addr` WHERE `id` IN ('.preg_replace('/^SELECT `id`/', 'SELECT `cnee_id`', $sq).')';
			$sql[] = 'DELETE FROM `addr` WHERE `id` IN ('.preg_replace('/^SELECT `id`/', 'SELECT `cnee_id`', $sq).')';

			//shipment
			$sql[] = 'INSERT INTO `shipment_archive` SELECT * FROM `shipment` WHERE '.$c;
			$sql[] = 'DELETE FROM `shipment` WHERE '.$c;

			return implode(";\n", $sql).";\n";
		}

		//cancelled shipments
		$sq = '`status` = 100 AND `created` < DATE_SUB(CURRENT_DATE(), INTERVAL 60 DAY)';

		//old consol
		$sq = 'consol_id IN (SELECT id FROM consol WHERE created < DATE_SUB(CURRENT_DATE(), INTERVAL 400 DAY))';

		echo arcShip($sq);

		$sq = 'consol_id = 0 AND created < DATE_SUB(CURRENT_DATE(), INTERVAL 400 DAY)';

		echo arcShip($sq);

		echo 'INSERT INTO `log_archive` SELECT * FROM `log` WHERE `time` < DATE_SUB(CURRENT_DATE(), INTERVAL 400 DAY);', PHP_EOL,
		'DELETE FROM `log` WHERE `time` < DATE_SUB(CURRENT_DATE(), INTERVAL 400 DAY);', PHP_EOL,
		'OPTIMIZE TABLE `tracking`, `log`, `addr`, `shipment`;', PHP_EOL;
	}

	public function updateWmsInvoiceGST(){
		$rs = Invoice::model()->findAll('type = 60');
		foreach($rs as $r){
			foreach($r->lines as $il){
				$il->gst = round($il->amount * 100 / 11) / 100;
				$il->save();
			}
			$r->save();
		}
		echo "Done\n";
	}

	public function altWmsLedgerTime(){
		$task = WmsTask::model()->findByPk($this->prompt('Task ID: '));
		if(empty($task)){
			die("Task not found\n");
		}
		echo 'Completion Time: '.$task->compl_time."\n";
		if(strtoupper($this->prompt('Continue?')) == 'Y'){
			$at = $task->actionTask;
			foreach($at->items as $itm){
				if($itm->ts != $task->compl_time){
					$itm->ts = $task->compl_time;
					$itm->save();
				}
				$sql = 'UPDATE wms_stock_ledger SET ts = "'.$itm->ts.'" WHERE ti_id = '.$itm->id;
				$this->db->createCommand($sql)->execute();
			}
		}
		echo "Done\n";
	}

	public function addPlt2task(){
		$tid = $this->prompt('Task ID: ');
		$plts = $this->prompt('Pallets (separated by ,): ');
		$plts = preg_split('/[, ]+/', $plts);
		foreach($plts as $p){
			$plt = WmsLocation::model()->find('(pid > 99 OR pid = 1) AND code = :n', [':n' => $p]);
			if(empty($plt)){
				echo $p." not found\n";
				continue;
			}
			$sl = WmsStockLocation::model()->find('location_id = :lid', [':lid' => $plt->id]);
			if(empty($sl)){
				echo 'no stock found at '.$p."\n";
				continue;
			}
			$itm = new WmsTaskItem;
			$itm->task_id = $tid;
			$itm->mdata = [
				'nt' => '',
				'pq' => 1,
				'si' => $sl->stock->id,
				'sn' => $sl->stock->stockName(),
				'pli' => $plt->id,
				'pl' => $p,
			];
			$itm->save();
		}
		echo "Done\n";
	}

	public function ccicdata(){
		$gts = ['B' => '婴儿奶粉', 'M' => '奶粉', 'O' => '保健品', 'X' => '奶粉, 保健品'];
		
		$hdr = ['快递公司代码', '中检包裹溯源码序号', '中检包裹溯源码', '包裹运单号', '包裹收取日期', '包裹收取城市', '航班号', '航班始发城市', '航班日期', '包裹产品种类', '包裹产品件数', '包裹重量', '快递公司查询链接', '空运提单号', '单批次包裹的总数量', '单批次包裹的总重量', '包裹集散仓地址'];
		$dt = date('Y-m-d');
		$dts = strtotime('+1 day');

		$rs = OriginTrace::model()->findAll('pid > 0 AND bdate IS NULL');
		$cps = [];
		$css = [];
		foreach($rs as $r){
			$p = ExParcel::model()->findByPk($r->pid);
			if(empty($p->consol_id)) continue;
			if(empty($css[$p->consol_id])){
				$cl = ExcoConsol::model()->findByPk($p->consol_id);
				$css[$p->consol_id] = [
					'flight' => $cl->flight,
					'etd' => $cl->etd,
					'awb' => $cl->awb,
					'pod' => $cl->pod,
					'tp' => $cl->totShipments(),
					'tw' => $cl->totWeight(),
				];
				$f = FileRepo::model()->find('fid = :id AND type = 80 AND status = 20 AND name LIKE :n', [':id' => $p->consol_id, ':n' => '%pdf']);
				if(!empty($f)){
					copy($f->getFile(), $cl->awb.'.pdf');
				}
			}
			if(empty($css[$p->consol_id]['awb']) || empty($css[$p->consol_id]['flight']) || empty($css[$p->consol_id]['etd']) || strtotime($css[$p->consol_id]['etd']) > $dts) continue;
			if(!in_array($css[$p->consol_id]['pod'], ['CNCSX', 'CNXMN', 'CNXM2'])) continue;
			if(!isset($cps[$p->consol_id])) $cps[$p->consol_id] = [];
			$cps[$p->consol_id][] = ['1168', $r->ref, '="'.$r->no.'"', $p->hbn, $p->pickupDate(), '悉尼', $css[$p->consol_id]['flight'], '悉尼', $css[$p->consol_id]['etd'], $gts[$p->goodsType()], array_sum($p->eitems['q']), $p->shipWeight(), 'https://www.pcaexpress.com.au/tracking/?c='.$p->hbn, $css[$p->consol_id]['awb'], $css[$p->consol_id]['tp'], $css[$p->consol_id]['tw'], '6C The Crescent, Kingsgrove, NSW 2208'];
			$r->bdate = $dt;
			$r->save();
		}
		
		foreach($cps as $cid=>$rs){
			$xls = new oExcel;
			$i = 1;
			$xls->addRow($i++, $hdr);
			foreach($rs as $r){
				$xls->addRow($i++, $r);
			}
			$xls->output('ccic_'.$dt.'_'.$r[13].'.xlsx', null, false);
		}
		echo "Done\n";
	}

	public function exAvgRev(){
		$rs = Invoice::model()->findAll('type = 20 AND status > 1 AND status < 10 AND date >= :fd AND date <= :td', [':fd' => $this->prompt('From Date: ', date('Y-m-01')), ':td' => $this->prompt('To Date: ', date('Y-m-d'))]);
		$tv = 0;
		$tw = 0;
		$rpt = [];
		foreach($rs as $r){
			if(!isset($rpt[$r->to_id])) $rpt[$r->to_id] = ['v' => 0, 'w' => 0];
			foreach($r->lines as $il){
				foreach($il->mdata['items'] as $i=>$v){
					$iv = $v[7]+(empty($v[8])? 0 : $v[8]);
					$rpt[$r->to_id]['v'] += $iv;
					$rpt[$r->to_id]['w'] += $v[2];
					$tv += $iv;
					$tw += $v[2];
				}
			}
		}

		$o = 'Agent,Total,Weight,PKG'."\n";
		foreach($rpt as $agt=>$r){
			$o .= $agt.','.$r['v'].','.$r['w'].','.round(($r['v'] / (empty($r['w']) || $r['w'] == 0)? 1 : $r['w']), 3)."\n";
		}

		$o .= 'Total: $'. $tv.',Weight: '. $tw. 'kg,PKG: $'.round($tv/$tw, 3)."\n";
		file_put_contents('exavgrev.csv', $o);
		echo "Done\n";
	}

	public function fixCourier(){
		$bd = date('Y-m-d', strtotime('-6 month'));
		$t = ExParcel::model()->count('status = 90 AND created > "'.$bd.'"');
		$ppg = 5000;
		$c = 0;
		for($i = 0; $i < ceil($t/$ppg); $i++){
			$rs = ExParcel::model()->findAll(['condition' => 'status = 90 AND created > "'.$bd.'"', 'offset' => $ppg*$i, 'limit' => $ppg]);
			foreach($rs as $r){
				$lt = $r->getLastTrack();
				if(preg_match('/(?<!未)(妥投|签收|已投到|已领取|自提点)/', $lt->activity)){
					$r->status = 99;
					$r->save();
					echo $r->hbn." delivered\n";
					$c++;
				}
			}
		}
		echo 'Total '.$c." delivered\n";
	}

	public function idAddCheckStamp(){
		foreach(glob(realpath('./id_joined').DIRECTORY_SEPARATOR.'*.jpg') as $f){
			$s = getimagesize($f);
			$im=imagecreatefromjpeg($f);
			imagealphablending($im, true);
			imagesavealpha($im, true);
			$wm=imagecreatefrompng(realpath('../images/id_check_stamp.png'));
			imagecopy($im, $wm, 0, round($s[1] / 2 * 0.75), 0, 0, 600, 40);
			imagecopy($im, $wm, 0, round($s[1] * 0.875), 0, 0, 600, 40);
			imagejpeg($im, realpath('./id_joined/stamped/').DIRECTORY_SEPARATOR.basename($f), 95);
		}
		echo "Done\n";
	}

	public function rsvAmqRange(){
		$aid = $this->prompt('Agent ID: ');
		$m = $this->prompt('Qty: ', 1000);
		$f = 'amq_'.$aid.'_'.time().'.txt';
		for($i = 0; $i < $m; $i++){
			$p = new ImParcel;
			$p->agent_id = $aid;
			$p->status = 10;
			$p->save();
			$p->ref = 'AMQ'.sprintf('%07s', substr($p->id, -7));
			$p->nolog = true;
			$p->isNewRecord = false;
			$p->update(['ref']);
			file_put_contents($f, $p->ref."\n" , FILE_APPEND);
		}
		echo "Done\n";
	}

	public function pricejmtoxa(){
		$rs = ExProdb::model()->findAll();
		$c = 0;
		foreach($rs as $r){
			if(!empty($r->mdata['price_CNJMN'])){
				$r->noaup = true;
				$r->mdata['price_CNXIA'] = $r->mdata['price_CNJMN'];
				$r->save();
				$c++;
			}
		}
		echo $c." products updated\n";
	}

	public function cusReport839(){
		$c = ExParcel::model()->count('agent_id = 839');
		$pg = ceil($c / 1000);
		echo "Date,Cnee,Mobile,State,Goods,Qty\n";
		for($i = 0; $i < $pg; $i++){
			$rs = ExParcel::model()->findAll(['condition' => 'agent_id = 839', 'limit' => 1000, 'offset' => $i * 1000]);
			foreach($rs as $r){
				if($r->goodsType() == 'B'){
					echo $r->created , ',', $r->cnee->name, ',', $r->cnee->tel, ',', $r->cnee->state, ',', $r->eitems['g'][0], ",", $r->eitems['q'][0],"\n";
				}
			}
		}
	}

	public function xeroTest(){
		include_once(Yii::app()->basePath.'/libs/XeroPHP/Autoloader.php');
		$config = [
			'oauth' => [
				'callback'         => 'http://localhost/',
				'consumer_key'     => '2QYRVIQMKA1PAMHUHTUMZT1GGEKKCE',
				'consumer_secret'  => 'EKCBDGCQ0RHDGAJGAFUKK12QMHDOOI',
				'rsa_private_key'  => 'file://'.Yii::app()->basePath.'\\config\\xero\\privatekey.pem',
			],
			'curl' => [
				CURLOPT_SSL_VERIFYPEER => false,
			],
		];
		$xero = new XeroPHP\Application\PrivateApplication($config);
		//print_r($xero->load('Accounting\\Organisation')->execute());
		//print_r($xero->loadByGUID('Accounting\\Invoice', '853d17a6-da96-446d-9b6b-658e7ce83599'));
		//print_r($xero->loadByGUID('Accounting\\Payment', '952e07eb-ad03-419e-ad36-2a9946330e1c'));
		//print_r($xero->loadByGUID('Accounting\\Journal', '8b56dcb5-23b9-403b-983a-fcc68bb5a67f'));
		//print_r($xero->loadByGUID('Accounting\\BankTransaction', '62304c2c-1d42-49a9-a861-d752171287fd'));
		print_r($xero->loadByGUID('Accounting\\Report\\Report', 'd853f6bc-e96b-4b7c-b3cc-7c1f73debfd3'));
	}

	public function readXeroUI(){
		//login
		$c = new curl('https://login.xero.com/');
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_COOKIESESSION, true);
		$c->setopt(CURLOPT_COOKIEJAR, "xero.cookie");
		$c->exec();
		preg_match('/name="__RequestVerificationToken" type="hidden" value="([^"]+)"/', $c->result, $rvt);

		$c->setopt(CURLOPT_POST, true);
		$c->setopt(CURLOPT_REFERER, 'https://login.xero.com/');
		$c->setopt(CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0', 'Cache-Control: no-cache', 'Content-Type: application/x-www-form-urlencoded; charset=utf-8'));

		$postr = $c->asPostString(array(
			'fragment' => '',
			'userName' => 'carlos@toplogistics.com.au',
			'password' => '1234567890',
			'__RequestVerificationToken' => $rvt[1],
		));
		$c->setopt(CURLOPT_POSTFIELDS, $postr);
		if(!$c->exec()) die("Problem logging in\n");
		//$c->setopt(CURLOPT_URL, 'https://go.xero.com/Bank/BankTransactions.aspx?accountID=654dba61-0366-4214-8ae5-a9eb322081a0&description=adjustment&startDate=2017-07-01&endDate=2017-07-31&show=true');
		$c->setopt(CURLOPT_URL, 'https://go.xero.com/Reporting/Report/Execute');
		$c->setopt(CURLOPT_COOKIEFILE, "xero.cookie");
		$c->setopt(CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0', 'Cache-Control: no-cache', 'Accept: */*', 'X-Requested-With: XMLHttpRequest', 'X-CSRFToken: puuJSOjVL4CW7uq9h5N1pEfHkASX464mf684kq2IzH1H6ueU-iNBLQ9GVr82Ur9B7e1MkZ82mGIPc6nOJGcigu1QEtOb1CsVGpCwYusjMoW3wY106R-H-Bn7Fx2OPX9umX4JhfJ04Xk3_oaRbw09xQcQ5XI1'));
		$c->setopt(CURLOPT_REFERER, 'https://go.xero.com/Reporting/Report/Run/1009');
		
		$c->setopt(CURLOPT_POSTFIELDS, $postr);
		$c->exec();
		file_put_contents('1701.json', $c->result);
		echo "Done\n";
	}

	public function wcaMember(){
		function getNodesByClass($dom, $cls){
			$finder = new DomXPath($dom);
			return $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $cls ')]");
		}

		function readMember($html,$id,$cc){
			$data = ['id' => $id, 'country' => $cc];
			$dom = new DomDocument();
			libxml_use_internal_errors(true);
			$dom->loadHTML($html, LIBXML_NOBLANKS&LIBXML_NOEMPTYTAG);

			// $nodes = getNodesByClass($dom, 'compid');
			// if(empty($nodes[0])) return $data;
			// if(preg_match('/ID: (\d+)/', trim($nodes[0]->nodeValue), $m)){
			// 	$data['id'] = $m[1];
			// }

			$nodes = getNodesByClass($dom, 'company');
			$data['name'] = trim($nodes[0]->nodeValue);
			$nodes = getNodesByClass($dom, 'branchname');
			$data['branch'] = trim($nodes[0]->nodeValue);
			
			// $nns = explode("\n", $nv);
			// if(empty($nns[1])){
			// 	if(preg_match('/^(.+)\s+\(([^,\)]+),*?([^,\)]*)\)$/', trim($nns[0]), $m)){
			// 		$data['name'] = trim($m[1]);
			// 		$data['city'] = trim($m[2]);
			// 		$data['type'] = empty($m[3])? '' : trim($m[3]);
			// 	}else{
			// 		$data['name'] = trim($nns[0]);
			// 	}
			// }else{
			// 	$data['name'] = trim($nns[0]);
			// 	$nns[1] = preg_replace('/ \- Administrative support provided by .+\)$/', ')', $nns[1]);
			// 	if(preg_match('/^\(([^,\)]+),*?([^,\)]*)\)$/', trim($nns[1]), $m)){
			// 		$data['city'] = trim($m[1]);
			// 		$data['type'] = empty($m[2])? '' : trim($m[2]);
			// 	}
			// }

			$nodes = getNodesByClass($dom, 'memberof_img');
			foreach($nodes as $n){
				$src = $n->childNodes[1]->childNodes[1]->getAttribute('src');
				// https://www.wcaworld.com/static/images/logoptone/logo/logo_wca.gif
				if(preg_match('/logo_(.+)\.gif$/', $src, $logo)){
					$data['members'][] = $logo[1];
				}
			}

			$nodes = getNodesByClass($dom, 'profile_row');
			$kvp = [];
			$ks = ['Address', 'Telephone', 'Emergency Call', 'Website', 'Email', 'Contact', 'Name', 'Title', 'Direct Line', 'Mobile'];
			$is_contact = false;
			foreach($nodes as $n){
				$ck = null;
				foreach ($n->childNodes as $i=>$c){
					if(empty($c->tagName)) continue;
					$v = trim($c->nodeValue);
					if($i == 1){
						$v = rtrim($v, ':');
						$ck = in_array($v, $ks)? $v : null;
						if($v == 'Name'){
							$data['contacts'] = [];
							$is_contact = true;
						}
						if($is_contact){
							if($ck == 'Name') $cp = &$data['contacts'][];
						}else{
							$cp = &$data;
						}
						continue;
					}

					if($i == 3 && !empty($ck) && !preg_match('/^Members only/', $v)){
						if($ck == 'Email') $v = preg_split('/[;, ]+/', $v);
						$cp[$ck] = $v;
					}
				}
			}
			return $data;
		}

		$countries = ['AF', 'AX', 'AL', 'DZ', 'AS', 'AD', 'AO', 'AI', 'AQ', 'AG', 'AR', 'AM', 'AW', 'AU', 'AT', 'AZ', 'BS', 'BH', 'BD', 'BB', 'BY', 'BE', 'BZ', 'BJ', 'BM', 'BT', 'BO', 'BQ', 'BA', 'BW', 'BV', 'BR', 'IO', 'BN', 'BG', 'BF', 'BI', 'BU', 'KH', 'CM', 'CA', 'CV', 'KY', 'CF', 'TD', 'CL', 'CN', 'CX', 'CC', 'CO', 'KM', 'CG', 'CD', 'CK', 'CR', 'CI', 'HR', 'CU', 'CW', 'CY', 'CZ', 'DK', 'DJ', 'DM', 'DO', 'EC', 'EG', 'SV', 'GQ', 'ER', 'EE', 'ET', 'FK', 'FO', 'FJ', 'FI', 'FR', 'GF', 'PF', 'TF', 'GA', 'GM', 'GE', 'DE', 'GH', 'GI', 'GR', 'GL', 'GD', 'GP', 'GU', 'GT', 'GN', 'GW', 'GY', 'HT', 'HM', 'VA', 'HN', 'HK', 'HU', 'IS', 'IN', 'ID', 'IR', 'IQ', 'IE', 'IL', 'IT', 'JM', 'JP', 'JO', 'KZ', 'KE', 'KI', 'KP', 'KR', 'XK', 'KW', 'KG', 'LA', 'LV', 'LB', 'LS', 'LR', 'LY', 'LI', 'LT', 'LU', 'MO', 'MK', 'MG', 'MW', 'MY', 'MV', 'ML', 'MT', 'MH', 'MQ', 'MR', 'MU', 'YT', 'MX', 'FM', 'MD', 'MC', 'MN', 'ME', 'MS', 'MA', 'MZ', 'MM', 'NA', 'NR', 'NP', 'NL', 'AN', 'NC', 'NZ', 'NI', 'NE', 'NG', 'NU', 'NF', 'NO', 'OM', 'PK', 'PW', 'PS', 'PA', 'PG', 'PY', 'PE', 'PH', 'PN', 'PL', 'PT', 'PR', 'QA', 'RE', 'RO', 'RU', 'RW', 'BL', 'SH', 'KN', 'LC', 'PM', 'TS', 'VC', 'SP', 'WS', 'SM', 'ST', 'SA', 'SN', 'RS', 'SC', 'SL', 'SG', 'SX', 'SK', 'SI', 'SB', 'SO', 'ZA', 'GS', 'SS', 'ES', 'LK', 'SD', 'SR', 'SJ', 'SZ', 'SE', 'CH', 'SY', 'TW', 'TJ', 'TZ', 'TH', 'TL', 'TG', 'TK', 'TO', 'TT', 'TN', 'TR', 'TM', 'TC', 'TV', 'UG', 'UA', 'AE', 'GB', 'UM', 'US', 'UY', 'UZ', 'VU', 'VE', 'VN', 'VG', 'VI', 'WF', 'EH', 'YE', 'ZM', 'ZW']; //all counties
		$countries = ['AU', 'BD','BR','CA','CN','FR','DE','HK','IN','ID','IT','KR','MY','MX','NL','PK','SG','ES','TW','TH','TR','AE','GB','US','VN']; //main countries

		if(empty($this->args[1]) || $this->args[1] == '-d'){
			//login
			$c = new curl('https://webservice.wcaworld.com/wcasso/wcasso/LogIn');
			$c->setopt(CURLOPT_FOLLOWLOCATION, true);
			$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
			$c->setopt(CURLOPT_SSL_VERIFYHOST, false);
			$c->setopt(CURLOPT_POST, true);
			$c->setopt(CURLOPT_COOKIESESSION, true);
			$c->setopt(CURLOPT_REFERER, 'http://www.wcaworld.com/Account/Login');
			$c->setopt(CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0', 'Cache-Control: no-cache'));
			$postr = $c->asPostString(array(
				'password' => '817421',
				'username' => 'PCAsdy',
			));
			$c->setopt(CURLOPT_POSTFIELDS, $postr);
			$c->setopt(CURLOPT_COOKIEJAR, "wca.cookie");
			$c->exec();

			// if($this->debug) file_put_contents('wca_login.html', $c->result);
			$c->setopt(CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0', 'Cache-Control: no-cache'));
			$c->setopt(CURLOPT_COOKIEFILE, "wca.cookie");
			$c->setopt(CURLOPT_POST, false);
		}else{
			var_dump(readMember(file_get_contents($this->args[1])));
			return;
		}

		$json_file = 'wca_members.json';
		if(!is_file($json_file)) file_put_contents($json_file, '');
		$dl = file($json_file);
		$mids = [];
		foreach($dl as $i => $l){
			$l = json_decode(trim($l), true);
			if(empty($l['id'])) echo 'Problem reading line: '.$i."\n";
			else $mids[] = $l['id'];
		}

		function getList ($wca, $qs, &$cmids, &$c, $pg=1){
			$c->setopt(CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0', 'Cache-Control: no-cache', 'Accept: text/html,*/*', 'X-Requested-With: XMLHttpRequest', 'Access-Control-Allow-Origin: *', 'WCA-Referer: '.$wca['referer'], 'Authorization: Basic '.$wca['token'], 'Origin: https://www.wcaworld.com'));
			$c->setopt(CURLOPT_REFERER, 'https://www.wcaworld.com/Directory'.$qs);
			$c->setopt(CURLOPT_URL, $wca['api'].$qs.'&sid='.$wca['sid'].'&pageSize=100&=pageNumber='.$pg.'&pageIndex='.$pg);
			$c->exec();

			if(preg_match_all('/\/directory\/members\/(\d+)/', $c->result, $m)){
				$cmids = array_merge($cmids, array_unique($m[1]));
			}

			// <a href="#" onmouseover="onLoadMoreResult('%3fsiteID%3d24%26networkId%3d24%26pageNumber%3d2%26pageIndex%3d2%26pageSize%3d100%26allnet%3dyes%26networkIds%3d1%26networkIds%3d2%26networkIds%3d3%26networkIds%3d4%26networkIds%3d61%26networkIds%3d98%26networkIds%3d108%26networkIds%3d6%26networkIds%3d5%26networkIds%3d22%26networkIds%3d13%26networkIds%3d18%26networkIds%3d15%26networkIds%3d16%26networkIds%3d105%26networkIds%3d38%26licenseIds%3d0%26licenseIds%3d0%26licenseIds%3d0%26searchby%3dCountryCode%26orderby%3dCountryCity%26country%3dCN%26city%3d%26keyword%3d%26_t%3d5e5649be%26sid%3de5ee534e-4a1d-f6f0-57d4-381997502d32%26type%3d%26state%3d%26q%3dsql%26_%3d1582713278032%26lastCid%3d0'); return false;" class="loadmore">CLICK HERE TO LOAD MORE RESULTS</a>
			if(preg_match('/onmouseover="onLoadMoreResult[^"]+pageIndex\%3d(\d+)\%26/', $c->result, $m)){
				getList($wca, $qs, $cmids, $c, $m[1]);
			}
		};

		foreach($countries as $cc){
			//read
			$qs = '?siteID=24&networkId=24&allnet=yes&networkIds=1&networkIds=2&networkIds=3&networkIds=4&networkIds=61&networkIds=98&networkIds=108&networkIds=6&networkIds=5&networkIds=22&networkIds=13&networkIds=18&networkIds=15&networkIds=16&networkIds=105&networkIds=38&licenseIds=0&licenseIds=0&licenseIds=0&searchby=CountryCode&orderby=CountryCity&country='.$cc.'&city=&keyword=&type=m&state=&q=sql';
			$c->setopt(CURLOPT_URL, 'https://www.wcaworld.com/Directory'.$qs);
			$c->setopt(CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0', 'Cache-Control: no-cache'));
			$c->exec();

			preg_match_all('/(token|api|referer|sid):\s+\'([^\']+)\',/', $c->result, $ms);
			$wca = [];
			foreach($ms[1] as $k => $v){
				$wca[$v] = $ms[2][$k];
			}

			if($this->debug) file_put_contents('wca_list_'.$cc.'.html', $c->result);
			$cmids = [];
			getList($wca, $qs, $cmids, $c);

			echo $cc.' has '.count($cmids).' members', PHP_EOL;
			$i = 0;
			foreach($cmids as $id){
				if(in_array($id, $mids)) continue;
				sleep(1);
				echo "Reading ".$id."\n";
				$c->setopt(CURLOPT_URL, 'https://www.wcaworld.com/directory/members/'.$id);
				$c->setopt(CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0', 'Cache-Control: no-cache'));
				$c->exec();

				preg_match_all('/(token|api|referer|type):\s+\'([^\']+)\',/', $c->result, $ms);
				$member = [];
				foreach($ms[1] as $k => $v){
					$member[$v] = $ms[2][$k];
				}

				$c->setopt(CURLOPT_URL, $member['api'].'/24/members/'.$id.'/view?type='.$member['type'].'&q=sql');
				$c->setopt(CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0', 'Cache-Control: no-cache', 'Accept: text/html,*/*', 'X-Requested-With: XMLHttpRequest', 'Access-Control-Allow-Origin: *', 'WCA-Referer: '.$member['referer'], 'Authorization: Basic '.$member['token'], 'Origin: https://www.wcaworld.com'));
				$c->setopt(CURLOPT_REFERER, 'https://www.wcaworld.com/directory/members/'.$id);
				$c->exec();

				file_put_contents($json_file, json_encode(readMember($c->result, $id, $cc))."\n", FILE_APPEND);
				if($this->debug) file_put_contents('wca_member_'.$id.'.html', $c->result);
				$i++;
			}
			echo $i.' '.$cc.' Members Saved', PHP_EOL;
		}
	}

	public function wcaExcel(){
		$json_file = 'wca_members.json';
		$dl = file($json_file);
		$xls = new oExcel;
		$xls->setColWidth([15,10,10,10]);
		$i = 1;
		$xls->addRow($i++, ['ID', 'Company', 'Type', 'Country', 'City', 'Website', 'Emergency Call', 'Email', 'Contact Name', 'Title', 'Direct Line', 'Mobile', 'Email']);
		
		foreach($dl as $l){
			$l = json_decode(trim($l), true);
			if(empty($l['id']) || empty($l['name'])) continue;
			$city = preg_match('/\(([^-,\)]+)/', @$l['branch'], $m)? $m[1] : '';
			$members = implode(', ', $l['members']);
			$xls->addRow($i++, [$l['id'], $l['name'], $members, $l['country'], $city, @$l['Website'], @$l['Emergency Call'], empty($l['Email'])? '' : implode(', ',$l['Email'])]);
			if(!empty($l['contacts'])){
				foreach($l['contacts'] as $c){
					$xls->addRow($i++, [$l['id'], $l['name'], $members, $l['country'], $city, '', '', '', $c['Name'], $c['Title'], $c['Direct Line'], $c['Mobile'], empty($c['Email'])? '' : implode(', ',$c['Email'])]);
				}
			}
		}
		$xls->output('wca_members.xlsx', null, false);
		echo "Done\n";
	}

	public function resendYtoOMS(){
		$fd = $this->prompt('From Date: ', date('Y-m-d'));
		$rs = YtoOms::model()->findAll("md >= :md AND ref = '' AND status = 0", [':md' => strtotime($fd)]);
		
		$yto = new YtoAPI('tnbrwdhz7e4kvx1g', '75gfn3uw6osjr2z4ly9xap1k0icvdqht'); // Jack
		switch($rs[0]->tpl){
			case 'CNJMN':
			case 'CNJM2':
				$pdc = 'AU0007';
			break;
			case 'CNXIA':
				$pdc = 'AU0006';
			break;
			case 'CNHGH':
				$pdc = 'AU0003';
			break;
		}
		$err = false;

		foreach($rs as $i => $p){
			$r = $yto->createOmsOrder($p, $pdc);
			$r = json_decode($r->ServiceEntranceResult);
			if(empty($r->success)){
				echo 'ORD:'.$p->hbn.': '.$r->cnmessage."\n";
				continue;
			}
			$p->ref = $r->data->shipping_method_no;
			$p->status = 1;
			$p->save();
		}

		foreach($rs as $i => $p){
			if(empty($p->ref)) continue;
			$r = $yto->getDistribute($p->ref);
			$r = json_decode($r->ServiceEntranceResult);
			if(isset($r->data->distribute_code)){
				$p->mdata['dtb'] = $r->data->distribute_code;
				$p->status = 2;
				$p->save();
			}else{
				echo 'DTB:'.$p->hbn.': '.$r->cnmessage."\n";
			}
		}

	}

	public function addOTno(){
		$sql = "SELECT id FROM shipment WHERE consol_id IN (SELECT id FROM consol WHERE poc IN ('CNXMN', 'CNXM2', 'CNCSX', 'CNCS2') AND created > '2017-08-15')";
		$rs = $this->db->createCommand($sql)->queryAll();
		$c = 0;
		foreach($rs as $p){
			if(OriginTrace::model()->count('pid = '.$p['id']) == 0){
				Yii::app()->db->createCommand('UPDATE origin_trace SET pid = '.$p['id'].' WHERE pid = 0 LIMIT 1')->execute();
				echo $p['id']."\n";
				$c++;
			}
		}

		echo $c." records updated\n";
	}

	public function checkPLInv(){
		$rs = PickupList::model()->findAll('created > DATE_SUB(NOW(), INTERVAL 3 MONTH)');
		foreach($rs as $r){
			$il = InvLine::model()->with('invoice')->find('invoice.status NOT IN (1, 10) AND t.fid = :fid AND t.model = "Manifest"', [':fid' => $r->id]);
			if(empty($il)) continue;
			$t = sizeof($il->mdata['items']);
			$t2 = $r->countLines();
			if($t < $t2){
				echo $r->ref.": ".($t2-$t)."\n";
				$ts = [];
				foreach($il->mdata['items'] as $l){
					$ts[] = $l[0];
				}
				foreach($r->lines as $l){
					$p = $l->mm();
					if(!in_array($p->hbn, $ts)){
						echo $p->hbn."\n";
					}
				}
			}elseif($t > $t2){
				echo $r->ref.": ".($t2-$t)."\n";
			}
		}
		echo "Done\n";
	}

	public function turtleXIA3in1(){
		$id_dir = $this->args[2];
		$data = $this->_loadXlsData($this->args[1]);
		unset($data[0]);
		$rs = [];

		foreach($data as $r){
			if(empty($r[1])) continue;

			if(!isset($rs[$r[1]])){
				$p = new ExParcel;
				$p->hbn = $r[1];
				$p->ref = $r[1];
				$p->weight = $r[6];
				$p->cnee = new Addr;
				$p->cnee->name = $r[9];
				$p->cnee->address = $r[10];
				$p->cnee->tel = $r[11];
				$p->cnee->cnid_no = $r[13];
				$p->cnor = new Addr;
				$p->cnor->name = 'Turtle Express';
				$p->cnor->tel = '(02) 83100421';
				$p->eitems = ['g' => [], 'b' => [], 'q' => [], 'm' => []];
				$p->consol = new ExcoConsol;
				$p->consol->poc = 'CNXIA';
				$p->consol->exrate = 5.2;
				$rs[$r[1]] = $p;
			}

			$gb = preg_split('/[,，]+/', $r[2]);
			$pd = ExProdb::model()->find('name_zh LIKE :p', [':p' => '%'.$gb[0].'%']);
			if(empty($pd)) $pd = ExProdb::model()->find('name_zh LIKE :p', [':p' => '%'.mb_substr($gb[0], 0, 2).'%']);
			$rs[$r[1]]->eitems['g'][] = $gb[0];
			$rs[$r[1]]->eitems['b'][] = empty($gb[1])? '' : $gb[1];
			$rs[$r[1]]->eitems['type'][] = empty($pd)? 'O' : substr($pd->getType(), 0, 1);
			$rs[$r[1]]->eitems['q'][] = $r[23];
			$rs[$r[1]]->eitems['m'][] = $r[22];
			$rs[$r[1]]->eitems['t'][] = 50;
			$rs[$r[1]]->eitems['pid'][] = empty($pd)? 0 : $pd->id;
		}

		$zf = $id_dir.'_3in1_'.date('YmdHi').'.zip';
		$zip = new ZipArchive;
		$zip->open($zf, ZipArchive::CREATE);
		$td = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
		mkdir($td);
		$tt = sizeof($rs);
		$i = 1;
		foreach($rs as $p){
			//label
			$html = oPDF::renderHTML('../expLabel/label_yto3', array('p'  => $p), true);
			$tf = tempnam($td, "clabel");
			oPDF::html2image($html, 2, $tf);
			$fn = empty($p->ref)? $p->hbn : $p->ref;

			//id
			$idp = $id_dir.DIRECTORY_SEPARATOR.$p->cnee->cnid_no.'.jpg';
			if(!is_file($idp)){
				echo 'ID '.$p->cnee->cnid_no." not found\n";
				$idp = '';
			}
			
			//rcpt
			$d = $p->receiptData(false, false);
			$tf2 = tempnam($td, "rcpt");
			$html = oPDF::renderHTML('receipt_'.$d['tpl'], array('r' => $p, 'd' => $d), true);
			oPDF::html2image($html, 2, $tf2);
			$magick = '/usr/bin/convert';
			$magick = 'C:\Progra~1\ImageMagick-7.0.4-Q16\magick.exe';
			exec($magick.' '.$tf.' ( '.$idp.' -resize "600x" ) '.$tf2.' +append '.$tf);
			$zip->addFile($tf, $fn.'.jpg');

			if(empty($p->cnee->cnid_no)) continue;
			$tf3 = tempnam($td, "idv");
			oPDF::renderImage('cnid_validation', ['name' => $p->cnee->name, 'idno' => $p->cnee->cnid_no], 2, $tf3);
			$zip->addFile($tf3, $p->cnee->cnid_no.'.jpg');
			echo $i++.'/'.$tt.':'.$p->hbn."\n";
		}

		$zip->close();
		AppHelper::unlinkRecursive($td);
		echo "Done\n";
	}

	public function exportAWB(){
		$awbs = explode(",", $this->prompt('AWBS: '));
		$rs = ExcoConsol::model()->findAll('awb IN ("'.implode('", "', $awbs).'")');
		foreach($rs as $r){
			echo $r->no;
			$f = FileRepo::model()->find('fid = :id AND type = 80 AND status = 20 AND name LIKE :n', [':id' => $r->id, ':n' => '%pdf']);
			if(!empty($f)){
				copy($f->getFile(), $r->awb.'.pdf');
				echo " exported\n";
			}
			else echo " no file\n";
		}
		echo "Done\n";
	}

	public function getOldRef(){
		$rs = ExParcel::model()->findAll('consol_id = :cid', [':cid' => empty($this->args[1])? $this->prompt('Consol ID: ') : $this->args[1]]);
		foreach($rs as $p){
			foreach($p->logs as $l){
				if(!empty($l->extra['note']) && preg_match('/Ref changed from: (.+)/', $l->extra['note'], $m)){
					echo "'".$p->hbn."' => '".$m[1]."',\n";
				}
			}
		}
	}

	public function xeroJson2Excel(){
		$d = json_decode(file_get_contents($this->args[1]), true);

		$xls = new oExcel;
		$i = 1;
		$hdr = [];
		foreach($d['Reports'][0]['Sections'][0]['Columns'] as $c){
			$hdr[] = $c['N'];
		}
		$xls->addRow($i++, $hdr);
		foreach($d['Reports'][0]['Sections'][0]['Grids'][0]['R'] as $rs){
			foreach($rs['R'] as $r){
				if(!empty($r['T']) && in_array($r['T'], ['Total', 'Balance'])) continue;
				$rd = [];
				foreach($r['C'] as $c){
					$rd[] = $c['V'];
				}
				$xls->addRow($i++, $rd);
			}
		}

		$xls->output(str_replace('.json', '.xlsx', $this->args[1]), null, false);
		echo "Done\n";
	}

	public function wca2xls(){
		$rs = file('wca_chinese_member.json');
		$ks = ['id', 'name', 'type', 'city', 'Address', 'Website', 'Telephone', 'Emergency Call', 'Email'];
		$ps = ['Name', 'Title', 'Direct Line', 'Email'];
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, array_merge($ks, $ps));
		foreach($rs as $r){
			$d = json_decode(trim($r), true);
			$p = [];

			foreach($ks as $k){
				if(!isset($d[$k])) $d[$k] = '';
				if($k == 'Email') $p[] = empty($d[$k])? '' : implode(';', $d[$k]);
				else $p[] = $d[$k];
			}
			if(empty($d['contacts'])){
				$d['contacts'] = [['Name' => '']];
			}
			foreach($d['contacts'] as $c){
				$l = [];
				foreach($ps as $k){
					if(!isset($c[$k])) $c[$k] = '';
					if($k == 'Email'){
						$l[] = empty($c[$k])? '' : implode(';', $c[$k]);
					}else{
						$l[] = $c[$k];
					}
				}
				$xls->addRow($i++, array_merge($p, $l));
			}
		}
		$xls->output('wca.xlsx', null, false);
		echo "Done\n";
	}

	public function rmExpByProdName(){
		$criteria = new CDbCriteria();
		$criteria->compare('status', '<30', true);
		$criteria->addInCondition("no", preg_split('/[\s;,]+/', $this->prompt('Consol #: ')));
		$rs = ExcoConsol::model()->findAll($criteria);
		$reg = $this->prompt('Product Regex: ');
		$ms = [];
		$a = '';
		foreach($rs as $c){
			echo $c->no, ": ";
			$i = 0;
			foreach($c->shipments as $p){
				if(preg_match($reg, implode(',', $p->eitems['g']))){
					$i++;
					$ms[] = $p;
					$a .= "'".$p->hbn."' => '".$p->ref."',\n";
				}
			}
			echo $i." records\n";
		}
		file_put_contents('rm.log', $a);
		if(strtoupper($this->prompt("Move Out?", 'n')) == 'Y'){
			foreach($ms as $p){
				$p->ref = '';
				$p->consol_id = 0;
				$p->save();
				//move out pallet if any
				$m = ManiMap::model()->with('manifest')->find('fid = :f AND manifest.type = 60', [':f' => $p->id]);
				if(!empty($m)){
					$m->mani_id = 0;
					$m->save();
				}
			}
		}
		echo "Done\n";
	}

	public function bc462Sku(){
		$pds = include('462_bc_prod.php');
		$c = ExcoConsol::model()->find('no = :n', [':n' => $this->args[1]]);
		foreach($c->shipments as $p){
			$cltent = $p->mdata['client_entry'];
			foreach($p->eitems['g'] as $gi => $g){
				$pd = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
				$sku = '';
				//if($pd) $sku = empty($pd->mdata['id_CNXMN'])? $pd->sku : $pd->mdata['id_CNXMN'];
				if(empty($sku) && !empty($cltent['items'])){
					$g = $cltent['items']['g'][$gi];
					if(empty($g)) continue;
					echo $g;
					foreach($pds as $r){
						if(strpos(trim($r[2]), trim($g)) !== false){
							echo ' : '.$r[0];
							if(!empty($pd)){
								$pd->mdata['id_CNXMN'] = $r[0];
								$pd->noaup = true;
								$pd->save();
							}
							break;
						}
					}
					echo "\n";
				}
			}
		}
	}

	public function chkExpStock(){
		$ds = file($this->args[1]);
		echo "HBN,Agent,Status,Pickup,Info Ready,Duplicate\n";
		foreach($ds as $l){
			$l = trim($l);
			$p = ExParcel::model()->find('hbn = :h OR ref = :h', [':h' => $l]);
			if(empty($p)){
				echo $l , ",0,not found\n";
				continue;
			}
			if($p->status > 18 && $p->status < 100) continue;
			$days = $p->getPerformanceDates();
			echo $l , ",", $p->agent_id, ",", $p->getStatus(), ",", $days[0], ",", empty($days[5])? '' : $days[5], ",", $p->status == 18? $p->dupCount() : '', "\n";
		}
		echo "Done\n";
	}

	public function loadUgg(){
		//$d = file('ugg.tsv');
		$rs = [];
		$pm = ['豆豆鞋' => 130, '短靴' => 160, '长靴' => 200, '童鞋' => 130, '围巾' => 80];
		$z2e = ['豆豆鞋' => 'Sneaker', '短靴' => 'Short Boots', '长靴' => 'Boots', '童鞋' => 'Children Boots', '围巾' => 'Scarf'];
		$wts = ['豆豆鞋' => 0.8, '短靴' => 1, '长靴' => 1.2, '童鞋' => 0.8, '围巾' => 0.2];
		$nids = ['Ozlamb|童鞋' => 'IE214817002',
'Emu Australia|童鞋' => 'IE214817032',
'Ozlana|童鞋' => 'IE214817033',
'Tasman UGG|童鞋' => 'IE214817034',
'D&K|童鞋' => 'IE214817035',
'AXA|童鞋' => 'IE214817036',
'Ozwear|童鞋' => 'IE214817037',
'Everugg|童鞋' => 'IE214817038',
'Viva|童鞋' => 'IE214817039',
];
		/*foreach($d as $l){
			$r = explode("\t", trim($l));
			$r[1] = str_replace('筒', '靴', $r[1]);
			if($r[1] == '中靴') $r[1] = '短靴';
			$rs[$r[2].'|'.$r[1]][] = $r[0];
		}*/

		$c = 0;
		foreach($nids as $k => $v){
			$d = explode('|' ,$k);
			$p = ExProdb::model()->find('brand = :b AND name_zh = :n', [':b' => $d[0], ':n' => $d[0].' '.$d[1]]);
			if(empty($p)){
				$p = new ExProdb;
				$p->type = 90;
			}
			$p->brand = $d[0];
			$p->model = $d[1];
			$p->name_zh = $d[0].' '.$d[1];
			$p->name = $d[0]. ' '.$z2e[$d[1]];
			$p->price = $pm[$d[1]];
			$p->status = 1;
			$p->hs = $d[1] == '围巾'? '04020200' : '06029900';
			$p->hs2 = $d[1] == '围巾'? '04020200' : '6405200090';
			//$p->tag = $d[1].','.implode(',', $v);
			$p->weight = $wts[$d[1]];
			$p->noaup = true;
			$p->save();

			$pp = ExProdbPrice::model()->find('pid = :id AND poc = :poc', [':id' => $p->id, ':poc' => 'CNTAO']);
			if(empty($pp)){
				$pp = new ExProdbPrice;
				$pp->type = 10;
				$pp->pid = $p->id;
				$pp->poc = 'CNTAO';
				$pp->status = 1;
			}
			$pp->price = $p->price;
			$mn = preg_replace('/(UGG|SHEEPSKIN)\s+/', '', $p->name_zh);
			$pp->sn = empty($v)? '' : $v;
			$pp->save();
			echo $p->name_zh.':'.$pp->sn."\n";
			$c++;
		}
		echo $c." products created\n";
	}
		
	public function eqtyxls(){
		$start_date=$this->prompt('start date:');
		$end_date=$this->prompt('end date:');
		$rs=ReconciliationLine::model()->with('parent')->findAll('parent.client_type=1 AND parent.invoice_date > :start_date AND  parent.invoice_date < :end_date',
				[":start_date"=>$start_date,":end_date"=>$end_date]);
		$weight=0;
		$cbm=0;
		if(!empty($rs)){
			foreach ($rs as $r){
				$p = ImParcel::model()->find('ref = :ref' ,[':ref' => $r->shipment_no]);
				if(empty($p->cbm))                       continue;
				$weight+=$r->weight;
				$cbm+=$p->cbm;
			}
		}
		$c=$cbm*250/$weight;
		echo "the factor is :".$c;
	}
	public function ccicAPItest(){
		$a = new CcicOriginAPI(true);
		//$a->getNumber();
		$gts = ['B' => '婴儿奶粉', 'M' => '奶粉', 'O' => '保健品', 'X' => '奶粉, 保健品'];
		$dt = date('Y-m-d');
		$dts = strtotime('+1 day');

		$rs = OriginTrace::model()->findAll('pid > 0 AND bdate IS NULL');
		$css = [];
		$cpd = [];
		foreach($rs as $r){
			$p = ExParcel::model()->findByPk($r->pid);
			if(empty($p->consol_id)) continue;
			if(empty($css[$p->consol_id])){
				$cl = ExcoConsol::model()->findByPk($p->consol_id);
				$css[$p->consol_id] = [
					'CCode' => 1168,
					'FCode' => $cl->flight,
					'FDate' => $cl->etd,
					'AWBNo' => $cl->awb,
					'DCity' => '澳大利亚, 悉尼',
					'AWBC' => $cl->totShipments(),
					'AWBW' => $cl->totWeight(),
					'WH' => '6C The Crescent, Kingsgrove, NSW 2208',
					'URL' => 'https://www.pcaexpress.com.au/tracking/?c=',
					'items' => [],
				];
				$cpd[$p->consol_id] = $cl->pod;
				/*
				$f = FileRepo::model()->find('fid = :id AND type = 80 AND status = 20 AND name LIKE :n', [':id' => $p->consol_id, ':n' => '%pdf']);
				if(!empty($f)){
					copy($f->getFile(), $cl->awb.'.pdf');
				}
				*/
			}
			if(!in_array($cpd[$p->consol_id], ['CNCSX', 'CNXMN', 'CNXM2'])){
				$r->pid = 0;
				$r->save();
				continue;
			}
			if(empty($css[$p->consol_id]['AWBNo']) || empty($css[$p->consol_id]['FCode']) || empty($css[$p->consol_id]['FDate']) || strtotime($css[$p->consol_id]['FDate']) > $dts) continue;
			$css[$p->consol_id]['items'][] = [
					'Code' => $r->no,
					'PNo' => $p->hbn,
					'CCity' => '澳大利亚, 悉尼',
					'CDate' => $p->pickupDate(),
					'PT' => $gts[$p->goodsType()],
					'PC' => array_sum($p->eitems['q']),
					'PW' => $p->shipWeight(),
				];
			//$r->bdate = $dt;
			//$r->save();
		}

		foreach($css as $cid => $d){
			if(empty($d['items'])) continue;
			if($a->sendData($d)){
				foreach($d['items'] as $itm){
					$r = OriginTrace::model()->find('no = :n', [':n' => $itm['Code']]);
					$r->bdate = $dt;
					$r->save();
				}
				echo 'Sent '.$d['AWBNo']."\n";
			}
			break;
		}
		echo "Done\n";
	}

	public function yrdata(){
			$rs = ExcoConsol::model()->findAll(['condition' => 'poc = "CNXM2" AND status = 80 AND created > "2017-08-01"']);
			$i = 0;
			foreach($rs as $r){
					foreach($r->shipments as $p){
							$i++;
							if($i<3000) continue;
							echo $p->hbn, "\t", $p->ref, "\t", $p->cnee->state, "\t", $p->getDvalue(),"\n";
					}
					if($i > 7000) break;
			}
	}

	public function qdcc(){
		$c = ExcoConsol::model()->findByPk($this->args[1]);
		$xls = new oExcel;
		ini_set('precision', 12);
		//$sheet = $xls->getActiveSheet();
		//$sheet->setTitle('运单');

		$i = 1;
		$ttls = array('','','','分运单号','商品编号附加编号','英文货物名称','中文货物名称','','规格','件数','净重','','重量','','数量','计量单位','单价','申报单价','申报总价','','币制','收件人','收件人电话','收件人详细地址','收件人身份证号码','发件人','发件人地址','发件人电话','发件人城市','报关类别','贸易方式','原产/消费国','经营单位代码','经营单位名称');
		$xls->addRow($i++, $ttls);
		$pi = 1;
		$rows = [];
		$ri = 0;
		foreach($c->shipments as $p){
			$gc = sizeof($p->eitems['g']);
			$tnt = 0;
			if(is_array($p->eitems['w'])){
				foreach($p->eitems['w'] as $gi => $w){
					if(empty($w)) continue;
					$tnt += $w;
				}
			}
			$swt = $p->shipWeight(true);

			if($tnt <= 0) $tnt = min($swt * 0.9, $swt - 0.2);
			if($tnt <= 0) $tnt = 0.1;
			$tv = 0;
			foreach($p->eitems['g'] as $gi => $g){
				$pd = ExProdb::model()->findByPk($p->eitems['pid'][$gi]);
				$hs = empty($pd->mdata['hs_CNTAO'])? $pd->hs : $pd->mdata['hs_CNTAO'];
				$u = HS::getUpr($hs, 'uc');
				$hs = empty($pd->mdata['hs_CNTAO'])? $pd->hs : $pd->mdata['hs_CNTAO'];
				$qty = $p->eitems['q'][$gi];
				$ge = empty($pd)? '' : $pd->name;

				if(empty($pd)){
					$uv = $p->eitems['v'][$gi] / $c->exrate;
				}else{
					$uv = empty($pd)? $p->eitems['t'][$i] : ((empty($pd->mdata['price_CNTA2'])? $pd->price : $pd->mdata['price_CNTA2']) / $c->exrate);
					$p->eitems['g'][$gi] = empty($pd->mdata['name_CNTAO'])? $pd->name_zh : $pd->mdata['name_CNTAO'];
					$p->eitems['m'][$gi] = $pd->model;
					$p->eitems['b'][$gi] = $pd->brand;
					$p->eitems['w'][$gi] = $pd->weight * $qty;
				}

				switch($p->eitems['type'][$gi]){
					case 'B':
						$m = (($p->eitems['w'][$gi] / $qty) * 1000).'g/罐';
						if($qty < 3){
							$awt = 1.2 * $qty;
						}elseif($qty == 3){
							$awt = 3.5;
						}elseif($qty == 4){
							$awt = 4.6;
						}elseif($qty == 6){
							$awt = 7;
						}
					break;
					case 'M':
						$m = (($p->eitems['w'][$gi] / $qty) * 1000).'g/袋';
						$awt = $p->eitems['q'][$gi] + 0.2;
					break;
					case 'O':
						$m = $p->eitems['m'][$gi];
					break;
				}

				$nw = empty($pd)? $p->eitems['w'][$gi] : $pd->weight * $p->eitems['q'][$gi];
				$uv = sprintf('%.2f', $uv);
				$tv += $uv * $qty;

				$rows[$ri++] = [$gi+1, $pi, $gc, '="'.$p->ref.'"', '="'.$hs.'"', $ge, $g, ($gi==0? $g : ''), $m, ($gi==0? 1: 0), $p->eitems['w'][$gi], ($gi==0? $tnt: ''), round($p->eitems['w'][$gi] / $tnt * $swt, 2), ($gi==0? $swt : ''), $qty, $u, $uv, $uv, $uv * $qty, '', 601, $p->cnee->name, $p->cnee->tel, $p->cnee->getCnFullAddress(), '="'.$cnid.'"', (empty($p->cnor->address)? '6C The Crescent, Kingsgrove, NSW 2208' : $p->cnor->fullAddress()), (empty($p->cnor->tel)? '="1800518000"' : '="'.$p->cnor->tel.'"')];
				$pi++;
			}
			$rows[$ri-$gi-1][19] = $tv;
			if($gi > 0){
				$xls->mergeCells('H'.($ri-$gi+1).':H'.($ri+1));
				$xls->mergeCells('L'.($ri-$gi+1).':L'.($ri+1));
				$xls->mergeCells('N'.($ri-$gi+1).':N'.($ri+1));
				$xls->mergeCells('T'.($ri-$gi+1).':T'.($ri+1));
			}
		}

		foreach($rows as $r){
			$xls->addRow($i++, $r);
		}
		$xls->output($c->awb.'.xlsx', null, false);
		echo "Done\n";
	}

	public function expreturned(){
		$criteria = new CDbCriteria();
		$criteria->addInCondition("hbn", preg_split('/[\s;,]+/', $this->prompt('HBNs: ')));
		$rs = ExParcel::model()->findAll($criteria);
		$cr = $this->prompt('Credit?', 'N');
		$c = 0;
		foreach($rs as $r){
			$r->status = 104;
			$r->save();
			if(strtoupper($cr) == 'Y') Payment::createReturnedCreditNote($r);
			echo $r->hbn." returned\n";
			$c++;
		}

		echo 'Total '. $c. " returned\n";
	}

	public function FixInvoiceStatus(){
		$rs = Invoice::model()->findAll('status IN (3,7)');
		foreach($rs as $r){
			$os = $r->status;
			$r->checkPaid();
			if($r->status != $os){
				$r->save();
				echo $r->no." updated\n";
			}
		}
	}
	public function bfeInvoice(){
		$consol_id=$this->prompt('consol_id:');
		$consol_no=$this->prompt('consol_no:');
		$consol= Consol::model()->find('id=:id AND no=:no',array(':id'=>$consol_id,':no'=>$consol_no));
		$con=$consol;
		$owner = Org::model()->findByPk(1206);
		$ownerId=$owner->id;
		if(!empty($consol)){
		$inv = new Invoice;
		$inv->type = 10;
		$inv->dpmt = Invoice::DPMT_IMPORT;
		$inv->to_id = $ownerId;
		$inv->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY; // default set Sydney as warehouse
		$inv->ref = 'fw'.date('Ymd', strtotime($consol->created));
		$inv->currency = 1;
		$inv->status = 2;
		$inv->date = date('Y-m-d', strtotime($consol->created));
		$inv->due = $inv->date;
		$inv->save();

	$items = [];
	$tot = 0;

	// for special org 1206 client , we use special charge code
	// to calculate the voice
	$chargeCode = '';
	$chargeCode = 8271;
					$ss=$consol->shipments;
	foreach ( $ss as $i => $p ) {
		$amt = $p->getChargeByChargecode($chargeCode,true);  // fastway with special charge code
		if(!empty($p->tempChargeweight))  $p->weight=$p->tempChargeweight;
		$items[] = [$p->ref, $p->getDesc() , $p->pkg, $p->weight, $p->cbm, $amt,$p->cnee->postcode];
		$tot += $amt;
	}
	$inv = Invoice::model()->findByPk($inv->id);
	$il = new InvLine;
	$il->inv_id = $inv->id;
	$il->ccode = 'EPA';
	$il->mdata['items'] = $items;
	$il->det = $consol->no;
	$il->fid = $consol->id;
	$il->model = 'ImcoConsol'; // invoice connected with console directly
	$il->amount = round($tot * 1000) / 1000;
	$il->qty = 1;
	$il->save();
	$inv->getTotal();
	$inv->consol_id = $consol->id;

	$inv->mdata['name'] = $owner->name;
	$inv->mdata['address'] = $owner->getAddress();
	$inv->mdata['payterm'] = empty($owner->extra['payterm'])? 'COD' : $owner->extra['payterm'].' days';
	$inv->no = 'FW'.$inv->id;
	$inv->save();

	// update console's fastway cost billing
	$consoleIds = array($consol->id);
//        Consol::model()->updateImportConsoleBilling($consoleIds);

	echo 'Invoice '.$inv->no." issued<br />";     
	
			
		}else{
			echo "consol not valid";
		}
		
   }

	public function aupost2014(){
		$ship_id=$this->prompt('enter the shipment id:');
		$p=Shipment::model()->findByPk($ship_id);
		if(!empty($p)){
			$p->mdata['test_aupost_2014']=1;
		}
		if($p->updateMeta()) echo "done";
	}

	public function APIntGetLabel(){
		$hbn = $this->prompt('HBN: ');
		$rid = strtoupper($this->prompt('Request ID: '));
		$p = ExParcel::model()->find('hbn = :h', [':h' => $hbn]);
		
		$apa = new AusPostAPI('int', true, false); //TMA
		$r3 = $apa->getLabel($rid);
		foreach($r3->labels as $l){
			$p->note = $l->url;
			echo 'wget "'.$l->url.'" -O '.$p->ref.'.pdf'.PHP_EOL;
			$p->save();
		}
		echo "Done\n";
	}

	public function APIntOrder(){
		$hbns = $this->prompt('HBN: ');
		$ref = $this->prompt('Ref: ');
		$ss = [];
		foreach(preg_split('/[\s,;]/', trim($hbns)) as $hbn){
			$p = ExParcel::model()->find('hbn = :h', [':h' => $hbn]);
			if(empty($p->mdata['apsid'])) continue;
			$ss[] = $p->mdata['apsid'];
		}
		$apa = new AusPostAPI('int', true, false); //TMA
		$apa->createOrderFromShipments($ss, $ref);
		echo "Done\n";
	}

	public function updateWmasTask(){
		$job_id=$this->prompt('enter the job id:');
		$wmsTasks=WmsTask::model()->findAll('job_id=:job_id',array(':job_id'=>$job_id));
		if(!empty($wmsTasks)){
			foreach($wmsTasks as $t){
				$t->save();
			}
		}
		echo "done";
	}

	public function sendTracking(){
		
		$hbn=$this->prompt("the hbn of the shipment");
		$shipment= ImParcel::model()->find('hbn=:hbn',array(':hbn'=>$hbn));
		$etoalApi=new EtoalAPI();
		if(!empty($shipment->tracks)){
			foreach($shipment->tracks as $tracking){
				$etoalApi->doRequest($tracking);
			}  
		}
		if(!empty($shipment)){
			
		}else{
			echo 'shipment not found!';
		}
		
	}
	

	function createFastwayLabel(){
		$hbn=$this->prompt("shipment hbn:");
		
		$shipment= ImParcel::model()->find('hbn=:hbn',array(':hbn'=>trim($hbn)));

		if(empty($shipment)){
			echo 'shipment not found!';
			return;
		}
		
		$fw = new FastwayAPI(true);
		$result = $fw->addConsignment($shipment);
		if ( $result ) {
			$cost = $result->TotalCostExGST;

			// for fastway , because fastway server side issue
			// we need shrink the cost to 90.75 %
			$cost = $cost * 0.9075;

			$consignmentID = $result->ConsignmentID;
			$manifestID = $result->ManifestID;
			$refNumber = $result->LabelNumbers[0];
			$shipment->nolog = true;
			$shipment->ref = $refNumber;
			$shipment->update(['ref']);

			// save tranship information for Fastway courier
			$ts = new Tranship;
			$ts->pid = $shipment->id;
			$ts->org_id = 115;  // for Fastway post office
			$ts->man_id = $shipment->man_id;
			$ts->type = 80;  // shipment transfer to a different delivery courier
			$ts->status = 19; // in finally moving status
			$ts->connote = $shipment->ref;
			$ts->time = date('Y-m-d H:i:s');
			$ts->mdata['fw_consigment_id'] = $consignmentID;
			$ts->mdata['fw_manifest_id'] = $manifestID;
			$ts->mdata['fw_label_color'] = $result->LabelColour;
			$ts->mdata['fw_exlabel_count'] = $result->ExcessLabelCount;
			$ts->mdata['fw_dest_code'] = $result->DestinationRFCode; // delivering deport code
			$ts->cost = number_format(round($cost,2),2,'.','');
			$ts->save();
			echo 'done!';
						return;
		}
		echo 'failed';
			
	}
		
		public function getHeldReason(){
			$hbn=$this->prompt("the hbn is :");
			$shipment= ImParcel::model()->find('hbn=:hbn',array(':hbn'=>$hbn));
			if(empty($shipment)){
				echo 'shipment not found!';
				return;
			}
			$shipment->heldReason();
	}
	
		public function updateHeldReason(){
			$shipments= ImParcel::model()->findAll('status=55');
			foreach($shipments as $p){
				echo $p->hbn."\n";
				$p->heldReason();
			}
		}
		
	public function actionClear(){
		$shipments= ImParcel::model()->findAll('bwf>=4 AND status!=55');
		foreach($shipments as $p){
			$p->afterClear();
		}
	}
	
	public function addWmsPack(){
		$org_id=$this->prompt("the org_id:");
		$prodOrg= WmsProdOrg::model()->findAll('org_id=:oid',array(':oid'=>$org_id));
		foreach($prodOrg as $org){
			$model=new WmsProdPack;
			$model->prod_id=$org->prod_id;
			$model->type=10;
			$model->qty=15;
			$model->cbm=0;
			$model->dim= json_encode(array("w"=>"0","h"=>0,"d"=>0));
			$model->weight=6.00;
			if($model->save()){
				echo "1"."\n";
			}else{
				echo "2"."\n";
			}
		}
	}
	
	public function findTheConsol(){
		$consols=ImcoConsol::model()->findAll('id>0');
		
		foreach($consols as $c){
			if(!isset($c->mdata['cnor'])) continue;
			if($c->mdata['cnor']=="21st"){
				echo $c->no;
				echo "\n";
			}
		}
	}
		 public function countJcex(){
		 $invoices=Invoice::model()->findAll('type=10 AND status!=10 AND to_id=838 AND date>="2017-07-01" AND date<="2017-12-13"');
		 $n=0;
		 foreach ($invoices as $inv){
			 if(preg_match('/\-/i', $inv->no)) continue; 
			 foreach ($inv->lines as $il){
				 foreach($il->mdata['items'] as $si=>$sp){
					 $n++;
				 }
			 }
		 }
		  echo $n;
	 }

	public function loadExconsolCost(){
		//AWB#, Channel Weight, Channel Qty, Clearance, Delivery Cost, Duty, Others, ExRate, Invoice ref.

		function addBilling($code, $amt, $c, $ref, $update = true){
			if($update){
				$bl = BillingLine::model()->find('type = 2 AND link_id = :cid AND item_code = :c', [':cid' => $c->id, ':c' => $code]);
			}
			$org = Org::model()->find('code = :code',[':code' => $c->poc]);
			if(empty($org)){
				echo $c->poc." has no supplier org\n";
				return;
			}
			if(empty($bl)){
				$bl = new BillingLine;
				$bl->item_code = $code;
				$bl->link_id = $c->id;
				$bl->org_id = $org->id;
				$bl->type = 2;
				$bl->dpmt = 20;
				$bl->gst = 'EXEMPTEXPENSES';
				$bl->billing_ref = $c->no;
				$bl->billing_cref = $ref;
				$bl->charge_code = '91002';
			}
			$bl->actual_amount = round($amt*100)/100;
			$bl->date = $c->etd;
			return $bl->save();
		}

		$data = $this->_loadXlsData($this->args[1]);
		ini_set('precision', 12);
		$fc = 0;
		unset($data[0]);

		$cmap = [4 => 'channel_clear_cost', 5 => 'channel_delivery_cost', 6 => 'channel_duty_cost', 7 => 'channel_others_cost'];
		$dd = [];
		foreach($data as $r){
			$c = ExcoConsol::model()->find('awb = :a', [':a' => $r[1]]);
			if(empty($c)){
				echo $r[1]." not found\n";
				continue;
			}
			if(strtotime($c->etd) < strtotime('2017-07-01')){
				//echo $r[1].' etd: '.$c->etd."\n";
				continue;
			}

			$exrate = round($r[8] * 100) / 100;

			foreach($cmap as $v=>$k){
				if(!empty($r[$v])){
					if(!isset($dd[$c->id])) $dd[$c->id] = [];
					if(!empty($dd[$c->id][$v])){
						echo $r[1].' '.$k." already load\n";
						if($k == 'channel_duty_cost'){
							$r[$v] += $dd[$c->id][$v];
						}else{
							continue;
						}
					}
					addBilling($k, $r[$v] / $exrate, $c, $r[9]);
					$dd[$c->id][$v] = $r[$v];
				}
			}

			$fc++;
		}
		echo $fc." consols cost loaded\n";
	}

	public function createRtsConsol(){
		$month=empty($this->args[1])?0:$this->args[1];
		if(empty($month)) $month=date('m');
		if((!is_numeric($month)||!($month>0&&$month<=12))){
			 echo 'valid argument';
			 return;
		}

		$consol=ImcoConsol::model()->find('awb=:awb',array(':awb'=>'RTS'.date('Y'.$month)));
		 if(empty($consol)){
			 $consol=new ImcoConsol();
			 $consol->dpt_id=106;
			 $consol->awb='RTS'.date('Y').(empty($month)?date('m'):$month);
			 $consol->pol = 'AUSYD';
			 $consol->pod = 'AUSYD';
			 $consol->eta = date("Y-$month-d");
			 $consol->created = date("Y-$month-d");
			 $consol->status = 60; 
			 $consol->save();
		}
		$invs=Invoice::model()->findAll('(type=37) AND date>=:start and date<=:end',array(":start"=>date("Y-$month-01"),":end"=>date("Y-$month-t"))); //rts fee and rts resend fee
		foreach($invs as $inv){
			foreach($inv->lines as $il){
				foreach($il->mdata['items'] as $si=>$sp){
					 if(!empty($sp[3])) {
						$p=ImParcel::model()->find('ref=:ref',array(':ref'=>$sp[3]));
						if(!empty($p)&&$p->consol_id<=0){
							$p->consol_id=$consol->id;
							$p->update(['consol_id']); 
						}
					}
				}
			}
			$inv->consol_id=$consol->id;
			$inv->update(['consol_id']);
		}
		$consoleIds = array($consol->id);
			ImcoConsol::updateImportConsoleBilling($consoleIds);
			$consol->updateAupostRealCost();
			$consol->updateFastWayRealCost();
			//need to update the real cost;
		echo 'done!';
	}

	public function test(){
		$date = date('Y-m-d 16:00', strtotime($date.' -1 day'));
		$proceses=ConsolProcess::model()->findAll('main_type = 1 AND status in (24,28,32,80) AND to_warehouse>:date AND to_warehouse<DATE_ADD(:date, INTERVAL 1 DAY)', [':date'=>$date]);
		foreach ($proceses as $pro) {
			$consol=$pro->consol;
			echo $consol->no, ': '.intval($consol->totLeftPacks()), PHP_EOL;
		}
	}
}
