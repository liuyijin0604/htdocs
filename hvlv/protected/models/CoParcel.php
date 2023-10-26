<?php
class CoParcel extends Shipment{

	public static $my_type = 30;

	public static $states = array(
		10 => 'New',
		20 => 'Picked Up',
		25 => 'Received',
		30 => 'Manifested',
		35 => 'Export Clear',
		38 => 'Forwarded',
		50 => 'Reported',
		55 => 'Held',
		60 => 'Cleared',
		65 => 'Sorted',
		70 => 'Courier',
		90 => 'Delivered',
		0 => 'Canelled',
	);

	public function rules(){
		$rules = array(
			array('agent_id', 'required'),
			array('state', 'auStates'),
			array('items', 'validItems'),
		);
		return array_merge(parent::rules(), $rules);
	}
	
	public function auStates(){
		if(!in_array(strtoupper($this->state), array('ACT', 'NSW', 'NT', 'QLD', 'SA', 'TAS', 'VIC', 'WA'))){
			$this->addError('state', 'Please only use abbreviations for Australian states, e.g. ACT, NSW, NT, QLD, SA, TAS, VIC, WA');
			return false;
		}
	}

	public function rateSelection(){
		if(empty($this->zr_id)) $this->bestRate();
		$ors = OrgRate::model()->findAll('type != 30');
		$ops = array();
		$sa = array();
		foreach($ors as $or){
			$rs = $this->rating($or);
			if(empty($rs)) continue;
			foreach($rs as $r){
				$ops[$r[0]] = '$'.$r[1].' '.$or->name.($or->type == 20? ' '.rtrim($r[2]->weight_hi,'.0').'kg' : '');
				$sa[$r[0]] = $r[1];
			}
		}
		//sort by pricing
		asort($sa);
		$sops = array();
		foreach($sa as $k=>$v){
			$sops[$k] = $ops[$k];
		}
		$sops[328] = 'PCA Express';
		return CHtml::dropDownList('zr_id['.$this->id.']', $this->zr_id, $sops, array('class' => 'rate_sel'));
	}

	public function getPerformanceDates(){
		$dates = [NULL,NULL,NULL];
		foreach($this->tracks as $t){
			if($t->type == 70 && empty($dates[0])){
				$dates[0] = substr($t->dt,0,10);
			}elseif($t->type == 90){
				$dates[1] = substr($t->dt,0,10);
				break;
			}
		}
		if(!empty($dates[0]) && !empty($dates[1])) $dates[2] = ceil((strtotime($dates[1]) - strtotime($dates[0])) / 86400);

		return $dates;
	}
	
	public function afterSave(){
		if($this->isNewRecord){
			$this->addTracking(10, 'Shipment info received');
		}else{
			$ht = Tracking::model()->count('pid = :pid AND type = :t', array(':pid' => $this->id, ':t' => $this->status));
			if(empty($ht)){
				switch($this->status){
					case 25:
						$this->addTracking(25, 'Received at depot', empty($this->odepot)? '' : $this->odepot->suburb);
					break;
					case 30:
						$this->addTracking(30, 'Shipment info confirmed', empty($this->odepot)? '' : $this->odepot->suburb);
					break;
					case 35:
						$this->addTracking(35, 'Customs cleared for export', empty($this->odepot)? '' : $this->odepot->suburb);
					break;
					case 55:
						$this->addTracking(55, 'Customs held', 'Botany');
					break;
					case 60:
						$this->addTracking(60, 'Customs cleared, ready for delivery', 'Botany');
					break;
					case 65:
						$this->addTracking(65, 'Sorted for dispatch', empty($this->odepot)? '' : $this->odepot->suburb);
					break;
					default:
					break;
				}
			}
		}
		
		return parent::afterSave();
	}
	
	public function getCarrierSeq($sn=0){
		$oid = $this->zrate->orgrate->org_id;
		$i = 1;
		foreach($this->consol->shipments as $p){
			if($p->zrate->orgrate->org_id != $oid) continue;
			if($p->id == $this->id){
				return $sn == 0? $i : $i + $sn - 1;
			}
			$i += $p->pkg;
		}
	}

	public function getCharge($owner, $eco = false, $dtl = false){
		$amt = 0;
		$rpf = $eco? 'eco_rate_' : 'rate_';
		$brate = 0;
		$rate = 0;
		$unit = ceil($this->weight);
		
		if($this->weight > 1000){
			$rate = $owner->extra[$rpf.'pkg_1000'];
			$amt = $unit * $rate;
		}elseif($this->weight > 500){
			$rate = $owner->extra[$rpf.'pkg_500'];
			$amt = $unit * $rate;
		}elseif($this->weight > 300){
			$rate = $owner->extra[$rpf.'pkg_300'];
			$amt = $unit * $rate;
		}elseif($this->weight > 100){
			$rate = $owner->extra[$rpf.'pkg_100'];
			$amt = $unit * $rate;
		}elseif($this->weight > 70){
			$rate = $owner->extra[$rpf.'pkg_70'];
			$amt = $unit * $rate;
		}elseif($this->weight > 50){
			$rate = $owner->extra[$rpf.'pkg_50'];
			$amt = $unit * $rate;
		}elseif($this->weight > 30){
			$rate = $owner->extra[$rpf.'pkg_30'];
			$amt = $unit * $rate;
		}elseif($this->weight > 20){
			$brate = $owner->extra[$rpf.'base_2'];
			$rate = $owner->extra[$rpf.'pkg_2'];
			$amt = $unit * $rate;
		}elseif($this->weight > 1){
			$brate = $owner->extra[$rpf.'base_1'];
			$rate = $owner->extra[$rpf.'pkg_1'];
			$unit = ceil($this->weight * 2) - 1;
			$amt = $unit * $rate;
		}else{
			$brate = $owner->extra[$rpf.'base_0'];
			$rate = $owner->extra[$rpf.'pkg_0'];
			$unit = $this->weight > 0.5? 1 : 0;
			$amt = $unit * $rate;
		}
		$amt = $brate + $amt;
		
		return $dtl? array($amt, $brate, $rate, $unit) : $amt;
	}

}
