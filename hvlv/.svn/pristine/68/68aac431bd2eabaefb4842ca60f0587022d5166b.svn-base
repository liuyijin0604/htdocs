<?php

/**
 * This is the model class for table "imports_mail_user_map".
 *
 * The followings are the available columns in table 'imports_mail_user_map':
 * @property integer $id
 * @property integer $type_id
 * @property integer $user_id
 * @property String  $tel
 */
class ImportsMailUserMap extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'imports_mail_user_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type_id, user_id', 'required'),
			array('type_id, user_id', 'numerical', 'integerOnly'=>true),
                        array('tel','length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type_id, user_id,tel', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'type_id' => 'Type',
			'user_id' => 'User',
                        'tel' => 'Tel'
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
		$criteria->compare('type_id',$this->type_id);
		$criteria->compare('user_id',$this->user_id);
                $criteria->compare('tel',$this->tel,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        
        public static function getUserTypes($user_id=''){
            $typeArray=[1];
            if(empty($user_id)){
             $user_id=isset(Yii::app()->user->id)?Yii::app()->user->id:0;
            }
            $rs=self::model()->findAll('user_id=:user_id',array(':user_id'=>$user_id));
            if(!empty($rs)){
                foreach ($rs as $r){
                    if(!in_array($r->type_id, $typeArray)){
                        $typeArray[]=$r->type_id;
                    }
                }
            }
            return $typeArray;
        }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ImportsMailUserMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
