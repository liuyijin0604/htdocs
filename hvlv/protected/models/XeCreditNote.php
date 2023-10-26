<?php
/*
 * @author Iain Gray <igray@itgassociates.com>
 * @copyright Copyright &copy; Iain Gray 2013-
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 * @package yii-xero
 */ 

class XeCreditNote extends XeRecord{


    /**
     * ACCPAYCREDIT	An Accounts Payable(supplier) Credit Note
     * ACCRECCREDIT	An Account Receivable(customer) Credit Note
     */
    const TYPE_PAYABLE = "ACCPAYCREDIT";
    const TYPE_RECEIVABLE = "ACCRECCREDIT";

    /**
     * SUBMITTED	An expense claim has been submitted for approval (default)
     * AUTHORISED	An expense claim has been authorised for payment
     * PAID	An expense claim has been paid
     */
    const ST_SUBMITTED = "SUBMITTED";
    const ST_AUTHORISED = "AUTHORISED";
    const ST_PAID = "PAID";

    /**
     * @var string
     */
    public $creditNoteID;

    public $type;
    public $contact; // XeContact
    public $date;
    public $status;
    public $lineAmountTypes;
    public $lineItems;
    public $subTotal;
    public $totalTax;
    public $total;
    public $appliedAmount;
    public $updateDateUTC;
    public $currencyCode;
    public $fullyPaidOnDate;
    public $creditNoteNumber;
    public $reference;
    public $sendToContact;
    public $currencyRate;
    public $remainingCredit;
    public $allocations;
    public $bandingThemeID;
    public $hasAttachments;
    public $payments;

    // for response data
    public $hasValidationErrors;
    public $statusAttributeString;

    public function init(){
        parent::init();

        // TODO soon just hacky now
        // because when load object from xml object we need related model class loaded
        include_once(Yii::app()->basePath.DIRECTORY_SEPARATOR .'models/XeAllocation.php');
        include_once(Yii::app()->basePath.DIRECTORY_SEPARATOR .'models/XeAllocations.php');
        include_once(Yii::app()->basePath.DIRECTORY_SEPARATOR .'models/XeInvoice.php');
        include_once(Yii::app()->basePath.DIRECTORY_SEPARATOR .'models/XeContact.php');
    }


    /**
     * @return array
     */
    public function collections()
    {
        return array(
            'lineItems' => 'XeLineItem',
            'allocations' => 'XeAllocation'
        );
    }

    public function rules()
    {
        return array(
            array('type', 'required'),
            array('type', 'in', 'range'=>array(self::TYPE_PAYABLE, self::TYPE_RECEIVABLE)), //credit note types
            array('lineAmountTypes', 'in', 'range'=>array('Exclusive', 'Inclusive', 'NoTax')),
            array('status', 'in', 'range'=> array(self::ST_AUTHORISED, self::ST_SUBMITTED, self::ST_PAID)), // credit note statuses
        );
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->creditNoteID;
    }


    /**
     * Adds a lineitem with the min. required info.
     * @param $description String
     * @param $quantity Float
     * @param $unitAmount Float
     * @param $accountCode Int , default 200 for sales
     * @param $taxType String
     */
    public function addLineItem( &$data)
    {
        $item = new XeLineItem();
        $item->description = $data['description'];
        $item->quantity = $data['quantity'];
        $item->unitAmount = $data['unitAmount'];

        // set default account code if not existing
        $item->accountCode = '5010.00.00';
        if ( !empty($data['accountCode']) ) $item->accountCode = $data['accountCode'];

        // refer to :
        // https://developer.xero.com/documentation/api/types/#InvoiceTypes
        $item->taxType = 'EXEMPTEXPORT';
        if ( !empty($data['taxType']) )  $item->taxType = $data['taxType'];

        // add tracking option if needed
        if ( !empty($data['trackingName']) && !empty($data['trackingValue']) ) {

            foreach ( $data['trackingName'] as $k => $v ) {
                if ( isset($data['trackingValue'][$k] ) ) {
                    $trackingCategory = new XeTrackingCategory();
                    $trackingCategory->name = $v;
                    $trackingCategory->option = $data['trackingValue'][$k];
                    $item->tracking->add($trackingCategory);
                }
            }
        }

        $this->lineItems->add($item);
    }

    /**
     * @param $allocs
     * @return bool
     */
    public function saveAllocate($allocs){
        $xmlData = '<?xml version="1.0"?><Allocations>';
        foreach ( $allocs as $alloc ) {
            $xmlData .= $alloc->getXmlData();
        }
        $xmlData .= '</Allocations>';

        $id = $this->creditNoteID . '/Allocations';
        $result =  Yii::app()->xero->apiPost($this->_endPoint,$xmlData, $id,'POST');
        if ( !is_null($result) )
        {
            $jsonData = json_decode($result);
            $this->setData($jsonData->{$this->endPoint}[0]);
            return true;
        }
        return false;
    }
}