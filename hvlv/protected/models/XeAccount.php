<?php

class XeAccount extends XeRecord {


    public $accountID;
    public $code;
    public $name;
    public $type;
    public $backAccountNumber;
    public $status;
    public $description;
    public $taxType;
    public $enablePaymentsToAccount;
    public $showExpenseClaims;
    public $class;
    public $systemAccount;
    public $bankAccountType;
    public $currencyCode;
    public $reportingCode;
    public $reportingCodeName;
    public $hasAttachments;
    public $updatedDateUTC;


    public function collections()
    {
        return array(
           //'addresses' => 'XeAddress',
         //  'phones' => 'XePhone',
        );
    }


    /**
     * @return string
     */
    public function getId()
    {
        return $this->accountID;
    }


    /**
     * Validation rules for Contact
     * @return array
     */
    public function rules()
    {
        return array(
        //    array('name, emailAddress', 'length', 'max'=>500),
         //   array('firstName, lastName', 'length', 'max'=>255),
         //   array('taxNumber', 'length', 'max'=>50),
        //   array('emailAddress', 'email'),
         //   array('name, firstName, lastName, emailAddress, skypeUserName, bankAccountDetails, taxNumber, accountsReceivableTaxType, accountsPayableTaxType, defaultCurrency', 'safe' )
        );
    }


}