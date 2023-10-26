<?php

class ExDirect extends Shipment{

	public static $my_type = 50;

	public static $states = array(
		10 => 'Pending',
		20 => 'Paid',
		50 => 'Processing',
		70 => 'Courier',
		90 => 'Delivered',
		0 => 'Canelled',
	);

	public static $bwfs = array(
		1 => 'Cash Payment',
		2 => 'Check Address',
		4 => 'Missing Tel',
	);

	public static $prodb = array(
		['id' => '100', 'name' => 'Golden Koala Fresh Milk 1L', 'price' => 13, 'min' => 2 , 'step' => 2],
	);

	public function rules(){
		$rules = array(
			array('agent_id', 'required'),
		);
		return array_merge(parent::rules(), $rules);
	}

	public function validItems(){
		$this->cleanItems();
		if(empty($this->eitems['q'])){
			$this->addError('items', Yii::t(strtolower(__CLASS__), 'Pelase enter at least one item'));
			return false;
		}
		$valid = true;

		foreach($this->eitems['q'] as $i => $q){
			if(!is_numeric($q)){
				$this->addError('items', Yii::t(strtolower(__CLASS__), 'Item {item}: quantity has non numeric value', ['{item}' => $this->eitems['g'][$i]]));
				$valid = false;
			}
		}
		
		return $valid;
	}

	public function attributeLabels(){
		return AppHelper::tArray(strtolower(__CLASS__), array_merge(parent::attributeLabels(), array(
			'cnee_name' => 'Receiver',
			'cnee_tel' => 'Receiver Tel',
			'cnor_name' => 'Sender',
			'cnor_tel' => 'Sender Tel',
		)));
	}

	public function genHbn(){
		return 'EDO' . sprintf('%03s', $this->agent_id) . sprintf('%06s', $this->agent->getLabelNumber());
	}

	public function getProdb($org){
		$db = self::$prodb;
		foreach($db as $k=>&$v){
			$v['price'] += empty($org->extra['edo_margin'])? 1 : $org->extra['edo_margin'];
		}
		return $db;
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
		return parent::beforeSave();
	}

	public function afterSave(){
		if($this->isNewRecord){
			$this->addTracking(10, 'Order Created');
		}elseif($this->status == 20){
			$ht = Tracking::model()->count('pid = :pid AND type = :t', array(':pid' => $this->id, ':t' => $this->status));
			if(empty($ht)){
				$this->addTracking(20, 'Payment Received', empty($this->odepot)? '' : $this->odepot->suburb);
			}
		}
		
		return parent::afterSave();
	}


	public function search($pgn=true, $ps = 30, $ec = false){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;
		if(!empty($this->agent_id)){
			$criteria->compare('agent_id',$this->agent_id,false);
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

		if($ec){
			$criteria->mergeWith($ec);
		}

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
