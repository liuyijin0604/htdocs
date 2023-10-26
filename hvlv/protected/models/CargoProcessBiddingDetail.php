<?php

/**
 * This is the model class for table "cargo_process_bidding_detail".
 *
 * The followings are the available columns in table 'cargo_process_bidding_detail':
 * @property integer $id
 * @property integer $bid_id
 * @property integer $driver_id
 * @property string $choosedate
 * @property double $cost
 * @property string $note
 * @property integer $status
 * @property string $meta
 * @property string $createtime
 * @property integer $creater
 */
class CargoProcessBiddingDetail extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cargo_process_bidding_detail';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('choosedate', 'required'),
			array('bid_id, driver_id, status, creater', 'numerical', 'integerOnly'=>true),
			array('cost', 'numerical'),
			array('meta, createtime', 'safe'),
			array('note', 'length', 'max' => 500),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, bid_id, driver_id, choosedate, cost, note, status, meta, createtime, creater', 'safe', 'on'=>'search'),
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
			'cargo_process_bidding' => [self::BELONGS_TO, 'CargoProcessBidding','bid_id'],
			'driver'=>[self::BELONGS_TO,'Org','driver_id']
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'bid_id' => 'Bid',
			'driver_id' => 'Driver Id',
			'choosedate' => 'Delivery Date',
			'cost' => 'Quote',
			'note' => 'Note',
			'status' => 'Status',
			'meta' => 'Meta',
			'createtime' => 'Createtime',
			'creater' => 'Creater',
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
	public function search($pgn = true,$ps = 50,$ec = false, $defaultOrder = true)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		if(!empty($this->bid_id)){
			$criteria->compare('bid_id',$this->bid_id);
		}else{
			$criteria->compare('bid_id',0);
		}
		$criteria->compare('driver_id',$this->driver_id);
		$criteria->compare('choosedate',$this->choosedate,true);
		$criteria->compare('cost',$this->cost);
		$criteria->compare('note',$this->note,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('meta',$this->meta,true);
		$criteria->compare('createtime',$this->createtime,true);
		$criteria->compare('creater',$this->creater);

		$sort = new CSort();

		$pagerparams = $_GET;
		$sort->defaultOrder = 't.cost';
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>$sort,
			'pagination'=>$pgn?[
				'pageSize'=>$ps,
				'params'=>$pagerparams,
			]:false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CargoProcessBiddingDetail the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function afterSave()
	{
		$objBidding = $this->cargo_process_bidding;
		$numCount = count(CargoProcessBiddingDetail::model()->findAll('bid_id = :bid_id',[':bid_id'=>$this->bid_id]));
		$objBidding->count = $numCount;
		$objBidding->save();
	}
}
