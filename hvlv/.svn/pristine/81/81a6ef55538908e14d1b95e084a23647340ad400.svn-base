<?php

/**
 * This is the model class for table "balance_report".
 *
 * The followings are the available columns in table 'balance_report':
 * @property integer $id
 * @property string $created_date
 * @property string $date_month
 * @property string $last_balance
 * @property string $current_invoice
 * @property string $current_payment
 * @property string $current_balance
 * @property string $note
 */
class BalanceReport extends CActiveRecord
{
    public $mdata;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'balance_report';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created_date', 'required'),
			array('date_month', 'length', 'max'=>15),
			array('last_balance, current_invoice, current_payment, current_balance', 'length', 'max'=>20),
			array('note', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, created_date, date_month, last_balance, current_invoice, current_payment, current_balance, note', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'created_date' => 'Created Date',
			'date_month' => 'Date Month',
			'last_balance' => 'Last Balance',
			'current_invoice' => 'Current Invoice',
			'current_payment' => 'Current Payment',
			'current_balance' => 'Current Balance',
			'note' => 'Note',
		);
	}

    /**
     * run balance report
     * return error message or success message
     * @throws CDbException
     */
    public function runBalanceReport($overwrite = false){
        // first step we need to close all invoices and payments for last month
        Invoice::closeInvoicesFromNow();
        $overwriteOldReport = $overwrite;
        $currentDate = date('Y-m-d');
        $existingReport = BalanceReport::model()->find('date_month = :dm',[':dm' => $currentDate]);
        if ( !empty($existingReport) && !$overwriteOldReport) {
            return  'Report has been created successfully before, if you want to overwrite it , please tick the overwrite box';
        }

        // get previous balance report current balance amount
        $bp = BalanceReport::model()->findAll(array(
            'condition' => 'date_month < :dm',
            'limit' => 1,
            'order' => 't.id DESC',
            'params' => array(':dm' => $currentDate),
        ));
        $prevBalance = 0;
        $prevDate = '';
        if ( !empty($bp) ) {
            $prevBalance = $bp[0]->current_balance;
            $prevDate = $bp[0]->date_month;
        }

        // get last month balance
        // based on posted date in invoice and transaction data in payment line
        // because old duty data , we can't use this logic
        $lastMonthBalance = 0;

        // get last month occurring invoice balance
        // based on posted date
        if ( empty($prevDate) ) {
            $lastMonthOccurringInvoiceAmount = 0;
        } else {
            $lastMonthOccurringInvoiceAmount = Invoice::getOccurringAmountByDateSpan($prevDate, $currentDate);
            if (empty($lastMonthOccurringInvoiceAmount)) $lastMonthOccurringInvoiceAmount = 0;
        }

        // get last month occurring payment balance
        // based on transaction date
        if ( empty($prevDate) ) {
            $lastMonthOccurringPaymentAmount = 0;
        } else {
            $lastMonthOccurringPaymentAmount = Payment::getOccurringAmountByDatespan($prevDate, $currentDate);
            if (empty($lastMonthOccurringPaymentAmount)) $lastMonthOccurringPaymentAmount = 0;
        }
        // save last month balance
        if ( !empty($existingReport) && $overwriteOldReport ){
            $bp = $existingReport; // just update old one
        } else {
            $bp = new BalanceReport(); // create a new one
        }
        $bp->created_date = date('Y-m-d');
        $bp->last_balance = $prevBalance;
        $bp->current_balance = $lastMonthBalance;
        $bp->current_invoice = $lastMonthOccurringInvoiceAmount;
        $bp->current_payment = $lastMonthOccurringPaymentAmount;
        $bp->date_month = $currentDate;
        $bp->save();
        $rt = $bp->getErrors();
        $errors = '';
        foreach ( $rt as $k => $error ) {
            if ( !empty($errors) )  $errors .= ',';
            $errors .=  implode(' ' , $error);
        }

        if ( empty($errors) ) {
            // save last month balance details by customer
            $note = array();
            $totalBalance = 0;
            $balanceByCustomer = array();
            $invoiceByCustomer = array();
            $paymentByCustomer = array();

            $bkFileId = $this->backupDatespanBalanceDetails($currentDate,$bp->id,$totalBalance,$balanceByCustomer);
            $bp->current_balance = $totalBalance;

            $note['balance_file_id'] = $bkFileId;

            // save last month occurring invoice details
            $bkFileId = $this->backupDatespanOccurringInvoiceDetails($prevDate,$currentDate,$bp->id,$invoiceByCustomer);
            $note['invoice_file_id'] = $bkFileId;

            // save last month occurring payment details
            $deleteAmount = 0;
            // in case OP delete some payment , we should revert that amount in currently invoice amount
            // otherwise balance is not equal
            $bkFileId = $this->backupDatespanOccurringPaymentDetails($prevDate,$currentDate,$bp->id,$deleteAmount,$paymentByCustomer);

            // we should think more about this logic
            // ???????? how to deal with payment deleted issue
            $bp->current_invoice += $deleteAmount;
            //$bp->current_payment += $deleteAmount;

            $bp->save();
            $note['payment_file_id'] = $bkFileId;

            // back up balance movement by customer
            $bkFileId = $this->backupCustomerBalanceMovement($currentDate,$bp->id,$balanceByCustomer,$invoiceByCustomer,$paymentByCustomer);
            $note['cbalance_file_id'] = $bkFileId;

            // save all related balace file
            $bp->note = json_encode($note);
            $bp->update('note');
            $errors = 'Report has been created successfully';
        }

        return $errors;

    }

    /**
     * @param $endDate
     * @param $reportId
     * @return int
     */
    private function backupCustomerBalanceMovement($endDate,$reportId,$balanceByCustomer,$invoiceByCustomer,$paymentByCustomer){
        $filename = 'customer_balance_movent_report'. $endDate . '.xlsx';
        $fullPathFile = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.$filename;

        $prevBalanceByCustomer = array();

        // get last day or month balance by customer data
        $model = BalanceReport::model()->findByPk($reportId);
        $dateMonth = $model->date_month;
        $lastMonth = date('Y-m-d', strtotime("-1 day",strtotime($dateMonth)));
        $preModel = BalanceReport::model()->find('date_month = :dm',[':dm' => $lastMonth]);
        if ( !empty($preModel) ) {
            $prebalanceFileInfo = FileRepo::model()->findByPk($preModel->mdata->balance_file_id);
            if ( !empty($prebalanceFileInfo) ) {
                $xls = new oExcel;
                $xls->load($prebalanceFileInfo->getFile());
                $rows = $xls->getAll();
                $rowIndex = 0;
                $specRowStarted = 0;
                foreach ($rows as $row) {
                    $rowIndex++;
                    if ($row[1] == 'Total Balance By Customer') {
                        $specRowStarted = $rowIndex + 1;
                    }
                    if ( $specRowStarted > 0 && $rowIndex > $specRowStarted) {
                        $prevBalanceByCustomer[$row[1]] = array(
                            'name' => $row[2],
                            'balance' => $row[3]
                        );
                    }
                }
                unset($xls);
            }
        }

        // make up all balance movement data for each customer
        $allCustomers = array();
        foreach ( $prevBalanceByCustomer as $cid => $data ) {
            $allCustomers[$cid] = array(
                'name' => $data['name'],
                'invoice' => isset($invoiceByCustomer[$cid]) ? $invoiceByCustomer[$cid] : 0,
                'payment' => isset($paymentByCustomer[$cid]) ? $paymentByCustomer[$cid] : 0,
                'ob' => $data['balance'],
                'cb' => 0
            );
        }
        foreach ( $balanceByCustomer as $cid => $data ) {
            if ( !isset($allCustomers[$cid]) ) {
                $allCustomers[$cid] = array(
                    'name' => $data['name'],
                    'invoice' => isset($invoiceByCustomer[$cid]) ? $invoiceByCustomer[$cid] : 0,
                    'payment' => isset($paymentByCustomer[$cid]) ? $paymentByCustomer[$cid] : 0,
                    'ob' => 0,
                    'cb' => $data['balance']
                );
            } else {
                $allCustomers[$cid]['cb'] = $data['balance'];
            }
        }
        $xls = new oExcel;
        $i = 1;
        $xls->addRow($i++, ['ID', 'Name','Open Balance', 'Invoice', 'Payment', 'Close Balance']);
        foreach($allCustomers as $cid => $rs){
            $xls->addRow($i++, [$cid, $rs['name'],$rs['ob'],$rs['invoice'],$rs['payment'],$rs['cb']]);
        }
        $xls->output($fullPathFile,'',false);

        // save last month balance details file
        $filerepoId = FileRepo::storeFile($fullPathFile, $filename,59,$reportId);
        unset($xls);

        // delete origin file
        unlink($fullPathFile);

        return $filerepoId;
    }

    /**
     * @param $endDate
     * @param $reportId
     * @param $allBalance
     * @return int
     */
    private function backupDatespanBalanceDetails($endDate,$reportId,&$allBalance,&$balanceByCustomer){
        $filename = 'balance_report_details'. $endDate . '.xlsx';
        $fullPathFile = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.$filename;

        // get last month balance details by customer
        $model = new Invoice('search');
        $model->unsetAttributes();
        $ec = new CDbCriteria;
        $ec->condition = "status IN (2,3,7)"; // posted , overdue, and partially paid
        $ec->condition .= ' AND dpt_id = '. Org::PCAE_DEPARTMENT_SYDNEY;
        $ec->condition .= " AND posted <= '" . $endDate . "'";
        $dp = $model->search(false, 0, 't.due ASC', $ec);
        $cus = [];
        foreach($dp->data as $r){
            $cus[$r->to_id][] = $r;
        }
        $xls = new oExcel;
        $i = 1;
        $totalBalance = 0;
        $totalBalanceByCustomer = array();
        //  $allUnlinkedPayment = Payment::getTotalUnallocPaymentAmountList($lastMonthLastDate);
        $allAtaPayment = Payment::getTotalAtaPaymentAmountList($endDate);

        foreach($cus as $cid => $rs){
            $xls->addRow($i++, [$cid, $rs[0]->cust->name]);
            $totalBalanceByCustomer[$cid] = array(
                'name' => $rs[0]->cust->name,
                'balance' => 0
            );
            $xls->addRow($i, ['No', 'Type','AWB', 'Date', 'Due', 'Total', 'Paid', 'Balance']);
            $xls->setFont('A'.$i.':H'.$i, array('bold' => true));
            $i++;
            $tot = [0,0,0];
            $customerTotalBalance = 0;
            foreach($rs as $k => $r){
                $paid = $r->paid();
                $awb='';
                $balance = $r->total - $paid;
                if ( $balance == 0 ) continue;
                if(!empty($r->mdata['awb']) ) $awb = $r->mdata['awb'];
                $xls->addRow($i++, [$r->no, $r->getType(),$awb, $r->date, $r->due,$r->total,$paid,$balance]);
                $tot[0] += $r->total;
                $tot[1] += $paid;
                $tot[2] += $balance;
                unset($r); // release memory in time
                unset($rs[$k]); // release memory in time
            }

            $xls->addRow($i++, ['', '', '','', 'Total:',$tot[0], $tot[1], $tot[2]]);
            $ataAmount = isset($allAtaPayment[$cid]) ? $allAtaPayment[$cid] : 0;
            if ( $ataAmount > 0 ) {
                $xls->addRow($i++, ['', '', '', '', '', '', 'Total Available Payment:', '-' . $ataAmount]);
                unset($allAtaPayment[$cid]);
            }

            $totalBalance += $tot[2];
            $totalBalance -= $ataAmount;
            $customerTotalBalance += $tot[2];
            $customerTotalBalance -= $ataAmount;

            $totalBalanceByCustomer[$cid]['balance'] = $customerTotalBalance;

        }
        $xls->addRow($i++, ['']);

        // for all still available payment but not linked with any outstanding invoices
        foreach ( $allAtaPayment as $oid => $amount ) {
            $org = Org::model()->findByPk($oid);
            $xls->addRow($i++, [$oid, $org->name]);
            $xls->addRow($i++, ['', '', '', '', '', '', 'Total Available Payment:', '-' . $amount]);
            $totalBalance -= $amount;
            $totalBalanceByCustomer[$oid] = array(
                'name' =>  $org->name,
                'balance' => '-' . $amount
            );
        }

        $xls->addRow($i++, ['', '', '','','','', 'Total Balance:', $totalBalance]);


        $xls->addRow($i++, ['']);
        $xls->addRow($i++, ['']);
        $xls->addRow($i++, ['Total Balance By Customer']);
        $xls->addRow($i++, ['ID','Name','Total Balance']);
        foreach ( $totalBalanceByCustomer as $oid => $data ) {
            $xls->addRow($i++, [$oid,$data['name'],$data['balance']]);
        }

        $xls->output($fullPathFile,'',false);

        // save last month balance details file
        $filerepoId = FileRepo::storeFile($fullPathFile, $filename,56,$reportId);
        unset($xls);

        // delete origin file
        unlink($fullPathFile);

        $allBalance = $totalBalance;
        $balanceByCustomer = $totalBalanceByCustomer;

        // temporary save an aging report at this time for testing only
        //$this->saveAgingReport();

        return $filerepoId;

    }

    /**
     * save aging report as local only for testing currently
     */
    private function saveAgingReport(){

        $model=new Invoice('search');
        $model->unsetAttributes();
        $ec = new CDbCriteria;
        $ec->condition = "status IN (2,3,7)";
        $ec->condition .= ' AND dpt_id = '. Org::PCAE_DEPARTMENT_SYDNEY;
        $ec->condition .= " AND posted <= '" . date('Y-m-d') . "'";
        $dp = $model->search(false, 0, 't.due ASC', $ec);
        $cus = [];
        foreach($dp->data as $r){
            $cus[$r->dpt_id][$r->to_id][] = $r;
        }

        $xls = new oExcel;
        $i = 1;
        $showCredits = true;
        if ( $showCredits ) {
            $allAtaPayment = Payment::getTotalAtaPaymentAmountList(date('Y-m-d'));
            $xls->addRow($i++, array('Agent', 'Agent ID', '7 Days', '15 Days', '30 Days', '60 Days', '90 Days', '> 90 Days', 'Credits', 'Total'));
            $xls->setColWidth(array(25,10,15,15,15,15,15,15,15,20));
        } else {
            $xls->addRow($i++, array('Agent', 'Agent ID', '7 Days', '15 Days', '30 Days', '60 Days', '90 Days', '> 90 Days', 'Total'));
            $xls->setColWidth(array(25,10,15,15,15,15,15,15,20));
        }

        $data = [];
        foreach($cus as $oid => $cs){
            foreach($cs as $cid => $rs){
                if(!isset($data[$cid])){
                    $data[$cid] = ['', 0,0,0,0,0,0,0];
                }
                foreach($rs as $r){
                    if(empty($data[$cid][0])) $data[$cid][0] = $r->cust->name;
                    $td = ceil((time() - strtotime($r->due)) / 86400);
                    $bal = $r->getBalance();
                    if($td > 90){
                        $data[$cid][6] += $bal;
                    }elseif($td > 60){
                        $data[$cid][5] += $bal;
                    }elseif($td > 30){
                        $data[$cid][4] += $bal;
                    }elseif($td > 15){
                        $data[$cid][3] += $bal;
                    }elseif($td > 7){
                        $data[$cid][2] += $bal;
                    }else{
                        $data[$cid][1] += $bal;
                    }
                    $data[$cid][7] += $bal;
                }
            }
        }

        foreach($data as $cid => $r){
            // get customer all available payment now
            if ( $showCredits ) {
                $alvPayamount = Payment::orgTotalAta($cid);
                $total = $r[7] - $alvPayamount;
                if ( $alvPayamount > 0 ) $alvPayamount = '-' . $alvPayamount;
                else $alvPayamount = '0';
                $xls->addRow($i++, [$r[0], $cid, $r[1], $r[2], $r[3], $r[4], $r[5], $r[6],$alvPayamount, $total]);
                if ( isset($allAtaPayment[$cid]) )  unset($allAtaPayment[$cid]);
            } else {
                $xls->addRow($i++, [$r[0], $cid, $r[1], $r[2], $r[3], $r[4], $r[5], $r[6],$r[7]]);
            }
        }

        if ( $showCredits ) {
            foreach ($allAtaPayment as $oid => $amount) {
                $org = Org::model()->findByPk($oid);
                $xls->addRow($i++, [$org->name, $oid, '', '', '', '', '', '', '', '-' . $amount]);
            }
        }

        $xls->output( Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'debtor_aging_'.date('Ymd').'.xlsx',null,false);

    }

    /**
     * @param $fromDate
     * @param $endDate
     * @param $reportId
     * @return int
     */
    private function backupDatespanOccurringInvoiceDetails($fromDate,$endDate,$reportId,&$invoiceByCustomer){

        if ( empty($fromDate) ) return 0;

        $filename = 'balance_report_occurring_invoice_details'. $endDate . '.xlsx';
        $fullPathFile = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.$filename;

        // get last month balance details by customer
        $model = new Invoice('search');
        $model->unsetAttributes();
        $ec = new CDbCriteria;
        $ec->condition = "status IN (2,3,7,9)"; // posted , overdue, and partially paid
        $ec->condition .= ' AND dpt_id = '. Org::PCAE_DEPARTMENT_SYDNEY;
        $ec->condition .= " AND posted <= '" . $endDate  . "' AND posted > '" . $fromDate  . "'";
        $dp = $model->search(false, 0, 't.id DESC', $ec);
        $cus = [];
        foreach($dp->data as $r){
            $cus[$r->to_id][] = $r;
        }
        $xls = new oExcel;
        $i = 1;
        $total = 0;
        foreach($cus as $cid => $rs){
            $xls->addRow($i++, [$cid, $rs[0]->cust->name]);
            $xls->addRow($i, ['No', 'Type','AWB', 'Date', 'Due', 'Total']);
            $xls->setFont('A'.$i.':H'.$i, array('bold' => true));
            $i++;
            $tot = 0;
            foreach($rs as $k => $r){

                $awb='';
                if(!empty($r->mdata['awb']) ) $awb = $r->mdata['awb'];
                $xls->addRow($i++, [$r->no, $r->getType(),$awb, $r->posted, $r->due, $r->total]);

                $tot += $r->total;
                unset($r); // release memory in time
                unset($rs[$k]); // release memory in time
            }
            $xls->addRow($i++, ['', '', '','', 'Total:',$tot]);
            $total += $tot;
            $invoiceByCustomer[$cid] = $tot;
        }
        $xls->addRow($i++, ['']);
        $xls->addRow($i, ['', '', '','', 'Total Amount:', $total]);
        $xls->output($fullPathFile,'',false);

        // save last month balance details file
        $filerepoId = FileRepo::storeFile($fullPathFile, $filename,57,$reportId);
        unset($xls);

        // delete origin file
        unlink($fullPathFile);

        return $filerepoId;

    }

    /**
     * @param $fromDate
     * @param $endDate
     * @param $reportId
     * @param $deleteAmount
     * @return int
     */
    private function backupDatespanOccurringPaymentDetails($fromDate,$endDate,$reportId,&$deleteAmount,&$paymentByCustomer){

        if ( empty($fromDate) ) return 0;
        $filename = 'balance_report_occurring_payment_details'. $endDate . '.xlsx';
        $fullPathFile = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.$filename;

        // get last month balance details by customer
        $model = new Payment('search');
        $model->unsetAttributes();
        $ec = new CDbCriteria;
        $ec->condition = "status IN (1,6,9)";
        $ec->condition .= " AND transaction_date <= '" . $endDate . "' AND transaction_date > '" . $fromDate . "'";
        $dp = $model->search(false, 0, $ec);
        $cus = [];
        foreach($dp->data as $r){
            $cus[$r->org_id][] = $r;
        }
        $xls = new oExcel;
        $i = 1;
        $total = 0;
        foreach($cus as $cid => $rs){
            $xls->addRow($i++, [$cid, $rs[0]->cust->name]);
            $xls->addRow($i, [ 'Date','Transaction Date','Type','Status','Amount']);
            $xls->setFont('A'.$i.':H'.$i, array('bold' => true));
            $i++;
            $tot = 0;
            foreach($rs as $k => $r){
                $tot += $r->amount;

                // get currently deleted invoices
                if ( $r->status == Payment::PAYMENT_STATUS_DELETED ) {
                    $deleteAmount += $r->amount;
                }

                $xls->addRow($i++, [$r->date, $r->transaction_date,$r->getType(),$r->getStatus(), $r->amount]);
                unset($r); // release memory in time
                unset($rs[$k]); // release memory in time
            }
            $xls->addRow($i++, ['', '', '','Total:', $tot]);
            $total += $tot;
            $paymentByCustomer[$cid] = $tot;
        }


        $xls->addRow($i++, ['']);
        $xls->addRow($i, ['','','', 'Total Amount:',$total]);
        $xls->output($fullPathFile,'',false);

        // save last month balance details file
        $filerepoId = FileRepo::storeFile($fullPathFile, $filename,58,$reportId);
        unset($xls);

        // delete origin file
        unlink($fullPathFile);

        return $filerepoId;

    }

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('created_date',$this->created_date,true);
		$criteria->compare('date_month',$this->date_month,true);
		$criteria->compare('last_balance',$this->last_balance,true);
		$criteria->compare('current_invoice',$this->current_invoice,true);
		$criteria->compare('current_payment',$this->current_payment,true);
		$criteria->compare('current_balance',$this->current_balance,true);
		$criteria->compare('note',$this->note,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder'=> 't.id DESC',
            ),
            'pagination'=>array(
                'pageSize'=> 30,
            )
		));
	}

    public function afterFind(){
        if(empty($this->mdata)) $this->mdata = json_decode($this->note);
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BalanceReport the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
