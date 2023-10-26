<?php

/**
 * This is the model class for table "type_map_user".
 *
 * The followings are the available columns in table 'type_map_user':
 * @property integer $id
 * @property integer $type
 * @property integer $map_type
 * @property integer $user_id
 * @property integer $status
 */
class TypeMapUser extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'type_map_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, map_type, user_id', 'required'),
			array('type, map_type, user_id, status', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type, map_type, user_id, status', 'safe', 'on'=>'search'),
		);
	}
        
        const TYPE_CUSTOM_PROCESS=1;
        const TYPE_CARGO_PROCESS=2;
        const TYPE_CUSTOMER_SERVICE = 3;
        public static $theTypes=array(
            1=>'custom Process',
        );
        
        public static  $states=array(
            1=>'valid',
        );

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
			'id' => 'ID',
			'type' => 'Type',
			'map_type' => 'Map Type',
			'user_id' => 'User',
			'status' => 'Status',
		);
	}
        /**
         * @param type $mainType the type 
         * @param type $map_type the map_type of the business 
         * @return array  the user id 
         */
        public static function getUserFromType($mainType=1,$map_type){
           $allTypes=TypeMapUser::model()->findAll('type=:type AND map_type=:map_type AND status=1',array(':type'=>$mainType,':map_type'=>$map_type));
           $users=[]; //store the user_id;
           if(!empty($allTypes)){
                  foreach($allTypes as $oneType ){
                      $users[]=$oneType['user_id'];
                  }
            }
            return $users;
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
		$criteria->compare('type',$this->type);
		$criteria->compare('map_type',$this->map_type);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TypeMapUser the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
