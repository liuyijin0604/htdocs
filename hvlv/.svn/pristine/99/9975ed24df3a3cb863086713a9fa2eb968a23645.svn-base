<?php

/**
 * This is the model class for table "imports_system_fuel".
 *
 * The followings are the available columns in table 'imports_system_fuel':
 * @property integer $id
 * @property string $rate
 * @property string $month
 * @property integer $type
 * @property string $created
 */
class ImportsSystemFuel extends CActiveRecord
{
	const COURIER_TYPE = 1;
	const CARTAGE_TYPE = 2;
	const KP_TYPE = 3;
	const KP_DISCOUNT_TYPE = 5;
	
	public $createdUserName = null;

	public static $types = [
		1=>"courier",
		2=>"Container cartage",
		3=>"KP cartage",
		4=>"3PL",
		5=>"KP Discount",
	];
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'imports_system_fuel';
	}


	public function getType()
	{
		$type = isset(static::$types[$this->type]) ? Yii::t(strtolower(__CLASS__), static::$types[$this->type]) : $this->type;
		return $type;
	}


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('rate, from_day,to_day, type', 'required'),
			array('type', 'numerical', 'integerOnly'=>true),
			array('rate', 'length', 'max'=>10),
			array('from_day, to_day', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, rate, month, type, created, createdUserName', 'safe', 'on'=>'search'),
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
			'createUser' => [self::BELONGS_TO, 'User', 'user_id']
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'rate' => 'Rate',
			'from_day' => 'From Date',
			'to_day' => 'To Date',
			'type' => 'Type',
			'user_id' => 'Created By',
			'created' => 'Created',
			'createdUserName'=>'Create User'
		);
	}

	public static function getThisMonthFuel($from_day="",$type = "")
	{
		if(empty($from_day))
		{
			$from_day = date("Y-m-d");
		}
		if(empty($type))
		{
			$type = self::COURIER_TYPE;
		}

		$thisFuel = self::model()->find("from_day <= :from_day and to_day >= :from_day and type =:type",[":type"=>$type,":from_day"=>$from_day]);
		if(!empty($thisFuel))
		{
			return $thisFuel->rate;
		}else
		{
			return 0;
		}
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
	public function search($pgn=true,$ps=30)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('rate',$this->rate,true);
		$criteria->compare('from_day',$this->from_day,true);
		$criteria->compare('to_day',$this->from_day,true);
		$criteria->compare('t.type',$this->type);
		$criteria->compare('created',$this->created,true);


		if($this->createdUserName!==false)
		{
			$with[] = 'createUser';
			$criteria->compare('createUser.fname',$this->createdUserName,true);
		}

		$sort = new CSort(get_called_class());
		$sort->attributes = [
			'from_day' => [
				'asc' => 'from_day ASC',
				'desc' => 'from_day DESC',
			]
		];

		$sort->defaultOrder = "from_day ASC";

		if (!empty($with)) {
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}
		// in case sub-gridview, we need consol_id set by parent grid view
		$pagerparams = $_GET;

		return new CActiveDataProvider($this, [
			'criteria' => $criteria,
			'sort' => $sort,
			'pagination' => $pgn ? [
				'pageSize' => $ps,
				'params' => $pagerparams,
			] : false,
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ImportsSystemFuel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
