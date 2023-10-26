<?php

class PackController extends Controller
{

	/**
	 * @return array action filters
	 */

	public function actionIndex()
	{
		$this->render("site/index");
	}

	/**
	 * No longer use
	 */
	public function actionPack()
	{
		$this->render("pack");
	}

	/**
	 * Show task item details
	 */
	public function actionDetail()
	{
		$taskId = $_GET["taskid"];
		$temp_taskId = $taskId;

		if (strtoupper(substr($taskId, 0, 1)) == "T") {
			// task 二维码
			$taskId = substr($taskId, 1);
			$task = WmsTask::model()->findByPk($taskId);
		} else {
			// 双面单
			$material = $_GET["material"];
			$weight = $_GET["weight"];
			if ($weight > 0.1 && $material == "是") {
				$task = WmsTask::model()->find('ref = :ref', array(':ref' => $taskId));
				$ac_task = $task->actionTask;
				foreach ($task->items as $item) {
					$stock = WmsStock::model()->findByPk($item->mdata['si']);
					$sls = $stock->locs;
					$qty = $item->mdata['uq'];
					foreach ($sls as $sl) {
						if ($qty) {
							$itm = new WmsTaskItem;
							$itm->task_id = $ac_task->id;
							$itm->mdata = array(
								'si' => $item->mdata['si'],
								'sn' => $item->mdata['sn'],
								'uq' => number_format(($qty > $sl->qty ? $sl->qty : $qty), 0),
								'pli' => $sl->loc->id,
								'pl' => $sl->loc->code,
							);
							$itm->save();
							$qty -= $qty > $sl->qty ? $sl->qty : $qty;
						}
					}
				}
				$ac_task->compl_time = date('Y-m-d H:i:s');
				$ac_task->save();
				$task->status = WmsTask::STATUS_WIP;
				$task->save();

				$pack_task = WmsTask::model()->find('link_id = :link_id AND type = :type', array(':link_id' => $task->id, 'type' => WmsTask::TYPE_PACK_ORDER));
				$meta = json_decode($task->meta, true);
				if ($meta && array_key_exists("pkg", $meta)) {
					$pkg = json_decode($meta['pkg'], true);
				} else {
					$pkg = array();
				}

				$parcelId = "P" . sprintf("%06d", $task->id) . sprintf("%03d", count($pkg) + 1);
				$pkg[] = array("wt" => $weight, "w" => "", "h" => "", "d" => "", "nt" => $material . " " . date("Y-m-d H:i:s", time()) . " " . $parcelId);
				$meta["pkg"] = json_encode($pkg);
				$pack_task->mdata = $meta;
				$pack_task->save();
			}
		}

		if (!empty($task) && in_array($task->status, [WmsTask::STATUS_WIP, WmsTask::STATUS_HOLD]) && in_array($task->type, [WmsTask::TYPE_PICK_PALLET, WmsTask::TYPE_PICK_CARTON, WmsTask::TYPE_PICK_UNIT]) && $task->is_request) {
			$products = array();
			$note = "";
			foreach ($task->items as $taskItem) {
				$meta = json_decode($taskItem->meta, true);

				$stock = WmsStock::model()->findByPk($meta["si"]);
				if (empty($stock)) continue;

				$product = WmsProd::model()->findByPk($stock->prod_id);
				$product = json_decode(json_encode($product->attributes), true);

				$product["pq"] = isset($meta["pq"]) && $meta["pq"] ? $meta["pq"] : 0;
				$product["cq"] = isset($meta["cq"]) && $meta["cq"] ? $meta["cq"] : 0;
				$product["uq"] = isset($meta["uq"]) && $meta["uq"] ? $meta["uq"] : 0;
				$product["note"] = isset($meta["nt"]) && $meta["nt"] ? $meta["nt"] : "无特殊要求";
				$product["taskid"] = $temp_taskId;

				if ($product['pq'] == 0 && $product['cq'] == 0 && $product['uq'] == 0) {
					continue;
				}

				if (empty($products[$product["name"]])) {
					$products[$product["name"]] = $product;
				} else {
					$products[$product["name"]]['uq'] += $product['uq'];
				}
			}

			foreach ($task->actionTask->items as $item) {
				if (empty($item->mdata['short'])) continue;

				$stock = WmsStock::model()->findByPk($item->mdata['si']);
				if (empty($stock)) continue;

				if (!empty($products[$stock->prod->name])) {
					$products[$stock->prod->name]['uq'] -= $item->mdata['uq'];
				}
			}

			$pack_task = WmsTask::model()->find("link_id = :taskid and type = :type", array(":taskid" => $task->id, ":type" => WmsTask::TYPE_PACK_ORDER));
			$meta = json_decode($pack_task->meta, true);
			if ($pack_task->ref) {
				$markstatus = $pack_task->ref;
			} else {
				$markstatus = "null";
			}

			if ($meta && array_key_exists("pkg", $meta)) {
				$pkgs = json_decode($meta["pkg"], true);
				foreach ($pkgs as $index => $pkg) {
					if ($pkg["wt"] && $pkg["nt"]) {
						$pkgs[$index] = array("id" => explode(" ", $pkg["nt"])[3], "taskid" => $temp_taskId, "date" => explode(" ", $pkg["nt"])[1] . " " . explode(" ", $pkg["nt"])[2], "weight" => $pkg["wt"], "material" => explode(" ", $pkg["nt"])[0], "height" => $pkg["h"], "width" => $pkg["w"], "depth" => $pkg["d"]);
					} else {
						array_splice($pkgs, $index, 1);
					}
				}
			} else {
				$pkgs = array();
			}

			$delivery_task = WmsTask::model()->find("link_id = :taskid and type = :type", array(":taskid" => $task->id, ":type" => WmsTask::TYPE_DELIVERY));
			if ($delivery_task) {
				$labelstatus = ($delivery_task->ref == '' || preg_match('/pca/i', $delivery_task->ref)) ? 'pca' : $delivery_task->ref;
			} else {
				$labelstatus = "null";
			}

			$rs = FileRepo::model()->findAll('fid = :taskid AND type IN (86,87)', [':taskid' => $task->id]);
			$files = [];
			foreach ($rs as $r) {
				$files[] = ['printer' => 'A4', 'type' => 'pdf', 'file' => base64_encode(file_get_contents($r->getFile()))];
			}
			if ($task->job->org_id == Org::ORGID_3PL_LIFESTYLE && preg_match('/SO/', $task->ref)) {
				oPDF::renderPDF('packing_list', array('model' => $task), 2, 'packing_list_' . $task->id . '.pdf');
				$files[] = ['printer' => 'A4', 'type' => 'pdf', 'file' => base64_encode(file_get_contents('packing_list_' . $task->id . '.pdf'))];
				unlink('packing_list_' . $task->id . '.pdf');
			}

			$this->renderPartial("taskdetail", array("result" => true, "task" => $task, "products" => $products, "parcels" => $pkgs, "labelstatus" => $labelstatus, "markstatus" => $markstatus, 'lsfile' => json_encode($files)));
		} else if ($task->status == WmsTask::STATUS_COMPLETED && isset($_GET['type']) && $_GET['type'] == 'reprint') {
			$rs = [];
			foreach ($task->deliveryTask->mdata['shipment_id'] as $sid) {
				$rs[] = Shipment::model()->findByPk($sid);
			}
			if (!empty($rs)) {
				if ($rs[0]->type == 10) {
					if (empty($rs[0]->id)) {
						print_r($rs[0]->getErrors());
					} else {
						$allData = "";
						foreach ($rs as $p) {
							for ($i = 1; $i <= $p->pkg; $i++) {
								$aid = $p->ref . sprintf('%02s', $i) . '00093' . '50' . '0';
								$aid .= AusPostAPI::aidChkDgt($aid);
								$said = str_replace('AMQ', '>6AMQ>5', $aid);
								$model = array(
									'agent_id' => $p->agent_id,
									'cnee_company' => $p->cnee->company,
									'cnee_name' => $p->cnee->name,
									'cnee_address' => $p->cnee->address,
									'cnee_state_postcode' => $p->cnee->suburb . ' ' . $p->cnee->state . ' ' . $p->cnee->postcode,
									'shipment' => $p,
									'cnee_phone' => $p->cnee->tel,
									'weight' => $p->weight / $p->pkg,
									'cnor_name' => $p->cnor->name,
									'cnor_address' => '6C The Crescent',
									'cnor_state_postcode' => 'KINGSGROVE NSW 2208',
									'parcel_ref' => $p->ref,
									'parcel_uni_no' => $p->hbn,
									'parcel_cref' => $p->cref,
									'parcel_qty' => round(@$totalQty / $p->pkg, 0),
									'parcel_index' => "$i/$p->pkg",
									'parcel_aupost_id' => $aid,
									'created' => $p->created,
									'parcel_aupost_barcode' => '>;>8019931265099999891' . $said,
									'parcel_aupost_matrix' => '019931265099999891' . $aid . '420' . $p->cnee->postcode . '8008' . date('ymdHis'),
								);
								if (isset($p->trans) && !empty($p->trans) && $p->trans[0]->org_id == Org::ORGID_COURIER_FASTWAY) {
									$allData .= $this->renderPartial('//_pdf/_label_fastway_zpl2020', ['label' => $p], true);
									$type = 'zpl';
								} else if (preg_match('/AMQ/i', $p->ref)) {
									$allData .= $this->renderPartial('//_pdf/_label_eparcel_zpl_2014_1', ['model' => $model], true);
									$type = 'zpl';
								} else if (preg_match('/7RFZ|DKC/i', $p->ref)) {
									$tf = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR, 'label');
									oPDF::renderPDF('label_A6', array('rs' => [$p]), 2, $tf);
									$allData .= base64_encode(file_get_contents($tf));
									unlink($tf);
									$type = 'pdf';
								} else if (!empty($p->trans) && $p->trans[sizeof($p->trans) - 1]->org_id == Org::ORGID_COURIER_SENDLE) {
									$sendle = new SendleAPI();
									$allData .= base64_encode($sendle->getLabel($p));
									$type = 'pdf';
								} else if (preg_match('/' . $task->getNo() . '/i', $p->ref)) {
									$tf = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR, 'label');
									oPDF::renderPDF('label_A6', ['rs' => $rs, 'tpl' => '_label_letter', 'default_rts' => true], 2, $tf);
									$allData = base64_encode(file_get_contents($tf));
									unlink($tf);
									$type = 'pdf';
									break;
								}
							}
						}

						$files = [];
						if (!empty($allData)) {
							$files[] = ['printer' => 'label', 'type' => $type, 'file' => $allData];
						}

						echo json_encode(array("result" => true, "msg" => "订单完成, 等待打印", "file" => json_encode($files)));
						Yii::app()->end();
					}
				} elseif ($rs[0]->type == 20) {
					$p = $rs[0];
					if (empty($p->id)) {
						print_r($p->getErrors());
					} else {
						$allData = "";
						$total_pkg = 0;
						foreach ($rs as $p) {
							$total_pkg += $p->pkg;
						}
						$k = 1;
						foreach ($rs as $p) {
							if (!empty($p->eitems['g'])) {
								$total = 0;
								foreach ($p->eitems['g'] as $i => $g) {
									$total += $p->eitems['q'][$i];
								}
							}
							for ($j = 1; $j <= $p->pkg; $j++, $k++) {
								if (!empty($p->eitems['g'])) {
									$gs = [];
									$pos = 0;
									foreach ($p->eitems['g'] as $i => $g) {
										if ($pos >= ceil($total * ($k - 1) / $total_pkg) && $pos < ceil($total * $k / $total_pkg)) {
											if (ceil($total * $k / $total_pkg) >= $p->eitems['q'][$i] + $pos) {
												$gs[] = $g . ' X ' . $p->eitems['q'][$i];
												$pos += $p->eitems['q'][$i];
											} else {
												$gs[] = $g . ' X ' . ceil($total * $k / $total_pkg - $pos);
												$pos += ceil($total * $k / $total_pkg) - $pos;
											}
										} else {
											if ($p->eitems['q'][$i] + $pos >= ceil($total * ($k - 1) / $total_pkg) && $pos < ceil($total * $k / $total_pkg)) {
												$gs[] = $g . ' X ' . ($p->eitems['q'][$i] + $pos - ceil($total * ($k - 1) / $total_pkg));
											}
											$pos += $p->eitems['q'][$i];
										}
									}
									$allData .= $this->renderPartial('//_pdf/_label_ex_zpl', array('label' => $p, 'gs' => implode("_0A", $gs), 'pkg_sn' => $j), true);
								}
							}
						}
						echo json_encode(array("result" => true, "msg" => "订单完成, 等待打印", "file" => $allData));
						Yii::app()->end();
					}
				}
			}
		} else {
			$this->renderPartial("taskdetail", array("result" => false, "text" => "订单号出错了 或 包裹未放在称上"));
		}
	}

	/**
	 * Add parcel
	 */
	public function actionComplete()
	{
		$taskId = $_POST["taskid"];
		$temp_taskId = $taskId;
		$taskId = substr($taskId, 1);
		$material = $_POST["material"];
		$weight = $_POST["weight"];
		$height = intval(@$_POST['height']);
		$width = intval(@$_POST['width']);
		$depth = intval(@$_POST['depth']);

		$task = WmsTask::model()->find("link_id = :taskid and type = :type", array(":taskid" => $taskId, ":type" => WmsTask::TYPE_PACK_ORDER));
		if (in_array($task->mainTask->job->org_id, [Org::ORGID_3PL_ABUNDANT])) {
			$weight = ceil($weight * 2) / 2;
		}
		$meta = json_decode($task->meta, true);

		if ($meta && array_key_exists("pkg", $meta)) {
			$pkg = json_decode($meta["pkg"], true);
		} else {
			$pkg = array();
		}

		$last = 0;
		if ($pkg) {
			foreach ($pkg as $last) {}
			$last = $last['nt'];
			if (preg_match('/P\d{6}(\d{3})/', $last, $matches)) {
				$last = $matches[1];
			}
		}

		$parcelId = "P" . sprintf("%06d", $taskId) . sprintf("%03d", $last + 1);
		$pkg[] = array("wt" => $weight, "w" => $width, "h" => $height, "d" => $depth, "nt" => $material . " " . date("Y-m-d H:i:s", time()) . " " . $parcelId);
		$meta["pkg"] = json_encode($pkg);
		$task->mdata = $meta;
		$task->save();

		$parcel = array();
		$parcel["taskid"] = $temp_taskId;
		$parcel["id"] = "P" . sprintf("%06d", $taskId) . sprintf("%03d", count($pkg));
		$parcel["date"] = date("Y-m-d H:i:s", time());
		$parcel["weight"] = $weight;
		$parcel['height'] = $height;
		$parcel['width'] = $width;
		$parcel['depth'] = $depth;
		$parcel["material"] = $material;

		$result = array("result" => true, "parcel" => $parcel);
		echo json_encode($result);
		Yii::app()->end();
	}

	public function actionWhole()
	{
		$this->render("whole");
	}

	public function actionBatch()
	{
		$this->render('whole');
	}

	public function actionBox()
	{
		$shelf_number = intval(substr($_GET['boxid'], 1, 2));
		$box_number = intval(substr($_GET['boxid'], 3));
		$task = WmsTask::model()->with('batch')->find('batch.box_number = :box_number AND batch.shelf_number = :shelf_number AND batch.status = :status', [':box_number' => $box_number, ':shelf_number' => $shelf_number, ':status' => WmsTaskBatch::WMS_TASK_BATCH_STATUS_NEW]);
		if (!empty($task)) {
			echo json_encode(['taskid' => $task->getNo()]);
		} else {
			echo json_encode(['taskid' => '']);
		}
		Yii::app()->end();
	}

	public function actionSingleItem()
	{
		$barcode = $_GET['barcode'];
		$prod = WmsProd::model()->find('ean = :barcode AND status = 1', [':barcode' => $barcode]);
		if (empty($prod)) {
			$prod = WmsProd::model()->with('orgs')->find('ean = :barcode OR orgs.sku = :barcode', [':barcode' => $barcode]);
		}
		$tasks = WmsTask::model()->with('batch')->findAll('batch.date >= :date AND batch.status = :status AND t.status NOT IN (40,99,100) AND batch.type = :type', [':date' => date('Y-m-d', strtotime(date('Y-m-d') . ' - 5 days')), ':status' => WmsTaskBatch::WMS_TASK_BATCH_STATUS_NEW, ':type' => WmsBatch::WMS_BATCH_TYPE_SINGLE]);
		foreach ($tasks as $task) {
			if ($task->status != 30) {
				continue;
			}

			if (!in_array($task->job->org_id, [Org::ORGID_3PL_XCSOURCE])) {
				// if (count($task->items) > 1 || $task->items[0]->mdata['uq'] > 1) {
				// 	continue;
				// }
				$count = 0;
				foreach ($task->items as $item) {
					$count += intval($item->mdata['uq']);
				}
				if ($count > 1) continue;
			} else {
				// $count = 0;
				// foreach ($task->items as $item) {
				// 	$count += $item->mdata['uq'];
				// }
				// XCSOURCE NOT CHOOSE LETTER
				if (!empty($task->deliveryTask->mdata['customer_choose_courier']) && !preg_match('/LETTER/i', $task->deliveryTask->mdata['customer_choose_courier'])) {
					continue;
				}

			}

			foreach ($task->items as $item) {
				if (empty($item->mdata['si'])) {
					continue;
				}

				$stock = WmsStock::model()->findByPk($item->mdata['si']);
				if (empty($stock)) {
					continue;
				}

				if ($stock->prod_id == $prod->id) {
					echo json_encode(['taskid' => $task->getNo(), 'stockid' => $stock->id]);
					Yii::app()->end();
				}
			}
		}
		echo json_encode([]);
		Yii::app()->end();
	}

	/**
	 * No longer use
	 */
	public function actionBarcode()
	{
		$this->render("barcode");
	}

	public function actionAutocomplete()
	{
		$type = $_GET["type"];
		$term = $_GET["term"];

		if ($type === "org") {
			$orgs = Org::model()->findAllBySql("select o.id, o.name from org o right join wms_stock ws on o.id = ws.org_id where name like :name group by o.id", array(":name" => "%" . $term . "%"));
			foreach ($orgs as $index => $org) {
				$orgs[$index] = array("id" => $org->id, "name" => $org->name);
			}

			if (!empty($orgs)) {
				echo json_encode(array('result' => true, 'items' => $orgs));
				Yii::app()->end();
			} else {
				echo json_encode(array('result' => false));
				Yii::app()->end();
			}
		} else if ($type === "product") {
			$org_name = $_GET["org"];
			$org = Org::model()->find("name = :name", array(":name" => $org_name));

			if (!empty($org)) {
				$products = WmsStock::model()->with("prod")->findAll("t.org_id = :orgid and prod.name like :name", array(":orgid" => $org->id, ":name" => "%" . $term . "%"));
				foreach ($products as $index => $product) {
					$products[$index] = array("id" => $product->id, "name" => $product->prod->name, "expiry" => $product->expiry);
				}

				echo json_encode(array('result' => true, 'items' => $products));
				Yii::app()->end();
			} else {
				echo json_encode(array('result' => false));
				Yii::app()->end();
			}
		}
	}

	public function actionPrint()
	{
		$taskId = $_GET["taskid"];

		if (strtoupper(substr($taskId, 0, 1)) == "T") {
			// task 二维码
			$taskId = substr($taskId, 1);
		} else {
			$task = WmsTask::model()->find('ref = :ref', array(':ref' => $taskId));
			$taskId = $task->id;
		}

		$label = $_GET["label"];
		if ($label == "null") {
			$this->_printNull($taskId);
		} else if ($label == "pca") {
			$this->_printPca($taskId);
		} else if ($label == 'other') {
			$this->_printOther($taskId);
		} else {
			$this->_printElse($taskId);
		}
	}

	public function _printNull($taskId)
	{
		$task = WmsTask::model()->find("link_id = :link_id and type = :type", array(":link_id" => $taskId, ':type' => WmsTask::TYPE_DELIVERY));
		$pack_task = WmsTask::model()->find("link_id = :link_id and type = :type", array(":link_id" => $taskId, ':type' => WmsTask::TYPE_PACK_ORDER));
		if ($task) {
			$task->mainTask->status = 99;
			$task->mainTask->compl_time = date('YmdHis');
			$task->mainTask->save();
			$task->compl_time = $task->mainTask->compl_time;
			$task->save();
			$pack_task->compl_time = $task->mainTask->compl_time;
			$pack_task->save();
			echo json_encode(array("result" => true, "msg" => "订单完成"));
			Yii::app()->end();
		} else {
			// echo json_encode(array("result" => false, "msg" => "订单号出错了"));
			// Yii::app()->end();
			$tf = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR, 'label');
			oPDF::renderPDF('wms_error', array('msg' => '该订单自提', 'no' => 'T' . $taskId), 2, $tf);
			$file = [['printer' => 'label', 'type' => 'pdf', 'file' => base64_encode(file_get_contents($tf))]];
			unlink($tf);
			echo json_encode(array("result" => true, "msg" => "订单完成, 该订单自提", "file" => json_encode($file)));
			Yii::app()->end();
		}
	}

	public function _printPca($taskId)
	{
		$task = WmsTask::model()->find("link_id = :link_id and type = :type", array(":link_id" => $taskId, ':type' => WmsTask::TYPE_DELIVERY));
		$pack_task = WmsTask::model()->find("link_id = :link_id and type = :type", array(":link_id" => $taskId, ':type' => WmsTask::TYPE_PACK_ORDER));
		if (empty($task)) {
			echo json_encode(array("result" => false, "msg" => "订单号出错了"));
			Yii::app()->end();
		}

		if ($task->job->org_id != Org::ORGID_3PL_XCSOURCE || ($task->job->org_id == Org::ORGID_3PL_XCSOURCE && !empty($task->mainTask->deliveryTask->mdata['customer_choose_courier']) && !preg_match('/LETTER/i', $task->mainTask->deliveryTask->mdata['customer_choose_courier']))) {
			$weight = [];
			foreach (json_decode($pack_task->mdata['pkg']) as $pkg) {
				$weight[] = $pkg->wt;
			}

			if (count($weight) == 0) {
				echo json_encode(array("result" => false, "msg" => "包裹数量为0"));
				Yii::app()->end();
			}

			// auto get courier by cost
			if (!empty($task->mdata['cnee']['suburb']) && !empty($task->mdata['cnee']['postcode']) && !empty($task->mdata['cnee']['state'])) {
				if (preg_match('/^australia$|^Au$|^AUS$/i', $task->mdata['cnee']['country'])) {
					if (!Postcode::validateAddress($task->mdata['cnee']['suburb'], $task->mdata['cnee']['state'], $task->mdata['cnee']['postcode'])) {
						echo json_encode(array("result" => false, "msg" => "地址信息错误，suburb，state和postcode不匹配"));
						Yii::app()->end();
					} else {
						$select_courier = $task->selectAuCourier($task->mdata['cnee']['suburb'], $task->mdata['cnee']['postcode'], $task->mdata['cnee']['state'], array_sum($weight), count($weight));
					}
				}
			} else {
				echo json_encode(array("result" => false, "msg" => "未填写收件人信息"));
				Yii::app()->end();
			}

			/**
			 * cobayer => fastway
			 * pobox => aupost
			 */
			if ($task->job->org_id == Org::ORGID_3PL_COBAYER) {
				$tp = new ImParcel;
				$tp->weight = array_sum($weight);
				$tp->pkg = count($weight);
				$tp->cnee = new Addr;
				$tp->cnee->state = $task->mdata['cnee']['state'];
				$tp->cnee->postcode = $task->mdata['cnee']['postcode'];
				$tp->cnee->suburb = $task->mdata['cnee']['suburb'];
				if (ChooseShipment::courierCanDelivery(Org::ORGID_COURIER_FASTWAY, $tp, false)) {
					$select_courier = Org::ORGID_COURIER_FASTWAY;
				}

			} else if ($task->job->org_id != Org::ORGID_3PL_COBAYER && Addr::checkIsPoBox($task->mdata['cnee']['address'])) {
				$select_courier = Org::ORGID_COURIER_AUPOST;
			}

			// oversize
			// foreach ($task->mainTask->items as $item) {
			//     $stock = WmsStock::model()->findByPk($item->mdata['si']);
			//     $pack = WmsProdPack::model()->find('prod_id = :prod_id AND type = 10 AND dim IS NOT NULL', array(':prod_id' => $stock->prod->id));
			//     if (!empty($pack) && floatval($pack->dims['w']) * floatval($pack->dims['h']) * floatval($pack->dims['d']) > 100000 && $task->mdata['courier'] == Org::ORGID_COURIER_FASTWAY) {
			//         $task->mdata['courier'] = Org::ORGID_COURIER_AUPOST;
			//     }
			// }

			// check chargecode in wms rate
			$rate = WmsOrgQuote::model()->find('org_id = :org_id AND status = 1', [':org_id' => $task->job->org_id]);
			if (!empty($rate->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE])) {
				$chargecode = ImportChargeCode::model()->find('chargecode = :code', [':code' => $rate->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE]]);
				$couriers = [];
				foreach ($chargecode->couriersObj as $courier) {
					$orgRate = OrgRate::model()->findByPk($courier);
					if (empty($orgRate) || empty($orgRate->org_id)) {
						continue;
					}
					$couriers[] = $orgRate->org_id;
				}
				if (!empty($select_courier) && !in_array($select_courier, $couriers)) {
					$select_courier = Org::ORGID_COURIER_AUPOST;
				}
			}

			/**
			 * 1. Instruction contains TNT
			 * 2. PEAK
			 * 3. ELEKZON shopify
			 */
			if (!empty($task->mainTask->mdata['note']) && preg_match('/TNT/i', $task->mainTask->mdata['note'])) {
				$select_courier = Org::ORGID_COURIER_TNT;
			} else if (in_array($task->job->org_id, [Org::ORGID_3PL_PEAKCARE])) {
				$select_courier = Org::ORGID_COURIER_TNT;
			} else if (in_array($task->job->org_id, [Org::ORGID_3PL_ELEKZON]) && (empty($task->mdata['shopify_standard']))) {
				$select_courier = Org::ORGID_COURIER_TNT;
			}

			/**
			 * international => sendle
			 * if customer has forced courier => courier
			 */
			if ($task->mdata['cnee']['country'] != 'AU' && !in_array($task->job->org_id, [Org::ORGID_3PL_COBAYER])) {
				if ($task->job->org_id == Org::ORGID_3PL_NINJA_SHARK) {
					$select_courier = Org::ORGID_COURIER_AUPOST;
				} else {
					$select_courier = Org::ORGID_COURIER_SENDLE;
				}
			} else if (!empty($task->mdata['courier']) && $task->mdata['courier'] != Org::ORGID_COURIER_PCA) {
				$tp = new ImParcel;
				$tp->weight = array_sum($weight);
				$tp->pkg = count($weight);
				$tp->cnee = new Addr;
				$tp->cnee->state = $task->mdata['cnee']['state'];
				$tp->cnee->postcode = $task->mdata['cnee']['postcode'];
				$tp->cnee->suburb = $task->mdata['cnee']['suburb'];
				if ($task->mdata['courier'] != Org::ORGID_COURIER_STARTRACK && ChooseShipment::courierCanDelivery($task->mdata['courier'], $tp, false)) {
					$select_courier = $task->mdata['courier'];
				} else if (in_array($task->mdata['courier'], [Org::ORGID_COURIER_TNT, Org::ORGID_COURIER_STARTRACK])) {
					$select_courier = Org::ORGID_COURIER_STARTRACK;
					foreach (json_decode($pack_task->mdata['pkg'], true) as $pkg) {
						if (empty($pkg['w']) || empty($pkg['h']) || empty($pkg['d'])) {
							echo json_encode(array("result" => false, "msg" => "TNT服务失败，请填写三边转为ST，共" . count($weight) . "包裹", 'st' => true));
							Yii::app()->end();
						}
					}
				}
			}

			$task->mdata['courier'] = $select_courier;
			$task->save();

			if (in_array($task->job->org_id, [Org::ORGID_3PL_LIFESTYLE])) {
				if (preg_match('/SO/', $task->mainTask->ref)) {
					$task->mainTask->status = self::STATUS_HOLD;
					$task->mainTask->update('status');
					echo json_encode(array("result" => false, "msg" => "Lifestyle toB订单 需联系办公室提供面单"));
					Yii::app()->end();
				} else if (preg_match('/ESAU/', $task->mainTask->ref)) {
					$rs = FileRepo::model()->findAll('fid = :taskid AND type IN (85,86,87)', [':taskid' => $taskId]);
					if (!empty($rs)) {
						$this->_printOther($taskId);
					}
				}
			}

			if ($task->mdata['cnee']['country'] != 'AU' && in_array($task->job->org_id, [Org::ORGID_3PL_COBAYER])) {
				$task->mdata['courier'] = Org::ORGID_COURIER_PCA;
				$task->save();
				$task->toShipment();
				echo json_encode(array("result" => false, "msg" => "Good Health 另出面单"));
				Yii::app()->end();
			}

			if (empty($task->mdata['shipment_id']) || empty($task->mdata['shipment_courier_id']) || $task->mdata['shipment_courier_id'] != $task->mdata['courier'] || ($task->mdata['courier'] == Org::ORGID_COURIER_FASTWAY && sizeof($task->mdata['shipment_id']) != count(json_decode($pack_task->mdata['pkg'], true))) || ($task->mdata['courier'] == Org::ORGID_COURIER_AUPOST && Shipment::model()->findByPk($task->mdata['shipment_id'][0])->pkg != count(json_decode($pack_task->mdata['pkg'], true)))) {
				unset($task->mdata["shipment_id"]);
				unset($task->mdata["shipment_courier_id"]);
				$rs = $task->toShipment();
			} else {
				$rs = [];
				foreach ($task->mdata['shipment_id'] as $shipment_id) {
					$rs[] = Shipment::model()->findByPk($shipment_id);
				}
			}

			if (!empty($rs)) {
				if ($rs[0]->type == 10) {
					if (empty($rs[0]->id)) {
						print_r($rs[0]->getErrors());
					} else {
						$allData = "";
						foreach ($rs as $p) {
							for ($i = 1; $i <= $p->pkg; $i++) {
								$aid = $p->ref . sprintf('%02s', $i) . '00093' . '50' . '0';
								$aid .= AusPostAPI::aidChkDgt($aid);
								$said = str_replace('AMQ', '>6AMQ>5', $aid);
								$model = array(
									'agent_id' => $p->agent_id,
									'cnee_company' => $p->cnee->company,
									'cnee_name' => $p->cnee->name,
									'cnee_address' => $p->cnee->address,
									'cnee_state_postcode' => $p->cnee->suburb . ' ' . $p->cnee->state . ' ' . $p->cnee->postcode,
									'shipment' => $p,
									'cnee_phone' => $p->cnee->tel,
									'weight' => $p->weight / $p->pkg,
									'cnor_name' => $p->cnor->name,
									'cnor_address' => '6C The Crescent',
									'cnor_state_postcode' => 'KINGSGROVE NSW 2208',
									'parcel_ref' => $p->ref,
									'parcel_uni_no' => $p->hbn,
									'parcel_cref' => $p->cref,
									'parcel_task_ref' => $task->ref,
									'parcel_qty' => round(@$totalQty / $p->pkg, 0),
									'parcel_index' => "$i/$p->pkg",
									'parcel_aupost_id' => $aid,
									'created' => $p->created,
									'parcel_aupost_barcode' => '>;>8019931265099999891' . $said,
									'parcel_aupost_matrix' => '019931265099999891' . $aid . '420' . $p->cnee->postcode . '8008' . date('ymdHis'),
								);
								if (isset($p->trans) && !empty($p->trans) && $p->trans[0]->org_id == Org::ORGID_COURIER_FASTWAY) {
									$allData .= $this->renderPartial('//_pdf/_label_fastway_zpl2020', ['label' => $p], true);
									$type = 'zpl';
								} else if (preg_match('/AMQ/i', $p->ref)) {
									$allData .= $this->renderPartial('//_pdf/_label_eparcel_zpl_2014_1', ['model' => $model], true);
									$type = 'zpl';
								} else if (preg_match('/7RFZ|DKC/i', $p->ref)) {
									$tf = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR, 'label');
									oPDF::renderPDF('label_A6', array('rs' => [$p]), 2, $tf);
									$allData .= base64_encode(file_get_contents($tf));
									unlink($tf);
									$type = 'pdf';
								} else if (!empty($p->trans) && $p->trans[sizeof($p->trans) - 1]->org_id == Org::ORGID_COURIER_SENDLE) {
									$sendle = new SendleAPI();
									$allData .= base64_encode($sendle->getLabel($p));
									$type = 'pdf';
								}
							}
						}
						$task->mainTask->status = 99;
						$task->mainTask->compl_time = date('YmdHis');
						$task->mainTask->save();
						$task->compl_time = $task->mainTask->compl_time;
						$task->save();
						$pack_task->compl_time = $task->mainTask->compl_time;
						$pack_task->save();

						$files = [];
						if (!empty($allData)) {
							$files[] = ['printer' => 'label', 'type' => $type, 'file' => $allData];
						}

						echo json_encode(array("result" => true, "msg" => "订单完成, 等待打印", "file" => json_encode($files)));
						Yii::app()->end();
					}
				} elseif ($rs[0]->type == 20) {
					$p = $rs[0];
					if (empty($p->id)) {
						print_r($p->getErrors());
					} else {
						$allData = "";
						$total_pkg = 0;
						foreach ($rs as $p) {
							$total_pkg += $p->pkg;
						}
						$k = 1;
						foreach ($rs as $p) {
							if (!empty($p->eitems['g'])) {
								$total = 0;
								foreach ($p->eitems['g'] as $i => $g) {
									$total += $p->eitems['q'][$i];
								}
							}
							for ($j = 1; $j <= $p->pkg; $j++, $k++) {
								if (!empty($p->eitems['g'])) {
									$gs = [];
									$pos = 0;
									foreach ($p->eitems['g'] as $i => $g) {
										if ($pos >= ceil($total * ($k - 1) / $total_pkg) && $pos < ceil($total * $k / $total_pkg)) {
											if (ceil($total * $k / $total_pkg) >= $p->eitems['q'][$i] + $pos) {
												$gs[] = $g . ' X ' . $p->eitems['q'][$i];
												$pos += $p->eitems['q'][$i];
											} else {
												$gs[] = $g . ' X ' . ceil($total * $k / $total_pkg - $pos);
												$pos += ceil($total * $k / $total_pkg) - $pos;
											}
										} else {
											if ($p->eitems['q'][$i] + $pos >= ceil($total * ($k - 1) / $total_pkg) && $pos < ceil($total * $k / $total_pkg)) {
												$gs[] = $g . ' X ' . ($p->eitems['q'][$i] + $pos - ceil($total * ($k - 1) / $total_pkg));
											}
											$pos += $p->eitems['q'][$i];
										}
									}
									$allData .= $this->renderPartial('//_pdf/_label_ex_zpl', array('label' => $p, 'gs' => implode("_0A", $gs), 'pkg_sn' => $j), true);
								}
							}
						}
						echo json_encode(array("result" => true, "msg" => "订单完成, 等待打印", "file" => json_encode($allData)));
						Yii::app()->end();
					}
				}
			} else {
				echo json_encode(array("result" => false, "msg" => "未填写收件人信息"));
				Yii::app()->end();
			}
		} else if ($task->job->org_id == Org::ORGID_3PL_XCSOURCE) {
			$task = $task->mainTask;
			// XCSOURCE CHOOSE LETTER
			if (!empty($task->deliveryTask->mdata['customer_choose_courier']) && !preg_match('/LETTER/i', $task->deliveryTask->mdata['customer_choose_courier'])) {
				echo json_encode(array('result' => false, 'msg' => '客人未选择LETTER'));
				Yii::app()->end();
			} else {
				$printed = 0;
				foreach ($task->items as $item) {
					if (empty($item->mdata['si'])) {
						continue;
					}

					$printed += intval(@$item->mdata['letter_qty']);
					if (!empty($item->mdata['letter_qty']) && $item->mdata['letter_qty'] == $item->mdata['uq']) {
						continue;
					}

					if ($item->mdata['si'] != $_GET['stockid']) {
						continue;
					}

					if (empty($item->mdata['letter_qty'])) {
						$item->mdata['letter_qty'] = 0;
					}
					$item->mdata['letter_qty'] += 1;
					$item->update('meta');

					$dt = $task->deliveryTask;
					$stk = WmsStock::model()->findByPk($item->mdata['si']);

					$ar = new Addr;
					$ar->name = $task->job->customer->name . ((!empty($task->job->customer->extra['sp_id']) && $task->job->customer->extra['sp_id'] == 305) ? ' - 3PL' : '');
					$ar->tel = $task->job->customer->phone;
					$ar->country = 'Australia';
					$ar->save();

					$ae = new Addr;
					$ae->setAttributes($dt->mdata['cnee']);
					$ae->save();

					$p = new ImParcel;
					$p->setAttributes(array(
						'agent_id' => Org::ORGID_3PL_XCSOURCE,
						'odpt_id' => 106,
						'cnor_id' => $ar->id,
						'cnee_id' => $ae->id,
						'status' => 10,
						'state' => $ae->state,
						'postcode' => $ae->postcode,
						'pkg' => 1,
						'currency' => 1,
						'ref' => $task->getNo() . '-' . ($printed + 1),
						'cref' => $task->ref,
						'hbn' => $task->getNo() . '-' . ($printed + 1),
					));
					$p->mdata['show_sku'] = true;
					$p->eitems = ['sku' => ['<span style="font-size:1.2em">' . $item->mdata['sn'] . ' ' . $stk->prod->ean . ' x 1</span>']];
					$p->save();
					if ($p->getErrors()) {
						echo json_encode(array('result' => false, 'msg' => '出错了，请联系IT，' . json_encode($p->getErrors())));
						Yii::app()->end();
					}

					$dt->mdata['shipment_id'][] = $p->id;
					$dt->update('meta');

					$tf = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR, 'label');
					oPDF::renderPDF('label_A6', ['rs' => [$p], 'tpl' => '_label_letter', 'default_rts' => true], 2, $tf);
					$allData = base64_encode(file_get_contents($tf));
					unlink($tf);
					$type = 'pdf';
					break;
				}

				$task->refresh();
				$all_print = true;
				foreach ($task->items as $item) {
					if (empty($item->mdata['letter_qty']) || $item->mdata['letter_qty'] < $item->mdata['uq']) {
						$all_print = false;
						break;
					}
				}
				if ($all_print) {
					$task->status = 99;
					$task->compl_time = date('YmdHis');
					$task->save();
				}

				$files = [];
				if (!empty($allData)) {
					if ($task->mdata['cnee']['country'] != 'AU' && $task->job->org_id == Org::ORGID_3PL_NINJA_SHARK) {
						$printer = 'A4';
						$type = 'pdf';
					} else {
						$printer = 'label';
					}
					$files[] = ['printer' => $printer, 'type' => $type, 'file' => $allData];
					echo json_encode(array("result" => true, "msg" => "订单完成, 等待打印", "file" => json_encode($files)));
					Yii::app()->end();
				} else {
					echo json_encode(array('result' => false, 'msg' => '出错了，请联系IT'));
					Yii::app()->end();
				}
			}
		} else {
			echo json_encode(array("result" => false, "msg" => "订单号出错了"));
			Yii::app()->end();
		}
	}

	public function _printOther($taskId)
	{
		$task = WmsTask::model()->find("link_id = :link_id and type = :type", array(":link_id" => $taskId, ':type' => WmsTask::TYPE_DELIVERY));
		$pack_task = WmsTask::model()->find("link_id = :link_id and type = :type", array(":link_id" => $taskId, ':type' => WmsTask::TYPE_PACK_ORDER));
		$rs = FileRepo::model()->findAll('fid = :taskid AND type IN (85,86,87)', [':taskid' => $taskId]);
		$files = [];
		foreach ($rs as $r) {
			if ($r->type == 85) {
				$files[] = ['printer' => 'label', 'type' => 'pdf', 'file' => base64_encode(file_get_contents($r->getFile()))];
			// } else if ($r->type == 86 || $r->type == 87) {
			// 	$files[] = ['printer' => 'A4', 'type' => 'pdf', 'file' => base64_encode(file_get_contents($r->getFile()))];
			}
		}
		$task->mainTask->status = 99;
		$task->mainTask->compl_time = date('YmdHis');
		$task->mainTask->save();
		$task->compl_time = $task->mainTask->compl_time;
		$task->save();
		$pack_task->compl_time = $task->mainTask->compl_time;
		$pack_task->save();
		echo json_encode(array("result" => true, "msg" => "订单完成, 等待打印", "file" => json_encode($files)));
		Yii::app()->end();
	}

	public function _printElse($taskId)
	{
		$task = WmsTask::model()->find("link_id = :link_id and type = :type", array(":link_id" => $taskId, ':type' => WmsTask::TYPE_DELIVERY));
		$pack_task = WmsTask::model()->find("link_id = :link_id and type = :type", array(":link_id" => $taskId, ':type' => WmsTask::TYPE_PACK_ORDER));
		if ($task) {
			if ($task->ref == $label) {
				$task->mainTask->status = 99;
				$task->mainTask->compl_time = date('YmdHis');
				$task->mainTask->save();
				$task->compl_time = $task->mainTask->compl_time;
				$task->save();
				$pack_task->compl_time = $task->mainTask->compl_time;
				$pack_task->save();

				echo json_encode(array("result" => true, "msg" => "订单完成"));
				Yii::app()->end();
			} else {
				echo json_encode(array("result" => false, "msg" => "面单号出错了"));
				Yii::app()->end();
			}
		} else {
			echo json_encode(array("result" => false, "msg" => "订单号出错了"));
			Yii::app()->end();
		}
	}

	public function actionDim()
	{
		$taskId = substr($_GET['taskid'], 1);
		$task = WmsTask::model()->findByPk($taskId);
		$pack_task = WmsTask::model()->find('link_id = :link_id AND type = :type', array(':link_id' => $task->id, 'type' => WmsTask::TYPE_PACK_ORDER));
		$meta = json_decode($pack_task->meta, true);
		if ($meta && array_key_exists("pkg", $meta)) {
			$pkg = json_decode($meta['pkg'], true);
		} else {
			$pkg = array();
		}

		foreach ($pkg as $k => $item) {
			$pkg[$k]['w'] = $_POST['width'][$k];
			$pkg[$k]['h'] = $_POST['height'][$k];
			$pkg[$k]['d'] = $_POST['length'][$k];
		}

		$meta["pkg"] = json_encode($pkg);
		$pack_task->mdata = $meta;
		$pack_task->save();

		$this->redirect(Yii::app()->createUrl('pack/pack/print', ['taskid' => $_GET['taskid'], 'label' => 'pca']));
	}

	/**
	 * No longer use
	 */
	public function actionGenerate()
	{
		$stockid = $_GET["prodid"];
		$stock = WmsStock::model()->findByPk($stockid);
		$prod = $stock->prod->name . " (Exp: " . $stock->expiry . ")";
		$org = $stock->customer;
		$box = $_GET["box"];
		$quantity = $_GET["quantity"];
		$stock_quantity = $stock->qty - $stock->qty_res;

		if (!in_array($box, [1, 2, 3, 4, 6])) {
			echo "只能预打印1、2、3、4、6罐箱标签";
			return;
		}
		if ($quantity > floor($stock_quantity / $box)) {
			echo "Stock不足, 只能打印" . floor($stock_quantity / $box) . "张";
			return;
		} else {
			oPDF::renderPDF("label_prepack", array("org" => $org->name, "prod" => $prod, "quantity" => $quantity, "box" => $box, "code" => "S" . $stockid . "Q" . $box), 1, "prepack_label.pdf");
		}
	}

	/**
	 * No longer use
	 */
	public function actionCheck()
	{
		$taskId = $_POST["taskid"];
		$temp_taskId = $taskId;
		$taskId = substr($taskId, 1);
		$stockid = $_POST["stockid"];
		$material = $_POST["material"];
		$weight = $_POST["weight"];

		$task = WmsTask::model()->findByPk($taskId);
		$taskItems = WmsTaskItem::model()->findAll("task_id = :task_id", array(":task_id" => $taskId));
		foreach ($taskItems as $taskItem) {
			$meta = json_decode($taskItem->meta, true);

			$stock = WmsStock::model()->findByPk($meta["si"]);

			if ($stock->id === $stockid) {
				$task = WmsTask::model()->find("link_id = :taskid and type = :type", array(":taskid" => $taskId, ":type" => WmsTask::TYPE_PACK_ORDER));
				$meta = json_decode($task->meta, true);

				if ($meta && array_key_exists("pkg", $meta)) {
					$pkg = json_decode($meta["pkg"], true);
				} else {
					$pkg = array();
				}

				$parcelId = "P" . sprintf("%06d", $taskId) . sprintf("%03d", count($pkg) + 1);
				$pkg[] = array("wt" => $weight, "w" => "", "h" => "", "d" => "", "nt" => $material . date("Y-m-d H:i:s", time()) . " " . $parcelId);
				$meta["pkg"] = json_encode($pkg);
				$task->mdata = $meta;
				$task->save();

				$parcel = array();
				$parcel["taskid"] = $temp_taskId;
				$parcel["id"] = "P" . sprintf("%06d", $taskId) . sprintf("%03d", count($pkg));
				$parcel["date"] = date("Y-m-d H:i:s", time());
				$parcel["weight"] = $weight;
				$parcel["material"] = $material;

				$result = array("result" => true, "parcel" => $parcel);
				echo json_encode($result);
				Yii::app()->end();
			}
		}

		echo json_encode(array("result" => false));
		Yii::app()->end();
	}

	/**
	 * No longer use
	 */
	public function actionPicture()
	{
		$taskid = $_POST["taskid"];
		$data = $_POST["data"];

		$f = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR, 'pp');
		file_put_contents($f, base64_decode(preg_replace('/^data:image\/jpeg;base64,/', '', $_POST['data'])));
		$id = FileRepo::storeFile($f, 'P' . date('YmdHis') . '.jpeg', 84, $_POST['taskid']);
		unlink($f);

		if ($id > 0) {
			echo json_encode(array("result" => true, "text" => "上传成功"));
			Yii::app()->end();
		} else {
			echo json_encode(array("result" => false, "text" => "上传失败"));
			Yii::app()->end();
		}
	}

	/**
	 * Delete wrong weight parcel
	 */
	public function actionDelete($id)
	{
		preg_match('/P(\d{6})(\d{3})/', $id, $matches);
		if (sizeof($matches) == 3) {
			$task = WmsTask::model()->findByPk($matches[1]);
			$pkg = $task->packTask->mdata['pkg'];
			$pkg = json_decode($pkg, true);
			foreach ($pkg as $k => $item) {
				if (preg_match('/' . $id . '/', $item['nt'])) {
					unset($pkg[$k]);
					break;
				}
			}
			$task->packTask->mdata['pkg'] = json_encode($pkg);
			$task->packTask->update('meta');

			echo json_encode(['result' => true]);
		} else {
			echo json_encode(['result' => false]);
		}
	}

	/**
	 * No longer use
	 */
	public function actionTransfer()
	{
		if (empty($_POST)) {
			$this->render('transfer');
		} else {
			$task = WmsTask::model()->find('ref = :ref', [':ref' => $_POST['shipment']]);
			if (empty($task)) {
				echo json_encode(['result' => false, 'msg' => 'Not find']);
				Yii::app()->end();
			} else {
				$weight = [];
				$pack_task = WmsTask::model()->find("link_id = :link_id and type = :type", array(":link_id" => $task->mainTask->id, ':type' => WmsTask::TYPE_PACK_ORDER));
				foreach (json_decode($pack_task->mdata['pkg']) as $pkg) {
					$weight[] = $pkg->wt;
				}

				if (empty($task->mdata['courier'])) {
					$select_courier = Org::ORGID_COURIER_PCA;
				} else {
					$select_courier = $task->mdata['courier'];
				}

				if (!empty($task->mdata['cnee']['suburb']) && !empty($task->mdata['cnee']['postcode']) && !empty($task->mdata['cnee']['state'])) {
					if (preg_match('/^australia$|^Au$|^AUS$/i', $task->mdata['cnee']['country'])) {
						$select_courier = $task->selectAuCourier($task->mdata['cnee']['suburb'], $task->mdata['cnee']['postcode'], $task->mdata['cnee']['state'], array_sum($weight), count($weight));
					}
				} else {
					echo json_encode(array("result" => false, "msg" => "未填写收件人信息"));
					Yii::app()->end();
				}

				$tp = new ImParcel;
				$tp->weight = array_sum($weight);
				$tp->pkg = count($weight);
				$tp->cnee = new Addr;
				$tp->cnee->state = $task->mdata['cnee']['state'];
				$tp->cnee->postcode = $task->mdata['cnee']['postcode'];
				$tp->cnee->suburb = $task->mdata['cnee']['suburb'];

				if ($task->job->org_id == Org::ORGID_3PL_COBAYER && ChooseShipment::courierCanDelivery(Org::ORGID_COURIER_FASTWAY, $tp, false)) {
					$select_courier = Org::ORGID_COURIER_FASTWAY;
				} else if ($task->job->org_id != Org::ORGID_3PL_COBAYER && Addr::checkIsPoBox($task->mdata['cnee']['address'])) {
					$select_courier = Org::ORGID_COURIER_AUPOST;
				}

				// oversize
				foreach ($task->mainTask->items as $item) {
					$stock = WmsStock::model()->findByPk($item->mdata['si']);
					$pack = WmsProdPack::model()->find('prod_id = :prod_id AND type = 10 AND dim IS NOT NULL', array(':prod_id' => $stock->prod->id));
					// if (!empty($pack) && floatval($pack->dims['w']) * floatval($pack->dims['h']) * floatval($pack->dims['d']) > 100000 && $task->mdata['courier'] == Org::ORGID_COURIER_FASTWAY) {
					//     $task->mdata['courier'] = Org::ORGID_COURIER_AUPOST;
					// }
				}

				$task->mdata['courier'] = $select_courier;
				$task->save();

				if (empty($task->mdata['shipment_id']) || empty($task->mdata['shipment_courier_id']) || $task->mdata['shipment_courier_id'] != $task->mdata['courier'] || ($task->mdata['courier'] == Org::ORGID_COURIER_FASTWAY && sizeof($task->mdata['shipment_id']) != count(json_decode($pack_task->mdata['pkg'], true))) || ($task->mdata['courier'] == Org::ORGID_COURIER_AUPOST && Shipment::model()->findByPk($task->mdata['shipment_id'][0])->pkg != count(json_decode($pack_task->mdata['pkg'], true)))) {
					unset($task->mdata["shipment_id"]);
					unset($task->mdata["shipment_courier_id"]);
					$rs = $task->toShipment();
				} else {
					$rs = [];
					foreach ($task->mdata['shipment_id'] as $shipment_id) {
						$rs[] = Shipment::model()->findByPk($shipment_id);
					}
				}

				if (!empty($rs)) {
					if ($rs[0]->type == 10) {
						if (empty($rs[0]->id)) {
							print_r($rs[0]->getErrors());
						} else {
							$allData = "";
							foreach ($rs as $p) {
								for ($i = 1; $i <= $p->pkg; $i++) {
									$aid = $p->ref . sprintf('%02s', $i) . '00093' . '50' . '0';
									$aid .= AusPostAPI::aidChkDgt($aid);
									$said = str_replace('AMQ', '>6AMQ>5', $aid);
									$model = array(
										'agent_id' => $p->agent_id,
										'cnee_company' => $p->cnee->company,
										'cnee_name' => $p->cnee->name,
										'cnee_address' => $p->cnee->address,
										'cnee_state_postcode' => $p->cnee->suburb . ' ' . $p->cnee->state . ' ' . $p->cnee->postcode,
										'shipment' => $p,
										'cnee_phone' => $p->cnee->tel,
										'weight' => $p->weight / $p->pkg,
										'cnor_name' => $p->cnor->name,
										'cnor_address' => '6C The Crescent',
										'cnor_state_postcode' => 'KINGSGROVE NSW 2208',
										'parcel_ref' => $p->ref,
										'parcel_uni_no' => $p->hbn,
										'parcel_cref' => $p->cref,
										'parcel_task_ref' => $task->ref,
										'parcel_qty' => round(@$totalQty / $p->pkg, 0),
										'parcel_index' => "$i/$p->pkg",
										'parcel_aupost_id' => $aid,
										'created' => $p->created,
										'parcel_aupost_barcode' => '>;>8019931265099999891' . $said,
										'parcel_aupost_matrix' => '019931265099999891' . $aid . '420' . $p->cnee->postcode . '8008' . date('ymdHis'),
									);
									if (isset($p->trans) && !empty($p->trans) && $p->trans[0]->org_id == Org::ORGID_COURIER_FASTWAY) {
										$allData .= $this->renderPartial('//_pdf/_label_fastway_zpl2020', ['label' => $p], true);
									} else {
										$allData .= $this->renderPartial('//_pdf/_label_eparcel_zpl_2014_1', ['model' => $model], true);
									}
								}
							}
							$task->mainTask->status = 99;
							$task->mainTask->compl_time = date('YmdHis');
							$task->mainTask->save();
							$task->compl_time = $task->mainTask->compl_time;
							$task->save();
							$pack_task->compl_time = $task->mainTask->compl_time;
							$pack_task->save();
							echo json_encode(array("result" => true, "msg" => "订单完成, 等待打印", "file" => $allData));
							Yii::app()->end();
						}
					} elseif ($rs[0]->type == 20) {
						$p = $rs[0];
						if (empty($p->id)) {
							print_r($p->getErrors());
						} else {
							$allData = "";
							$total_pkg = 0;
							foreach ($rs as $p) {
								$total_pkg += $p->pkg;
							}
							$k = 1;
							foreach ($rs as $p) {
								if (!empty($p->eitems['g'])) {
									$total = 0;
									foreach ($p->eitems['g'] as $i => $g) {
										$total += $p->eitems['q'][$i];
									}
								}
								for ($j = 1; $j <= $p->pkg; $j++, $k++) {
									if (!empty($p->eitems['g'])) {
										$gs = [];
										$pos = 0;
										foreach ($p->eitems['g'] as $i => $g) {
											if ($pos >= ceil($total * ($k - 1) / $total_pkg) && $pos < ceil($total * $k / $total_pkg)) {
												if (ceil($total * $k / $total_pkg) >= $p->eitems['q'][$i] + $pos) {
													$gs[] = $g . ' X ' . $p->eitems['q'][$i];
													$pos += $p->eitems['q'][$i];
												} else {
													$gs[] = $g . ' X ' . ceil($total * $k / $total_pkg - $pos);
													$pos += ceil($total * $k / $total_pkg) - $pos;
												}
											} else {
												if ($p->eitems['q'][$i] + $pos >= ceil($total * ($k - 1) / $total_pkg) && $pos < ceil($total * $k / $total_pkg)) {
													$gs[] = $g . ' X ' . ($p->eitems['q'][$i] + $pos - ceil($total * ($k - 1) / $total_pkg));
												}
												$pos += $p->eitems['q'][$i];
											}
										}
										$allData .= $this->renderPartial('//_pdf/_label_ex_zpl', array('label' => $p, 'gs' => implode("_0A", $gs), 'pkg_sn' => $j), true);
									}
								}
							}
							$task->mainTask->status = 99;
							$task->mainTask->compl_time = date('YmdHis');
							$task->mainTask->save();
							$task->compl_time = $task->mainTask->compl_time;
							$task->save();
							$pack_task->compl_time = $task->mainTask->compl_time;
							$pack_task->save();
							echo json_encode(array("result" => true, "msg" => "订单完成, 等待打印", "file" => $allData));
							Yii::app()->end();
						}
					}
				} else {
					echo json_encode(array("result" => false, "msg" => "未填写收件人信息"));
					Yii::app()->end();
				}
			}
		}
	}

	public function actionHoldTask()
	{
		$taskId = substr($_GET['taskid'], 1);
		$task = WmsTask::model()->findByPk($taskId);
		$task->status = self::STATUS_HOLD;
		$task->update('status');

		echo json_encode(array("result" => true, "msg" => "操作成功"));
		Yii::app()->end();
	}

	public function actionDoubleCheck()
	{
		if (empty($_GET['id'])) {
			$this->render('double_check');
		} else {
			$model = WmsTask::model()->findByPk(trim(explode(' ', $_GET['id'])[0], 'T'));
			$prods = [];
			foreach ($model->items as $item) {
				if (empty($item->mdata['si']) || empty($item->stockLedgers)) {
					continue;
				}

				$stock = WmsStock::model()->findByPk($item->mdata['si']);
				if (empty($prods[$stock->prod_id])) {
					$prods[$stock->prod_id] = 0;
				}

				$prods[$stock->prod_id] += intval($item->mdata['uq']);
			}
			foreach ($prods as $prod_id => $qty) {
				$wts = WmsTaskSort::model()->find('task_id = :task_id AND prod_id = :prod_id', [':task_id' => $model->id, ':prod_id' => $prod_id]);
				if (empty($wts)) {
					$wts = new WmsTaskSort;
					$wts->task_id = $model->id;
					$wts->prod_id = $prod_id;
					$wts->qty = $qty;
					$wts->sort_qty = 0;
					$wts->save();
				}
				$prod = WmsProd::model()->findByPk($prod_id);
				$prods[$prod->ean . '_' . $prod->name] = [intval($wts->sort_qty), $wts->qty];
				unset($prods[$prod_id]);
			}
			$this->renderPartial('_double_check', ['prods' => $prods]);
		}
	}

	public function actionAjaxDoubleCheck($id)
	{
		$model = WmsTask::model()->findByPk($id);

		while (true) {
			try {
				$fp = fopen(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'system_lock' . DIRECTORY_SEPARATOR . 'double_check.lock', 'r');
				flock($fp, LOCK_EX);
				$wts = WmsTaskSort::model()->with('prod')->find('task_id = :task_id AND prod.ean = :ean', [':task_id' => $model->id, ':ean' => $_POST['ean']]);
				if (!empty($wts)) {
					$wts->sort_qty += 1;
					$wts->save();
				}
				flock($fp, LOCK_UN);
				fclose($fp);
				break;
			} catch (Exception $ex) {
				throw $ex;
			}
		}
	}

	public function actionShelfCheck()
	{
		if (empty($_GET['shelf_no'])) {
			$this->render('shelf_check');
		} else {
			if (!is_numeric($_GET['shelf_no'])) {
				return;
			}

			$batch = WmsTaskBatch::model()->with('parent')->find('t.status = :status AND t.shelf_number = :sn AND parent.type = :type', [':sn' => $_GET['shelf_no'], ':status' => WmsTaskBatch::WMS_TASK_BATCH_STATUS_NEW, ':type' => WmsBatch::WMS_BATCH_TYPE_MULTI]);
			if (!empty($batch)) {
				$batches = WmsTaskBatch::model()->findAll('batch_id = :bid', [':bid' => $batch->batch_id]);
				$boxes = [];
				foreach ($batches as $batch) {
					$boxes[$batch->box_number] = $batch;
				}
			} else {
				$boxes = [];
			}
			$this->render('_shelf_check', ['boxes' => $boxes]);
		}
	}

}
