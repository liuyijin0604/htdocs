<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iain
 * Date: 29/05/13
 * Time: 00:17
 * To change this template use File | Settings | File Templates.
 */

/*
 * @author Iain Gray <igray@itgassociates.com>
 * @copyright Copyright &copy; Iain Gray 2013-
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 * @package yii-xero
 */ 

class XePayment extends XeRecord {

    /*
    Payment Types
    ACCRECPAYMENT	Accounts Receivable Payment
    ACCPAYPAYMENT	Accounts Payable Payment
    ARCREDITPAYMENT	Accounts Receivable Credit Payment (Refund)
    APCREDITPAYMENT	Accounts Payable Credit Payment (Refund)
    AROVERPAYMENTPAYMENT	Accounts Receivable Overpayment Payment (Refund)
    ARPREPAYMENTPAYMENT	Accounts Receivable Prepayment Payment (Refund)
    APPREPAYMENTPAYMENT	Accounts Payable Prepayment Payment (Refund)
    APOVERPAYMENTPAYMENT	Accounts Payable Overpayment Payment (Refund)
    */
    const TYPE_ACCRECPAYMENT = 'ACCRECPAYMENT';
    const TYPE_ACCPAYPAYMENT = 'ACCPAYPAYMENT';
    const TYPE_ARCREDITPAYMENT = 'ARCREDITPAYMENT';
    const TYPE_APCREDITPAYMENT = 'APCREDITPAYMENT';
    const TYPE_AROVERPAYMENTPAYMENT = 'AROVERPAYMENTPAYMENT';
    const TYPE_ARPREPAYMENTPAYMENT = 'ARPREPAYMENTPAYMENT';
    const TYPE_APPREPAYMENTPAYMENT = 'APPREPAYMENTPAYMENT';
    const TYPE_APOVERPAYMENTPAYMENT = 'APOVERPAYMENTPAYMENT';

    /**
    Payment Status Codes
    AUTHORISED
    DELETED
     */
    const ST_AUTHORISED = 'AUTHORISED';
    const ST_DELETED = 'DELETED';

    /**
     * @var string
     */
    public $paymentID;
    public $invoice;
    public $account;
    public $bankAmount;
    public $date;
    public $currencyRate;
    public $amount;
    public $reference;
    public $isReconciled;
    public $status;
    public $paymentType;
    public $updateDateUTC;

    // for response data
    public $hasValidationErrors;
    public $statusAttributeString;
   // public $validationErrors;
   // public $warnings;

    public function init(){
        parent::init();

        // TODO soon just hacky now
        // because when load object from xml object we need related model class loaded
        include_once(Yii::app()->basePath.DIRECTORY_SEPARATOR .'models/XeAccount.php');
        include_once(Yii::app()->basePath.DIRECTORY_SEPARATOR .'models/XeContact.php');
        include_once(Yii::app()->basePath.DIRECTORY_SEPARATOR .'models/XeInvoice.php');
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->paymentID;
    }

    public function collections()
    {
        return array(
            'invoice' => 'XeInvoice',
            'account'=> 'XeAccount'
        );
    }

    /**
     * Validation rules
     * @return array
     * TODO add for certain scenarios
     */
    public function rules()
    {
        return array(
           // array('type', 'required'),
           // array('type', 'in', 'range'=>array(self::TYPE_PAYABLE, self::TYPE_RECEIVABLE)), //invoice types
           // array('lineAmountTypes', 'in', 'range'=>array('Exclusive', 'Inclusive', 'NoTax')),
           // array('status', 'in', 'range'=> array(self::ST_AUTHORISED, self::ST_DRAFT, self::ST_SUBMITTED)), //invoice statuses
        );
    }

}