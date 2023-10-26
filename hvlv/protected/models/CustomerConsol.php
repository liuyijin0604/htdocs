<?php
class CustomerConsol extends Consol{

	public static $my_type = 35;
	public static $states = array(
		10 => 'New',
	    90 => 'Completed',
		100 => 'Cancelled',
	);

	public function rules(){
		$rules = array(
			array('dpt_id, type, pol, pod, eta', 'required'),
		);
		return array_merge(parent::rules(), $rules);
	}
      public function relations(){
		$r = parent::relations();
		$r['shipments'] = array(self::HAS_MANY, 'ImParcel', 'consol_id');
		return $r;
	}

	public function genNo(){
		$n = 'CU'.date('ymd', strtotime($this->created));
		$s = self::model()->count('no LIKE :n', [':n' => $n.'%']) + 1;
		return $n.sprintf('%02d',$s).substr($this->pod,2);
	}
        

	public function getEtaToNow(){
		if(empty($this->eta)) return 0;
		$etaDay=new DateTime($this->eta,new DateTimeZone('Australia/Sydney'));
		$now=new DateTime('now',new DateTimeZone('Australia/Sydney'));
		$daydiff = $now->diff($etaDay)->format("%a");
		return $daydiff;
	}

	public function getOrgsInfo(){
		$orgs = array();
		foreach($this->shipments as $shipment ) {
			if ( !isset($orgs[$shipment->agent->id]) ){
				$orgs[$shipment->agent->id] = array(
					'id' => $shipment->agent->id,
					'name' => $shipment->agent->name,
					'weight' => floatval($shipment->weight)
				);
			} else {
				$orgs[$shipment->agent->id]['weight'] += floatval($shipment->weight);
			}
			unset($shipment->agent);
		}
		return $orgs;
	}
      public function beforeSave(){     
            parent::beforeSave();
            return true;
        }

	public function afterSave() {
		parent::afterSave();
	}

    public function getOwnerName($type = false){
		$rs = Shipment::model()->findAll(['condition' => 'consol_id = :c', 'params' => [':c' => $this->id], 'group' => 'agent_id']);
		$ownerNames = array();
		foreach ($rs as $shipment ){
			if(!empty($shipment->agent)){
				$ownerNames[] = $shipment->agent->shortName(2);
			}
		}
		return implode(', ', $ownerNames);
	}

        
        public function search($pgn=true, $ps = 30, $ec = false,$forgp = false,$bydate = false,$defaultOrder=false ){
		
		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.no',$this->no,true);
		$criteria->compare('t.owner_id',$this->owner_id);
		$criteria->compare('t.dpt_id',$this->dpt_id);
		$criteria->compare('carrier_id',$this->carrier_id);

                 if(empty($this->status)){
			$criteria->addCondition('t.status != 100');
                 }
               
		$with=[];
		if(!empty($this->owner_name)){
			//$criteria->compare('owner.name',$this->owner_name,true);
			$sql = 'SELECT s.consol_id FROM shipment s INNER JOIN org o on s.agent_id = o.id WHERE o.name LIKE :n GROUP BY s.agent_id, s.consol_id';
			$rs = Yii::app()->db->createCommand($sql)->bindValues([':n' => '%'.$this->owner_name.'%'])->queryAll();
			$cids = [];
                        $anotherSql='SELECT s.id FROM consol s INNER JOIN org o ON s.owner_id=o.id WHERE s.owner_id>0 AND o.name LIKE :n';
                        $rs1=Yii::app()->db->createCommand($anotherSql)->bindValues([':n'=>'%'.$this->owner_name.'%'])->queryAll();
			foreach($rs as $r){
				if(empty($r['consol_id'])) continue;
				$cids[] = $r['consol_id'];
			}
                        foreach($rs1 as $r){
                            if(empty($r['id']))  continue;
                            $cids[]=$r['id'];
                        }
			$criteria->addInCondition("t.id", $cids);
		}
			   
		if(!empty($this->awb)){
			$cawb = new CDbCriteria;
			$cawb->compare('t.awb',$this->awb,true);
			$cawb->compare('t.meta',$this->awb,true, 'OR');
			$criteria->mergeWith($cawb);
		} else {
			if ( $forgp ) {
				$criteria->addCondition('awb is not NULL');
			}
		}
		$criteria->compare('exrate',$this->exrate,true);
		$criteria->compare('airline',$this->airline,true);
		$criteria->compare('flight',$this->flight,true);
		$criteria->compare('pol',$this->pol);
		$criteria->compare('pod',$this->pod);
		$criteria->compare('poc',$this->poc,true);
		$criteria->compare('etd',$this->etd,true);
		$criteria->compare('eta',$this->eta,true);

		if ( $forgp ) {
			$criteria->addCondition('t.status < 90');
		} else {
			$criteria->compare('t.status', $this->status);
		}
		if ( $bydate ) {
			$criteria->addCondition("t.created >= '2017-01-01'");
		} else {
			$criteria->compare('t.created',$this->created,true);
		}
		if(!empty($this->bwf)){
			$criteria->addCondition('bwf & ' . $this->bwf . ' > 0');
		}

		if($ec) $criteria->mergeWith($ec);
				$sort = new CSort(get_called_class());       
		$sort->attributes = array( 
					'owner_name'=>array(
					   'asc'=>'owner.name',
					   'desc'=>'owner.name DESC',
					),
                                        'process_airport'=>array(
                                            'asc'=>'process.airport',
					   'desc'=>'process.airport DESC',
                                        ),
                                         'process_airport_dead'=>array(
                                            'asc'=>'process.pick_dead_date',
					   'desc'=>'process.pick_dead_date DESC',
                                        ),
					'*',
				);
                if(!$defaultOrder){
                  $sort->defaultOrder='t.id DESC';  
                }else{
                    $sort->defaultOrder=$defaultOrder; 
                }
		$sob = $sort->getOrderBy();
		$sobs = preg_split('/, */', $sob);
		foreach($sobs as $ob){
			if(!strpos($ob, '.')) continue;
			list($m, $f) = explode('.', $ob);
			if(str_replace('`', '', $m) == 't') continue;
			$with[] = $m;
		}
			 if(!empty($with)){
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}
			  
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>$sort,
			'pagination'=> $pgn? array(
				'pageSize' => $ps,
			) : false,
		));
	}
}
