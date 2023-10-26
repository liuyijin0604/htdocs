<?php

/**
 * This is the model class for table "wms_stock_location".
 *
 * The followings are the available columns in table 'wms_stock_location':
 * @property string $id
 * @property string $stock_id
 * @property string $location_id
 * @property double $qty
 * @property string $updated
 * @property string $meta
 */
class WmsStockLocation extends CActiveRecord
{
	public $loc_code;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_stock_location';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('stock_id, location_id, qty', 'required'),
			array('updated, meta', 'safe'),
			array('qty', 'numerical'),
			array('stock_id, location_id', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, stock_id, location_id, loc_code, qty, updated, meta', 'safe', 'on'=>'search'),
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
			'task' => array(self::BELONGS_TO, 'WmsTask', 'task_id'),
			'stock' => array(self::BELONGS_TO, 'WmsStock', 'stock_id'),
			'loc' => array(self::BELONGS_TO, 'WmsLocation', 'location_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'stock_id' => 'Stock',
			'location_id' => 'Location',
			'loc_code' => 'Location',
			'qty' => 'Qty',
			'updated' => 'Updated',
			'meta' => 'Meta',
		);
	}

	public static function isMixedPallet($plt){
		return self::model()->with('loc')->together()->count('qty > 0 AND loc.code = :c', [':c' => $plt]) > 1;
	}

	public function getMfr()
	{
		$in = WmsStockLedger::model()->with('taskItem')->find('t.stock_id = :stock_id AND t.location_id = :location_id AND JSON_VALUE(taskItem.meta, "$.mfr")', [':stock_id' => $this->stock_id, ':location_id' => $this->location_id]);
		return !empty($in) ? $in->taskItem->mdata['mfr'] : '';
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
	public function search($pgn=true, $ps = 30, $ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.stock_id',$this->stock_id);
		$criteria->compare('t.location_id',$this->location_id);
		$criteria->compare('qty',$this->qty);
		$criteria->compare('updated',$this->updated,true);

		$with = array();
		if(!empty($this->loc_code)){
			$with[] = 'loc';
			$criteria->compare('loc.code', $this->loc_code, true);
		}

		if(!empty($with)){
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if($ec) $criteria->mergeWith($ec);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
    			'defaultOrder'=>'t.id DESC',
  			),
			'pagination'=> $pgn? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WmsStockLocation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
