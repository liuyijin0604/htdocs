<?php

/**
 * This is the model class for table "salesfunnel_requirements_records".
 *
 * The followings are the available columns in table 'salesfunnel_requirements_records':
 * @property integer $id
 * @property integer $submit_id
 * @property integer $question_id
 * @property integer $answer_id
 * @property string $answer_str
 * @property string $date
 */
class SalesfunnelRequirementsRecords extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'salesfunnel_requirements_records';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('submit_id, question_id, answer_id', 'numerical', 'integerOnly'=>true),
			array('answer_str', 'length', 'max'=>200),
			array('date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, submit_id, question_id, answer_id, answer_str, date', 'safe', 'on'=>'search'),
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
			'question'=>[self::BELONGS_TO,"SalesfunnelRequirementsQuestions","question_id"],
			'answer'=>[self::BELONGS_TO,"SalesfunnelRequirementsAnswer","answer_id"],
			'submission'=>[self::BELONGS_TO,"SalesfunnelRequirementsSubmission","submit_id"]
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'submit_id' => 'Submit',
			'question_id' => 'Question',
			'answer_id' => 'Answer',
			'answer_str' => 'Answer Str',
			'date' => 'Date',
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
		$criteria->compare('submit_id',$this->submit_id);
		$criteria->compare('question_id',$this->question_id);
		$criteria->compare('answer_id',$this->answer_id);
		$criteria->compare('answer_str',$this->answer_str,true);
		$criteria->compare('date',$this->date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SalesfunnelRequirementsRecords the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
