<?php

class XeOverPayment extends XeRecord {

    /*
    Prepayments
    Types
    RECEIVE-PREPAYMENT
    SPEND-PREPAYMENT
    */
    const TYPE_RECEIVE_PREPAYMENT = 'RECEIVE-PREPAYMENT';
    const TYPE_SPEND_PREPAYMENT = 'SPEND-PREPAYMENT';

    /*
    Prepayment Status Codes
    AUTHORISED
    PAID
    VOIDED
    */
    const ST_AUTHORISED = 'AUTHORISED';
    const ST_PAID = 'PAID';
    const ST_VOIDED = 'VOIDED';

    /**
     * @var string
     */
    public $prepaymentID;
    public $type;
    public $contact;
    public $date;
    public $status;
    public $lineAmountType;
    public $lineItems;
    public $subTotal;
    public $totalTax;
    public $total;
    public $updateDateUTC;
    public $currencyCode;
    public $currencyRate;
    public $remainingCredit;
    public $allocations;
    public $hasAttachments;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->$prepaymentID;
    }

    /**
     * @return array
     */
    public function collections()
    {
        return array(
            'lineItems' => 'XeLineItem'
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