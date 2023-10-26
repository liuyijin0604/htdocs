<?php

/**
 * This is the model class for table "edi_job_basic_template".
 *
 * The followings are the available columns in table 'edi_job_basic_template':
 * @property string $id
 * @property string $name
 * @property integer $owner_id
 * @property integer $user_id
 * @property string $created
 * @property string $valid_from
 * @property string $valid_to
 * @property string $meta
 * @property integer $status
 */
class EdiJobBasicTemplate extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'edi_job_basic_template';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('owner_id, user_id, status', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>45),
			array('meta', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, owner_id, user_id, created, valid_from, valid_to, meta, status', 'safe', 'on'=>'search'),
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
            'user' => array(self::BELONGS_TO,'User','user_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'owner_id' => 'Owner',
			'user_id' => 'Operator',
			'created' => 'Created',
			'valid_from' => 'Valid From',
			'valid_to' => 'Valid To',
			'meta' => 'Meta',
			'status' => 'Status',
		);
	}

    public function beforeSave(){
        if ( empty($this->user_id) ) $this->user_id = Yii::app()->user->id;
        if ( empty($this->created) ) $this->created = date('Y-m-d');
        return true;
    }

    /**
     * list all availabel basic templates for choice
     * @return array
     */
    public static function getTemplatesList(){
        $ts = EdiJobBasicTemplate::model()->findAll();
        $list = array();
        foreach ( $ts as $t) {
            $list[$t->id] = $t->name;
        }
        return $list;
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('owner_id',$this->owner_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('valid_from',$this->valid_from,true);
		$criteria->compare('valid_to',$this->valid_to,true);
		$criteria->compare('meta',$this->meta,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return EdiJobBasicTemplate the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
