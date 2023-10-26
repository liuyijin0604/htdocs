<?php

/**
 * This is the model class for table "imparcel_scan_check".
 *
 * The followings are the available columns in table 'imparcel_scan_check':
 * @property integer $id
 * @property integer $user_id
 * @property string $meta
 * @property string $created
 */
class ImparcelScanCheck extends MetaModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'imparcel_scan_check';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, created', 'required'),
			array('id, user_id', 'numerical', 'integerOnly'=>true),
			array('created', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('user_id, created', 'safe', 'on'=>'search'),
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
			'user' => [self::BELONGS_TO, 'User', 'user_id'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'meta' => 'Meta',
			'created' => 'created',
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
	public function search($pgn = true, $ps = 50)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('meta',$this->meta,true);
		$criteria->compare('created',$this->created,true);
		$pagerparams = $_GET;
		$sort = new CSort();
		$sort->defaultOrder ='id desc';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort' => $sort,
			'pagination' => $pgn ? [
				'pageSize' => $ps,
				'params' => $pagerparams,
			] : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ImparcelScanCheck the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
