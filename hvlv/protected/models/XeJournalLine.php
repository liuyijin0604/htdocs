<?php

class XeJournalLine extends XeModel{

    public $description;
    public $lineAmount;
    public $accountCode;
    public $taxType;
    public $tracking;


    public function __construct( $scenario = '')
    {
        $this->taxType = 'BASEXCLUDED';
        parent::__construct($scenario);
    }

    public function rules()
    {
        return array(
          array('description, lineAmount, taxType, accountCode', 'required'),
        );

    }



    public function collections()
    {
        return array(
            'tracking' => 'XeTrackingCategory',
        );
    }


}