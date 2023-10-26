<?php


class OrmSmsNotice extends CActiveRecord
{
	CONST ONSCHEDULED = 0;
	CONST ONRECEIVED = 1;
	CONST SENT = 2;
	CONST FALLSENT = 3;
	const TableName = 'sms_notice';
	
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return self::TableName;
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

            array(Field::id .','.Field::phone .','. Field::message.','.  Field::is_processed , 'safe', 'on'=>'search'),
		
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
			Field::id => Field::id,
			Field::phone =>Field::phone,
			Field::message => Field::message,
			Field::is_processed =>Field::is_processed,
			Field::sent_time => Field::sent_time,
			Field::cargo_process_id => Field::cargo_process_id
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

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
    }
    
    // public function funcGetRecordsByTime($strTimeFrom , $strTimeTo )
	// {
    //     // $strSql = "select * from ".$this->tableName()." where create_time between '".$strTimeFrom."' and '".$strTimeTo."'";
	// 	// $objAllRecords = parent::findAllBySql($strSql);
	// 	$objAllRecords = parent::findAll(Str::create_time.'>:from and '.Str::create_time.'<:to',[':from'=>$strTimeFrom,':to'=>$strTimeTo]);
	// 	return $objAllRecords;
	// }
	
	// static function funcFindBySubNumber($strSubNumber)
	// {
	// 	// $strSql = "select * from ".Self::TableName." where ".Str::sub_number." = '".$strSubNumber."'";
	// 	// $objAllRecords = (Self::model())->findAllBySql($strSql);
	// 	$objAllRecords = (Self::model())->findAll(Str::sub_number.'=:sub_number',[':sub_number'=>$strSubNumber]);
	// 	return $objAllRecords;
	// }
    
    


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
	

	// public function afterFind()
	// {
	// 	$this->JsonData = json_decode($this[Str::json], true);
		
	// 	parent::afterFind();
	// }
	
	// protected function beforeSave()
	// {
	// 	$this[Str::json] = json_encode($this->JsonData);
		
	// 	return parent::beforeSave();
	// }

	
}


class Field{
	const id = 'id';
	const phone = 'phone';
	const message = 'message';
	const is_processed = 'is_processed';
	const sent_time = 'sent_time';
	const cargo_process_id = 'cargo_process_id';
    
}