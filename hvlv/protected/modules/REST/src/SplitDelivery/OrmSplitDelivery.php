<?php

require_once 'protected\modules\REST\src\SplitDelivery\Str.php';

class OrmSplitDelivery extends CActiveRecord
{
	const TableName = 'split_delivery';
	
	public $JsonData = [];
	
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return OrmSplitDelivery::TableName;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.

            // array(Str::id .','.Str::main_number .','. Str::sub_number.','.  Str::weight .','. Str::actual_size .','. Str::report_volume .','.Str::actual_volume .','. Str::quantity .','. Str::address .','. Str::region .','. Str::postcode .','. Str::contact.','. Str::phone.','. Str::create_time, 'safe', 'on'=>'search'),
			array(Str::id .','.Str::main_number .','. Str::sub_number.','.  Str::weight .','.Str::actual_volume .','. Str::quantity .','. Str::address .','. Str::suburb .','. Str::postcode.','. Str::state  .','. Str::contact.','. Str::phone.','. Str::create_time, 'safe', 'on'=>'search'),
			
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{

		return array(
            'id' => 'id',
            'main_number' => 'main_number',
            'sub_number' =>  'sub_number',
            'weight' => 'weight',
            // 'actual_size' => 'actual_size',
            // 'report_volume' => 'report_volume ',
            'actual_volume' => 'actual_volume',
            'quantity' => 'quantity',
            'address' => 'address',
			// 'region' => 'region',
			'suburb' =>'suburb',
			'postcode' => 'postcode',
			'state' => 'state',
            'contact' => 'contact',
            'phone' => 'phone',
			'create_time' => 'create_time',
			
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
        // $criteria->compare('create_time',$this->id_type);
		// $criteria->compare('status',$this->name,true);
		// $criteria->compare('main_number',$this->address,true);
		// $criteria->compare('sub_number',$this->id_type);
        // $criteria->compare('contacter',$this->id_number,true);
        // $criteria->compare('phone',$this->companion_id,true);
        // $criteria->compare('email',$this->time_in,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
    }
    
    public function funcGetRecordsByTime($strTimeFrom , $strTimeTo )
	{
        // $strSql = "select * from ".$this->tableName()." where create_time between '".$strTimeFrom."' and '".$strTimeTo."'";
		// $objAllRecords = parent::findAllBySql($strSql);
		$objAllRecords = parent::findAll(Str::create_time.'>:from and '.Str::create_time.'<:to',[':from'=>$strTimeFrom,':to'=>$strTimeTo]);
		return $objAllRecords;
	}
	
	static function funcFindBySubNumber($strSubNumber)
	{
		// $strSql = "select * from ".Self::TableName." where ".Str::sub_number." = '".$strSubNumber."'";
		// $objAllRecords = (Self::model())->findAllBySql($strSql);
		$objAllRecords = (Self::model())->findAll(Str::sub_number.'=:sub_number',[':sub_number'=>$strSubNumber]);
		return $objAllRecords;
	}
    
    


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SystemSetting the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	

	public function afterFind()
	{
		$this->JsonData = json_decode($this[Str::json], true);
		
		parent::afterFind();
	}
	
	protected function beforeSave()
	{
		$this[Str::json] = json_encode($this->JsonData);
		
		return parent::beforeSave();
	}

	
}
