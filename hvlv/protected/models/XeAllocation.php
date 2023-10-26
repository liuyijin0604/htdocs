<?php
/*
 * @author Iain Gray <igray@itgassociates.com>
 * @copyright Copyright &copy; Iain Gray 2013-
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 * @package yii-xero
 */ 

class XeAllocation extends XeRecord{

    public $appliedAmount;
    public $amount;
    public $date;
    public $invoice;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }



}