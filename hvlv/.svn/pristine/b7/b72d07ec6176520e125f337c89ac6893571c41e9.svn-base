<?php
/**
 * Class XeInvoices
 *
 * @author Michael Yue
 * @copyright Copyright &copy; Michael Yue 2016-
 * @package yii-xero
 * @link http://developer.xero.com/api/invoices/
 *
 */

class XeInvoices extends XeRecord {

    public $id;
    public $status;
    public $providerName;
    public $dateTimeUtc;


    /**
     * Array of invoice
     * @var array
     */
    public $invoices;


    /**
     * @return array
     */
    public function collections()
    {
        return array(
            'invoices' => 'XeInvoice'
        );
    }

    /**
     * Gets the current invoices ID
     * @return string
     */
    public function getId(){
        return $this->id;
    }

}