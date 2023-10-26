<?php

/**
 * This is the model class for table "import_amazon_plan".
 *
 * The followings are the available columns in table 'import_amazon_plan':
 * @property integer $id
 * @property string $amazon_booking_time
 * @property string $booking_ref
 * @property integer $pallet
 * @property integer $slot
 * @property integer $user_id
 * @property string $create
 */
class ImportsAmazonPlan extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'imports_amazon_plan';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('amazon_booking_time, pallet', 'required'),
			array('pallet, slot, user_id', 'numerical', 'integerOnly'=>true),
			array('booking_ref', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, amazon_booking_time, booking_ref, pallet, slot, user_id, create', 'safe', 'on'=>'search'),
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
			'amazon_booking_time' => 'Amazon Booking Time',
			'booking_ref' => 'Booking Ref',
			'pallet' => 'Pallet',
			'slot' => 'Slot',
			'user_id' => 'User',
			'create' => 'Create',
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
	public function search($pgn = true, $ps = 10)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('amazon_booking_time',$this->amazon_booking_time,true);
		$criteria->compare('booking_ref',$this->booking_ref,true);
		$criteria->compare('pallet',$this->pallet);
		$criteria->compare('slot',$this->slot);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('dpt_id',$this->dpt_id);
		$criteria->compare('create',$this->create,true);
		$pagerparams = $_GET;
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => $pgn ? [
				'pageSize' => $ps,
				'params' => $pagerparams,
			] : false
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ImportAmazonPlan the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
