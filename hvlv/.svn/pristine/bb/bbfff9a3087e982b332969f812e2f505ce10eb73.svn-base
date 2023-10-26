<?php

/**
 * This is the model class for table "wms_task_batch".
 *
 * The followings are the available columns in table 'wms_task_batch':
 * @property string $id
 * @property string $date
 * @property int $batch
 * @property int $task_id
 * @property int $box_number
 * @property int $status
 * @property string $meta
 * @property int $type
 * @property int $shelf_number
 * @property int $org_id
 * @property int $batch_id
 */
class WmsTaskBatch extends CActiveRecord
{

	public static $states = array(
		10 => 'New',
		40 => 'Hold',
		99 => 'Finished',
		100 => 'Cancelled',
	);

	const WMS_TASK_BATCH_STATUS_NEW = 10;
	const WMS_TASK_BATCH_STATUS_HOLD = 40;
	const WMS_TASK_BATCH_STATUS_COMPLETED = 99;
	const WMS_TASK_BATCH_STATUS_CANCELLED = 100;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_task_batch';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, batch, task_id, box_number, status, type, shelf_number, org_id', 'required'),
			array('date, batch, task_id, box_number, status, type, shelf_number, meta, org_id, batch_id', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('date, batch, task_id, box_number, status, type, shelf_number, meta, org_id, batch_id', 'safe', 'on' => 'search'),
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
			'parent' => array(self::BELONGS_TO, 'WmsBatch', 'batch_id'),
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
			'task_id' => 'WmsTask ID',
			'box_number' => 'Box No.',
			'status' => 'Status',
			'type' => 'Type',
			'shelf_number' => 'Shelf No.',
			'org_id' => 'Org',
		);
	}

	public static function getLastBatch($type = WmsBatch::WMS_BATCH_TYPE_MULTI)
	{
		$batch = self::model()->find(['condition' => 'type = :type', 'params' => [':type' => $type], 'order' => 'id DESC']);
		return $batch;
	}

	public static function getCurrentBatch($type = null)
	{
		if (!empty($type)) {
			$batch = self::model()->find(['condition' => 'type = :type AND status = :status', 'params' => [':type' => $type, ':status' => self::WMS_TASK_BATCH_STATUS_NEW]]);
		} else {
			$batch = self::model()->find(['condition' => 'status = :status', 'params' => [':status' => self::WMS_TASK_BATCH_STATUS_NEW]]);
		}
		return $batch;
	}

	public function getNo()
	{
		return date('ymd', strtotime($this->date)) . ' - ' . sprintf('%02d', $this->batch) . ' - ' . WmsBatch::$types[$this->type];
	}

	public static function findEmptyShelf()
	{
		for ($i = 1; $i <= WmsBatch::WMS_BATCH_SHELF_MAX; $i++) {
			if (self::ifEmptyfindEmptyShelfShelf($i)) {
				return $i;
			}
		}

		return 0;
	}

	public static function ifEmptyfindEmptyShelfShelf($no)
	{
		$batch = self::model()->findAll(['condition' => 'status = :status AND shelf_number = :no', 'params' => [':status' => self::WMS_TASK_BATCH_STATUS_NEW, ':no' => $no]]);
		if (empty($batch)) return true;
		else return false;
	}

	public function beforeSave()
	{
		if (empty($this->batch_id)) {
			$batch = WmsBatch::model()->find('date = :date AND type = :type AND no = :no', [':date' => $this->date, ':type' => $this->type, ':no' => date('ymd', strtotime($this->date)) . ' - ' . sprintf('%02d', $this->batch) . ' - ' . WmsBatch::$types[$this->type]]);
			if (empty($batch)) {
				$batch = new WmsBatch;
				$batch->date = $this->date;
				$batch->type = $this->type;
				$batch->status = 10;
				$batch->org_id = $this->org_id;
				$batch->save();
			}

			$this->batch_id = $batch->id;
		}

		return true;
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
		$criteria->compare('t.date', $this->date);
		$criteria->compare('t.batch', $this->batch);
		$criteria->compare('t.task_id', $this->task_id);
		$criteria->compare('t.box_number', $this->box_number);
		$criteria->compare('t.status', $this->status);
		$criteria->compare('t.type', $this->type);
		$criteria->compare('t.shelf_number', $this->shelf_number);
		$criteria->compare('t.org_id', $this->org_id);

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