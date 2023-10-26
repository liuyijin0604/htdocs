<?php

/**
 * This is the model class for table "crm_log".
 *
 * The followings are the available columns in table 'crm_log':
 * @property string $id
 * @property string $crm_id
 * @property string $operator_id
 * @property Integer process_method
 * @property string $time
 * @property string $note
 */
class CrmLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'crm_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('crm_id, operator_id, note', 'required'),
			array('crm_id, operator_id', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
                        array('id, crm_id, operator_id, time, note, process_method', 'safe'),
			array('id, crm_id, operator_id, time, note, process_method', 'safe', 'on'=>'search'),
		);
	}

    public function getUser(){
        if(empty($this->operator_id)){
            return 'System';
        }else{
            return $this->user->getFullName();
        }
    }
    const METHOD_SMS=10;
    const METHOD_EMAIL=20;
    const METHOD_TELEPHONE=30;
    const METHOD_OTHERS=0;
    const METHOD_MANAGER=40;
    const METHOD_FINANCE=50;
    const METHOD_AUS_MANA=60;
    public static $method_types=array(
        10=>"SMS",
        20=>"Email",
        30=>'Telephone',
        40 => 'Push to Manager',
        50 => 'Push to Finance',
        0=>'Others',
     
    );

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
                  'user' => array(self::BELONGS_TO, 'User', 'operator_id'),
                  'crm'=> array(self::BELONGS_TO,'CRM','crm_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'crm_id' => 'Crm',
			'operator_id' => 'Operator',
			'time' => 'Time',
			'note' => 'Note',
		);
	}
        
        public function getProcessMethod(){
            return self::$method_types[$this->process_method];
            
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

		//$criteria->compare('id',$this->id,true);
		$criteria->compare('crm_id',$this->crm_id);
		//$criteria->compare('operator_id',$this->operator_id,true);
		//$criteria->compare('time',$this->time,true);
		//$criteria->compare('note',$this->note,true);
                $criteria->compare('process_method',$this->process_method,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

       public function beforeSave() {
             if ($this->isNewRecord)
                $this->time = new CDbExpression('NOW()');
                return parent::beforeSave();
         }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CrmLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
