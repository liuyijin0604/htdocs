<?php

class XeCreditNotes extends XeRecord {

    public $id;
    public $status;
    public $providerName;
    public $dateTimeUtc;


    /**
     * Array of payments
     * @var array
     */
    public $creditnotes;


    /**
     * @return array
     */
    public function collections()
    {
        return array(
            'creditnotes' => 'XeCreditNote'
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
        $this->_endPoint = 'CreditNotes';
        include_once(Yii::app()->basePath.DIRECTORY_SEPARATOR .'models/XeCreditNote.php');
    }

}