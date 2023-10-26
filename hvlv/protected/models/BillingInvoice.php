<?php

/**
 * This is the model class for table "billing_invoice".
 *
 * The followings are the available columns in table 'billing_invoice':
 * @property int $id
 * @property string $billing_cref
 * @property int $org_id
 * @property string $inv_id
 * @property string $consol_id
 * @property string $shipment_id
 * @property string $billing_subtotal
 * @property string $billing_gst
 * @property string $invoice_subtotal
 * @property string $invoice_gst
 * @property string $diff
 * @property string $status
 * @property string $date
 * @property string $note
 */

class BillingInvoice extends CActiveRecord
{

	public $billing_total, $invoice_total, $reconcile_id, $custom_log_note;

	public $nolog = false;

	public static $states = array(
		10 => 'Need Approve',
		20 => 'Matched',
		30 => 'Posted',
	);

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'billing_invoice';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('billing_cref', 'required'),
			array('id, org_id, billing_cref, inv_id, consol_id, shipment_id, status, date, note', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, org_id, billing_cref, inv_id, consol_id, shipment_id, billing_subtotal, billing_gst, billing_total, invoice_subtotal, invoice_gst, invoice_total, diff, status, reconcile_id, date, note', 'safe', 'on' => 'search'),
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
			'org' => array(self::BELONGS_TO, 'Org', 'org_id'),
			'invoice' => array(self::BELONGS_TO, 'Invoice', 'inv_id'),
			'consol' => array(self::BELONGS_TO, 'Consol', 'consol_id'),
			'shipment' => array(self::BELONGS_TO, 'Shipment', 'shipment_id'),
			'billing' => array(self::BELONGS_TO, 'Billing', ['billing_cref' => 'billing_cref', 'org_id' => 'org_id']),
			'lines' => array(self::HAS_MANY, 'BillingLine', ['billing_cref' => 'billing_cref', 'org_id' => 'org_id'], 'on' => 'lines.status != 11'),
			'af' => array(self::HAS_ONE, 'AFInvoiceReconciliation', ['invoice_no' => 'billing_cref', 'supplier_id' => 'org_id']),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'org_id' => 'Org',
			'billing_cref' => 'Billing No.',
			'consol_id' => 'AWB',
			'shipment_id' => 'Shipment',
			'inv_id' => 'Invoice No.',
			'status' => 'Status',
			'date' => 'Date',
			'note' => 'Note',
		);
	}

	public function beforeSave()
	{
		$this->diff = $this->invoice_subtotal - $this->billing_subtotal;
		if (empty($this->status)) {
			$this->checkStatus(false);
		}
		if (empty($this->date) || $this->date == '0000-00-00') {
			$this->checkDate(false);
		}
		return true;
	}

	public function afterSave()
	{
		if (!$this->nolog && !empty($this)) {
			$extra = empty($this->custom_log_note) ? array() : array('note' => $this->custom_log_note);
			Log::add($this, $this->isNewRecord ? 3 : 4, array_merge(array('status' => $this->status != 10 ? $this->getStatus() : 'need approve'), $extra));
		}
	}

	public function afterFind()
	{
		$this->billing_total = $this->billing_subtotal + $this->billing_gst;
		$this->invoice_total = $this->invoice_subtotal + $this->invoice_gst;
		if (empty($this->status) || $this->status != $this->checkStatus(false)) {
			$this->checkStatus();
		}
		if (empty($this->date) || $this->date == '0000-00-00') {
			$this->checkDate();
		}
	}

	public function getConsol()
	{
		if (empty($this->consol)) {
			return '';
		} else {
			return "<a href=\"" . Yii::app()->createUrl($this->consol->getType() . "/update", ["id" => $this->consol->id]) . "\" class=\"tab_link\" data-win-class=\"XL\" title=\"" . $this->consol->awb . "\">" . $this->consol->awb . "</a>";
		}
	}

	public function getShipment()
	{
		if (empty($this->shipment)) {
			return '';
		} else {
			if (!empty($this->shipment->ref)) {
				$ref = $this->shipment->ref;
			} else {
				$ref = $this->shipment->hbn;
			}
			return "<a href=\"" . Yii::app()->createUrl(Shipment::$types[$this->shipment->type] . "/update", ["id" => $this->shipment->id]) . "\" class=\"tab_link\" title=\"" . $ref . "\">" . $ref . "</a>";
		}
	}

	public function getInvoice()
	{
		if (empty($this->invoice)) {
			return '';
		} else {
			return "<a href=\"" . Yii::app()->createUrl("invoice/print", ["id" => $this->invoice->id]) . "\" target=\"_blank\" title=\"" . $this->invoice->no . "\">" . $this->invoice->no . "</a>" . ($this->invoice->status == 8 ? '(Fully Credited)' : '');
		}
	}

	public function checkInvoice()
	{
		$invoice = null;
		$shipment = Shipment::model()->findByPk($this->shipment_id);
		$consol = Consol::model()->findByPk($this->consol_id);

		// first check CA
		if (!empty($shipment) && !empty($consol)) {
			$invoice = Invoice::model()->find(['condition' => 'pid = :pid AND consol_id = :consol_id AND type = :type AND status NOT IN (8, 10)', 'params' => [':pid' => $shipment->id, ':consol_id' => $consol->id, ':type' => Invoice::INVOICE_TYPE_CASUAL], 'order' => 'id DESC']);
			if (!empty($invoice)) {
				[$this->inv_id, $this->invoice_subtotal, $this->invoice_gst] = $invoice->getCustomBrokerAmount();
			}
		}
		// then check CA fully credit
		if (empty($invoice) && !empty($shipment) && !empty($consol)) {
			$invoice = Invoice::model()->find(['condition' => 'pid = :pid AND consol_id = :consol_id AND type = :type AND status = 8', 'params' => [':pid' => $shipment->id, ':consol_id' => $consol->id, ':type' => Invoice::INVOICE_TYPE_CASUAL], 'order' => 'id DESC']);
			if (!empty($invoice)) {
				[$this->inv_id, $this->invoice_subtotal, $this->invoice_gst] = $invoice->getCustomBrokerAmount();
			}
		}

		// then check CA for dmawbconsol shipment
		if (empty($invoice) && !empty($consol) && get_class($consol) == 'DmawbConsol' && count($consol->shipments) == 1) {
			$invoice = Invoice::model()->find(['condition' => 'pid = :pid AND consol_id = :consol_id AND type = :type AND status NOT IN (8, 10)', 'params' => [':pid' => $consol->shipments[0]->id, ':consol_id' => $consol->id, ':type' => Invoice::INVOICE_TYPE_CASUAL], 'order' => 'id DESC']);
			if (!empty($invoice)) {
				[$this->inv_id, $this->invoice_subtotal, $this->invoice_gst] = $invoice->getCustomBrokerAmount();
			}
		}

		// then check DI
		if (empty($invoice) && !empty($shipment) && !empty($consol)) {
			$invoices = Invoice::model()->findAll('consol_id = :consol_id AND type = :type AND status NOT IN (8, 10)', [':consol_id' => $consol->id, ':type' => Invoice::INVOICE_TYPE_DIRECT_MAWB]);
			foreach ($invoices as $invoice) {
				[$this->inv_id, $this->invoice_subtotal, $this->invoice_gst] = $invoice->getCustomBrokerAmount();
				if (!empty($this->invoice_subtotal)) {
					break;
				}
			}
		}

		// then check OT
		if (empty($invoice) && !empty($shipment) && !empty($consol)) {
			$invoices = Invoice::model()->findAll('consol_id = :consol_id AND type = :type AND status NOT IN (8, 10)', [':consol_id' => $consol->id, ':type' => Invoice::INVOICE_TYPE_OTHERS]);
			foreach ($invoices as $invoice) {
				[$this->inv_id, $this->invoice_subtotal, $this->invoice_gst] = $invoice->getCustomBrokerAmount();
				if (!empty($this->invoice_subtotal)) {
					break;
				}
			}
		}
	}

	public function getStatus()
	{
		if ($this->status != 10) {
			return Yii::t(strtolower(__CLASS__), empty(self::$states[$this->status]) ? '' : self::$states[$this->status]);
		} else {
			if (empty($this->af)) {
				return 'No import invoice';
			} else {
				return '<a class="ajax_link" title="Approve" href="' . Yii::app()->createUrl('billing/approveAF', ['afid' => $this->af->id, 'cref' => $this->billing_cref]) . '"><div style="background-position: 0 0" class="icon"></div> Approve</a>';
			}
		}
	}

	public function checkStatus($save = true)
	{
		if (empty($this->af)) {
			if (!empty($this->billing) && $this->billing->sync_xero == 1) {
				$this->status = 30;
			} else {
				if ($this->billing_subtotal + 20 > $this->invoice_subtotal) {
					$this->status = 10;
				} else {
					$this->status = 20;
				}
			}
		} else {
			if ($this->af->matched_result == 1) {
				if (!empty($this->billing) && $this->billing->sync_xero == 1) {
					$this->status = 30;
				} else {
					$this->status = 20;
				}
			} else {
				if ($this->billing_subtotal + 20 > $this->invoice_subtotal) {
					$this->status = 10;
				} else {
					$this->status = 20;
					$af = AFInvoiceReconciliation::model()->find('invoice_no = :no AND supplier_id = :org_id', [':no' => $this->billing_cref, ':org_id' => $this->org_id]);
					$af->matched_result = 1;
					$af->update('matched_result');
				}
			}
		}
		if ($save) {
			$this->update('status');
		}

		return $this->status;
	}

	public function checkDate($save = true)
	{
		if (!empty($this->af)) {
			$this->date = $this->af->date;
		}
		if ((empty($this->date) || $this->date == '0000-00-00') && !empty($this->billing)) {
			$this->date = $this->billing->date;
		}
		if ((empty($this->date) || $this->date == '0000-00-00') && !empty($this->lines)) {
			$this->date = $this->lines[0]->date;
		}
		if ($save) {
			$this->update('date');
		}
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

		$criteria->compare('org_id', $this->org_id, true);
		$criteria->compare('billing_cref', $this->billing_cref, true);
		$criteria->compare('billing_subtotal', $this->billing_subtotal, true);
		$criteria->compare('billing_gst', $this->billing_gst, true);
		$criteria->compare('invoice_subtotal', $this->invoice_subtotal, true);
		$criteria->compare('invoice_gst', $this->invoice_gst, true);
		$criteria->compare('diff', $this->diff, true);
		$criteria->compare('t.status', $this->status);
		$criteria->compare('t.date', $this->date, true);
		$criteria->compare('t.note', $this->note, true);

		$with = [];
		if (!empty($this->shipment_id)) {
			$with[] = 'shipment';
			$criteria->addCondition('shipment.hbn like "%' . $this->shipment_id . '%" OR shipment.ref like "%' . $this->shipment_id . '%"');
		}
		if (!empty($this->consol_id)) {
			$with[] = 'consol';
			$criteria->addCondition('consol.no like "%' . $this->consol_id . '%" OR consol.awb like "%' . $this->consol_id . '%"');
		}
		if (!empty($this->inv_id)) {
			$with[] = 'invoice';
			$criteria->addCondition('invoice.no like "%' . $this->inv_id . '%"');
		}
		if (!empty($this->reconcile_id)) {
			$with[] = 'af.history';
			$criteria->compare('history.id', $this->reconcile_id);
		}

		if (!empty($with)) {
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if ($ec) {
			$criteria->mergeWith($ec);
		}

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 't.inv_id DESC',
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
	 * @return WmsTask the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

}
