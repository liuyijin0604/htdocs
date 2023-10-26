<?php
class OutturnController extends Controller{

	protected $nonAjax = ['export'];

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id){
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate(){
		$model=new Outturn;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(!empty($_POST)){
			$err = array();
			$data = array();
			if(is_uploaded_file($_FILES['manifest']['tmp_name'])){
				$xls = new oExcel;
				if(!$xls->supported($_FILES['manifest']['name'])){
					foreach($xls->getError() as $e){
						$err[] = $e;
					}
				}else{
					$xls->load($_FILES['manifest']['tmp_name']);
					$data = $xls->getAll();
					if(rtrim(implode(',', $data[2]),',') != 'ConsignmentRef,Status,ScannedDateTime,Count'){
						$err[] = 'Column Mismatch!';
					}
					unset($data[1]);
					unset($data[2]);
				}
			}else{
				$err[] = 'Manifest File Required.';
			}
			
			foreach($data as $k => $r){
				$p = ImParcel::model()->find('hbn = :n', array(':n' => $r[1]));
				if(empty($p)){
					$err[] = $r[1].' not found!';
				}
				/*elseif($p->ot_id > 0){
					$err[] = $r[1].' already outturned!';
				}
				if($k == 3){
					$pcc = sizeof($p->consol->shipments);
					$otc = sizeof($data);
					if($pcc != $otc){
						$err[] = $p->consol->awb.' has '.$pcc.' shipments not equal outturn'.$otc;
					}
				}*/
			}
			
			if(!empty($err)){
				foreach($err as $e){
					$model->addError('meta', $e);
				}
			}else{
				$model->save();
				foreach($data as $r){
					$p = ImParcel::model()->find('hbn = :n', array(':n' => $r[1]));
					if($p->status >= 60) continue;
					$p->ot_id = $model->id;
					if($r[2] == 'Clear'){
						$p->status = 60;
					}else{
						$p->status = 55;
					}
					$p->save();
				}
			}
			$this->ajaxResult($model, array('id'));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionScan(){
		if(isset($_POST['barcode'])){
			$r = new StdClass;

			$r->color = '#c00';
			$r->sound = 'not_found';
			$r->stop = 0;
            $r->nosound = 0;


            $barcode = $_POST['barcode'];
            $postcode = $_POST['barcode'];

            $r->msg = 'Not Found ' . $barcode;


			//aupost barcode
			if(preg_match('/019931265099999891([\d\w]{3}|[\d\w]{5})(\d{7})(\d{2})(\d{5})(\d{2})0\d{1}/',$postcode, $m) || preg_match('/997\d{5}([\d\w]{3})(\d{7})(\d{2})(\d{4})0\d{4}/',$postcode, $m)){
				$hbn = strtoupper($m[1].$m[2]);
				$sn = ltrim($m[3],'0');
				$p = ImParcel::model()->find('hbn = :r OR ref = :r', [':r' => $hbn]);
                $postcode = strtoupper($hbn.$m[3]);
			} else if ( preg_match('/^T\d{6}AWNL\d{10}/',$postcode, $m)  ){  // for toll barcode
                // toll barcode format : 'T207711AWNL5450160016';
                $tollBarcode = $m[0];
                $ref = substr($tollBarcode,7,10);;
                $p = ImParcel::model()->find('ref = :r', [':r' => $ref]);
                $hbn = (!empty($P)) ? $p->hbn : '';
            } else {
				if(preg_match('/-\d+$/',$postcode)){
					list($hbn, $sn) = explode('-', $postcode);
				}else{
					$hbn =$postcode;
					$sn = 0;
				}
				$p = ImParcel::fromBarcode($hbn);
				if(!empty($p)){
					$hbn = $p->hbn;
					$sn = $p->sn;
				}
			}
			$r->hbn = $hbn;
			if(empty($p) || empty($p->consol_id)){
				echo json_encode($r);
				Yii::app()->end();
			}
			if($p && $p->consol->type == 30){//local
					$r->msg = $p->getCarrier().', '.($p->zrate->orgrate->type == 20? 'Satchel, ':'').$p->getCarrierSeq($sn);
					$r->sound = strtolower(str_replace(' ','_', $p->getCarrier()).'-'.($p->zrate->orgrate->type == 20? 'satchel-':'').$p->getCarrierSeq($sn));
					$r->color = '#0c0';
					if($p->status < 65){
						$p->status = 65;
						$p->save();
					}
					$s = Storage::allocate(empty($_POST['wid'])? 106 : $_POST['wid'], 50, $p);
			} elseif ( $p && $p->consol->type == 15 ) { // for import parcel

                if( $p->status < 60 ) {
                    $r->msg = 'HELD'.', ' . $barcode ;
                    $r->sound = strtolower('held_alarm');
                    $r->stop = 1;
                   // $s = Storage::allocate(empty($_POST['wid'])? 106 : $_POST['wid'], 40, $p);
                }else{
                    $status = $p->getStatus();
                    $r->msg = strtoupper($status).', ' . $barcode;
                    $r->sound = strtolower($status);
                    $r->color = '#0c0';
                   // $s = Storage::allocate(empty($_POST['wid'])? 106 : $_POST['wid'], 50, $p);
                }

                // we only show status and sequence number
                // so comment the following lines
                /*
				if($p->status < 60){
					$r->msg = 'HELD'.', '.$p->getCarrier().', '.($p->zrate->orgrate->type == 20? 'Satchel, ':'').$p->getCarrierSeq($sn);
					$r->sound = strtolower('held_alarm');
					$r->stop = 1;
					$s = Storage::allocate(empty($_POST['wid'])? 106 : $_POST['wid'], 40, $p);
				}else{
					$r->msg = strtoupper($p->getStatus()).', '.$p->getCarrier().', '.($p->zrate->orgrate->type == 20? 'Satchel, ':'').$p->getCarrierSeq($sn);
					$r->sound = strtolower($p->getStatus().'-'.str_replace(' ','_', $p->getCarrier()).'-'.($p->zrate->orgrate->type == 20? 'satchel-':'').$p->getCarrierSeq($sn));
					$r->color = '#0c0';
					$s = Storage::allocate(empty($_POST['wid'])? 106 : $_POST['wid'], 50, $p);
				}
                */

				$p->mdata['scan_time'] = date('Y-m-d H:i:s');

                // use this field to save how many shipments has been scanned ( received by ourself)
				$p->scan_data[50][strtoupper($postcode)] = date('Y-m-d H:i:s');
				$p->save();
			}

            // in case from mobile device , no sound needed
            // if ( Utility::isMobileRequest() ) $r->nosound = 1;

			echo json_encode($r);
			Yii::app()->end();
		}

		$this->render('scan');
	}

	/**
	 * Lists and search.
	 */
	public function actionList(){
		$model=new Outturn('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Outturn']))
			$model->attributes=$_GET['Outturn'];

		$this->render('list',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=Outturn::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='outturn-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
