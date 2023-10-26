<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ApiTrackingAction extends CAction {
	public $ctlr, $debug,$user;

	public function run() {
		$this->ctlr = $this->getController();
		$this->user = empty($this->ctlr->user)? false : User::model()->findByPk($this->ctlr->user);
		$this->debug = !empty($_POST['test']);

		$f = 'hbn';
		$n = '';
		if(!empty($this->ctlr->data->connote)){
			$n = $this->ctlr->data->connote;
		}elseif(!empty($this->ctlr->data->ref)){
			$n = $this->ctlr->data->ref;
			$f = 'ref';
		}elseif(!empty($this->ctlr->data->cref)){
			$n = $this->ctlr->data->cref;
			$f = 'cref';
		}

		if(is_array($n)){
			$criteria = new CDbCriteria();
			$criteria->addCondition("status > 0 AND status != 100");
			$criteria->addInCondition($f, $n);
			$criteria->addCondition("agent_id = ".$this->user->org_id);

			$cr = new CDbCriteria();
			$cr->addInCondition('pref', $n);
			$refMap =[];
			$sql = 'SELECT pid,pref FROM change_shipment_label WHERE '.$cr->condition;

			$cs = Yii::app()->db->createCommand($sql)->queryAll(true, $cr->params);
			if(!empty($cs)){
				$pids = [];
				foreach($cs as $c){
					$pids[] = $c['pid'];
					$refMap[$c['pid']]=$c['pref'];
				}
				$criteria->addCondition('id IN ('.implode(',', $pids).')', 'OR');
			}
			$rs = Shipment::model()->findAll($criteria);
			$o = [];
			foreach($rs as $p){
				if(empty($p->tracks)){
					$o[$p->hbn] = false;
				}else{
					$o[$p->hbn] = $p->trackingInfo(0, false,false,true);
					if(!empty($refMap[$p->id]))
					{
						$o[$p->hbn]->ref = $refMap[$p->id];
					}
				}
			}
			echo json_encode($o);
		}else{
			$p = Shipment::model()->find(["condition"=>$f.' = :c AND status > 0 AND status != 100 and agent_id = '.$this->user->org_id, "params"=>array(':c' => $n),"order"=>"consol_id DESC"]);
			if(empty($p)){
				$csl = ChangeShipmentLabel::model()->find('pref=:pref', [':pref'=>$n]);
				if(!empty($csl)){
					$p = $csl->shipment;
				}
			}
			if(empty($p) || empty($p->tracks)){
				$o = false;
			}else{
				$o = $p->trackingInfo(0, false,false,true);
				if(!empty($csl)){
					$o->ref = $csl->pref;
				}
			}
			echo json_encode($o);
		}
	}
}
