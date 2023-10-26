<?php

/**
 * This is the model class for table "air_freight_invoice_reconciliation".
 *
 * The followings are the available columns in table 'air_freight_invoice_reconciliation':
 * @property string $id
 * @property integer $supplier_id
 * * @property integer $history_id
 * @property string $invoice_no
 * @property string $awb
 * @property string $date
 * @property string $due
 * @property string $eta
 * @property string $etd
 * @property string $created
 * @property string $cnee
 * @property string $cnor
 * @property string $origin
 * @property string $dest
 * @property string $weight
 * @property string $charge_weight
 * @property string $volume
 * @property string $subtotal
 * @property string $gst
 * @property string $total
 * @property string $model
 * @property integer $fid
 * @property integer $op
 * @property integer $matched_result
 * @property string $meta
 */
class AFInvoiceReconciliation extends CActiveRecord
{
	const MATCHED_RESULT_NOT_DONE = 0;
	const MATCHED_RESULT_SUCCESS = 1;
	const MATCHED_RESULT_WRONG_AWB_NOTFOUND = 2;
	const MATCHED_RESULT_WRONG_REJECTED = 3;
	const MATCHED_RESULT_WRONG_LINES_COUNT_NOTMATCHED = 4;
	const MATCHED_RESULT_WRONG_LINES_GLCODE_NOTMATCHED = 5;
	const MATCHED_RESULT_WRONG_DIFF_TOOBIG_PREIMPORT = 8;
	const MATCHED_RESULT_WRONG_DIFF_TOOBIG = 9; // compare to accrual not acceptable
	const MATCHED_RESULT_POST_BY_GENERAL = 10; // the bill will be posted by general cost
	const MATCHED_RESULT_WRONG_INVOICE_DIFF = 11;
	const MATCHED_RESULT_SUCCESS_ACCRUAL = 12;

	public static $matched_error_msg = array(
		self::MATCHED_RESULT_WRONG_AWB_NOTFOUND => 'Not matched yet',
		self::MATCHED_RESULT_SUCCESS => 'Matched successfully',
		self::MATCHED_RESULT_WRONG_AWB_NOTFOUND => 'Related Job / Consol not found',
		self::MATCHED_RESULT_WRONG_LINES_COUNT_NOTMATCHED => 'Cost Lines not equal',
		self::MATCHED_RESULT_WRONG_LINES_GLCODE_NOTMATCHED => 'Cost Lines GL Code not matched',
		self::MATCHED_RESULT_WRONG_DIFF_TOOBIG_PREIMPORT => 'Pre-imported, Cost Line actual and accrual amount diff too much',
		self::MATCHED_RESULT_WRONG_DIFF_TOOBIG => 'Cost Line actual and accrual amount diff too much',
		self::MATCHED_RESULT_WRONG_REJECTED => 'Invoice has been rejected',
		self::MATCHED_RESULT_POST_BY_GENERAL => 'Post by General Cost',
		self::MATCHED_RESULT_WRONG_INVOICE_DIFF => 'No invoice or amount is wrong',
		self::MATCHED_RESULT_SUCCESS_ACCRUAL => 'Matched accrual successfully',
	);

	public $checkMatchedResult,$op_name,$note,$mdata,$shipno,$inv;

	public function getMatchedErrMessage(){
		if ( isset(AFInvoiceReconciliation::$matched_error_msg[$this->matched_result]) ) return  AFInvoiceReconciliation::$matched_error_msg[$this->matched_result] . (!empty($this->mdata['nt']) ? ', ' . $this->mdata['nt'] : '');
		return 'Unknown Error';
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'air_freight_invoice_reconciliation';
	}

	public function getLastLog(){
		$log = Log::getLast($this);
		if ( !empty($log) ) {
			return $log->getExtra();
		}
		return '';
	}

	private function dashAwb($awb) {
		if (is_numeric(substr($awb, 0, 1))) {
			$awb = str_replace('-', '', $awb);
			return substr($awb, 0, 3) . '-' . substr($awb, 3);
		}
		return $awb;
	}

	public function getAwbView() {
		$result = '';
		if (in_array($this->supplier_id, array_merge([954, 964, 1008], Org::$airports))) {
			if (!empty($this->model)) {
				$result = '<a href="' . Yii::app()->createUrl($this->model . '/update', ["id" => $this->fid]) . '" class="tab_link" title="' . $this->dashAwb($this->awb) . '">' . $this->dashAwb($this->awb) . '</a>';
			} else {
				$result = $this->dashAwb($this->awb);
			}
		} else if ($this->supplier_id == 1133) {
			if (!empty($this->mdata['model'])) {
				foreach ($this->mdata['model'] as $model) {
					if (!empty($model['model'])) {
						if (!empty($result)) $result .= ' ';
						$result .= '<a href="' . Yii::app()->createUrl($model['model'] . '/update', ["id" => $model['id']]) . '" class="tab_link" title="' . $this->dashAwb($model['awb']) . '">' . $this->dashAwb($model['awb']) . '</a>';
					} else {
						if (!empty($result)) $result .= ' ';
						$result .= '<span style="color:red">' . $this->dashAwb($model['awb']) . '</span>';
					}
				}
			} else if (!empty($this->mdata['import_model'])) {
				foreach ($this->mdata['import_model'] as $model) {
					if (!empty($model['model'])) {
						if (!empty($result)) $result .= ' ';
						$result .= '<a href="' . Yii::app()->createUrl($model['model'] . '/update', ["id" => $model['id']]) . '" class="tab_link" title="' . $this->dashAwb($model['awb']) . '">' . $this->dashAwb($model['awb']) . '</a>';
					} else {
						if (!empty($result)) $result .= ' ';
						$result .= '<span style="color:red">' . $this->dashAwb($model['awb']) . '</span>';
					}
				}
			}
		} else if (in_array($this->supplier_id, Org::$brokers)) {
			if (!empty($this->model)) {
				return '<a href="' . Yii::app()->createUrl($this->model . '/update', ["id" => $this->fid]) . '" class="tab_link" title="' . $this->dashAwb($this->awb) . '">' . $this->dashAwb($this->awb) . '</a>';
			} else {
				$consol = Consol::model()->find('awb like :awb1 OR awb like :awb2', [':awb1' => '%' . $this->awb . '%', ':awb2' => '%' . $this->dashAwb($this->awb) . '%']);
				if (!empty($consol)) {
					return '<a href="' . Yii::app()->createUrl($consol->getType() . '/update', ["id" => $consol->id]) . '" class="tab_link" title="' . $consol->awb . '">' . $consol->awb . '</a>';
				}
				return $this->dashAwb($this->awb);
			}
		} else {
			$result = $this->awb;
		}
		return $result;
	}

	public function getShipmentView()
	{
		if (in_array($this->supplier_id, Org::$brokers)) {
			if (empty($this->shipno)) {
				return '';
			} else {
				$shipment = Shipment::model()->find('ref = :shipno OR hbn = :shipno', [':shipno' => $this->shipno]);
				if (!empty($shipment)) {
					return '<a href="' . Yii::app()->createUrl('imParcel/update', ['id' => $shipment->id]) . '" title="' . $this->shipno . '" class="tab_link">' . $this->shipno . '</a>';
				} else {
					return $this->shipno;
				}
			}
		}
		return '';
	}

	public function getInvView()
	{
		if (in_array($this->supplier_id, Org::$brokers)) {
			if (empty($this->mdata['invoice'])) {
				return '';
			} else {
				$invoice = Invoice::model()->findByPk($this->mdata['invoice']);
				if (!empty($invoice)) {
					return '<a href="' . Yii::app()->createUrl('invoice/print', ['id' => $invoice->id]) . '" title="' . $invoice->no . '" target="blank">' . $invoice->no . '</a>';
				}
			}
		}
	}

	public function notFoundAwb() {
		if ($this->supplier_id == 1133) {
			if (!empty($this->mdata['model'])) {
				foreach ($this->mdata['model'] as $model) {
					if (empty($model['model'])) {
						return $model['awb'];
					}
				}
			} else if (!empty($this->mdata['import_model'])) {
				foreach ($this->mdata['import_model'] as $model) {
					if (empty($model['model'])) {
						return $model['awb'];
					}
				}
			} else {
				return true;
			}
		} else if (in_array($this->supplier_id, [954, 964])) {
			if (empty($this->awb)) {
				return true;
			}
		}
		return false;
	}

	public function updateAmount()
	{
		$this->subtotal = 0;
		$this->gst = 0;
		foreach ($this->lines as $line) {
			$this->subtotal += $line->amount;
			$this->gst += $line->gst;
		}
		$this->total = $this->subtotal + $this->gst;
		$this->update('subtotal', 'gst', 'total');
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('supplier_id, history_id,fid,op,matched_result', 'numerical', 'integerOnly'=>true),
			array('invoice_no', 'length', 'max'=>20),
			array('awb', 'length', 'max'=>25),
			array('cnee, cnor', 'length', 'max'=>100),
			array('origin, dest', 'length', 'max'=>200),
			array('weight, charge_weight, volume, subtotal, gst, total', 'length', 'max'=>10),
			array('model', 'length', 'max'=>30),
			array('date, due, eta, etd, created, meta,op_name', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, supplier_id, history_id,fid,op,op_name,model,invoice_no, awb, date, due, eta, etd, created, cnee, cnor, origin, dest, weight, charge_weight, volume, subtotal, gst, total, matched_result, meta', 'safe', 'on'=>'search'),
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
			'lines' => array(self::HAS_MANY,'AFInvoiceReconciliationLine','invoice_id'),
			'history' => array(self::BELONGS_TO, 'AFInvoiceReconciliationHistory', 'history_id'),
			'user' => array(self::BELONGS_TO,'User','op'),
			'invoice' => array(self::HAS_ONE, 'BillingInvoice', ['billing_cref' => 'invoice_no']),
			'org' => array(self::BELONGS_TO, 'Org', 'supplier_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'supplier_id' => 'Supplier',
			'history_id' => 'Import ID',
			'invoice_no' => 'Invoice No',
			'awb' => 'Awb',
			'date' => 'Date',
			'due' => 'Due',
			'eta' => 'Eta',
			'etd' => 'Etd',
			'created' => 'Created',
			'cnee' => 'Cnee',
			'cnor' => 'Cnor',
			'origin' => 'Origin',
			'dest' => 'Dest',
			'weight' => 'Weight',
			'charge_weight' => 'Charge Weight',
			'volume' => 'Volume',
			'subtotal' => 'Subtotal',
			'gst' => 'Gst',
			'total' => 'Total',
			'model' => 'Model',
			'fid' => 'Fid',
			'op_name' => 'Operator',
			'matched_result' => 'Matched Result',
			'note' => 'Note',
			'meta' => 'Meta',
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
	public function search($pgn = true, $ps = 20)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('history_id',$this->history_id);
		$criteria->compare('supplier_id',$this->supplier_id);
		$criteria->compare('op',$this->op,false);
		$criteria->compare('invoice_no',$this->invoice_no,true);
		$criteria->compare('awb', str_replace('-', '', $this->awb), true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('due',$this->due,true);
		$criteria->compare('eta',$this->eta,true);
		$criteria->compare('etd',$this->etd,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('cnee',$this->cnee,true);
		$criteria->compare('cnor',$this->cnor,true);
		$criteria->compare('origin',$this->origin,true);
		$criteria->compare('dest',$this->dest,true);
		$criteria->compare('weight',$this->weight,true);
		$criteria->compare('charge_weight',$this->charge_weight,true);
		$criteria->compare('volume',$this->volume,true);
		$criteria->compare('subtotal',$this->subtotal,true);
		$criteria->compare('gst',$this->gst,true);
		$criteria->compare('total',$this->total,true);
		$criteria->compare('model',$this->model,true);
		$criteria->compare('fid',$this->fid);
		$criteria->compare('meta',$this->meta,true);

		$with = array();
		if ( !empty($this->op_name) ) {
			$with[] = 'user';
			$criteria->compare('user.fname',$this->op_name,true);
			$criteria->addSearchCondition('user.lname',$this->op_name,true,'OR','LIKE');
		}

		if ( $this->checkMatchedResult > 0 ) {
			$criteria->compare('t.matched_result' , '>'.$this->checkMatchedResult);
		} else {
			$criteria->compare('matched_result',$this->matched_result,false);
		}

		if ( !empty($with) ) {
			$criteria->with = array_unique($with);
		   $criteria->together = true;
		}

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=> 't.op DESC, t.id ASC',
			),
			'pagination' => $pgn ? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	public function beforeSave() {
		if (!empty($this->mdata)) $this->meta = json_encode($this->mdata);
		if ($this->matched_result == self::MATCHED_RESULT_NOT_DONE) $this->matched_result = self::MATCHED_RESULT_WRONG_AWB_NOTFOUND;
		return true;
	}

	public function afterFind() {
		if (!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		if (!empty($this->mdata['shipno'])) $this->shipno = $this->mdata['shipno'];
		return true;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AFInvoiceReconciliation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
