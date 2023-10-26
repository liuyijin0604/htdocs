<?php
/**
 * Class XeCustomer
 *
 * @author Iain Gray <igray@itgassociates.com>
 * @copyright Copyright &copy; Iain Gray 2013-
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 * @package yii-xero
 *
 */

class XeContactPerson extends XeRecord {


    public $firstName;
    public $lastName;
    public $emailAddress;
    public $includeInEmails;


    /**
    /**
     * @return string
     */
    public function getId()
    {
        return $this->Id;
    }


    /**
     * Validation rules for Contact
     * @return array
     */
    public function rules()
    {
        return array(
            array('emailAddress', 'length', 'max'=>500),
            array('firstName, lastName', 'length', 'max'=>255),
            array('firstName, lastName, emailAddress, includeInEmails', 'safe' )
        );
    }

}