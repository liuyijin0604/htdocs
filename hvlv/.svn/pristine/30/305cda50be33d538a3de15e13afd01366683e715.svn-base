<?php

/**
 * This is the model class for table "wms_prod".
 *
 * The followings are the available columns in table 'wms_prod':
 * @property string $id
 * @property integer $status
 * @property integer $type
 * @property integer $org_id
 * @property string $name
 * @property string $name_zh
 * @property string $ean
 * @property string $brand
 * @property string $model
 * @property string $dim
 * @property double $cbm
 * @property double $weight
 * @property string $bwf
 * @property string $meta
 */
class WmsProd extends CActiveRecord
{

	public static $types = array(
		10 => 'Physical Product',
		20 => 'Material',
		50 => 'Kit/Set',
		70 => 'Virtual Product',
	);

	const WMS_PROD_PHYSICAL = 10;
	const WMS_PROD_KIT = 50;

	public static $states = array(
		1 => 'Active',
		0 => 'Inactive',
	);

	public $nolog = false;
	public $custom_log_note = '';
	public $mdata = array();
	public $dims = array();
	public $order = '';
	public $sku;
	public $prodids = [];
	public $stock_qty, $stock_res, $stock_avail;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_prod';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status, type, name, ean, weight', 'required'),
			array('name_zh, brand, model, dim,org_id, bwf, meta, sku, stock_qty, stock_res, stock_avail', 'safe'),
			array('status, type', 'numerical', 'integerOnly' => true),
			array('cbm, weight', 'numerical'),
			array('name, name_zh', 'length', 'max' => 200),
			array('brand, model, dim', 'length', 'max' => 100),
			array('ean', 'unique', 'message' => 'EAN Already exists'),
			array('ean', 'length', 'max' => 50),
			array('bwf', 'length', 'max' => 11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, status, type, name, name_zh, ean, brand, model, dim, cbm, weight, bwf,org_id, meta,prodids,order, sku, stock_qty, stock_res, stock_avail', 'safe', 'on' => 'search'),
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
			'packs' => array(self::HAS_MANY, 'WmsProdPack', 'prod_id'),
			'orgs' => array(self::HAS_MANY, 'WmsProdOrg', 'prod_id'),
			'items' => array(self::HAS_MANY, 'WmsProdKit', 'kit_id'),
			'stocks' => array(self::HAS_MANY, 'WmsStock', 'prod_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'status' => 'Status',
			'type' => 'Type',
			'name' => 'Name',
			'name_zh' => '中文品名',
			'ean' => 'EAN',
			'sku' => 'SKU',
			'brand' => 'Brand',
			'model' => 'Model',
			'dim' => 'Dim (cm)',
			'cbm' => 'Volume',
			'weight' => 'Weight',
			'bwf' => 'Bwf',
			'meta' => 'Meta',
			'order' => 'Display Order',
		);
	}

	public function getType()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$types[$this->type]) ? '' : self::$types[$this->type]);
	}

	public function getStatus()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$states[$this->status]) ? '' : self::$states[$this->status]);
	}

	public function afterSave()
	{
		if (!$this->nolog && !empty($this)) {
			$extra = empty($this->custom_log_note) ? array() : array('note' => $this->custom_log_note);
			Log::add($this, $this->isNewRecord ? 3 : 4, array_merge(array('status' => $this->getStatus()), $extra));
		}
	}

	public function beforeSave()
	{
		if (!empty($this->mdata)) {
			$this->meta = json_encode($this->mdata);
		}

		if (!empty($this->dims)) {
			$this->dim = json_encode($this->dims);
			if ($this->cbm == 0) {
				$this->cbm = floatval(@$this->dims['w']) * floatval(@$this->dims['h']) * floatval(@$this->dims['d']);
			}

		}

		$o = self::model()->findByPk($this->id);
		if (!empty($o) && !empty($o->ean) && $this->ean != $o->ean && $this->ifHasStock()) {
			$this->addError('id', 'Still have inventory of this product, cannot change EAN');
			return false;
		}

		return true;
	}

	public function afterFind()
	{
		if (!empty($this->meta)) {
			$this->mdata = json_decode($this->meta, true);
		}

		if (!empty($this->dim)) {
			$this->dims = json_decode($this->dim, true);
		}

		return true;
	}

	public function ifHasStock($org_id = null)
	{
		$condition = 'prod_id = :prod_id AND qty > 0';
		$params = [':prod_id' => $this->id];
		if ($org_id) {
			$condition .= ' AND org_id = :org_id';
			$params[':org_id'] = $org_id;
		}
		$stock = WmsStock::model()->find($condition, $params);
		return !empty($stock);
	}

	public function uq2cq($q, $type = 'ceil')
	{
		$pps = WmsProdPack::model()->findAll(['condition' => 'type = 10 AND prod_id = :pid', 'params' => [':pid' => $this->id], 'group' => 'qty']);
		if (empty($pps) || sizeof($pps) > 1 || $pps[0]->qty == 0) {
			return 0;
		}

		return $type == 'ceil' ? ceil(intval($q) / intval($pps[0]->qty)) : floor(intval($q) / intval($pps[0]->qty));
	}

	public function getCBM($q)
	{
		$pps = WmsProdPack::model()->findAll(['condition' => 'type = 10 AND prod_id = :pid', 'params' => [':pid' => $this->id], 'group' => 'qty']);
		if (empty($pps) || sizeof($pps) > 1) {
			return 0;
		}

		$cbm = $pps[0]->getCBM();
		if ($cbm == 0) {
			return 0;
		}

		return $cbm * ceil($q / $pps[0]->qty);
	}

	public function getCqWeight($cq, $uq = 0)
	{
		$pps = WmsProdPack::model()->findAll(['condition' => 'type = 10 AND prod_id = :pid AND weight > 0', 'params' => [':pid' => $this->id], 'group' => 'qty']);
		if (!$uq > 0) {
			if (!empty($pss)) {
				foreach ($pss as $ps) {
					if ($ps->qty == floor($uq / $cq)) {
						break;
					}

				}
			}
		} else if (!empty($pps)) {
			$ps = $pps[0];
		}
		if (empty($ps)) {
			return 0;
		}

		return $cq * $ps->weight;
	}

	public function hasPkgWt()
	{
		return WmsProdPack::model()->count(['condition' => 'type = 10 AND prod_id = :pid AND weight > 0', 'params' => [':pid' => $this->id]]) > 0;
	}

	public function availQty($org_id)
	{
		$stocks = WmsStock::model()->findAll('org_id = :org_id AND prod_id = :prod_id', [':org_id' => $org_id, ':prod_id' => $this->id]);
		$qty = 0;
		foreach ($stocks as $stock) {
			$qty += $stock->qty - $stock->qty_res;
		}
		return intval($qty);
	}

	public function getStockQty($org_id, $type)
	{
		$stocks = WmsStock::model()->findAll('org_id = :org_id AND prod_id = :prod_id', [':org_id' => $org_id, ':prod_id' => $this->id]);
		$qty = 0;
		foreach ($stocks as $stock) {
			if ($type == 'avail') {
				$qty += $stock->getQty() - $stock->qty_res;
			} else if ($type == 'qty') {
				$qty += $stock->getQty();
			} else if ($type == 'qty_res') {
				$qty += $stock->qty_res;
			}
		}
		return intval($qty);
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

		if (empty($this->prodids)) {
			$criteria->compare('t.id', $this->id);
		} else {
			$criteria->addInCondition('t.id', $this->prodids);
		}

		$criteria->compare('status', $this->status);
		$criteria->compare('type', $this->type);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('name_zh', $this->name_zh, true);
		$criteria->compare('ean', $this->ean, true);
		$criteria->compare('brand', $this->brand, true);
		$criteria->compare('model', $this->model, true);
		$criteria->compare('dim', $this->dim, true);
		$criteria->compare('cbm', $this->cbm);
		$criteria->compare('weight', $this->weight);
		$criteria->compare('bwf', $this->bwf, true);
		$criteria->compare('meta', $this->meta, true);

		$with = [];
		if (!empty($this->orgs)) {
			$with[] = 'orgs';
			$criteria->compare('orgs.org_id', $this->orgs->id);
		}
		if (!empty($this->sku)) {
			$with[] = 'orgs';
			$criteria->addCondition('(orgs.sku LIKE :sku)');
			$criteria->params[':sku'] = '%' . $this->sku . '%';
		}
		if (!empty($this->stock_qty)) {
			$with[] = 'stocks';
			$criteria->group = 'stocks.prod_id, stocks.org_id';
			$criteria->having = 'SUM(stocks.qty) ' . $this->stock_qty;
		}
		if (!empty($this->stock_res)) {
			$with[] = 'stocks';
			$criteria->group = 'stocks.prod_id, stocks.org_id';
			$criteria->having = 'SUM(stocks.qty_res) ' . $this->stock_res;
		}
		if (!empty($this->stock_avail)) {
			$with[] = 'stocks';
			$criteria->group = 'stocks.prod_id, stocks.org_id';
			$criteria->having = 'SUM(stocks.qty - stocks.qty_res) ' . $this->stock_avail;
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
				'defaultOrder' => 't.id DESC',
			),
			'pagination' => $pgn ? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	public static function getName($id)
	{
		$model = WmsProd::model()->findByPk($id);
		if (!empty($model)) {
			return $model->name;
		} else {
			return '';
		}
	}

	public function getSku($org_id = '')
	{
		if (empty($org_id)) {
			if (isset($this->orgs[0])) {
				return $this->orgs[0]->sku;
			} else {
				return '';
			}
		} else {
			$org = WmsProdOrg::model()->find('org_id = :org_id AND prod_id = :prod_id', [':org_id' => $org_id, ':prod_id' => $this->id]);
			if (!empty($org)) {
				return $org->sku;
			} else {
				return '';
			}
		}
	}

	public function getDim()
	{
		if (!empty($this->dims)) {
			return (intval(@$this->dims['h']) * 10) . ' x ' . (intval(@$this->dims['w']) * 10) . ' x ' . (intval(@$this->dims['d']) * 10);
		}
	}

	public function getMinStockAlert($org_id = '')
	{
		if (empty($org_id)) {
			return '';
		} else {
			$r = WmsProdOrg::model()->find('prod_id = :pid AND org_id = :oid', [':pid' => $this->id, ':oid' => $org_id]);
			return intval(@$r->mdata['min_stock_alert']);
		}
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WmsProd the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
