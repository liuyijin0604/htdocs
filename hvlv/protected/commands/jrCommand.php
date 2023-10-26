<?php

class jrCommand extends CConsoleCommand
{
	private $args;
	private $tmp;
	private $db;
	
	public function run($args)
	{
		$this->db = Yii::app()->getDb();
		$this->args = $args;
		$this->tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
		if (!empty($args[0]) && method_exists($this, $args[0])) {
			$this->{$args[0]}();
		}
	}
		
	public function _loadXlsData($f)
	{
		if (!is_file($f)) {
			die($f." not exist\n");
		}
		return oExcel::getAllData($f, false, true);
	}
	 
	
	public function countJcex()
	{
		$invoices=Invoice::model()->findAll('type=10 AND status!=10 AND to_id=838 AND date>="2017-07-01" AND date<="2017-12-13"');
		$n=0;
		foreach ($invoices as $inv) {
			if (preg_match('/\-/i', $inv->no)) {
				continue;
			}
			foreach ($inv->lines as $il) {
			}
		}
		echo $n;
	}
	
	public function createRtsConsol()
	{
		//         $month=empty($this->args[1])?0:$this->args[1];
		//         if(empty($month)) $month=date('m');
		//         if((!is_numeric($month)||!($month>0&&$month<=12))){
		//             echo 'valid argument';
		//             return;
		//         }
		$month=trim($this->prompt('month:'));
		$consol=ImcoConsol::model()->find('awb=:awb', [':awb'=>'RTS'.date('Y'.$month)]);
		//          if(empty($consol)){
		//             $consol=new ImcoConsol();
		//             $consol->dpt_id=106;
		//             $consol->awb='RTS'.date('Y').(empty($month)?date('m'):$month);
		//             $consol->pol = 'AUSYD';
		//             $consol->pod = 'AUSYD';
		//             $consol->eta = date("Y-$month-d");
		//             $consol->created = date("Y-$month-d");
		//             $consol->status = 60;
		////             $consol->save();
		//         }
		//         $invs=Invoice::model()->findAll('type=37 AND consol_id=0 AND date>=:start and date<=:end',array(":start"=>date("Y-$month-01"),":end"=>date("Y-$month-t"))); //rts fee and rts resend fee
		//         foreach($invs as $inv){
		//             foreach($inv->lines as $il){
		//                foreach($il->mdata['items'] as $si=>$sp){
		//                     if(!empty($sp[3])) {
		//                         $p=ImParcel::model()->find('ref=:ref',array(':ref'=>$sp[3]));
		//                         if(!empty($p)&&$p->consol_id<=0){
		//                         $p->consol_id=$consol->id;
		//                         $p->update(['consol_id']);
		//                         }
		//                     }
		//                 }
//
		//             }
		//             $inv->consol_id=$consol->id;
		//             $inv->update(['consol_id']);
		//         }
		//        $consoleIds = array($consol->id);
		//          ImcoConsol::updateImportConsoleBilling($consoleIds);
		//          $consol->updateAupostRealCost();
		//          $consol->updateFastWayRealCost();
//
		$transaction=Yii::app()->db->beginTransaction();
		try {
			$invoices = Invoice::model()->findAll('date>=:start and date<=:end AND type=:type AND consol_id=0', [':type' => Invoice::INVOICE_TYPE_RTS_FEE,':start'=>date("Y-$month-01"),':end'=>date("Y-$month-t")]);
			foreach ($invoices as $inv) {
				$inv->consol_id=$consol->id;
				$inv->update('consol_id');
			}
			$transaction->commit();
		} catch (Exception $ex) {
			$transaction->rollback();
		}
		//need to update the real cost;
		echo 'done!';
	}
		
		
	public function createFastwayLabel()
	{
		$hbn=$this->prompt("shipment hbn:");
			
		$shipment= ImParcel::model()->find('hbn=:hbn', [':hbn'=>trim($hbn)]);

		if (empty($shipment)) {
			echo 'shipment not found!';
			return;
		}
		 
		$fw = new FastwayAPI(true);
		$result = $fw->addConsignment($shipment);
		if ($result) {
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
			$ts->cost = number_format(round($cost, 2), 2, '.', '');
			$ts->save();
			echo 'done!';
			return;
		}
		echo 'failed';
	}
		
	private function createRTSScanInvoice(&$parcel)
	{

		// check to see if RTS scan invoice with pending status existing or not
		// if existing , we just update it
		// otherwise create a new one with pending status
		// Normally only account change status from pending to posted
		// check to see if related pending invoice existing
		$invoice = Invoice::model()->find('to_id = :tid AND type = :itype AND status = 1', [':tid' => $parcel->agent_id,':itype' => Invoice::INVOICE_TYPE_RTS_FEE]);
		if (empty($invoice)) {
			// not existing yet , just create a new one
			$invoice = new Invoice();
			$invoice->type = Invoice::INVOICE_TYPE_RTS_FEE; // for RTS scan fee invoice
			$invoice->to_id = $parcel->agent_id;
			$invoice->man_id = 0; // in case manifest id means nothing
			$invoice->dpt_id = $parcel->ddpt_id;
			$invoice->dpmt= Invoice::DPMT_IMPORT;

			// if department not set , we set as Sydney warehouse
			if (empty($invoice->dpt_id)) {
				$invoice->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
			}
			$invoice->status = Invoice::INVOICE_STATUS_PENDING; // set as pending status
			$invoice->date = date('Y-m-d'); // here just dummy date , once accounting change to posted , should create new date for the invoice

			// get invoice currency
			$invoiceCurrency = 1; // default as AUD
			$orgRate = OrgRate::model()->find('org_id = :oid AND type = 40', [':oid' => $parcel->agent_id]);
			if (!empty($orgRate)) {
				$invoiceCurrency =  $orgRate['currency'];
			}
			$invoice->currency = $invoiceCurrency;

			// set invoice name , address and pay terms information
			$owner = $parcel->agent;
			$invoice->mdata['name'] = $owner->name;
			$invoice->mdata['address'] = $owner->getAddress();
		}
		 
		$couries='aus';  //default auspost
		if (isset($parcel->trans) && !empty($parcel->trans) && $parcel->trans[0]->org_id == Org::ORGID_COURIER_FASTWAY) {
			$couries='fastway';
		} elseif (isset($parcel->trans) && !empty($parcel->trans) && $parcel->trans[0]->org_id == Org::ORGID_COURIER_STARTRACK) {
			$couries='startrack';
		}
		// get RTS fee
		$rtsFee = $this->getRtsRate($parcel->agent_id, $couries, $parcel);

		// check to see if we should including GST (default 10%)
		$gst = 0;
		if (isset($parcel->agent->extra['incl_gst']) && $parcel->agent->extra['incl_gst'] == 1) {
			$gst = round($rtsFee * 10 / 100, 2);
			$rtsFee += $gst;
		}

		$invoice->total += $rtsFee;
		if ($gst > 0) {
			$invoice->gst += $gst;
		}
		$invoice->mdata['payterm'] = empty($owner->extra['payterm'])? '2 days' : $owner->extra['payterm'].' days';
		// here just dummy date , once accounting change to posted , should create new date for the invoice
		$invoice->due = Invoice::calcDue($invoice->date, $invoice->mdata['payterm']);
		
		//if some couries resend not charge, we don't need create  invoice
		if ($invoice->total>0) {
			$invoice->save();
			// create related invoice line and attached it to the invoice
			$il = new InvLine;
			$il->inv_id = $invoice->id;
			$il->amount = $rtsFee;
			if ($gst > 0) {
				$il->gst = $gst;
			}

			// save receiving date,shipment ref No. and customer ref No.
			$il->mdata['items'] = [ [  empty($parcel->cref) ? $parcel->hbn : $parcel->cref , $parcel->ref , date('Y-m-d') , 'Returned to Sender Receiving Fee', $il->amount] ];
			$il->model = 'ImParcel';
			$il->fid = $parcel->id;
			if ($il->amount>0) {
				$il->save();
			}
		}
	}
	private function getRtsRate($orgId, $courier='AUS', $p=null)
	{

		// get organizaton price rate
		$orgRate = OrgRate::model()->find('org_id = :oid AND type = 40', [':oid' => $orgId]);
		if (empty($orgRate)) {
			return 0;
		}

		// get org rate details
		$rateInfo = json_decode($orgRate['meta']);
		$code = 'AU'; // RTS fee belongs to Australia local
		$rtsFee = 0;
		if ($courier=='fastway') {
			if (isset($rateInfo->$code->rts_rate_fast)) {
				$rtsFee = floatval($rateInfo->$code->rts_rate_fast);
			}
		} elseif ($courier=='startrack') {
			if (isset($rateInfo->$code->rts_rate_star)) {
				$rtsFee = floatval($rateInfo->$code->rts_rate_star)*$p->getRtsEstiInvoice()/100;
			}
		} else {
			if (isset($rateInfo->$code->rts_rate)) {
				$rtsFee = floatval($rateInfo->$code->rts_rate);
			}
		}
		 
		return $rtsFee;
	}
	public function createRtsInvoiceManual()
	{
		$ref=$this->prompt('ref:');
		$p= ImParcel::model()->find('ref=:ref', [':ref'=>$ref]);
		if (!empty($p)) {
			$this->createRTSScanInvoice($p);
			echo 'success '. $p->ref;
		}
	}
		
	public function getHeldReason()
	{
		$hbn=$this->prompt("the hbn is :");
		$shipment= ImParcel::model()->find('hbn=:hbn', [':hbn'=>$hbn]);
		if (empty($shipment)) {
			echo 'shipment not found!';
			return;
		}
		$shipment->heldReason();
	}
		 
	public function updateHeldReason()
	{
		$shipments= ImParcel::model()->findAll('status=55');
		foreach ($shipments as $p) {
			echo $p->hbn."\n";
			$p->heldReason();
		}
	}
			
	public function actionClear()
	{
		$shipments= ImParcel::model()->findAll('(bwf&4>0 OR bwf&8>0 OR bwf&16>0 OR bwf&32>0) AND status!=55');
		foreach ($shipments as $p) {
			$p->afterClear();
		}
	}
	/*
	 * get the fastway Zone Map
	 *
	 */
	 
	 
	public function fastwayZoneMap()
	{
		ini_set('memory_limit', '1024M');
		$file= $this->tmp."fastway.xlsx";
		$xls= new oExcel;
		$xls->supported($file);
		$xls->load($file);
		$data=$xls->getAll();
		unset($xls);
		unset($data[1]);
		$fastway=[];
		foreach ($data as $i=>$d) {
			if (empty($d[3])) {
				continue;
			}
			//                  if(!empty($d[9]))                      continue;
			$fastway[$d[3]]=$d[9];
		}
		ksort($fastway);
		//              var_dump($fastway);
		//              echo '<br/>';
		//              echo '<br/>';
		$i=0;
		$range=[];
		$a=[];
		$temp= key($fastway);
		$tempY='N';
		$a[0]=$temp;
		while ($current= current($fastway)) {
			$ckey= key($fastway);
			$next= next($fastway);
			$next_key=key($fastway);
			if ($current!=$next||$next_key-$ckey!=1) {
				$a[1]=$ckey;
				$a[2]=$current;
				$range[]=$a;
				$a[0]=$next_key;
			}
		}
		$non=[];
		$syd=[];
		foreach ($range as $rg) {
			if ($rg[2]=='N') {
				if ($rg[0]==$rg[1]) {
					$non[]=$rg[0];
				} else {
					$non[]=$rg[0].'-'.$rg[1];
				}
			} else {
				if ($rg[0]==$rg[1]) {
					$syd[]=$rg[0];
				} else {
					$syd[]=$rg[0].'-'.$rg[1];
				}
			}
		}
		$non_string='';
		foreach ($non as $n) {
			$non_string.=$n.',';
		}
		$syd_string='';
		foreach ($syd as $n) {
			$syd_string.=$n.',';
		}
		//          var_dump($non);
//
		//          echo '<br/><br/><br/>';
		//              var_dump($syd);
			
			
		$this->upfw1($non_string, $syd_string);
	}
		
	public function upfw1($non, $syd)
	{
		$output=new oExcel;
		$output->setCell('A1', 'code');
		$output->setCell('B1', 'name');
		$output->setCell('C1', 'postcodelist');
			
		$output->setCell('A2', 'SYD');
		$output->setCell('B2', 'Sydney Area');
		$output->setCell('C2', $syd);
				
		$output->setCell('A3', 'NON');
		$output->setCell('B3', 'Non-Sydney Area');
		$output->setCell('C3', $non);
				
		$output->output('fastway_zone_map.xlsx', false, false);
	}

	public function addWmsPack()
	{
		$org_id=$this->prompt("the org_id:");
		$prodOrg= WmsProdOrg::model()->findAll('org_id=:oid', [':oid'=>$org_id]);
		foreach ($prodOrg as $org) {
			$model=new WmsProdPack;
			$model->prod_id=$org->prod_id;
			$model->type=10;
			$model->qty=15;
			$model->cbm=0;
			$model->dim= json_encode(["w"=>"0","h"=>0,"d"=>0]);
			$model->weight=6.00;
			if ($model->save()) {
				echo "1"."\n";
			} else {
				echo "2"."\n";
			}
		}
	}
	public function eqtyxls()
	{
		$start_date=$this->prompt('start date:');
		$end_date=$this->prompt('end date:');
		$rs=ReconciliationLine::model()->with('parent')->findAll(
			'parent.client_type=1 AND parent.invoice_date > :start_date AND  parent.invoice_date < :end_date',
				[":start_date"=>$start_date,":end_date"=>$end_date]
		 );
		$weight=0;
		$cbm=0;
		if (!empty($rs)) {
			foreach ($rs as $r) {
				$p = ImParcel::model()->find('ref = :ref', [':ref' => $r->shipment_no]);
				if (empty($p->cbm)) {
					continue;
				}
				$weight+=$r->weight;
				$cbm+=$p->cbm;
			}
		}
		$c=$cbm*250/$weight;
		echo "the factor is :".$c;
	}
	public function bfeInvoice()
	{
		$consol_id=$this->prompt('consol_id:');
		$consol_no=$this->prompt('consol_no:');
		$consol= Consol::model()->find('id=:id AND no=:no', [':id'=>$consol_id,':no'=>$consol_no]);
		$con=$consol;
		$owner = Org::model()->findByPk(1206);
		$ownerId=$owner->id;
		if (!empty($consol)) {
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
			foreach ($ss as $i => $p) {
				$amt = $p->getChargeByChargecode($chargeCode, true);  // fastway with special charge code
				if (!empty($p->tempChargeweight)) {
					$p->weight=$p->tempChargeweight;
				}
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
			$consoleIds = [$consol->id];
			//        Consol::model()->updateImportConsoleBilling($consoleIds);

			echo 'Invoice '.$inv->no." issued<br />";
		} else {
			echo "consol not valid";
		}
	}
	public function aupost2017()
	{
		$ship_id=$this->prompt('enter the shipment id:');
		$p=Shipment::model()->findByPk($ship_id);
		if (!empty($p)) {
			$p->mdata['test_aupost_2017']=1;
		}
		if ($p->updateMeta());
		echo "done";
	}
	public function updateWmasTask()
	{
		$job_id=$this->prompt('enter the job id:');
		$wmsTasks=WmsTask::model()->findAll('job_id=:job_id', [':job_id'=>$job_id]);
		if (!empty($wmsTasks)) {
			foreach ($wmsTasks as $t) {
				$t->save();
			}
		}
		echo "done";
	}
	public function sendTracking()
	{ //etal api
		 
		$hbn=$this->prompt("the hbn of the shipment");
		$shipment= ImParcel::model()->find('hbn=:hbn', [':hbn'=>$hbn]);
		$etoalApi=new EtoalAPI();
		if (!empty($shipment->tracks)) {
			foreach ($shipment->tracks as $tracking) {
				$etoalApi->doRequest($tracking);
			}
		}
		if (!empty($shipment)) {
		} else {
			echo 'shipment not found!';
		}
	}
		
	public function uploadCustomRate()
	{
		$file= $this->tmp."part3.xlsx";
		$xls=new oExcel();
		$xls->load($file);
		$data=$xls->getAll();
		$hs='';
		foreach ($data as $i=>$d) {
			if (empty($d[2])) {
				continue;
			}
			if (!empty($d[2])) {
				$hs=$d[2];
			}
			$start=empty($d[3])?null:date('Y-m-d', strtotime(preg_replace('/from\s/i', '', $d[3])));
			$rate=null;  //preg_replace('/free/i','0',$d[4]);
			//                $rate=$rate*100;
			if (empty($data[$i+1][2])&&!empty($data[$i+1][3])) {
				$end=date('Y-m-d', strtotime(preg_replace('/from\s/i', '', $data[$i+1][3])));
			} else {
				$end=null;
			}
			echo 'hs:'.$hs."\t";
			echo  'rate:'.$rate."\t";
			echo 'start:'.$start."\t";
			echo 'end:'.$end."\n";
			$model=new CustomRate();
			$model->from_date=$start;
			$model->to_date=$end;
			$model->setAttributes([
				'hs'=>$hs,
				'rate'=>$rate,
				'charge_type'=>0,
			]);

			$model->save();
		}
	}
		
	public function getCustomRate()
	{
		$hs=$this->prompt('hs code:');
		//          $rate= CustomRate::getRate($hs);
		//          print_r($rate);
			
		var_dump(CustomRate::suggestHs($hs));
	}
		
		
		
	public function aupostManifest()
	{
		$hbn=$this->prompt('hbn:');
		$p= ImParcel::model()->find('hbn=:hbn', [':hbn'=>$hbn]);
		$ss=[];
		if (!empty($p)) {
			$ss[]=$p;
		}
		if (!empty($ss)) {
			$apa = new AusPostAPI('syd'); //TMA
			$r = $apa->createOrderIncludingShipments($ss, $ss[0]->id, AusPostAPI::CHARGE_CODE_POD);
			if (!empty($r->order)) {
				$oid = $r->order->order_id;
				foreach ($ss as $i => $s) {
					$ts = new Tranship;
					$ts->pid = $s->id;
					$ts->org_id = 101;  // for Australia post office
					$ts->man_id = 0;
					$ts->type = 80;  // shipment transfer to a different delivery courier
						$ts->status = 19; // in finally moving status
						$ts->connote = $s->ref;
					$ts->time = date('Y-m-d H:i:s');
					$ts->mdata['oid'] = $oid;
					$ts->mdata['sid'] = $r->order->shipments[$i]->shipment_id;
					$ts->save();
				}
				echo 'sucessfully';
				echo "\n";
			} else {
				echo 'failed';
			}
		}
	}
		
		
	public function getSumStartrack()
	{
		$rs=ImParcel::model()->findAll('created>="2017-01-01" AND (status=70 or status=90) AND agent_id=1002 AND ref like :ref ', [':ref'=>'7RFZ%']);
		$file= $this->tmp."part3.xlsx";
		$xls=new oExcel();
		$xls->setCell('A1', 'ref');
		$xls->setCell('B1', 'date');
		$xls->setCell('C1', 'cost');
		$xls->setCell('D1', 'cost5000');
		$xls->setCell('E1', 'cost6000');
		$xls->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
		foreach ($rs as $i=>$r) {
			$dim = floatval($r->cbm);
			if ($dim <= 0.0) {
				if (!isset($r->mdata['dim'])) {
				} else {
					$cbm = floatval($r->mdata['dim']['w']) * floatval($r->mdata['dim']['h']) * floatval($r->mdata['dim']['d']);
					$r->cbm= number_format($cbm/1000000, 6, '.', '');
				}
			}
			$cost=$r->getChargeByChargecode();
			$r->weight=max($r->weight, $r->cbm*200*$r->pkg);
			$cost5000=$r->getChargeByChargecode('', true);
			$r->weight=max($r->weight, $r->cbm*167*$r->pkg);
			$cost6000=$r->getChargeByChargecode('', true);
			$xls->setCell('A' . ($i + 2), $r->ref);
			$xls->setCell('B' . ($i + 2), $r->created);
			$xls->setCell('C' . ($i + 2), $cost);
			$xls->setCell('D' . ($i + 2), $cost5000);
			$xls->setCell('E' . ($i + 2), $cost6000);
		}
		$xls->output($file, null, false);
	}
	public function getGatePass()
	{
		$start=$this->prompt('start:');
		$end=$this->prompt('end:');
		$manifest= GatePass::model()->findAll('created<:end and created>=:start', [':start'=>$start,':end'=>$end]);

		foreach ($manifest as $m) {
			foreach ($m->lines as $r) {
				$p=$r->mm();
				if ($p->agent_id==1222 and preg_match('/7RFZ/i', $p->ref)) {
					$a=new DateTime(date('Y-m-d', strtotime($m->created)));
					$b=new DateTime($p->consol->eta);
					$t=$a->diff($b)->format("%a");
					if ($t>14) {
						echo $p->hbn."\t".$p->ref."\t".$m->created."\t".($t-14)."\t".$p->weight."\t".(0.2*$p->weight*($t-14))."\n";
					}
				}
			}
		}
	}
		 
		 
	/* this is used to send storage invoice to client daily
	 *
	 */
	public function sendStorageInvoiceDaily()
	{
		$tempDirectory = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
		if (!file_exists($tempDirectory)) {
			mkdir($tempDirectory);
		}
		$invoices = Invoice::model()->findAll(
				'type = :type AND status = :status',
			[':type' => Invoice::INVOICE_TYPE_STORAGE_FEE, ':status' => Invoice::INVOICE_STATUS_PENDING]
			);
		foreach ($invoices as $invoice) {
			$fileName=$tempDirectory.DIRECTORY_SEPARATOR.'Invoice_'.$invoice->no.'.pdf';
			oPDF::renderPDF('invoice', ['inv'=>$invoice], 2, $fileName);
			$emailLog = new Emailog();
			$emailLog->type = Emailog::INVOICE;
			$emailLog->fid = $invoice->id;
			$emailLog->dt = date('Y-m-d H:i:s');
			$emailLog->to_id = $invoice->to_id;
			$emailLog->status = 10;
			$emailLog->prepTemplate();
			$emailLog->subject = $emailLog->tpl->subject;
			$emailLog->body = $emailLog->tpl->getContent();
			if ($emailLog->storageSend($fileName)) {
				$invoice->status = Invoice::INVOICE_STATUS_POSTED;
				$invoice->posted = date('Y-m-d');
				$invoice->update('status');
				echo $invoice->no;
			}
		}
		AppHelper::unlinkRecursive($tempDirectory);
	}
		 
		 
	public function getTime()
	{
		$no=$this->prompt('consol no:');
		$con= ImcoConsol::model()->find('no=:no', [':no'=>$no]);
		$file= $this->tmp."time.xlsx";
		$xls=new oExcel();
		$xls->setCell('A1', 'long ref');
		$xls->setCell('B1', 'date');
		$xls->setCell('C1', 'ref');
		$xls->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
		$i=0;
		foreach ($con->shipments as $p) {
			if ($p->agent_id!=1002) {
				continue;
			}
			if (empty($p->scan_data[50])) {
				continue;
			}
			foreach ($p->scan_data[50] as $key=> $r) {
				$xls->setCell('A' . ($i + 2), $key);
				$xls->setCell('B' . ($i + 2), $r);
				$xls->setCell('C' . ($i + 2), $p->ref);
				$i++;
			}
		}
		$xls->output($file, null, false);
	}
		 
		 
	public function createTranship()
	{
		$cid=$this->prompt('consol_id');
		$consol= ImcoConsol::model()->findByPk($cid);
		foreach ($consol->shipments as $p) {
			if (!preg_match('/AMQ\d{7}/i', $p->ref)) {
				continue;
			}
			$ts = new Tranship;
			$ts->pid = $p->id;
			$ts->org_id = 101;  // for Australia post office
			$ts->man_id = 0;
			$ts->type = 80;  // shipment transfer to a different delivery courier
					$ts->status = 19; // in finally moving status
					$ts->connote = $p->ref;
			$ts->time = date('Y-m-d H:i:s', strtotime(date('2017-12-27 10:39:44')));

			// currently we save cost with a single field
			// $ts->mdata['cost'] = $r->order->shipments[$i]->shipment_summary->total_cost;
			$costValue = 1.0;
			$ts->cost = round($costValue, 2);
			$ts->save();
		}
		echo 'done!';
	}
		 
		 
	public function changeChargecode()
	{
		$cid=$this->prompt('consol_id');
		$chargecode=$this->prompt('chargecode');
		$consol= ImcoConsol::model()->findByPk($cid);
		foreach ($consol->shipments as $p) {
			$p->mdata['chargecode']=$chargecode;
			$p->updateMeta();
		}
	}
		 
		 
	public function createShipment()
	{
		$hbn=$this->prompt('hbn:');
		$p=ImParcel::model()->find('hbn=:hbn', [':hbn'=>$hbn]);
		if (!empty($p)) {
			$apa = new AusPostAPI('syd'); //TMA
			$apa->createShipments($p);
		}
	}
	public function createLabel()
	{
		$id=$this->prompt('id:');
		$ids=[$id];
		$apa = new AusPostAPI('syd'); //TMA
		$apa->createLabels($ids);
	}
		
	public function getAupostReport()
	{
		$rs=ReconciliationLine::model()->findAll('shipment_no like "AMQ%"');
		$xsl=new oExcel();
		$file=$this->tmp.'aupost_report.xlsx';
		$shipments=[];
		foreach ($rs as $r) {
			$p=ImParcel::model()->find('ref=:ref', [':ref'=>$r->shipment_no]);
			$aweight=$r->weight;
			$acharge=$r->value;
			$weight=$p->weight;
			$chargewt=$p->chargeWeight();
			
			if ($p->agent_id==1206) {
				$chargevalue = $p->getChargeByChargecode(5813, true);
			} else {
				$chargevalue=$p->getRtsEstiInvoice();
			}
			$shipments[$p->agent_id][]=[$p->ref,$aweight,$acharge,$weight,$chargewt,$chargevalue];
		}
		$n=1;
		foreach ($shipments as $agent_id=>$ss) {
			$org=Org::model()->findByPk($agent_id);
			$orgName=empty($org)?$agent_id:$org->shortName(3);
			$xsl->createSheet($orgName);
			$xsl->goSheet($n++);
			$i=1;
			$xsl->addRow($i++, ['REf','Aupost Charge weight','Aupost Charge','shipment real weight','our charge weight','our charge value']);
			foreach ($ss as $s) {
				$xsl->addRow($i++, $s);
			}
		}
		
		$xsl->output($file, null, false);
	}
	 
	public function updateRe()
	{
		$rs=ReconciliationLine::model()->findAll('shipment_no not like "AMQ%" and my_charge=0');
		foreach ($rs as $r) {
			$p=ImParcel::model()->find('ref=:ref', [':ref'=>$r->shipment_no]);
			$chargevalue=0;
			if (!empty($p)) {
				if ($p->agent_id==1206) {
					$chargcode= preg_match('/AMQ\d{7}/i', $p->ref)?5813:8271;
					try {
						$chargevalue = $p->getChargeByChargecode($chargecode, true);   //5813    8271-->fastway
					} catch (Exception $e) {
						echo $e->errorMessage();
					}
				} else {
					try {
						$chargevalue=$p->getRtsEstiInvoice();
					} catch (Exception $e) {
						echo $e->errorMessage();
					}
				}
			}
			if ($chargevalue==0) {
				continue;
			}
			$r->my_charge=$chargevalue;
			$r->update(['my_charge']);
		}
	}


	public function uploadParcelabn()
	{
		$fileName=$this->prompt('file name:');
		$file=$this->tmp.'forau'.DIRECTORY_SEPARATOR.$fileName.'.xlsx';
		$xls=new oExcel();
		$xls->supported($file);
		$xls->load($file);
		$data=$xls->getAll();
		unset($data[1]);
		foreach ($data as $d) {
			if (empty($d[2])||empty($d[4])) {
				continue;
			}
			$p=ImParcel::model()->find('ref=:ref', [':ref'=>$d[2]]);
			if (!empty($p)) {
				$p->mdata['vendor_id']= preg_replace("/[^\d]/i", "", trim($d[4]));
				if ($p->save()) {
				} else {
					var_dump($p->cnor->getErrors());
				}
				echo $p->ref."\n";
			}
		}
	}

	//   public function genReport(){
	//       $rs=BillingLine::model()->findAll('org_id=1068 AND charge_code=91032 AND `desc` like "%ECN%"');
	//       foreach($rs as $r){
	//           preg_match('/ECN\d{11}/', $r->desc,$match);
	//           $p=ImParcel::model()->find('hbn=:hbn',array(':hbn'=>$match[0]));
	//           if(!empty($p)){
	//               if(($p->bwf&2)>0){
	//                   echo $p->hbn."\t weight:".$p->weight."\t amount".$r->actual_amount."\n";
	//               }
//
	//           }
//
	//       }
//
	//   }
//
	public function updateItem()
	{
		$ref=$this->prompt('ref:');
		$p=ImParcel::model()->find('ref=:ref', [':ref'=>$ref]);
		if (!empty($p)) {
			$n= sizeof($p->eitems['v']);
			for ($i=0;$i<$n;$i++) {
				$p->eitems['v'][$i]=round($p->eitems['v'][$i]/$p->eitems['q'][$i], 2);
			}
			$p->save();
		}
	}
	 
	 
	 
	public function getReport()
	{
		$rs=ReconciliationLine::model()->findAll('shipment_no like "AMQ%" AND cdeadwt>0');
		$report=[];
		if (!empty($rs)) {
			foreach ($rs as $r) {
				$p=ImParcel::model()->find('ref=:ref', [':ref'=>$r->shipment_no]);
				if (!empty($p)) {
					$cost=$this->getCourierCostByShipment($p, $r->cdeadwt);
					$p->weight=$r->cdeadwt;
					$cheapCost=$this->getCheapCost($p);
					if ($cheapCost==0) {
						$theNo=0;
					} else {
						$theNo=1;
					}
					if (key_exists($p->agent_id, $report)) {
						$report[$p->agent_id]['no']+=1;
						$report[$p->agent_id]['cct']+=$r->my_value;
						$report[$p->agent_id]['cc']+=$r->value;
						$report[$p->agent_id]['pcac']+=$r->my_charge;
						$report[$p->agent_id]['cdwt']+=$r->cdeadwt;
						$report[$p->agent_id]['custdwt']+=$p->weight;
						$report[$p->agent_id]['ccbwt']+=$r->weight;
						$report[$p->agent_id]['custcbwt']+=($p->agent_id==1206?$p->weight:$p->chargeWeight());
						$report[$p->agent_id]['cc2018']+=$cost;
						$report[$p->agent_id]['cpcost']+=$cheapCost;
						$report[$p->agent_id]['theNo']+=$theNo;
					} else {
						$report[$p->agent_id]=['no'=>1,'cct'=>$this->getCouiercost($p),'cc'=>$r->value,'pcac'=>$r->my_charge,'cdwt'=>$r->cdeadwt,'custdwt'=>$p->weight,'ccbwt'=>$r->weight,
							'custcbwt'=>$p->agent_id==1206?$p->weight:$p->chargeWeight(),'cc2018'=>$cost,'cpcost'=>$cheapCost,'theNO'=>$theNo];
					}
				}
			}
		}
		print_r($report);
	}
	 
	 
//
	//   public function getCouiercost(&$p){
	//           if($p->agent_id==1206){
	//              $chargcode= preg_match('/AMQ\d{7}/i',$p->ref)?5813:8271;
	//              $chargevalue = $p->getChargeByChargecode($chargecode,true);   //5813    8271-->fastway
	//          } else {
	//              $chargevalue=$p->getRtsEstiInvoice();
	//          }
	//         return $chargevalue;
	//   }
	 
	private function getCourierCostByShipment(&$shipment, $charge_weight=0, $courierId = Org::ORGID_COURIER_AUPOST, $c=1, $b=101)
	{

		// make it fast we hardcode here currently
		$orgRates = [
			Org::ORGID_COURIER_FASTWAY => 52,  // you can get this id from Org -> Rates Tab -> Flex Rate by Zone hypelink will including this id
			Org::ORGID_COURIER_AUPOST => $b,
			//                        102=>75,// live 101 test 75   44
		];
		$price = 0;
		if (!empty($charge_weight)) {
			$weight=$charge_weight;
		} else {
			$weight = $shipment->weight;
		}
			
		// base on postcode to get related charge code//zonid 1 eparcel   3:eparcel 2018
		$zoneMap = ZoneMap::model()->find(
				'org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code',
				[':oid' =>$courierId, ':zoneid' => 3, ':code' => $shipment->cnee->postcode]
			);

		// if not found we set as NSW country
		$chargeCode = 'N1';
		if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
			$chargeCode = $zoneMap['z1'];
		}

		// base charge code to get zone rate
		$zrs = ZoneRate::model()->findAll(
				"zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ",
				[':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRates[$courierId]]
			);
		if (!empty($zrs)) {
			// get maximum one
			foreach ($zrs as $zr) {
				$temp = $zr['base'] + $zr['item'];
				if ($zr['nkg'] > 0) {
					$wl = $weight - ($zr['base'] > 0 ? $zr['nkg'] : 0);
					$temp += ceil($wl / $zr['nkg']) * $zr['perkg']*$c;
				} else {
					$temp += $weight * $zr['perkg']*$c;
				}
				if ($zr['minimum'] > 0 && $temp < $zr['minimum']) {
					$temp = $zr['minimum'];
				}
				$price = max($price, $temp);
			}
		}
		
		return $price;
	}
		
	public function getCheapCost($shipment)
	{
		$couriers = [101,52];
		$selectedOrgRates = [];

		if (!empty($couriers)) {
			// fastway not support po box,
			// so if po box we need to remove the fastway couriers.
			if (isset($shipment->cnee->address)&&Addr::checkIsPoBox($shipment->cnee->address)) {
				if ((in_array(52, $couriers))) {
					$key= array_search(52, $couriers);
					unset($couriers[$key]);
				}
			}
							

			$left_couriers=$couriers;
							
			while (!empty($left_couriers)) {
				$selectedOrgRates=[];
			
				// set cheap price courier
				foreach ($left_couriers as $courier) {
					// check which courier is cheap
					// step 1
					// get Org ID base on OrgRate ID
					$orgRate = OrgRate::model()->findByPk($courier);
					if (empty($orgRate)) {
						continue;
					}
					$orgId = $orgRate->org_id;

					// check courier's minimum conditions
					// check max weight  and max dimension
					$org = Org::model()->findByPk($orgId);
					if (empty($org)) {
						continue;
					}
					$maxWeight = 0; // by KG
					if (isset($org->extra['maxwt'])) {
						$maxWeight = $org->extra['maxwt'];
					}
					$maxDim = 0; // by CM for any maximum of D, H , W
					if (isset($org->extra['maxdim'])) {
						$maxDim = $org->extra['maxdim'];
					}
					$units = $shipment->pkg <= 0 ? 1 : $shipment->pkg;
										 
					
					$chargeWeight=$shipment->weight;
										
					if ($maxWeight > 0 && $chargeWeight / $units > $maxWeight) {
						// over the courier's max weight
						continue;
					}
								
					if ($maxDim > 0) {
						$w = 0;
						if (isset($shipment->mdata['dim']['w'])) {
							$w =  $shipment->mdata['dim']['w'];
						}
						$h = 0;
						if (isset($shipment->mdata['dim']['h'])) {
							$h =  $shipment->mdata['dim']['h'];
						}
						$d = 0;
						if (isset($shipment->mdata['dim']['d'])) {
							$d =  $shipment->mdata['dim']['d'];
						}
						$sMaxDim = max($w, $h, $d);
						if ($sMaxDim > $maxDim) {
							// over the courier's max dimension
							continue;
						}
					}
					$selectedOrgRates[] = $orgRate;
				}
				// may have no couries as all the courier don't  meet above requirement,we need figure out what the problems?
					 
				// check courier minimum conditions

				// finally set the cheapest courier
				$imcoConsole = new ImcoConsol();
				$minCost = 99999; // in order to get minimum one
				$cheapOrgRate = null;
				$getCheapOrgRateError = '';
				foreach ($selectedOrgRates as $orgrate) {
					if (!ChooseShipment::courierCanDelivery($orgrate->org_id, $shipment, false)) {
						continue;
					}

					// get cost based on org rate
					$singleWeight = $chargeWeight / ($shipment->pkg > 0 ? $shipment->pkg : 1);
					$cost = $this->getCourierCostPrice($orgrate, $shipment->cnee->postcode, $singleWeight);
					$cost =$cost*$shipment->pkg;
					if ($cost > 0 &&  $cost < $minCost) {
						$minCost = $cost;
						$cheapOrgRate = $orgrate;
					}
				}
				if ($minCost==99999) {
					$minCost=0;
				}
				return $minCost;
			}
		}
	}
	public function getCourierCostPrice(&$orgRate, $postcode, $weight)
	{
		$price = 0;

		// base on postcode to get related charge code
		$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>$orgRate->org_id ,':zoneid' => $orgRate->zone_id,':code' => $postcode]);

		// if not found we set as NSW country
		$chargeCode = 'N1';
		if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
			$chargeCode = $zoneMap['z1'];
		}

		// base charge code to get zone rate
		$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRate->id]);
		$c=1;
		if ($orgRate->org_id==101) {
			$c=1.45;
		}
		if (!empty($zrs)) {

			// get maximum one
			foreach ($zrs as $zr) {
				$temp = $zr['base'] + $zr['item'];
				if ($zr['nkg'] > 0) {
					$wl = $weight- ($zr['base'] > 0 ? $zr['nkg'] : 0);
					$temp += ceil($wl / $zr['nkg']) * $zr['perkg']*$c;
				} else {
					$temp +=  $weight * $zr['perkg']*$c;
				}
				if ($zr['minimum'] > 0 && $temp < $zr['minimum']) {
					$temp = $zr['minimum'];
				}
				$price = max($price, $temp);
			}
		}

		return $price;
	}
	 
	public function withdrawACR()
	{
		$cn = ImcoConsol::model()->findByPk($this->prompt('Consol ID: '));
		if (empty($cn)) {
			die("Consol not found\n");
		} else {
			echo "AWB: ", $cn->awb, "\n";
		}
		$oawb = $this->prompt('Wrong AWB: ');
		$hwb = empty($cn->mdata['house_bill'])? $cn->no : $cn->mdata['house_bill'];
		//                $hbn=$this->prompt('hbn:');
		$airline = substr(trim($cn->airline), 0, 2);
		$flight = substr(trim($cn->flight), 2);
		//                $shipments=[];
		//                $shipment=ImParcel::model()->find('hbn=:hbn',array(':hbn'=>$hbn));
		//                $shipments[]=$shipment;
		foreach ($cn->shipments as $i=>$p) {
			//                    if(($p->bwf&4)<=0)                        continue;
			$os = Edimsg::model()->findAll("status = 30 AND type = 'AIRCR-S' AND fid = :id", [':id' => $p->id]);
			$omid = null;
			if (!empty($os)) {
				foreach ($os as $o) {
					if (preg_match('/MWB:'.$oawb.'/', $o->msg) && preg_match('/BGM\+933:::AIRCR\+([^\+:]+):\d+\+9\'/', $o->msg, $m)) {
						$omid = $m[1];
						break;
					}
				}
			}
			if (empty($omid)) {
				echo 'Original MSG not found for ', $p->hbn, "\n";
				continue;
			}
			$em = new Edimsg;
			$em->dt = date('Y-m-d H:i:s');
			$em->type = 'AIRCR-S';
			$em->sender = Yii::app()->params['ics']['testing']? Yii::app()->params['ics']['test_site'] : Yii::app()->params['ics']['prod_site'];
			$em->receiver = Yii::app()->params['ics']['customs_id'];
			$em->status = 19;
			$em->fid = $p->id;
			$em->save();
			$em->mid = sprintf('%06s', substr($em->id, -6));
			$vn = Edimsg::model()->count('type = :t AND fid = :fid', [':t' => 'AIRCR-S', ':fid' => $p->id]) + 1;
			$msg = "UNH+".$em->mid."+CUSCAR:D:99B:UN'
BGM+933:::AIRCR+".$omid.":".$vn."+50'
RFF+HWB:".$p->hbn."'
RFF+MWB:".$oawb."'
TDT+20+".$flight."++6+".$airline."::3'
DTM+178:".date("Ymd", strtotime($cn->eta)).":102'
";
			$mc = substr_count($msg, "\n")+1;
			$msg .= "UNT+".$mc."+".$em->mid."'";
			$em->msg = $msg;
			$em->status = 20;
			$em->save();
		}
		echo "Done\n";
	}
	 
		
		
	public function updateWeight()
	{
		$fileName=$this->prompt('file name:');
		$file=$this->tmp.'forau'.DIRECTORY_SEPARATOR.$fileName.'.xlsx';
		$xls=new oExcel();
		$xls->supported($file);
		$xls->load($file);
		$data=$xls->getAll();
		unset($data[1]);
		foreach ($data as $d) {
			if (empty($d[1])) {
				continue;
			}
			$p=ImParcel::model()->find('ref=:ref', [':ref'=>trim($d[1])]);
			if (!empty($p)) {
				$p->weight=round($d[4], 2);
				if ($p->save()) {
					echo $p->ref." updated!\n";
				} else {
					echo 2;
				}
			}
		}
	}
		
	public function getCharge()
	{
		$ref='AMQ3286358';
		$p= ImParcel::model()->find('ref=:ref', [':ref'=>$ref]);
		echo $p->getDiffChargeByChargecode(5.2, 1.2);
	}
		
	public function startrackManifest()
	{
		$ref=$this->prompt('ref:');
		$p=ImParcel::model()->find('ref=:ref', [':ref'=>$ref]);
		if (!empty($p)) {
			$ss[]=$p;
			$cn=$p->consol;
		}
						
		if (!empty($ss)) {
			$ssApi = new StarTrackAPI('syd', true);
			$r = $ssApi->createOrderFromShipments($ss, $cn->no);

			if (!empty($r->order)) {
				$cn->mdata['startracksent'] = 1;
				$cn->save();

				$oid = $r->order->order_id;

				// because aupost returned shipment is not the same order with our sending order
				// so here we need to order by HBN again
				$auPostShipments = [];
				foreach ($r->order->shipments as $aushipment) {
					$auPostShipments[$aushipment->shipment_reference] = $aushipment;
				}

				foreach ($ss as $i => $s) {
					$aushipment = $auPostShipments[$s->hbn];

					// check to see if tranship existing
					// in case existing , just update it
					$ts = Tranship::model()->find('pid = :pid AND org_id = :oid AND status = 19', [':pid' => $s->id,':oid' => Org::ORGID_COURIER_STARTRACK]);
					if (empty($ts)) {
						$ts = new Tranship;
						$ts->pid = $s->id;
						$ts->org_id = Org::ORGID_COURIER_STARTRACK;  // for startrack post office
						$ts->man_id = $s->man_id;
						$ts->type = 80;  // shipment transfer to a different delivery courier
						$ts->status = 19; // in finally moving status
						$ts->connote = $s->ref;
						// currently we save cost with a single field

						$ts->mdata['cost'] = $aushipment->shipment_summary->total_cost;
						$costValue = floatval($aushipment->shipment_summary->total_cost - $aushipment->shipment_summary->total_gst);
						$ts->cost = round($costValue, 2);
					}
					$ts->time = date('Y-m-d H:i:s');
					$ts->mdata['oid'] = $oid;
					$ts->mdata['sid'] = $aushipment->shipment_id;
					$ts->save();
				}
			}
			echo 'done';
		}
	}

	public function getcost()
	{
		$ref=$this->prompt(':ref');
		$p=ImParcel::model()->find('ref=:ref', [':ref'=>$ref]);
		var_dump($p->getChargeByChargecode());
	}
		 
		 
		 
		 
	public function sendCreditNotice()
	{
		$sql='SELECT to_id FROM `invoice` where status in(2,3,7) group by to_id;';
		$rs=Yii::app()->db->createCommand($sql)->queryAll();
		$orgIds=[];
		foreach ($rs as $r) {
			$orgIds[]=$r['to_id'];
		}
		if (!empty($orgIds)) {
			foreach ($orgIds as $oid) {
				$this->checkTheLimit($oid);
			}
		}
	}
	
	public function checkTheLimit($oid)
	{
		$org=Org::model()->findByPk($oid);//$id
		if (empty($org)) {
			return;
		}
		if (!isset($org->extra['last_send_percent'])) {
			$org->extra['last_send_percent']=0;
			$org->updateMeta();
		}
		if (!(!empty($org->extra['creditlimit'])&&isset($org->extra['client_billing_email']))) {
			return;
		}
		$limit =floor($org->extra['creditlimit']);
		$a=$org->getCurrentCreditOfLimit();
		if (!empty($a)) {
			if ($a>=1) {
				if (isset($org->extra['last_send_percent'])&&$org->extra['last_send_percent']!=1.0) {
					if ($this->sendCreditLimit($org, $a, $limit)) {
						$org->extra['last_send_percent']=1.0;
						$org->updateMeta();
					}
				}
			} elseif ($a>=0.8) {
				if (isset($org->extra['last_send_percent'])&&$org->extra['last_send_percent']!=0.8) {
					if ($this->sendCreditLimit($org, $a, $limit)) {
						$org->extra['last_send_percent']=0.8;
						$org->updateMeta();
					}
				}
			} elseif ($a>=0.5) {
				if (isset($org->extra['last_send_percent'])&&$org->extra['last_send_percent']!=0.5) {
					if ($this->sendCreditLimit($org, $a, $limit)) {
						$org->extra['last_send_percent']=0.5;
						$org->updateMeta();
					}
				}
			}
		}
	}
	public function sendCreditLimit($org, $percent, $creditLimit)
	{
		$emailLog = new Emailog();
		$percent=$percent*100 ."%";
		$emailLog->type = Emailog::CREDIT_NOTICE;
		$emailLog->fid = $org->id;
		$emailLog->dt = date('Y-m-d H:i:s');
		$emailLog->to_id = $org->id;
		$emailLog->status = 10;
		$emailLog->prepTemplate();
		$emailLog->tpl->assignThese([
			'CLIENT_NAME' => $org->name,
			'PERCENT'=>$percent,
			'CREDIT_LIMIT' => $creditLimit,
		]);
		$emailLog->subject = $emailLog->tpl->subject;
		$emailLog->body = $emailLog->tpl->getContent();
		if ($emailLog->creditNoticeSend()) {
			return true;
		}
		return  false;
	}
		 
	public function getTheOrg()
	{
		$dpmt=$this->prompt("department:");
		$fileName=$this->tmp.$dpmt.".xlsx";
		$sql="SELECT to_id FROM `invoice` WHERE dpmt=".$dpmt." AND date >'2017-12-01'GROUP BY to_id" ;
		$rs=Yii::app()->db->createCommand($sql)->queryAll();
		$xls=new oExcel();
		$i=1;
		$xls->addRow($i++, ['Org ID','Org Name','Billing Email','Op Name','Op Email']);
		foreach ($rs as $r) {
			$org=Org::model()->findByPk($r['to_id']);
			if (!empty($org)) {
				$opName='';
				$opEmail='';
				$billingEmail='';
				if (!empty($org->extra['op_id'])) {
					$op=User::model()->findByPk($org->extra['op_id']);
					if (!empty($op)) {
						$opName=$op->getName();
						$opEmail=$op->email;
					}
				}
				if (!empty($org->extra['client_billing_email'])) {
					$billingEmail=$org->extra['client_billing_email'];
				}
			}
			$xls->addRow($i++, [$org->id,$org->name,$billingEmail,$opName,$opEmail]);
		}
		$xls->output($fileName, null, false);
	}
			 
	public function getCurrentLimit()
	{
		$org_id=$this->prompt("Org_id");
				 
		$org= Org::model()->findByPk($org_id);
		var_dump($org->getCurrentCreditOfLimit());
	}
	public function getLetter()
	{
		$id=$this->prompt('id:');
		$consol= ImcoConsol::model()->findByPk($id);
				
		var_dump($consol->getLetter());
	}
			
	public function wordReplace()
	{
		$dpmt=$this->prompt("file Name:");
		$fileName=$this->tmp."forau".DIRECTORY_SEPARATOR.$dpmt.".xlsx";
		$xls=new oExcel();
		$xls->supported($fileName);
		$xls->load($fileName);
		$data=$xls->getAll();
		foreach ($data as $d) {
			if (empty(trim($d[1]))) {
				continue;
			}
			$word=new WordReplace();
			$word->pre_word=$d[1];
			$word->replace_word=substr($d[1], 0, 1).substr($d[1], -1, 1);
			$word->status=1;
			$word->save();
		}
	}
		 
	public function getTrackInfo()
	{
		$shipment= Shipment::model()->find("ref=:ref", [":ref"=>$this->prompt("ref")]);
		var_dump($shipment->trackingInfo());
	}
	 
	public function changeToLetter()
	{
		$hbn=$this->prompt(":hbn");
		$type=$this->prompt(':type');
		$shipment= ImParcel::model()->find('hbn=:hbn', [':hbn'=>$hbn]);
		$shipment->cbwf=$shipment->cbwf&$type;
		$shipment->ref= 'LET'.sprintf('%07s', substr($shipment->id, -7));
		 
		$shipment->mdata['letter_aupost']=$type;
		 
		$shipment->save();
	}
	 
	public function updateBilling()
	{
		$no= $this->prompt(":consol_no;");
		$consol= ImcoConsol::model()->find('no=:no', [':no'=>$no]);
		if (!empty($consol)) {
			$consol->updateAupostRealCost();
			$consol->updateFastWayRealCost();
		}
			 
		echo "done\n";
	}
	 
	public function getShipmentNo()
	{
		$no= $this->prompt(":consol_no;");
		$consol= ImcoConsol::model()->find('no=:no', [':no'=>$no]);
		if (!empty($consol)) {
			var_dump($consol->getReportDetail());
		}
	}
	 
	 
	public function updateCharge()
	{
		$no= $this->prompt(":consol_no:");
		$consol= ImcoConsol::model()->find('no=:no', [':no'=>$no]);
		foreach ($consol->shipments as $p) {
			$chargevalue=0;
			if ($p->agent_id==1206&&empty($p->mdata['chargecode'])) {
				$chargcode= preg_match('/AMQ\d{7}/i', $p->ref)?5813:8271;
				$chargevalue = $p->getChargeByChargecode($chargecode, true);
			} else {
				$chargevalue=$p->getRtsEstiInvoice();
			}
			$p->mdata['charge_client_amount']= number_format($chargevalue, 4, '.', '');
			$p->updateMeta();
		}
		echo "done\n";
	}
		 
	public function updateChargeByDate()
	{
		$start= $this->prompt("start:");
		$end=$this->prompt("end:");
		$criteria=new CDbCriteria;
		$criteria->addCondition("t.eta >= '" .$start ."'");
		$criteria->addCondition("t.eta <= '".$end ."'");
		$criteria->addCondition("t.status <= 100");
		$criteria->addCondition("t.owner_id != 114");
		$criteria->addInCondition('type', [15]);
		$criteria->order='eta';
		$consols= ImcoConsol::model()->findAll($criteria);
		$i=1;
		foreach ($consols as $consol) {
			if (!empty($consol->shipments)) {
				foreach ($consol->shipments as $p) {
					$chargevalue=0;
					if ($p->agent_id==1206&&empty($p->mdata['chargecode'])) {
						$chargcode= preg_match('/AMQ\d{7}/i', $p->ref)?5813:8271;
						$chargevalue = $p->getChargeByChargecode($chargecode, true);
					} else {
						$chargevalue=$p->getRtsEstiInvoice();
					}
					$p->mdata['charge_client_amount']= number_format($chargevalue, 4, '.', '');
					$p->updateMeta();
				}
			}
			echo 'update '.$consol->no."\t".$i++."\n";
		}
		echo "done\n";
	}
		 
	public function updateTheWeight()
	{
		$fileName=$this->prompt('file name:');
		$file=$this->tmp.'forau'.DIRECTORY_SEPARATOR.$fileName.'.xlsx';
		$xls=new oExcel();
		$xls->supported($file);
		$xls->load($file);
		$data=$xls->getAll();
		foreach ($data as $d) {
			if (empty($d[1])) {
				continue;
			}
			$p= ImParcel::model()->find('hbn=:hbn', [':hbn'=>$d[1]]);
			if (!empty($p)) {
				$p->weight=$d[2];
				$p->update(['weight']);
				echo $p->hbn.'updated!\n';
			}
		}
	}
	 
	public function sendTheEmail()
	{
		$id= $this->prompt('id:');
		$model= ImcoConsol::model()->findByPk($id);
		$agent_ships=[];
		$select_agent='';
		foreach ($model->shipments as $s) {
			$agent_ships[$s->agent_id][]=$s;
		}

		if (!empty($model->mdata['owner_id'])) {
			$select_agent=$model->mdata['owner_id'];
		}
				
		$tempDirectory = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
		if (!file_exists($tempDirectory)) {
			mkdir($tempDirectory);
		}
		foreach ($agent_ships as $org_id=>$ships) {
			$csv = [];
			if ($org_id==$select_agent) {
				$csv[]='MAWB:,'.(empty($model->awb)?$model->no:$model->awb);
				$csv[]='ETD:,'.$model->etd.',ETA:,'.$model->eta;
				$csv[]='WEIGHT(KG):,'.(empty($model->mdata['awb_wt'])?'':$model->mdata['awb_wt']).',CHARGEABLE WEIGHT(KG):,'.(empty($model->mdata['cgb_wt'])?'':$model->mdata['cgb_wt']);
				$csv[]='Airline:,'.$model->airline.',Flight No:,'.$model->flight;
				$csv[]='POL:,'.$model->pol.',POD:,'.$model->pod;
				$csv[]='';
			}
			$csv[] = 'ConsignmentRef,Tracking,Status,ScannedDateTime,Count,Manifested';
			foreach ($ships as $s) {
				$csv[] = $s->hbn.','.$s->ref.','.($s->status<50? 'Held' : 'Clear').','.(empty($s->getScanTime())?'':date('Y-m-d', strtotime($s->getScanTime()))).','.$s->getOutPkg().','.$s->pkg;
			}
			$fileName=$tempDirectory.DIRECTORY_SEPARATOR.'PCA_'.$org_id.'_'.$model->no.'.csv';
			file_put_contents($fileName, implode("\n", $csv));
			$emailLog = new Emailog();
			$emailLog->type = Emailog::OUTTURN_REPORT;
			$emailLog->fid = $id;
			$emailLog->dt = date('Y-m-d H:i:s');
			$emailLog->to_id = $org_id;
			$emailLog->status = 10;
			$emailLog->prepTemplate();

			$emailLog->subject = $emailLog->tpl->subject;
			$emailLog->body = $emailLog->tpl->getContent();
			if (empty($emailLog->scheduled)) {
				$emailLog->imConsolSend($fileName);
			}
		}
		AppHelper::unlinkRecursive($tempDirectory);
		echo 'Email Sent Successfully';
//            $this->ajaxResult($model, array('id'), 'Email Sent Successfully');
	}
		
	public function getEparcelReport()
	{
		$start=$this->prompt('the start date:');
		$end=$this->prompt('the end date:');
		//1. get the previous shipment that using eparcel, (a. including BFE; b. not including BFE;)
		$final['total_no']=0;
		$final['total_weight']=0;
		$final['total_cost']=0;
		while ($start<$end) {
			$second=date('Y-m-d', strtotime('+5 day', strtotime($start)));
			if ($second>$end) {
				$second=$end;
			}
			$rs= ImParcel::model()->findAll('ref like :ref and created>=:start AND created<:end AND consol_id>0 ', [':ref'=>'%'.'AMQ'.'%',':start'=>$start,':end'=>$second]);
			foreach ($rs as $p) {
				$weight=$p->weight;
				$r= ReconciliationLine::model()->find('shipment_no=:ref', [':ref'=>$p->ref]);
				if (!empty($r)) {
					$weight=$r->weight;
				}
				unset($r);
				$temp=$this->getCostExLocation($p->postcode, $weight);
				$final['total_no']+=1;
				$final['total_weight']+=$weight;
				$final['total_cost']+=$temp['current_cost'];
				if (array_key_exists($temp['org_id'], $final)) {
					$final[$temp['org_id']]['weight']+=$temp['weight'];
					$final[$temp['org_id']]['cost']+=$temp['cost'];
					$final[$temp['org_id']]['number']+=1;
				} else {
					$final[$temp['org_id']]['weight']=$temp['weight'];
					$final[$temp['org_id']]['cost']=$temp['cost'];
					$final[$temp['org_id']]['number']=1;
				}
			}
			$start=$second;
			unset($rs);
		}
		$theCost=0;
		foreach ($final as $f) {
			if (is_array($f)) {
				$theCost+=$f['cost'];
			}
		}
		$final['mixed_cost']=$theCost;

	
	
		print_r($final);
		//3 output.....
	}
		
		
	public function getCostExLocation($postcode, $weight)
	{
		//return Org_rate id, weight, cost;
		$temp=['org_id'=>0,'cost'=>99999,'zone_id'=>3];
		$rs= OrgRate::model()->findAll('org_id=101 AND zone_id>2 AND type=5');
		$currentCost=0;
		foreach ($rs as $orgRate) {
			$cost=$this->getCourierCost($orgRate, $postcode, $weight);
			if ($orgRate->zone_id==3) {
				$currentCost=$cost;
			}
			if ($temp['cost']>$cost) {
				$temp['org_id']=$orgRate->id;
				$temp['cost']=$cost;
				$temp['zone_id']=$orgRate->zone_id;
			}
		}
		$temp['weight']=$weight;
		$temp['current_cost']=$currentCost;
		if ($temp['zone_id']>3) {
			$temp['cost']-=0.5*$weight;
		}
		//         var_dump($temp);
		return $temp;
	}
		 
	public function getCourierCost($orgRate, $postcode, $weight)
	{
		$price = 0;
		// base on postcode to get related charge code
		$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>$orgRate->org_id ,':zoneid' => $orgRate->zone_id,':code' => $postcode]);

		// if not found we set as NSW country
		$chargeCode = 'N1';
		if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
			$chargeCode = $zoneMap['z1'];
		}
		// base charge code to get zone rate
		$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRate->id]);
		if (!empty($zrs)) {
			// get maximum one
			foreach ($zrs as $zr) {
				$temp = $zr['base'] + $zr['item'];
				if ($zr['nkg'] > 0) {
					$wl = $weight- ($zr['base'] > 0 ? $zr['nkg'] : 0);
					$temp += ceil($wl / $zr['nkg']) * $zr['perkg'];
				} else {
					$temp +=  $weight * $zr['perkg'];
				}
				if ($zr['minimum'] > 0 && $temp < $zr['minimum']) {
					$temp = $zr['minimum'];
				}
				$price = max($price, $temp);
			}
		}
		if ($orgRate->zone_id>3) { //for ex mel and ex brisbane, we extra charge
			$price+=$weight*0.5;
		}
		return $price;
	}
		 
		 
		 
		 
	public function seaAcr()
	{
		$cn= Consol::model()->find("no='DW17120701SYD'");
	}
		 
		 
		 
	public function changeManifest()
	{
		$p1=ImParcel::model()->find("hbn='ECN1247000001'");
		$p2=ImParcel::model()->find("hbn='7RFZ50002410'");
		$p2->mdata['ss_shipment_id']=$p1->mdata['ss_shipment_id'];
		$p2->mdata['ss_shipment_items']= $p1->mdata['ss_shipment_items'];
		$p2->mdata['ss_lbl_request_id']= $p1->mdata['ss_lbl_request_id'];
		$p2->updateMeta();
	}
		 
		 
		 
	//       public function fetchEncryptedMsg(){
//    $msg_enc = $this->tmp.'1519620889.M589771P289214.titan.orite.com,S=7353,W=7477';
//    $msg_sig = $this->tmp.'bb.txt';
	//                echo 3;
//    openssl_pkcs7_decrypt($msg_enc, $msg_sig, Yii::app()->params['ics']['enc_pem'], array(Yii::app()->params['ics']['enc_pem'], Yii::app()->params['ics']['pem_pwd']));
	////    unlink($msg_enc);
	//                $a= file_get_contents($msg_sig);
	//                $b=explode(PHP_EOL.PHP_EOL, $a,2);
	//                echo base64_decode(trim($b[1]));
	////    $this->getAttachedEDI($mid, $header, $msg_sig);
//  }
//
		
	public function updateStartrackShipment()
	{
		$id = $this->prompt('shipment id: ');
		$p = ImParcel::model()->findByPk($id);
		if (!empty($p) &&  $p->type == 10) {
			if (isset($p->mdata['ss_shipment_id']) && !empty($p->mdata['ss_shipment_id'])) {
				if (empty($p->mdata['ss_shipment_items'])) {
					echo 'failed';
					return;
				}
				$ss = new StarTrackAPI('syd', true);
				$result = $ss->updateShipment($p->mdata['ss_shipment_id'], $p);
				$result=(array) $result;
				if (!empty($result)) {
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
	public function updateStartrack()
	{
		$id = $this->prompt('shipment id: ');
		$ref=$this->prompt('item ref: ');
		$p = ImParcel::model()->findByPk($id);
		$p->mdata['ss_shipment_items'][]=$ref;
		$p->updateMeta();
		echo 'updated!';
	}
		
	public function getTheWeight()
	{
		$p= ImParcel::model()->find('ref=:ref', [':ref'=>$this->prompt('ref:')]);
		echo $p->chargeWeight();
	}
	
	
	public function updateSomeCost()
	{
		$sql="SELECT s.shipment_no as no FROM reconciliation_line s INNER JOIN reconciliation t ON s.parent_id=t.id WHERE t.invoice_date>'2018-01-15' AND t.client_type=1";
		$rs=Yii::app()->db->createCommand($sql)->queryAll();
		$i=0;
		foreach ($rs as $r) {
			if (!empty($r['no'])) {
				$p=Tranship::model()->find('connote=:ref', [':ref'=>$r['no']]);
				if (!empty($p->mdata['oid'])&&substr($p->mdata['oid'], 3)>='1910261') {
					//update my_value and my_value_m
					$shipment= ImParcel::model()->find('ref=:ref', [':ref'=>trim($r['no'])]);
					$rc= ReconciliationLine::model()->find('shipment_no=:ref', [':ref'=>trim($r['no'])]);
					$rc->my_value= number_format(ImParcel::getCourierCostByShipments($shipment->weight, $shipment->postcode, 101)['price'], 2, '.', '');
					$rc->my_value_m=number_format(ImParcel::getCourierCostByShipments($shipment->weight, $shipment->postcode, 101, true)['price'], 2, '.', '');
					$rc->update(['my_value','my_value_m']);
					echo $r['no'].PHP_EOL;
					$i++;
				}
			}
		}
		echo $i.' Updated!'.PHP_EOL;
	}
//
//
//
	//      function getTheCourierCostByShipments(){
	//                  $shipmentRef=$this->prompt('ref');
	//                  $courierId=101;
//    // make it fast we hardcode here currently
//    $orgRates = array(
//      Org::ORGID_COURIER_FASTWAY => 52,  // you can get this id from Org -> Rates Tab -> Flex Rate by Zone hypelink will including this id
//      Org::ORGID_COURIER_AUPOST => 101,
//    );
//
//    $price = 0;
//    $consolId = 0;
//    $shipment = Shipment::model()->find('ref = :ref',[':ref' => $shipmentRef]);
//    if ( !empty($shipment) ) {
//      $consolId = $shipment->consol_id;
	//                        $ap= new AusPostAPI('syd', FALSE);
	//                        $weight =sprintf('%.02f', $ap->getBreakWeight($weight));
//
//      // base on postcode to get related charge code
//      $zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code',
//        [':oid' =>$courierId, ':zoneid' => 1, ':code' => $shipment->cnee->postcode]);
//
//      // if not found we set as NSW country
//      $chargeCode = 'N1';
//      if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
//        $chargeCode = $zoneMap['z1'];
//      }
//
//      // base charge code to get zone rate
//      $zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ",
//        [':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRates[$courierId]]);
//
//      if (!empty($zrs)) {
//
//        // get maximum one
//        foreach ($zrs as $zr) {
//          $temp = $zr['base'] + $zr['item'];
//          if ($zr['nkg'] > 0) {
//            $wl = $weight - ($zr['base'] > 0 ? $zr['nkg'] : 0);
//            $temp += ceil($wl / $zr['nkg']) * $zr['perkg'];
//          } else {
//            $temp += $weight * $zr['perkg'];
//          }
//          if ($zr['minimum'] > 0 && $temp < $zr['minimum']) $temp = $zr['minimum'];
//          $price = max($price, $temp);
//        }
//      }
//    }
	//                var_dump($price);
//    return array('price' => $price, 'consol_id' => $consolId);
//  }
//
		
	//        public function getTheTheweigth(){
	//            $weight=$this->prompt('weight');
	//            $ap= new AusPostAPI('syd', FALSE);
	//            echo  sprintf('%.02f', $ap->getBreakWeight($weight));
//
	//        }
		
	public function checkTheCost()
	{
		var_dump(ImParcel::getCourierCostByShipments(2.50, 4207, 101));
		var_dump(ImParcel::getCourierCostByShipments(2.50, 4207, 101, true));
	}
		
		
	public function updateReWeight()
	{
		$i=0;
		$rs=1;
		while (!empty($rs)) {
			$rs= ReconciliationLine::model()->findAll('id>0 Limit :offset,10', [':offset'=>$i]);
			foreach ($rs as $r) {
				$shipment= ImParcel::model()->find('ref=:ref', [':ref'=>$r->shipment_no]);
				if (!empty($shipment)) {
					$r->cust_check_weight=$shipment->weight;
					$r->update('cust_check_weight');
				}
				echo $r->shipment_no.PHP_EOL;
			}
			$i+=10;
		}
	}
	public function getTheHash()
	{
		$ref='7RFZ50002556';
		$r= ImParcel::model()->find('ref=:ref', [':ref'=>$ref]);
		$hash=HashVerify::genHash($r);
		echo $hash;
	}
		
	//        public function testMessage(){
//
	//            Sms::sendMessageLocal("0430390862", "hohohoho");
	//        }

		
		
		
	public function updateShipmentSenderAddress()
	{
		$fileName=$this->prompt('file name:');
		$file=$this->tmp.'forau'.DIRECTORY_SEPARATOR.$fileName.'.xlsx';
		$xls=new oExcel();
		$xls->supported($file);
		$xls->load($file);
		$data=$xls->getAll();
		unset($data[1]);
		foreach ($data as $index=>$d) {
			if (empty($d[1])) {
				continue;
			}
			$p= ImParcel::model()->find('ref=:ref', [':ref'=>trim($d[1])]);
			if (!empty($p)) {
				$p->cnor->address=$d[5];
				$p->cnor->name=$d[2];
				$p->cnor->tel= preg_replace("/[^\d]/", '', $d[4]);
				$p->cnor->postcode=$d[6];
				$p->cnor->company=$d[3];
				$p->cnor->save();
				echo $p->ref.PHP_EOL;
			}
		}
	}
	 
	public function getAmazonReport()
	{
		$sql='SELECT s.id from `shipment` s INNER JOIN `addr` a ON s.cnee_id=a.id WHERE s.type=10 AND a.name like "%amazon%" AND s.consol_id>0';
		$rs=Yii::app()->db->createCommand($sql)->queryAll();
		$file= $this->tmp."amazon.xlsx";
		$xls=new oExcel();
		$xls->setCell('A1', 'ref');
		$xls->setCell('B1', 'cbm');
		$xls->setCell('C1', 'weight(kg)');
		$xls->setCell('D1', 'packs');
		$xls->setCell('E1', 'dim(WxHxL)');
		$xls->setCell('F1', 'date');
		$xls->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
		foreach ($rs as $i=>$r) {
			$p= ImParcel::model()->findByPk($r['id']);
			$xls->setCell('A'.($i+2), $p->ref);
			$xls->setCell('B'.($i+2), $p->cbm);
			$xls->setCell('C'.($i+2), $p->weight);
			$xls->setCell('D'.($i+2), $p->pkg);
			$xls->setCell('E'.($i+2), number_format($p->mdata['dim']['w'], 2, '.', '').'x'.number_format($p->mdata['dim']['h'], 2, '.', '').'x'.number_format($p->mdata['dim']['d'], 2, '.', ''));
			$xls->setCell('F'.($i+2), $p->created);
		}
		$xls->output($file, null, false);
	}
	 
		
	public function getTheReport()
	{
		$file= $this->tmp."report.xlsx";
		$rs=Reconciliation::model()->findAll('client_type=1 AND invoice_date>="2018-02-01"');
		$w1total=$w2total=$w3total=$w4total=$w5total=$w7total=$w10total=$w15total=$w22total=$w25total=0;
		$w1gap=$w2gap=$w3gap=$w4gap=$w5gap=$w7gap=$w10gap=$w15gap=$w22gap=$w25gap=0;
		$w1no= $w2no=$w3no=$w4no=$w5no=$w7no=$w10no=$w15no=$w22no=$w25no=0;
			
		$totolcost=0;
		$oldcost=0;
		
		foreach ($rs as $r) {
			foreach ($r->lines as $line) {
				$totolcost+=$line->value;
				$p= ImParcel::model()->find('ref=:ref', [':ref'=>$line->shipment_no]);
				if (empty($p)) {
					continue;
				}
				$oldcost+=$this->getCourierCostByShipmentss($line->weight, $p->postcode);
				if ($line->cdeadwt<=0.5) {
					continue;
				}
				if ($line->cdeadwt<=1) {
					$w1no++;
					$w1total+=$line->weight;
					$w1gap+=abs($line->weight-$line->cdeadwt);
				} elseif ($line->cdeadwt<=2) {
					$w2no++;
					$w2total+=$line->weight;
					$w2gap+=abs($line->weight-$line->cdeadwt);
				} elseif ($line->cdeadwt<=3) {
					$w3no++;
					$w3total+=$line->weight;
					$w3gap+=abs($line->weight-$line->cdeadwt);
				} elseif ($line->cdeadwt<=4) {
					$w4no++;
					$w4total+=$line->weight;
					$w4gap+=abs($line->weight-$line->cdeadwt);
				} elseif ($line->cdeadwt<=5) {
					$w5no++;
					$w5total+=$line->weight;
					$w5gap+=abs($line->weight-$line->cdeadwt);
				} elseif ($line->cdeadwt<=7) {
					$w7no++;
					$w7total+=$line->weight;
					$w7gap+=abs($line->weight-$line->cdeadwt);
				} elseif ($line->cdeadwt<=10) {
					$w10no++;
					$w10total+=$line->weight;
					$w10gap+=abs($line->weight-$line->cdeadwt);
				} elseif ($line->cdeadwt<=15) {
					$w15no++;
					$w15total+=$line->weight;
					$w15gap+=abs($line->weight-$line->cdeadwt);
				} elseif ($line->cdeadwt<=22) {
					$w22no++;
					$w22total+=$line->weight;
					$w22gap+=abs($line->weight-$line->cdeadwt);
				} elseif ($line->cdeadwt<=25) {
					$w25no++;
					$w25total+=$line->weight;
					$w25gap+=abs($line->weight-$line->cdeadwt);
				}
			}
		}
		$xls=new oExcel();
		$i=1;
		$xls->addRow($i++, ['0.5-1','1-2','2-3','3-4','4-5','5-7','7-10','10-15','15-22','22-25']);
		$xls->addRow($i++, [$w1gap,$w2gap,$w3gap,$w4gap,$w5gap,$w7gap,$w10gap,$w15gap,$w22gap,$w25gap]);
		$xls->addRow($i++, [ $w1no,$w2no,$w3no,$w4no,$w5no,$w7no,$w10no,$w15no,$w22no,$w25no]);
		$xls->addRow($i++, [ $w1total,$w2total,$w3total,$w4total,$w5total,$w7total,$w10total,$w15total,$w22total,$w25total]);
		$xls->addRow($i++, [ $totolcost,$oldcost]);
		
		$xls->output($file, null, false);
	}
		 
		 
	public function getCourierCostByShipmentss($weight, $postcode, $courierId = Org::ORGID_COURIER_AUPOST)
	{
		 
		// make it fast we hardcode here currently
		$orgRates = [
			Org::ORGID_COURIER_FASTWAY => 52,  // you can get this id from Org -> Rates Tab -> Flex Rate by Zone hypelink will including this id
			Org::ORGID_COURIER_AUPOST => 44,
		];
		$orate=OrgRate::model()->findByPk($orgRates[$courierId]);
		$price = 0;
				 
		// base on postcode to get related charge code
		$zoneMap = ZoneMap::model()->find(
				'org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code',
				[':oid' =>$courierId, ':zoneid' => $orate->zone_id, ':code' => $postcode]
			);

		// if not found we set as NSW country
		$chargeCode = 'N1';
		if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
			$chargeCode = $zoneMap['z1'];
		}

		// base charge code to get zone rate
		$zrs = ZoneRate::model()->findAll(
				"zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ",
				[':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRates[$courierId]]
			);

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
				if ($zr['minimum'] > 0 && $temp < $zr['minimum']) {
					$temp = $zr['minimum'];
				}
				$price = max($price, $temp);
			}
		}
		
		return $price;
	}
		

	public function getHashUrl()
	{
		$ref=$this->prompt('ref:');
		$shipment= ImParcel::model()->find('ref=:ref', [':ref'=>$ref]);
		echo $shipment->getTheHashUrl();
	}
		
	public function updateMani()
	{
		$i=0;
		$manis=1;
		while (!empty($manis)) {
			$manis=ManiMap::model()->findAll('model="ImParcel" Limit :offset,1000', [':offset'=>$i]);
			foreach ($manis as $m) {
				$p=ImParcel::model()->findByPk($m->fid);
				if (!empty($p)&&empty($p->man_id)) {
					$p->man_id=$m->mani_id;
					$p->update('man_id');
				}
				echo $m->fid."\n";
			}
			$i+=1000;
		}
	}
	public function getChargeByChargecode($myWeight, $postcode, $chargecode)
	{
		$amount = 0;
		// based on chargecode to get chargecode ID
		$chargecodeInfo = ImportChargeCode::model()->find('chargecode = :cid', [':cid' => $chargecode]);
		$zoneMap = ZoneMap::model()->find('chargecode_id = :cid AND pc_lo <= :pc AND pc_hi >= :pc', [':cid' => $chargecodeInfo['id'], ':pc' => $postcode]);
		if (!empty($zoneMap)) {
			$zoneRates = ZoneRate::model()->findAll('chargecode_id = :cid AND weight_lo <:w AND weight_hi >= :w AND zone = :z AND base+item+perkg > 0', [':cid' => $chargecodeInfo['id'], ':w' => ceil($myWeight*10)/10, ':z' => $zoneMap['z1']]);
			if (empty($zoneRates)) {
				$zoneRates = ZoneRate::model()->findAll('chargecode_id = :cid AND weight_lo <=:w AND weight_hi >= :w AND zone = :z AND base+item+perkg > 0', [':cid' => $chargecodeInfo['id'], ':w' => ceil($myWeight*10)/10, ':z' => $zoneMap['z1']]);
			}
			// get maximum one
			foreach ($zoneRates as $zoneRate) {
				if ($zoneRate['nkg']>0) {  // used to break weight charge  45g==>50g; 43g=>50;   93g=>100g;
					$temp = $zoneRate['base'] + $zoneRate['item'] + $zoneRate['perkg']*($zoneRate['nkg']-fmod($myWeight, $zoneRate['nkg'])+$myWeight);
					$this->tempChargeweight=$zoneRate['nkg']-fmod($myWeight, $zoneRate['nkg'])+$myWeight;
				} else {
					$temp = $zoneRate['base'] + $zoneRate['item'] + $zoneRate['perkg'] * $myWeight;
				}
				if ($zoneRate['minimum'] > 0) {
					$temp = max($temp, $zoneRate['minimum']);
				}
				$amount = max($amount, $temp);
			}
		}
		// no chargecode set
		// we use default charge rate
		if ($amount == 0) {
			if ($myWeight >= 5) {
				$amount = 8.0 + ($myWeight - 5) * 0.36; // default only
			} else {
				$amount = 8.0;
			}
		}

		return $amount;
	}
	
	public function testTheChargecode()
	{
		$chargecode=$this->prompt('chargecode:');
		$weight=10;
		$postcode=2217;
		$cost= $this->getChargeByChargecode($weight, $postcode, $chargecode);
		echo $cost;
	}
	
	
	public function dothereturn()
	{
		$ref= $this->prompt('ref:');
		$p= ImParcel::model()->find('ref=:ref', [':ref'=>trim($ref)]);
		if (!empty($p->mdata['rts_scan_date'])) {
			$p->cbwf=$p->cbwf|256;
			$p->update('cbwf');
		}
	}
	
	public function getTheOrgLeft()
	{
		$oid=$this->prompt('org_id:');
		$org= Org::model()->findByPk($oid);
		
		echo $org->getCurrentCreditLeft();
	}
	
	public function ChangeShipmentChargeCode()
	{
		$hbn=$this->prompt('hbn:');
		$chargecode=$this->prompt('chargecode:');
		$p= ImParcel::model()->find('hbn=:hbn', [':hbn'=>$hbn]);
		$p->mdata['chargecode']=trim($chargecode);
		$p->updateMeta();
		echo 'done';
	}
	public function changeHeldReason()
	{
		$p= ImParcel::model()->findByPk(3286432);
		$p->updateHeldTrackingReason();
	}
	public function getTheCostLetter()
	{
		$cid=$this->prompt('consol_id:');
		$consol= ImcoConsol::model()->findByPk($cid);
		$a=$consol->getReportDetailByCourier();
		var_dump($a);
	}
	public function testMsg()
	{
		$no='15900467493';
		$msg='hello hello';
		Sms::SendMessageCn($no, $msg);
	}
	
	public function updateStartrack1()
	{
		$ref=$this->prompt('ref:');
		if (preg_match('/7RFZ\d{8}/i', $ref)) {
			$p= ImParcel::model()->find('ref=:ref', [':ref'=>$ref]);
			$scan_no=$p->getOutPkg();
			if ($scan_no==0) {
				echo 'not any scaned!';
				return;
			}
			$ss =new StarTrackAPI('syd', true);
			if ($scan_no<$p->pkg) {
				$p->mdata['origin_pkg']=$p->pkg;
				$p->mdata['origin_weight']=$p->weight;
				$p->note.='[changed pack no from '.$p->pkg.' to '.$scan_no.' origin weight:'.$p->weight.' ]';
				$p->weight= number_format($p->weight*$scan_no/$p->pkg, 4, '.', '');
				$p->pkg=$scan_no;
				$p->scan_no=0;
				$p->cbwf=$p->cbwf|1024;
				if ($p->save()) {
					$result = $ss->updateShipment($p->mdata['ss_shipment_id'], $p);
					$respLabel = $ss->createLabels([$p->mdata['ss_shipment_id']]);
					echo 'done!';
				}
			}
		} else {
			echo "not startrack\n";
		}
	}
	
	public function testMessage()
	{
		$sms = new Sms;
		$sms->type = 10;
		$sms->no = "15900467493";//电邮 id@pca168.com  或微信公众号 wz0577td
		$sms->msg = "高星您好！您来自澳洲的包裹  PE1111111 至今未成功匹配身份证，请通过网站 http://t.cn/RwBkisk 上传，如有疑问请联系客服电话 95040315891";
		$sms->send_yp();
	}
	
	public function createExInvoice()
	{
		//        $created=$this->prompt('date:');
		//        $agent_id= $this->prompt('agent_id:');
		//        $invoice_no= $this->prompt('inv_no:');
		$id=$this->prompt('Picking List id:');
		$bd=$this->prompt('date:');
		
		//        $id2= $this->prompt('id2:');
		//        $rs = PickupList::model()->findAll('created like :created AND  fwd_id=:agent_id',array(':created'=>$created."%",':agent_id'=>$agent_id));
		$r= PickupList::model()->findByPk($id);
		//        $r2= PickupList::model()->findByPk($id2);
		var_dump($r->id);
		$aid=$r->fwd_id;
		$rs=[$r];
		$inv = Invoice::createExInv($aid, $bd, $rs, true);
		var_dump($inv->no);
		//      if(!empty($invoice)&&!empty($rs)&&!empty($agent_id)){
//        $inv = $this->createExInv($agent_id, $bd, $rs,true,$invoice);
//      }
	}
	
	public function createExInv($aid, $bd, $rs, $reuse = false, $invoice)
	{
		$owner = $rs[0]->owner;
//    if($reuse) $inv= Invoice::model()->find('type = 20 AND to_id = :aid  AND date=:d ', [':aid' => $aid, ':d' => $bd]);
//    if(empty($inv)) $inv= new Invoice;
		$inv=$invoice;
		$inv->type = 20;
		$inv->dpmt = Invoice::DPMT_EXPORT;
		$inv->currency = 1;
		$inv->to_id = $aid;
//    $inv->status = 2;
		$inv->date = $bd;
		$inv->mdata['name'] = $owner->name;
		$inv->mdata['address'] = $owner->getAddress();
		$inv->mdata['payterm'] = empty($owner->extra['payterm'])? 'COD' : $owner->extra['payterm'].' days';
		$inv->mdata['paytype'] = empty($owner->extra['paytype'])? '' : $owner->extra['paytype'];
		$inv->due = $inv->date;//Invoice::calcDue($inv->date, $inv->mdata['payterm']);
		$inv->total = 0;
		$inv->save();
		$inv->afterFind();

		if ($reuse) {
			foreach ($inv->lines as $l) {
				$l->delete();
			}
		}
		$tot = 0;
		foreach ($rs as $r) {
			$items = [];
			$stot = 0;
			foreach ($r->lines as $l) {
				$p = $l->mm();
				if (in_array($p->status, [12, 100, 104])) {
					continue;
				}
				$rate = $p->getAgentRate(true);
				$weight = $p->chargeWeight();
				$typ = $p->goodsType();
				/*if($p->status == 102){
					$rate[2] == 0;
					$items[] = array($p->hbn, $p->cnor->name, $weight, $p->getStatus(), '', '', '', 0);
				}else{*/
				$duty = $rate[0] == 'EC'? round($p->calTariff() / 4.75 * 1000) / 1000 : 0;
				$items[] = [$p->hbn, $p->cnor->name, $weight, $p->cnee->state, $typ, $rate[0], $rate[1]->perkg, $rate[2], $duty];
				$stot += $rate[2] + $duty;
				//}
			}
			if (empty($items)) {
				continue;
			}

			$il = new InvLine;
			$il->inv_id = $inv->id;
			$il->amount = $stot;
			$il->mdata['items'] = $items;
			$il->mdata['rlno'] = $r->ref;
			$il->model = 'Manifest';
			$il->fid = $r->id;
			$il->save();
			$tot += $stot;
			$r->bwf = $r->bwf | 1;
			$r->save();
		}
		$inv->dpt_id = $r->dpt_id;
		$inv->total = $tot;
		$inv->save();
		return $inv;
	}
		
	public function getExRate()
	{
		$hbn=$this->prompt('hbn:');
		$shipment= ExParcel::model()->find('hbn=:hbn', [':hbn'=>trim($hbn)]);
		var_dump($shipment->getAgentRate(true));
		echo "weight".$shipment->chargeWeight()."\n";
	}
		
		
	public function getEitem()
	{
		$start=$this->prompt('start:');
		$end=$this->prompt('end:');
		$rs = PickupList::model()->findAll(['condition' => 'dpt_id = 106 and fwd_id=462 AND created >=:start AND created<:end ', 'params' => [':start' => $start,':end'=>$end], 'order' => 'created']);
		$file= $this->tmp."part3.xlsx";
		$xls=new oExcel();
		$k=1;
		$xls->addRow($k++, ['hbn','item']);
		foreach ($rs as $r) {
			$xls->addRow($k++, ['','']);
			$xls->addRow($k++, [$r->ref,$r->billdate()]);
			foreach ($r->lines as $l) {
				$p = $l->mm();
				if (in_array($p->status, [12, 100, 104])) {
					continue;
				}
				$temp='';
				if (!empty($p->mdata['client_entry'])) {
					$items= $p->mdata['client_entry']['items'];
					foreach ($items['g'] as $i=>$name) {
						$temp.=$items['q'][$i].'X'.$name."   \n";
					}
				}
				$xls->addRow($k++, [$p->hbn,$temp]);
			}
		}
		$xls->output($file, null, false);
	}
	public function testcrm()
	{
		$ticket= ExCrm::model()->find('no="EIN0000092"');
		foreach ($ticket->shipments as $i=>$ship) {
			echo $ship->ref."\n";
		}
		//            var_dump($ticket->shipments);
	}
		
		
	public function backCrm()
	{
		$erms= ExCrm::model()->findAll('id>1');
			
		foreach ($erms as $cr) {
			if (!empty($cr->parcel_id)) {
				$shipment= Shipment::model()->findByPk($cr->parcel_id);
				if (!empty($shipment)) {
					CrmMap::createCrmMaps($cr->id, $shipment, [$shipment->id]);
				}
			}
		}
	}
		 
		 
	public function testtest()
	{
		ExCrm::chaseIdTicket([3286411,3286407], '0430390862');
	}
		 
		 
	public function startrackLabel()
	{
		$ref=$this->prompt('ref:');
		$shipment= ImParcel::model()->find('ref=:ref', [':ref'=>$ref]);
		$ss = new StarTrackAPI('syd', true);
		$respLabel = $ss->createLabels([$shipment->mdata['ss_shipment_id']]);
		$lblRequestId = '';
		if ($respLabel) {
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
			$ts->cost = number_format(round($cost, 2), 2, '.', '');
			$ts->save();
		}
	}
	public function updateProcess()
	{
		$rs=ShipmentProcess::model()->findAll('type=0');
		foreach ($rs as $r) {
			echo $r->id;
			$r->type=4;
			$r->update('type');
		}
	}
		 
	public function addProcessLog()
	{
		$rs=ShipmentProcess::model()->findAll('id>0');
		foreach ($rs as $r) {
			if (($r->type&4)>0) {
				$this->addingproceslog($r, 4);
			}
			if (($r->type&1)>0) {
				$this->addingproceslog($r, 1);
			}
			if (($r->type&2)>0) {
				$this->addingproceslog($r, 2);
			}
		}
	}
		 
		 
	public function addingproceslog($r, $type)
	{
		$log= ProcessLog::model()->find('fid=:fid AND type=:type', [':fid'=>$r->id,':type'=>$type]);
		if (empty($log)) {
			$log=new ProcessLog;
			$log->fid=$r->id;
			$log->type=$type;
			$log->status=1;
			$log->time=$r->started;
			if (!$log->save()) {
				var_dump($log->getErrors());
			}
		} elseif ($log->status!=1) {
			$log->status=1;
			$log->update('status');
		}
	}
	public function testThechargecode1()
	{
		$chargecode= $this->prompt('chargecode:');
		$hbn= $this->prompt('hbn:');
		$chargecode= ImportChargeCode::model()->find('chargecode=:cgc', [':cgc'=>$chargecode]);
		$p= ImParcel::model()->find('hbn=:hbn', [':hbn'=>$hbn]);
			 
		var_dump($chargecode->hasChargeSetUp($p));
	}
	public function genCrmHash()
	{
		$crm= Crm::model()->findByPk(39);
		echo $crm->genHashUrl('cf');
	}
	public function imparcelEmailTest()
	{
		$p= ImParcel::model()->find('hbn="ECN1222002622"');
		if (!empty($p)) {
			$p->msgNotice();
			echo $p->ref.PHP_EOL;
		}
	}
		 
	public function testOrgLimit()
	{
		$oid=$this->prompt('org_id:');
		$org= Org::model()->findByPk($oid);
		var_dump($org->overCreditLimit());
	}
		 
		 
	public function createInvoiceCsv()
	{
		$oid= $this->prompt('org_id:');
		$file=Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
		if (!file_exists($file)) {
			mkdir($file);
		}
		$org=Org::model()->findByPk($oid);
		$files=[];
		$csv=[];
		if (isset($org->extra['credit_payment_method'])&&$org->extra['credit_payment_method']==1) {
			$start=(date('d')<16?date('Y-m-16', strtotime("-1 month")):date('Y-m-01'));
			$end=(date('d')<16?(date('Y-m', strtotime("-1 month")).'-'.date('t', strtotime('last month'))):date('Y-m-15'));
			$csv[]='Unpaid Inovice Summary before '.$end;
			$csv[]='';
			$csv[]='Unpaid Invoices from '.$start.' To '.$end;
			$csv[]='Invoice,Date,Status,amount,paid,credit,unpaid';
			$invoices= Invoice::model()->findAll('to_id=:org_id AND date<=:end AND date>=:start AND status IN(2,3,7) ORDER BY date', [':org_id'=>$oid,':end'=>$end,':start'=>$start]);
			$total=0;
			$subTotal1=0;
			foreach ($invoices as $inv) {
				$csv[]=$inv->no.','.$inv->date.','.$inv->getStatus().','.$inv->total.','.$inv->realPaid().','.$inv->getCredit().','.$inv->getBalance();
				$subTotal1+=$inv->getBalance();
			}
			$csv[]='subTotal,,,,,,'.$subTotal1;
			$csv[]='';
			$csv[]='';
			$subTotal2=0;
			$csv[]='Unpaid Invoices before '.$start;
			$invoices= Invoice::model()->findAll('to_id=:org_id AND date<:start AND status IN(2,3,7) ORDER BY date', [':org_id'=>$oid,':start'=>$start]);
			foreach ($invoices as $inv) {
				$csv[]=$inv->no.','.$inv->date.','.$inv->getStatus().','.$inv->total.','.$inv->realPaid().','.$inv->getCredit().','.$inv->getBalance();
				$subTotal2+=$inv->getBalance();
			}
			$csv[]='subTotal,,,,,,'.$subTotal2;
			$total=$subTotal1+$subTotal2;
			$csv[]='Total,,,,,,'.$total;
		}
		$fileName=$file.DIRECTORY_SEPARATOR.$org->name.'_'.date('Y-m-d').'_invoice_statment.csv';
		file_put_contents($fileName, implode("\n", $csv));
		$files[]=[$fileName, basename($fileName)];
	}
		
	public function updateTheInvoiceStatus()
	{
		$date=$this->prompt('date:');
		$invoices= Invoice::model()->findAll('status=9 AND date like :date', [':date'=>$date."%"]);
		foreach ($invoices as $inv) {
			$inv->checkPaid();
			$inv->update('status');
			echo $inv->no;
		}
	}
		
	public function testsendmsg()
	{
		$shipment= ImParcel::model()->findByPk(3286611);
		$shipment->msgNotice();
		echo 'send!';
	}
		
	public function getConsolCharge()
	{
		$no=$this->prompt('consol:');
		$consol= ImcoConsol::model()->find('no=:no', [':no'=>trim($no)]);
		$total=0;
		foreach ($consol->shipments as $shipment) {
			$total+= floatval($shipment->mdata['charge_client_amount']);
		}
		echo $total.PHP_EOL;
	}
	public function getStartrackResult()
	{
		$id=$this->prompt('startrack shipment id:');
		$hbn=$this->prompt('shipment hbn:');
		$shipment= ImParcel::model()->find('hbn=:hbn', [':hbn'=>trim($hbn)]);
		if (!empty($shipment)) {
			$ss = new StarTrackAPI('syd', true);
			$result = $ss->getShipments($id);
			$result_item=[];
			if ($result && isset($result->shipments) && is_array($result->shipments)) {
				$result = $result->shipments[0];
				$shipment->mdata['ss_shipment_id'] = $result->shipment_id;
				foreach ($result->items as $item) {
					if (!empty($item->tracking_details->article_id)) {
						if (preg_match('/7RFZ\d{8}EXP(\d{5})/i', $item->tracking_details->article_id, $match)) {
							$result_item[intval($match[1]-1)]=$item->item_id;
						}
					}
				}
				ksort($result_item);
				$shipment->mdata['ss_shipment_items']=$result_item;
				$shipment->updateMeta();
				var_dump($result_item);
			}
		}
	}
	public function createAupostShipment()
	{
		$hbn= 'ECN1222002665';
		$shipment= ImParcel::model()->find('hbn=:hbn', [':hbn'=>$hbn]);
		$aupostApi= new AusPostAPI('stone', true);
		//         $aupostApi= new AusPostAPI('bne', true);
		//         $aupostApi= new AusPostAPI('mel', true);
		//         $aupostApi= new AusPostAPI('syd', true);
		 
		$result= $aupostApi->createShipments($shipment);
		//         $ids='AMQ4163899';
		//         $result=$aupostApi->trackItems($ids);
		var_dump($result);
	}
	
	public function checkHasTickets()
	{
		$no='3286468';
		var_dump(ExCrm::hasTickets($no, ExCrm::TYPE_COMPENSATION, true));
	}
	public function getTheCourierCost()
	{
		$ref=$this->prompt(':ref');
		$shipment= ImParcel::model()->find('ref=:ref', [':ref'=>$ref]);
		echo $shipment->getCouiercost();
	}
	public function fastwayWeightTest1()
	{
		$fastway=new FastwayAPI();
		for ($i=0;$i<100000;$i++) {
			$w=rand(0, 100)*3/100+5;
			if (($f=$fastway->roundWeight($w))<=$w) {
				echo  $f."\t".$w."\n";
			}
		}
	}
		
	public function getExparcelValue()
	{
		$hbn=$this->prompt(':hbn');
		$p= ExParcel::model()->find('hbn=:hbn', [':hbn'=>$hbn]);
		echo $p->getDvalue().PHP_EOL;
	}
		
	public function getExCrmTicket()
	{
		$no=$this->prompt(':no');
		$ticket= ExCrm::model()->find('no=:no', [':no'=>trim($no)]);
		echo $ticket->getPortClaimValue();
	}
		
	public function fetchEncryptedMsg()
	{
		$msg_enc = $this->tmp.'1527753448.M652215P2001293.titan.orite.com,S=8421,W=8562_2,';
		$msg_sig = $this->tmp.'bb.txt';
		echo 3;
		openssl_pkcs7_decrypt($msg_enc, $msg_sig, Yii::app()->params['ics']['enc_pem'], [Yii::app()->params['ics']['enc_pem'], Yii::app()->params['ics']['pem_pwd']]);
//    unlink($msg_enc);
		$a= file_get_contents($msg_sig);
		$b=explode(PHP_EOL.PHP_EOL, $a, 2);
		echo base64_decode(trim($b[1]));
//    $this->getAttachedEDI($mid, $header, $msg_sig);
	}
	public function changeConsolStatus()
	{
		$id=$this->prompt("id:");
		$consol= Consol::model()->findbyPk(trim($id));
		if (!empty($consol)) {
			foreach ($consol->shipments as $p) {
				$p->status=10;
				$p->update('status');
			}
			$consol->save();
			echo 'done!';
		} else {
			echo 'empty!';
		}
	}
	public function doTheReportForNew()
	{
		$file= $this->tmp."report.xlsx";
		$xsl=new oExcel;
		$i=1;
		$xsl->addRow($i++, ["date",'Sydeny cost',' sydey weight',' sydney number','mel cost','mel weight','mel number']);
		$xsl->getActiveSheet()->getStyle('A1:A'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$xsl->output($file, false, false);
	}
	
	
	private function getCourierCostByShipment1($weight, $postcode)
	{
		// make it fast we hardcode here currently
		$oid=101;
				
		$options=[[3,101],[4,110]];
		$sydPrice=0;
		$melPrice=0;
		foreach ($options as $op) {
			$price = 0;
			// base on postcode to get related charge code
			$zoneMap = ZoneMap::model()->find(
					'org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code',
				[':oid' =>$oid, ':zoneid' => $op[0], ':code' =>$postcode]
				 );
			// if not found we set as NSW country
			$chargeCode = 'N1';
			if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
				$chargeCode = $zoneMap['z1'];
			}
			$zrs = ZoneRate::model()->findAll(
			"zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ",
				[':s' => $chargeCode, ':w' => $weight, ':rateid' => $op[1]]
		);

			if (!empty($zrs)) {
				foreach ($zrs as $zr) {
					$temp = $zr['base'] + $zr['item'];
					if ($zr['nkg'] > 0) {
						$wl = $weight - ($zr['base'] > 0 ? $zr['nkg'] : 0);
						$temp += ceil($wl / $zr['nkg']) * $zr['perkg'];
					} else {
						$temp += $weight * $zr['perkg'];
					}
					if ($zr['minimum'] > 0 && $temp < $zr['minimum']) {
						$temp = $zr['minimum'];
					}
					$price = max($price, $temp);
				}
			}
			if ($op[1]==101) {
				$sydPrice=$price;
			} else {
				$melPrice=$price;
			}
		}
		if ($sydPrice<=$melPrice) {
			return [$sydPrice,101];
		} else {
			return [$melPrice,110];
		}
	}
		
		
	public function testD2zApi()
	{
		$shipment= ImParcel::model()->find('ref="AMQ3286711"');
		//            $shipment1= ImParcel::model()->find('ref="AMQ3286691"');
		$d2z=new D2zApi(false, false);
		$d2z->createShipment([$shipment]);
		//            $d2z->getLabelInfo([$shipment]);
//           $d2z->manifest([$shipment]);
	}
		
	public function updateStartrackid()
	{
		$shipment= ImParcel::model()->find('ref=:ref', [':ref'=>trim($this->prompt(":ref"))]);
		$shipment->mdata['ss_shipment_id'] = "qJkK0EgEH7sAAAFjXdMcvM6V";
		$shipment->mdata['ss_lbl_request_id'] = "qJkK0EgEH7sAAAFjXdMcvM6V";
		$shipment->updateMeta();
	}
		
	public function updateBillingLine()
	{
		$id=$this->prompt(":id");
		$p= BillingLine::model()->findByPk($id);
		$p->mdata['from_rec']=1;
		$p->updateMeta();
		echo 'done'.PHP_EOL;
	}
	public function testTheTransaction()
	{
		$model= ImParcel::model()->find('ref="JDQ0011641"');
		//         var_dump(PDO::getAvailableDrivers());
		//          $pdo=new NestedPDO();
		//          echo $pdo->nestable();
		//          PDO::ATTR_DRIVER_NAME
		$transaction=Yii::app()->db->beginTransaction();
		try {
			$model->mdata['test_tran_2']=11;
			$model->updateMeta();
			$innerTransaciton=Yii::app()->db->beginTransaction();
			try {
				$p= ImParcel::model()->find('ref="AMQ3286744"');
				$p->pkg=7;
				$p->save();
				$innerTransaciton->commit();
			} catch (Exception $ex) {
				echo $ex->getMessage()."Inner";
				$innerTransaciton->rollback();
			}
			$transaction->commit();
		} catch (Exception $ex) {
			echo $ex->getMessage()."=>Outer";
			$transaction->rollback();
		}
	}
	public function unsetAupsot()
	{
		$no=$this->prompt("Consol:");
		$consol= ImcoConsol::model()->find('no=:no', [':no'=>$no]);
		$transaction=Yii::app()->db->beginTransaction();
		try {
			foreach ($consol->shipments as $p) {
				if (preg_match('/^AMQ\d{7}|^333UF\d{7}|^33EVJ\d{7}|^33EVH\d{7}/', $p->ref)||(!empty($p->trans)&&$p->trans[0]->org_id==101)) {
					unset($p->mdata['import_billing_id']);
					$p->updateMeta();
				}
			}
			$transaction->commit();
		} catch (Exception $ex) {
			$transaction->rollback;
		}
		$consol->updateAupostRealCost();
		echo 'done';
	}
	 
	public function changePodLink()
	{
		$p= ImParcel::model()->find('hbn=:hbn', [':hbn'=> trim($this->prompt(':hbn'))]);
		if (!empty($p)) {
			$p->updatePodTrackingUrl();
		}
	}
		
	public function checkthepostcode()
	{
		$api= new AusPostAPI('syd');
			
		$result=$api->validSuburb('BAGOT', 'NT', "0820");
		var_dump($result);
	}
	public function connectTheMail()
	{
		$mailFetcher=new MailFetcher();
		//          $mailFetcher=new icsFetcher();
		$mailFetcher->fetchMsgFor();
	}
		
	public function updateMailContent()
	{
		$rs= ImportsMail::model()->findAll('id>0');
		foreach ($rs as $r) {
			MailBody::genMailBody($r->id, $r->body);
		}
	}
		
	public function clearMailBox()
	{
		$mailFetcher=new MailFetcher();
		$mailFetcher->clearHistoryMailBox();
	}
		
	public function testStartrack()
	{
		$hbn="ZK610262391";
		$shipment= ImParcel::model()->find('hbn=:hbn', [':hbn'=>$hbn]);
		$statrackApi= new StarTrackAPI('test', true, true);
		$statrackApi->createShipments($shipment);
			
		echo 1;
	}
		
	public function doTheMailCheckType()
	{
		$no=$this->prompt('no:');
		$r=ImportsMail::model()->find('no=:no', [':no'=>$no]);
		$r->checkTheType();
		echo $r->type.PHP_EOL;
	}
	public function checkOrgOverLimit()
	{
		$org=Org::model()->findByPk(1222);
		
		var_dump($org->overCreditLimit());
	}
	public function testFtp()
	{
		$fileName= $this->tmp."cc.txt";
		D2zCountryRate::upLoadManifestFile($fileName);
	}
	public function testFile()
	{
		$r= ImParcel::model()->find('ref=:ref', [':ref'=>'33A8Y9000015']);
		$rs=[$r];
		D2zCountryRate::manifest($rs, 'testconsol');
	}
	public function syncTollTrack()
	{
		$toll = new TollAPI();
		$toll->syncTracking();
	}
	public function updateMailUser()
	{
		$rs=ImportsMail::model()->findAll(1);
		$transaction=Yii::app()->db->beginTransaction();
		try {
			foreach ($rs as $r) {
				$us=ImportsMailUserMap::model()->findAll('type_id=:type', [':type'=>$r->type]);
				if (!empty($us)) {
					foreach ($us as $u) {
						$record=MailUser::model()->find('mail_id=:mail_id AND user_id=:user_id', [':mail_id'=>$r->id,':user_id'=>$u->user_id]);
						if (empty($record)) {
							$record=new MailUser();
							$record->mail_id=$r->id;
							$record->user_id=$u->user_id;
							$record->create_time=$r->create_time;
							$record->mail_type=$r->type;
							if ($r->status>10) {
								$record->status=60;
								$record->close_time=$r->date;
							} else {
								$record->status=10;
							}
							$record->save();
						}
					}
				}
			}
			$transaction->commit();
		} catch (Exception $ex) {
			$transaction->rollback();
		}
	}
		
	public function genD2zNo()
	{
		echo D2zCountryRate::genNo();
	}
	public function genLabelNumber()
	{
		$org=Org::model()->findByPk('1222');
		echo $org->getLabelNumber();
	}
		
	public function genBFENo()
	{
		echo ImParcel::genStoneBridgeNo();
	}
		
	public function reprintStartracklabel()
	{
		$ss= new StarTrackAPI('syd', true);
		$ss->createLabels(["1kkK0E.z_rUAAAFkwnhuhZLo"]);
	}
	public function decodethemail()
	{
		require_once(Yii::app()->basePath.'/extensions/mimeparser/rfc822_addresses.php');
		require_once(Yii::app()->basePath.'/extensions/mimeparser/mime_parser.php');
		include_once('PHPMailer/class.phpmailer.php');
		require_once(Yii::app()->basePath.'/vendor/autoload.php');
		$file= $this->tmp.DIRECTORY_SEPARATOR.'importsmail'.DIRECTORY_SEPARATOR."EM0015720.eml";
		$data= file_get_contents($file);
		//                $mime=new mime_parser_class;
//    $mime->ignore_syntax_errors = 1;
//    $parameters=array(
//      'Data'=>$data,
//    );
//    $mime->Decode($parameters, $decoded);
		//                var_dump($decoded);
		$parser = new PhpMimeMailParser\Parser();
		$parser->setText($data);
		//            if(preg_match("/multipart\/alternative/i",$parser->getHeader('content-type'))){
		//                 $html=$parser->getMessageBody('text');
		//                 if(preg_match('/\</i', $html,$matched,PREG_OFFSET_CAPTURE)){
		//                    $headBody=substr($html,0,$matched[0][1]);
		//                 }
		//                echo $headBody;
		//            }
		$html= $parser->getMessageBody('html');
		$html=preg_replace('/charset=[^"^\']*/', 'charset=utf9', $html);
		echo $html;
	}
		
	public function genTntNo()
	{
		echo TntAPI::genConsignmentNo();
	}
		
	public function testD2zformat()
	{
		$p= ImParcel::model()->find('ref="333UF3286861"');
		D2zCountryRate::manifest([$p], '11111');
		echo ' done!';
	}
	public function getStatrackAccountInfo()
	{
		$ss= new StarTrackAPI('mel', true);
			
		var_dump($ss->getAccounts($id=''));
	}		
		
	public function showDimensions()
	{
		$shipment=ImParcel::model()->find('hbn=:hbn', [':hbn'=>trim($this->prompt('consol hbn:'))]);
		var_dump($shipment->getPackDimensions());
	}
		
	public function testTnt()
	{
		$shipment= ImParcel::model()->find('hbn=:hbn', [':hbn'=>'ECN1222002832']);
		//            $shipment2= ImParcel::model()->find('hbn=:hbn',array(':hbn'=>'ECN1222002830'));
		$tnt= TntAPI::getTntInterface('test');
		echo $tnt->createShipment($shipment);
	}
		
	public function testTntLabel()
	{
		$shipment= ImParcel::model()->find('hbn=:hbn', [':hbn'=>'ECN1222002830']);
		$tnt= TntAPI::getTntInterface('test');
		$tnt->rePrintLabel($shipment);
	}
		
	public function getTntPrice()
	{
		$shipment= ImParcel::model()->find('hbn=:hbn', [':hbn'=>'ECN1222002830']);
		$tnt= TntAPI::getTntInterface('test');
		$result=$tnt->getRrtPrice($shipment);
		var_dump($result);
	}
		
	public function recordHistory()
	{
		$date=date('Y-m-d');
		MailHistoryRecord::genHistoryRecord($date);
	}
	public function getHeldTime()
	{
		$p= ImParcel::model()->find('hbn=:hbn', [':hbn'=>'ECN1222002842']);
		var_dump($p->getHeldTrackingTime());
	}
		
	public function scanOneShipment()
	{
		$p= ImParcel::model()->find('hbn=:hbn', [':hbn'=>trim($this->prompt('hbn:'))]);
		$st = time() - rand(2000, 6000);
		if ($p->pkg == $p->getOutPkg()) {
			return;
		}
		for ($j = 1; $j <= $p->pkg; $j++) {
			if (!empty($p->scan_data[50][$p->hbn.'-'.$j])) {
				continue;
			}
			$pt = $st + rand(10, 100 * $j);
			$p->scan_data[50][$p->hbn.'-'.$j] = date('Y-m-d H:i:s', $pt);
		}
		if (empty($p->mdata['scan_time'])) {
			$p->mdata['scan_time'] = date('Y-m-d H:i:s', $st);
		}
		$p->updateUnScan();
		$p->save();
		echo 'done!';
	}
		
		
	public function testScanNo()
	{
		$transaction=Yii::app()->db->beginTransaction();
		try {
			for ($index=1;$index<20;$index++) {
				echo ConnoteRange::newNumber('Org', 121);
				sleep(1);
			}
			$transaction->commit();
		} catch (Exception $ex) {
			$transaction->rollback();
		}
	}
		
		
	public function updateProcessData()
	{
		$rs= ShipmentProcess::model()->findAll('1');
		$transaction=Yii::app()->db->beginTransaction();
		try {
			foreach ($rs as $r) {
				$shipment= ImParcel::model()->findByPk($r->pid);
				if (!empty($shipment)) {
					$r->s_agent_id=$shipment->agent_id;
					$r->s_bwf=$shipment->bwf;
					$r->consol_id=$shipment->consol_id;
					$r->consol_eta=@$shipment->consol->eta;
					$r->save();
				}
			}
			$transaction->commit();
		} catch (Exception $ex) {
			$transaction->rollback();
		}
	}
		
	public function testSendEmail()
	{
		Emailog::sendEmailTo("gero@toplogistics.com.au", "Hello", "hellow");
	}
		
	public function testReportDetail()
	{
		$consol= ImcoConsol::model()->find('no=:no', [':no'=> trim($this->prompt('no:'))]);
		$result=$consol->getReportDetailBaseOnAgent();

		var_dump($result);
	}
	public function testInvoiceShipmentCost()
	{
		$shipment= ImParcel::model()->find('ref=:ref', [':ref'=>trim($this->prompt('ref:'))]);
		$result=$this->getCourierCostByShipment2($shipment->ref, ImportChargeCode::STARTRACK_MEL);
		var_dump($result);
	}
	public function getCourierCostByShipment2($shipmentRef, $org_rate_id, $chargeWeight=null)
	{
		$orgRate= OrgRate::model()->findByPk($org_rate_id);
		$price = 0;
		$consolId = 0;
		$shipment = Shipment::model()->find('ref = :ref', [':ref' => $shipmentRef]);
		if (!empty($shipment)) {
			$consolId = $shipment->consol_id;
			if (empty($chargeWeight)) {
				$weight = $shipment->weight;
			} else {
				$weight=$chargeWeight;
			}
			$zoneMap = ZoneMap::model()->find(
						'org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code',
								 [':oid' =>$orgRate->org_id, ':zoneid' => $orgRate->zone_id, ':code' => $shipment->cnee->postcode]
					 );
			$chargeCode = 'N1';
			if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
				$chargeCode = $zoneMap['z1'];
			}
			// base charge code to get zone rate
			$zrs = ZoneRate::model()->findAll(
				"zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ",
				[':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRate->id]
			);
			if (!empty($zrs)) {
				foreach ($zrs as $zr) {
					$temp = $zr['base'] + $zr['item'];
					if ($zr['nkg'] > 0) {
						$wl = $weight - ($zr['base'] > 0 ? $zr['nkg'] : 0);
						$temp += ceil($wl / $zr['nkg']) * $zr['perkg'];
					} else {
						$temp += $weight * $zr['perkg'];
					}
					if ($zr['minimum'] > 0 && $temp < $zr['minimum']) {
						$temp = $zr['minimum'];
					}
					$price = max($price, $temp);
				}
			}
		}
		return ['price' => $price, 'consol_id' => $consolId];
	}
		
	public function testHasProcess()
	{
		$shipment= ImParcel::model()->find('ref=:ref', [':ref'=>trim($this->prompt('ref:'))]);
		var_dump($shipment->hasCustomProcess());
	}
			
	public function dealwithDutyHeld()
	{
		$shipments=ImParcel::model()->findAll('status=57');
		foreach ($shipments as $p) {
			if ($p->canSAC(true)) {
				$p->status=60;
				$p->update('status');
				echo $p->ref.PHP_EOL;
			}
		}
	}
		 
	public function getTheParcelCost()
	{
		$incomsol=new ImcoConsol();
		$orgRate= OrgRate::model()->findByPk($this->prompt('Org Rate Id:'));
		$postcode= $this->prompt('postcode:');
		$weight= $this->prompt('weight:');
		$cost= $incomsol->getCourierCostPrice($orgRate, $postcode, $weight, false);
		echo $cost.PHP_EOL;
	}
		 
	public function testPoli()
	{
		$file= $this->tmp.'a.txt';
		$content= file_get_contents($file);
		$data= json_decode($content, true);
		$amount=$data['PaymentAmount'];
		$shipment= ImParcel::model()->find('ref=:ref', [':ref'=>'QH0000701361']);
		$shipment->receivedPaymentByPoli($amount, $data);
	}
	public function testTheScanDate()
	{
		$shipment= ImParcel::model()->find('ref=:ref', [':ref'=>trim($this->prompt('ref:'))]);
		echo $shipment->findTheStorageDate();
	}
	public function updateConsolCourierCost()
	{
		$imconsol= ImcoConsol::model()->find('no=:no', [':no'=>trim($this->prompt('consol:no'))]);
		$imconsol->updateD2zRealCost();
	}
		 
		 
	public function testStorageInvoice()
	{
		$shipment= ImParcel::model()->find('ref=:ref', [':ref'=>trim($this->prompt('ref:'))]);
		if (!empty($shipment)) {
			$result=$shipment->createStorageInvoiceWhenDdu();
			var_dump($result);
		}
			 
		echo 'done!';
	}
		 
	public function genDduStorageWhenNotPaid()
	{
		$processes= ShipmentProcess::model()->findAll('status=:status AND DATEDIFF(NOW(),`date`)>=7', [':status'=> ShipmentProcess::INFROM_CUSTOMER_PAY]);
		foreach ($processes as $process) {
			$shipment=$process->shipment;
			if (!empty($shipment->mdata['ddu_storage_invoice_date'])) {
				$inTime = new DateTime($shipment->mdata['ddu_storage_invoice_date'], new DateTimeZone('Australia/Sydney'));
				$outTime = new DateTime('now', new DateTimeZone('Australia/Sydney'));
				$storedDays = $outTime->diff($inTime)->format("%a");
				$week=floor($storedDays/7);
				if ($week>0) {
					$invoice=new Invoice();
					$dueDay=($week+1)*7-1;
					$rate=1.4;
					$amount=$rate*$week*$shipment->weight; //currently $1.4/week
							$invoice->date = date('Y-m-d'); // for update we shouldn't change invoice data
						$invoice->status = Invoice::INVOICE_STATUS_PENDING; // set as pending which means will send to client for paying via email at night
							$invoice->type = Invoice::INVOICE_TYPE_DDU_STORAGE; // for custom casual client /DDU
							$invoice->dpmt = Invoice::DPMT_IMPORT;
					$invoice->currency = 1;
					$invoice->pid= $shipment->id;
					$invoice->to_id = 1228;//casual client org
					$invoice->consol_id = $shipment->consol_id;
					$invoice->mdata['name'] = !empty($shipment->cnee->company) ? $shipment->cnee->company : $shipment->cnee->name;
					$invoice->mdata['address'] = $shipment->cnee->fullAddress();
					$invoice->mdata['payterm'] = 'COD';
					$invoice->mdata['awb'] = isset($shipment->consol->awb) ? $shipment->consol->awb : '';
					$invoice->due =date('Y-m-d', strtotime("+$dueDay day", strtotime($shipment->mdata['ddu_storage_invoice_date'])));//we need a due day...
					$invoice->gst = 0;
					$invoice->total=0;
					$items[] = [$shipment->consol->awb, $shipment->hbn, $shipment->getDesc(), $shipment->pkg, $shipment->weight, $shipment->cbm, $amount, $shipment->ref,$rate, 0, $week, $shipment->mdata['ddu_storage_invoice_date'], date('Y-m-d')];
					$gst = 0;
					$org = Org::model()->findByPk(1228);
					if (isset($org) && isset($org->extra['incl_gst'])) {
						if ($org->extra['incl_gst'] == 1) {
							$gst = round($amount * 10 / 100, 2);
						}
					}
					if ($amount>0) {
						$invoice->total += $amount;
						$invoice->gst += $gst;
						$invoice->total += $gst;
						$invoice->save();
						$shipment->mdata['ddu_storage_invoice_date']=date('Y-m-d');
						$shipment->updateMeta(true);
						$il = new InvLine;
						$il->inv_id = $invoice->id;
						$il->amount = $amount;
						$il->amount += $gst;
						$il->gst = $gst;
						$il->qty = 1;
						$il->mdata['items'] = $items;
						$il->model = 'Manifest';
						$il->fid = 0;
						$il->save();
					}
				}
			}
		}
	}
		 
	public function testSupPay()
	{
		$id=3286877;
		$shipment= ImParcel::model()->findByPk($id);
		$trnInfo=[
			'notice_id'=>123111223,
			'amount'=>194
		];
		$type='SUPAY';
		$this->afterPaymentPaid($shipment, $trnInfo, $type);
	}
		 
	private function afterPaymentPaid($shipment, $trnInfo, $type)
	{
		if (!empty($trnInfo['notice_id'])) {
			$trn = $trnInfo['notice_id'];
			$amount = $trnInfo['amount'];
		}

		$payment_gateway_history_model = PaymentGatewayHistory::model()->find('trn = :trn', [':trn' => $trn]);
		if (!isset($payment_gateway_history_model)) {
			$payment_gateway_history_model = new PaymentGatewayHistory();
			$payment_gateway_history_model->trn = $trn;
			$payment_gateway_history_model->pid = $shipment->id;
			$payment_gateway_history_model->type = constant('PaymentGatewayHistory::PAYMENT_GATEWAY_TYPE_'. $type);
			$payment_gateway_history_model->token = $trn;
			$payment_gateway_history_model->meta = json_encode($trnInfo);
			$payment_gateway_history_model->result = 'success';
			$payment_gateway_history_model->payed_time = new CDbExpression('NOW()');
			$payment_gateway_history_model->save();
		} else {
			if ($payment_gateway_history_model->result == 'success' && !empty($payment_gateway_history_model->payed_time)) {
				echo 'you have paid the invoice successfully already before!';
				return false;
			}
		}
		$invoices = Invoice::model()->findAll('pid=:pid AND status!=10', [':pid'=>$shipment->id]);
		$paidAmount=$amount;
		if (!empty($paidAmount)) {
			$transaction=Yii::app()->db->beginTransaction();
			try {
				$shipment->receivedPaymentBySubPay($paidAmount, $trn);
				$transaction->commit();
				return true;
			} catch (Exception $ex) {
				$transaction->rollback();
				throw  $ex;
			}
		}
		return false;
	}
	
	public function testTheFindConsignee()
	{
		$ref=$this->prompt('ref:');
		$p= ImParcel::model()->find('ref=:ref', [':ref'=>$ref]);
		if (!empty($p)) {
			$result=$p->process->findRelatedConsignee();
			var_dump($result);
		}
	}
	public function updateParcelWeight()
	{
		$fileName=$this->prompt('file name:');
		$file=$this->tmp.'forau'.DIRECTORY_SEPARATOR.$fileName.'.xlsx';
		$xls=new oExcel();
		$xls->supported($file);
		$xls->load($file);
		$data=$xls->getAll();
		foreach ($data as $d) {
			if (empty($d[1])&&empty($d[2])) {
				continue;
			}
			$p=ImParcel::model()->find('ref=:ref', [':ref'=>trim($d[1])]);
			if (!empty($p)) {
				$p->weight= floatval($d[2]);
				$p->update('weight');
				echo $p->ref." updated!";
			} else {
				echo $d[1]." not found!".PHP_EOL;
			}
		}
	}
	
	public function unsetTheBillingFlat()
	{
		$ref= $this->prompt('ref:');
		$p= ImParcel::model()->find('ref=:ref', [':ref'=>$ref]);
		if (!empty($p)) {
			unset($p->mdata['import_billing_id']);
			$p->updateMeta();
			echo 'done!'.PHP_EOL;
		}
	}
	public function updateConsolFastwayBilling()
	{
		$no= $this->prompt('consol no:');
		$consol= Consol::model()->find('no=:no', [':no'=>$no]);
		$consol->updateFastWayRealCost();
		$consol->updateAupostRealCost();
		echo 'done!';
	}
	public function updateScanInfo()
	{
		ShipmentScanOverview::updateData();
		echo ' done!';
	}
	public function genTntTransactionNumber()
	{
		//        $shipment= ImParcel::model()->find('ref=:ref',array(':ref'=>'DKC000000069'));
		//        $shipment2= ImParcel::model()->find('ref=:ref',array(':ref'=>'DKC000000048'));
		$tnt= TntAPI::getTntInterface('test');
		//        $tnt->preEt12File([$shipment,$shipment2]);
	}
	
	public function createOneTntshipment()
	{
		$shipment= ImParcel::model()->find('hbn=:hbn', [':hbn'=>'ECN1206000111']);
		$shipment1= ImParcel::model()->find('hbn=:hbn', [':hbn'=>'ECN1206000110']);
		$tnt= TntAPI::getTntInterface('test');
		//        $result=$tnt->createOneShipment($shipment);
		//        var_dump($result);
		$tnt->preEt12File([$shipment,$shipment1]);
	}
	public function checkSuburbOnDeliveryZone()
	{
		$tnt= TntAPI::getTntInterface('syd');
		$suburb="DARWIN";
		$postcode="0800";
		var_dump($tnt->checkIfDelivery($suburb, $postcode));
	}
	public function tntTracking()
	{
		$tnt= TntAPI::getTntInterface('syd');
		$data=$tnt->trackingTnt("DKC999999902");
		var_dump($data);
	}
	
	public function checkTntCost()
	{
		$shipment= ImParcel::model()->find('hbn=:hbn', [':hbn'=>'ECN1206000110']);
		$tnt= TntAPI::getTntInterface('syd');
		echo $tnt->getTntCost($shipment);
	}
	
	public function testFastWayForD2ZCost()
	{
		$imconsol=new ImcoConsol();
		$orgRate= OrgRate::model()->findByPk(ImportChargeCode::FASTWAY_D2Z);
		$postcode= $this->prompt('Postcode:');
		$weight=$this->prompt('Weight:');
		$price=$imconsol->getCourierCostPrice($orgRate, $postcode, $weight, true);
		echo $price.PHP_EOL;
	}
	
	public function getSeaParcelUrl()
	{
		$shipment= ImParcel::model()->find('hbn=:hbn', [':hbn'=>trim($this->prompt('hbn:'))]);
		var_dump($shipment->getSeaParcelInfo());
	}
	public function testTheWareshouse()
	{
		var_dump(ShipmentScan::getTodayScanInfo());
	}
}
