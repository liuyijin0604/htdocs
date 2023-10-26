<?php

/**
 * for Michael Yue to execute some short command only
 * Class YXCommand
 */
class YXCommand extends CConsoleCommand {
	private $db;
	private $args;
	private $tmp;

	public function run($args) {
		$this->db = Yii::app()->getDb();
		$this->args = $args;
		$this->tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
		if(!empty($args[0]) && method_exists($this, $args[0])){
			$this->{$args[0]}();
		}
	}


    public function getTotalSyncXeroCountInfo(){

        $criteria = new CDbCriteria();
       // $criteria->addCondition('history_id = ' . $this->id);
        //$criteria->addInCondition('matched_result',[AFInvoiceReconciliation::MATCHED_RESULT_SUCCESS,AFInvoiceReconciliation::MATCHED_REASULT_POST_BY_GENERAL]);
        $criteria->addInCondition('matched_result',[AFInvoiceReconciliation::MATCHED_RESULT_SUCCESS]);
        $sBillings = AFInvoiceReconciliation::model()->findAll($criteria);
        echo 'all susccess ' . count($sBillings) . PHP_EOL;

        $processCount = 0;
        $emptyModelCount = 0;
        $modelNotFound = 0;
        $successCount = 0;
        $failedCount = 0;
        $otCount = 0;

        foreach ( $sBillings as $billing ) {
            $processCount++;
            if ( empty($billing->model) ) {$emptyModelCount++;continue;}

            // loop each imported invoice
            // step 1
            // try to get all billing lines based on imported invoiced matched EdiJob or ExcoConsol
            $r = new $billing->model;
            $pModel = $r::model()->findByPk($billing->fid);
            if (!empty($pModel)) {
                // only for not sync with xero billing lines
                // currently only considerate the supplier (954) - Priority Cargo
                $pBillingLines = BillingLine::model()->findAll('billing_ref = :bref AND sync_xero = 1 AND org_id = 954', [':bref' => $pModel->no]);
                if ( !empty($pBillingLines) ) {
                    $successCount++;
                } else {
                    // currently only considerate the supplier (954) - Priority Cargo
                    $pBillingLines = BillingLine::model()->findAll('billing_ref = :bref AND sync_xero > 1  AND org_id = 954', [':bref' => $pModel->no]);
                    if ( !empty($pBillingLines) ) {
                        $failedCount++;
                    } else {
                        echo 'job/console no: ' . $pModel->no . ' not found' . PHP_EOL;
                        $otCount++;
                    }
                }
            } else {
                $modelNotFound++;
            }
        }
        echo 'process count : ' . $processCount . PHP_EOL;
        echo 'empty model count : ' . $emptyModelCount . PHP_EOL;
        echo 'model not found count : ' . $modelNotFound . PHP_EOL;
        echo 'success count : ' . $successCount . PHP_EOL;
        echo 'failed count : ' . $failedCount . PHP_EOL;
        echo 'others count : ' . $otCount . PHP_EOL;

    }


    public function fixSyncXeroIssue(){
        $afbillings = AFInvoiceReconciliation::model()->findAll('matched_result = 1');
        foreach ( $afbillings as $afbilling ) {
            //foreach ( $afbilling->lines as $line )
            {
                $r = new $afbilling->model;
                $pModel = $r::model()->findByPk($afbilling->fid);
                if ( !empty($pModel) ) {
                    $costLines = BillingLine::model()->findAll('billing_ref = :bref',[':bref' => $pModel->no]);
                    if ( !empty($costLines) ) {
                        foreach ( $costLines as $cost ) {
                            if ( $cost->sync_xero != 1 ) {
                                if (in_array($cost->item_code, ['GL1', 'GL11', 'GL5'])) {
                                    $cost->sync_xero = 0;
                                    $cost->update('sync_xero');
                                }
                            }
                        }
                    }
                }
            }
        }
        echo 'All Done' . PHP_EOL;
    }

    public function gotIInvoiceStatusIssue(){
        $p = Payment::model()->findByPk(21505);
        $inv = Invoice::model()->findByPk(28900);

        $pi = new PayInv();
        $pi->inv_id = 28900;
        $pi->pay_id = 21505;

        // we need to check the maximum allowed payment amount
        // should not be more than invoice balance
        $t = 132;
        $balance = $inv->getBalance();
        echo 'balance is :' . $balance . PHP_EOL;
        if ( $t > $balance ) {
            $t = $balance;
            echo 'big one' . PHP_EOL;
        }

        $pi->amount = $t;
        $pi->save();
        echo 'inv status1 : ' . $inv->status . PHP_EOL;

        $amt = 0;
        foreach($inv->payments as $p){
            echo 'inv paid amt1 : ' .$p->amount . PHP_EOL;
            echo 'inv paid status: ' .$p->payment->status . PHP_EOL;
            if($p->payment->status == 6){
                $amt += $p->amount;
                echo 'inv paid amt : ' .$p->amount . PHP_EOL;
            }
        }
        $total_paid =  round($amt * 1000)/1000;
        echo 'inv paid last amt : ' .$total_paid . PHP_EOL;

        echo 'inv paid : ' . $inv->paid() . PHP_EOL;
        echo 'inv ttl : ' . $inv->total . PHP_EOL;

        $inv->checkPaid();
        echo 'inv status2 : ' . $inv->status . PHP_EOL;
        $inv->save();

        echo 'inv status3 : ' . $inv->status . PHP_EOL;

        $p->ata -= $t;
        $p->update(['ata']);

    }

    public function getAfbSyncXero(){
        $sBillings = AFInvoiceReconciliation::model()->findAll('history_id = :hid AND matched_result = :mok',[':hid' => 1,':mok' => AFInvoiceReconciliation::MATCHED_RESULT_SUCCESS]);
        foreach ( $sBillings as $billing ) {
            $r = new $billing->model;
            $pModel = $r::model()->findByPk($billing->fid);
            if (!empty($pModel)) {
                // only for not sync with xero billing lines
                $pBillingLines = BillingLine::model()->findAll('billing_ref = :bref AND sync_xero = 1', [':bref' => $pModel->no]);
                if ( !empty($pBillingLines) ) {
                    echo $billing->invoice_no . PHP_EOL;
                }
            }
        }
        echo 'ALL Done'. PHP_EOL;
    }

    /**
     * fix storage invoice issue , can only be run once !!!!
     */
    public function fixStInvoiceIssue(){
        return;
        $invoice = Invoice::model()->findByPk(29790);
        $invoice->createCreditForMe();
        $invoice = Invoice::model()->findByPk(29799);
        $inv1 = Invoice::model()->findByPk(29642);
        $inv2 = Invoice::model()->findByPk(29500);
        $oldInv = array($inv1,$inv2);
        foreach ( $oldInv as $inv ) {
            foreach ($inv->lines as $line) {
                $invoice->total += $line->amount;
                $invoice->gst += $line->gst;

                // create related invoice line and attached it to the invoice
                $il = new InvLine;
                $il->inv_id = $invoice->id;
                $il->amount = $line->amount;
                $il->gst = $line->gst;
                $il->qty = 1;
                $il->mdata['items'] = $line->mdata['items'];
                $il->model = 'Manifest';
                $il->fid = $line->fid;
                $il->save();
            }
        }
        $invoice->save();
        $inv1->createCreditForMe();
        $inv2->createCreditForMe();

    }

    /**
     * update all job related document accrual cost as 51.00
     */
    public function updateOldDocumentFee()
    {
        $jobs = EdiJob::model()->findAll('created >= :ldate and status != 40', [':ldate' => '2017-07-01']);
        echo 'all ' . count($jobs) . ' to be done';
        foreach ($jobs as $job) {
            $billings = BillingLine::model()->findAll('billing_ref = :cno', [':cno' => $job->no]);
            foreach ($billings as $billing) {
                if ($billing->item_code == 'GL5') {
                    $billing->price = 51;
                    $billing->accrual_amount = 51;
                    $billing->update('price', 'accrual_amount');
                }
            }
            echo 'done for ' . $job->no . PHP_EOL;
        }

        echo 'all finished';
    }

    public function addNewGlCodes(){
        $mapfile = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'newcodes.xlsx';
        $xls = new oExcel;
        $xls->load($mapfile);
        $data = $xls->getAll();
        echo 'all ' . count($data) . ' loaded' . PHP_EOL;
        unset($data[1]);
        $addedCount = 0;
        foreach ( $data as $k => $line) {
            $newCode = trim($line[1]);
            echo 'processing : ' . $newCode . PHP_EOL;
            $newDesc = trim($line[2]);
            $type = trim($line[3]);
            $taxCode = trim($line[4]);
            $m = Chargecode::model()->find('status = 1 AND code = :code',[':code' => $newCode]);
            if ( empty($m) ) {
                $m = new Chargecode();
                $m->code = $newCode;
                $m->name = $newDesc;
                $m->description = $newDesc;
                $m->type = $type;
                $m->tax_code = $taxCode;
                $m->save();
                echo 'added ' . $newCode . PHP_EOL;
                $addedCount ++;
            }
        }
        echo  $addedCount . ' added ' . PHP_EOL;
        echo 'All done ' . PHP_EOL;
    }

    /**
     * update to latest gl codes for new import console
     */
    public function updateGlCodesForImportConsol(){
        $mapfile = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'glmap.xlsx';
        $xls = new oExcel;
        $xls->load($mapfile);
        $data = $xls->getAll();
        echo 'all ' . count($data) . ' loaded' . PHP_EOL;

        unset($data[1]);
        $mapping = array();
        foreach ( $data as $k => $line) {
            $oldCode = trim($line[1]);
            $newCode = trim($line[4]);
            if ( !empty($newCode) ) {
                $mapping[$oldCode] = $newCode;
            }
        }

        // update charge item type as well
        $billings = BillingLine::model()->findAll();
        foreach ( $billings as $billing ) {
            if ( isset($mapping[$billing->charge_code]) ) {
                $billing->charge_code = $mapping[$billing->charge_code];
                $billing->update('charge_code');
                echo 'updated for ' . $billing->id . PHP_EOL;
            }
        }

        echo 'ALL done' . PHP_EOL;

    }

    /**
     * update to latest gl codes for new xero account
     */
    public function mapNewGlCodes(){
        $mapfile = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'glmap.xlsx';
        $xls = new oExcel;
        $xls->load($mapfile);
        $data = $xls->getAll();
        echo 'all ' . count($data) . ' loaded' . PHP_EOL;

        unset($data[1]);
        $mapping = array();
        foreach ( $data as $k => $line) {
            $oldCode = trim($line[1]);
            echo 'processing : ' . $oldCode . PHP_EOL;
            $type = trim($line[2]);
            $taxCode = trim($line[3]);
            $newCode = trim($line[4]);
            $newDesc = trim($line[5]);
            $m = Chargecode::model()->find('status = 1 AND code = :code',[':code' => $oldCode]);
            if ( !empty($m) && !empty($newCode)) {
                $mapping[$oldCode] = $newCode;
                $newExisting = Chargecode::model()->find('status = 1 AND code = :code',[':code' => $newCode]);
                if ( !empty($newExisting ) ) {
                    $m->delete(); // duplicate just removed
                } else {
                    $m->code = $newCode;
                    $m->name = $newDesc;
                    $m->description = $newDesc;
                    $m->type = $type;
                    $m->tax_code = $taxCode;
                    $m->save();
                }
                echo ' updated  ' . $oldCode . PHP_EOL;
            }
            if ( !empty($m) && empty($newCode)) {
                // just delete
              //  $m->delete();
            }

        }

        // update charge item type as well
        $chargeItemTypes = ChargeItemType::model()->findAll();
        foreach ( $chargeItemTypes as $itemType ) {
            if ( !empty($itemType->charge_code) && isset($mapping[$itemType->charge_code]) ) {
                $itemType->charge_code = $mapping[$itemType->charge_code];
            }

            if ( !empty($itemType->cost_code) && isset($mapping[$itemType->cost_code]) ) {
                $itemType->cost_code = $mapping[$itemType->cost_code];
            }

            if ( !empty($itemType->pl_charge_code) && isset($mapping[$itemType->pl_charge_code]) ) {
                $itemType->pl_charge_code = $mapping[$itemType->pl_charge_code];
            }

            if ( !empty($itemType->pl_cost_code) && isset($mapping[$itemType->pl_cost_code]) ) {
                $itemType->pl_cost_code = $mapping[$itemType->pl_cost_code];
            }

            $itemType->save();
        }

        echo 'ALL done' . PHP_EOL;

    }

    public function stCostNoOrg(){
        // for export small parcel
        $ecs = ExcoConsol::model()->findAll('created >= :ldate and status != 90',[':ldate' => '2017-07-01']);
        $noOrgAmount = 0;
        $noOrgAirFreightAmount = 0;
        foreach ( $ecs as $ec ) {
            $billings = BillingLine::model()->findAll('billing_ref = :cno',[':cno' => $ec->no]);
            foreach ( $billings as $billing ) {
                if ( empty($billing->org_id) ) {
                    $noOrgAmount += $billing->accrual_amount;
                    if ( $billing->item_code == 'GL1' ) {
                        $noOrgAirFreightAmount += $billing->accrual_amount;
                    }
                }
            }
        }

        echo 'Export Small Parcels' . PHP_EOL;
        echo 'No Supplier Total Amount ' . $noOrgAmount . PHP_EOL;
        echo 'No Supplier Air Freight Total Amount ' . $noOrgAirFreightAmount . PHP_EOL;

        // for export big parcel
        $noOrgAmount = 0;
        $noOrgAirFreightAmount = 0;
        $jobs = EdiJob::model()->findAll('created >= :ldate and status != 40',[':ldate' => '2017-07-01']);
        foreach ( $jobs as $job ) {
            $billings = BillingLine::model()->findAll('billing_ref = :cno',[':cno' => $job->no]);
            foreach ( $billings as $billing ) {
                if ( empty($billing->org_id) ) {
                    $noOrgAmount += $billing->accrual_amount;
                    if ( $billing->item_code == 'GL1' ) {
                        $noOrgAirFreightAmount += $billing->accrual_amount;
                        $billing->org_id = 954; // update as priority carge
                        $billing->update('org_id');
                    }
                }
            }
        }
        echo 'Export Big Parcels' . PHP_EOL;
        echo 'No Supplier Total Amount ' . $noOrgAmount . PHP_EOL;
        echo 'No Supplier Air Freight Total Amount ' . $noOrgAirFreightAmount . PHP_EOL;
    }

    /**
     *
     */
    public function fixOldEdiTemplate(){
        $allTemplates = EdiJobTemplate::model()->findAll();
        foreach ( $allTemplates  as $template ) {
            $data = json_decode($template->meta);
            if ( !empty($data) && is_array($data) ) {

                // create new version invoice and cost template data
                $costs = array();
                $invoices = array();
                foreach ( $data as $line ) {

                    $desc = $line->desc;
                    if ( empty($desc) ) {
                        $chargeItemInfo = ChargeItemType::model()->find('code = :code' , [':code' => $line->ccode]);
                        if ( !empty($chargeItemInfo) ) $desc = $chargeItemInfo->name;
                    }

                    // convert for invoice
                    $invoices[] = array(
                        'ccode' => $line->ccode,
                        'desc' => $desc,
                        'qty' => $line->qty,
                        'rate' => $line->rate,
                        'inv_gst' => $line->inv_gst,
                    );

                    // convert for cost
                    $price = $line->cost_amount;
                    if ( $price <= 0.0 )  $price = 0.01; // set default one
                    $gst = $line->cost_gst;
                    if ( $gst == 'EXEMPTEXPORT') $gst = 'EXEMPTEXPENSES';
                    if ( empty($gst ) ) $gst = 'EXEMPTEXPENSES';
                    $costs[] = array(
                        'item_code' => $line->ccode,
                        'desc' => $desc,
                        'org_id' => $line->supplier_id ,
                        'qty' => 1,
                        'price' => $price,
                        'gst' =>$gst,
                    );

                }
                $template->meta = json_encode(array('invoice' => $invoices,'cost' => $costs));
                $template->save();

            }
        }

    }
    // from 10.26 balance report and 9.28 - 10.26 occured invoice and payment
    // to get 9.28 maybe right balance report
    public function getBackOldBalanceReportPro(){
        $fromDate = '2017-09-29';
        $toDate = '2017-10-26';
        $b2file = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'b2.xlsx';

        // get balance data by customer for previous one - b1
        $balanceData = array();
        $overpaymentData = array();
        $xls = new oExcel;
        $xls->load($b2file);
        $data = $xls->getAll();
        $preOrgid = 0;
        $allBalanceAmount = 0;
        foreach ( $data as $k => $line) {
            $orgId = trim($line[1]);
            if ( is_numeric($orgId) ) {
                $preOrgid = $orgId;
            } else {
                $tag = trim($line[1]);
                if ( !empty($tag) && $tag != 'No') {
                    $balanceData[$tag] = $line[8];
                    $allBalanceAmount += $line[8];
                } else {
                    if ( trim($line[7]) == 'Total Available Payment:') {
                        // save as overpayment
                        if ( !isset($overpaymentData[$preOrgid]) ) {
                            $overpaymentData[$preOrgid] = array();
                        }
                        $overpaymentData[$preOrgid][] = array(
                            'id' => 'PCN-D01',
                            'amount' => abs($line[8])
                        );
                        $allBalanceAmount -= abs($line[8]);
                    }
                }
            }
        }
        unset($xls);

        // get payments from 9.29 - 10.26
        $payments = Payment::model()->findAll('status != :dstates and transaction_date > :fdate and transaction_date <= :tdate' , [
            ':dstates' =>  Payment::PAYMENT_STATUS_DELETED,
            ':fdate' => $fromDate,
            ':tdate' => $toDate
        ]);

        // get invoices from 9.29 - 10.26
        $invoices = Invoice::model()->findAll('status != :dstates and dpt_id = :did and posted > :fdate and posted <= :tdate' , [
            ':dstates' =>  Invoice::INVOICE_STATUS_CACELLED,
            ':fdate' => $fromDate,
            ':tdate' => $toDate,
            ':did' =>   Org::PCAE_DEPARTMENT_SYDNEY
        ]);
        $allInvoices = array();
        $invoiceTotal = 0;
        foreach ( $invoices as $invoice) {
            $allInvoices[$invoice->no] = $invoice;
            $invoiceTotal += $invoice->total;
        }

        $paymentTotal = 0;
        foreach ( $payments as $payment ) {
            $paymentTotal += $payment->amount;
        }

        // remove all new added invoices , and revert not existing as an overpayment
        foreach ( $allInvoices as $invno => $invoice ) {
            if ( isset($balanceData[$invno]) ) {
                $balanceData[$invno] -= $invoice->total;
            } else {
                if ( !isset($overpaymentData[$invoice->to_id]) ) {
                    $overpaymentData[$preOrgid] = array();
                    $overpaymentData[$preOrgid][] = array(
                        'id' => 'PCN-D01',
                        'amount' => 0
                    );
                }
                $overpaymentData[$preOrgid][0]['amount'] -= $invoice->total;
            }
        }

        // remove payments which including new added invoice
        foreach ( $payments as $payment ) {

            // in case no any related invoice we revert it as over payment
            if ( empty($payment->invoices) ) {
                if ( !isset($overpaymentData[$payment->org_id]) ) {
                    $overpaymentData[$payment->org_id] = array();
                    $overpaymentData[$payment->org_id][] = array(
                        'id' => 'PCN-D01',
                        'amount' => 0
                    );
                }
                $overpaymentData[$payment->org_id][0]['amount'] += $payment->amount;
                continue;
            }

            // in case related invoices existing
            // and invoices in closing balance as well
            // we should add paid amount to the balance invoice amount
            foreach ( $payment->invoices as $invoice ) {
                $invno = $invoice->no;
                $pi = PayInv::model()->find('pay_id = :pid  AND inv_id = :iid',[':pid' => $payment->id,':iid' => $invoice->id]);
                $paidAmount = 0;
                if ( !empty($pi) ) $paidAmount = $pi->amount;
                if ( !isset($balanceData[$invno]) ) {
                    if ( $paidAmount > 0 ) {
                        $balanceData[$invno] = $paidAmount;
                    }
                } else {
                    if ( $paidAmount > 0 ) {
                        $balanceData[$invno] += $paidAmount;
                    }
                }
            }
        }

        // out put all the result
        $filename = 'balance_report_get_revert_balance_report.xlsx';
        $fullPathFile = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.$filename;

        $xls = new oExcel;
        $i = 1;
        $totBalance = 0;
        $balanceDataByOrg = array();
        foreach ( $balanceData as $invno => $amount ) {
            $invoice = Invoice::model()->find('no = :no',[':no' => $invno]);
            $orgId = 0;
            $orgName = '';
            if ( !empty($invoice) ) {
                $org = Org::model()->findByPk($invoice->to_id);
                $orgId = empty($org) ? 0 : $org->id;
                $orgName = empty($org) ? '' : $org->name;
            }

            if ( !isset($balanceDataByOrg[$orgId]) ) {
                $balanceDataByOrg[$orgId] = array( 'name' => $orgName, 'invoice' => array() );
            }
            $balanceDataByOrg[$orgId]['invoice'][] = array(
                'no' => $invno,
                'amount' => $amount
            );
        }

        // try to calculate new balance
        $newBalanceAmount = 0;
        foreach ( $balanceDataByOrg as $orgId => $data ) {
            foreach ( $data['invoice'] as $invoice ) {
                $newBalanceAmount += $invoice['amount'];
            }
        }
        foreach ( $overpaymentData as $orgId =>  $ops ) {
            foreach ( $ops as $op )
            {
                $newBalanceAmount -= $op['amount'];
            }
        }
        // get diff now
        $diff = round($allBalanceAmount - ($newBalanceAmount + $invoiceTotal - $paymentTotal),2);
        if ( $diff != 0 ) {
            // apply diff to op evenly
            $opCount = count($overpaymentData);
            $evenAmount = round($diff/$opCount,2);
            $adjust = round($diff - ($evenAmount * $opCount) - 0.5,2) ;
            foreach ( $overpaymentData as $orgId =>  $ops ) {
                if ( $adjust != 0.0 ) {
                    $overpaymentData[$orgId][0]['amount'] += $adjust;
                    $adjust = 0.0;
                }
                $overpaymentData[$orgId][0]['amount'] -= $evenAmount;
            }
        }

        // balance total by customer
        $balanceTotalByCustomer = array();
        foreach ( $balanceDataByOrg as $orgId => $data ) {
            if ( !isset($balanceTotalByCustomer[$orgId]) ) {
                $org = Org::model()->findByPk($orgId);
                $orgName = empty($org) ? '' : $org->name;
                $balanceTotalByCustomer[$orgId] = array(
                    'name' => $orgName,
                    'balance' => 0
                );
            }
            foreach ( $data['invoice'] as $invoice ) {
                $balanceTotalByCustomer[$orgId]['balance'] += $invoice['amount'];
            }
        }
        foreach ( $overpaymentData as $orgId => $op ) {
            if ( !isset($balanceTotalByCustomer[$orgId]) ) {
                $org = Org::model()->findByPk($orgId);
                $orgName = empty($org) ? '' : $org->name;
                $balanceTotalByCustomer[$orgId] = array(
                    'name' => $orgName,
                    'balance' => 0
                );
            }
            $balanceTotalByCustomer[$orgId]['balance'] -=  $op[0]['amount'];
        }


        // output balance data
        foreach ( $balanceDataByOrg as $orgId => $data ) {
            $xls->addRow($i++, [$orgId,$data['name']]);
            $xls->addRow($i++, ['No','','balance']);
            foreach ( $data['invoice'] as $invoice ) {
                if ( $invoice['amount'] == 0 ) continue;
                $xls->addRow($i++, [$invoice['no'],'',$invoice['amount']]);
                $totBalance += $invoice['amount'];
            }

            if ( isset($overpaymentData[$orgId]) ) {
                foreach ( $overpaymentData[$orgId] as $op ) {
                    if ( $op['amount'] == 0 ) continue;
                    $opAmount = 0;
                    $opAmount -= $op['amount'];
                    $xls->addRow($i++, [$op['id'],'', $opAmount]);
                    $totBalance -= $op['amount'];
                }
                unset($overpaymentData[$orgId]);
            }
        }

        // still having some op ?
        foreach ( $overpaymentData as $orgId =>  $ops ) {
            $org = Org::model()->findByPk($orgId);
            $xls->addRow($i++, [$orgId,!empty($org) ? $org->name : '']);
            foreach ( $ops as $op )
            {
                if ( $op['amount'] == 0 ) continue;
                $opAmount = 0;
                $opAmount -= $op['amount'];
                $xls->addRow($i++, [$op['id'],'', $opAmount]);
                $totBalance -= $op['amount'];
            }
        }

        $xls->addRow($i++, ['']);
        $xls->addRow($i++, ['Balance Total',$totBalance]);
        $xls->addRow($i++, ['Occuring Invoice',$invoiceTotal]);
        $xls->addRow($i++, ['Occuring Payment',$paymentTotal]);

        $xls->addRow($i++, ['New Balance',round($totBalance + $invoiceTotal - $paymentTotal,2) ]);
        $xls->addRow($i++, ['Origin Balance',$allBalanceAmount]);
        $xls->addRow($i++, ['Diff',round($allBalanceAmount - ($totBalance + $invoiceTotal - $paymentTotal) ,2)]);


        $xls->addRow($i++, ['']);
        $invoicesByOrg = array();
        $xls->addRow($i++, ['occurring invoice']);
        $invoicesByOrg = array();
        foreach ( $invoices as $invoice) {
           if ( !isset($invoicesByOrg[$invoice->to_id]) ) {
               $invoicesByOrg[$invoice->to_id] = array();
           }
            $invoicesByOrg[$invoice->to_id][] = $invoice;
        }
        foreach ( $invoicesByOrg as $orgId => $invoices ) {
            $org = Org::model()->findByPk($orgId);
            $xls->addRow($i++, [$orgId,$org->name]);
            $xls->addRow($i++, ['No.' , 'Amount']);
            foreach ( $invoices  as $invoice ) {
                $xls->addRow($i++, [$invoice->no ,$invoice->total]);
            }
        }


        $xls->addRow($i++, ['occurring payments']);
        $paymentsByOrg = array();
        foreach ( $payments as $payment) {
            if ( !isset($invoicesByOrg[$payment->org_id]) ) {
                $invoicesByOrg[$payment->org_id] = array();
            }
            $paymentsByOrg[$payment->org_id][] = $payment;
        }
        foreach ( $paymentsByOrg as $orgId => $payments ) {
            $org = Org::model()->findByPk($orgId);
            $xls->addRow($i++, [$orgId,$org->name]);
            $xls->addRow($i++, ['Date' , 'Transaction Date','Amount']);
            foreach ( $payments  as $payment ) {
                $xls->addRow($i++, [$payment->date ,$payment->transaction_date,$payment->amount]);
            }
        }

        // add balance total by customer
        $xls->addRow($i++, ['balance by customer']);
        $xls->addRow($i++, ['id','name','balance']);
        foreach ( $balanceTotalByCustomer as $orgId => $data ) {
            $xls->addRow($i++, [$orgId,$data['name'],$data['balance']]);
        }

        $xls->output($fullPathFile);
    }

    // from 10.26 balance report and 9.28 - 10.26 occured invoice and payment
    // to get 9.28 maybe right balance report
    public function getBackOldBalanceReport(){
        $fromDate = '2017-09-29';
        $toDate = '2017-10-26';
        $b2file = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'b2.xlsx';

        // get balance data by customer for previous one - b1
        $balanceData = array();
        $overpaymentData = array();
        $xls = new oExcel;
        $xls->load($b2file);
        $data = $xls->getAll();
        $preOrgid = 0;
        $allBalanceAmount = 0;
        foreach ( $data as $k => $line) {
            $orgId = trim($line[1]);
            if ( is_numeric($orgId) ) {
                if ( !isset($balanceData[$orgId]) ) {
                    $balanceData[$orgId] = array(
                        'invoice' => array(),
                        'name' => '');
                }
                $preOrgid = $orgId;
            } else {
                $tag = trim($line[1]);
                if ( !empty($tag) && $tag != 'No') {
                    $balanceData[$preOrgid]['invoice'][] = array(
                        'no' => $line[1],
                        'balance' => $line[8]
                    );
                    $allBalanceAmount += $line[8];
                } else {
                    if ( trim($line[7]) == 'Total Available Payment:') {
                        // save as overpayment
                        if ( !isset($overpaymentData[$preOrgid]) ) {
                            $overpaymentData[$preOrgid] = array();
                        }
                        $overpaymentData[$preOrgid][] = array(
                            'id' => 'PCN-D01',
                            'amount' => abs($line[8])
                        );
                        $allBalanceAmount -= abs($line[8]);
                    }
                }
            }
        }
        unset($xls);

        // get payments from 9.29 - 10.26
        $payments = Payment::model()->findAll('status != :dstates and transaction_date > :fdate and transaction_date <= :tdate' , [
           ':dstates' =>  Payment::PAYMENT_STATUS_DELETED,
            ':fdate' => $fromDate,
            ':tdate' => $toDate
        ]);

        // get invoices from 9.29 - 10.26
        $invoices = Invoice::model()->findAll('status != :dstates and dpt_id = :did and posted > :fdate and posted <= :tdate' , [
            ':dstates' =>  Invoice::INVOICE_STATUS_CACELLED,
            ':fdate' => $fromDate,
            ':tdate' => $toDate,
            ':did' =>   Org::PCAE_DEPARTMENT_SYDNEY
        ]);
        $allInvoices = array();
        $invoiceTotal = 0;
        foreach ( $invoices as $invoice) {
            $allInvoices[$invoice->no] = $invoice;
            $invoiceTotal += $invoice->total;
        }

        $paymentTotal = 0;
        foreach ( $payments as $payment ) {
            $paymentTotal += $payment->amount;
        }

        // remove all new added invoices
        foreach ( $balanceData as $orgId => $data ) {
            foreach ( $data['invoice'] as $k =>  $invoice ) {
                if ( isset($allInvoices[$invoice['no']]) ) {
                    unset($data['invoice'][$k]);
                    unset($allInvoices[$invoice['no']]);
                }
            }
            $balanceData[$orgId] = $data;
        }

        // still having some invoice not found in balance we need added them as overpayment
        foreach ( $allInvoices as $ino => $inv ) {
            if ( !isset($overpaymentData[$inv->to_id]) ) {
                $overpaymentData[$inv->to_id] = array();
            }
            $overpaymentData[$inv->to_id][] = array(
                'id' => 'PCN-' . $inv->no,
                'amount' => $inv->total
            );
        }

        // remove payments which including new added invoice
        foreach ( $payments as $payment ) {

            // in case no any related invoice we revert it as over payment
            if ( empty($payment->invoices) ) {
                if ( !isset($balanceData[$payment->org_id]) ) {
                    $balanceData[$payment->org_id] = array('invoice' => array(), 'name' => '');
                }
                $balanceData[$payment->org_id]['invoice'][] = array(
                    'no' => 'VPCN-' . $payment->id,
                    'balance' => $payment->amount
                );
                continue;
            }

            // in case related invoices existing
            // and invoices in closing balance as well
            // we should add paid amount to the balance invoice amount
            foreach ( $payment->invoices as $invoice ) {
                $existing = false;
                foreach ( $balanceData as $orgId => $data ) {
                    foreach ( $data['invoice'] as $k =>  $inv ) {
                        if ( $invoice->no == $inv['no'] ) {
                            $existing = true;
                            // get paid amount
                            $pi = PayInv::model()->find('pay_id = :pid  AND inv_id = :iid',[':pid' => $payment->id,':iid' => $invoice->id]);
                            $paidAmount = 0;
                            if ( !empty($pi) ) $paidAmount = $pi->amount;
                            if ( $paidAmount > 0 ) {
                                $data['invoice'][$k]['balance'] += $paidAmount;
                                $balanceData[$orgId] = $data;
                            }
                            break;
                        }
                    }
                    if ( $existing ) break;
                }
                if ( !$existing ) {
                    if ( !isset($allInvoices[$invoice->no])) {
                        // get paid amount
                        $pi = PayInv::model()->find('pay_id = :pid  AND inv_id = :iid', [':pid' => $payment->id, ':iid' => $invoice->id]);
                        $paidAmount = 0;
                        if (!empty($pi)) $paidAmount = $pi->amount;
                        if ($paidAmount > 0) {
                            // we should add as a virtual invoice
                            if (!isset($balanceData[$invoice->to_id])) {
                                $balanceData[$invoice->to_id] = array('invoice' => array(), 'name' => '');
                            }
                            $balanceData[$invoice->to_id]['invoice'][] = array(
                                'no' => $invoice->no,
                                'balance' => $paidAmount
                            );
                        }
                    }
                }
            }
        }

        // out put all the result
        $filename = 'balance_report_get_revert_balance_report.xlsx';
        $fullPathFile = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.$filename;

        $xls = new oExcel;
        $i = 1;
        $totBalance = 0;
        foreach ( $balanceData as $orgId => $data ) {
            $org = Org::model()->findByPk($orgId);
            $xls->addRow($i++, [$orgId,!empty($org) ? $org->name : '']);
            $xls->addRow($i++, ['No','','balance']);
            foreach ( $data['invoice'] as $invoice ) {
                $xls->addRow($i++, [$invoice['no'],'',$invoice['balance']]);
                $totBalance += $invoice['balance'];
            }

            if ( isset($overpaymentData[$orgId]) ) {
                foreach ( $overpaymentData[$orgId] as $op ) {
                   // foreach ( $ops as $k => $op )
                    {
                        $xls->addRow($i++, [$op['id'],'', '-' . $op['amount']]);
                        $totBalance -= $op['amount'];
                    }
                }
                unset($overpaymentData[$orgId]);
            }
        }

        // still having some op ?
        foreach ( $overpaymentData as $orgId =>  $ops ) {
            $org = Org::model()->findByPk($orgId);
            $xls->addRow($i++, [$orgId,!empty($org) ? $org->name : '']);
            foreach ( $ops as $op )
            {
                $xls->addRow($i++, [$op['id'],'', '-' . $op['amount']]);
                $totBalance -= $op['amount'];
            }
        }

        $xls->addRow($i++, ['']);
        $xls->addRow($i++, ['Balance Total',$totBalance]);
        $xls->addRow($i++, ['Occuring Invoice',$invoiceTotal]);
        $xls->addRow($i++, ['Occuring Payment',$paymentTotal]);

        $xls->addRow($i++, ['New Balance',$totBalance + $invoiceTotal - $paymentTotal ]);
        $xls->addRow($i++, ['Origin Balance',$allBalanceAmount]);
        $xls->addRow($i, ['Diff',$allBalanceAmount - ($totBalance + $invoiceTotal - $paymentTotal) ]);

        $xls->output($fullPathFile);
    }

    public function addChannelDB(){
        $cs = ExChannel::getPocs(true);
        foreach ( $cs as $code => $name ) {
            $org = Org::model()->find('code = :code',[':code' => $code] );
            if ( empty($org) ) {
                $org = new Org();
                $org->code = $code;
                $org->name = $name;
                $org->status = 1; // active;
                $org->type = 70; // as supplier
                $org->country = 'China';
                $org->phone = '000000';
                $org->email = 't@t.com';
            }
            $org->name = $name;
            $org->save();
        }
    }

    public function fixExConsoleCost(){
        $costs = ConsolWeightCheck::model()->findAll('status = 1');
        $costsCount = count($costs);
        echo $costsCount .' to be done' . PHP_EOL;

        foreach ( $costs as $cost ) {
            $channel = $cost->channel;
            $org = Org::model()->find('code = :code',[':code' => $channel]);
            $oid = empty($org) ? 0 : $org->id;
            echo 'done for : '. $cost->id . PHP_EOL;

            foreach ( $cost->lines as $line ) {
                $consoleNo = $line->consol_id;
                $clearCost = $line->clearance_cost;
                $deliveryCost = $line->delivery_cost;
                $duty = $line->duty;
                $others = $line->others;
                $exConsol = ExcoConsol::model()->find('no = :no' ,[':no' => $consoleNo] ) ;

                if ( !empty($exConsol) ) {
                    if ( $clearCost > 0 ) {

                        // save as billing
                        $billing = BillingLine::model()->find('org_id = :oid AND billing_ref = :bref AND type = 2 AND item_code = :icode',[
                            ':oid' => $oid , ':icode' => 'channel_clear_cost',':bref' => $exConsol->no
                        ]);
                        if ( empty($billing) ) {
                            $billing = new BillingLine();
                            $billing->org_id = $oid;
                            $billing->billing_ref = $exConsol->no;
                            $billing->item_code = 'channel_clear_cost';
                            $billing->link_id = $exConsol->id;

                            $billing->currency = 1;
                            $billing->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
                            $billing->dpmt = Invoice::DPMT_EXPORT;
                            $billing->type = BillingLine::BILLING_TYPE_EXPORT;
                            $billing->desc = 'channel_clear_cost';
                            $billing->gst = 'EXEMPTEXPENSES';
                            $billing->price = 0;
                            $billing->qty = 1;

                            // get related supplier cost gl code
                            $billing->charge_code = '91002';

                            $billing->op_id =  isset(Yii::app()->user) ? Yii::app()->user->id : 0;

                        }
                        // get related supplier cost gl code
                        $billing->charge_code = '91002';
                        $billing->actual_amount = $clearCost;
                        $billing->save();

                        PlLedger::add([
                            'fid' => $exConsol->id,
                            'model' => 'ExcoConsol',
                            'org_id' => $oid,
                            'dpt_id' => Org::PCAE_DEPARTMENT_SYDNEY,
                            'dpmt' => Invoice::DPMT_EXPORT,
                            'lid' => 0,
                            'grp1' => $exConsol->id,
                            'grp2' => 'clear_cost',
                            'gl' => 3, // for EXPORT SMALL PARCEL - PORT & TERMINAL COST
                            'date' => $cost->created,
                            'amt' => $clearCost,
                            'actual_amt' => $clearCost,
                            'gst' => 0,
                            'acc' => 1,
                        ], true);
                    }

                    if ( $deliveryCost > 0 ) {

                        // save as billing
                        $billing = BillingLine::model()->find('org_id = :oid AND billing_ref = :bref AND type = 2 AND item_code = :icode',[
                            ':oid' => $oid , ':icode' => 'channel_delivery_cost',':bref' => $exConsol->no
                        ]);
                        if ( empty($billing) ) {
                            $billing = new BillingLine();
                            $billing->org_id = $oid;
                            $billing->billing_ref = $exConsol->no;
                            $billing->item_code = 'channel_delivery_cost';
                            $billing->link_id = $exConsol->id;

                            $billing->currency = 1;
                            $billing->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
                            $billing->dpmt = Invoice::DPMT_EXPORT;
                            $billing->type = BillingLine::BILLING_TYPE_EXPORT;
                            $billing->desc = 'channel_delivery_cost';
                            $billing->gst = 'EXEMPTEXPENSES';
                            $billing->price = 0;
                            $billing->qty = 1;

                            // get related supplier cost gl code
                            $billing->charge_code = '91002';

                            $billing->op_id =  isset(Yii::app()->user) ? Yii::app()->user->id : 0;

                        }
                        // get related supplier cost gl code
                        $billing->charge_code = '91002';
                        $billing->actual_amount = $deliveryCost;
                        $billing->save();

                        PlLedger::add([
                            'fid' => $exConsol->id,
                            'model' => 'ExcoConsol',
                            'org_id' => $oid,
                            'dpt_id' => Org::PCAE_DEPARTMENT_SYDNEY,
                            'dpmt' => Invoice::DPMT_EXPORT,
                            'lid' => 0,
                            'grp1' => $exConsol->id,
                            'grp2' => 'delivery_cost',
                            'gl' => 3, // for EXPORT SMALL PARCEL - PORT & TERMINAL COST
                            'date' =>  $cost->created,
                            'amt' => $deliveryCost,
                            'actual_amt' => $deliveryCost,
                            'gst' => 0,
                            'acc' => 1,
                        ], true);
                    }

                    if ( $duty > 0 ) {

                        // save as billing
                        $billing = BillingLine::model()->find('org_id = :oid AND billing_ref = :bref AND type = 2 AND item_code = :icode',[
                            ':oid' => $oid , ':icode' => 'channel_duty_cost',':bref' => $exConsol->no
                        ]);
                        if ( empty($billing) ) {
                            $billing = new BillingLine();
                            $billing->org_id = $oid;
                            $billing->billing_ref = $exConsol->no;
                            $billing->item_code = 'channel_duty_cost';
                            $billing->link_id = $exConsol->id;

                            $billing->currency = 1;
                            $billing->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
                            $billing->dpmt = Invoice::DPMT_EXPORT;
                            $billing->type = BillingLine::BILLING_TYPE_EXPORT;
                            $billing->desc = 'channel_duty_cost';
                            $billing->gst = 'EXEMPTEXPENSES';
                            $billing->price = 0;
                            $billing->qty = 1;

                            // get related supplier cost gl code
                            $billing->charge_code = '91002';

                            $billing->op_id =  isset(Yii::app()->user) ? Yii::app()->user->id : 0;

                        }
                        // get related supplier cost gl code
                        $billing->charge_code = '91002';
                        $billing->actual_amount = $duty;
                        $billing->save();

                        PlLedger::add([
                            'fid' => $exConsol->id,
                            'model' => 'ExcoConsol',
                            'org_id' => $oid,
                            'dpt_id' => Org::PCAE_DEPARTMENT_SYDNEY,
                            'dpmt' => Invoice::DPMT_EXPORT,
                            'lid' => 0,
                            'grp1' => $exConsol->id,
                            'grp2' => 'duty_cost',
                            'gl' => 3, // for EXPORT SMALL PARCEL - PORT & TERMINAL COST
                            'date' =>  $cost->created,
                            'amt' => $duty,
                            'actual_amt' => $duty,
                            'gst' => 0,
                            'acc' => 1,
                        ], true);
                    }

                    if ( $others > 0 ) {

                        // save as billing
                        $billing = BillingLine::model()->find('org_id = :oid AND billing_ref = :bref AND type = 2 AND item_code = :icode',[
                            ':oid' => $oid , ':icode' => 'channel_others_cost',':bref' => $exConsol->no
                        ]);
                        if ( empty($billing) ) {
                            $billing = new BillingLine();
                            $billing->org_id = $oid;
                            $billing->billing_ref = $exConsol->no;
                            $billing->item_code = 'channel_others_cost';
                            $billing->link_id = $exConsol->id;

                            $billing->currency = 1;
                            $billing->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
                            $billing->dpmt = Invoice::DPMT_EXPORT;
                            $billing->type = BillingLine::BILLING_TYPE_EXPORT;
                            $billing->desc = 'channel_others_cost';
                            $billing->gst = 'EXEMPTEXPENSES';
                            $billing->price = 0;
                            $billing->qty = 1;

                            // get related supplier cost gl code
                            $billing->charge_code = '91002';

                            $billing->op_id =  isset(Yii::app()->user) ? Yii::app()->user->id : 0;

                        }
                        // get related supplier cost gl code
                        $billing->charge_code = '91002';
                        $billing->actual_amount = $others;
                        $billing->save();

                        PlLedger::add([
                            'fid' => $exConsol->id,
                            'model' => 'ExcoConsol',
                            'org_id' => $oid,
                            'dpt_id' => Org::PCAE_DEPARTMENT_SYDNEY,
                            'dpmt' => Invoice::DPMT_EXPORT,
                            'lid' => 0,
                            'grp1' => $exConsol->id,
                            'grp2' => 'others_cost',
                            'gl' => 3, // for EXPORT SMALL PARCEL - PORT & TERMINAL COST
                            'date' =>  $cost->created,
                            'amt' => $others,
                            'actual_amt' => $others,
                            'gst' => 0,
                            'acc' => 1,
                        ], true);
                    }

                }
            }
        }

        echo 'All done' . PHP_EOL;
    }

    public function checkBalanceReportProblems(){
        $fromDate = '2017-09-29';
        $toDate = '2017-10-26';
        $b1file = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'b1.xlsx';
        $b2file = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'b2.xlsx';

        // get balance data by customer for previous one - b1
        $balanceData = array();
        $xls = new oExcel;
        $xls->load($b1file);
        $data = $xls->getAll();
        $preOrgid = 0;
        foreach ( $data as $k => $line) {
            $orgId = trim($line[1]);
            if ( is_numeric($orgId) ) {
                if ( !isset($balanceData[$orgId]) ) {
                    $balanceData[$orgId] = array(
                        'invoice' => Invoice::getOccurringAmountByDateSpanByOrg($fromDate,$toDate,$orgId),
                        'payment' => Payment::getOccurringAmountByDatespanByOrg($fromDate,$toDate,$orgId),
                        'fbalance' => 0,
                        'sbalance' => 0,
                        'name' => $line[2]);
                }
                $preOrgid = $orgId;
            }

            // try to find total OP line if existing
            if ( $line[5] == 'Total:' ) {
               // $balanceData[$preOrgid]['invoice'] = $line[6];
               // $balanceData[$preOrgid]['payment'] = $line[7];
                $balanceData[$preOrgid]['fbalance'] = $line[8];
            }

            if ( $line[7] == 'Total Available Payment:' ) {
                $balanceData[$preOrgid]['fbalance'] += $line[8];
            }
        }
        unset($xls);

        $xls = new oExcel;
        $xls->load($b2file);
        $data = $xls->getAll();
        $preOrgid = 0;
        foreach ( $data as $k => $line) {
            $orgId = trim($line[1]);
            if ( is_numeric($orgId) ) {
                if ( !isset($balanceData[$orgId]) ) {
                    $balanceData[$orgId] = array(
                        'invoice' => Invoice::getOccurringAmountByDateSpanByOrg($fromDate,$toDate,$orgId),
                        'payment' => Payment::getOccurringAmountByDatespanByOrg($fromDate,$toDate,$orgId),
                        'fbalance' => 0,
                        'sbalance' => 0,
                        'name' => $line[2]);
                }
                $preOrgid = $orgId;
            }

            // try to find total OP line if existing
            if ( $line[5] == 'Total:' ) {
                // $balanceData[$preOrgid]['invoice'] = $line[6];
                // $balanceData[$preOrgid]['payment'] = $line[7];
                $balanceData[$preOrgid]['sbalance'] = $line[8];
            }

            if ( $line[7] == 'Total Available Payment:' ) {
                $balanceData[$preOrgid]['sbalance'] += $line[8];
            }
        }

        unset($xls);

        // out put all the result
        $filename = 'balance_report_check_problems.xlsx';
        $fullPathFile = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.$filename;

        $xls = new oExcel;
        $i = 1;
        $xls->addRow($i++, ['id','name','last balance','invoice','payment','new balance']);
        foreach ( $balanceData as $orgId => $data ) {
            $xls->addRow($i++, [$orgId,$data['name'],$data['fbalance'],$data['invoice'],$data['payment'],$data['sbalance']]);
        }
        $xls->output($fullPathFile);

    }

    public function checkBalanceReportProblemsPro(){
        $fromDate = '2017-09-29';
        $toDate = '2017-10-26';
        $b1file = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'b1.xlsx';
        $b2file = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'b2.xlsx';

        // get balance data by customer for previous one - b1
        $balanceData = array();
        $xls = new oExcel;
        $xls->load($b1file);
        $data = $xls->getAll();
        $preOrgid = 0;
        foreach ( $data as $k => $line) {
            $orgId = trim($line[1]);
            if ( is_numeric($orgId) ) {
                if ( !isset($balanceData[$orgId]) ) {
                    $balanceData[$orgId] = array(
                        'balance' => 0,
                        'name' => $line[2]);
                }
                $preOrgid = $orgId;
            }

            // try to find total OP line if existing
            if ( $line[5] == 'Total:' ) {
                // $balanceData[$preOrgid]['invoice'] = $line[6];
                // $balanceData[$preOrgid]['payment'] = $line[7];
                $balanceData[$preOrgid]['balance'] = $line[8];
            }

            if ( $line[7] == 'Total Available Payment:' ) {
                $balanceData[$preOrgid]['balance'] += $line[8];
            }
        }
        unset($xls);

        $xls = new oExcel;
        $balanceDataOld = array();
        $xls->load($b2file);
        $data = $xls->getAll();
        $preOrgid = 0;
        foreach ( $data as $k => $line) {
            $tag = trim($line[1]);
            $org = Org::model()->find('name = :name', [':name' => $tag]);
            $orgId = empty($org) ? 0 : $org->id;
            if ( $preOrgid != $orgId && $orgId != 0 ) {
                if ( !isset($balanceDataOld[$orgId]) ) {
                    $balanceDataOld[$orgId] = array(
                        'balance' => 0,
                        'name' => $tag);
                }
                $preOrgid = $orgId;
            }

            // try to find total OP line if existing
            if ( strpos($tag, 'Total') !== false ) {
                if ( !isset($balanceDataOld[$preOrgid]) ) {
                    $balanceDataOld[$orgId] = array(
                        'balance' => 0,
                        'name' => $tag );
                }
                $balanceDataOld[$preOrgid]['balance'] += $line[4];
            }
        }
        unset($xls);

        // out put all the result
        $filename = 'balance_1026_bycustomer.xlsx';
        $fullPathFile = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.$filename;

        $xls = new oExcel;
        $i = 1;
        $xls->addRow($i++, ['id','name','balance']);
        foreach ( $balanceData as $orgId => $data ) {
            $xls->addRow($i++, [$orgId,$data['name'],$data['balance']]);
        }
       // $xls->output($fullPathFile);
        unset($xls);

        $filename = 'balance_0929_bycustomer.xlsx';
        $fullPathFile = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.$filename;

        $xls = new oExcel;
        $i = 1;
        $xls->addRow($i++, ['id','name','balance']);
        foreach ( $balanceDataOld as $orgId => $data ) {
            $xls->addRow($i++, [$orgId,$data['name'],$data['balance']]);
        }
        $xls->output($fullPathFile);

    }

    public function separateAirfreightCost(){
        $jobs = EdiJob::model()->findAll();
        $totalCount =  count($jobs);
        echo 'jobs : ' . $totalCount  . ' to be processed' . PHP_EOL;
        $successCount = 0;
        foreach ( $jobs as $job ) {
            echo 'processing: ' . $job->no . PHP_EOL;
            foreach ( $job->lines as $line ) {
                $billing = BillingLine::model()->find('type = :type AND billing_ref = :bref AND link_id = :lid',
                    [':type' => BillingLine::BILLING_TYPE_AIR_SEA, ':bref' => $job->no, ':lid' => $line->id]);
                if ( empty($billing) ) {
                    echo 'line ' . $line->id . ' related billing line not found'  . PHP_EOL;
                } else {
                    $billing->qty = 1;
                    $billing->price = $billing->accrual_amount;
                    $billing->item_code = $line->ccode;
                    $billing->update();
                    $successCount++;
                    echo 'done for ' . $line->id  . PHP_EOL;
                }
            }
        }
        echo 'all done : ' . $totalCount . ' success : ' . $successCount . PHP_EOL;
    }

    public function fixConsoleInsurance(){
        $ps = array('20003538','460.38','20003540','109.63','20004165','126.88','20004405','63.38','20004409','142.13','20004410','306.14','20004435','31.13',
            '20004476','18.63','20004493','143.50','20004517','127.56','20004519','145.19','20004521','45.75','20004523','272.38','20004524','83.25','20004525','129.83',
            '20004526','27.25','20004527','83.91','20004528','44.75','20004530','175.38','20004532','67.00','20004533','52.94','20004536','89.38','20004537','52.54',
            '20004539','218.88','20004540','133.00','20004541','75.88','20004542','37.38','20004543','43.63','20004544','34.75','20004545','9.88','20004546','68.50',
            '20004548','129.38','20004550','28.63','20004551','240.75','20004552','135.91','20004555','82.13','20004557','48.54','20004558','140.38','20004562','126.16',
            '20004563','67.00','20004565','138.13','20004566','31.35','20004568','14.81','20004569','53.50','20004570','69.63','20004571','140.75','20004572','233.89',
            '20004577','66.00','20004578','125.63','20004579','44.75','20004580','159.06','20004581','19.88','20004582','31.00','20004584','118.91','20004586','69.75',
            '20004588','144.41','20004590','28.38','20004592','16.13','20004593','13.50','20004594','22.38','20004596','50.88','20004598','133.00','20004599','18.63','20004600','139.38',
            '20004601','28.63','20004602','22.38','20004605','126.63','20004606','77.00','20004607','31.13','20004609','321.38','20004610','144.25','20004611','212.25','20004612','22.38',
            '20004613','133.88','20004615','130.50','20004616','144.00','20004617','144.00','20004619','128.13','20004620','165.13','20004624','47.94','20004626','78.38','20004627','34.75',
            '20004628','222.28','20004634','163.66','20004637','125.50','20004638','141.83','20004640','34.88','20004642','134.41','20004644','97.13','20004645','134.79','20004646','38.50',
            '20004647','19.88','20004649','139.25','20004651','40.28','20004652','179.33','20004656','28.63','20004658','129.38','20004660','56.00','20004662','46.84','20004663','200.13',
            '20004664','153.75','20004667','148.88','20004668','150.00','20004669','81.13','20004672','31.13','20004674','208.25','20004677','73.38','20004678','181.38','20004680','151.75',
            '20004681','123.00','20004682','21.79','20004683','129.91','20004684','145.25','20004687','34.88','20004688','157.70','20004689','201.31','20004690','70.88','20004692','22.38',
            '20004695','182.23','20004696','51.00','20004698','31.13','20004701','311.83'
        );

        $allCount = count($ps);
        for ( $index = 0 ; $index < $allCount ;) {

            $p = ImParcel::model()->find('hbn = :hbn', [':hbn' => $ps[$index]]);
            $insurance = $ps[$index+1];
            $p->insurance = $insurance;
            $p->update('insurance');
            $index = $index + 2;
        }
    }
    public function getAirStsData(){
        $processedCount = 0;
        $totalWeight = 0;
        $totalInvoice = 0;
        $rs = EdiJob::model()->findAll('status < 40 AND created >= :fd AND created <= :td', [':fd' => '2017-01-01', ':td' => '2017-06-31']);
        foreach ($rs as $i => $job){
            if ( empty($job->awb) ) conitinue;

            foreach ( $job->lines as $line ){
                $processedCount++;
                if ( empty($line) ) continue;
                if ( $line->ccode == 'GL1' ) {
                    $totalWeight += floatval($line->qty);
                    $totalInvoice += $line->qty * $line->rate;
                    break;
                }
            }
            echo 'Job ' . $job->no . ' Done'. PHP_EOL;
            unset($job->lines);
            unset($rs[$i]);
        }

        echo 'all ' . $processedCount . ' Jobs done'. PHP_EOL;
        echo 'All weight : ' . $totalWeight . PHP_EOL;
        echo 'All invoice : ' . $totalInvoice . PHP_EOL;

    }
}
