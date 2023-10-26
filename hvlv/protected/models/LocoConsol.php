<?php

class LocoConsol extends Consol{

	public static $my_type = 30;

	public static $states = array(
		10 => 'New',
		20 => 'Confirmed',
		30 => 'Export Clear',
		40 => 'Air Arrival',
		90 => 'Completed',
	);

	public function rules(){
		$rules = array(
			array('dpt_id, type, eta', 'required'),
		);
		return array_merge(parent::rules(), $rules);
	}

	public function genInvoices($up = false){
		$tos = array();
		foreach($this->shipments as $p){
			if(!in_array($p->agent_id, $tos)){
				$tos[] = $p->agent_id;
				$this->_genInv($p->agent_id, $up);
			}
		}
	}
	
	private function _genInv($to_id, $up = false){
		$owner = Org::model()->findByPk($to_id);
		if(empty($owner) || empty($owner->extra['rate_base_1'])) return false;
		
		if($up){
			foreach($this->invoices as $r){
				if($r->to_id == $to_id && $r->type == 10){
					$inv = $r;
					break;
				}
			}
		}
		if(empty($inv)) $inv= new Invoice;
		$inv->type = 10;
		$inv->dpmt = Invoice::DPMT_IMPORT;
		$inv->currency = $owner->extra['currency'];
		$inv->to_id = $to_id;
		$inv->consol_id = $this->id;
		$inv->status = 2;
		$inv->date = $this->created;
		
		$items = array();
		$tot = 0;
		$term = '';
		foreach($this->shipments as $p){
			if($p->agent_id != $to_id) continue;
			$amt = $p->getCharge($owner, $this->service == 0, true);
			$items[] = array($p->hbn, $p->getDesc(), $p->pkg, $p->weight, $p->cbm, $amt[0], $amt[1], $amt[2], $amt[3]);
			$tot += $amt[0];
		}
		$inv->mdata['name'] = $owner->name;
		$inv->mdata['address'] = $owner->getAddress();
		$inv->mdata['items'] = $items;
		$inv->mdata['payterm'] = empty($owner->extra['payterm'])? '2 days' : $owner->extra['payterm'].' days';
		$inv->mdata['awb'] = $this->awb;
		$inv->due = Invoice::calcDue($inv->date, $inv->mdata['payterm']);
		$inv->total = $tot;
		$inv->save();
	}
	
	public function afterSave(){
		parent::afterSave();
		if($this->status == 20 && empty($this->invoices)) $this->genInvoices();
	}
}
