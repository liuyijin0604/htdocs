<?php

/**
 * This is the model class for table "wms_task_item".
 *
 * The followings are the available columns in table 'wms_task_item':
 * @property string $id
 * @property string $task_id
 * @property string $op_id
 * @property string $ts
 * @property string $meta
 */
class WmsTaskItem extends CActiveRecord
{

	public $mdata = [];
	public $meta_changed = true, $prod;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_task_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id', 'required'),
			array('op_id, ts, meta, del', 'safe'),
			array('task_id', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, task_id, op_id, ts, meta, del, prod', 'safe', 'on'=>'search'),
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
			'stockLedgers' => array(self::HAS_MANY, 'WmsStockLedger', 'ti_id'),
			'op' => array(self::BELONGS_TO, 'User', 'op_id'),
		);
	}

	protected function beforeSave(){
		$this->meta = empty($this->mdata)? '' : json_encode($this->mdata);
		if($this->id > 0){
			$o = self::model()->findByPk($this->id);
			if($this->meta === $o->meta) $this->meta_changed = false;
		}
		if(empty($this->ts)) $this->ts = date('Y-m-d H:i:s');
		if(empty($this->op_id)) $this->op_id = User::currentUserID();
		return parent::beforeSave();
	}

	protected function afterSave(){
		if($this->meta_changed || WmsStockLedger::model()->count('ti_id = :id', [':id' => $this->id]) < 1)	$this->toStock();
	}
	
	protected function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return true;
	}

	protected function beforeDelete(){
		if(in_array($this->task->type, [2030, 2040])){
			if(empty($this->mdata['pli'])) return;
			$l = WmsLocation::model()->findByPk($this->mdata['pli']);
			// $l->pid = 2;
			// when container load delete item, "Pending Out" caused stock countall fail; so change to "Pending In"
			$l->pid = 1;
			$l->save();
		}
		return parent::beforeDelete();
	}

	public function delete(){
		if(!$this->getIsNewRecord()){
			Yii::trace(get_class($this).'.delete()','system.db.ar.CActiveRecord');
			if($this->beforeDelete()){
				$this->meta_changed = false;
				$this->del = 1;
				$this->update(['del']);
				$this->afterDelete();
				return true;
			}
			else
				return false;
		}
		else
			throw new CDbException(Yii::t('yii','The active record cannot be deleted because it is new.'));
	}
	
	protected function afterDelete(){
		$sids = [];
		foreach($this->stockLedgers as $l){
			$l->delete();
			$sids[] = $l->stock_id;
		}
		if (in_array($this->task->type, [3010])) {
			if (empty($this->mdata['pli'])) return;
			$l = WmsLocation::model()->findByPk($this->mdata['pli']);
			$l->pid = 1;
			$l->update('pid');
			$sls = WmsStockLocation::model()->findAll('location_id = :l', array(':l' => $l->id));
			foreach ($sls as $sl) {
				WmsStock::countAll($sl->stock_id);
			}
		}
		foreach(array_unique($sids) as $sid) WmsStock::countAll($sid);
		if (in_array($this->task->type, [3020, 3030]) && $this->task->is_request == 0) {
			$this->deleteSerialNo();
		}
		return parent::afterDelete();
	}

	public function deleteSerialNo()
	{
		// delete wms_serial_no
		if (empty($this->mdata['si']) || empty($this->mdata['pli'])) return;

		$o = WmsTaskItem::model()->find('task_id = :id AND del = 0 AND JSON_VALUE(meta, "$.si") = :si AND JSON_VALUE(meta, "$.pli") = :pli', [':id' => $this->task_id, ':si' => $this->mdata['si'], ':pli' => $this->mdata['pli']]);
		if (!empty($o)) return;

		$serials = WmsSerialNo::model()->findAll('task_id = :id AND stock_id = :sid AND location_id = :pli', [':id' => $this->task->link_id, ':sid' => $this->mdata['si'], ':pli' => $this->mdata['pli']]);
		foreach ($serials as $serial) {
			$serial->delete();
		}
	}

	public function toStock(){
		if ($this->del == 1) return;
		if($this->task->is_request){
			if($this->task->status == 10) return;
			if(in_array($this->task->type, [3020, 3030, 5010])){// pick carton or unit or cg delivery
				if(empty($this->mdata['si'])) return;
				$rq = 0;
				foreach($this->stockLedgers as $l){
					if($l->location_id == WmsLocation::WMS_LOCATION_RESERVED){
						$rq = $l->qty_in;
					}
				}
				$stock = WmsStock::model()->findByPk($this->mdata['si']);
				$sl = WmsStockLedger::model()->find('ti_id = :tid AND location_id = :location_id', [':tid' => $this->id, ':location_id' => WmsLocation::WMS_LOCATION_RESERVED]);
				if(empty($sl)){
					$sl = new WmsStockLedger;
					$sl->ti_id = $this->id;
				}
				if($stock->availQty() + $rq - floatval($this->mdata['uq']) < 0){
					$this->addError('task_id', 'Not enough stock to reserve '.$this->mdata['sn'].', available '.($stock->availQty() + $rq));
					$sl->qty_in = 0;
				}else{
					$sl->qty_in = $this->mdata['uq'];
				}
				$sl->stock_id = $this->mdata['si'];
				$sl->location_id = WmsLocation::WMS_LOCATION_RESERVED;
				$sl->save();
				WmsStock::countAll($this->mdata['si']);
			}
			return;
		}
		if(in_array($this->task->type, [1010, 1020, 1030])){// stock in
			// if(empty($this->mdata['gi']) || empty($this->mdata['uq'])) return;
			if (empty($this->mdata['gi'])) return;
			$stock = WmsStock::creget($this->task->job->org_id, $this->mdata, $this->task->dpt_id);
			if(!empty($this->mdata['pl'])){
				$loc = WmsLocation::model()->find('type IN (30, 50, 60) AND (name = :n OR code = :n)', [':n' => $this->mdata['pl']]);
				if(empty($loc)){
					$loc = new WmsLocation;
					$loc->type = 50;
					// 2020-04-17 add mel
					// $loc->wid = 106;
					$loc->wid = $this->task->dpt_id;
					$loc->status = 1;
					$loc->name = $this->mdata['pl'];
					$loc->code = $this->mdata['pl'];
				}
				if(empty($loc->pid) || in_array($loc->pid, [2,3])) $loc->pid = 1; //pending in
				$loc->save();
				$loc_id = $loc->id;
			}else{
				$loc_id = 1;
			}

			$sl = WmsStockLedger::model()->find('ti_id = :tid', [':tid' => $this->id]);

			$lso = 0;
			if(empty($sl)){
				$sl = new WmsStockLedger;
				$sl->ti_id = $this->id;
			}else{
				$lso = $sl->stock_id;
			}
			$sl->stock_id = $stock->id;
			$sl->location_id = $loc_id;
			$sl->qty_in = $this->mdata['uq'];
			$sl->save();

			WmsStock::countAll($stock->id);
			if($lso != $stock->id) WmsStock::countAll($lso);
		}elseif(in_array($this->task->type, [3010])){// pick pallet to pending out
			if(empty($this->mdata['pli'])) return;
			$l = WmsLocation::model()->findByPk($this->mdata['pli']);
			if (!empty($this->task->mainTask->mdata['ct']) || $this->task->job->type == WmsJob::TYPE_PICK_LOAD) {
				// new bulk task for pick pallet, only put into pending out status, do not calculate stock
				$l->pid = 2;
				$l->save();
			} else {
				$l->pid = 5;
				$l->save();
				$sl = WmsStockLedger::model()->find('ti_id = :ti_id AND stock_id = :stock_id', [':ti_id' => $this->id, ':stock_id' => $this->mdata['si']]);
				if (empty($sl)) {
					$sl = new WmsStockLedger;
					$sl->ti_id = $this->id;
					$sl->stock_id = $this->mdata['si'];
					$sl->location_id = $this->mdata['pli'];
					$sloc = WmsStockLocation::model()->find('stock_id = :stock_id AND location_id = :location_id', [':stock_id' => $this->mdata['si'], ':location_id' => $this->mdata['pli']]);
					$sl->qty_out = $sloc->qty;
					$sl->save();
				}
				WmsStock::countAll($this->mdata['si']);
			}
		}elseif(in_array($this->task->type, [2030, 2040])){// pallet to out
			if(empty($this->mdata['pli'])) return;
			$l = WmsLocation::model()->findByPk($this->mdata['pli']);
			$l->pid = 5;
			$l->save();
			$sps = WmsStockLocation::model()->findAll('location_id = :lid AND qty > 0', [':lid' => $this->mdata['pli']]);
			$sgs = [];
			foreach($sps as $sp){
				if(isset($sgs[$sp->stock_id])){
					$oq = $sgs[$sp->stock_id] + $sp->qty;
				}else{
					$oq = $sp->qty;
				}
				$sl = WmsStockLedger::model()->find('stock_id = :sid AND ti_id = :tid AND location_id = :lid', [':sid' => $sp->stock_id, ':tid' => $this->id, ':lid' => $this->mdata['pli']]);
				if(empty($sl)){
					$sl = new WmsStockLedger('create');
					$sl->stock_id = $sp->stock_id;
					$sl->location_id = $this->mdata['pli'];
					$sl->ti_id = $this->id;
				}
				$sl->qty_out = $oq;
				$sl->save();
				$sgs[$sp->stock_id] = $oq;
				WmsStock::countAll($sp->stock_id);
			}
		}elseif(in_array($this->task->type, [3020, 3030, 5010])){// pick carton or unit or cg delivery
			if(empty($this->mdata['pli'])) return;
			if (!empty($this->mdata['short'])) {
				$sle = WmsStockLedger::entryPair($this->mdata['si'], $this->id, $this->mdata['uq'], $this->mdata['pli'], WmsLocation::WMS_LOCATION_SHORT_RELEASE);
			} else {
				$sle = WmsStockLedger::entryPair($this->mdata['si'], $this->id, $this->mdata['uq'], $this->mdata['pli'], WmsLocation::WMS_LOCATION_PACKING);
			}
			if($sle === -1){
				$this->addError('task_id', 'Not enough stock for picking '.$this->mdata['sn'].', '.$this->mdata['pl'].' ('.$this->mdata['pli'].')');
			}else{
				$sl = WmsStockLedger::model()->find('ti_id = :tid AND location_id = :location_id', [':tid' => $this->id, ':location_id' => WmsLocation::WMS_LOCATION_RESERVED]);
				if(empty($sl)){
					$sl = new WmsStockLedger;
					$sl->ti_id = $this->id;
				}
				$sl->stock_id = $this->mdata['si'];
				$sl->location_id = WmsLocation::WMS_LOCATION_RESERVED;
				$sl->qty_out = $this->mdata['uq'];
				$sl->save();
				WmsStock::countAll($this->mdata['si']);
			}
		}elseif(in_array($this->task->type, [3210])){// pack to pending out
			$sle = WmsStockLedger::entryPair($this->mdata['si'], $this->id, $this->mdata['uq'], 3, 2);
			if($sle === -1){
				$this->addError('task_id', 'Not enough stock in packing '.$this->mdata['sn'].' ('.$this->mdata['si'].')');
			}
		}
	}

	public function leaveWarehouse(){
		if(empty($this->mdata['uq']) && !empty($this->mdata['pq']) && $this->mdata['pq'] == 1){// pallet
			if(empty($this->mdata['pli'])) return;
			$sps = WmsStockLocation::model()->findAll('location_id = :lid AND qty > 0', [':lid' => $this->mdata['pli']]);
			if(empty($sps)){
				$this->addError('task_id', 'Location '.$this->mdata['pl'].' not found for '.$this->mdata['sn'].' ('.$this->mdata['si'].')');
			}else{
				$l = WmsLocation::model()->findByPk($this->mdata['pli']);
				$l->pid = 5;
				$l->save();
				foreach($sps as $sp){
					$oq = $sp->qty;
					$sl = WmsStockLedger::model()->find('stock_id = :sid AND ti_id = :tid AND location_id = :lid', [':sid' => $sp->stock_id, ':tid' => $this->id, ':lid' => $this->mdata['pli']]);
					if(empty($sl)){
						$sl = new WmsStockLedger('create');
						$sl->stock_id = $sp->stock_id;
						$sl->location_id = $this->mdata['pli'];
						$sl->ti_id = $this->id;
						$sl->ts = $this->task->mainTask->compl_time;
					}
					$sl->qty_out = $oq;
					$sl->save();
					WmsStock::countAll($sp->stock_id);
				}
			}
		}else{
			$sle = WmsStockLedger::entryPair($this->mdata['si'], $this->id, $this->mdata['uq'], 2, 5);
			if($sle === -1){
				$this->addError('task_id', 'Not enough stock in pending out '.$this->mdata['sn'].' ('.$this->mdata['si'].')');
			}
		}
	}
		
	public function realaseReserve() {
		if (in_array($this->task->type, [3020, 3030])) {
			$sl = WmsStockLedger::model()->find('ti_id = :tid AND location_id = :location_id', [':tid' => $this->id, ':location_id' => WmsLocation::WMS_LOCATION_RESERVED]);
			if (!empty($sl)) {
				$sl->qty_in = 0;
				$sl->qty_out = 0;
				$sl->save();	
				WmsStock::countAll($this->mdata['si']);
				$this->mdata['uq'] = 0;
				$this->mdata['cq'] = 0;
				$this->update('meta');
			}
		}
	}

	public function revertPick() {
		if (in_array($this->task->type, [3020, 3030])) {
			$sls = WmsStockLedger::model()->findAll('ti_id = :tid', [':tid' => $this->id]);
			foreach ($sls as $sl) {
				$sl->qty_in = 0;
				$sl->qty_out = 0;
				$sl->save();
				WmsStock::countAll($this->mdata['si']);
			}
			$this->mdata['uq'] = 0;
			$this->update('meta');
		}
	}

	public function waitInNum()
	{
		$num = $this->mdata['uq'];

		foreach ($this->task->actionTask->items as $item) {
			if ($item->mdata['gn'] === $this->mdata['gn']) {
				$num -= $item->mdata['uq'];
			}
		}

		return $num;
	}

	public function waitOutNum()
	{
		foreach ($this->task->actionTask->items as $item) {
			if ($item->mdata['pli'] === $this->mdata['pli']) {
				return array('name' => $this->mdata['pl'], 'status' => false);
			}
		}

		return array('name' => $this->mdata['pl'], 'status' => true);
	}

	public function recSerial(){
		if(empty($this->mdata['si'])) return false;
		$s = WmsStock::model()->findByPk($this->mdata['si']);
		if(empty($s)) return false;
		$wpo = WmsProdOrg::model()->find('prod_id = :pid AND org_id = :oid', [':pid' => $s->prod_id, ':oid' => $s->org_id]);
		if(empty($wpo->mdata['rec_sn'])) return false;
		if($wpo->mdata['rec_sn'] == 3) return true;

		if($wpo->mdata['rec_sn'] == 1 && in_array($this->task->mainTask->type, [1010, 1020, 1030])){
			return true;
		}elseif($wpo->mdata['rec_sn'] == 2 && in_array($this->task->mainTask->type, [3010, 3020, 3030])){
			return true;
		}

		return false;
	}

	public function totalUq(){
		$tuq = 0;
		foreach(array_reverse($this->task->items) as $itm){
			if($itm->mdata['si'] == $this->mdata['si']){
				$tuq += $this->mdata['uq'];
			}
		}
		return $tuq;
	}

	public function serialScanned(){
		return WmsSerialNo::itemCount($this->task->link_id, $this->mdata['si']);
	}

	public function serialComplete(){
		return $this->serialScanned() >= $this->totalUq();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'task_id' => 'Task',
			'sl_id' => 'Stock Ledger',
			'ts' => 'Ts',
			'op_id' => 'OP',
			'meta' => 'Meta',
			'del' => 'Deleted',
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
		$criteria->compare('task_id',$this->task_id);
		$criteria->compare('op_id',$this->op_id);
		// $criteria->compare('sl_id',$this->task_id);
		$criteria->compare('ts',$this->ts,true);
		$criteria->compare('del', 0);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WmsTaskItem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
