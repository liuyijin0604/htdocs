<?php
class ExDirect extends Shipment{

	public static $my_type = 50;

	public static $states = array(
		10 => 'Pending',
		20 => 'Paid',
		50 => 'Processing',
		90 => 'Courier',
		99 => 'Delivered',
		100 => 'Cancelled',
	);

	public static $bwfs = array(
		1 => 'Cash Payment',
		2 => 'Check Address',
		4 => 'Missing Tel',
	);

	public function rules(){
		$rules = array(
			array('agent_id', 'required'),
		);
		return array_merge(parent::rules(), $rules);
	}

	public function genHbn(){
		return ($this->agent_id > 0)? 'EDO' . sprintf('%03s', $this->agent_id) . sprintf('%06s', $this->agent->getLabelNumber()) : '';
	}

	public function beforeSave(){
		$this->cleanItems();
		$this->value = empty($this->eitems['v'])? 0 : array_sum($this->eitems['v']);
		return parent::beforeSave();
	}

	public function afterSave(){
		if($this->isNewRecord){
			$this->addTracking(10, 'Order Created');
		}else{
			$ht = Tracking::model()->count('pid = :pid AND type = :t', array(':pid' => $this->id, ':t' => $this->status));
			if(empty($ht)){
				switch($this->status){
					case 20:
						$this->addTracking(20, 'Payment Received', empty($this->odepot)? '' : $this->odepot->suburb);
					break;
					case 50:
						$this->addTracking(50, 'Order Processing', empty($this->odepot)? '' : $this->odepot->suburb);
					break;
					default:
					break;
				}
			}
		}
		
		return parent::afterSave();
	}

	public function getDesc(){
		if(empty($this->eitems['g'])) return 'EMPTY';
		$r = [];
		foreach($this->eitems['g'] as $i => $g){
			$r[] = $g.' x '.$this->eitems['q'][$i];
		}
		return implode(', ', $r);
	}

	public function getTotal(){
		if(empty($this->eitems['q'])) return 0;
		$rates = SellRate::getRates($this->agent_id, 20, $this->created);
		$rate = empty($rates['D1'])? 13 : $rates['D1']->perkg;
		$ec = !in_array($this->state, ['江苏省', '浙江省', '上海市', '安徽省'])? 1 : 0;
		
		return round(($rate + $ec) * array_sum($this->eitems['q']) * 100) / 100;
	}

}
