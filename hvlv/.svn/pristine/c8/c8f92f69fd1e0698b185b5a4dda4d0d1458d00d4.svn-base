<?php
class ElmsConsol extends Consol{

	public static $my_type = 80;

	public static $states = array(
		10 => 'New',
		20 => 'Confirmed',
		30 => 'Reported',
		40 => 'Acknowledged',
		50 => 'Withdrawn',
		70 => 'Dispatched',
		80 => 'Completed',
		100 => 'Cancelled',
	);

	public function rules(){
		$rules = array(
			array('owner_id', 'required'),
		);
		return array_merge(parent::rules(), $rules);
	}

	public function genNo(){
		$n = 'EL'.date('ymd', strtotime($this->created));
		$s = self::model()->count('no LIKE :n', [':n' => $n.'%']) + 1;
		return $n.sprintf('%02d',$s).substr($this->pol,2);
	}

}
