<?php

/**
 * This is the model class for table "invoice_template".
 *
 * The followings are the available columns in table 'invoice_template':
 * @property string $id
 * @property string $ref
 * @property string $dpt_id
 * @property integer $dpmt
 * @property integer $currency
 * @property string $total
 * @property string $gst
 * @property integer $status
 * @property string $meta
 * @property integer $type
 */
class InvoiceTemplate extends CActiveRecord
{

	const INVOICE_TEMPLATE_STATUS_INACTIVE = 0;
	const INVOICE_TEMPLATE_STATUS_ACTIVE = 1;
	public static $states = array(
		1 => 'Active',
		0 => 'Inactive',
	);

	public static $types = array(
		40 => 'Other',
		75 => 'WMS Service',
	);

	public $mdata = [], $total2;

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'invoice_template';
	}

	public function rules()
	{
		return array(
			array('ref, currency, dpt_id, dpmt, status, type', 'required', 'on' => 'create'),
			array('ref, currency, dpt_id, dpmt, status, type', 'required', 'on' => 'update'),
			array('ref, currency, dpt_id, dpmt, total, gst, status, meta, type', 'safe'),
			array('ref, currency, dpt_id, dpmt, total, gst, status, meta, type', 'safe', 'on' => 'search'),
		);
	}

	public function relations()
	{
		return array(
			'lines' => array(self::HAS_MANY, 'InvTempLine', 'temp_id', 'order' => 'lines.id ASC'),
			'branch' => array(self::BELONGS_TO, 'Org', 'dpt_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'ref' => 'Ref',
			'dpt_id' => 'Branch',
			'dpmt' => 'Department',
			'currency' => 'Currency',
			'total' => 'SubTotal',
			'gst' => 'GST',
			'total2' => 'Total',
			'status' => 'Status',
			'meta' => 'Meta',
			'type' => 'Type',
		);
	}

	public function getCurrency()
	{
		return Yii::t(strtolower(__CLASS__), Invoice::$currencies[$this->currency]);
	}
	
	public function getStatus()
	{
		if ( isset(self::$states[$this->status]) ) {
			return Yii::t(strtolower(__CLASS__), self::$states[$this->status]);
		} else {
			return '';
		}
	}

	public function getDpmt()
	{
		if ( isset(Invoice::$dpmts[$this->dpmt]) ) {
			return Yii::t(strtolower(__CLASS__), Invoice::$dpmts[$this->dpmt]);
		} else {
			return 0;
		}
	}

	public function getBranch()
	{
		return empty($this->dpt_id) ? '' : $this->branch->shortName(1);
	}

	public function getType()
	{
		return Yii::t(strtolower(__CLASS__), self::$currencies[$this->type]);
	}

	public function getTotal()
	{
		$total = 0;
		$gst = 0;
		foreach ($this->lines as $line) {
			$total += $line->rate * $line->qty;
			if ($line->gst == 'OUTPUT') {
				$gst += $line->rate * $line->qty * 0.1;
			}
		}
		$this->total = number_format(round($total * 100) / 100, 2, '.', '');
		$this->gst = number_format(round($gst * 100) / 100, 2, '.', '');

		return $this->total;
	}

	public function getGST()
	{
		return $this->gst;
	}

	protected function afterFind()
	{
		if (!empty($this->meta)) $this->mdata = json_encode($this->meta);
		$this->total2 = $this->total + $this->gst;
	}

	protected function beforeSave()
	{
		if (!empty($this->mdata)) $this->meta = json_decode($this->mdata, true);
		$this->getTotal();

		return true;
	}

	protected function afterSave()
	{
		if (!empty($this)) {
			$extra = empty($this->custom_log_note) ? array() : array('note' => $this->custom_log_note);
			$opname = 'Cron Or API';
			if ( isset(Yii::app()->user) ) $opname = Yii::app()->user->name;
			if ( $this->isNewRecord ) {
				Log::add($this, Log::LOG_TYPE_CREATE, array_merge(['notes' => $opname . ' create'], $extra));
			} else {
				Log::add($this, Log::LOG_TYPE_UPDATE, array_merge(['notes' => $opname . ' update status is : ' . $this->getStatus()], $extra));
			}
		}
	}

	public function search($pgn = true, $ps = 30, $odr = 't.id DESC', $ec = false)
	{
		$criteria = new CDbCriteria;
		$criteria->compare('t.ref', $this->ref, true);
		$criteria->compare('t.dpt_id', $this->dpt_id);
		$criteria->compare('t.dpmt', $this->dpmt);
		$criteria->compare('t.currency', $this->currency);
		$criteria->compare('t.total', $this->total, true);
		$criteria->compare('t.gst', $this->gst, true);
		$criteria->compare('t.status', $this->status, true);

		if ($ec) {
			$criteria->mergeWidth($ec);
		}

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => $odr,
			),
			'pagination' => $pgn ? array(
				'pageSize' => $ps,
			) : false,
		));
	}

}