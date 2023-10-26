<?php

/**
 * This is the model class for table "wms_batch".
 *
 * The followings are the available columns in table 'wms_batch':
 * @property string $id
 * @property string $date
 * @property string $no
 * @property int $status
 * @property int $type
 * @property int $org_id
 * @property string $meta
 * @property string $shelf_number
 */
class WmsBatch extends CActiveRecord
{

	public $mdata;

	public static $states = array(
		10 => 'New',
		30 => 'WIP',
		99 => 'Finished',
	);
	const WMS_BATCH_STATUS_NEW = 10;
	const WMS_BATCH_STATUS_WIP = 30;
	const WMS_BATCH_STATUS_FINISHED = 99;

	public static $types = array(
		1 => 'Single',
		2 => 'Multi',
	);
	const WMS_BATCH_TYPE_SINGLE = 1;
	const WMS_BATCH_TYPE_MULTI = 2;

	// Other constant
	const WMS_BATCH_BOX_MAX = 21;
	const WMS_BATCH_SHELF_MAX = 3;
	const WMS_BATCH_PICKING_SINGLE_MAX = 25;

	const WMS_BATCH_OTHER_MULTI_MAX = 50;
	const WMS_BATCH_XCSOURCE_SINGLE_MAX = 4;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_batch';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, no, status, type, org_id, shelf_number', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, date, no, status, type, org_id, shelf_number', 'safe', 'on' => 'search'),
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
			'tasks' => array(self::MANY_MANY, 'WmsTask', 'wms_task_batch(batch_id, task_id)', 'on' => 'tasks.status != 100'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'date' => 'Date',
			'batch' => 'Batch No.',
			'status' => 'Status',
			'type' => 'Type',
			'org_id' => 'Org',
			'shelf_number' => 'Shelf No.',
		);
	}

	public function genNo()
	{
		$pfix = date('ymd', strtotime($this->date));
		$sql = 'SELECT MAX(no) FROM wms_batch WHERE date = :date AND type = :type AND no LIKE :pfix';
		$cmd = Yii::app()->db->createCommand($sql);
		$cmd->bindValues([':date' => $this->date, ':type' => $this->type, ':pfix' => $pfix . '%']);
		$max = $cmd->queryScalar();
		if (empty($max)) {
			$max = 0;
		} else {
			$max = ltrim(substr($max, 9, 2), '0');
		}

		return $pfix . ' - ' . sprintf('%02d', $max + 1) . ' - ' . self::$types[$this->type];
	}

	public function calRemain()
	{
		$remain = 0;
		foreach ($this->tasks as $task) {
			foreach ($task->items as $item) {
				$remain += intval($item->mdata['uq']) - intval(@$item->mdata['sort_qty']);
			}
		}

		foreach ($this->tasks as $task) {
			foreach ($task->actionTask->items as $item) {
				if (!empty($item->mdata['short'])) {
					$remain -= $item->mdata['uq'];
				}
			}
		}

		$this->mdata['sort_remain'] = $remain;
		$this->update('meta');
	}

	public function beforeSave()
	{
		if (empty($this->no)) $this->no = $this->genNo();
		$this->meta = empty($this->mdata) ? '' : json_encode($this->mdata);

		return true;
	}

	public function afterFind()
	{
		if (!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
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

		$criteria = new CDbCriteria;
		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.no', $this->no);
		$criteria->compare('t.date', $this->date);
		$criteria->compare('t.status', $this->status);
		$criteria->compare('t.type', $this->type);
		$criteria->compare('t.org_id', $this->org_id);
		$criteria->compare('t.shelf_number', $this->shelf_number);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 't.id DESC',
			),
			'pagination' => $pgn ? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WmsTaskMap the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

}