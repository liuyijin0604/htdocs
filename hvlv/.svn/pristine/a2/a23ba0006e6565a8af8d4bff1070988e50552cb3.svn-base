<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/05/2016
 * Time: 4:38 PM
 */


/**
 * Cron to retrieve invoice status from Xero
 * Class XeroCommand
 */
class XeroCommand extends CConsoleCommand {

    private $debug = true;

    public function run($args) {

        if(!empty($args[0]) && method_exists($this, $args[0])){
            $this->{$args[0]}();
        }
    }

    /**
     * fixed storage invoice issue - forget GST
     */
    private function fixStorageInvoice(){
        $no = $this->prompt('Storage Invoice ID: ');
        echo 'got storage invoice ID : ' . $no . PHP_EOL;

        $invoice = Invoice::model()->findByPk($no);
        if ( empty($invoice) ) {
            echo 'Inovice not found';
            return;
        }

        if ( $invoice->type != 35 ) {
            echo 'Sorry , Not Storage invioce';
            return;
        }

        if ( $invoice->gst > 0 ) {
            echo 'GST issue has been fixed';
        }

        // fix the GST issue now
        $orgTotal = $invoice->getTotal();
        $invoice->total = $orgTotal * 1.1; // add GST now
        $invoice->gst = $orgTotal * 10 / 100;
        $invoice->save();

        echo 'GST issue fixed';
    }

    /**
     * sync one specified invoic from HVLV to Xero
     */
    private function pushoneinvoice(){
        $no = $this->prompt('Invoice ID: ');
        echo 'got invoice ID : ' . $no . PHP_EOL;
        $ids = array($no);
        $this->pushInvoicesByIDS($ids);
        echo 'DONE' . PHP_EOL;
    }

    /**
     * recreate or refresh specified console's invoice
     */
    private function rcinvoice(){
        $no = $this->prompt('Consol No: ');
        echo 'got console NO. : ' . $no . PHP_EOL;
        $model = ImcoConsol::model()->find('no = :no',[':no' => $no]);
        if (!empty($model) ) {
            echo 'Found console ID: ' . $model->id . PHP_EOL;
            echo 'creating invoice now ' . PHP_EOL;
            $model->genSpecialInvoice(true, true);
            echo 'Invoice created ' . PHP_EOL;
        } else {
            echo $no . ' Not found ' . PHP_EOL;
        }
    }

    /**
     * recreate or refresh specified console's invoice by console's ID
     */
    private function rcinvoicebyid(){
        $no = $this->prompt('Consol ID: ');
        echo 'got console ID. : ' . $no . PHP_EOL;
        $model = ImcoConsol::model()->findByPk( $no);
        if (!empty($model) ) {
            echo 'Found console ID: ' . $model->id . PHP_EOL;
            echo 'creating invoice now ' . PHP_EOL;
            $model->genSpecialInvoice(true,true);
            echo 'Invoice created ' . PHP_EOL;
        } else {
            echo $no . ' Not found ' . PHP_EOL;
        }
    }

    /**
     * call by HVLV UI not for cron
     */
    public function syncXeroByCall(){
        $this->syncxero();
    }

    /**
     * sync invoice status from Xero server
     */
    private function syncxero(){

        // do nothing currently , stop sync with old xero account now
        return;

        /*
        Yii::app()->xero->log('Begin sync xero',Xero::LOG_LEVEL_INFO);

        // check to see if sync xero switch on or not
        if ( Yii::app()->params['settings']['sync_xero_enable']['value'] == 0 ) {
            $this->echo_debug('! sync xero disabled now');
            Yii::app()->xero->log('! sync xero disabled now');
            return ;
        }

        $pid = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'xero_sync.pid';
        if ( is_file($pid) && filectime($pid) > time() - 600 ) return false;
        file_put_contents($pid, '1');

        // create invoices in HVLV
        $this->echo_debug('=== create invoices in HVLV ===');
        Yii::app()->xero->log('create invoices in HVLV',Xero::LOG_LEVEL_INFO);
        $this->createHvlvInvoices();

        // push invoice from HVLV to Xero
        $this->echo_debug('=== push invoices and payments to Xero ===');
        Yii::app()->xero->log('push invoices and payments to Xero',Xero::LOG_LEVEL_INFO);
        $this->pushData();

        // get invoice status from Xero
        $this->echo_debug('=== update invoices status and payments from Xero ===');
        Yii::app()->xero->log('update invoices status and payments from Xero',Xero::LOG_LEVEL_INFO);
        $this->pullData();

        // delete temporary file , for next time running again
        unlink($pid);

        $this->echo_debug('=== ALL DONE ===');
        Yii::app()->xero->log('This time sync xero done',Xero::LOG_LEVEL_INFO);
*/
    }

    private function syncpayments(){
        $this->pullPayments();
        $this->pullCreditNotes();
    }

    /**
     * recreate all invoice for AUME (id:1125)
     * in order to change all 3 milk container change weight from 3.6 -> 3.55
     */
    public function recreateInvoices1125(){
        $invoices = Invoice::model()->findAll('to_id = 1125');

        foreach ( $invoices as $invoice ) {
            $tot = 0;
            // update each invoice line
            foreach ( $invoice->lines as $line ) {
                // get line related parcel
                $items = [];
                $stot = 0;
                foreach ( $line->mdata['items'] as $k => $item ) {
                    $hbn = $item[0];
                    $parcel = ExParcel::model()->find('hbn = :hbn',[':hbn' => $hbn]);
                    if ( !empty($parcel) ) {
                        // change weight , recreate amount for the parcel again
                        $rate = $parcel->getAgentRate1125();
                        $weight = $parcel->chargeWeight1125();
                        $typ = $parcel->goodsType();
                        $duty = $rate[0] == 'EC'? round($parcel->calTariff() / 4.75 * 100) / 100 : 0;
                        $items[] = array($parcel->hbn, $parcel->cnor->name, $weight, $parcel->cnee->state, $typ, $rate[0], $rate[1]->perkg, $rate[2], $duty);
                        $stot += $rate[2] + $duty;
                    }
                }
                $line->mdata['items'] = $items;
                $line->amount = $stot;
                $line->save();

                $tot += $stot;
            }

            // update invoice total now
            $invoice->total = $tot;
            $invoice->update('total');
        }

        echo 'All Done!' . PHP_EOL;
    }

    /**
     * create invoices for HVLV firstly
     */
    public function createHvlvInvoicesByCommand(){
        $ais = [];
        $ndyet = [];
        // current only for Sydney warehouse only
        $rs = PickupList::model()->findAll(['condition' => 'bwf & 1 = 0 AND dpt_id = :wid AND created < CURDATE()', 'params' => [':wid' => 106 ], 'order' => 'created']);
        foreach($rs as $r){
            if(empty($r->lines) || in_array($r->fwd_id, $ndyet)) continue;
            $bd = $r->billdate();
            if(strtotime($bd) > time()){
                $ndyet[] = $r->fwd_id;
                continue;
            }
            $err = false;
            $pc = 0;
            foreach($r->lines as $l){
                $p = $l->mm();
                if ( !isset($p) ) continue;
                if ( in_array($p->status, [100])) continue;
                if($p->status == 12){
                    echo $p->hbn . ' just picked up' . PHP_EOL;
                    $err = true;
                    break;
                }

                if($p->status == 14 || empty($p->weight) || $p->weight == 0){ //missing info
                    echo $p->hbn . ' missing info' . PHP_EOL;
                    $err = true;
                    break;
                }
                if($p->agent_id != $r->fwd_id && $p->agent->accode != $r->owner->accode){ //mismatch agent
                    echo $p->hbn . ' mismatch agent' . PHP_EOL;
                    $err = true;
                    break;
                }
                $rate = $p->getAgentRate();
                if(empty($rate[1])){ //no rate
                    echo $p->hbn . ' no rate' . PHP_EOL;
                    $err = true;
                    break;
                }
                $pc++;
            }
            if($err || $pc == 0){
                unset($ais[$r->fwd_id][$bd]);
                continue;
            }
            $ais[$r->fwd_id][$bd][] = $r;
        }
        $c = 0;
        foreach($ais as $aid => $bds){
            foreach($bds as $bd => $rs){
                Invoice::createExInv($aid, $bd, $rs);
                $c++;
            }
        }
        Yii::app()->xero->log( $c . ' Invoices created in HVLV ',Xero::LOG_LEVEL_INFO);

    }

    /**
     * create invoices for HVLV firstly
     */
    public function createHvlvInvoices(){
        $ais = [];
        $ndyet = [];
        // current only for Sydney warehouse only
        $rs = PickupList::model()->findAll(['condition' => 'bwf & 1 = 0 AND dpt_id = :wid AND created < CURDATE()', 'params' => [':wid' => 106 ], 'order' => 'created']);
        foreach($rs as $r){
            if(empty($r->lines) || in_array($r->fwd_id, $ndyet)) continue;
            $bd = $r->billdate();
            if(strtotime($bd) > time()){
                $ndyet[] = $r->fwd_id;
                continue;
            }
            $err = false;
            $pc = 0;
            foreach($r->lines as $l){
                $p = $l->mm();
                if ( !isset($p) ) continue;
                if ( in_array($p->status, [100])) continue;
                if($p->status == 12){
                    $err = true;
                    break;
                }

                if($p->status == 14 || empty($p->weight) || $p->weight == 0){ //missing info
                    $err = true;
                    break;
                }
                if($p->agent_id != $r->fwd_id && $p->agent->accode != $r->owner->accode){ //mismatch agent
                    $err = true;
                    break;
                }
                $rate = $p->getAgentRate();
                if(empty($rate[1])){ //no rate
                    $err = true;
                    break;
                }
                $pc++;
            }
            if($err || $pc == 0){
                unset($ais[$r->fwd_id][$bd]);
                continue;
            }
            $ais[$r->fwd_id][$bd][] = $r;
        }
        $c = 0;
        foreach($ais as $aid => $bds){
            foreach($bds as $bd => $rs){
                Invoice::createExInv($aid, $bd, $rs);
                $c++;
            }
        }
        Yii::app()->xero->log( $c . ' Invoices created in HVLV ',Xero::LOG_LEVEL_INFO);

    }

    /**
     * pull invoice status from Xero
     */
    private function pullData(){

        // get all latest payments from Xero
        $this->pullPayments();

        // get all latest overpayments from Xero
        $this->pullOverPayments();

        // get all latest credit notes from Xero
        $this->pullCreditNotes();

        // get invoice status from Xero
      //  Yii::app()->xero->log( 'pull invoice from Xero',Xero::LOG_LEVEL_INFO);
        $this->pullInvoices();

    }

    /**
     * pull invoice status from Xero
     *  we must call pullInvoices after pullPayment and pullCreditNotes
     * because adjustment for invoice is not available directly
     * after sync payments and credit notes
     * if invoice is fully paid , but checkPaid return false
     * we can get adjustment amount now , then create another payment as adjustment for the invoice
     * make the local invoice as real fully paid status
     */
    public function pullInvoices(){
        $newTimeFlag = gmdate("Y-m-d\TH:i:s");
        try {
            $xeInvoicesModel = XeInvoices::model();
            $xeInvoicesModel->setEndPoint('Invoices');
            $xeInvoices = $xeInvoicesModel->retrieve(NULL,$this->getLatestUpdateTime('invoice_update_utc'));
            //Yii::app()->xero->log('pull invoices response data :' . var_export($xeInvoices,true),Xero::LOG_LEVEL_INFO);
            //Yii::app()->xero->log('pull invoices response data :' . json_encode(var_export($xeInvoices,true)),Xero::LOG_LEVEL_INFO);

        } catch ( Exception $ecp ) {
            $xeroError = $ecp->getMessage();
            $msg = 'Failed to retrieve invoices error : '  . $xeroError;
            Yii::app()->xero->log($msg,Xero::LOG_LEVEL_ERR);
            return;
        }

        if ( !empty($xeInvoices) ) {

            // sync all invoices to local
            foreach ( $xeInvoices->invoices as $invoice ) {
                if ( $invoice->status === 'PAID' ) {
                    $localInvoice = Invoice::model()->find('no = :xno',[':xno' => $invoice->invoiceNumber]);
                    if ( !empty($localInvoice) ) {

                        // Xero has changed the payment amount when add adjustment
                        // so the following logic useless
                        //if ( $localInvoice->status == 7 ) {// partially paid
                            // which means there are some adjustment amount
                            // create a new payment as adjustment
                            //$this->addNewAdjustment($localInvoice);
                        //}

                        $oldStatus = $localInvoice->status;
                        $localInvoice->status = 9;
                        $localInvoice->update('status');
                        Log::add($localInvoice, Log::LOG_TYPE_UPDATE, ['notes' =>  'Xero API modify status from ' . $oldStatus . ' to ' . $localInvoice->status] );

                    } else {
                        $msg = 'Couldnot find local invoice :' . json_encode(var_export($invoice,true));
                        Yii::app()->xero->log($msg,Xero::LOG_LEVEL_ERR);
                    }
                }
            }

            // save this time sync time flag
            $this->updateLatestUpdateTime('invoice_update_utc',$newTimeFlag);
            Yii::app()->xero->log('update invoice updated time to :' . $newTimeFlag,Xero::LOG_LEVEL_INFO);
        } else {
            Yii::app()->xero->log('no any updated invoices this time',Xero::LOG_LEVEL_INFO);
        }

        return;

    }

    /**
     * create new payment as adjustment in order to make invoice as fully paid status
     * @param $invoice
     */
    private function addNewAdjustment(&$invoice){
        $paid = $invoice->paid();
        $adjustment = $invoice->total - $paid;
        if ( $adjustment > 0 ) {
            $newPayment = new Payment();
            $newPayment->setAttributes(
                array(
                    'org_id' => $invoice->to_id,
                    'date' => date('Y-m-d'),
                    'amount' => $adjustment,
                    'type' => 1, // as EFT
                    'ata' => 0,
                    'status' => 6, // posted
                    'bank' => 10, // bank default as Westpac AUD
                    'currency' => 1, // default as AUD
                    'ref' => 'adjustment',
                    'xero_id' => '0000'
                )
            );
            $newPayment->save();
            Log::add($newPayment, Log::LOG_TYPE_CREATE, array_merge(['notes' => 'created by Xero sync']) );

            // add new pay_inv
            $payInv = new PayInv();
            $payInv->setAttributes(
                array(
                    'pay_id' => $newPayment->id,
                    'inv_id' => $invoice->id,
                    'amount' => $adjustment,
                    'sync_xero' => 1 // avoid sync to Xero again
                )
            );
            $payInv->save();
        }
    }

    /**
     * pull payments status from Xero
     */
    public function pullPayments(){

        $newTimeFlag = gmdate("Y-m-d\TH:i:s");
        try {
            $xePaymentsModel = XePayments::model();
            $xePaymentsModel->setEndPoint('Payments');
            $xePayments = $xePaymentsModel->retrieve(NULL,$this->getLatestUpdateTime('payment_update_utc'));
           // $msg = 'pullPayments return data from Xero : '  .  json_encode(var_export($xePayments, true));
          //  Yii::app()->xero->log($msg,Xero::LOG_LEVEL_INFO);

        } catch ( Exception $ecp ) {
            $xeroError = $ecp->getMessage();
            $msg = '[Failed to retrieve payments error : '  . $xeroError;
            Yii::app()->xero->log($msg,Xero::LOG_LEVEL_ERR);
            return;
        }

        if ( !empty($xePayments) ) {
            // sync all payments to local
            // if payments existing in local , just ignore
            // otherwise create new Payment and Pay_Inv as well
            Yii::app()->xero->log( count($xePayments->payments) . ' payments will be updated into local ',Xero::LOG_LEVEL_INFO);
            foreach ( $xePayments->payments as $payment ) {
                $this->addNewPayment($payment);
            }

            // save this time sync time flag
            Yii::app()->xero->log( 'update local payments update time to :' . $newTimeFlag , Xero::LOG_LEVEL_INFO);
            $this->updateLatestUpdateTime('payment_update_utc',$newTimeFlag);
        } else {
            Yii::app()->xero->log('no more payments this time',Xero::LOG_LEVEL_INFO);
        }
    }

    /**
     * pull overpayments status from Xero
     */
    public function pullOverPayments(){

        $newTimeFlag = gmdate("Y-m-d\TH:i:s");
        try {
            $xeOverPaymentsModel = XeOverpayments::model();
            $xeOverPaymentsModel->setEndPoint('Overpayments');
            $xeOverPayments = $xeOverPaymentsModel->retrieve(NULL,$this->getLatestUpdateTime('overpayment_update_utc'));
            // $msg = 'pullPayments return data from Xero : '  .  json_encode(var_export($xePayments, true));
            //  Yii::app()->xero->log($msg,Xero::LOG_LEVEL_INFO);

        } catch ( Exception $ecp ) {
            $xeroError = $ecp->getMessage();
            $msg = '[Failed to retrieve overpayments error : '  . $xeroError;
            Yii::app()->xero->log($msg,Xero::LOG_LEVEL_ERR);
            return;
        }

        if ( !empty($xeOverPayments) ) {
            // sync all payments to local
            // if payments existing in local , just ignore
            // otherwise create new Payment and Pay_Inv as well
            Yii::app()->xero->log( count($xeOverPayments->overpayments) . ' overpayments will be updated into local ',Xero::LOG_LEVEL_INFO);
            foreach ( $xeOverPayments->overpayments as $payment ) {
                $this->addNewOverPayment($payment);
            }

            // save this time sync time flag
            Yii::app()->xero->log( 'update local overpayments update time to :' . $newTimeFlag , Xero::LOG_LEVEL_INFO);
            $this->updateLatestUpdateTime('overpayment_update_utc',$newTimeFlag);
        } else {
            Yii::app()->xero->log('no more payments this time',Xero::LOG_LEVEL_INFO);
        }
    }

    /**
     * pull latest credit notes from Xero
     */
    public function pullCreditNotes(){

        $newTimeFlag = gmdate("Y-m-d\TH:i:s");
        try {
            $xeCreditNotesModel = XeCreditNotes::model();
            $xeCreditNotesModel->setEndPoint('CreditNotes');
            $xeCreditNotes = $xeCreditNotesModel->retrieve(NULL,$this->getLatestUpdateTime('creditnotes_update_utc'));

            $msg = 'pullCreditNotes return data from Xero : '  . json_encode($xeCreditNotes);
            Yii::app()->xero->log($msg,Xero::LOG_LEVEL_INFO);

        } catch ( Exception $ecp ) {
            $xeroError = $ecp->getMessage();
            $msg = 'Failed to retrieve credit notes error : '  . $xeroError;
            Yii::app()->xero->log($msg,Xero::LOG_LEVEL_ERR);
            return;
        }

        if ( !empty($xeCreditNotes) ) {
            // sync all credit notes to local
            // if credit notes not existing in local , just ignore
            // otherwise link to local invoice
            foreach ( $xeCreditNotes->creditnotes as $creditnote ) {
                // try to find credit note to see if credit note existing local
                // if existing just update , otherwise create new one
                if ( isset($creditnote->creditNoteID)  &&
                    $creditnote->creditNoteID !== '00000000-0000-0000-0000-000000000000' ) {
                    $myCreditNote = Payment::model()->find('xero_id = :xid',[':xid' => $creditnote->creditNoteID]);
                    if ( empty($myCreditNote) ) {
                        // in case we need add the credit notes to HVLV
                        $myCreditNote = $this->addNewCreditNote($creditnote);
                        if ( empty($myCreditNote) ) {
                            continue; // create this credit failed , continue try next one
                        }
                    }
                    // loop through all allocations and put into our local payment system
                    foreach ($creditnote->allocations as $allocation) {

                        // in case returned by JSON format will be amount
                        // XML format will be appliedAmount
                        if (!empty($allocation->invoice) && ($allocation->appliedAmount > 0 || $allocation->amount > 0 ) ) {

                            $myInvoice = Invoice::model()->find('no = :ino' , [':ino' => $allocation->invoice->invoiceNumber] );
                            if ( empty($myInvoice) ) continue;

                            // push to local now
                            // add new pay_inv
                            $amount = $allocation->amount;
                            if ( $amount <= 0 ) $amount = $allocation->appliedAmount;
                            try {
                                $payInv = new PayInv();
                                $payInv->setAttributes(
                                    array(
                                        'pay_id' => $myCreditNote->id,
                                        'inv_id' => $myInvoice->id,
                                        'amount' => $amount,
                                        'sync_xero' => 1 // avoid sync to Xero again
                                    )
                                );
                                $payInv->save();

                                // try to update invoice status
                                $oldStatus = $myInvoice->status;
                                $myInvoice->checkPaid();
                                $myInvoice->save();
                                if ( $oldStatus != $myInvoice->status ){
                                    Log::add($myInvoice, Log::LOG_TYPE_UPDATE, ['notes' =>  'Xero API modify status from ' . $oldStatus . ' to ' . $myInvoice->status] );
                                }

                                // update local credit notes available amount
                                $myCreditNote->ata -= $amount;
                                if ($myCreditNote->ata < 0) $myCreditNote->ata = 0;
                                $myCreditNote->update(['ata']);

                            } catch ( Exception $mye ){
                                $msg = 'faild to update local credit notes - ' . $mye->getMessage();
                                Yii::app()->xero->log($msg,Xero::LOG_LEVEL_ERR);
                                Yii::app()->xero->log('credit note info : ' . json_encode($creditnote),Xero::LOG_LEVEL_INFO);
                                Yii::app()->xero->log('allocation info: ' . json_encode($allocation),Xero::LOG_LEVEL_INFO);
                            }
                        }
                    }
                }
            }

            // save this time sync time flag
            $this->updateLatestUpdateTime('creditnotes_update_utc',$newTimeFlag);
        }
    }

    /**
     * add new credit note which is from Xero into HVLV
     * @param $creditnote
     */
    private function addNewCreditNote(&$creditnote){

        // for AP credit notes we don't need to push into HVLV
        if ( $creditnote->type == XeCreditNote::TYPE_PAYABLE ) return;

        Yii::app()->xero->log('add new credit note to local',Xero::LOG_LEVEL_INFO);
        // when get updated creditnotes from Xero
        // Xero API does not return contact number
        // so we need try to get by call api get again
        $xeContact = XeContact::model();
        $contact = $xeContact->retrieve($creditnote->contact->contactID);
        if (empty($contact) ) {
            Yii::app()->xero->log('credit note no related contact',Xero::LOG_LEVEL_ERR);
            return null;
        }

        $agentId = $contact->contactNumber;
        // agent id in Xero format as : PORG-618
        if ( !empty($agentId) ) {
            if (strpos($agentId, 'PORG-') !== FALSE) {
                $agentId = substr($agentId, 5);
            }
        }
        // create a specified payment for the credit note
        $payment = new Payment();
        $payment->org_id = $agentId;
        $payment->amount = $creditnote->total;
        $payment->ata = $creditnote->total;
        $payment->ref = $creditnote->reference;

        preg_match('/(\d{10})(\d{3})([\+\-]\d{4})/', $creditnote->date, $matches);
        $payment->date = date( "Y-m-d", $matches[1] );

        $payment->type = 5; // default credit note
        $payment->bank = 90; // credit note
        $payment->currency = 1; // AUD
        $payment->status = 6; // should be posted automatically
        $payment->xero_id = $creditnote->creditNoteID;
        $payment->save();

        Log::add($payment, Log::LOG_TYPE_CREATE, array_merge(['notes' => 'created by Xero sync']) );

        return $payment;

    }

    /**
     * insert new Payment from Xero
     * @param $payment
     * @return bool
     */
    private function addNewPayment(&$payment){
        $success = false;

        // check to see if the same payment existing
        $existingPayment = Payment::model()->find('xero_id = :xid',[':xid' => $payment->paymentID]);

        Yii::app()->xero->log('try to add payment : ' . $payment->paymentID . ' to local',Xero::LOG_LEVEL_INFO);

        // in case existing just ignore
        if ( empty($existingPayment) ) {
            // create new one
            // get related invoice id and client id
            $agentId = $payment->invoice->contact->contactNumber;
            Yii::app()->xero->log('try to find local payment agent : ' . $agentId ,Xero::LOG_LEVEL_INFO);
            // agent id in Xero format as : PORG-618
            if ( !empty($agentId) ) {
                if ( strpos($agentId,'PORG-')  !== FALSE ) {
                    $agentId = substr($agentId, 5);
                }
                $invoiceNo = $payment->invoice->invoiceNumber;
                Yii::app()->xero->log('try to find local invoice  : ' . $invoiceNo ,Xero::LOG_LEVEL_INFO);
                $localInvoice = Invoice::model()->find('no = :ino', [':ino' => $invoiceNo]);
                if ( !empty($localInvoice) ) {
                    $newPayment = new Payment();

                    // get Xero payment date
                    // because returned json date format is : '/Date(1476316800000+0000)'
                    // time stamp (U) = 1365004652
                    // Microseconds (u) = 303
                    // Difference to Greenwich time (GMT) (O) = -0500
                    // refer to : http://stackoverflow.com/questions/16749778/php-date-format-date1365004652303-0500
                    // we need to convert to my date format
                    preg_match('/(\d{10})(\d{3})([\+\-]\d{4})/', $payment->date, $matches);
                    $paymentDate = date( "Y-m-d", $matches[1] );

                    // get payment type based on reference
                    // we have a agreement with Joyce
                    // if reference is 'cash' we think it is cash otherwise it should be EFT
                    $pType = 1;
                    if ( !empty($payment->reference) && strtolower(trim($payment->reference)) == 'cash' ) $pType = 3;

                    // in case all payments from Driver Cash account , we set payment type as Cash
                    if ( isset($payment->account->code) && $payment->account->code == '5101.10.31' ) $pType = 3;

                    $newPayment->setAttributes(
                        array(
                            'org_id' => $agentId,
                            'date' => $paymentDate,
                            'amount' => $payment->amount,
                            'type' => $pType,
                            'ata' => 0,
                            'status' => 6, // posted
                            'bank' => 10, // bank default as Westpac AUD
                            'currency' => 1, // default as AUD
                            'ref' => $payment->reference,
                            'xero_id' => $payment->paymentID
                        )
                    );
                    $newPayment->save();
                    Log::add($newPayment, Log::LOG_TYPE_CREATE, array_merge(['notes' => 'created by Xero sync']) );

                    // add new pay_inv
                    $payInv = new PayInv();
                    $payInv->setAttributes(
                        array(
                            'pay_id' => $newPayment->id,
                            'inv_id' => $localInvoice->id,
                            'amount' => $payment->amount,
                            'sync_xero' => 1 // avoid sync to Xero again
                        )
                    );
                    $payInv->save();
                    $success = true;

                    // try to update invoice status
                    $oldStatus = $localInvoice->status;
                    $localInvoice->checkPaid();
                    $localInvoice->save();
                    if ( $oldStatus != $localInvoice->status ){
                        Log::add($localInvoice, Log::LOG_TYPE_UPDATE, ['notes' =>  'Xero API modify status from ' . $oldStatus . ' to ' . $localInvoice->status] );
                    }

                    Yii::app()->xero->log('save local payment('.$newPayment->id.') and pay_inv ok ' ,Xero::LOG_LEVEL_INFO);
                } else {
                    Yii::app()->xero->log('local invoice not existing do nothing ',Xero::LOG_LEVEL_WARNING);
                }
            } else {
                Yii::app()->xero->log('local agent not existing do nothing ',Xero::LOG_LEVEL_WARNING);
            }

        } else {
            Yii::app()->xero->log('local payment existing do nothing ',Xero::LOG_LEVEL_WARNING);
        }
        return $success;
    }

    /**
     * insert new Over Payment from Xero
     * @param $payment
     * @return bool
     */
    private function addNewOverPayment(&$payment){

        // we only sync AR overpayment to HVLV from Xero
        if ( $payment->type != XeOverpayment::TYPE_RECEIVE_OVERPAYMENT ) return;
        if ( $payment->status == XeOverpayment::ST_VOIDED ) return; // ignore voided overpayment

        $success = false;

        // check to see if the same payment existing
        $existingPayment = Payment::model()->find('xero_id = :xid',[':xid' => $payment->overpaymentID]);

        Yii::app()->xero->log('try to add payment : ' . $payment->overpaymentID . ' to local',Xero::LOG_LEVEL_INFO);

        // in case existing just ignore
        if ( empty($existingPayment) ) {
            // create a new payment now
            $agentId = $payment->contact->contactNumber;
            Yii::app()->xero->log('try to find local overpayment agent : ' . $agentId ,Xero::LOG_LEVEL_INFO);
            // agent id in Xero format as : PORG-618
            if ( !empty($agentId) ) {
                if ( strpos($agentId,'PORG-')  !== FALSE ) {
                    $agentId = substr($agentId, 5);
                }

                $existingPayment = new Payment();
                // get Xero payment date
                // because returned json date format is : '/Date(1476316800000+0000)'
                // time stamp (U) = 1365004652
                // Microseconds (u) = 303
                // Difference to Greenwich time (GMT) (O) = -0500
                // refer to : http://stackoverflow.com/questions/16749778/php-date-format-date1365004652303-0500
                // we need to convert to my date format
                preg_match('/(\d{10})(\d{3})([\+\-]\d{4})/', $payment->date, $matches);
                $paymentDate = date( "Y-m-d", $matches[1] );
                $existingPayment->setAttributes(
                    array(
                        'org_id' => $agentId,
                        'date' => $paymentDate,
                        'amount' => $payment->total,
                        'type' => 1, // default as EFT
                        'ata' => $payment->remainingCredit,
                        'status' => 6, // posted
                        'bank' => 10, // bank default as Westpac AUD
                        'currency' => 1, // default as AUD
                        'ref' => '',
                        'xero_id' => $payment->overpaymentID
                    )
                );
                $existingPayment->save();
                Log::add($existingPayment, Log::LOG_TYPE_CREATE, array_merge(['notes' => 'created by Xero sync']) );
                $success = true;

            } else {
                Yii::app()->xero->log('local agent not existing do nothing ',Xero::LOG_LEVEL_WARNING);
            }
        } else {
            // update total amount and remaining credit
            $existingPayment->amount = $payment->total;
            $existingPayment->ata = $payment->remainingCredit;
            $existingPayment->save();
        }

        // try to allocate some invoice if existing
        foreach ( $payment->allocations as $allocation ) {
            // get invoice number
            $invoiceNo = $allocation->invoice->invoiceNumber;
            Yii::app()->xero->log('try to find local invoice  : ' . $invoiceNo ,Xero::LOG_LEVEL_INFO);
            $localInvoice = Invoice::model()->find('no = :ino', [':ino' => $invoiceNo]);
            if ( !empty($localInvoice) ) {

                // check if the payment line existing or not
                $payInv = PayInv::model()->find('pay_id = :pid AND inv_id = :iid',[':pid' => $existingPayment->id,':iid' => $localInvoice->id]);
                if ( empty($payInv) ) {
                    // add new pay_inv
                    $payInv = new PayInv();
                    $payInv->setAttributes(
                        array(
                            'pay_id' => $existingPayment->id,
                            'inv_id' => $localInvoice->id,
                            'amount' => $allocation->amount,
                            'sync_xero' => 1 // avoid sync to Xero again
                        )
                    );
                } else {
                    $payInv->amount = $allocation->amount;
                }
                $payInv->save();

                // try to update invoice status
                $oldStatus = $localInvoice->status;
                $localInvoice->checkPaid();
                $localInvoice->save();
                if ( $oldStatus != $localInvoice->status ){
                    Log::add($localInvoice, Log::LOG_TYPE_UPDATE, ['notes' =>  'Xero API modify status from ' . $oldStatus . ' to ' . $localInvoice->status] );
                }
                Yii::app()->xero->log('save local payment('.$existingPayment->id.') and pay_inv ok ' ,Xero::LOG_LEVEL_INFO);
                $success = true;
            } else {
                Yii::app()->xero->log('local invoice not existing do nothing ',Xero::LOG_LEVEL_WARNING);
            }
        }

        return $success;
    }

    /**
     * @return null|string
     */
    private function getLatestUpdateTime($node){
        $updateTime = NULL;
        $setting = XeroSettings::model()->find('name = :cname',[':cname' => $node]);
        if ( !empty($setting) ) {
            $updateTime = $setting->value;
        }
        return $updateTime;
    }

    private function updateLatestUpdateTime($node,$updateTime){
        $setting = XeroSettings::model()->find('name = :cname',[':cname' => $node]);
        if ( empty($setting) ) {
            $setting = new XeroSettings();
            $setting->name = $node;
        }
        $setting->value = $updateTime;
        $setting->save();
    }

    /**
     * push invoice to Xero
     */
    private function pushData(){

        // push invoices to Xero
        $this->pushInvoices();
        //$this->pushSpecInvoices();

        // push all credit notes as well
        $this->pushCreditNotes();

        // currently only push invoice to xero
        // all payments will be pulled down from Xero
        // so comment the following lines
        //--------------------------------------------------------------
        // push payments as well
       // $this->pushPayments();
        //----------------------------------------------------------------
    }

    /**
     * temp used push all invoice after 2016.11.01
     */
    private function pushSpecInvoices(){

        // bulk push invoice to Xero with maximum 40 invoices each time
        $MAX_LOOP_DATA = 40;

        // get all invoices ( not paid and not sync yet ) which are not sync with Xero
        // don't push credit note ( type = 90)
        // we only sync Sydney invoice to Xero currently

        // currently only push after 2016-11-01
        $invoices = Invoice::model()->findAll('status != 10 AND type != 90 AND dpt_id = :dptid AND date >= :idate',[':dptid' => Org::PCAE_DEPARTMENT_SYDNEY,':idate' => '2016-11-01']);
        $this->echo_debug('=== Found '.sizeof($invoices)." invoices to sync with Xero ===");
        Yii::app()->xero->log( 'Found '.sizeof($invoices).' invoices to sync with Xero',Xero::LOG_LEVEL_INFO );

        if ( empty($invoices) ) return;

        // make up bulk push invoice xml data
        $invoicesData = '';
        $index = 0;
        foreach ( $invoices as $invoice ) {
            $xmlInvoice = Invoice::getXeroXmlData($invoice);
            if ( !empty($xmlInvoice) ) {
                $invoicesData .= $xmlInvoice;
            } else {
                Yii::app()->xero->log( 'invoice[' . $invoice->no . '] no any items',Xero::LOG_LEVEL_ERR );
            }

            $index++;
            if ( $index >= $MAX_LOOP_DATA ) {
                $this->sendBulkInvoices($invoicesData);
                Yii::app()->xero->log( $index . ' invoices have been pushed to Xero',Xero::LOG_LEVEL_INFO );

                sleep(2); // avoid rate limit over for Xero
                // for next loop again
                $invoicesData = '';
                $index = 0;
            }
        }

        // send last loop data if existing
        if ( !empty($invoicesData) ) {
            $this->sendBulkInvoices($invoicesData);
            Yii::app()->xero->log( $index . ' invoices have been pushed to Xero',Xero::LOG_LEVEL_INFO );
        }


    }

    /**
     * @param $invs
     */
    public function pushInvoicesByIDS($invs){

        // bulk push invoice to Xero with maximum 40 invoices each time
        $MAX_LOOP_DATA = 40;
        $invoiceIds = implode(',',$invs);
        $invoices = Invoice::model()->findAll('id in (' . $invoiceIds  . ')');
        $this->echo_debug('=== Found '.sizeof($invoices)." invoices to sync with Xero ===");
        Yii::app()->xero->log( 'Found '.sizeof($invoices).' invoices to sync with Xero',Xero::LOG_LEVEL_INFO );

        if ( empty($invoices) ) return;

        // make up bulk push invoice xml data
        $invoicesData = '';
        $index = 0;
        foreach ( $invoices as $invoice ) {
            $xmlInvoice = Invoice::getXeroXmlData($invoice);
            if ( !empty($xmlInvoice) ) {
                $invoicesData .= $xmlInvoice;
            } else {
                Yii::app()->xero->log( 'invoice[' . $invoice->no . '] no any items',Xero::LOG_LEVEL_ERR );
            }

            $index++;
            if ( $index >= $MAX_LOOP_DATA ) {
                $this->sendBulkInvoices($invoicesData);
                Yii::app()->xero->log( $index . ' invoices have been pushed to Xero',Xero::LOG_LEVEL_INFO );

                sleep(2); // avoid rate limit over for Xero
                // for next loop again
                $invoicesData = '';
                $index = 0;
            }
        }

        // send last loop data if existing
        if ( !empty($invoicesData) ) {
            $this->sendBulkInvoices($invoicesData);
            Yii::app()->xero->log( $index . ' invoices have been pushed to Xero',Xero::LOG_LEVEL_INFO );
        }
    }

    /**
     *
     */
    private function pushInvoices(){

        // bulk push invoice to Xero with maximum 40 invoices each time
        $MAX_LOOP_DATA = 40;

        // get all invoices ( not paid and not sync yet ) which are not sync with Xero
        // don't push credit note ( type = 90)
        // we only sync Sydney invoice to Xero currently

        // currently only push after 2016-11-01
        // and Sydney(106) and Melbourne(218)
        $invoices = Invoice::model()->findAll('status IN (1,2,3) AND total > 0 AND sync_xero = 0 AND type != 90 AND dpt_id IN (106,218) AND date >= :idate',[':idate' => '2017-01-01']);
        $this->echo_debug('=== Found '.sizeof($invoices)." invoices to sync with Xero ===");
        Yii::app()->xero->log( 'Found '.sizeof($invoices).' invoices to sync with Xero',Xero::LOG_LEVEL_INFO );

        if ( empty($invoices) ) return;

        // make up bulk push invoice xml data
        $invoicesData = '';
        $index = 0;
        foreach ( $invoices as $invoice ) {
            $xmlInvoice = Invoice::getXeroXmlData($invoice);
            if ( !empty($xmlInvoice) ) {
                $invoicesData .= $xmlInvoice;
            } else {
                Yii::app()->xero->log( 'invoice[' . $invoice->no . '] no any items',Xero::LOG_LEVEL_ERR );
            }

            $index++;
            if ( $index >= $MAX_LOOP_DATA ) {
                $this->sendBulkInvoices($invoicesData);
                Yii::app()->xero->log( $index . ' invoices have been pushed to Xero',Xero::LOG_LEVEL_INFO );

                sleep(2); // avoid rate limit over for Xero
                // for next loop again
                $invoicesData = '';
                $index = 0;
            }
        }

        // send last loop data if existing
        if ( !empty($invoicesData) ) {
            $this->sendBulkInvoices($invoicesData);
            Yii::app()->xero->log( $index . ' invoices have been pushed to Xero',Xero::LOG_LEVEL_INFO );
        }
    }

    private function pushPayments(){
        // bulk push payments to Xero with maximum 40 payments each time
        $MAX_LOOP_DATA = 40;

        // get all payments
        $payments = PayInv::model()->findAll('sync_xero = 0');
        $this->echo_debug('=== Found '.sizeof($payments)." payments to sync with Xero ===");
        Yii::app()->xero->log( 'Found '.sizeof($payments).' payments to sync with Xero',Xero::LOG_LEVEL_INFO );

        if ( empty($payments) ) return;

        // make up bulk push payments xml data
        $paymentsData = '';
        $index = 0;
        foreach ( $payments as $payment ) {
            $xmlPayment = PayInv::getXeroXmlData($payment);
            if ( !empty($xmlPayment) ) {
                $paymentsData .= $xmlPayment;
            } else {
                Yii::app()->xero->log( 'payinv[payid:' . $payment->pay_id . ', invid:'.$payment->inv_id.'] no any items' ,Xero::LOG_LEVEL_ERR);
            }

            $index++;
            if ( $index >= $MAX_LOOP_DATA ) {
                $this->sendBulkPayments($paymentsData);
                sleep(2); // avoid rate limit over for Xero

                $this->echo_debug('=== ' .$index .' payments synced with Xero ===');
                Yii::app()->xero->log( $index .' payments synced with Xero',Xero::LOG_LEVEL_INFO );

                // for next loop again
                $paymentsData = '';
                $index = 0;

            }
        }

        // send last loop data if existing
        if ( !empty($paymentsData) ) {
            $this->sendBulkPayments($paymentsData);
            $this->echo_debug('=== ' .$index .' payments synced with Xero ===');
            Yii::app()->xero->log( $index .' payments synced with Xero',Xero::LOG_LEVEL_INFO );
        }

        $this->echo_debug('=== payments sync with Xero DONE ===');
        Yii::app()->xero->log('payments sync with Xero DONE',Xero::LOG_LEVEL_INFO );
    }

    /**
     * push all hvlv current credit notes to Xero
     * we only push credit notes with available amount is not zero
     */
    public function pushCreditNotes(){
        // bulk push credit note payments to Xero with maximum 40 payments each time
        $MAX_LOOP_DATA = 40;

        // get all credit notes with available amount is more than zero
        $creditNotes = Payment::model()->findAll(' (xero_id = "" or xero_id is NULL ) and ata > 0 and type = 5');
        $this->echo_debug('=== Found '.sizeof($creditNotes)." credit notes to sync with Xero ===");
        Yii::app()->xero->log('Found '.sizeof($creditNotes).' credit notes to sync with Xero',Xero::LOG_LEVEL_INFO );

        if ( empty($creditNotes) ) return;

        // make up bulk push credit notes xml data
        $creditNotesData = '';
        $index = 0;
        foreach ( $creditNotes as $creditNote ) {
            $xmlData = Payment::getXeroXmlData($creditNote);
            if ( !empty($xmlData) ) {
                $creditNotesData .= $xmlData;
            } else {
                Yii::app()->xero->log( 'credit notes[' . $creditNote->id . '] no any items' ,Xero::LOG_LEVEL_ERR);
            }

            $index++;
            if ( $index >= $MAX_LOOP_DATA ) {
                $this->sendBulkCreditNotes($creditNotesData);
                sleep(2); // avoid rate limit over for Xero
                // for next loop again
                $creditNotesData = '';
                $index = 0;
                $this->echo_debug('=== ' .$index .' creditnotes synced with Xero ===');
                Yii::app()->xero->log($index .' creditnotes synced with Xero',Xero::LOG_LEVEL_INFO );
            }
        }

        // send last loop data if existing
        if ( !empty($creditNotesData) ) {
            $this->sendBulkCreditNotes($creditNotesData);
            $this->echo_debug('=== ' .$index .' creditnotes synced with Xero ===');
            Yii::app()->xero->log($index .' creditnotes synced with Xero',Xero::LOG_LEVEL_INFO );
        }

        $this->echo_debug('=== creditnotes sync with Xero DONE ===');
        Yii::app()->xero->log('creditnotes sync with Xero DONE',Xero::LOG_LEVEL_INFO );

    }

    /**
     * push last months' all accrual cost to xero by creating manual journals
     */
    public function pushLastMonthJournals(){

        $pMonthFile = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'xero'  . DIRECTORY_SEPARATOR . 'xero_accrual_sync.pid';
        if ( is_file($pMonthFile)  ) {
            $doneMonth = file_get_contents($pMonthFile);
            if ( $doneMonth == date('Y-m') ) return;
        }

        // get all last month accrual billings
        // group by charge code
        // get last day of last month
        $lastMonthLastDate= date('Y-m-d', strtotime("last day of -1 month"));
        $lastMonthFirstDate = date('Y-m-d', strtotime("first day of -1 month"));
        $billings = BillingLine::model()->findAll('created >= :fdate AND created <= :tdate AND actual_amount = 0',[':fdate' => $lastMonthFirstDate,':tdate' => $lastMonthLastDate]);
        if ( empty($billings) ) {
            Yii::app()->xero->log('this time no any accrual billings'  ,Xero::LOG_LEVEL_INFO);
            return;
        }

        $billingsData = array(); // group by chargecode now
        $xls = new oExcel;
        $i = 1;
        $xls->addRow($i++, ['No','Description','Charge Dode','Accrual Amount' ,'Type','Created']);
       // $xls->setFont('A1:F1', array('bold' => true));
      //  $xls->centerAlignment('A1:F1');

        // save all to local file as well , will be posted to Xero as journal attachement
        $totCredits = 0;
        foreach ( $billings as $billing ) {
            $type = 'Unknown';
            if ( empty($billing->charge_code) ) continue;
            if ( isset(Invoice::$dpmts[$billing->dpmt]) ) $type = Invoice::$dpmts[$billing->dpmt];
            $xls->addRow($i, [$billing->no,$billing->desc,$billing->charge_code,$billing->accrual_amount,$type,$billing->created]);
            $i++;
            if ( !isset($billingsData[$billing->charge_code]) ) {
                $billingsData[$billing->charge_code] = 0;
            }
            $billingsData[$billing->charge_code] += $billing->accrual_amount;
            $totCredits += $billing->accrual_amount;
        }

        // save details to local
        $fileName = 'accrual_'.$lastMonthLastDate.'.xlsx';
        $detailsFile = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'xero' .DIRECTORY_SEPARATOR .$fileName;
        $xls->output($detailsFile,'Excel2007',false);
        unset($xls);

        if ( $totCredits > 0 ) {
            $journal = new XeManualJournal();
            $journal->date = $lastMonthLastDate;
            $narration = 'Month end accrual ' . $lastMonthLastDate;
            $journal->narration = $narration;
            $journal->status = XeManualJournal::MANUAL_JOURNAL_STATUS_POSTED;
            $journal->lineAmountTypes = 'NoTax';

            foreach ( $billingsData as $code =>  $amount ) {
                if ( empty($code) ) continue;
                $line = new XeJournalLine();
                $line->accountCode = $code;
                $line->description = $narration;
                $line->taxType = 'BASEXCLUDED';
                $line->lineAmount = $amount;
                $journal->journalLines->add($line);
            }

            // add total credits
            $line = new XeJournalLine();
         //   $line->accountCode = '8099.00.00'; // no new gl code ,so comment now
            $line->description = $narration;
            $line->taxType = 'BASEXCLUDED';
            $line->lineAmount = '-' . $totCredits;
            $journal->journalLines->add($line);

            $rt = $journal->save();
            if ( $rt && $journal->manualJournalID != '00000000-0000-0000-0000-000000000000') {
                $data = file_get_contents($detailsFile);
                $this->journalAttachment($journal->manualJournalID, $data, $fileName);
                unlink($detailsFile);

                // save flag for this month close accrual has been done
                file_put_contents($pMonthFile,date('Y-m'));

                // update all billing accrual closed flag
                // when push actual amount to xero , we will check this flag to confirm
                // if we should change the date to current date
                foreach ( $billings as $billing ) {
                    $billing->mdata['accrual_closed'] = 1;
                    $billing->updateMeta();
                }


                // create a reverse manual journal now at the end of current month
                $journal = new XeManualJournal();
                $journal->date = date('Y-m-t');;
                $narration = 'Reverse: Month end accrual ' . $lastMonthLastDate;
                $journal->narration = $narration;
                $journal->status = XeManualJournal::MANUAL_JOURNAL_STATUS_POSTED;
                $journal->lineAmountTypes = 'NoTax';

                foreach ( $billingsData as $code =>  $amount ) {
                    $line = new XeJournalLine();
                    $line->accountCode = $code;
                    $line->description = $narration;
                    $line->taxType = 'BASEXCLUDED';
                    $line->lineAmount = '-' . $amount;
                    $journal->journalLines->add($line);
                }

                // add total credits
                $line = new XeJournalLine();
            //    $line->accountCode = '8099.00.00';
                $line->description = $narration;
                $line->taxType = 'BASEXCLUDED';
                $line->lineAmount =  $totCredits;
                $journal->journalLines->add($line);

                $rt = $journal->save();
                if ( !$rt ||  $journal->manualJournalID == '00000000-0000-0000-0000-000000000000') {
                    Yii::app()->xero->log('this time push reverse journals ['. $narration .'] failed, please refer to xero log for more details'  ,Xero::LOG_LEVEL_ERR);
                } else {
                    Yii::app()->xero->log('this time push journals finished successfully'  ,Xero::LOG_LEVEL_ERR);
                }
            } else {
                Yii::app()->xero->log('this time push journals failed, please refer to xero log for more details'  ,Xero::LOG_LEVEL_ERR);
            }
        } else {
            Yii::app()->xero->log('this time all accrual billings amount is zero'  ,Xero::LOG_LEVEL_ERR);
        }

    }

    private function journalAttachment($xeroInvoiceId,$fileData,$fileName){
        Yii::app()->xero->apiJournalAttachment($xeroInvoiceId,$fileData,$fileName);
    }


    private function invoiceAttachment($xeroInvoiceId,&$invoice){
        $pdf = oPDF::renderPDF('invoice', array('inv'=>$invoice),0);
        Yii::app()->xero->apiInvoiceAttachment($xeroInvoiceId,$pdf,$invoice->no.'.pdf');
    }

    private function invoiceAttachmentById($xeroInvoiceId,$invoiceId){
        $model = Invoice::model()->findByPk($invoiceId);
        $pdf = oPDF::renderPDF('invoice', array('inv'=>$model),0);
        Yii::app()->xero->apiInvoiceAttachment($xeroInvoiceId,$pdf,$model->no.'.pdf');
    }

    private function sendBulkCreditNotes(&$creditNotesData){
        if ( empty($creditNotesData) ) return;
        $bulkXmlData = '<?xml version="1.0"?><CreditNotes>';
        $bulkXmlData .= $creditNotesData;
        $bulkXmlData .= '</CreditNotes>';
        $xecreditnotes = new XeCreditNotes();
        try {
            $xecreditnotes->setEndPoint('CreditNotes');
            $rt = $xecreditnotes->bulkSave($bulkXmlData);

            if ($rt) {
               // Yii::app()->xero->log('Response Data : ' . json_encode(var_export($xecreditnotes,true)),Xero::LOG_LEVEL_INFO);
                // loop for all synced invoices
                foreach ($xecreditnotes->creditnotes as $creditnote) {

                    if ( empty($creditnote->creditNoteNumber) ) continue;

                    // credit note number format as : PCN-****
                    $payment_ids = explode('-', $creditnote->creditNoteNumber);
                    $myPayment = NULL;
                    if ( count($payment_ids) == 2 ) {
                        $payment_id = $payment_ids[1];
                        $myPayment = Payment::model()->findByPk($payment_id);
                    }
                    if ( empty($myPayment) ) {
                        Yii::app()->xero->log('could not find related creditnote: ' . $payment_id  ,Xero::LOG_LEVEL_ERR);
                        continue;
                    }

                    // in case validation error
                    $xeroId = 'ERROR';
                    if ( $creditnote->hasValidationErrors || $creditnote->statusAttributeString == 'ERROR' ) {
                        // try to avoid sync with xero again for next time
                        // but we should save as another flag for example xero_id flag as 'ERROR' means faild to sync with error
                    } else {
                        // in case push to Xero system ok, payment ID will be valid ID
                        if (isset($creditnote->creditNoteID) &&
                            $creditnote->creditNoteID !== '00000000-0000-0000-0000-000000000000'
                        ) {
                            $xeroId = $creditnote->creditNoteID;
                        } else {
                            Yii::app()->xero->log('Failed to push one creditnote', Xero::LOG_LEVEL_ERR);
                            Yii::app()->xero->log('creditnote response:' . json_encode(var_export($creditnote, true)), Xero::LOG_LEVEL_ERR);
                        }
                    }
                    $myPayment->xero_id = $xeroId;
                    $myPayment->update('xero_id');
                }
            } else {
                Yii::app()->xero->log('Failed to sync all credit notes this time',Xero::LOG_LEVEL_ERR);
                Yii::app()->xero->log('Request Data : ' . $bulkXmlData,Xero::LOG_LEVEL_INFO);
            }
        } catch (Exception $ecp) {
            $msg = 'Failed to sync all credit notes - ' . $ecp->getMessage();
            Yii::app()->xero->log($msg,Xero::LOG_LEVEL_ERR);
            Yii::app()->xero->log('Request Data : ' . $bulkXmlData,Xero::LOG_LEVEL_INFO);
        }
    }

    /**
     * @param $paymentsData
     */
    public function sendBulkPayments(&$paymentsData){
        if ( empty($paymentsData) ) return;
        $bulkXmlData = '<?xml version="1.0"?><Payments>';
        $bulkXmlData .= $paymentsData;
        $bulkXmlData .= '</Payments>';
        $xepayments = new XePayments();
        try {
            $xepayments->setEndPoint('Payments');
            $rt = $xepayments->bulkSave($bulkXmlData);

            if ($rt) {
                Yii::app()->xero->log('Response Data : ' . json_encode(var_export($xepayments,true)),Xero::LOG_LEVEL_INFO);
                // loop for all synced invoices
                foreach ($xepayments->payments as $payment) {

                    if ( empty($payment->reference) ) continue;
                    $payinv_ids = explode('-', $payment->reference);
                    $pay_id = $payinv_ids[0];
                    $inv_id = $payinv_ids[1];
                    $myPayInv = PayInv::model()->find('pay_id = :pid and inv_id = :iid', [':pid' => $pay_id,':iid' => $inv_id]);
                    if ( empty($myPayInv) )  {
                        Yii::app()->xero->log('could not find related PayInv payid: ' . $pay_id . ' invid:' . $inv_id, Xero::LOG_LEVEL_ERR);
                        continue;
                    }

                    // in case validation error
                    if ( $payment->hasValidationErrors || $payment->statusAttributeString == 'ERROR' ) {
                        // try to avoid sync with xero again for next time
                        // but we should save as another flag for example sync_xero flag as 2 means faild to sync with error
                        $myPayInv->sync_xero = 2;
                        $myPayInv->update('sync_xero');
                    } else {

                        // in case push to Xero system ok, payment ID will be valid ID
                        if (isset($payment->paymentID) &&
                            $payment->paymentID !== '00000000-0000-0000-0000-000000000000'
                        ) {
                            $myPayInv->sync_xero = 1;
                            $myPayInv->update('sync_xero');
                        } else {
                            Yii::app()->xero->log('Failed to push one payment', Xero::LOG_LEVEL_ERR);
                            Yii::app()->xero->log('payment response:' . json_encode(var_export($payment, true)), Xero::LOG_LEVEL_ERR);
                        }
                    }
                }
            } else {
                Yii::app()->xero->log('Failed to sync all payments this time',Xero::LOG_LEVEL_ERR);
                Yii::app()->xero->log('Request Data : ' . $bulkXmlData,Xero::LOG_LEVEL_INFO);
            }
        } catch (Exception $ecp) {
            $msg = 'Failed to sync all payments - ' . $ecp->getMessage();
            Yii::app()->xero->log($msg,Xero::LOG_LEVEL_ERR);
            Yii::app()->xero->log('Request Data : ' . $bulkXmlData,Xero::LOG_LEVEL_INFO);
        }
    }


    /**
     * @param $invoicesData
     */
    private function sendBulkInvoices(&$invoicesData){
        if ( empty($invoicesData) ) return;
        $bulkXmlData = '<?xml version="1.0"?><Invoices>';
        $bulkXmlData .= $invoicesData;
        $bulkXmlData .= '</Invoices>';
        $xeinvoices = new XeInvoices();
        try {
            $xeinvoices->setEndPoint('Invoices');
            $rt = $xeinvoices->bulkSave($bulkXmlData);

            if ($rt) {
                Yii::app()->xero->log('Response Data : ' . json_encode(var_export($xeinvoices,true)),Xero::LOG_LEVEL_INFO);

                // loop for all synced invoices
                foreach ($xeinvoices->invoices as $invoice) {

                    $invoiceNo = $invoice->invoiceNumber;
                    $myInvoice = Invoice::model()->find('no = :no', [':no' => $invoiceNo]);
                    if ( empty($myInvoice) ) {
                        Yii::app()->xero->log('could not find related invoice : ' . $invoiceNo,Xero::LOG_LEVEL_ERR);
                        continue;
                    }

                    if ( $invoice->statusAttributeString == 'ERROR' ) {
                        // try to avoid sync with xero again for next time
                        // but we should save as another flag for example sync_xero flag as 2 means failed to sync with error
                        $myInvoice->sync_xero = 2;
                        $myInvoice->update('sync_xero');
                    } else {
                        // in case push to Xero system ok, invoice ID will be valid ID
                        if (isset($invoice->invoiceID) &&
                            $invoice->invoiceID !== '00000000-0000-0000-0000-000000000000'
                        ) {

                            $myInvoice->sync_xero = 1;
                            $myInvoice->update('sync_xero');
                            // attach real invoice pdf file
                            $this->invoiceAttachment($invoice->invoiceID, $myInvoice);

                        } else {
                            Yii::app()->xero->log('Failed to push one invoice', Xero::LOG_LEVEL_ERR);
                            Yii::app()->xero->log('Invoice response:' . json_encode(var_export($invoice, true)), Xero::LOG_LEVEL_ERR);
                        }
                    }
                }
            } else {
                Yii::app()->xero->log('Failed to sync all invoices this time',Xero::LOG_LEVEL_ERR);
                Yii::app()->xero->log('Request Data : ' . $bulkXmlData,Xero::LOG_LEVEL_INFO);
            }
        } catch (Exception $ecp) {
            $msg = 'Failed to sync all invoices - ' . $ecp->getMessage();
            Yii::app()->xero->log($msg,Xero::LOG_LEVEL_ERR);
            Yii::app()->xero->log('Request Data : ' . $bulkXmlData,Xero::LOG_LEVEL_INFO);
        }
    }

    /**
     * @param $l
     */
    private function log($msg){
        $tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
        file_put_contents($tmp.'xero_sync.log', date('Y-m-d H:i:s') . ' ' . $msg ."\n", FILE_APPEND);
    }

    /**
     * echo debug information
     * @param $str
     */
    private function echo_debug($str){
        if ( $this->debug )  echo $str . "\n";
    }

}
