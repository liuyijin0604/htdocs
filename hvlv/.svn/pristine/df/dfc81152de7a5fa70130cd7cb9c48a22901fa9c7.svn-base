<?php

/**
 * This is the model class for table "imports_mail_filter".
 *
 * The followings are the available columns in table 'imports_mail_filter':
 * @property integer $id
 * @property string $from_email
 * @property string $from_name
 * @property string $subject
 * @property string $to_email
 * @property string $cc_email
 * @property integer $type
 * @property string $tel
 * @property string $note
 */
class ImportsMailFilter extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'imports_mail_filter';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type', 'required'),
			array('type', 'numerical', 'integerOnly'=>true),
			array('from_email, from_name, subject, to_email, cc_email', 'length', 'max'=>400),
			array('tel', 'length', 'max'=>80),
			array('note', 'length', 'max'=>80),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, from_email, from_name, subject,tel,note,to_email,tel, cc_email, type', 'safe', 'on'=>'search'),
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
			'from_email' => 'From Email',
			'from_name' => 'From Name',
			'subject' => 'Subject',
			'to_email' => 'To Email',
			'cc_email' => 'Cc Email',
			'type' => 'Type',
			'tel'=>'Tel',
			'note'=> 'Note'
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
		$criteria->compare('from_email',$this->from_email,true);
		$criteria->compare('from_name',$this->from_name,true);
		$criteria->compare('subject',$this->subject,true);
		$criteria->compare('to_email',$this->to_email,true);
		$criteria->compare('cc_email',$this->cc_email,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('tel',$this->tel,true);
		$criteria->compare('note',$this->note,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ImportsMailFilter the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
