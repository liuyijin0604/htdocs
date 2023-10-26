<?php

/**
 * This is the model class for table "inv_temp_org".
 *
 * The followings are the available columns in table 'inv_temp_org':
 * @property string $id
 * @property integer $temp_id
 * @property integer $org_id
 * @property string $start
 * @property string $end
 * @property string $freq
 * @property integer $status
 */
class InvTempOrg extends CActiveRecord
{

	const INVOICE_TEMPLATE_FREQ_BY_DAY = 1;
	const INVOICE_TEMPLATE_FREQ_BY_WEEK = 2;
	const INVOICE_TEMPLATE_FREQ_BY_MONTH = 3;
	const INVOICE_TEMPLATE_FREQ_BY_NEXT_MONTH = 4;
	public static $freq = array(
		1 => 'By Day',
		2 => 'By Week',
		3 => 'By Month',
		4 => 'By Next Month'
	);

	const INVOICE_TEMPLATE_STATUS_INACTIVE = 0;
	const INVOICE_TEMPLATE_STATUS_ACTIVE = 1;
	public static $states = array(
		0 => 'Inactive',
		1 => 'Active',
	);

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'inv_temp_org';
	}

	public function rules()
	{
		return array(
			array('temp_id, org_id, start, end, freq, status', 'required', 'on' => 'create'),
			array('temp_id, org_id, start, end, freq, status', 'safe'),
			array('temp_id, org_id, start, end, freq, status', 'safe', 'on' => 'search'),
		);
	}

	public function relations()
	{
		return array(
			'template' => array(self::BELONGS_TO, 'InvoiceTemplate', 'temp_id'),
			'org' => array(self::BELONGS_TO, 'Org', 'org_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'temp_id' => 'Invoice Template',
			'org_id' => 'Org',
			'start' => 'Start Date',
			'end' => 'End Date',
			'freq' => 'Frequency',
			'status' => 'Status',
		);
	}

	public function getFreq()
	{
		if ( isset(self::$freq[$this->freq]) ) {
			return Yii::t(strtolower(__CLASS__), self::$freq[$this->freq]);
		} else {
			return '';
		}
	}

	public function getStatus()
	{
		if ( isset(self::$states[$this->status]) ) {
			return Yii::t(strtolower(__CLASS__), self::$states[$this->status]);
		} else {
			return '';
		}
	}

	public function search()
	{
		$criteria = new CDbCriteria;
		$criteria->compare('id', $this->id);
		$criteria->compare('temp_id', $this->temp_id);
		$criteria->compare('org_id', $this->org_id);
		$criteria->compare('start', $this->start);
		$criteria->compare('end', $this->end);
		$criteria->compare('freq', $this->freq);
		$criteria->compare('status', $this->status);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => 100,
			),
		));
	}

}