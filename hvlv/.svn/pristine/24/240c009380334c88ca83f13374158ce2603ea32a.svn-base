<?php

class XeAllocations extends XeRecord {

    /**
     * Array of allocations
     * @var array
     */
    public $allocations;


    /**
     * @return array
     */
    public function collections()
    {
        return array(
            'allocations' => 'XeAllocation'
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
    }

}