<?php
class PickupList extends Manifest{

	public static $my_type = 40;

	public $_mkv = [];
	public $sids, $hbn;

	public function rules(){
		$rules = array(
			array('dpt_id', 'required'),
			array('hbn', 'safe', 'on'=>'search'),
		);
		return array_merge(parent::rules(), $rules);
	}

	public function relations(){
		$relations = array(
			'shipments'=>array(self::MANY_MANY, 'Shipment', 'mani_map(mani_id, fid)'),
		);
		return array_merge(parent::relations(), $relations);
	}

	public function countLines(){
		$db = Yii::app()->db;
		$sql = 'SELECT COUNT(fid) AS t FROM mani_map m INNER JOIN shipment s ON s.id = m.fid WHERE s.status != 100 AND mani_id = '.$this->id;
		$c = $db->createCommand($sql);
		return $c->queryScalar();
	}
	
	public function totPacks($wid = 0){
		return $this->totQuery('COUNT(s.pkg) AND s.status != 100', $wid);
	}

	public function getBranch(){
		return empty($this->dpt_id)? '' : $this->dpt->shortName(1);
	}

	public function getInvoiceLine(){
		$inv = InvLine::model()->with('invoice')->find('invoice.status NOT IN (1,10) AND model = :m AND fid = :fid', [':m' => 'Manifest', ':fid' => $this->id]);
		return $inv;
	}

	public function beforeSave(){
		return parent::beforeSave();
	}

	public function totWeight($wid = 0){
		$sql = 'SELECT SUM(weight) FROM shipment s INNER JOIN mani_map m ON m.fid = s.id WHERE m.mani_id = '.$this->id;
		$c = Yii::app()->db->createCommand($sql);
		return $c->queryScalar();
	}

	public function isBilled(){
		return InvLine::model()->with('invoice')->count('invoice.status NOT IN (1, 10) AND t.fid = :fid AND t.model = "Manifest"', [':fid' => $this->id]) > 0;
	}

	public function billDate(){
		if(empty($this->owner->extra['payterm'])) return date('Y-m-d', strtotime($this->created));
		if($this->owner->extra['payterm'] == 30){ //monthly
			$bd = strtotime(date('Y-m-02', strtotime($this->created)).' +1 month');
		}else{
			$cts = strtotime($this->created);
			$wd = date('N', $cts);
			$bd = ($cts + (1 + intval($this->owner->extra['payterm']) - ($wd > 5? 5-$wd : $wd)) * 86400);
		}
		return date('Y-m-d', $bd);
	}

	public function toStat(){
		$sql = "SELECT s.id, s.weight, s.items FROM mani_map m INNER JOIN shipment s ON m.fid = s.id WHERE mani_id = ".$this->id;
		$rs = Yii::app()->db->createCommand($sql)->queryAll();
		$sum = [];

		foreach(ExStat::$types as $k){
			$sum[$k] = ['qty' => 0, 'wt' => 0];
		}

		foreach($rs as $r){
			$eitems = json_decode($r['items'], true);

			if(empty($eitems['type'])) $typ = 'X';
			else{
				$typs = array_unique($eitems['type']);
				$typ = 'X';
				switch(sizeof($typs)){
					case 1:
						$typ = array_pop($typs);
					break;
					case 2:
						$typ = in_array('O', $typs)? 'X' : 'B';
					break;
				}
			}

			$gm = empty($eitems['g'])? '' : implode('', $eitems['g']);
			$d = 90;

			if($typ == 'M'){
				if(preg_match('/小安素/', $gm)) $d = 21;
				else $d = 20;
			}elseif($typ == 'B'){
				if(preg_match('/一段|1段/', $gm)){
					$d = 11;
				}elseif(preg_match('/二段|2段/', $gm)){
					$d = preg_match('/A2|可瑞康|Karicare/', $gm)? 11 : 12;
				}else{
					$d = 12;
				}
			}else{
				if(preg_match('/UGG|鞋|靴|围巾|Scarf/i', $gm)) $d = 91;
			}

			$sum[$d]['qty']++;
			$sum[$d]['wt'] += $r['weight'];
		}

		$pu_date = substr($this->created, 0 ,10);
		foreach($sum as $t=>$d){
			$es = ExStat::model()->find('pu_id = :id AND type = :t', [':id' => $this->id, ':t' => $t]);
			if(empty($es)){
				$es = new ExStat('create');
				$es->date = $pu_date;
				$es->pu_id = $this->id;
				$es->org_id = $this->fwd_id;
				$es->type = $t;
			}
			$es->qty = $d['qty'];
			$es->wt = $d['wt'];
			$es->save();
		}
	}

	public function afterSave(){
		return parent::afterSave();
	}

	public function attributeLabels(){
		return array_merge(parent::attributeLabels(), array(
		));
	}

	public function search($pgn=true, $ps = 30, $ec = false,$paidonly = false){
		$criteria=new CDbCriteria;
		
		if(empty($this->status)) $this->status = '<90';

		$criteria->compare('t.id',$this->id);
		$criteria->compare('fwd_id',$this->fwd_id);
		$criteria->compare('dpt_id',$this->dpt_id);
		$criteria->compare('by_id',$this->by_id);
		$criteria->compare('consol_id',$this->consol_id);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('t.type',$this->type);
		if(empty($this->status)) $this->status = '<100';
		$criteria->compare('t.status',$this->status);
		$criteria->compare('t.ref',$this->ref,true);
		$criteria->compare('total',$this->total,true);
		$criteria->compare('t.bwf',$this->bwf,true);

		$with = array();
		if(!empty($this->fwd_name)){
			$with[] = 'owner';
			$criteria->addCondition('owner.name LIKE :agt_name OR owner.code LIKE :agt_name OR fwd_id = :agt_id');
			$criteria->params[':agt_name'] = '%'.$this->fwd_name.'%';
			$criteria->params[':agt_id'] = $this->fwd_name;
		}

		if(!empty($this->hbn)){
			$with[] = 'shipments';
			$criteria->compare('shipments.hbn', trim($this->hbn));
		}
		
		if(!empty($with)){
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if($ec){
			$criteria->mergeWith($ec);
		}

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'t.created DESC',
			),
			'pagination'=>$pgn? array(
				'pageSize' => $ps,
			) : false,
		));
	}
		
	public function checkFinish(){
		$db = Yii::app()->db;
		$sql = "SELECT COUNT(s.id) FROM `shipment` s LEFT JOIN `addr` a ON a.id = s.cnee_id INNER JOIN `mani_map` m ON s.id = m.fid WHERE s.status < 18 AND m.mani_id = ".$this->id." AND (a.id IS NULL OR a.address = '' OR a.name = '' OR a.tel = '')";

		$c = $db->createCommand($sql);
		return $c->queryScalar() == 0;
	}

}
