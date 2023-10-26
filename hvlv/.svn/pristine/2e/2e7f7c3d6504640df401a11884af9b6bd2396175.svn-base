<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CalculateController extends Controller
{
	protected $nonAjax = ['calculate','calculate1','addItem','changechargecode','updatefw','updatefw1','changeLabel','changeStatus','createFastway','removeFastRef','linkref','fastwayZone','change2Aupost','getTrack'];
	
	public function actionIndex()
	{
		$this->render('//excel/calculate');
	}
	
	/*
	 * this method is used to change fastway status for Michella
	 *
	 */
	
	public function actionChangeStatus()
	{
		if (!empty($_FILES)) {
			$file=empty($_FILES['fw_file'])?[]:$_FILES['fw_file'];
			if (empty($file['tmp_name'])) {
				echo 'no file return';
				return;
			}
			$xls= new oExcel;
			$xls->supported($file['name']);
			$xls->load($file['tmp_name'], false);
			$data=$xls->getAll();
		 
			 
			unset($xls);
			$status=empty($_POST['status_change'])?40:trim($_POST['status_change']);
			foreach ($data as $d) {
				if (empty($d[1])) {
					continue;
				}
				$r= ImParcel::model()->find('ref=:ref', [':ref'=>$d[1]]);
				if (empty($r)) {
					continue;
				}
				$r->status=$status;
				$r->save();
			}
		}
	}
	
	public function actionChangechargecode()
	{
		if (!empty($_FILES)) {
			$file=empty($_FILES['fw_file'])?[]:$_FILES['fw_file'];
			if (empty($file['tmp_name'])) {
				echo 'no file return';
				return;
			}
			$xls= new oExcel;
			$xls->supported($file['name']);
			$xls->load($file['tmp_name'], false);
			$data=$xls->getAll();
		 
			if (empty($_POST['chargecode'])) {
				echo 'no chargecode';
				return;
			}
			unset($xls);
			$chargecode=trim($_POST['chargecode']);
			$chargeCodeInfo = ImportChargeCode::model()->find('chargecode = :ccode', [':ccode' => $chargecode]);
			if (!empty($chargeCodeInfo)&&$chargeCodeInfo->status==1) {
				foreach ($data as $d) {
					if (empty($d[1])) {
						continue;
					}
					$r= ImParcel::model()->find('ref=:ref', [':ref'=>$d[1]]);
					if (empty($r)) {
						echo $d[1].' shipment not found<br>';
						continue;
					}
					$r->mdata['chargecode']=$chargecode;
					if ($r->save()) {
						echo $d[1].' shipment done!<br>';
					}
				}
			} else {
				echo 'chargecode not valid';
				return;
			}
		}
	}
	
	
	public function actionAddItem()
	{
		if (!empty($_FILES)) {
			$file=empty($_FILES['fw_file'])?[]:$_FILES['fw_file'];
			if (empty($file['tmp_name'])) {
				echo 'no file return';
				return;
			}
			$xls= new oExcel;
			$xls->supported($file['name']);
			$xls->load($file['tmp_name'], false);
			$data=$xls->getAll();
						
			foreach ($data as $d) {
				if (empty($d[1])) {
					continue;
				}
				$p= ImParcel::model()->find('ref=:ref', [':ref'=>$d[1]]);
				if (!empty($p->eitems)) {
					continue;
				}
				if (!empty($p)) {
					$names = explode(';', $d[3]);
					$names_zh=explode(';', $d[2]);
					$values = explode(';', $d[4]);
					$qtys = explode(';', $d[5]);
					for ($i=0;$i<sizeof($names);$i++) {
						$p->eitems['type'][$i] = 'O';
						$p->eitems['g'][$i] = $names[$i];
						$p->eitems['g_zh'][$i] = $names_zh[$i];
						$p->eitems['b'][$i] = '';
						$p->eitems['m'][$i] = '';
						$p->eitems['q'][$i] = $qtys[$i];
						$p->eitems['v'][$i] = sprintf('%0.2f', $values[$i]);
						$p->eitems['hs'][$i] = '';
					}
					if ($p->save()) {
						echo $d[1]." update!<br>";
					}
				}
			}
		}
	}
	
	public function actionChangeLabel()
	{
		if (!empty($_POST['label_change'])) {
			$id=$_POST['label_change'];
			$r= ImParcel::model()->find('id=:id', [':id'=>$id]);
			if (!empty($r)) {
				unset($r->mdata['test_aupost_2014']);
				$r->mdata['test_aupost_2017']=1;
				$r->save();
			}
		}
	}
	
	public function actionLinkref()
	{
		if (!empty($_FILES)) {
			$file=empty($_FILES['fw_file'])?[]:$_FILES['fw_file'];
			if (empty($file['tmp_name'])) {
				echo 'no file return';
				return;
			}
			$xls= new oExcel;
			$xls->supported($file['name']);
			$xls->load($file['tmp_name'], false);
			$data=$xls->getAll();
			unset($xls);
			$status=empty($_POST['status_change'])?40:trim($_POST['status_change']);
			foreach ($data as $d) {
				if (empty($d[1])||empty($d[2])||empty($d[3])) {
					continue;
				}
				$r= ImParcel::model()->find('hbn=:hbn', [':hbn'=>$d[2]]);
				$r1= ImParcel::model()->find('ref=:ref', [':ref'=>$d[3]]);
				 
				if (empty($r)||empty($r1)) {
					continue;
				}
				$maping= ChangeShipmentLabel::model()->find('phbn=:phbn', [':phbn'=>$r->hbn]);
				if (empty($maping)) {
					$maping=new ChangeShipmentLabel();
					$maping->pid=$r->id;
					$maping->pref=$r->ref;
					$maping->phbn=$r->hbn;
					$maping->newref=$r1->ref;
				}
				$maping->save();
			}
		}
	}
	
	/*
	 * this method used to create AUSPoST for unsend fastway shipments,
	 * and output an excel which contains the old fastway label ECN, fastway label, and new AMQ
	 * save the old fastway number to the note of new AMQ shipments
	 *
	 */
	public function actionCreateFastway()
	{
		if (!empty($_FILES)) {
			$file=empty($_FILES['fw_file'])?[]:$_FILES['fw_file'];
			if (empty($file['tmp_name'])) {
				echo 'no file return';
				return;
			}
			$xls= new oExcel;
			$xls->supported($file['name']);
			$xls->load($file['tmp_name'], false);
			$data=$xls->getAll();
			foreach ($data as $i=>$d) {
				if (empty($d[1])) {
					continue;
				}
				$orgShipment= ImParcel::model()->find('ref=:ref', [':ref'=>$d[1]]);
				if (empty($orgShipment)) {
					continue;
				}
				$newShipment = new ImParcel('create');
				// copy basic attributes
				$newShipment->attributes = $orgShipment->attributes;
				$cnor = new Addr;
				$cnor->attributes = $orgShipment->cnor->attributes;
				$cnor->save();
				$newShipment->cnor_id = $cnor->id;
				$cnee = new Addr;
				$cnee->attributes = $orgShipment->cnee->attributes;
				;
				$cnee->save();
				$newShipment->cnee_id = $cnee->id;
				$newShipment->eitems = $orgShipment->eitems;
				$newShipment->mdata = $orgShipment->mdata;
				$newShipment->scan='';
					 
				$newShipment->hbn = ''; //  in order to create a new one in case
					$newShipment->status = 40; // set as Received status
					$newShipment->consol_id = 0;
				$newShipment->created = date('Y-m-d');
				$newShipment->mdata['test_aupost_2014'] = 1;
				$newShipment->note=$orgShipment->ref;
				$newShipment->save();
				$newShipment->ref = 'AMQ'.sprintf('%07s', substr($newShipment->id, -7));
				$newShipment->update('ref');

				$xls->setCell('B'.$i, $orgShipment->hbn);
				$xls->setCell('C'.$i, $newShipment->ref);
			}
			$xls->output('aupost.xlsx');
		}
	}
	
	
	
	
	
	/*
	 */
	public function actionGetTrack()
	{
		if (!empty($_FILES)) {
			$file=empty($_FILES['fw_file'])?[]:$_FILES['fw_file'];
			if (empty($file['tmp_name'])) {
				echo 'no file return';
				return;
			}
			$xls= new oExcel;
			$xls->supported($file['name']);
			$xls->load($file['tmp_name'], false);
			$data=$xls->getAll();
			foreach ($data as $i=>$d) {
				if (empty($d[1])) {
					continue;
				}
				$orgShipment= ImParcel::model()->find('ref=:ref', [':ref'=>$d[1]]);
				if (empty($orgShipment)) {
					continue;
				}
				$ts=Tracking::model()->findAll('pid=:pid order by dt desc', [':pid'=>$orgShipment->id]);

				$xls->setCell('B'.$i, $ts[0]->dt);
				$xls->setCell('C'.$i, $ts[0]->activity);
				$xls->setCell('D'.$i, $ts[0]->depot);
			}
			$xls->output('getTracking.xlsx');
		}
	}
		
	/**
	 * change old fastway delete their ref and
	 * save ref to their note
	 *
	 *
	 */
	
	
	public function actionRemoveFastRef()
	{
		ini_set('memory_limit', '1024M');
			
		if (!empty($_FILES)) {
			$file=empty($_FILES['fw_file'])?[]:$_FILES['fw_file'];
			if (empty($file['tmp_name'])) {
				echo 'no file return';
				return;
			}
			$xls= new oExcel;
			$xls->supported($file['name']);
			$xls->load($file['tmp_name'], false);
			$data=$xls->getAll();
			unset($xls);
			$temp=[];
			foreach ($data as $d) {
				if (empty($d[1])) {
					continue;
				}
				$result='';
				$r= ImParcel::model()->find('ref=:ref', [':ref'=>$d[1]]);
				if (empty($r)) {
					continue;
				}
				$r->note.='('.$r->ref.')';
				$r->note.='(auspost ref:'.$d[2].')';
				$r->ref='';
				$r->save();
			}
		}
	}
	/*this method used to find if it is in the fastway mapzone
	 *
	 *
	 */
	public function actionUpdatefw()
	{
		ini_set('memory_limit', '1024M');
			
		if (!empty($_FILES)) {
			$file=empty($_FILES['fw_file'])?[]:$_FILES['fw_file'];
			if (empty($file['tmp_name'])) {
				echo 'no file return';
				return;
			}
			$xls= new oExcel;
			$xls->supported($file['name']);
			$xls->load($file['tmp_name'], false);
			$data=$xls->getAll();
			unset($xls);
			$temp=[];
			foreach ($data as $d) {
				if (empty($d[1])) {
					continue;
				}
				$result='';
				$r= ImParcel::model()->find('ref=:ref', [':ref'=>$d[1]]);
				if (empty($r)) {
					continue;
				}
				
				$temp[]=['ref'=>$d[1],'postcode'=>$r->cnee->postcode,'state'=>$r->cnee->state,'suburb'=>$r->cnee->suburb,'result'=>FastwayAPI::isInDeliveryArea($r->cnee->postcode, $r->cnee->suburb)? 'YES' : 'NO'];
			}
			$this->upfw($temp);
		}
	}
	
	public function upfw($data)
	{
		$output=new oExcel;
		$output->setCell('A1', 'ref');
		$output->setCell('B1', 'postcode');
		$output->setCell('C1', 'suburb');
		$output->setCell('D1', 'state');
		$output->setCell('E1', 'result');
		foreach ($data as $i=>$d) {
			$output->setCell('A'.($i+2), $d['ref']);
			$output->setCell('B'.($i+2), $d['postcode']);
			$output->setCell('C'.($i+2), $d['suburb']);
			$output->setCell('D'.($i+2), $d['state']);
			$output->setCell('E'.($i+2), $d['result']);
		}
		$output->output('fastway_zone_map.xlsx');
	}
		 
	/*
		* used to create tranship
		*
		*/
		 
		 
	public function actionCreateTranship()
	{
		if (!empty($_FILES)) {
			$file=empty($_FILES['fw_file'])?[]:$_FILES['fw_file'];
			if (empty($file['tmp_name'])) {
				echo 'no file return';
				return;
			}
			$xls= new oExcel;
			$xls->supported($file['name']);
			$xls->load($file['tmp_name'], false);
			$data=$xls->getAll();
			unset($xls);
				
			$out=[]; //save AMQ ===>fastway numbers;
				 $ss=[];  //use to save shipment for send to aupost
				foreach ($data as $d) {
					$tempRef='';
					if (empty($d[1])) {
						continue;
					}
					$ims= ImParcel::model()->find('ref=:ref', [':ref'=>$d[1]]);
					if (empty($ims)) {
						continue;
					}
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
					$costValue =12.11;
					$ts->cost = $costValue;
					if (!$ts->save()) {
						echo json_encode($ts->getErrors());
					}
				}
		}
	}

	/*used to create
	 * tranship and billingline when auspost have some problems
	 *
	 *
	 */
	 
	public function actionCreateTranshipAndBilling()
	{
		//        $start_date=$_POST['start_date'];
		$end_date=$_POST['end_date'];
		$manifestId=$_POST['agent_id'];
		$id = $end_date;
		$orderId =$manifestId;
		$aupostApi = new AusPostAPI('syd', true);
		$orderInfo = $aupostApi->getOrder($orderId);
								
		if (!empty($orderInfo->order)) {
								 
					// update shipment related cost and aupost shipment id

			// because aupost returned shipment is not the same order with our sending order
			// so here we need to order by HBN again
			$auPostShipments = [];
			foreach ($orderInfo->order->shipments as $aushipment) {
				$ims = ImParcel::model()->find('hbn = :hbn', [':hbn' => $aushipment->shipment_reference]);
												
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

				echo 'done for shipment : ' ;
			}


			// update all console's Australia post office cost billing
			$consolIds = [$id];
			Consol::model()->updateImportConsoleBilling($consolIds);
		}
	}
		 
		 
	public function save2excel($data)
	{
		$output=new oExcel;
		$output->setCell('A1', 'Old Ref');
		$output->setCell('B1', 'new Ref');
		foreach ($data as $i=>$d) {
			$output->setCell('A'.($i+2), $d['oldRef']);
			$output->setCell('B'.($i+2), $d['newRef']);
		}
		 
		$output->output('fastway2eParcel.xlsx');
	}
		 
		 
		 
	public function actionCalculate3()
	{
		$start_date=$_POST['start_date'];
		$end_date=$_POST['end_date'];
		$org_id=$_POST['org_id'];
		ini_set('memory_limit', '1024M');
//
		$criteria=new CDbCriteria;
		$criteria->addCondition('org_id=858');   // 115 fastway   AMQ
		
		$criteria->addCondition('time >= :start_date AND time <=:end_date ');
		$criteria->params += [':start_date' =>date("Y-m-d", strtotime("0 day", strtotime($start_date))),
			':end_date' =>date("Y-m-d", strtotime("0 day", strtotime($end_date)))];
//
		$fastway= Tranship::model()->findAll($criteria);
		$result_fastway=[];
		foreach ($fastway as $e) {
			if (empty($e->shipment->consol_id)) {
				continue;
			}
			if ($e->shipment->agent_id!=$org_id) {
				continue;
			}
			if ($e->shipment->cbm<=0) {
				if (isset($e->shipment->mdata['dim'])) {
					$e->shipment->cbm=floatval($e->shipment->mdata['dim']['w']) * floatval($e->shipment->mdata['dim']['h']) * floatval($e->shipment->mdata['dim']['d'])/1000000;
					//                    $e->shipment->update(['cbm']);
				}
			}
			$result_fastway[]=['hbn'=>$e->shipment->hbn,'ref'=>$e->connote, 'date'=> substr($e->time, 0, 10),'postcode'=>$e->shipment->postcode,'packs'=>$e->shipment->pkg,'weight'=>$e->shipment->weight,'cbm'=>$e->shipment->cbm];
		}
		//        $criteria1=new CDbCriteria;
		//        $criteria1->addCondition('time >= :start_date AND time <=:end_date ');
		//        $criteria1->params += [':start_date' =>date("Y-m-d", strtotime("0 day", strtotime($start_date))),
		//                              ':end_date' =>date("Y-m-d", strtotime("0 day", strtotime($end_date)))];
//
		//       $criteria1->addCondition('connote like :hbn');
		//        $criteria1->params +=[':hbn'=>'AMQ%'];
		//        $eParcel= Tranship::model()->findAll($criteria1);
		//         $result_eParcel=[];
		//        foreach ($eParcel as $e){
		//          if(empty($e->shipment->consol_id))     continue;
		//           $result_eParcel[]=['hbn'=>$e->shipment->hbn,'ref'=>$e->connote, 'date'=> substr($e->time, 0,10),'postcode'=>$e->shipment->postcode,'packs'=>$e->shipment->pkg,'weight'=>$e->shipment->weight];
//
		//        }
		$this->save21excel($result_fastway);
	}
	
	public function save21excel($data)
	{
		$output=new oExcel();
		$outputFile='statics.xlsx';
		$output->setCell('A1', 'HBN');
		$output->setCell('B1', 'REF');
		$output->setCell('C1', 'POSTCODE');
		$output->setCell('D1', 'PACKS');
		$output->setCell('E1', 'WEIGHT');
		$output->setCell('F1', 'cbm');
		$output->setCell('G1', 'DATE');
		$output->setTitle('startrack');
		foreach ($data as $i=>$ds) {
			$output->setCell('A' . ($i + 2), $ds['hbn']);
			$output->setCell('B' . ($i + 2), $ds['ref']);
			$output->setCell('C' . ($i + 2), $ds['postcode']);
			$output->setCell('D' . ($i + 2), $ds['packs']);
			$output->setCell('E' . ($i + 2), $ds['weight']);
			$output->setCell('F' . ($i + 2), $ds['cbm']);
			$output->setCell('G' . ($i + 2), $ds['date']);
		}
					 
		$output->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
		$output->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$output->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$output->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			 
		////            $output->createSheet('eParcel');
		////            $output->goSheet(1);
		////            $output->setCell('A1', 'HBN');
		////            $output->setCell('B1', 'REF');
		////            $output->setCell('C1', 'POSTCODE');
		////            $output->setCell('D1', 'PACKS');
		////            $output->setCell('E1', 'WEIGHT');
		////            $output->setCell('F1', 'DATE');
		////
		////            foreach ($data1 as $i=>$ds){
		////            $output->setCell('A' . ($i + 2), $ds['hbn']);
		////            $output->setCell('B' . ($i + 2), $ds['ref']);
		////            $output->setCell('C' . ($i + 2), $ds['postcode']);
		////            $output->setCell('D' . ($i + 2), $ds['packs']);
		////            $output->setCell('E' . ($i + 2), $ds['weight']);
		////            $output->setCell('F' . ($i + 2), $ds['date']);
		////          }
//
		//             $output->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
		//             $output->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		//             $output->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		//             $output->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			 
		$output->output($outputFile);
	}

	public function actionChange2Aupost()
	{
		$ref=$_POST['shipment_ref'];
		$r=ImParcel::model()->find('ref=:ref', [':ref'=>$ref]);
		if (!empty($r)) {
			$r->note.=$r->ref;
			$r->ref='AMQ'.$r->id;
			$r->mdata['test_aupost_2014'] = 1;
			$r->scan="";
			$t= Tranship::model()->find('pid=:pid', [':pid'=>$r->id]);
			//          if(!empty($t)) $t->delete();
			if ($r->save()) {
				echo "succesfully";
			}
		}
	}

	public function actionCalculate()
	{
		$invoices=Invoice::model()->findAll('type=10 AND status!=10 AND to_id=838 AND date>="2017-07-01" AND date<="2017-12-13"');
		$i=1;
		$output=new oExcel();
		$outputFile='jcex.xlsx';
		$output->setCell('A1', 'REF');
		$output->setCell('B1', 'POSTCODE');
		$output->setCell('C1', 'Description');
		$output->setCell('D1', 'Invoice');
		$output->setCell('E1', 'Inovice date');
		foreach ($invoices as $inv) {
			if (preg_match('/\-/i', $inv->no)) {
				continue;
			}
			$no=$inv->no;
			$date=$inv->date;
			foreach ($inv->lines as $il) {
				foreach ($il->mdata['items'] as $si=>$sp) {
					$mixDesc = nl2br($sp[1]);
					$pos = strrpos($mixDesc, "-");
					$desc = $mixDesc;
					if ($desc=='Insurance Fee') {
						continue;
					}
					$postcode = '';
					if (isset($sp[6])) {
						$postcode = $sp[6];
					}
					$p=ImParcel::model()->find('ref=:ref', [':ref'=>$sp[0]]);
					if (empty($p->mdata['chargecode'])) {
						continue;
					}
					if ($p->mdata['chargecode']!='5302') {
						continue;
					}
					$i++;
					$output->setCell('A' . $i, $sp[0]);
					$output->setCell('B' . $i, $postcode);
					$output->setCell('C' . $i, $desc);
					$output->setCell('D' . $i, $no);
					$output->setCell('E' . $i, $date);
				}
			}
		}
		$output->output($outputFile);
	}


	public function actionCalculate1()
	{
		$start_date=$_POST['start_date'];
		$end_date=$_POST['end_date'];
		$c=empty($_POST['org_id'])?1:$_POST['org_id'];
		$b=empty($_POST['courier_id'])?44:$_POST['courier_id'];
		$rs=ReconciliationLine::model()->with('parent')->findAll(
			'parent.invoice_date > :start_date AND  parent.invoice_date < :end_date',
				[":start_date"=>$start_date,":end_date"=>$end_date]
		);
		if (!empty($rs)) {
			$temp=[];
			$i=0;
			$fw=new FastwayAPI();
			
			foreach ($rs as $r) {
				$p = ImParcel::model()->find('ref = :ref', [':ref' => $r->shipment_no]);
				
				if (empty($p)) {
					continue;
				}
				$real_wt=$p->weight;
				if ($p->agent_id==1206) {
					$real_value=$p->getChargeByChargecode(9271, true);
				} else {
					$real_value=$p->getShipmentInvoice();
				}
				$send_wt=$p->weight;                 //$fw->roundWeight($p->weight);
				$send_value=$this->getCourierCostByShipment($p->ref, $send_wt, 101, $c, $b)['price'];
				$temp[$p->agent_id][]=['ref'=>$p->ref,'manifest'=>$r->parent->invoice_no,'real_wt'=>$real_wt,'real_value'=>$real_value,'send_wt'=>$send_wt,'send_value'=>$send_value,'fw_wt'=>$r->weight,'fw_value'=>$r->value,'date'=>$r->parent->invoice_date];
			}
			
			$output=new oExcel();
			$output->setCell('A1', 'date');
			$output->setCell('B1', 'customer');
			$output->setCell('C1', 'manifest');
			$output->setCell('D1', 'tracking no');
			$output->setCell('E1', 'Customer weight(Kg)');
			$output->setCell('F1', 'Customer Charge($)');
			$output->setCell('G1', 'modifed weight(Kg)');
			$output->setCell('H1', 'courier cost($)');
			$output->setCell('I1', 'fastway weight(Kg)');
			$output->setCell('J1', 'fastway charge($)');
			$output->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
			$m=2;
			foreach ($temp as $i=>$ts) {
				$customer='';
				$org= Org::model()->find('id=:id', [':id'=>$i]);
				if (!empty($org)) {
					$customer=$org->name;
				}
				foreach ($ts as $pd) {
					$output->setCell('A'.$m, $pd['date']);
					$output->setCell('B'.$m, $customer);
					$output->setCell('C'.$m, $pd['manifest']);
					$output->setCell('D'.$m, $pd['ref']);
					$output->setCell('E'.$m, $pd['real_wt']);
					$output->setCell('F'.$m, $pd['real_value']);
					$output->setCell('G'.$m, $pd['send_wt']);
					$output->setCell('H'.$m, $pd['send_value']);
					$output->setCell('I'.$m, $pd['fw_wt']);
					$output->setCell('J'.$m, $pd['fw_value']);
					$m++;
				}
			}
			$output->output('statics.xlsx');
		}
	}

	private function getCourierCostByShipment($shipmentRef, $charge_weight=0, $courierId = Org::ORGID_COURIER_AUPOST, $c=1, $b=44)
	{

		// make it fast we hardcode here currently
		$orgRates = [
			Org::ORGID_COURIER_FASTWAY => 52,  // you can get this id from Org -> Rates Tab -> Flex Rate by Zone hypelink will including this id
			Org::ORGID_COURIER_AUPOST => $b,
			//                        102=>75,// live 101 test 75   44
		];

		$price = 0;
		$consolId = 0;
		$shipment = Shipment::model()->find('ref = :ref', [':ref' => $shipmentRef]);
		if (!empty($shipment)) {
			$consolId = $shipment->consol_id;
			if (!empty($charge_weight)) {
				$weight=$charge_weight;
			} else {
				$weight = $shipment->weight;
			}
			
			// base on postcode to get related charge code
			$zoneMap = ZoneMap::model()->find(
				'org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code',
				[':oid' =>$courierId, ':zoneid' => 1, ':code' => $shipment->cnee->postcode]
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
		}
		return ['price' => $price, 'consol_id' => $consolId];
	}

	public function getCheapCost($shipment)
	{
		$couriers = [44,52];
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
							
			//for fastway and eparcel mixed service
			//                             if((in_array(52, $couriers))&& preg_match('/wa/i', $shipment->cnee->state)) {
			//                                    $key= array_search(52, $couriers);
			//                                      unset($couriers[$key]);
			//                                }
								 
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
					$cost = ImcoConsol::getCourierCostPrice($orgrate, $shipment->cnee->postcode, $singleWeight);
					$cost =$cost*$shipment->pkg;
					if ($cost > 0 &&  $cost < $minCost) {
						$minCost = $cost;
						$cheapOrgRate = $orgrate;
					}
				}
				return [$minCost,$cheapOrgRate->org_id];
			}
		}
	}

}
