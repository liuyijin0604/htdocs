<?php

/**
 * This is the model class for table "cogs_line".
 *
 * The followings are the available columns in table 'cogs_line':
 * @property string $id
 * @property integer $type
 * @property string $charge_code
 * @property integer $status
 * @property string $org_id
 * @property integer $fid
 * @property string $model
 * @property string $created
 * @property integer $dpmt
 * @property string $gst
 * @property string $desc
 * @property integer $qty
 * @property string $price
 * @property string $dpt_id
 * @property integer $currency
 * @property string $actual_amount
 * @property string $accrual_amount
 * @property string $accrual_gst_amount
 * @property string $actual_gst_amount
 * @property string $meta
 */
class CogsLine extends MetaModel
{
	public $consol_id = 0;
	public static $types = array(
		'1' => 'Import',
		'2' => 'Export',
		'3' => 'Air/Sea',
		'4' => '3PL',
		'5' => 'Tow Service',
		'6' => 'Others',
	);

	public static $charge_code = array(
		'91031'=>'item cost'
	);

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cogs_line';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('charge_code, status, org_id, fid, model, created, dpmt, gst, qty, price, dpt_id', 'required'),
			array('type, status, fid, dpmt, currency', 'numerical', 'integerOnly'=>true),
			array('charge_code', 'length', 'max'=>20),
			array('org_id, price, dpt_id, actual_amount, accrual_amount, accrual_gst_amount, actual_gst_amount', 'length', 'max'=>10),
			array('model, gst', 'length', 'max'=>45),
			array('desc', 'length', 'max'=>450),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type, charge_code, status, org_id, fid, model, created, dpmt, gst, desc, qty, price, dpt_id, currency, actual_amount, accrual_amount, accrual_gst_amount, actual_gst_amount, meta', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @param $rs
	 * @param $key
	 * @return int
	 */
	public static function getTotal(&$rs, $key)
	{
		$total = 0;
		if (empty($rs)) {
			return $total;
		}
		foreach ($rs as $r) {
			switch ($key) {
				case 'accrual_amount':
					$total +=  self::getByCurrency($r->accrual_amount,$r);
					break;

				case 'actual_amount':
					$total += self::getByCurrency($r->actual_amount,$r);
					break;

				case 'accrual_gst_amount':
					$total += self::getByCurrency($r->accrual_gst_amount,$r);
					break;

				case 'actual_gst_amount':
					$total += self::getByCurrency($r->actual_gst_amount,$r);
					break;
			}
		}
		return $total;
	}

	public static function getByCurrency($value,$r)
	{
		if($r->currency==1)
		{
			return $value;
		}else
		{
			$rate = Currency::getExrate('',$r->currency)[0];
			if (empty($rate)) $rate = 1;
			return round($value / $rate,2);
		}
	}

	public function getCCodeDesc()
	{
		if (!isset($this->charge_code)) {
			return '';
		}

		$desc = '';
		$ccodeModel = Chargecode::model()->find('status = 1 AND code = :ccode', [':ccode' => $this->charge_code]);
		if (!empty($ccodeModel)) {
			$desc = $ccodeModel->name;
		}

		$desc .= '(' . $this->charge_code . ')';
		return $desc;
	}

	public function getToOrgName()
	{
		if (empty($this->to_id)) {
			return 'All';
		} else {
			return empty($this->toOrg->name) ? '' : $this->toOrg->name;
		}
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			"imparcel"=>[self::BELONGS_TO,"ImParcel",'fid','on' => "t.model='ImParcel'"],
			'cust' => array(self::BELONGS_TO, 'Org', 'org_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type' => 'Type',
			'charge_code' => 'Charge Code',
			'status' => 'Status',
			'org_id' => 'Org',
			'fid' => 'Fid',
			'model' => 'Model',
			'created' => 'Created',
			'dpmt' => 'Dpmt',
			'gst' => 'Gst',
			'desc' => 'Desc',
			'qty' => 'Qty',
			'price' => 'Price',
			'dpt_id' => 'Dpt',
			'currency' => 'Currency',
			'actual_amount' => 'Actual Amount',
			'accrual_amount' => 'Accrual Amount',
			'accrual_gst_amount' => 'Accrual Gst Amount',
			'actual_gst_amount' => 'Actual Gst Amount',
			'meta' => 'Meta',
		);
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
	public function search($pgn = true, $ps = 30)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('charge_code',$this->charge_code,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('org_id',$this->org_id,true);
		$criteria->compare('fid',$this->fid);
		$criteria->compare('model',$this->model,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('dpmt',$this->dpmt);
		$criteria->compare('gst',$this->gst,true);
		$criteria->compare('desc',$this->desc,true);
		$criteria->compare('qty',$this->qty);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('dpt_id',$this->dpt_id,true);
		$criteria->compare('currency',$this->currency);
		$criteria->compare('actual_amount',$this->actual_amount,true);
		$criteria->compare('accrual_amount',$this->accrual_amount,true);
		$criteria->compare('accrual_gst_amount',$this->accrual_gst_amount,true);
		$criteria->compare('actual_gst_amount',$this->actual_gst_amount,true);
		$criteria->compare('meta',$this->meta,true);
		if(!empty($this->consol_id))
		{
			$criteria->with=['imparcel'];
			$criteria->addCondition(' (fid = '.$this->consol_id.' and model in ("ImcoConsol","DmawbConsol","ElmsConsol")) or imparcel.consol_id = '.$this->consol_id);


		}
		$pagerparams = $_GET;

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => $pgn ? [
				'pageSize' => $ps,
				'params' => $pagerparams,
			] : false,
		));
	}

	public static function updateCogsLine($fid,$cost)
	{
		$c = CogsLine::model()->find('fid = :fid and model="ImParcel" and charge_code="91031"',[":fid"=>$fid]);
		if(empty($c))
		{
			$cogs = new CogsLine();
			$cogs->type = 1;
			$cogs->charge_code = '91031';
			$cogs->status = 1;
			$cogs->org_id = $t->org_id;
			$cogs->fid = $t->pid;
			$cogs->model = 'ImParcel';
			$cogs->created = $t->imparcel->created;
			$cogs->dpmt = 10;
			$cogs->gst = 0;
			$cogs->desc = "";
			$cogs->qty = 1;
			$cogs->price = 0;
			$cogs->dpt_id = $t->shipment->ddpt_id;
			$cogs->currency = 1;
			$cogs->actual_amount = 0;
			$cogs->accrual_amount = $cost;
			$cogs->accrual_gst_amount  = $cost*0.1;
			$cogs->actual_gst_amount = 0;
			$cogs->save();
		}else
		{
			$cosg->accrual_amount = $cost;
			$cogs->accrual_gst_amount  = $cost*0.1;
			$cogs->save();
		}
	}

	public static function updateCogsLineActual($fid,$cost,$myValue=0,$itemCode,$t,$dpmt=10)
	{
		$cogs = CogsLine::model()->find('fid = :fid and model="ImParcel" and charge_code=:charge_code and item_code = :itemCode',[":fid"=>$fid,":itemCode"=>$itemCode,":charge_code"=>Invoice::$delivery_charge_code[$dpmt]]);
		if($itemCode!='item')
		{
			$myValue = $cost;
		}
		if(empty($cogs))
		{
			$cogs = new CogsLine();
			$cogs->type = 1;
			$cogs->charge_code = Invoice::$delivery_charge_code[$dpmt];
			$cogs->status = 1;
			$cogs->org_id = $t->parent->org_id;
			$cogs->fid = $t->fid;
			$cogs->model = 'ImParcel';
			$cogs->created = $t->imparcel->created;
			$cogs->dpmt = $dpmt;
			$cogs->gst = Invoice::$InvoiceCostTaxRateSimple['free'];
			$cogs->desc = "";
			$cogs->qty = 1;
			$cogs->price = 0;
			$cogs->dpt_id = $t->imparcel->ddpt_id;
			$cogs->currency = 1;
			$cogs->actual_amount = $cost;
			$cogs->accrual_amount = $myValue;
			$cogs->accrual_gst_amount  = 0;
			$cogs->actual_gst_amount = 0;
			$cogs->item_code = $itemCode;
			$cogs->save();
		}else
		{
			$cogs->dpmt = $dpmt;
			$cogs->charge_code = Invoice::$delivery_charge_code[$dpmt];
			$cogs->actual_amount = $cost;
			//$cogs->actual_gst_amount  = $cost*0.1;
			if(!empty($myValue))
			{
				$cogs->accrual_amount = $myValue;
				//$cogs->accrual_gst_amount  = $myValue*0.1;
			}
			$cogs->save();
		}
	}

	public function getRef()
	{
		if($this->model=='ImParcel')
		{
			return $this->imparcel->ref;
		}else
		{
			return $this->model::model()->findByPk($this->fid)->no;
		}
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CogsLine the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
