<?php

class XeOverpayments extends XeRecord {

    public $id;
    public $status;
    public $providerName;
    public $dateTimeUtc;


    /**
     * Array of payments
     * @var array
     */
    public $overpayments;


    /**
     * @return array
     */
    public function collections()
    {
        return array(
            'overpayments' => 'XeOverpayment'
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
        $this->_endPoint = 'Overpayments';
    }

}