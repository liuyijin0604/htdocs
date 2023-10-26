<?php

/**
 * This is the model class for table "air_freight_invoice_reconciliation_history".
 *
 * The followings are the available columns in table 'air_freight_invoice_reconciliation_history':
 * @property string $id
 * @property string $created
 * @property string $hash
 * @property string $meta
 * @property integer $op_id
 * @property integer $invoice_total
 * @property integer $success
 * @property integer $failed
 * @property integer $attached_file_id
 * @property integer $report_file_id
 */
class AFInvoiceReconciliationHistory extends CActiveRecord
{
	public $totalFailed, $totalSuccess, $totalSyncXeroSuccess, $totalSyncXeroFailed, $totalSyncXeroTodo, $totalDelete, $total, $org;

	public $mdata;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'air_freight_invoice_reconciliation_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('op_id, invoice_total, success, failed, attached_file_id, report_file_id', 'numerical', 'integerOnly' => true),
			array('created, org', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, created, hash,op_id, invoice_total, meta,success, failed, attached_file_id, report_file_id, org', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @param $filename
	 * @return bool
	 */
	public static function fileImportedBefore($filename)
	{
		$hash = md5($filename);
		$existing = AFInvoiceReconciliationHistory::model()->find('hash = :hash', [':hash' => $hash]);
		if (empty($existing)) {
			return false;
		}

		return true;
	}

	/**
	 * save specified billing data into our DB
	 * @param $billing
	 * @return AFInvoiceReconciliation|static
	 */
	public function saveBilling(&$billing, $supplier_id)
	{
		/*
		 *   'number' => $invoiceNumber,
		'awb' => $awb,
		'date' => $invoiceDate,
		'due' => $invoiceDue,
		'cnee' => $consginee,
		'cnor' => $consignor,
		'cref' => $cref,
		'orgin' => $orgin,
		'dest' => $dest,
		'eta' => $eta,
		'etd' => $etd,
		'weight' => $weight,
		'chargeWeight' => $chargeWeight,
		'volume' => $volume,
		'subtotal' => $subtotal,
		'gst' => $gst,
		'total' => $total,
		'linetotal' => $allLineTotal,
		'lines' => $items
		 */
		$afIReconModel = AFInvoiceReconciliation::model()->find('invoice_no = :ino AND supplier_id = :supplier_id', [':ino' => $billing['number'], ':supplier_id' => $supplier_id]);
		if (empty($afIReconModel)) {
			$afIReconModel = new AFInvoiceReconciliation();
			$afIReconModel->created = date('Y-m-d');
			$afIReconModel->invoice_no = substr($billing['number'], 0, 19); // maximum 19
			// $afIReconModel->supplier_id = 954; // currently fixed as priority cargo
			$afIReconModel->supplier_id = $supplier_id;
		} else {
			if ($afIReconModel->history_id != $this->id) {
				$afIReconModel->addError('id', 'already uploaded');
				return $afIReconModel;
			}
			// delete old lines
			AFInvoiceReconciliationLine::model()->deleteAll('invoice_id = :iid', [':iid' => $afIReconModel->id]);
		}
		$invoice_type = (substr($billing['number'], 0, 4) == '0002' || (in_array($supplier_id, Org::$brokers) && $billing['type'])) ? 1 : 0;
		$afIReconModel->history_id = $this->id;
		$afIReconModel->awb = substr($billing['awb'], 0, 24);
		$afIReconModel->due = $billing['due'];
		$afIReconModel->eta = $billing['eta'];
		$afIReconModel->etd = $billing['etd'];
		$afIReconModel->date = $billing['date'];
		$afIReconModel->cnee = $billing['cnee'];
		$afIReconModel->cnor = $billing['cnor'];
		$afIReconModel->origin = $billing['orgin'];
		$afIReconModel->dest = $billing['dest'];
		$afIReconModel->weight = number_format(floatval($billing['weight']), 2, '.', '');
		$afIReconModel->charge_weight = number_format(floatval($billing['chargeWeight']), 2, '.', '');
		$afIReconModel->volume = number_format(floatval($billing['volume']), 2, '.', '');
		$afIReconModel->subtotal = number_format(($invoice_type ? -1 : 1) * floatval($billing['subtotal']), 2, '.', '');
		$afIReconModel->total = number_format(floatval(($invoice_type ? -1 : 1) * $billing['total']), 2, '.', '');
		$afIReconModel->gst = number_format(($invoice_type ? -1 : 1) * floatval($billing['gst']), 2, '.', '');
		$afIReconModel->matched_result = 0; // not matched yet
		// for broker, e.g. fyn
		if (!empty($billing['shipno'])) {
			$afIReconModel->mdata['shipno'] = $billing['shipno'];
		}
		$afIReconModel->save();

		// save lines now
		//  $items[] = array('desc' => $desc, 'gst' => $gst, 'chargecode' => $chargeCode, 'glcode' => $glcode,
		// 'value' => $value, 'subtotal' => $lineSubTotal, 'gstamount' => $gstAmount, 'total' => $lineTotal);
		foreach ($billing['lines'] as $line) {
			$afIReconLine = new AFInvoiceReconciliationLine();
			$afIReconLine->invoice_id = $afIReconModel->id;
			$afIReconLine->desc = substr($line['desc'], 0, 200);
			$afIReconLine->gst = number_format(($invoice_type ? -1 : 1) * floatval($line['gstamount']), 2, '.', '');
			$glcode = strtoupper($line['glcode']);
			if (empty($glcode)) {
				$chargecode = ChargeItemType::model()->find('name = :name', array(':name' => $afIReconLine->desc));
				if ($chargecode) {
					$glcode = $chargecode->code;
				} else {
					$words = explode(' ', $afIReconLine->desc);
					if (count($words) >= 2) {
						for ($i = 0; $i < count($words) - 1; $i++) {
							$chargecode = ChargeItemType::model()->find('name like :name', array(':name' => '%' . $words[$i] . ' ' . $words[$i + 1] . '%'));
							if ($chargecode) {
								$glcode = $chargecode->code;
								break;
							} else {
								$chargecode = ChargeItemType::model()->find('name like :name', array(':name' => '%' . $words[$i] . '%'));
								if ($chargecode) {
									$glcode = $chargecode->code;
									break;
								}
							}
						}
					} else {
						$chargecode = ChargeItemType::model()->find('name like :name', array(':name' => '%' . $words[0] . '%'));
						if ($chargecode) {
							$glcode = $chargecode->code;
						}
					}
					if (preg_match('/screening/i', $afIReconLine->desc)) {
						$chargecode = ChargeItemType::model()->find('name like "x-ray" or name like "xray"');
						if ($chargecode) {
							$glcode = $chargecode->code;
						}
					}
				}
			}
			$afIReconLine->glcode = $glcode;
			$afIReconLine->amount = number_format(($invoice_type ? -1 : 1) * floatval($line['subtotal']), 2, '.', '');
			$afIReconLine->save();

			$afIReconModel->refresh();
			$afIReconModel->updateAmount();
		}

		return $afIReconModel;
	}

	public function beforeSave()
	{
		$this->meta = empty($this->mdata) ? '' : json_encode($this->mdata);

		return true;
	}

	public function afterFind()
	{
		$this->totalFailed = $this->getMatchedFailedCount();
		$this->totalSuccess = $this->getMatchedSuccessCount();
		$this->invoice_total = $this->totalSuccess + $this->totalFailed;
		$this->total = 0;
		$afs = AFInvoiceReconciliation::model()->findAll('history_id = :id AND matched_result != :status', array(':id' => $this->id, ':status' => AFInvoiceReconciliation::MATCHED_RESULT_WRONG_REJECTED));
		foreach ($afs as $af) {
			$this->total += floatval($af->total);
		}

		if (!empty($this->meta)) {
			$this->mdata = json_decode($this->meta, true);
		}
	}

	/**
	 * @return string
	 */
	public function getAttachedFileLink()
	{
		$fileInfo = FileRepo::model()->findByPk($this->attached_file_id);
		if (empty($fileInfo)) {
			return '';
		}

		return DIRECTORY_SEPARATOR . 'filerepo' . DIRECTORY_SEPARATOR . $fileInfo->hash . DIRECTORY_SEPARATOR . $fileInfo->name;
	}

	public function getAttachedFileName()
	{
		$fileInfo = FileRepo::model()->findByPk($this->attached_file_id);
		if (empty($fileInfo)) {
			return '';
		}

		if (!empty($this->af) && in_array($this->af[0]->supplier_id, Org::$brokers)) {
			return $fileInfo->name;
		} else {
			return $fileInfo->name;
		}
	}

	/**
	 * @return mixed
	 */
	public function getMatchedSuccessCount()
	{
		$sql = 'SELECT count(id) AS t FROM air_freight_invoice_reconciliation WHERE matched_result = 1';
		$sql .= " AND history_id = " . $this->id;
		$c = Yii::app()->db->createCommand($sql);
		return $c->queryScalar();
	}

	/**
	 * @return mixed
	 */
	public function getMatchedFailedCount()
	{
		$sql = 'SELECT count(id) AS t FROM air_freight_invoice_reconciliation WHERE matched_result > 1';
		$sql .= " AND history_id = " . $this->id;
		$c = Yii::app()->db->createCommand($sql);
		return $c->queryScalar();
	}

	public function getMatchedRejectedCount()
	{
		$sql = 'SELECT count(id) AS t FROM air_freight_invoice_reconciliation WHERE matched_result = 3';
		$sql .= " AND history_id = " . $this->id;
		$c = Yii::app()->db->createCommand($sql);
		return $c->queryScalar();
	}

	/**
	 * @return mixed
	 */
	public function getPostByGeneralCostCount()
	{
		$sql = 'SELECT count(id) AS t FROM air_freight_invoice_reconciliation WHERE matched_result = ' . AFInvoiceReconciliation::MATCHED_RESULT_POST_BY_GENERAL;
		$sql .= " AND history_id = " . $this->id;
		$c = Yii::app()->db->createCommand($sql);
		return $c->queryScalar();
	}

	/**
	 * @return string
	 */
	public function getSyncXeroInfo()
	{
		if ($this->totalSyncXeroSuccess <= 0) {
			$info = $this->getTotalSyncXeroCountInfo();
			$this->totalSyncXeroSuccess = $info['success'];
			$this->totalSyncXeroFailed = $info['failed'];
			$this->totalSyncXeroTodo = $info['todo'];
		}
		return 'Success(' . $this->totalSyncXeroSuccess . ') Failed(' . $this->totalSyncXeroFailed . ') Todo(' . $this->totalSyncXeroTodo . ')';
	}

	/**
	 * get sync xero success count
	 * @return mixed
	 */
	public function getTotalSyncXeroSuccess()
	{
		if ($this->totalSyncXeroSuccess <= 0) {
			$info = $this->getTotalSyncXeroCountInfo();
			$this->totalSyncXeroSuccess = $info['success'];
			$this->totalSyncXeroFailed = $info['failed'];
			$this->totalSyncXeroTodo = $info['todo'];
		}
		return $this->totalSyncXeroSuccess;
	}

	/**
	 * get sync xero failed count
	 * @return mixed
	 */
	public function getTotalSyncXeroFailed()
	{
		if ($this->totalSyncXeroFailed <= 0) {
			$info = $this->getTotalSyncXeroCountInfo();
			$this->totalSyncXeroSuccess = $info['success'];
			$this->totalSyncXeroFailed = $info['failed'];
			$this->totalSyncXeroTodo = $info['todo'];
		}
		return $this->totalSyncXeroFailed;
	}

	public function getTotalSyncXeroTodo()
	{
		if ($this->totalSyncXeroTodo <= 0) {
			$info = $this->getTotalSyncXeroCountInfo();
			$this->totalSyncXeroSuccess = $info['success'];
			$this->totalSyncXeroFailed = $info['failed'];
			$this->totalSyncXeroTodo = $info['todo'];			
		}
		return $this->totalSyncXeroTodo;
	}

	/**
	 * get total sync with xero successfully count
	 * @return array
	 */
	public function getTotalSyncXeroCountInfo()
	{
		$syncInfo = array('success' => 0, 'failed' => 0, 'todo' => 0);
		$criteria = new CDbCriteria();
		$criteria->addCondition('history_id = ' . $this->id);
		// if (!empty($this->af[0]->supplier_id) && !in_array($this->af[0]->supplier_id, Org::$brokers)) {
			$criteria->addInCondition('matched_result', [AFInvoiceReconciliation::MATCHED_RESULT_SUCCESS]); //,AFInvoiceReconciliation::MATCHED_RESULT_POST_BY_GENERAL]);
		// }
		$sBillings = AFInvoiceReconciliation::model()->findAll($criteria);
		foreach ($sBillings as $billing) {
			if (empty($billing->model)) {
				$bls = BillingLine::model()->findAll('billing_cref = :billing_cref AND status != 11 AND org_id = :org_id', array(':billing_cref' => $billing->invoice_no, ':org_id' => $billing->supplier_id));
				if (!empty($bls)) {
					$flag = 1;
					foreach ($bls as $bl) {
						if ($bl->sync_xero == 0) {
							$flag = 0;
						} else if ($bl->sync_xero == 2) {
							$flag = 2;
						} 
					}
					if ($flag == 1) {
						$syncInfo['success'] ++;
					} else if ($flag == 2) {
						$syncInfo['failed'] ++;
					} else if ($flag == 0) {
						$syncInfo['todo'] ++;
					}

				} else {
					$bill = Billing::model()->find('billing_cref = :bref AND status != 11 AND org_id = :org_id', [':bref' => $billing->invoice_no, ':org_id' => $billing->supplier_id]);
					if (!empty($bill)) {
						if ($bill->sync_xero == 1) {
							$syncInfo['success'] ++;
						} else if ($bill->sync_xero == 2) {
							$syncInfo['failed'] ++;
						} else if ($bill->sync_xero == 0) {
							$syncInfo['todo'] ++;
						}
					} else {
						$syncInfo['todo'] ++;
					}
				}
				continue;
			}
			// loop each imported invoice
			// step 1
			// try to get all billing lines based on imported invoiced matched EdiJob or ExcoConsol
			// $r = new $billing->model;
			// $pModel = $r::model()->findByPk($billing->fid);
			// if (!empty($pModel)) {
				// only for not sync with xero billing lines
				$pBillingLines = BillingLine::model()->findAll('billing_cref = :bref AND status != 11 AND org_id = :org_id', [':bref' => $billing->invoice_no, ':org_id' => $billing->supplier_id]);
				if (!empty($pBillingLines)) {
					$flag = 1;
					foreach ($pBillingLines as $bl) {
						if ($bl->sync_xero == 0) {
							$flag = 0;
						} else if ($bl->sync_xero == 2) {
							$flag = 2;
						} 
					}
					if ($flag == 1) {
						$syncInfo['success'] ++;
					} else if ($flag == 2) {
						$syncInfo['failed'] ++;
					} else if ($flag == 0) {
						$syncInfo['todo'] ++;
					}
				}
			// }
		}
		return $syncInfo;
	}

	/**
	 * @return string
	 */
	public function getReportFileLink()
	{
		$fileInfo = FileRepo::model()->findByPk($this->report_file_id);
		if (empty($fileInfo)) {
			return '';
		}

		return DIRECTORY_SEPARATOR . 'filerepo' . DIRECTORY_SEPARATOR . $fileInfo->hash . DIRECTORY_SEPARATOR . $fileInfo->name;
	}

	public function getInvoicePeriod()
	{
		if (empty($this->mdata['invoice_period'])) {
			$first = date('Y-m-d');
			$last = date('1970-01-01');
			$afs = AFInvoiceReconciliation::model()->findAll('history_id = :hid', [':hid' => $this->id]);
			foreach ($afs as $af) {
				if (strtotime($af->date) < strtotime($first)) {
					$first = $af->date;
				}
				if (strtotime($af->date) > strtotime($last)) {
					$last = $af->date;
				}
			}
			$this->mdata['invoice_period'] = $first . ' ~ ' . $last;
			$this->update('meta');
		}

		return $this->mdata['invoice_period'];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'op' => array(self::BELONGS_TO, 'User', 'op_id'),
			'af' => array(self::HAS_MANY, 'AFInvoiceReconciliation', 'history_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'created' => 'Created',
			'hash' => 'Hash',
			'op_id' => 'Operator',
			'invoice_total' => 'Invoices',
			'success' => 'Success',
			'failed' => 'Failed',
			'totalSyncXeroSuccess' => 'Sync Xero',
			'meta' => 'Memo',
			'attached_file_id' => 'Attached File',
			'report_file_id' => 'Report File',
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
	public function search($ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;
		$criteria->compare('id', $this->id, true);
		$criteria->compare('created', $this->created, true);
		$criteria->compare('op_id', $this->op_id);
		$criteria->compare('invoice_total', $this->invoice_total);
		$criteria->compare('success', $this->success);
		$criteria->compare('failed', $this->failed);
		$criteria->compare('hash', $this->hash, false);
		$criteria->compare('attached_file_id', $this->attached_file_id);
		$criteria->compare('report_file_id', $this->report_file_id);

		if ($ec) {
			$ec->together = true;
			$ec->group = 't.id';
			$criteria->mergeWith($ec);
		}

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 't.created DESC, t.id DESC',
			),
			'pagination' => array(
				'pageSize' => '50',
			),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AFInvoiceReconciliationHistory the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
