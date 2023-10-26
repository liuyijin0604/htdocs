<?php
/**
 * Class XeContactPersons
 *
 * @author Michael Yue
 * @copyright Copyright &copy; Michael Yue 2016-
 * @package yii-xero
 * @link http://developer.xero.com/api/invoices/
 *
 */

class XeContactPersons extends XeRecord {

    /**
     * Array of Contact
     * @var array
     */
    public $contactpersons;


    /**
     * @return array
     */
    public function collections()
    {
        return array(
            'contactpersons' => 'XeContactPerson'
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