<?php
/**
 * Class XeContacts
 *
 * @author Michael Yue
 * @copyright Copyright &copy; Michael Yue 2016-
 * @package yii-xero
 * @link http://developer.xero.com/api/invoices/
 *
 */

class XeContacts extends XeRecord {

    /**
     * Array of Contact
     * @var array
     */
    public $contacts;


    /**
     * @return array
     */
    public function collections()
    {
        return array(
            'contacts' => 'XeContact'
        );
    }

    /**
     * Gets the current contact ID
     * @return string
     */
    public function getId(){
        return $this->id;
    }

}