<?php

class ExParcel extends Shipment{

	public static $my_type = 20;

	public static $states = array(
		8 => 'Pending',
		9 => 'Printed',
		10 => 'New',
		12 => 'Picked Up',
		14 => 'Rcvd. No Info',
		15 => 'Received',
		18 => 'Info Ready',
		20 => 'Consolidated',
		25 => 'Confirmed',
		30 => 'Reported',
		35 => 'Held',
		40 => 'Cleared',
		60 => 'Dispatched',
		70 => 'Arrived',
		80 => 'Clearance',
		90 => 'Courier',
		99 => 'Delivered',
		100 => 'Cancelled',
		101 => 'Problem',
		102 => 'Returned',
	);

	public static $bwfs = array(
		1 => 'Value Alert',
		2 => 'Similar Address',
		4 => 'Check Address',
		8 => 'Goods Detail',
		16 => 'Check Weight',
		32 => 'Multiple ID',
		64 => 'Missing Tel',
		128 => 'Name Changed',
	);

	public static $styps = array(
		0 => 'Standard',
		10 => 'VIP',
		20 => '玄武',
	);

	public function rules(){
		$rules = array(
			array('hbn', 'required'),
		);
		return array_merge(parent::rules(), $rules);
	}

	public function attributeLabels(){
		return AppHelper::tArray(strtolower(__CLASS__), array_merge(parent::attributeLabels(), array(
			'cnee_name' => 'Receiver',
			'cnee_tel' => 'Receiver Tel',
			'cnor_name' => 'Sender',
			'cnor_tel' => 'Sender Tel',
		)));
	}

	public function statusList(){
		$r = self::$states;
		/*if(!Acl::hasAccess('B:Export/UpdateParcelAfterConsolidation')){
			$r = array_slice($r, 0, 5, true);
		}*/
		return $r;
	}

	public function genHbn(){
		return (empty($this->mdata['pv'])? 'EAU' : 'EPV') . sprintf('%03s', $this->agent_id) . sprintf('%06s', $this->agent->getLabelNumber());
	}

	public function checkInfoReady($se=false){
		$this->mapGoods();
		$err = array();
		if(empty($this->weight)) $err[] = 'Weight required';
		if($this->cnor_id > 0)	$err = array_merge($err, $this->cnor->ExportValidate(1));
		if($this->cnee_id > 0)	$err = array_merge($err, $this->cnee->ExportValidate(2));

		$this->bwf = $this->bwf & (~ 8);
		if(!empty($this->eitems['pid'])){
			foreach($this->eitems['pid'] as $pid){
				if(empty($pid)){
					$this->bwf = $this->bwf | 8;
					break;
				}
			}
		}else{
			$this->bwf = $this->bwf | 8;
		}

		if(empty($err)){
			$this->status = 18;
			return true;
		}else{
			$this->status = 15;
			if($se){
				foreach($err as $e){
					$this->addError('cnee', $e);
				}
			}
			return false;
		}
	}

	public function duplicate(){
		$p = self::model()->findByPk($this->id);
		$p->id = null;
		$p->cnee->id = null;
		$p->cnee->isNewRecord = true;
		$p->cnee->save();
		$p->cnee_id = $p->cnee->id;
		$p->cnor->id = null;
		$p->cnor->isNewRecord = true;
		$p->cnor->save();
		$p->cnor_id = $p->cnor->id;
		$p->isNewRecord = true;
		$p->hbn = $this->genHbn();
		$p->save();
		return $p;
	}

	public function mapGoods($debug=false){
		if(!isset($this->eitems['g'])) return false;

		$tv = 0;
		$tdv = 0;
		$unmatch = false;
		foreach($this->eitems['g'] as $i => $g){
			if(!empty($this->eitems['pid'][$i])) continue;
			$pm = ExProdbMap::model()->find('name = :g AND agt_id = :aid', [':g' => $g, ':aid' => $this->agent_id]);
			if(!empty($pm)){
				$r = $pm->prod;
				$this->eitems['pid'][$i] = $r->id;
				$this->eitems['g'][$i] = $r->name_zh;
				$this->eitems['u'][$i] = $r->unit;
				$this->eitems['w'][$i] = $r->weight * $this->eitems['q'][$i];
				$this->eitems['b'][$i] = $r->brand;
				$this->eitems['m'][$i] = $r->model;
				$this->eitems['hs'][$i] = $r->hs;
				$this->eitems['t'][$i] = $r->tax;
				$this->eitems['v'][$i] = $r->price * $this->eitems['q'][$i];
				$tv += $this->eitems['v'][$i];
				$tdv += ($this->eitems['u'][$i] == '千克')?  $this->eitems['t'][$i] * $this->eitems['w'][$i] : $this->eitems['t'][$i] * $this->eitems['q'][$i];
				continue;
			}else{
				$this->bwf = $this->bwf | 8;
				$unmatch = true;
			}
		}
		
		if(!$unmatch){
			$this->value = $tv;
			$this->tariff = $tdv;
		}

	}

	public function beforeSave(){
		if($this->status == 100){
			$this->bwf = 0;
		}elseif($this->status < 25){
			$this->cleanItems();
			if(empty($this->hbn)) $this->hbn = $this->genHbn();
			if(empty($this->pkg)) $this->pkg = 1;
			if(empty($this->exm)) $this->exm = 'EXLV';
		}
		if($this->status != 14){
			$this->bwf = (!empty($this->cnee->name) && !empty($this->cnee->city) && !empty($this->cnee->postcode) && !empty($this->cnee->address))? $this->bwf & (~ 4) : $this->bwf | 4;
			$this->bwf = empty($this->cnee->tel)? $this->bwf | 64 : $this->bwf & (~ 64);
		}
		if($this->status >= 15 && $this->status < 20) $this->checkInfoReady();
		return parent::beforeSave();
	}

	public function afterSave(){
		if($this->status < 30){
			$ht = Tracking::model()->count('pid = :pid AND type = :t', array(':pid' => $this->id, ':t' => $this->status));
			if(empty($ht)){
				$dpt = empty($this->odepot)? '' : $this->odepot->suburb;
				switch($this->status){
					case 9:
						$this->addTracking(9, '运单信息已录入', $dpt);
					break;
					case 10:
						$this->addTracking(10, '收到运单信息', $dpt);
					break;
					case 12:
						$this->addTracking(12, 'Consignment Picked Up');
					break;
					case 18:
						$this->addTracking(18, '身份证信息已匹配成功，等待发运', $dpt);
					break;
					default:
					break;
				}
			}
		}

		return parent::afterSave();
	}

	public function search($pgn=true, $ps = 30, $ec = false, $defaultOrder=true){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;
		if(!empty($this->agent_id)){
			if(in_array($this->agent_id , [851])){
				$dc = new CDbCriteria;
				$criteria->addCondition('agent_id = '.$this->agent_id.' OR hbn regexp "^(E|D)AU'.$this->agent_id.'[0-9]{6,9}$"');
				$criteria->mergeWith($dc);
			}else{
				$criteria->compare('agent_id',$this->agent_id,false);
			}
		}

		$criteria->compare('hbn',$this->hbn,true);
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.owner_id',$this->owner_id);
		$criteria->compare('cnor_id',$this->cnor_id);
		$criteria->compare('cnee_id',$this->cnee_id);
		if(empty($this->status) && empty($this->hbn)){
			$criteria->addCondition('t.status != 100');
		}else{
			$criteria->compare('t.status',$this->status);
		}
		$criteria->compare('t.state',$this->state,true);
		$criteria->compare('t.postcode',$this->postcode,true);

		$with = array();

		if(!empty($this->cnee_name)){
			$with[] = 'cnee';
			$criteria->compare('cnee.name',$this->cnee_name,true);
		}

		if(!empty($this->bwf)){
			$criteria->addCondition('t.bwf & ' . $this->bwf . ' > 0');
		}

		if($this->cbwf !== null){
			if(empty($this->cbwf)){
				$criteria->compare('cbwf', $this->cbwf);
			}else{
				$criteria->addCondition('t.cbwf & ' . $this->cbwf . ' > 0');
			}
		}

		if(!empty($this->mids)){
			$criteria->addInCondition("t.id", $this->mids);
		}

		if(!empty($this->cnee_tel)){
			$with[] = 'cnee';
			$criteria->compare('cnee.tel',$this->cnee_tel,true);
		}

		if(!empty($this->cnor_name)){
			$with[] = 'cnor';
			$criteria->compare('cnor.name',$this->cnor_name,true);
		}

		if(!empty($this->cnor_tel)){
			$with[] = 'cnor';
			$criteria->compare('cnor.tel',$this->cnor_tel,true);
		}
		
		if(!empty($with)){
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if($ec) $criteria->mergeWith($ec);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=> array(
    			'defaultOrder'=>'t.id DESC',
  			),
			'pagination'=> $pgn? array(
				'pageSize' => $ps,
			) : false,
		));
	}
}
