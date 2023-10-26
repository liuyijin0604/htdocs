<?php

/**
 * This is the model class for table "wms_stock_ledger".
 *
 * The followings are the available columns in table 'wms_stock_ledger':
 * @property string $id
 * @property string $ti_id
 * @property string $stock_id
 * @property string $location_id
 * @property string $l2_id
 * @property double $qty_in
 * @property double $qty_out
 * @property string $ts
 * @property string $meta
 */
class WmsStockLedger extends CActiveRecord
{
	public $loc_code, $l2_code, $balance, $task_id;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_stock_ledger';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('stock_id, location_id', 'required'),
			array('ti_id, l2_id, qty_in, qty_out, ts, meta', 'safe'),
			array('qty_in, qty_out', 'numerical'),
			array('ti_id, stock_id, location_id', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, ti_id, stock_id, location_id, loc_code, l2_code, qty_in, qty_out, ts, meta, balance, task_id', 'safe', 'on'=>'search'),
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
			'taskItem' => array(self::BELONGS_TO, 'WmsTaskItem', 'ti_id'),
			'stock' => array(self::BELONGS_TO, 'WmsStock', 'stock_id'),
			'loc' => array(self::BELONGS_TO, 'WmsLocation', 'location_id'),
			'l2' => array(self::BELONGS_TO, 'WmsLocation', 'l2_id'),
		);
	}

	protected function beforeSave(){
		if(empty($this->l2_id) && !empty($this->location_id)) $this->l2_id = $this->loc->pid;
		return true;
	}

	public function delete(){
		if(!$this->getIsNewRecord()){
			Yii::trace(get_class($this).'.delete()','system.db.ar.CActiveRecord');
			if($this->beforeDelete()){
				$this->qty_in = 0;
				$this->qty_out = 0;
				$this->update(['qty_in', 'qty_out']);
				$this->afterDelete();
				return true;
			}
			else
				return false;
		}
		else
			throw new CDbException(Yii::t('yii','The active record cannot be deleted because it is new.'));
	}

	/**
	 * Add paired ledger
	 * @param  int   $sid  stock ID
	 * @param  int   $tid  task item ID
	 * @param  float $qty  Qty
	 * @param  int   $floc  From location ID
	 * @param  int   $tloc  To Location ID
	 * @return int        -1: not enough stock
	 */
	public static function entryPair($sid, $tid, $qty, $floc, $tloc){

		//out
		$sl = WmsStockLedger::model()->find('ti_id = :tid AND location_id = :lid', [':tid' => $tid, ':lid' => $floc]);

		if(empty($sl) || $sl->qty_out < $qty){
			//check enough stock at floc
			$qq = empty($sl->qty_out)? $qty : $qty - $sl->qty_out;
			$sloc = WmsStockLocation::model()->count('stock_id = :sid AND location_id = :lid AND qty >= :q', [':sid' => $sid, ':lid' => $floc, ':q' => $qty]);
			if(empty($sloc)) return -1;
		}

		$ups = [];
		if(empty($sl)){
			$sl = new WmsStockLedger('create');
			$sl->ti_id = $tid;
		}elseif($sl->stock_id != $sid){
			$ups[] = $sl->stock_id;
		}
		$sl->stock_id = $sid;
		$sl->location_id = $floc;
		$sl->qty_out = $qty;
		$sl->save();

		//in
		$sl = WmsStockLedger::model()->find('ti_id = :tid AND location_id = :lid', [':tid' => $tid, ':lid' => $tloc]);
		if(empty($sl)){
			$sl = new WmsStockLedger('create');
			$sl->ti_id = $tid;
		}elseif($sl->stock_id != $sid){
			$ups[] = $sl->stock_id;
		}
		$sl->stock_id = $sid;
		$sl->location_id = $tloc;
		$sl->qty_in = $qty;
		$sl->save();

		WmsStock::countAll($sid);
		foreach($ups as $lso) WmsStock::countAll($lso);
		return true;
	}

	public static function stockInTime($lid){
		$r = self::model()->find(['condition' => 'location_id = :lid AND qty_in > 0', 'params' => [':lid' => $lid], 'order' => 'ts ASC']);
		return $r->ts;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'ti_id' => 'Task',
			'stock_id' => 'Stock',
			'location_id' => 'L1',
			'l2_id' => 'L2',
			'loc_code' => 'L1',
			'l2_code' => 'L2',
			'qty_in' => 'Qty In',
			'qty_out' => 'Qty Out',
			'ts' => 'Time',
			'meta' => 'Meta',
			'task_id' => 'Task',
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
	public function search($pgn=true, $ps = 30, $ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.ti_id',$this->ti_id);
		$criteria->compare('t.stock_id',$this->stock_id);
		$criteria->compare('location_id',$this->location_id);
		$criteria->compare('l2_id',$this->l2_id);
		$criteria->compare('qty_in',$this->qty_in);
		$criteria->compare('qty_out',$this->qty_out);
		$criteria->compare('ts',$this->ts,true);

		$with = array();
		if(!empty($this->loc_code)){
			$with[] = 'loc';
			$criteria->compare('loc.code', $this->loc_code, true);
		}

		if (!empty($this->task_id)) {
			$this->task_id = trim(str_replace('T', '', $this->task_id));
			$with[] = 'taskItem.task';
			$criteria->addCondition('task.id LIKE "%' . $this->task_id . '%" OR task.link_id LIKE "%' . $this->task_id . '%"');
		}

		if(!empty($this->l2_code)){
			$with[] = 'l2';
			$criteria->compare('l2.code', $this->loc_code, true);
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
	 * @return WmsStockLedger the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
