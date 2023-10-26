<?php

/**
 * This is the model class for table "crm_msg_line".
 *
 * The followings are the available columns in table 'crm_msg_line':
 * @property integer $id
 * @property integer $crm_msg_id
 * @property integer $wechat_msg_id
 * @property integer $type
 * @property string $time
 * @property string $meta;
 */

class CrmMsgLine extends CActiveRecord
{

	public $mdata;

	public static $types = array(
		1 => 'text',
		2 => 'pic',
		3 => 'voice',
		8 => 'mass',
		9 => 'auto',
		10 => 'ticket'
	);

	const TYPE_TEXT = 1;
	const TYPE_PIC = 2;
	const TYPE_VOICE = 3;
	const TYPE_MASS = 8;
	const TYPE_AUTO = 9;
	const TYPE_TICKET = 10;

	public $op_name, $title;

	public function tableName() {
		return 'crm_msg_line';
	}

	public function rules() {
		return array(
			array('crm_msg_id, wechat_msg_id, type, time', 'required'),
			array('crm_msg_id, wechat_msg_id, type', 'numerical', 'integerOnly' => true),
			array('id, crm_msg_id, wechat_msg_id, type, time, meta', 'safe', 'on' => 'search'),
		);
	}

	public function relations() {
		return array(
			'crmMsg' => array(self::BELONGS_TO, 'CrmMsg', 'crm_msg_id'),
		);
	}

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'crm_msg_id' => 'Crm Msg',
			'wechat_msg_id' => 'Wechat Msg',
			'type' => 'Type',
			'time' => 'Time',
			'meta' => 'Meta',
		);
	}

	public static function amr2m4a($amr_string){
		return oPDF::procPipe('/usr/local/bin/ffmpeg -loglevel panic -i - -c aac -b:a 12k -movflags frag_keyframe+empty_moov -f mp4 -', $amr_string);
	}

	public function search() {
		$criteria = new CDbCriteria;
		$criteria->compare('id', $this->id);
		$criteria->compare('crm_msg_id', $this->crm_msg_id);
		$criteria->compare('wechat_msg_id', $this->wechat_msg_id);
		$criteria->compare('type', $this->type);
		$criteria->compare('time', $this->time);

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

	public function getType() {
		return Yii::t(strtolower(__CLASS__), empty(self::$states[$this->status])? '' : self::$states[$this->status]);
	}

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
}