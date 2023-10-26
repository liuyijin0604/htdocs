<?php

/**
 * This is the model class for table "wms_stock".
 *
 * The followings are the available columns in table 'wms_stock':
 * @property string $id
 * @property string $prod_id
 * @property string $org_id
 * @property double $qty
 * @property double $qty_res
 * @property string $expiry
 * @property string $batch
 * @property string $sn
 * @property string $updated
 * @property string $dpt_id
 */
class WmsStock extends CActiveRecord
{

	public $cust_name, $prod_name, $prod_ean, $loc_code, $orgids, $edit_qty, $no_updated = false, $prod_sku;

	public static $bwfs = array(
		2, // hide in portal, because of prod is inactive or no ledgers
	);

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_stock';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('prod_id, org_id', 'required'),
			array('qty, qty_res, expiry, batch, sn, updated, prod_sku, dpt_id', 'safe'),
			array('expiry', 'default', 'setOnEmpty' => true, 'value' => null),
			array('qty, qty_res', 'numerical'),
			array('prod_id, org_id', 'length', 'max' => 11),
			array('batch, sn', 'length', 'max' => 50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, prod_id, org_id, qty, qty_res, expiry, batch, sn, updated, cust_name,orgids, prod_name, loc_code, prod_ean, prod_sku, dpt_id', 'safe', 'on' => 'search'),
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
			'customer' => array(self::BELONGS_TO, 'Org', 'org_id'),
			'prod' => array(self::BELONGS_TO, 'WmsProd', 'prod_id'),
			'locs' => array(self::HAS_MANY, 'WmsStockLocation', 'stock_id', 'on' => 'locs.qty > 0 AND location_id > 99'),
			'ledgers' => array(self::HAS_MANY, 'WmsStockLedger', 'stock_id'),
			'branch' => array(self::BELONGS_TO, 'Org', 'dpt_id'),
		);
	}

	public function getBestLocs($q)
	{
		$rs = WmsStockLocation::model()->findAll(['condition' => 'qty > 0 AND stock_id = :sid AND location_id > 99', 'params' => [':sid' => $this->id], 'order' => 'id asc']);
		$lwt = [];
		$ls = [];
		if (empty($rs)) {
			return [false, []];
		}

		foreach ($rs as $i => $r) {
			$wt = $r->loc->parent->wt;
			// $wt = 1;
			// if ($r->loc->parent->type == 30) {
			// 	$wt *= 10;
			// }

			// if ($r->qty > $q) {
			// 	$wt *= 2;
			// }

			$lwt[$r->id] = $wt;
			$ls[$r->id] = $r;
		}
		arsort($lwt);
		$t = 0;
		$r = [];
		$count = 1;
		foreach ($lwt as $k => $w) {
			$t += $ls[$k]->qty;
		}
		foreach ($lwt as $k => $w) {
			$r[] = [$ls[$k], $w];
			// if($t > floatval($q) * 2) break;
			// if (++$count > 10) {
			// 	break;
			// }

		}

		return [$t >= $q, $r];
	}

	public function getFirstLocs($q)
	{
		$rs = WmsStockLocation::model()->findAll(['condition' => 'qty > 0 AND stock_id = :sid AND location_id > 99', 'params' => [':sid' => $this->id], 'order' => 'id asc']);
		$lwt = [];
		$ls = [];
		if (empty($rs)) {
			return [false, []];
		}
		foreach ($rs as $i => $r) {
			$wt = $r->qty;
			$lwt[$r->id] = $wt;
			$ls[$r->id] = $r;
		}
		asort($lwt);
		$t = 0;
		$r = [];

		foreach ($lwt as $k => $w) {
			$t += $ls[$k]->qty;
		}
		foreach ($lwt as $k => $w) {
			$r[] = [$ls[$k], $w];
		}
		return $r;
	}

	public static function parseExpiry($ex)
	{
		$ex = trim($ex, '/-');
		$ex = preg_replace('/\s+/', '', $ex);

		if (preg_match('/^(\d{4})[\/\-]{1}(\d{1,2})(?:[\/\-]{1}(\d{1,2}))?$/', $ex, $m)) {
			$ex = $m[1] . '-' . $m[2] . '-' . (empty($m[3]) ? '01' : $m[3]);
		} elseif (preg_match('/^(?:(\d{1,2})[\/\-]{1})?(\d{1,2})[\/\-]{1}(\d{4})$/', $ex, $m)) {
			$ex = $m[3] . '-' . $m[2] . '-' . (empty($m[1]) ? '01' : $m[1]);
		} elseif (preg_match('/^(\d{1,2})\/(\d{2,4})$/', $ex, $m)) {
			if (strlen($m[2]) == 2) {
				$m[2] = '20' . $m[2];
			}

			$ex = $m[2] . '-' . $m[1] . '-01';
		} elseif (preg_match('/^(\w{3})\-(\d{2,4})$/', $ex, $m)) {
			if (strlen($m[2]) == 2) {
				$m[2] = '20' . $m[2];
			}

			$ex = $m[2] . '-' . $m[1] . '-01';
		}

		$ts = strtotime($ex);
		if ($ts === false) {
			return false;
		} else {
			return date('Y-m-d', $ts);
		}
	}

	protected function beforeSave()
	{
		if (empty($this->dpt_id)) {
			return false;
			//$this->dpt_id = 106;
		}

		if (!empty($this->expiry) && WmsProdOrg::ExpByMonth($this->prod_id, $this->org_id) && !in_array($this->org_id, [1440])) {
			$this->expiry = date('Y-m-01', strtotime($this->expiry));
		}
		if (!$this->no_updated) {
			$this->updated = date('Y-m-d H:i:s');
		}
		return true;
	}

	public function getCustSKU()
	{
		$r = WmsProdOrg::model()->find('prod_id = :pid AND org_id = :oid', [':pid' => $this->prod_id, ':oid' => $this->org_id]);
		return empty($r) ? '' : $r->sku;
	}

	public function getStockEB($ebm = 0)
	{
		if (empty($ebm)) {
			$ebm = WmsProdOrg::ExpBatMgmt($this->prod_id, $this->org_id);
		}

		switch ($ebm) {
			case 1:
				$r = $this->org_id . '-' . $this->prod_id . $this->expiry;
				break;
			case 2:
				$r = $this->org_id . '-' . $this->prod_id . $this->batch;
				break;
			case 9:
				$r = $this->org_id . '-' . $this->prod_id;
				break;
			default:
				$r = $this->org_id . '-' . $this->prod_id . $this->expiry . $this->batch;
				break;
		}
		return $r;
	}

	public static function creget($org_id, $itm, $dpt_id = null)
	{
		$q = 'org_id = :oid AND prod_id = :pid';
		$p = [':oid' => $org_id, ':pid' => $itm['gi']];
		// 2020-04-17 add mel
		if (!empty($dpt_id)) {
			$q .= ' AND dpt_id = :dpt_id';
			$p[':dpt_id'] = $dpt_id;
		}
		if (!empty($itm['ex'])) {
			$q .= ' AND expiry = :ex';
			$itm['ex'] = self::parseExpiry($itm['ex']);
			if (WmsProdOrg::ExpByMonth($itm['gi'], $org_id)) {
				$itm['ex'] = date('Y-m-01', strtotime($itm['ex']));
			}

			$p[':ex'] = $itm['ex'];
		} else {
			$q .= ' AND expiry IS NULL';
		}

		if (!empty($itm['bn'])) {
			$q .= ' AND batch = :bn';
			$p[':bn'] = $itm['bn'];
		}
		$s = self::model()->find(['condition' => $q, 'params' => $p, 'order' => 'id DESC']);
		if (empty($s)) {
			$s = new WmsStock;
			$s->org_id = $org_id;
			$s->prod_id = $itm['gi'];
			$s->dpt_id = $dpt_id;
			if (!empty($itm['ex'])) {
				$s->expiry = $itm['ex'];
			}

			if (!empty($itm['bn'])) {
				$s->batch = $itm['bn'];
			}

			$s->save();
		}
		return $s;
	}

	public function stockName($ean = false)
	{
		$ex = [];
		if (!empty($this->expiry)) {
			$ex[] = 'Exp: ' . $this->expiry;
		}

		if (!empty($this->batch)) {
			$ex[] = 'Bat: ' . $this->batch;
		}

		if (!$ean) {
			return $this->prod->name . (empty($ex) ? '' : ' (' . implode(', ', $ex) . ')');
		} else {
			return $this->prod->name . ' ' . $this->prod->ean . (empty($ex) ? '' : ' (' . implode(', ', $ex) . ')');
		}
	}

	// when use pallet out, only change pid from 1 to 2, no ledger
	// location_id != 2
	public function chargeUnit($date, $free_day = 0, $cbm = false, &$locs = false, $byweek = true){
		//stock balance as of number of free days before billing date
		$sbd = date('Y-m-d', strtotime($date.' -'.$free_day.' day'));
		$sql = 'SELECT location_id as lid, SUM(qty_in) - SUM(qty_out) as qty FROM wms_stock_ledger WHERE stock_id = :sid AND ts <  DATE_ADD(:sbd, INTERVAL 1 DAY) AND location_id != 2 GROUP BY location_id';
		$rs = Yii::app()->db->createCommand($sql)->bindValues([':sid' => $this->id, ':sbd' => $sbd])->queryAll();
		$sls = [];
		foreach ($rs as $r) {
			$location = WmsLocation::model()->findByPk($r['lid']);
			if ($location->pid == 2) continue;
			if ($r['lid'] < 100) continue;
			$sls[$r['lid']] = $r['qty'];
		}

		//check if stock out not free
		$stockOutNotFree = function($l)use($free_day, $sbd, $byweek){
			//get stock balance before free day - total out between free day and ledger date
			$sql = 'SELECT t1.qty - t2.qty as bal FROM (SELECT COALESCE(b1.qty, b2.qty) as qty FROM (SELECT 0 as qty) b1 LEFT JOIN (SELECT SUM(qty_in) - SUM(qty_out) as qty FROM wms_stock_ledger WHERE stock_id = :sid AND ts < :bfd AND location_id = :lid GROUP BY location_id) b2 ON 1=1) t1 INNER JOIN (SELECT COALESCE(d1.qty, d2.qty) as qty FROM (SELECT 0 as qty) d1 LEFT JOIN (SELECT SUM(qty_out) as qty FROM wms_stock_ledger WHERE stock_id = :sid AND ts < :ts AND ts >= :bfd AND location_id = :lid GROUP BY location_id) d2 ON 1=1) t2';

			//remaining balance
			$rbal = Yii::app()->db->createCommand($sql)->bindValues([':sid' => $this->id, ':lid' => $l['lid'], ':ts' => $l['ts'], ':bfd' => date('Y-m-d', strtotime($l['ts'].' -'.max($free_day - 1, 0).' day'))])->queryScalar() - $l['qty'];

			//out after sbd, if the difference < 0 should be free
			if(strtotime($l['ts']) > strtotime($sbd)){
				return $rbal < 0? $rbal : 0; 
			}

			if(!$byweek && $rbal <= 0){ //check last week out should be free
				$sql = 'SELECT ts FROM wms_stock_ledger WHERE location_id = :lid AND stock_id = :sid AND qty_in > 0 ORDER BY ts ASC LIMIT 1';
				$in_ts = Yii::app()->db->createCommand($sql)->bindValues([':sid' => $this->id, ':lid' => $l['lid']])->queryScalar();
				$ssd = strtotime($in_ts.' +'.$free_day.' day');
				if($ssd < strtotime($sbd) && date('N', $ssd) > date('N', strtotime($l['ts']))){
					return -$l['qty'];
				}
			}

			//remaining balance > ledger qty means stock was available before free days
			if($rbal >= 0) return $l['qty'];
			
			//partial availabel return difference, or 0 when all stock was in afer the free days;
			return $rbal >= -$l['qty']? -$rbal : 0;
		};

		//add back out not free after last billing date (last week) not after the sbd
		$lfd = date('Y-m-d', strtotime($sbd.' -7 day'));
		$sql = 'SELECT location_id as lid, qty_out as qty, ts FROM wms_stock_ledger WHERE ti_id > 0 AND stock_id = :sid AND ts >= :lfd AND ts < DATE_ADD(:dt, INTERVAL 1 DAY) AND location_id != 2';
		$rs = Yii::app()->db->createCommand($sql)->bindValues([':sid' => $this->id, ':dt' => $date, ':lfd' => $lfd])->queryAll();
		foreach ($rs as $r) {
			//check if stock out - in more than free days
			$location = WmsLocation::model()->findByPk($r['lid']);
			if ($location->pid == 2) continue;
			if (isset($sls[$r['lid']])) {
				$sonf = $stockOutNotFree($r);
				$sls[$r['lid']] += $sonf;
			}
		}

		$cu = [0, 0];
		foreach ($sls as $k => $v) {
			$cu[0] += $v;
			if ($v <= 0 || (is_array($locs) && in_array($k, $locs))) continue;
			$wl = WmsLocation::model()->findByPk($k);
			if ($cbm) {
				$cu[1] += $this->prod->getCBM($v);
				$cu[1] += ($wl->pid > 0 && $wl->parent->type == '30') ? 0 : 0.12;
			} else {
				$cu[1] += ($wl->pid > 0 && $wl->parent->type == '30') ? 0.1 : 1;
			}
			if (is_array($locs)) $locs[] = $k;
		}
		$cu[1] = sprintf('%0.3f', round($cu[1] * 1000) / 1000);

		return $cu;
	}

	// when use pallet out, only change pid from 1 to 2, no ledger
	// location_id != 2
	public function chargeUnitByFreeWeek($date, $free_week = 0, $cbm = false, &$locs = false)
	{
		//all in and out before free weeks
		$sql = 'SELECT location_id as lid, SUM(qty_in) - SUM(qty_out) as qty FROM wms_stock_ledger WHERE stock_id = :sid AND ts < DATE_ADD(DATE_SUB(:dt, INTERVAL :fw WEEK), INTERVAL 1 DAY) AND location_id != 2 GROUP BY location_id';
		$rs = Yii::app()->db->createCommand($sql)->bindValues([':sid' => $this->id, ':dt' => $date, ':fw' => $free_week])->queryAll();
		$sls = [];
		foreach ($rs as $r) {
			$location = WmsLocation::model()->findByPk($r['lid']);
			if ($location->pid == 2) continue;
			if ($r['lid'] < 100) continue;
			$sls[$r['lid']] = $r['qty'];
		}

		//out in the free weeks
		if ($free_week > 0) {
			$sql = 'SELECT location_id as lid, SUM(qty_out) as qty FROM wms_stock_ledger WHERE ti_id > 0 AND stock_id = :sid AND ts >= DATE_ADD(DATE_SUB(:dt, INTERVAL :fw WEEK), INTERVAL 1 DAY) AND ts < DATE_ADD(DATE_SUB(:dt, INTERVAL 1 WEEK), INTERVAL 1 DAY) AND location_id != 2 GROUP BY location_id';
			$rs = Yii::app()->db->createCommand($sql)->bindValues([':sid' => $this->id, ':dt' => $date, ':fw' => $free_week])->queryAll();
			foreach ($rs as $r) {
				$location = WmsLocation::model()->findByPk($r['lid']);
				if ($location->pid == 2) continue;
				if (isset($sls[$r['lid']])) {
					$sls[$r['lid']] -= $r['qty'];
				}
			}
		} else {
			//out last week +
			$sql = 'SELECT location_id as lid, SUM(qty_out) as qty FROM wms_stock_ledger WHERE ti_id > 0 AND stock_id = :sid AND ts >= DATE_ADD(DATE_SUB(:dt, INTERVAL 1 WEEK), INTERVAL 1 DAY) AND ts < DATE_ADD(:dt, INTERVAL 1 DAY) AND location_id != 2 GROUP BY location_id';
			$rs = Yii::app()->db->createCommand($sql)->bindValues([':sid' => $this->id, ':dt' => $date])->queryAll();
			foreach ($rs as $r) {
				$location = WmsLocation::model()->findByPk($r['lid']);
				if ($location->pid == 2) continue;
				if (isset($sls[$r['lid']])) {
					$sls[$r['lid']] += $r['qty'];
				}
			}
		}

		$cu = [0, 0];
		foreach ($sls as $k => $v) {
			$cu[0] += $v;
			if ($v <= 0 || (is_array($locs) && in_array($k, $locs))) continue;
			$wl = WmsLocation::model()->findByPk($k);
			if ($cbm) {
				$cu[1] += $this->prod->getCBM($v);
				$cu[1] += ($wl->pid > 0 && $wl->parent->type == '30') ? 0 : 0.12;
			} else {
				$cu[1] += ($wl->pid > 0 && $wl->parent->type == '30') ? 0.1 : 1;
			}
			if (is_array($locs)) $locs[] = $k;
		}
		$cu[1] = sprintf('%0.3f', round($cu[1] * 1000) / 1000);

		return $cu;
	}

	// when use pallet out, only change pid from 1 to 2, no ledger
	// location_id != 2
	public function chargeUnitByFreeDay($date, $free_day = 0, $cbm = false, &$locs = false)
	{
		$help = function($date, $stock_id, $free_day) use (&$sls) {
			// all in before free date
			$sql = 'SELECT location_id as lid, SUM(qty_in) as qty FROM wms_stock_ledger WHERE stock_id = :sid AND ts < DATE_ADD(DATE_SUB(:dt, INTERVAL :fd DAY), INTERVAL 1 DAY) AND location_id > :location_id AND qty_in > 0 GROUP BY location_id';
			$rs = Yii::app()->db->createCommand($sql)->bindValues([':sid' => $this->id, ':dt' => $date, ':fd' => $free_day, ':location_id' => WmsLocation::WMS_LOCATION_MAX_MAGIC_ID])->queryAll();
			foreach ($rs as $r) {
				$location = WmsLocation::model()->findByPk($r['lid']);
				if ($location->pid == 2) continue;

				$sls[$r['lid']] = $r['qty'];
			}

			foreach ($sls as $lid => $qty) {
				$in = WmsStockLedger::model()->find(['condition' => 'stock_id = :stock_id AND location_id = :location_id AND qty_in > 0', 'params' => [':stock_id' => $this->id, ':location_id' => $lid], 'order' => 'id']);
				$outs = WmsStockLedger::model()->findAll('stock_id = :stock_id AND location_id = :location_id AND qty_out > 0', [':stock_id' => $this->id, ':location_id' => $lid]);

				$in->ts = date('Y-m-d 02:00:00', strtotime($in->ts));
				foreach ($outs as $out) {
					$out->ts = date('Y-m-d 22:00:00', strtotime($out->ts));

					if (strtotime($out->ts) >= strtotime($date) + 86400) {
					// out after period
						continue;
					} else if (strtotime($out->ts) < strtotime($date) - 6 * 86400) {
					// out before period
						$sls[$lid] -= $out->qty_out;
					} else if (strtotime($out->ts) - strtotime($in->ts) < $free_day * 86400) {
					// if out - in < free
						$sls[$lid] -= $out->qty_out;
					} else {
					// span > actual
						$in->ts = date('Y-m-d H:i:s', strtotime($in->ts) + $free_day * 86400);
						$actual = ceil((strtotime($out->ts) - strtotime($in->ts)) / 86400 / 7);
						if (date('N', strtotime($in->ts)) != 6) $in->ts = date('Y-m-d H:i:s', strtotime($in->ts . ' - ' . ((date('N', strtotime($in->ts)) + 1) % 7) . ' day'));
						$span = ceil((strtotime($out->ts) - strtotime($in->ts)) / 86400 / 7);
						if ($span > $actual) $sls[$lid] -= $out->qty_out;
					}
				}
			}
		};

		$sls = [];
		$help($date, $this->id, $free_day);

		$cu = [0, 0];
		foreach ($sls as $k => $v) {
			$cu[0] += $v;
			if ($v <= 0 || (is_array($locs) && in_array($k, $locs))) {
				continue;
			}

			$wl = WmsLocation::model()->findByPk($k);
			if ($cbm) {
				$cu[1] += $this->prod->getCBM($v);
				$cu[1] += ($wl->pid > 0 && $wl->parent->type == '30') ? 0 : 0.12;
			} else {
				$cu[1] += ($wl->pid > 0 && $wl->parent->type == '30') ? 0.1 : 1;
			}
			if (is_array($locs)) {
				$locs[] = $k;
			}

		}
		$cu[1] = sprintf('%0.3f', round($cu[1] * 1000) / 1000);

		return $cu;
	}

	public static function countAll($sid)
	{
		if (empty($sid)) {
			return false;
		}

		$s = self::model()->findByPk($sid);
		return $s->updateStock();
	}

	public function availQty()
	{
		return $this->qty - $this->qty_res;
	}

	public function updateStock()
	{
		//stock location
		$sql = 'SELECT location_id as lid, SUM(qty_in) - SUM(qty_out) as qty FROM wms_stock_ledger WHERE stock_id = ' . $this->id . ' GROUP BY location_id';
		$rs = Yii::app()->db->createCommand($sql)->queryAll();
		$slids = [0];
		foreach ($rs as $r) {
			$sl = WmsStockLocation::model()->find('location_id = :lid AND stock_id = :sid', [':lid' => $r['lid'], ':sid' => $this->id]);
			if (empty($sl)) {
				$sl = new WmsStockLocation;
				$sl->location_id = $r['lid'];
				$sl->stock_id = $this->id;
			}
			$sl->qty = intval($r['qty']);
			$sl->save();
			$slids[] = $sl->id;
		}
		$rs = WmsStockLocation::model()->findAll('stock_id = :sid AND t.id NOT IN (' . implode(',', $slids) . ')', [':sid' => $this->id]);
		foreach ($rs as $r) {
			$r->qty = 0;
			$r->save();
		}

		//reserve qty
		$sql = 'SELECT SUM(qty) as qty FROM wms_stock_location sl INNER JOIN wms_location l on sl.location_id = l.id WHERE (sl.location_id = :location_id OR l.pid = :location_id) AND stock_id = ' . $this->id;
		$this->qty_res = Yii::app()->db->createCommand($sql)->bindValues([':location_id' => WmsLocation::WMS_LOCATION_RESERVED])->queryScalar();

		//stock qty
		$sql = 'SELECT SUM(qty) as qty FROM wms_stock_location sl INNER JOIN wms_location l on sl.location_id = l.id WHERE qty > 0 AND sl.location_id > :location_id AND l.pid NOT IN (2,3,4,5,6) AND stock_id = ' . $this->id; //AND l.pid > 99
		$this->qty = floatval(Yii::app()->db->createCommand($sql)->bindValues([':location_id' => WmsLocation::WMS_LOCATION_MAX_MAGIC_ID])->queryScalar());

		$this->save();
	}

	public function getTotal($records, $column)
	{
		$total = 0;
		foreach ($records as $record) {
			$total += $record->$column;
		}
		return $total;
	}

	public function hasMultiEB()
	{
		return self::model()->count('qty > 0 AND prod_id = :pid AND org_id = :oid', [':pid' => $this->prod_id, ':oid' => $this->org_id]) > 1;
	}

	public function getBalance($date = false)
	{
		if (!$date) {
			$date = date('Y-m-d 23:59:59');
		}

		$sql = 'SELECT SUM(qty_in) - SUM(qty_out) as qty FROM wms_stock_ledger WHERE location_id > 10 AND ts <= "' . $date . '" AND stock_id = ' . $this->id;
		return Yii::app()->db->createCommand($sql)->queryScalar();
	}

	public function showExpiry()
	{
		if ($this->expiry == '0000-00-00') {
			return '';
		}
		if (empty($this->prod->mdata['expiry_span'])) {
			return $this->expiry;
		} else {
			$warn = ((strtotime($this->expiry) - strtotime(date('Y-m-d'))) / ($this->prod->mdata['expiry_span'] * 365 * 24 * 60 * 60));
			if ($warn > (2 / 3)) {
				$warn = 'green';
			} else if ($warn > (1 / 3)) {
				$warn = 'orange';
			} else {
				$warn = 'red';
			}
			return '<span style="color: ' . $warn . '">' . $this->expiry . '</span>';
		}
	}

	public function getQty()
	{
		if ($this->prod->type != WmsProd::WMS_PROD_KIT || (isset(Yii::app()->user->org) && in_array(Yii::app()->user->org, [Org::ORGID_3PL_IGEA]))) {
			return $this->qty;
		} else {
			$qty = PHP_INT_MAX;
			foreach ($this->prod->items as $item) {
				$count = 0;
				$rs = WmsStock::model()->findAll('prod_id = :prod_id AND org_id = :org_id', [':prod_id' => $item->item->id, ':org_id' => $this->org_id]);
				foreach ($rs as $r) {
					$count += $r->qty - $r->qty_res;
				}
				$qty = ceil($count / $item->qty) < $qty ? ceil($count / $item->qty) : $qty;
			}

			if ($qty > 0 && $qty != PHP_INT_MAX) {
				return floatval($qty);
			} else {
				return $this->qty;
			}
		}
	}

	public function getMinStockAlert()
	{
		$r = WmsProdOrg::model()->find('prod_id = :pid AND org_id = :oid', [':pid' => $this->prod_id, ':oid' => $this->org_id]);
		return intval(@$r->mdata['min_stock_alert']);
	}

	public function getBranch()
	{
		return empty($this->dpt_id) ? '' : $this->branch->shortName(1);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'prod_id' => 'Prod',
			'org_id' => 'Org',
			'qty' => 'Qty',
			'qty_res' => 'Qty Res',
			'expiry' => 'Expiry',
			'batch' => 'Batch',
			'loc_code' => 'Location',
			'sn' => 'Sn',
			'updated' => 'Updated',
			'dpt_id' => 'Branch',
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
	public function search($pgn = true, $ps = 30, $ec = false, $order = 't.id DESC')
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;

		if (empty($this->orgids)) {
			$criteria->compare('t.id', $this->id);
		}

		$criteria->compare('t.prod_id', $this->prod_id);
		$criteria->compare('t.org_id', $this->org_id);
		$criteria->compare('qty', $this->qty, true);
		$criteria->compare('qty_res', $this->qty_res, true);
		$criteria->compare('expiry', $this->expiry, true);
		$criteria->compare('batch', $this->batch, true);
		$criteria->compare('sn', $this->sn, true);
		$criteria->compare('t.updated', $this->updated, true);
		$criteria->compare('t.dpt_id', $this->dpt_id);
		if (!empty($this->orgids)) {
			$criteria->addInCondition('t.org_id', $this->orgids);
		}

		$with = array();
		if (!empty($this->cust_name)) {
			$with[] = 'customer';
			$criteria->addCondition('(customer.name like "%' . $this->cust_name . '%" OR customer.id = "' . $this->cust_name . '")');
		}
		if (!empty($this->prod_name)) {
			$with[] = 'prod';
			$criteria->addCondition('(prod.name LIKE :prod_name OR prod.name_zh LIKE :prod_name OR prod.ean LIKE :prod_name)');
			$criteria->params[':prod_name'] = '%' . $this->prod_name . '%';
		}
		if (!empty($this->prod_ean)) {
			$with[] = 'prod';
			$criteria->with['prod.orgs'] = ['alias' => 'orgs', 'joinType' => 'LEFT JOIN'];
			$criteria->addCondition('(prod.ean LIKE :prod_ean OR orgs.sku LIKE :prod_ean)');
			$criteria->params[':prod_ean'] = '%' . $this->prod_ean . '%';
			// $criteria->compare('prod.ean', $this->prod_ean,true);
		}
		if (!empty($this->prod_sku)) {
			$with[] = 'prod';
			$criteria->with['prod.orgs'] = ['alias' => 'orgs', 'joinType' => 'LEFT JOIN'];
			$criteria->addCondition('(prod.ean LIKE :prod_sku OR orgs.sku LIKE :prod_sku)');
			$criteria->params[':prod_sku'] = '%' . $this->prod_sku . '%';
			// $criteria->compare('prod.ean', $this->prod_ean,true);
		}
		if (!empty($this->loc_code)) {
			$criteria->addCondition('t.id IN (SELECT stock_id FROM wms_stock_location sl INNER JOIN wms_location l ON l.id = sl.location_id LEFT JOIN wms_location l2 ON l.pid = l2.id WHERE sl.qty > 0 AND l.code LIKE :loc_code OR l2.code LIKE :loc_code)');
			$criteria->params[':loc_code'] = '%' . $this->loc_code . '%';
		}

		if (!empty($with)) {
			if (empty($criteria->with)) {
				$criteria->with = [];
			}
			$criteria->with = array_merge(array_unique($with), $criteria->with);
			$criteria->together = true;
		}

		if (!empty($this->cust_name) && !empty($this->prod_ean)) {
			$criteria->with['prod.orgs.customer'] = ['alias' => 'cust', 'joinType' => 'LEFT JOIN'];
			$criteria->group = 't.id';
			// $criteria->addCondition('(cust.name like "%' . $this->cust_name . '%" OR cust.id = "' . $this->cust_name . '")');
		}

		if (!empty($this->cust_name) && !empty($this->prod_sku)) {
			$criteria->with['prod.orgs.customer'] = ['alias' => 'cust', 'joinType' => 'LEFT JOIN'];
			$criteria->group = 't.id';
			// $criteria->addCondition('(cust.name like "%' . $this->cust_name . '%" OR cust.id = "' . $this->cust_name . '")');
		}

		if ($ec) {
			$criteria->mergeWith($ec);
			$criteria->together = true;
		}

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => $order,
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
	 * @return WmsStock the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
