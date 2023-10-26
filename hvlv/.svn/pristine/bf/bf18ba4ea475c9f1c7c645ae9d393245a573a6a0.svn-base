<?php

/**
 * This is the model class for table "ex_stat".
 *
 * The followings are the available columns in table 'ex_stat':
 * @property string $id
 * @property string $date
 * @property string $org_id
 * @property string $pu_id
 * @property integer $type
 * @property string $qty
 * @property string $wt
 */
class ExStat extends CActiveRecord
{
	public static $types = [
		10 => 'Baby Formula',
		11 => 'Step 1/2',
		12 => 'Step 3/4',
		20 => 'Milk Powder',
		21 => 'Pedisure',
		90 => 'Other',
		91 => 'UGG',
	];

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ex_stat';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, org_id, pu_id, type, qty, wt', 'safe'),
			array('type', 'numerical', 'integerOnly'=>true),
			array('org_id, pu_id, qty, wt', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, date, org_id, pu_id, type, qty, wt', 'safe', 'on'=>'search'),
		);
	}
	
	public function getType(){
		return Yii::t(strtolower(__CLASS__), isset(self::$types[$this->type])? self::$types[$this->type] : 
			'');
	}



	public static function type2color($t, $alpha = 0.5){
		$cs = [
			10 => [255,200,40],
			11 => [130,180,0],
			12 => [255,200,40],
			20 => [0,217,217],
			21 => [200,40,255],
			90 => [150,150,150],
			91 => [0,128,255],
		];
		return isset($cs[$t])? 'rgba('.implode(',', $cs[$t]).','.$alpha.')' : 'rgba(128,128,128,'.$alpha.')';
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'agent' => array(self::BELONGS_TO, 'Org', 'org_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'date' => 'Date',
			'org_id' => 'Org',
			'pu_id' => 'PU',
			'type' => 'Type',
			'qty' => 'Qty',
			'wt' => 'Wt',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('org_id',$this->org_id);
		$criteria->compare('pu_id',$this->pu_id);
		$criteria->compare('type',$this->type);
		$criteria->compare('qty',$this->qty,true);
		$criteria->compare('wt',$this->wt,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ExStat the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
