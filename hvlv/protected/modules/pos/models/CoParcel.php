<?php

class CoParcel extends Shipment{

	public static $my_type = 30;

	public static $states = array(
		10 => 'New',
		20 => 'Picked Up',
		25 => 'Received',
		30 => 'Manifested',
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

	public function attributeLabels(){
		return AppHelper::tArray(strtolower(__CLASS__), array_merge(parent::attributeLabels(), array(
			'cnee_name' => 'Receiver',
			'cnee_tel' => 'Receiver Tel',
			'cnor_name' => 'Sender',
			'cnor_tel' => 'Sender Tel',
			'ref' => 'Reference No.',
		)));
	}

	public function statusList(){
		$r = self::$states;
		if(!Acl::hasAccess('B:Export/UpdateParcelAfterConsolidation')){
			$r = array_slice($r, 0, 4, true);
		}
		return $r;
	}

	public function genHbn(){
		return 'ELC' . sprintf('%03s', $this->agent_id) . sprintf('%06s', $this->agent->getLabelNumber());
	}

	public function beforeSave(){
		return parent::beforeSave();
	}
}
