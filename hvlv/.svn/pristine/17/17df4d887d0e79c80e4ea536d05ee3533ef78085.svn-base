<?php

class InsuranceController extends Controller
{
	public function actionLodge()
	{
       // $model = ImParcel::model();
       // $model->setAttribute('status',80);
       // if ( isset($_GET['ImParcel']) ) {
       //     $model->attributes = $_GET['ImParcel'];
       // }
       // $this->render('scan',['model' => $model]);

        if(isset($_POST['barcode'])){
            $barcode = trim($_POST['barcode']);
            $r = new StdClass;
            $r->msg = 'Not Found';
            $r->color = '#c00';
            $r->sound = 'not_found';
            $r->status = 0;
            $r->id = '';

            // in case aupost barcode
            if(preg_match('/019931265099999891([\d\w]{3}|[\d\w]{5})(\d{7})(\d{2})(\d{5})(\d{2})0\d{1}/', $barcode, $m) || preg_match('/997\d{5}([\d\w]{3})(\d{7})(\d{2})(\d{4})0\d{4}/', $barcode, $m)){
                $hbn = strtoupper($m[1].$m[2]);
                $p = ImParcel::model()->find('(hbn = :r OR ref = :r)', [':r' => $hbn]);
            }else{
                // in case barcode with serial number
                if(preg_match('/-\d+$/',$barcode)){
                    list($hbn, $sn) = explode('-', $_POST['barcode']);
                }else{
                    // in case clean barcode
                    $hbn = $barcode;
                }
                $p = ImParcel::model()->find('(hbn = :n OR ref = :n)', array(':n' => $hbn));
            }
            if ( !empty($p) ) {

                $claim = InsuranceClaim::model()->find('shipment_id = :sid',[':sid' => $p->id]);
                if ( isset($claim) ) {
                    $r->msg = 'Shipment has been claimed before';
                    $r->color = '#c00';
                } else {
                    if ($p->insurance > 0) {
                        $r->msg = 'Yes you have an insurance, Please make a claim now';
                        $r->status = 1;
                        $r->color = '#0c0';
                    } else {
                        $r->msg = 'Shipment does not have an insurance';
                        $r->color = '#c00';
                    }
                }
            }
            $r->id = $p->id;
            if ( empty($p) || empty($p->consol_id) ){
                echo json_encode($r);
                Yii::app()->end();
            }

            echo json_encode($r);
            Yii::app()->end();
        }

        // in case make claim logic
        if(isset($_POST['sid'])){
            $this->makeClaim();
        }

        $this->render('lodge');
	}

    /**
     * make a claim now
     * save claim data ( including data and attached photos)
     */
    private function makeClaim(){

        $r = new StdClass;
        $r->msg = 'Unknown error';
        $r->color = '#c00';
        $r->status = 0;

        $shipmentId = $_POST['sid'];

        // note should not be empty
        $note = $_POST['note'];
        if ( empty($note) ) {
            $r->msg = 'Claim note must not be empty';
            echo json_encode($r);
            Yii::app()->end();
        }

        // check to see if has been claimed before
        $claim = InsuranceClaim::model()->find('shipment_id = :sid', [':sid' => $shipmentId] );
        if ( !empty($claim) ) {
            $r->msg = 'Shipment has been claimed before';
            echo json_encode($r);
            Yii::app()->end();
        }

        // save insurance claim
        $claim = new InsuranceClaim();
        $claim->date_added = date('Y-m-d H:i:s');
        $claim->status = 1; // new claim
        $claim->shipment_id = $shipmentId;
        $claim->note = $note;
        $claim->save();

        $newClaimId = $claim->id;

        $fileTypeError = '';
        if ( isset($newClaimId) ) {
            // save claim uploaded photos
            // save photo 1 if existing
            $photo = empty($_FILES['claim_pics1']) ? array() : $_FILES['claim_pics1'];
            if (!empty($photo['tmp_name']) && is_file($photo['tmp_name'])) {
                if ( $this->checkFileType($photo['type']) ){
                    FileRepo::storeFile($photo['tmp_name'], $photo['name'], 100, $newClaimId);
                } else {
                    $fileTypeError = 'Photo 1 format not supported';
                }
            }

            // save photo 2 if existing
            $photo = empty($_FILES['claim_pics2']) ? array() : $_FILES['claim_pics2'];
            if (!empty($photo['tmp_name']) && is_file($photo['tmp_name'])) {
                if ( $this->checkFileType($photo['type']) ) {
                    FileRepo::storeFile($photo['tmp_name'], $photo['name'], 100, $newClaimId);
                } else {
                    if ( !empty( $fileTypeError) ) {
                        $fileTypeError .= '<br/>';
                    }
                    $fileTypeError .= 'Photo 2 format not supported';
                }
            }

            // save photo 3 if existing
            $photo = empty($_FILES['claim_pics3']) ? array() : $_FILES['claim_pics3'];
            if (!empty($photo['tmp_name']) && is_file($photo['tmp_name'])) {
                if ( $this->checkFileType($photo['type']) ) {
                    FileRepo::storeFile($photo['tmp_name'], $photo['name'], 100, $newClaimId);
                } else {
                    if ( !empty( $fileTypeError) ) {
                        $fileTypeError .= '<br/>';
                    }
                    $fileTypeError .= 'Photo 3 format not supported';
                }
            }

            if ( !empty($fileTypeError) ) {
                // delete just created claim
                InsuranceClaim::model()->deleteByPk($newClaimId);
                $r->msg = $fileTypeError;
            } else {
                $r->msg = 'Claim added successfully!';
                $r->status = 1;
            }
        } else {
            $r->msg = 'Failed to make a claim';
            $r->status = 0;
        }

        echo json_encode($r);
        Yii::app()->end();
    }

    /**
     * claim photo we only supports png , jpg ,jpeg
     * @param $fileType
     * @return bool
     */
    private function checkFileType($fileType){
        if ( $fileType == 'image/png' ||
             $fileType == 'image/jpg' ||
             $fileType == 'image/jpeg'
        ) {
            return true;
        }
        return false;
    }

    /**
     * List all waiting for approving insurance
     */
    public function actionList(){

        // show all new claims
        $model = InsuranceClaim::model();
        $model->unsetAttributes();
        $model->setAttribute('status',1);

        if(isset($_GET['Insurance']))
            $model->attributes=$_GET['Insurance'];

        $this->render('list',array(
            'model'=>$model,
        ));
    }

    public function actionAll(){

        // show all new claims
        $model = InsuranceClaim::model();
        $model->unsetAttributes();
        if(isset($_GET['Insurance']))
            $model->attributes=$_GET['Insurance'];

        $this->render('all',array(
            'model'=>$model,
        ));
    }

    /**
     * @param $id
     */
    public function actionApprove($id){
        $model = InsuranceClaim::model()->findByPk($id);
        if ( isset($_POST['amount']) ) {
            $amount = $_POST['amount'];
            if ( $amount > 0 ) {
               // $this->createRTSResendInvoice($model,$amount);
                $model->mdata['approve_amount'] = $amount;
                $model->mdata['approve_date'] = date('Y-m-d H:i:s');
                $model->status = 4; // approved
                $model->save();

                // make credit for the customer
                $payment = new Payment();
                $payment->org_id = $model->shipment->agent_id;
                $payment->amount = $amount;
                $payment->ata = $amount;
                $payment->ref = 'for insurance claim : ' . $model->id;
                $payment->date = date('Y-m-d');
                $payment->type = 5; // default credit note
                $payment->bank = 90; // credit note
                $payment->currency = 1; // AUD
                $payment->status = 6; // post status
                $payment->save();

                $this->ajaxResult($model);

            } else {
                $this->ajaxResult($model,['id'],'Credit Amount must be great than zero');
            }
        }

        $this->render('approve',array(
            'model'=>$model,
        ));
    }

    /**
     * @param $id
     */
    public function actionReject($id){
        $model = InsuranceClaim::model()->findByPk($id);
        if ( isset($_POST['reason']) ) {
            $reason = $_POST['reason'];
            if ( !empty($reason)  ) {
                $model->mdata['reject_reason'] = $reason;
                $model->mdata['reject_date'] = date('Y-m-d H:i:s');
                $model->status = 3; // rejected
                $model->save();

                $this->ajaxResult($model);

            }
        }

        $this->render('reject',array(
            'model'=>$model,
        ));
    }

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}