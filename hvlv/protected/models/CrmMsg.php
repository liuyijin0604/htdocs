<?php

/**
 * This is the model class for table "crm_msg".
 *
 * The followings are the available columns in table 'crm_msg':
 * @property integer $id
 * @property integer $crm_id
 * @property integer $operator_id
 * @property string $wechat_id
 * @property string $wechat_name
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 * @property string $close_time
 * @property string $meta;
 */

class CrmMsg extends CActiveRecord
{

	public $mdata;

	public static $states = array(
		1 => 'active',
		2 => 'assigned',
		99 => 'closed',
		100 => 'dup',
	);

	const STATUS_ACTIVE = 1;
	const STATUS_ASSIGNED = 2;
	const STATUS_CLOSED = 99;
	const STATUS_DUP = 100;

	public function tableName() {
		return 'crm_msg';
	}

	public function rules() {
		return array(
			array('wechat_id, wechat_name, status', 'required'),
			array('crm_id, operator_id, status', 'numerical', 'integerOnly' => true),
			array('id, crm_id, operator_id, wechat_id, wechat_name, status, create_time, update_time, close_time, meta', 'safe', 'on' => 'search'),
		);
	}

	public function relations() {
		return array(
			'lines' => array(self::HAS_MANY, 'CrmMsgLine', 'crm_msg_id', 'order' => 'lines.id ASC'),
		);
	}

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'crm_id' => 'Crm',
			'operator_id' => 'Operator',
			'wechat_id' => 'Wechat ID',
			'wechat_name' => 'Wechat',
			'status' => 'Status',
			'create_time' => 'Create Time',
			'updata_time' => 'Update Time',
			'close_time' => 'Close Time',
			'meta' => 'Meta',
		);
	}

	public function search() {
		$criteria = new CDbCriteria;
		$criteria->compare('id', $this->id);
		$criteria->compare('crm_id', $this->crm_id);
		$criteria->compare('operator_id', $this->operator_id);
		$criteria->compare('wechat_id', $this->wechat_id);
		$criteria->compare('wechat_name', $this->wechat_name);
		$criteria->compare('status', $this->status);
		$criteria->compare('create_time', $this->create_time);
		$criteria->compare('update_time', $this->update_time);
		$criteria->compare('close_time', $this->close_time);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	public function beforeSave() {
		if (!empty($this->mdata)) $this->meta = json_encode($this->mdata);
		return true;
	}

	public function afterFind() {
		if (!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return true;
	}

	public function getStatus() {
		return Yii::t(strtolower(__CLASS__), empty(self::$states[$this->status])? '' : self::$states[$this->status]);
	}

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

}