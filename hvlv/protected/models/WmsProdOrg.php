<?php

/**
 * This is the model class for table "wms_prod_org".
 *
 * The followings are the available columns in table 'wms_prod_org':
 * @property string $prod_id
 * @property string $org_id
 * @property string $sku
 * @property string $meta
 */
class WmsProdOrg extends CActiveRecord
{

	public $mdata, $cust_name;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_prod_org';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('prod_id, org_id', 'required'),
			array('sku, meta', 'safe'),
			array('prod_id, org_id', 'length', 'max'=>11),
			array('sku', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('prod_id, org_id, sku, meta', 'safe', 'on'=>'search'),
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
			'customer' => array(self::BELONGS_TO, 'Org', 'org_id'),
			'prod' => array(self::BELONGS_TO, 'WmsProd', 'prod_id'),
			'stocks' => array(self::HAS_MANY, 'WmsStock', ['org_id' => 'org_id', 'prod_id' => 'prod_id']),
		);
	}
	
	public function beforeSave(){
		if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);

		$o = self::model()->find('prod_id = :prod_id AND org_id = :org_id', [':prod_id' => $this->prod_id, ':org_id' => $this->org_id]);
		if (!empty($o) && !empty($o->sku) && $this->sku != $o->sku && $this->prod->ifHasStock($this->org_id)) {
			$this->addError('id', 'Still have inventory of this product, cannot change SKU');
			return false;
		}

		return true;
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return true;
	}

	public static function ExpByMonth($pid, $oid){
		$r = self::model()->find('prod_id = :pid AND org_id = :oid', [':pid' => $pid, ':oid' => $oid]);
		// return empty($r) || empty($r->mdata['exp_acc']) || $r->mdata['exp_acc'] == 'Month';
		return @$r->mdata['exp_acc'] == 'Month';
	}

	public static function ExpBatMgmt($pid, $oid){
		$r = self::model()->find('prod_id = :pid AND org_id = :oid', [':pid' => $pid, ':oid' => $oid]);
		return empty($r) || empty($r->mdata['exp_bat'])? 3 : $r->mdata['exp_bat'];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'prod_id' => 'Prod',
			'org_id' => 'Org',
			'sku' => 'SKU',
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
	public function search($pgn=true, $ps = 30, $ec = false, $order = 'customer.name DESC')
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('prod_id',$this->prod_id);
		$criteria->compare('org_id',$this->org_id);
		$criteria->compare('sku',$this->sku,true);

		$with = ['customer'];
		if(!empty($this->cust_name)){
			$criteria->compare('customer.name',$this->cust_name,true);
		}

		$criteria->with = array_unique($with);
		$criteria->together = true;

		if($ec) $criteria->mergeWith($ec);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>$order,
			),
			'pagination'=> $pgn? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WmsProdOrg the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
