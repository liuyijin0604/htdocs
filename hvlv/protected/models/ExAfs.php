<?php
class ExAfs extends Shipment{

	public static $my_type = 25;

	public static $states = array(
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
	);

	public static $bwfs = array(
		1 => 'Value Alert',
		2 => 'Check Address',
		4 => 'Missing Tel',
	);

	public function rules(){
		$rules = array(
			array('agent_id', 'required'),
		);
		return array_merge(parent::rules(), $rules);
	}

	public function altGoods(){
		return;
	}

	public function genWHD(){
		return [0,0,0];
	}

	public function shipWeight(){
		return $this->weight;
	}

	public function getDvalue(){
		return $this->value;
	}

	public function rating($or){
		return [];
	}

	public function genHbn(){
		return 'AFS' . sprintf('%03s', $this->agent_id) . sprintf('%06s', $this->agent->getLabelNumber());
	}

	public function beforeSave(){
		$this->cleanItems();
		$this->value = empty($this->eitems['v'])? 0 : array_sum($this->eitems['v']);
		return parent::beforeSave();
	}

	public function afterSave(){
		if($this->isNewRecord){
			$this->addTracking(20, 'Shipment Consolidated');
		}else{
			$ht = Tracking::model()->count('pid = :pid AND type = :t', array(':pid' => $this->id, ':t' => $this->status));
			if(empty($ht)){
				switch($this->status){
					case 20:
						$this->addTracking(40, 'Ready for Export', empty($this->odepot)? '' : $this->odepot->suburb);
					break;
					case 60:
						$this->addTracking(60, 'Dispatched', empty($this->odepot)? '' : $this->odepot->suburb);
					break;
					case 99:
						$this->addTracking(99, 'Delivered');
					break;
					default:
					break;
				}
			}
		}
		
		return parent::afterSave();
	}

	public function getDesc(){
		$r = [];
		foreach($this->eitems['g'] as $i => $g){
			$r[] = $g.'&times;'.$this->eitems['q'][$i];
		}
		return implode(', ', $r);
	}
	
	public function getGoods(){
		return implode(', ', $this->eitems['g']);
	}

}
