<?php

class XeManualJournal extends XeRecord {


    // line amount type definitions
    // refer to :
    // https://developer.xero.com/documentation/api/types#LineAmountTypes
    const LINE_AMOUNT_TYPE_EXCLUSIVE = 'Exclusive'; // Line items are exclusive of tax
    const LINE_AMOUNT_TYPE_INCLUSIVE = 'Inclusive'; // Line items are inclusive
    const LINE_AMOUNT_TYPE_NOTAX = 'NoTax'; // Line have no tax

    // manual journal status codes
    // refer to :
    // https://developer.xero.com/documentation/api/types#ManualJournalStatuses
    const MANUAL_JOURNAL_STATUS_DRAFT = 'DRAFT'; // A Draft ManualJournal (default)
    const MANUAL_JOURNAL_STATUS_POSTED = 'POSTED'; // A Posted ManualJournal
    const MANUAL_JOURNAL_STATUS_DELETED = 'DELETED'; // A Deleted Draft ManualJournal
    const MANUAL_JOURNAL_STATUS_VOIDED = 'VOIDED'; // A Voided Posted ManualJournal


    /**
     * @var string
     */
    public $manualJournalID;
    public $date;
    public $lineAmountTypes;
    public $narration;
    public $journalLines;
    public $url;
    public $showOnCashBasisReports;
    public $hasAttachements;


    // for response data
    public $hasValidationErrors;
    public $statusAttributeString;

    /**
     * @param string $type Line Amounts Type
     * @param string $scenario
     * @throws CException
     */
    public function __construct( $type = self::LINE_AMOUNT_TYPE_NOTAX,  $scenario = '')
    {
        if ( $type )
        {
            if ( !in_array($type, array(
                self::LINE_AMOUNT_TYPE_EXCLUSIVE,
                self::LINE_AMOUNT_TYPE_INCLUSIVE,
                self::LINE_AMOUNT_TYPE_NOTAX)) ) {
                throw new CException(Yii::t('yii-xero', 'Invalid Type {type}', array('{type}' => $type)));
            }

            $this->lineAmountTypes = $type;
        }
        $this->status = self::MANUAL_JOURNAL_STATUS_POSTED;

        parent::__construct($scenario);
    }

    /**
     * @return array
     */
    public function collections()
    {
        return array(
            'journalLines' => 'XeJournalLine'
        );
    }

    /**
     * Validation rules
     * @return array
     * TODO add for certain scenarios
     */
    public function rules()
    {
        return array(

            array('lineAmountTypes', 'in', 'range'=>array(self::LINE_AMOUNT_TYPE_EXCLUSIVE,
                self::LINE_AMOUNT_TYPE_INCLUSIVE,
                self::LINE_AMOUNT_TYPE_NOTAX)),

            array('status', 'in', 'range'=> array (
                self::MANUAL_JOURNAL_STATUS_DRAFT,
                self::MANUAL_JOURNAL_STATUS_POSTED,
                self::MANUAL_JOURNAL_STATUS_DELETED,
                self::MANUAL_JOURNAL_STATUS_VOIDED)),
        );
    }



    /**
     * Gets the current manual journal ID
     * @return string
     */
    public function getId(){
        return $this->journalID;
    }


    public function addLineItem( &$data)
    {
        $item = new XeJournalLine();
        $item->description = $data['description'];
        $item->lineAmount = $data['lineAmount'];

        // set default account code - for sales
        $item->accountCode = 200;
        if ( !empty($data['accountCode']) ) $item->accountCode = $data['accountCode'];

        // refer to :
        // https://developer.xero.com/documentation/api/types/#title23
        $item->taxType = 'BASEXCLUDED';
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

        $this->journalLines->add($item);
    }

}