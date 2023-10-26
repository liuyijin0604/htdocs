<?php

/**
 * This is the model class for table "inv_line".
 *
 * The followings are the available columns in table 'inv_line':
 * @property string $id
 * @property string $inv_id
 * @property string $model
 * @property string $fid
 * @property string $ccode
 * @property string $amount
 * @property string $gst
 * @property string $det
 * @property double $qty
 * @property string $tax
 * @property string $meta
 * @property string $rebate
 */
class InvLine extends oActiveRecord{

	public $mdata = [];
	public $pid;

	const WEIGHTCCODE = 'WEIGHT DIFF';
	const SURCHARGECCODE = 'SURCHARGE';
	const WEIGHTCCODEAUTO = 'WEIGHTDIFFAUTO';
	const SURCHARGECODEARR = ["MHP"=>'Manual Handling Fee',"RED"=>'Second Delivery Fee',"OS"=>'Oversize Fee',"OS0"=>'Oversize Fee 0',"OS1"=>'Oversize Fee 1',"SD0"=>"Second Delivery Fee","SD1"=>"Second Delivery Fee","MHR"=>'Manual Handling Fee','Tailgate'=>'Tailgate',"MH"=>'Manual Handling Fee',"Onforwarding"=>"Onforwarding","Remote"=>"Remote","Redelivery"=>"Redelivery","Book"=>"Book","RZ"=>'Remote',"Q1"=>'Onforwarding',"MI"=>'Manual Handling Fee',"MO"=>'Oversize Fee',"TG"=>"Tailgate","FR"=>'Redelivery'];
	static public $ccodes = [];


	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'inv_line';
	}

	// for TLA
	public function getDbConnection(){
		//return isset($this->inv_id) && $this->inv_id >= 500000? self::getTlaConnection() : parent::getDbConnection();
		//2022-06-17 
		return self::getTlaConnection();
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('inv_id', 'required'),
			array('model, fid, ccode, amount, gst, det, qty, tax, meta', 'safe'),
			array('qty', 'numerical'),
			array('inv_id, fid', 'length', 'max'=>11),
			array('model', 'length', 'max'=>50),
			array('amount,gst', 'length', 'max'=>10),
			array('det,linkto', 'length', 'max'=>300),
			array('tax', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, inv_id, model, fid, ccode, amount, gst, det, qty, tax, meta, linkto', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'invoice' => array(self::BELONGS_TO, 'Invoice', 'inv_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'inv_id' => 'Inv',
			'model' => 'Model',
			'fid' => 'Fid',
			'amount' => 'Amount',
			'det' => 'Detail',
			'ccode' => 'Charge Code',
			'qty' => 'Qty',
			'tax' => 'Tax',
			'gst' => 'GST',
			'meta' => 'Meta',
		);
	}
	
	protected function beforeSave(){
		if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);

		if ($this->gst > 0 && empty($this->tax)) {
			$this->tax = 'OUTPUT';
		}

		if (!empty($this->inv_id)) {
			$invoice = Invoice::model()->findByPk($this->inv_id);
			if (!empty($invoice) && in_array($invoice->to_id, Org::$airsea_heshengyuan)) {
				$this->tax = 'EXEMPTOUTPUT';
				$this->amount -= $this->gst;
				$this->gst = 0;
			}
		}

		return true;
	}

	protected function afterSave(){
		if ( !empty($this) ) {
			// push invoice details to p & l ledger table
			$this->syncWithPlLedger();
		}
	}

	/**
	 * once invoice changed or new created
	 * sync all details with P & L ledger table
	 */
	private function syncWithPlLedger(){
		if ( !isset($this->invoice) ) return;
		switch ( $this->invoice->type ) {
			case Invoice::INVOICE_TYPE_IMPORT: {
				$this->syncWithPlLedgerForImport();
			}
				break;
			default:
				break;
		}
	}

	/**
	 * sync with P & L ledger table
	 */
	private function syncWithPlLedgerForImport(){
	}

	protected function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
	}

	public function mm(){
		$r = new $this->model;
		return $r::model()->findByPk($this->fid);
	}

	public function hasPosted(){
		return InvLine::model()->with('invoice')->count('invoice.status NOT IN (1,10) AND t.model = :m AND t.fid = :fid and t.id != :id', [':m' => $this->model, ':fid' => $this->fid, ':id' => $this->id]) > 0;
	}

	/**
	 * get tax related type string
	 * @return string
	 */
	public function getTaxType(){
		if ( !isset(Invoice::$InvoiceTaxRate[$this->tax])) return '';
		return Yii::t(strtolower(__CLASS__), Invoice::$InvoiceTaxRate[$this->tax]);
	}

	/**
	 * get charge code related description
	 * @return string
	 */
	public function getCCodeDesc(){
		$desc = $this->ccode;
		$ccodeModel = Chargecode::model()->find('status = 1 AND code = :ccode',[':ccode' => $this->ccode]);
		return $desc . ':' . $ccodeModel->name;
	}

	/**
	 * get charge type related description
	 * @return string
	 */
	public function getCCodeTypeDesc()
	{
		$desc = $this->ccode;
		$ccodeModel = ChargeItemType::model()->find('code = :ccode', [':ccode' => $this->ccode]);
		return @$ccodeModel->name;
	}

	public function getLinktoCount(){
		if(!empty($this->linkto)){
			$arrTask = explode(';', $this->linkto);
			if(!empty($arrTask)){
				return count($arrTask);
			}
			return 1;
		}
		return 0;
	}

	public function getRelatedSupplyCost(){
		$numSupplierCost = 0;
		if(!empty($this->linkto)){
			$arrTasks = explode(';', $this->linkto);
			if(!empty($arrTasks)){
				foreach($arrTasks as $arrTask){
					$objShipment = ImParcel::model()->find('ref = :ref or hbn = :hbn and status != 100',[':ref'=>$arrTask,':hbn'=>$arrTask]);
					if(!empty($objShipment)){
						$strHbn = $objShipment->hbn;
						[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
						$modelSupplierInvLines = SiReconcileLine::model()->findAll('ref = :ref and confirm_status > 0', [':ref' => $strHbn]);
						if (!empty($modelSupplierInvLines)) {
							foreach ($modelSupplierInvLines as $modelSupplierInvLine) {
								if (preg_match('/item|UNLOADING|WAITING/i', $modelSupplierInvLine->item_code)) {
									$numSupplierCost += $modelSupplierInvLine->value;
								}
							}
						}
						Yii::app()->name = $app_name;
					}
				}
			}
		}
		return $numSupplierCost;
	}

	/**
	 * get gst flag bases on tax type
	 */
	public function hasGstByTaxValue($taxType = Invoice::INVOICE_TAX_TYPE_INCLUSIVE){
		// tax type
		//'1' => 'Tax Exclusive',
		// '2' => 'Tax Inclusive',
		// '0' => 'No Tax'

		/*
		 * TAX TYPE	RATE	NAME	SYSTEM DEFINED
		 *   OUTPUT	10.00	GST on Income
		 *   INPUT	10.00	GST on Expenses
		 *   CAPEXINPUT	10.00	GST on Capital	Yes
		 */
		$taxRate = array('INPUT','OUTPUT','CAPEXINPUT');

		switch ( $taxType) {
			case Invoice::INVOICE_TAX_TYPE_EXCLUSIVE:
			{
				if ( in_array($this->tax,$taxRate) ) {
					return true;
				}
				break;
			}
			case Invoice::INVOICE_TAX_TYPE_INCLUSIVE: {
				if ( in_array($this->tax,$taxRate) ) {
					return true;
				}
				break;
			}
		}
		return false;
	}

	public function getDefItem(){
		return Yii::t(strtolower(__CLASS__), Invoice::$DefItems[$this->ccode]);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search($ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('inv_id',$this->inv_id);
		$criteria->compare('model',$this->model,true);
		$criteria->compare('fid',$this->fid);
		$criteria->compare('ccode',$this->ccode,true);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('t.gst',$this->gst,true);
		$criteria->compare('det',$this->det,true);
		$criteria->compare('qty',$this->qty);
		$criteria->compare('tax',$this->tax,true);
		$criteria->compare('meta',$this->meta,true);

		if($ec) $criteria->mergeWith($ec);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return InvLine the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
