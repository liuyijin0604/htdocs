<?php

class XePayments extends XeRecord {

    public $id;
    public $status;
    public $providerName;
    public $dateTimeUtc;


    /**
     * Array of payments
     * @var array
     */
    public $payments;


    /**
     * @return array
     */
    public function collections()
    {
        return array(
            'payments' => 'XePayment'
        );
    }

    /**
     * Gets the current invoices ID
     * @return string
     */
    public function getId(){
        return $this->id;
    }

    public function init(){
        parent::init();
        $this->_endPoint = 'Payments';
    }

}