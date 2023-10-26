<?php

/**
 * This is the model class for table "inv_temp_line".
 *
 * The followings are the available columns in table 'inv_temp_line':
 * @property string $id
 * @property string $temp_id
 * @property string $ccode
 * @property string $desc
 * @property string $rate
 * @property string $qty
 * @property string $gst
 * @property string $meta
 */
class InvTempLine extends CActiveRecord
{

	public $mdata = [], $amount_total, $gst_total;

	static public $ccodes = [];

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'inv_temp_line';
	}

	public function rules()
	{
		return array(
			array('temp_id, ccode, rate, gst, desc, qty', 'required', 'on' => 'created'),
			array('temp_id, ccode, rate, gst, desc, qty', 'safe'),
			array('temp_id, ccode, rate, gst, desc, qty', 'safe', 'on' => 'search'),
		);
	}

	public function relations()
	{
		return array(
			'template' => array(self::BELONGS_TO, 'InvoiceTemplate', 'temp_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'temp_id' => 'Template',
			'ccode' => 'Charge Code',
			'desc' => 'Description',
			'rate' => 'Rate',
			'qty' => 'Qty',
			'gst' => 'GST',
			'meta' => 'Meta',
		);
	}

	public function shouldAddGST()
	{
		if (!empty($this->gst)) {
			if (in_array($this->gst, ['OUTPUT'])) {
				return true;
			}
		}
		return false;
	}

	public function getGSTValue()
	{
		if ($this->shouldAddGST()) {
			return round($this->rate * $this->qty * 10 / 100, 2);
		}
		return 0;
	}

	protected function afterFind()
	{
		if (!empty($this->meta)) $this->mdata = json_encode($this->meta);
		$this->amount_total = $this->rate * $this->qty;
	}

	protected function beforeSave()
	{
		if (!empty($this->mdata)) $this->meta = json_decode($this->mdata);
		return true;
	}

	protected function afterSave()
	{
		$this->template->getTotal();
		$this->template->update('total', 'gst');
	}

	protected function afterDelete()
	{
		$this->template->getTotal();
		$this->template->update('total', 'gst');
	}

	public function search()
	{
		$criteria = new CDbCriteria;
		$criteria->compare('id', $this->id);
		$criteria->compare('temp_id', $this->temp_id);
		$criteria->compare('desc', $this->desc);
		$criteria->compare('ccode', $this->ccode);
		$criteria->compare('rate', $this->rate);
		$criteria->compare('qty', $this->qty);
		$criteria->compare('gst', $this->gst);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => 100,
			),
		));
	}

}