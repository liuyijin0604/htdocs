<?php

/**
 * This is the model class for table "wms_prod_pack".
 *
 * The followings are the available columns in table 'wms_prod_pack':
 * @property string $id
 * @property integer $prod_id
 * @property integer $type
 * @property double $qty
 * @property string $barcode
 * @property double $cbm
 * @property string $dim
 * @property double $weight
 */
class WmsProdPack extends CActiveRecord
{
	public static $types = array(
		self::TYPE_CARTON => 'Carton',
		20 => 'Pallet',
		30 => 'Bag',
		50 => 'Barrow',
	);
	const TYPE_CARTON = 10;
	const TYPE_BAG = 30;

	public $dims = array();

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_prod_pack';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('prod_id, type, qty', 'required'),
			array('barcode, cbm, dim, weight', 'safe'),
			array('prod_id, type', 'numerical', 'integerOnly'=>true),
			array('qty, cbm, weight', 'numerical'),
			array('barcode', 'length', 'max'=>50),
			array('barcode', 'unique', 'message' => 'Barcode Already exists'),
			array('dim', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, prod_id, type, qty, barcode, cbm, dim, weight', 'safe', 'on'=>'search'),
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
			'prod' => array(self::BELONGS_TO, 'WmsProd', 'prod_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'prod_id' => 'Prod',
			'type' => 'Type',
			'qty' => 'Qty',
			'barcode' => 'Barcode',
			'cbm' => 'Cbm',
			'dim' => 'Dim',
			'weight' => 'Weight',
		);
	}
	
	public function getType(){
		return Yii::t(strtolower(__CLASS__), empty(self::$types[$this->type])? '' : self::$types[$this->type]);
	}
	
	public function beforeSave(){
		if(!empty($this->dims)){
			$this->dim = json_encode($this->dims);
			if(empty($this->cbm)) $this->cbm = floatval($this->dims['w']) * floatval($this->dims['h']) * floatval($this->dims['d']);
		}

		$o = self::model()->findByPk($this->id);
		if (!empty($o) && !empty($o->barcode) && $this->barcode != $o->barcode && $this->prod->ifHasStock()) {
			$this->addError('id', 'Still have inventory of this product, cannot change Barcode');
			return false;
		}

		return true;
	}

	public function showDim(){
		return empty($this->dims)? '' : 'W'.$this->dims['w'].' H'.$this->dims['h'].' D'.$this->dims['d'];
	}

	public function getCBM(){
		$cbm = 0;
		if(!empty($this->dims)) $cbm = $this->dims['w'] * $this->dims['h'] * $this->dims['d'] / 1000000;
		//if(!empty($this->weight)) $cbm = max($cbm, $this->weight / 167);
		return $cbm;
	}
	
	public function afterFind(){
		if(!empty($this->dim)) $this->dims = json_decode($this->dim, true);
		return true;
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('prod_id',$this->prod_id);
		$criteria->compare('type',$this->type);
		$criteria->compare('qty',$this->qty);
		$criteria->compare('barcode',$this->barcode,true);
		$criteria->compare('cbm',$this->cbm);
		$criteria->compare('dim',$this->dim,true);
		$criteria->compare('weight',$this->weight);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WmsProdPack the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
