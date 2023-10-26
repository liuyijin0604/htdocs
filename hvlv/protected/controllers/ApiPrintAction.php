<?php
class ApiPrintAction extends CAction {
	public $ctlr, $debug;

	public function run() {
		$this->ctlr = $this->getController();
		$this->debug = !empty($_POST['test']);

		if(!empty($this->ctlr->data->action) && method_exists($this, $this->ctlr->data->action)){
			$this->{$this->ctlr->data->action}();
		}else{
			throw new CHttpException(400, 'API method not found!');
		}
	}

	public function getConsols(){
		$rs = ExcoConsol::model()->findAll(['condition' => 'status < 70 AND created > DATE_SUB(NOW(), INTERVAL 25 DAY)', 'order' => 'id DESC']);
		$o = [];
		foreach($rs as $r){
			$sc = ExParcel::model()->count('consol_id = :id', [':id' => $r->id]);
			if($sc == 0) continue;
			$c = new StdClass;
			$c->id = $r->id;
			$c->no = $r->no;
			$c->poc = $r->poc;
			$c->pkgs = $sc;
			$c->tpl = $r->getLabelTpl();
			$o[] = $c;
		}

		//DXT AF consol
		$rs = ExacConsol::model()->findAll(['condition' => 'owner_id = 794 AND status = 10 AND created > DATE_SUB(NOW(), INTERVAL 30 DAY)', 'order' => 'id DESC']);
		foreach($rs as $r){
			$sc = Shipment::model()->count('consol_id = :id', [':id' => $r->id]);
			if($sc == 0) continue;
			$c = new StdClass;
			$c->id = $r->id;
			$c->no = $r->no;
			$c->poc = $r->pod;
			$c->pkgs = $sc;
			$c->tpl = '';
			$o[] = $c;
		}

		$this->ctlr->output($o);
	}

	public function getShipments(){
		$rs = Shipment::model()->findAll('consol_id = :id', [':id' => $this->ctlr->data->cid]);
		//$con = ExcoConsol::model()->findByPk($this->ctlr->data->cid);
		$o = [];
		$rmap = include(Yii::app()->basePath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'ref_map.php');
		foreach($rs as $r){
			//$r->altGoods();
			$p = new StdClass;
			$p->id = $r->id;
			$p->cid = $r->consol_id;
			$p->pallet = Yii::app()->db->createCommand('SELECT f.ref FROM mani_map m INNER JOIN manifest f ON f.id = m.mani_id WHERE f.consol_id = '.$r->consol_id.' AND f.type = 60 AND m.fid = '.$r->id)->queryScalar();
			$p->hbn = empty($rmap[$r->hbn])? $r->hbn : $rmap[$r->hbn];
			//$p->hbn = $r->hbn;
			$p->ref = $r->ref;
			//$p->eitems = $r->eitems;
			//$p->whd = $r->genWHD();
			//$p->weight = $r->weight;
			//$p->swt = $r->shipWeight();
			//$p->value = $r->getDvalue();
			//$rate = $r->rating($con->poc, $con->exrate, true);
			//$p->rzone = empty($rate[1])? '' : $rate[1]->zone;
			//$p->cnor = new StdClass;
			//$p->cnor->name = $r->cnor->name;
			//$p->cnor->tel = $r->cnor->tel;
			//$p->cnor->fullAddress = $r->cnor->fullAddress();
			//$p->cnee = new StdClass;
			//$p->cnee->name = $r->cnee->name;
			//$p->cnee->tel = $r->cnee->tel;
			//$p->cnee->state = $r->cnee->state;
			//$p->cnee->postcode = $r->cnee->postcode;
			//$p->cnee->fullAddress = $r->cnee->getCnFullAddress();
			//$p->mdata = [];
			/*if(in_array($con->poc, ['CNCA2', 'CNTSN', 'CNJNA'])){ //yto dtb
				$p->mdata['dtb'] = empty($r->mdata['dtb'])? $r->cnee->city : $r->mdata['dtb'];
				$p->mdata['cbc'] = empty($r->mdata['cbc'])? '' : $r->mdata['cbc'];
			}*/

			$o[] = $p;
		}

		$this->ctlr->output($o);
	}

	public function commPtz(){
		$trans = Yii::app()->db->beginTransaction();
		try{

			$ps = [];
			foreach($this->ctlr->data->ds as $i => $r){
				if(isset($ps[$r[0]])) unset($this->ctlr->data->ds[$ps[$r[0]]]);
				$ps[$r[0]] = $i;
			}

			foreach($this->ctlr->data->ds as $r){
				$p = Shipment::model()->findByPk($r[0]);
				$pn = sprintf('%02d', $r[1]);
				$m = Manifest::model()->find('consol_id = :c AND ref = :n', [':c' => $p->consol_id, ':n' => $pn]);
				if(empty($m)){
					$m = new Manifest;
					$m->type = 60;
					$m->ref = $pn;
					$m->consol_id = $p->consol_id;
					$m->save();
				}

				$sql = 'SELECT m.id FROM mani_map m INNER JOIN manifest f ON f.id = m.mani_id WHERE m.fid = :fid AND f.type = 60 AND f.consol_id = :cid';
				$mid = Yii::app()->db->createCommand($sql)->bindValues([':fid' => $r[0], ':cid' => $p->consol_id])->queryScalar();
				if(empty($mid)){
					$m->map($p);
				}else{
					$mm = ManiMap::model()->findByPk($mid);
					$mm->mani_id = $m->id;
					$mm->save();
				}
			}
			$trans->commit();
		} catch (Exception $ex) {
			$trans->rollback();
			throw $ex;
		}
		$this->ctlr->output(true);
	}

	public function ytoUpload(){
		$md = time();
		foreach($this->ctlr->data as $r){
			if(empty($r->hbn)) continue;
			$p = YtoOms::model()->find('api_id = :aid AND hbn = :h', [':aid' => $this->ctlr->api_id, ':h' => $r->hbn]);
			if(empty($p)){
				$p = new YtoOms;
				$p->api_id = $this->ctlr->api_id;
				$p->hbn = $r->hbn;
				$p->status = 0;
				$p->ref = $r->ref;
			}
			$p->md = $md;
			$p->cnors = json_encode($r->cnor);
			$p->cnees = json_encode($r->cnee);
			$p->items = $r->items;
			$p->tpl = $r->tpl;
			$p->swt = $r->swt;
			$p->save();
		}
		$thread = new Thread('create');
		$thread->method = 'printYtoGlobal';
		$thread->mdata['api_id'] = $this->ctlr->api_id;
		$thread->mdata['md'] = $md;
		$thread->save();
		$thread->dispatch(2);
		$this->ctlr->output(true);
	}


	public function ytoGet(){
		if(empty($this->ctlr->data->bc)) $this->ctlr->output(false);

		$p = YtoOms::model()->find('api_id = :aid AND hbn = :h', [':aid' => $this->ctlr->api_id, ':h' => $this->ctlr->data->bc]);
		if(empty($p)) $this->ctlr->output(false);
		$rs = YtoOms::model()->findAll('api_id = :aid AND md = :md', [':aid' => $this->ctlr->api_id, ':md' => $p->md]);
		$ds = [];
		foreach($rs as $p){
			$ds[] = $p->getAttributes();
		}
		$this->ctlr->output($ds);
	}

	public function ytoList(){
		if(empty($this->ctlr->data->md)){
			$rs = YtoOms::model()->findAll(['condition' => 'api_id = :aid AND md > :t', 'params' => [':aid' => $this->ctlr->api_id, ':t' => strtotime('-7 Day')], 'group' => 'md']);
			if(empty($rs)){
				$this->ctlr->output(false);
			}else{
				$ds = [];
				foreach($rs as $r){
					$ds[] = [$r->md, $r->tpl];
				}
				$this->ctlr->output($ds);
			}
		}else{
			$rs = YtoOms::model()->findAll('api_id = :aid AND md = :t', [':aid' => $this->ctlr->api_id, ':t' => $this->ctlr->data->md]);
			if(empty($rs)){
				$this->ctlr->output(false);
			}else{
				$ds = [];
				foreach($rs as $r){
					$ds[] = [$r->hbn, $r->ref, empty($r->mdata['dtb'])? '' : $r->mdata['dtb']];
				}
				$this->ctlr->output($ds);
			}
		}

		
		if(empty($p)) $this->ctlr->output(false);
		$rs = YtoOms::model()->findAll('api_id = :aid AND md = :md', [':aid' => $this->ctlr->api_id, ':md' => $p->md]);
		$ds = [];
		foreach($rs as $p){
			$ds[] = $p->getAttributes();
		}
		$this->ctlr->output($ds);
	}
}
