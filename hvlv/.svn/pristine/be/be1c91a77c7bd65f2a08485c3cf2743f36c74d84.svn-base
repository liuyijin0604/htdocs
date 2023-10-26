<?php

/**
 * This is the model class for table "salesfunnel_requirements_answer".
 *
 * The followings are the available columns in table 'salesfunnel_requirements_answer':
 * @property integer $id
 * @property string $answer
 * @property integer $question_id
 * @property integer $next_question_id
 */
class SalesfunnelRequirementsAnswer extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'salesfunnel_requirements_answer';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('answer, question_id', 'required'),
			array('question_id, next_question_id', 'numerical', 'integerOnly'=>true),
			array('answer', 'length', 'max'=>200),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, answer, question_id, next_question_id', 'safe', 'on'=>'search'),
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
			'this_question'=>[self::BELONGS_TO,"SalesfunnelRequirementsQuestions","question_id"],
			'next_question'=>[self::BELONGS_TO,"SalesfunnelRequirementsQuestions","next_question_id"]
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'answer' => 'Answer',
			'question_id' => 'Question',
			'next_question_id' => 'Next Question',
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
	public function search($pgn = true, $ps = 30, $ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('answer',$this->answer, true);
		$criteria->compare('question_id',$this->question_id);
		$criteria->compare('next_question_id',$this->next_question_id);
		$with = [];

		// if(!empty($this->id))
		// {
		// 	$with=['SalesfunnelRequirementsAnswer'];
		// 	$criteria->compare('SalesfunnelRequirementsAnswer.question_id',$this->id);
		// }

		$criteria->with = $with;
		$criteria->together = true;
		$sort = new CSort(get_called_class());
		$sort->defaultOrder = 't.id DESC';
		return new CActiveDataProvider($this, [
			'criteria' => $criteria,
			'sort' => $sort,
			'pagination' => [
			'pageSize' => 30,
			],
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SalesfunnelRequirementsAnswer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
