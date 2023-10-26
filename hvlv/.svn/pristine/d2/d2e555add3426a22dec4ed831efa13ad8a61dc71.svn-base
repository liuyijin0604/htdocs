<?php

class WarehouseController extends Controller{
	
	public function actionIn(){
		if(!empty($_POST)){
			$o = new ImParcel('scan');
			$r = $o->scanBarcode(array($_POST['barcode'], $_POST['dpt_id'], 25));

            // allocate the storage area for the parcel
            $storage = $this->allocateStorage($_POST['barcode'],$_POST['dpt_id']);
            if ( !empty($storage) ) {
                $r['storage_result'] = 1;
                $r['storage_msg'] = $storage['storage']->name;
            } else {
                $r['storage_result'] = 0;
                $r['storage_msg'] = $storage['storage']->name . ' is FULL';
            }

			echo json_encode($r);

            Yii::app()->end();
		}

		$this->render('scan');
	}

    /**
     * check the real weight by bulk
     */
    public function actionAjaxCheckWeight(){
        $r = array('success' => 0, 'msg' => 'Unknown error');
        // manifest ID format as : PCAM-*****
        // **** will be real manifest ID
        $mid = strtoupper(trim($_POST['barcode']));
        $mid = intval(str_replace('PCAM-', '', $mid));

        $dpt_id = 0;
        if ( isset($_POST['dpt_id']) ) {
            $dpt_id = intval(trim($_POST['dpt_id']));
        }
        $allCBM = 0.0;
        if ( isset($_POST['cbm']) ) {
            $allCBM = floatval(trim($_POST['cbm']));
        }

        if ($mid > 0) {
            $manifest = Manifest::model()->findByPk($mid);
            if (!empty($manifest)) {
                if (isset($_POST['w'])) {
                    // save all real checked weight
                    $weights = $_POST['w'];
                    $allWeights = 0.0;
                    foreach ($weights as $k => $w) {
                        $allWeights += floatval($w);
                    }
                    // we save checked weight in meta field with key checkweight
                    if ($allWeights > 0) {

                        $canReceive = true;
                        // we should check the weight
                        // if real weight is over specified the weight threshold
                        // we shouldn't receive the parcels
                        $weightThreshold = 0;
                        if ( isset($manifest->owner->extra['wthreshold']) ) {
                            $weightThreshold = $manifest->owner->extra['wthreshold'];
                        }
                        $totalWeight = $manifest->totWeight();
                        if ( $weightThreshold > 0 && $allWeights > $totalWeight ) {
                            if  ( round((( $allWeights - $totalWeight ) / $totalWeight ) * 100) > $weightThreshold ) {
                                $canReceive = false;
                            }
                        }

                        if ( !$canReceive ) {
                            $agree_average_extra_weight = 0;
                            if ( isset($_POST['average_extra']) ) {
                                $agree_average_extra_weight = intval($_POST['average_extra']);
                            }
                            if ( $agree_average_extra_weight == 0 ) {
                                $r['success'] = 0; // check weight failed
                                $r['msg'] = "Real weight is: $allWeights kg, there is big gap with: $totalWeight kg, we can't accept them, Please do as the following: <br>";
                                $r['msg'] .= 'Does the client agree with puting the extra weight into parcels averagely ? <button class="btn btn-primary btn-lg" id="extra-weight-confirm">Yes</button>';
                                $r['msg'] .= '&nbsp;&nbsp;<button class="btn btn-primary btn-lg" id="extra-weight-cancel">No</button>';
                            } else {
                                // put extra weight into parcels averagely
                                $canReceive = $this->averagePutinExtraWeight($manifest,$allWeights - $totalWeight);
                            }
                        }

                        if ( $canReceive ) {
                            $manifest->mdata['checkweight'] = $allWeights;
                            $manifest->mdata['cbm'] = $allCBM;
                            $manifest->dpt_id = $dpt_id;
                            $manifest->save();
                            $r['success'] = 1; // check weight save successfully
                            $r['id'] = $mid;
                            $r['totPacks'] = $manifest->totPacks();
                            $r['totWeight'] = $manifest->totWeight();
                            $r['checkWeight'] = $allWeights;

                            // set all shipments in the manifest as Manifested status
                            $this->receivedShipmentsByManifest($manifest, $dpt_id);

                            // make invoice for this client now
                            $this->makeManifestInvoice($manifest, $dpt_id);
                        }

                    } else {
                        $r['msg'] = 'Total weight must be more than zero';
                    }
                }
            }
        }
        echo json_encode($r);
    }

    /**
     * adjust all manifest parcel weight maybe minus or add
     * @param $mid
     * @param $extraWeight
     */
    private function adjustRealWeight($mid,$realWeight){

        $manifest = Manifest::model()->findByPk($mid);
        if ( empty($manifest) ) return false;

        $totalWeight = $manifest->totWeight();
        if ( $totalWeight <= 0.0 || $realWeight <= 0.0 ) return false;

        // get all shipments
        $parcels = ImParcel::model()->findAll('man_id = :mid', array(':mid' => $manifest->id));

        if ( $totalWeight > 0 ) {
            foreach ($parcels as $parcel) {
                $ratio = $parcel->weight / $totalWeight;
                // put extra weight in
                $parcel->nolog = true;
                $parcel->weight = $ratio * $realWeight;;
                $parcel->update('weight');
                $parcel->nolog = false;

            }
        }
        return true;
    }

    /**
     * put all extra weight into manifest parcels averagely
     * @param $manifest
     * @param $extraWeight
     */
    private function averagePutinExtraWeight(&$manifest,$extraWeight){

        $totalWeight = $manifest->totWeight();
        if ( $totalWeight <= 0.0 || $extraWeight <= 0.0 ) return false;

        // get all shipments
        $parcels = ImParcel::model()->findAll('status = 10 AND man_id = :mid', array(':mid' => $manifest->id));

        if ( $totalWeight > 0 ) {
            foreach ($parcels as $parcel) {
                $ratio = $parcel->weight / $totalWeight;
                $extra = $ratio * $extraWeight;

                // put extra weight in
                $parcel->nolog = true;
                $parcel->weight += $extra;
                $parcel->update('weight');
                $parcel->nolog = false;

            }
        }
        return true;
    }

    /**
     * set all shipments in specified manifest as received status
     * @param $mid
     */
    private function receivedShipmentsByManifest(&$manifest,$dptId){
        // only for shipments status is new or picked up
        // 10 - for new status
        // 20 - for picked up status
        // 25 - for received status we should set them as 25
        ImParcel::model()->updateAll(array('status' => 25,'odpt_id' => $dptId),'man_id = :mid', array(':mid' => $manifest->id));

        // .. TODO confirm with Frank
        // for import parcel we need mani_map table?

        // map all import parcels of the specified manifest to mani_map table
        /*
        $allParcels = ImParcel::model()->findAll('man_id = :mid AND status = 25 AND odpt_id = :did',array(':mid' => $manifest->id,':did' => $dptId));
        if ( !empty($allParcels) ) {
            foreach ($allParcels as $parcel) {
                $manifest->map($parcel);

                // add tracking information for this parcel
                $ht = Tracking::model()->count('pid = :pid AND type = :t', array(':pid' => $parcel->id, ':t' => 15));
                if (empty($ht)) {
                    $t = $parcel->addTracking(15, 'Parcel Received', '','', 0, Yii::app()->user->id);
                }
            }
        }*/
    }

    /**
     * recreate manifest invoice again
     * @param $mid
     */
    private function reCreateManifestInvoice($mid){
        $manifest = Manifest::model()->findByPk($mid);
        if ( empty($manifest) ) return;

        $owner = $manifest->owner;

        if( empty($owner) ) return ['Manifest owner is invalid'];

        // if existing or not checking
        $inv = Invoice::model()->with('lines')->find('type = 10 AND to_id = :id AND lines.model = :m AND lines.fid = :fid', [':id' => $manifest->fwd_id, ':m' => 'Manifest', ':fid' => $manifest->id]);

        // if not existing or not pending or posted we create a new one
        $isUpdate = true;
        if(empty($inv) || ( $inv->status > 2 )) {
            $inv= new Invoice;
            $isUpdate = false;
        }

        $inv->type = 10; // for import parcels
        $inv->dpmt = Invoice::DPMT_IMPORT;

        $inv->to_id = $manifest->fwd_id;
        $inv->man_id = $manifest->id;
        $inv->dpt_id = $manifest->dpt_id;

        $inv->status = 2; // set as posted which means will send to client for paying
        $inv->date = date('Y-m-d');

        // try to get org price rate
        $orgRate = OrgRate::model()->find('org_id = :oid AND type = 40',[':oid' => $owner->id]);
        if ( empty($orgRate) ) return ['Owner: ' . $owner->id . ' price rate is not available yet'];

        $inv->currency = $orgRate['currency'];

        // calculate all price based on weight for each shipment
        $items = array();
        $tot = 0;

        // only for received shipments( status = 25)
        $shipments = ImParcel::model()->findAll('odpt_id = :did AND man_id = :mid', array(':did' =>  $manifest->dpt_id,':mid' => $manifest->id));
        foreach($shipments as $p){
            if($p->agent_id != $manifest->fwd_id) continue; // check to see if this parcel belong to this owner again
            $amt = $p->getChargePro($manifest->owner, $orgRate);
            $items[] = array($p->hbn, $p->getDesc(), $p->pkg, $p->weight, $p->cbm, $amt[0], $amt[1], $amt[2], $amt[3]);
            $tot += $amt[0];
        }
        $inv->mdata['name'] = $owner->name;
        $inv->mdata['address'] = $owner->getAddress();
        $inv->mdata['payterm'] = empty($owner->extra['payterm'])? '2 days' : $owner->extra['payterm'].' days';
        $inv->due = Invoice::calcDue($inv->date, $inv->mdata['payterm']);
        $inv->total = $tot;
        $inv->save();

        // remove old invoice lines if existing
        if ( $isUpdate ) {
            // in case update , we should remove all old invoice lines
            InvLine::model()->deleteAll('inv_id = :lid' ,[':lid' => $inv->id]);
        }

        $il = new InvLine;
        $il->inv_id = $inv->id;
        $il->amount = $inv->total;
        $il->mdata['items'] = $items;
        $il->model = 'Manifest';
        $il->fid = $manifest->id;
        $il->save();

        // check to see if owner's credit is enough now
        // if yes, we set manifest status as ConsolWaiting (12)
        // otherwise set as PayWaiting (11)
        if (  $this->canPayManifest($manifest) ) {
            $manifest->status = 12; // can be consolidated now
        } else {
            $manifest->status = 11; // waiting for being paid
        }
        $manifest->save();

        // push the invoice to Xero
        $this->saveInvoic2Xero($inv,$il);

        return array();
    }

    /**
     * save all invoices to Xero system
     * @param $data
     */
    private function saveInvoic2Xero(&$data,&$line){
        $invoice = new XeInvoice('ACCREC');
        $invoice->status = 'AUTHORISED';
        $invoice->invoiceNumber = $data->no;

        $curIndex = $data->currency;
        $curCode = 'AUD';
        if ( isset(Invoice::$currencies[$curIndex]) ) {
            $curCode = Invoice::$currencies[$curIndex];
        }
        $invoice->currencyCode = $curCode;

        $contact = new XeContact();
        $contact->name = $data->cust->name;
        $contact->accountNumber = 'PORG-' . $data->cust->id;
        $invoice->contact = $contact;
        $invoice->date = date('Y-m-d');
        $invoice->dueDate = $data->due;

        // Exclusive - exclude GST
        // Inclusive - include GST
        // NoTax
        if ( $data->gst > 0 ) {
            $invoice->lineAmountTypes = 'Exclusive';
        } else {
            $invoice->lineAmountTypes = 'NoTax';
        }

        $invoiceAccountCode = AppHelper::getXeroSetting('import_invoice_glcode');
        if ( strtoupper(substr($data->no,0,2)) == 'EX' ) { // in case export invoice
            $invoiceAccountCode = AppHelper::getXeroSetting('export_invoice_glcode');
        }
        // add all items
        foreach ( $line->mdata['items'] as $item ) {
            // in item array($p->hbn, $p->getDesc(), $p->pkg, $p->weight, $p->cbm, $amt[0], $amt[1], $amt[2], $amt[3])
            // we map quantity = 1 , amount = total
            $itemData = array();
            $itemData['quantity'] = 1;
            $itemData['accountCode'] = $invoiceAccountCode;
            $itemData['description'] = '【'.$item[0] . '】 ' . $item[1];
            $itemData['unitAmount'] = $item[5];
            $tracking = Invoice::getTrackingInfo($data);
            $itemData['trackingName'] = $tracking['name'];
            $itemData['trackingValue'] = $tracking['value'];
            $invoice->addLineItem($itemData);
        }

        try {
            $rt = $invoice->save();
            if ( $rt ) {
                // set sync to Xero successful flag
                $data->sync_xero = 1;
                $data->update('sync_xero');
            } else {
                // log error message
                Yii::app()->xero->log( 'failed to save invoice to xero for : ' . $data->no,Xero::LOG_LEVEL_ERR );
            }
        } catch ( Exception $mye ) {
            $msg = 'failed to save invoice - ' . $mye->getMessage();
            $msg .= PHP_EOL;
            $msg .= 'Invoice Data : ' . json_encode( $invoice ,JSON_PRETTY_PRINT);
            Yii::app()->xero->log( $msg ,Xero::LOG_LEVEL_ERR);
        }
    }

    /**
     * make shipment invoice
     * based on client user's price which we provide based on client's selection
     * @param $mid
     * @param $dptId - warehouse id
     */
    private function makeManifestInvoice(&$manifest,$dptId){
        $owner = $manifest->owner;

        if( empty($owner) ) return ['Manifest owner is invalid'];

        // if existing or not checking
        $inv = Invoice::model()->with('lines')->find('type = 10 AND to_id = :id AND lines.model = :m AND lines.fid = :fid', [':id' => $manifest->fwd_id, ':m' => 'Manifest', ':fid' => $manifest->id]);

        // if not existing or not pending or posted we create a new one
        $isUpdate = true;
        if(empty($inv) || ( $inv->status > 2 )) {
            $inv= new Invoice;
            $isUpdate = false;
        }

        $inv->type = 10; // for import parcels
        $inv->dpmt = Invoice::DPMT_IMPORT;

        $inv->to_id = $manifest->fwd_id;
        $inv->man_id = $manifest->id;
        $inv->dpt_id = $dptId;

        $inv->status = 1; // set as posted which means will send to client for paying
        $inv->date = date('Y-m-d');

        // try to get org price rate
        $orgRate = OrgRate::model()->find('org_id = :oid AND type = 40',[':oid' => $owner->id]);
        if ( empty($orgRate) ) return ['Owner: ' . $owner->id . ' price rate is not available yet'];

        $inv->currency = $orgRate['currency'];

        // calculate all price based on weight for each shipment
        $items = array();
        $tot = 0;

        // only for received shipments( status = 25)
        $shipments = ImParcel::model()->findAll('status = 25 AND odpt_id = :did AND man_id = :mid', array(':did' => $dptId,':mid' => $manifest->id));
        foreach($shipments as $p){
            if($p->agent_id != $manifest->fwd_id) continue; // check to see if this parcel belong to this owner again
            $amt = $p->getChargePro($manifest->owner, $orgRate);
            $items[] = array($p->hbn, $p->getDesc(), $p->pkg, $p->weight, $p->cbm, $amt[0], $amt[1], $amt[2], $amt[3]);
            $tot += $amt[0];
        }
        $inv->mdata['name'] = $owner->name;
        $inv->mdata['address'] = $owner->getAddress();
        $inv->mdata['payterm'] = empty($owner->extra['payterm'])? '2 days' : $owner->extra['payterm'].' days';
        $inv->due = Invoice::calcDue($inv->date, $inv->mdata['payterm']);
        $inv->total = $tot;
        $inv->save();

        // remove old invoice lines if existing
        if ( $isUpdate ) {
            // in case update , we should remove all old invoice lines
            InvLine::model()->deleteAll('inv_id = :lid' ,[':lid' => $inv->id]);
        }

        $il = new InvLine;
        $il->inv_id = $inv->id;
        $il->amount = $inv->total;
        $il->mdata['items'] = $items;
        $il->model = 'Manifest';
        $il->fid = $manifest->id;
        $il->save();

        // check to see if owner's credit is enough now
        // if yes, we set manifest status as ConsolWaiting (12)
        // otherwise set as PayWaiting (11)
        if (  $this->canPayManifest($manifest) ) {
            $manifest->status = 12; // can be consolidated now
        } else {
            $manifest->status = 11; // waiting for being paid
        }
        $manifest->save();

        // sync invoice with Xero system
        $this->saveInvoic2Xero($inv,$il);

        return array();

    }

    /**
     * check to see if the manifest can be paid by owner's credit
     * first we check the owner's credit limitation ( set by administrator)
     * then check the owner's credit terms ( set by administrator as well)
     * if both conditions are matched return true
     * otherwise false returned
     * @param $manifest
     * @return bool
     */
    private function canPayManifest(&$manifest){
        $canBePaid = false;
        $owner = $manifest->owner;
        if ( empty($owner) ) return false;

        $creditLimit = $owner->extra['creditlimit'];  // credit limitation
        $creditTerms = $owner->extra['creditterms'];  // credit valid terms for example 14 days
        $creditInitDate = $owner->extra['credit_init_date']; // credit calculate start date

        // check to see if credit expired
        $expiredDate = strtotime( $creditInitDate .' +' . $creditTerms . ' days');
        if ( $expiredDate >= strtotime(date('Y-m-d')) ) {
            // get owner's total not paid invoice
            $total = $owner->getOrgTotalNotPaidAmount($manifest->dpt_id);
            if ( $creditLimit >= $total ) {
                $canBePaid = true;
            } else {
                //   @TODO need send owner notice email now
                //   ......
            }
        } else {
            //   @TODO need send owner notice email now
            //   ......
        }
        return $canBePaid;
    }


    /*
     * will receive all shipments by manifest ID
     * actually only check total weight of the same manifest ID
     */
    public function actionBulkIn(){

        if(!empty($_POST)) {
            $r = array('success' => 0, 'msg' => '');
            // manifest ID format as : PCAM-*****
            // **** will be real manifest ID
            $mid = strtoupper(trim($_POST['barcode']));
            $mid = intval(str_replace('PCAM-', '', $mid));
            if ($mid > 0) {
                $manifest = Manifest::model()->findByPk($mid);
                if (!empty($manifest)) {

                    // in case manifest has been received , you can receive it again
                    if ( $manifest->dpt_id > 0 ) {
                        $r['success'] = 0;
                        $r['msg'] = 'Manifest : ' . $_POST['barcode'] . ' has been received before';
                    } else {
                        // just new found manifest
                        $r['success'] = 1;
                        $r['id'] = $mid;
                        $r['totPacks'] = $manifest->totPacks();
                        $r['totWeight'] = $manifest->totWeight();
                    }
                } else {
                    $r['success'] = 0;
                    $r['msg'] = 'Manifest : ' . $_POST['barcode'] . ' Not Found';
                }
            }
            echo json_encode($r);
            Yii::app()->end();
        }
        $this->render('bulkin');
    }

    private function allocateStorage($hbn,$wid){
        // set which area this parcel should be stored in
        $storageType = 10 ; // storage area only
        $rt = array();
        $p = ImParcel::model()->find('hbn = :h', array(':h' => $hbn));
        if( !empty($p) ) {
            if ( isset($p->mdata['tracking']) && $p->mdata['tracking'] == 1 ) {
                // try to put into full tracking area (eParcel area) , tagged as 'FT'
                $rt['storage'] = Storage::allocateInDestArea($wid,$storageType,'FT',$p);
            } else {
                // try to put into semi tracking area (BPA or Letters), tagged as 'ST'
                $rt['storage'] = Storage::allocateInDestArea($wid,$storageType,'ST',$p);;
            }
        }
        return $rt;
    }

	public function actionManualin(){
		if(!empty($_POST['labels'])){
			foreach($_POST['labels'] as $l){
				$p = ImParcel::model()->findByPk($l);
				$p->status = 25;
				$p->odpt_id = $_POST['dpt_id'];
				$p->save();

                // allocate the storage area for the parcel
                $this->allocateStorage($p->hbn,$_POST['dpt_id']);

			}
			$this->ajaxResult($p);
		}

        $model=new ImParcel('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['ImParcel'])) {
            $model->attributes = $_GET['ImParcel'];
        } else {
            $model->status = 10; // default only get status new parcel
        }
        $this->render('manualin',array(
            'model'=>$model,
        ));

	}
	
	public function actionUpdate($id){

        $model = ImParcel::model()->findByPk($id);
        if ( $model === null )
            throw new CHttpException(404,'The requested page does not exist.');

        if(!Acl::hasAccess('B:Import/SeeAllShipments') && !empty($model->agent_id) && $model->agent_id != Yii::app()->user->org) Acl::denied403();

        if(!empty($_POST)){
            $_POST = utf8zts::t2sArray($_POST);
            $model->cnor->attributes = $_POST['Cnor'];
            $model->cnor->save();
            $model->cnee->attributes = $_POST['Cnee'];
            $model->cnee->save();
            $model->attributes=$_POST[get_class($model)];

            if(empty($model->cnor_id)) $model->cnor_id = $model->cnor->id;
            if(empty($model->cnee_id)) $model->cnee_id = $model->cnee->id;

            // remove inalid items
            $items = $_POST['items'];
            foreach($items['q'] as $i=>$qty){
                if(empty($qty)){
                    unset($items['q'][$i]);
                    unset($items['g'][$i]);
                    unset($items['g_zh'][$i]);
                    unset($items['pid'][$i]);
                    unset($items['hs'][$i]);
                    unset($items['v'][$i]);
                    continue;
                }
            }
            $model->eitems = $items;
            $model->state = $model->cnee->state;
            $model->postcode = $model->cnee->postcode;
            $model->save();
            $this->ajaxResult($model, ['id', 'hbn', 'status'], 'Shipment updated successfully.');
        }

        $this->render('update', ['model' => $model]);
    }
}
