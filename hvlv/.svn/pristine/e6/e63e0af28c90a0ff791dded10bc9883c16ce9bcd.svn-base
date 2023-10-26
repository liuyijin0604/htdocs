<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class UploadManiController extends Controller
{
	public $agent_id;
	public $chargecode;
	public $currency;
	public $isAus;
		
	protected $nonAjax=['createByMani','seperateShipment'];
	public function actionIndex()
	{
		$this->render('//import/shipment_mani',['modelType'=>ImParcel::$modelType]);
	}
	public function actionIndexTpl()
	{
		$this->render('//import/shipment_mani',['modelType'=>TplParcel::$modelType]);
	}
	public function log($l)
	{
		$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'shipmentapi' . DIRECTORY_SEPARATOR ;
		file_put_contents($tmp.'shipment_mani_'  . date('Y-m-d') . '.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
	}
		
		
	public function actionCreateByMani()
	{
		sleep(rand(1,10));
		$passCheck=true;      // use to check if all the result passed check. only true, then we do imput to the database.
		$model = new Manifest('upload');
		$checked= 0;   //empty($_POST['upcheck'])?1:0;
				 
		if (!empty($_FILES)) {
			$file = empty($_FILES['maniship_file'])? [] : $_FILES['maniship_file'];
			if (empty($file['tmp_name'])) {
				echo 'No file input';
				return;
			}
			// read all parcels data from excel template file
			$xls = new oExcel;
			$xls->supported($file['name']);
			$xls->load($file['tmp_name']);
			$data = $xls->getAll();
			$datakey  = MD5(json_encode($data)).strtotime("Y-m-d H:i");
			$checkProcess = Service::getCacheData($datakey);
			if(!empty($checkProcess))
			{
				echo  '<div style="color:red;">'."please retry after 5 minutes".'</div>';
				return;
			}
			$checkProcess = Service::setCacheData($datakey,"1",500);

				 
					 
					
			if ($data[1][3]!='WEIGHT'||$data[1][4]!='CNEE') {
				echo  '<div style="color:red;">'."Wrong Template Supplied".'</div>';
				return;
			}
			//check if agent and chargecode supplied
			$this->agent_id=empty($_POST['agent_id'])?'':$_POST['agent_id'];
			$this->chargecode=empty($_POST['charge_code'])?'':$_POST['charge_code'];
			$this->currency=empty($_POST['currency'])?'AUD':$_POST['currency'];
			$noAddrVeri = empty($_POST['noAddrVeri'])?false:true;
			$noDimRequired = empty($_POST['noDimRequired'])?false:true;
			$modelType = empty($_GET['modelType'])?0:$_GET['modelType'];
			if (empty($this->agent_id)) {
				echo  '<div style="color:red;">'."No agent_id supplied".'</div>';
				return;
			}
			if (empty($this->chargecode)) {
				echo  '<div style="color:red;">'."No chargecode supplied".'</div>';
				return;
			}
			$org = Org::model()->findByPk($this->agent_id);
			if (empty($org)) {
				echo  '<div style="color:red;">'."Unkown owner".'</div>';
				return;
			}
			// if ($org->overCreditLimit()) {
			// 	echo  '<div style="color:red;">Client '.$org->name.' credit limit exceeded, please contact accounts!</div>';
			// 	return;
			// }
			$checkArray=[];
			foreach ($data as $index=> $d) {
				if ($index==1) {
					continue;
				}
				if (!empty($d[2])) {
					if (in_array($d[2], $checkArray)) {
						{echo  '<div style="color:red;">Line '.$index.": connote ".$d[2]. " duplicate".'</div>'; return;}
					}
					$checkArray[]=$d[2];
				}
			}
			//check if the chargecode match the owner
			$r=ImportChargeCode::model()->find("chargecode=:chargecode", [":chargecode"=>$this->chargecode]);
			if (!empty($r)) {
				if (!in_array($this->chargecode, [9094,4356])&&$modelType!=TplParcel::$modelType) {
					if ($r->org_id!=$this->agent_id) {
						echo  '<div style="color:red;">'."chargecode: ".$this->chargecode.' don not belong to '.(empty($_POST['org_suggest'])?'':$_POST['org_suggest']).'</div>';
						return;
					}
				}
			} else {
				echo  '<div style="color:red;">'."chargecode: ".$this->chargecode.' Invalid</div>';
				return;
			}

			// check if any one of the shipments have errs before we save them
			$os=[];
			$imParcelService = new ImParcelService();
			$packagesInfo = $imParcelService->getAustwayPackagesData($file['name'],$file['tmp_name'],true);	
			$imsService = new ImsService($this->agent_id,$this->chargecode,$this->currency);

			foreach ($data as $l => $r) {
				if ($l == 1) {
					$this->isAus=true;
					continue;
				}
				if (empty($r[4])&&empty($r[3])) {
					continue;
				}  // use to check if blank rows.
				$o=$imsService->prepareData($r,$packagesInfo,true);
				if ($o->status) {
					$os[] = ChooseShipment::newShipmentWithChargeCode($o->shipmentData, true,null,$noAddrVeri,$noDimRequired);
				} else {
					$os[] = $o;
				}
			}
			foreach ($os as $i=>$o) {
				if (isset($o->success)&&$o->success) continue;
				$passCheck=false;
				if(empty($o->error))
				{
					echo  '<div style="color:red;">'."Line:".($i+2).'---'.$o->msg.'</div>';
				}else
				{
					echo  '<div style="color:red;">'."Line:".($i+2).'---'.implode('; ', $o->error).'</div>';
				}
			}
										 
			// passcheck means all the shipments have no errs that can be save one by one in the following steps.
			if ($passCheck) {
				// to check if we need to create new consol, if excel D4 cell have consol number, we would add all the shipment to the consol.
				if (!empty($data[1][28])) {
					$consol = ImcoConsol::model()->find("owner_id = :owner_id AND no = :no", ['owner_id'=>$this->agent_id,':no' => $data[1][28]]);
				}
				if (empty($consol)&&$modelType!=TplParcel::$modelType) {
					$consol = new ImcoConsol;
					$consol->owner_id =$this->agent_id;
					$consol->pol = $this->isAus?'AUSYD':'NZNZ';
					$consol->pod = $this->isAus?'AUSYD':'NZNZ';
					$consol->dpt_id = 106;
					$consol->eta = date('Y-m-d');
					$consol->created = date('Y-m-d');
					$consol->save();
					$consolArr = ['consol_id' => $consol->id];
				}else
				{
					$consolArr = null;
				}

				$imParcelService = new ImParcelService();
				$packagesInfo = $imParcelService->getAustwayPackagesData($file['name'],$file['tmp_name'],true);	
				$imsService = new ImsService($this->agent_id,$this->chargecode);

				$os=[];
				$err_out=false;
				$trans = Yii::app()->db->beginTransaction();
				try{
					foreach ($data as $l=>$r) {
						if ($l==1 || empty($r[4])) continue;
						$o=$imsService->prepareData($r,$packagesInfo,true);
						if ($o->status) {
							$os[$l] = ChooseShipment::newShipmentWithChargeCode($o->shipmentData, false, $consolArr,$noAddrVeri,$noDimRequired);
						} else {
							$os[$l] = $o;
						}

						if(!$os[$l]->success)
						{
							$err_out=true;
						}else
						{
							if($modelType==TplParcel::$modelType)
							{
								$p = ImParcel::model()->find('hbn = :hbn',[':hbn'=>$os[$l]->connote]);
								$p->status = ImParcel::STATE_LOCAL_ARRIVAL;
								$p->ot_id = TplParcel::$modelType;
								$p->save();
							}
						}
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}

				if (!$err_out) {
					foreach ($os as $l=>$o) {
						if(empty($o->error))
						{
							echo  '<div style="color:green;">'."Line:".$l.'---'.$o->msg.'---connote:'.(empty($o->connote)?'':$o->connote).'</div>';
						}else
						{
							echo  '<div style="color:green;">'."Line:".$l.'---'.join(';',$o->error).'---connote:'.(empty($o->connote)?'':$o->connote).'</div>';
						}

					}
					echo  '<div style="color:blue;">'."Consol:".(empty($consol->no)?'':$consol->no).'</div>';
				}
								 
				if ($err_out) {
					// $xls->setCell('AM1', $consol->no);
					foreach ($os as $l => $o) {
						// if ($o->success && $checked == 0) {
						// 	$xls->getActiveSheet()->removeRow($l, 1);
						// 	continue;
						// }
						if ($o->success) {
							$xls->setCell('AM' . $l, 'Success');
						} else {
							$xls->setCell('AM' . $l, 'Failed');
							$xls->setCell('AN' . $l, implode(', ', $o->error));
							$xls->getActiveSheet()->getStyle('AN' . $l)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
						}
						// $xls->setCell('AB' . $l, json_encode($o));
					}

					$xls->getActiveSheet()->getColumnDimension('AN')->setWidth(80);
					$xls->output(empty($consol->no)?"output":$consol->no.".xlsx");
				}
			} else {
				return;
			}
		}
		//        $this->ajaxResult($model, array('id', 'warns'));
	}

	public function actionSeperateShipment()
	{
		if(empty($_POST)&&empty($_FILES))
		{
			$this->render('//import/shipment_seperation');
			return "";
		}

		$checked= 0;   //empty($_POST['upcheck'])?1:0;
				 
		if (!empty($_FILES)) {
			$file = empty($_FILES['sepeship_file'])? [] : $_FILES['sepeship_file'];
			if (empty($file['tmp_name'])) {
				echo 'No file input';
				return;
			}
			// read all parcels data from excel template file
			$xls = new oExcel;
			$xls->supported($file['name']);
			$xls->load($file['tmp_name']);
			$data = $xls->getAll();
					 
					
			if ($data[1][2]!='REF-DELIVERY'||$data[1][3]!='REF-CUSTOM'||$data[1][4]!='SNO') {
				echo  '<div style="color:red;">'."Wrong Template Supplied".'</div>';
				return;
			}

			$checkArray=[];
			$isReturn = false;
			$allPkgs = 0.0;
			$pShipment = Imparcel::model()->find('ref = :ref',[':ref'=>$data[2][3]]);	

			if (empty($pShipment)) // if previous shipment is not found
			{		
				echo  '<div style="color:red;">previous ref '.$data[2][3]. " is not exist".'</div>';
				return ;
			}

			foreach ($data as $index=> $d) 
			{
				if ($index==1) {
					continue;
				}
				if (!empty($d[2])) 
				{
					$p = Imparcel::model()->find('ref = :ref',[':ref'=>$d[2]]);
					if (empty($p))
					{
						echo  '<div style="color:red;">Line '.$index.": ref ".$d[2]. " is not exist".'</div>';
						$isReturn = true;
					}else
					{
						if($p->pkg!=$d[4])
						{
							echo  '<div style="color:red;">Line '.$index.": ref ".$d[2]. " has {$p->pkg} packs, but ".$d[4]." provided </div>";
						}
					}

					$allPkgs += $d[4];
					$checkArray[]=$d[2];
				}
			}

			if($allPkgs!= $pShipment->pkg)// if the seperation amount num is not equal the total pkg of previous shipment
			{
					echo  '<div style="color:red;">previous ref '.$data[2][3]. " has {$pShipment->pkg} packs, but {$allPkgs} provided </div>";
					return;
			}

			if($isReturn)
			{
				return ;
			}

			unset($data[1]);

			$results = $this->row2dataSepe($data);
			if(!empty($results))
			{
				foreach ($results as $key => $result) 
				{
					echo  "<div style=\"color:green;\">Previous Ref:{$result->pref}; New Ref:{$result->newref}</div>";
				}
			}
			return;
		}
		//        $this->ajaxResult($model, array('id', 'warns'));
	}

	public function actionCheckChargecode()
	{
		$id = @$_GET['id'];
		// $user=User::model()->findByPk(Yii::app()->user->id);
		$rs= ImportChargeCode::model()->findAll('status=1 AND org_id=:oid', [':oid'=>$id]);
		$chargecodeInfo=[];
		foreach ($rs as $r) {
		    if(!empty($r->description))
		    {
		        $chargecodeInfo[$r->chargecode]=$r->chargecode."(".$r->description.")";
		    }else
		    {
		        $chargecodeInfo[$r->chargecode]=$r->chargecode;
		    }
		}
		if (empty($chargecodeInfo)) {
			echo json_encode(['done' => false]);
		}
		else{
			echo json_encode(['done' => true,'chargecodes'=>$chargecodeInfo]);
		}		
	}



	private function row2dataSepe($ds)
	{
		$snoAll = 0;
		foreach ($ds as $key => $d) 
		{
			$snoAll+=(int)$d[4];
		}
		$clsArr = [];
		$thisSno = 0;
		$errors = [];
		$transaction=  Yii::app()->db->beginTransaction();
		try 
		{
			$shipment = Imparcel::model()->find('ref = :ref',[":ref"=>$ds[2][3]]);
			$shipment->mdata["changeLabel"] = $snoAll;
			$shipment->save();
			foreach ($ds as $key => $d) 
			{
				$mySno = 1;
				for ($i=1; $i <= $d[4]; $i++) 
				{ 
					$thisSno ++;
					$model = ChangeShipmentLabel::model()->find("pref = :pref",[":pref"=>$d[3]."-".$thisSno]);
					if(empty($model))
					{
						$model = new ChangeShipmentLabel(); 
					}
					$shipment = Imparcel::model()->find('ref = :ref',[":ref"=>$d[3]]);
					$model->pid = $shipment->id;
					$model->pref = $d[3]."-".$thisSno;
					$model->phbn = $shipment->hbn;
					if($d[4]>1)
					{
						$model->newref = $d[2]."-".$mySno;
					}else
					{
						$model->newref = $d[2];
					}
					if(!$model->save())
					{
						foreach ($model->getErrors() as $key => $error) 
						{
							$errors[] = '<div style="color:red;">Line ref '.$error[0]. '</div>';
						}
					}else
					{
						$mySno++;
						$clsArr[] = $model;
					}
				}
			}
			$transaction->commit();
			if(!empty($errors))
			{
				foreach ($errors as $key => $value) {
					echo $value;
				}
				return [];
			}else
			{
				return $clsArr;
			}
			
		}catch (Exception $ex) 
		{
			$transaction->rollback();
			throw $ex;
		}

		return [];

	}

	public function actionAjaxFastwayCheck()
	{
		$suburb = $_POST['suburb'];
		$postcode = $_POST['postcode'];
		$weight = $_POST['weight'];
		$state = $_POST['state'];

		$resp = new stdClass();
		$resp->msg = 'Congratulations! Fastway can delivery your parcel';
		$fwApi = new FastwayAPI();
		if (isset($org->extra['maxwt'])) {
			$maxWeight = $org->extra['maxwt'];
		}
		$canDelivery = FastwayAPI::isInDeliveryArea($postcode, $suburb);
		if (!$canDelivery) {
			$resp->msg = 'Sorry! Fastway can not delivery to : ' . $suburb . ' ' . $postcode . ' ' . $state;
		} else {
			if ($fwApi->getProductCode($postcode, $weight)  == -1) {
				$resp->msg = 'Sorry! Fastway can not delivery to : ' . $suburb . ' ' . $postcode . ' ' . $state;
			}
		}

		echo json_encode($resp);
		Yii::app()->end();
	}

}
