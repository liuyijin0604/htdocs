<?php

class ShipmentController extends Controller{
	/**
	 * Declares class-based actions.
	 */
	protected $nonAjax=array('bulkPrint','print','print2','export','report');
	protected $skipAcl=array('auPcSuggest','cnCitySuggest','cnSuburbSuggest','prodSuggest', 'tracking');

	protected $org, $type;

	public function beforeAction($action){
		if(!Yii::app()->user->isGuest){
			$this->org = Org::model()->findByPk(Yii::app()->user->org);
			$this->type = 'ex';
			if(!empty($this->org)) $this->type = $this->org->type == 30? 'co' : 'ex';
		}

		return parent::beforeAction($action);
	}

	public function actionCreate(){
		$model = $this->type == 'co'? new CoParcel : new ExParcel;

		if(!empty($_POST)){
			$_POST = utf8zts::t2sArray($_POST);
			if(!empty($_POST['ExParcel']['hbn'])){
				$n = ExParcel::model()->find('status = 10 AND hbn = :h', [':h' => $_POST['ExParcel']['hbn']]);
				if(!empty($n)) $model = $n;
			}
			if(empty($model->cnor)) $model->cnor = new Addr;
			if(empty($model->cnee)) $model->cnee = new Addr;
			$model->cnor->attributes = $_POST['Cnor'];
			$model->cnor->save();
			if(!empty($this->org->extra['always_last_shipper'])){
				setcookie('last_cnor', json_encode($_POST['Cnor']));
				setcookie('last_note', $_POST['ExParcel']['note']);
			}
			if(in_array(Yii::app()->user->org, [1, 839])){
				setcookie('last_cref', $_POST['ExParcel']['cref']);
			}

			$model->cnee->attributes = $_POST['Cnee'];
			if(empty($model->cnee->postcode)){
				$model->cnee->postcode = CnArea::getZip($model->cnee->state, $model->cnee->city);
			}
			if($this->type != 'co') $model->cnee->country = 'PR China';
			$model->cnee->save();
			$model->attributes=$_POST[get_class($model)];
			$model->agent_id = Yii::app()->user->org;
			if(empty($model->hbn)) $model->hbn = $model->genHbn();
			if(!empty($_POST['serivce_pv'])) $model->mdata['pv'] = 1;
			$model->mdata['client_entry'] = $_POST;
			if(empty($model->cnor_id)) $model->cnor_id = $model->cnor->id;
			if(empty($model->cnee_id)) $model->cnee_id = $model->cnee->id;
			$items = $_POST['items'];
			$ptyp = array_flip(ExProdb::$rtypes);
			foreach($items['q'] as $i=>$qty){
				if(empty($qty)){
					unset($items['type'][$i]);
					unset($items['q'][$i]);
					unset($items['g'][$i]);
					unset($items['v'][$i]);
					continue;
				}
				$pd = ExProdb::model()->find('id = :id', [':id' => $items['pid'][$i]]);
				if(!empty($pd)){
					$items['type'][$i] = $ptyp[$pd->type];
					$items['b'][$i] = $pd->brand;
					$items['m'][$i] = $pd->model;
					$items['hs'][$i] = $pd->hs;
					$items['w'][$i] = $pd->weight * $qty;
					$items['v'][$i] = $pd->price;
					$items['u'][$i] = $pd->unit;
					$items['t'][$i] = $pd->tax;
				}
			}
			$model->eitems = $items;
			$model->status = 10;
			$model->state = $model->cnee->state;
			$model->postcode = $model->cnee->postcode;
			$model->cbwf = 1;
			$model->save();
			$ids = [$model->id];

			if(!empty($_POST['multi']) && $_POST['multi'] > 1 && $_POST['multi'] < 11){
				for($i = 1; $i < $_POST['multi']; $i++){
					$p = $model->duplicate();
					$ids[] = $p->id;
				}
			}
			$this->ajaxResult($model, ['id', 'hbn'], 'Shipment created successfully.', ['ids' => $ids, 'print' => in_array(Yii::app()->user->org, [1, 839])? 2 : 1]);
		}
		if(!empty($_GET['copy'])){
			$model = $this->type == 'co'? CoParcel::model()->findByPk($_GET['copy']) : ExParcel::model()->findByPk($_GET['copy']);
		}

		$this->render('create', ['model' => $model, 'type' => $this->type, 'org' => $this->org]);
	}

	public function actionManage(){
		if($this->type == 'co'){
			$model = new CoParcel('search');
		}else{
			$model = new ExParcel('search');
		}
		$model->unsetAttributes();
		$model->dbCriteria->order = 't.id DESC';
		if(!empty($_GET['ExParcel'])){
			$model->attributes = $_GET['ExParcel'];
		}elseif(!empty($_GET['CoParcel'])){
			$model->attributes = $_GET['CoParcel'];
		}
		if(isset(Yii::app()->user->org) && Yii::app()->user->org > 1) $model->agent_id = Yii::app()->user->org;
		$this->render('manage', ['model' => $model, 'type' => $this->type]);
	}

	public function actionWeigh() {
		$this->render('weigh');
	}

	public function actionWeighComplete() {
		if ($_POST) {
			$result = [];
			foreach ($_POST['shipment'] as $index => $shipment) {
				$exparcel = ExParcel::model()->find('hbn = :no AND agent_id = :org', array(':no' => $shipment, ':org' => Yii::app()->user->org));
				if (!empty($exparcel)) {
					$exparcel->weight = $_POST['weight'][$index];
					$exparcel->update(['weight']);
					$result[] = array('shipment' => $shipment, 'result' => 1);
				} else {
					$result[] = array('shipment' => $shipment, 'result' => 0);
				}
			}

			if (!empty($this->org->extra['pickup_when_weigh']) && !empty($this->org->extra['pos_pu'])) {
				$model = PickupList::model()->find('fwd_id = :oid AND created >= :today AND bwf & 2 != 2', [':oid' => Yii::app()->user->org, ':today' => date('Y-m-d')]);
				if(empty($model)){
					$model = new PickupList('create');
					$model->fwd_id = Yii::app()->user->org;
					$model->ref = $model->fwd_id . '-' . date('ymdH', empty($model->created) ? time() : strtotime($model->created));
					$model->dpt_id = 106;
					$model->mdata['cc_status'] = 10;
					$model->save();
				}
				foreach ($_POST['shipment'] as $c) {
					$c = trim(strtoupper($c));
					$p = ExParcel::model()->find('hbn = :h', [':h' => $c]);
					if(!empty($p) && $p->agent_id != Yii::app()->user->org){
						$result[] = array('shipment' => $shipment, 'result' => 2, 'msg' => 'not found');
						continue;
					}else{
						if(empty($p)){
							$p = new ExParcel('create');
							$p->agent_id = Yii::app()->user->org;
							$p->hbn = $c;
							$p->save();
						}
						$map = true;
						$save = false;
						if($p->status < 12){
							$ht = Tracking::model()->count('pid = :pid AND type = :t', array(':pid' => $p->id, ':t' => 12));
							if(empty($ht)) $p->addTracking(12, 'Consignment Picked Up');
						}
						if(empty($p->odpt_id)){
							$p->odpt_id = $model->dpt_id;
							$save = true;
						}

						if($model->hasMap($p)){
							$map = false;
						}else{
							$rs = ManiMap::model()->findAll("fid = :id AND model = 'ExParcel'", [':id' => $p->id]);
							foreach($rs as $m){
								if($m->mani_id == $model->id) continue;
								if($m->manifest->type == 40){
									$result[] = array('shipment' => $shipment, 'result' => 2, 'msg' => 'already picked');
									$map = false;
								}
							}
						}

						if($map){
							$model->map($p);
							$p->agent_id = $model->fwd_id;
							$save = true;
						}
						if($save) $p->save();
					}
				}
			}
		}

		echo json_encode($result);
		Yii::app()->end();
	}

	public function actionExport(){
		if($this->type == 'co'){
			$model = new CoParcel('search');
		}else{
			$model = new ExParcel('search');
		}
		$model->unsetAttributes();
		$model->dbCriteria->order = 't.id DESC';
		if(!empty($_GET['ExParcel'])){
			$model->attributes = $_GET['ExParcel'];
		}elseif(!empty($_GET['CoParcel'])){
			$model->attributes = $_GET['CoParcel'];
		}
		if(Yii::app()->user->org > 1) $model->agent_id = Yii::app()->user->org;
		$dp = $model->search(false);

		$criteria = new CDbCriteria;
		$criteria->condition = "t.created >= '".$_POST['start']."' AND t.created < DATE_ADD('".$_POST['end']."', INTERVAL 1 DAY)";

		$dp = $model->search(false, 0, $criteria);

		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, array('Connote', 'Cust. Ref', 'Courier Ref', 'Status', 'Sender', 'Sender Tel', 'Cnee Name', 'Cnee Tel', 'Address', 'State', 'Weight', 'Goods Type', 'Goods Detail', 'Date'));

		foreach($dp->data as $r){
			if(empty($r->cnee) || empty($r->eitems['type'])) continue;
			$ts = [];
			if(!is_array($r->eitems['type'])){
				echo $r->hbn;
				continue;
			}
			foreach($r->eitems['type'] as $t){
				$ts[] = $t;
			}
			if(!empty($r->mdata['client_entry'])){
				$cltent = $r->mdata['client_entry'];
				if(!empty($cltent['items'])) $r->eitems = $cltent['items'];
			}
			$gds = [];
			foreach($r->eitems['g'] as $gi=>$g){
				$gds[] = $g.' * '.$r->eitems['q'][$gi];
			}
			$ts = array_unique($ts);
			$xls->addRow($i++, array('="'.$r->hbn.'"', empty($r->cref)? '' : '="'.$r->cref.'"', '="'.$r->ref.'"', $r->getStatus(), $r->cnor->name, '="'.$r->cnor->tel.'"', $r->cnee->name, '="'.$r->cnee->tel.'"', $r->cnee->address, $r->cnee->state, $r->weight, implode(' ', $ts), implode(', ', $gds), $r->created));
		}
		$xls->output('shipment_export_'.time().'.xlsx');
	}

	public function actionReport(){
		if($this->type == 'co'){
			$model = new CoParcel('search');
		}else{
			$model = new ExParcel('search');
		}

		switch($_GET['type']){
			case 'noid':
				$rs = $model->with('cnee')->findAll('agent_id = :aid AND status < 18 AND cnee.cnid_id = 0', [':aid' => Yii::app()->user->org]);
			break;
			case 'nop':
				$rs = $model->findAll('agent_id = :aid AND status NOT IN (100,102) AND cbwf = 1', [':aid' => Yii::app()->user->org]);
			break;
		}

		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, array('Connote', 'Reference', 'Status', 'Sender', 'Sender Tel', 'Cnee Name', 'Cnee Tel', 'Address', 'State', 'Weight', 'Goods Type', 'Date'));

		foreach($rs as $r){
			if(empty($r->cnee) || empty($r->eitems['type'])) continue;
			$ts = [];
			if(!is_array($r->eitems['type'])){
				echo $r->hbn;
				continue;
			}
			foreach($r->eitems['type'] as $t){
				$ts[] = $t;
			}
			$ts = array_unique($ts);
			$xls->addRow($i++, array('="'.$r->hbn.'"', empty($r->cref)? '' : '="'.$r->cref.'"', $r->getStatus(), $r->cnor->name, '="'.$r->cnor->tel.'"', $r->cnee->name, '="'.$r->cnee->tel.'"', $r->cnee->address, $r->cnee->state, $r->weight, implode(' ', $ts), $r->created));
		}
		$xls->output('shipment_report_'.time().'.xlsx');
	}
	
	public function actionBulkPrint($s, $fn){
		if($this->type == 'co'){
			$model = new CoParcel('search');
		}else{
			$model = new ExParcel('search');
		}

		switch($s){
			case 'nop':
				$rs = $model->findAll('agent_id = :aid AND status NOT IN (100,102) AND cbwf = 1', [':aid' => Yii::app()->user->org]);
			break;
			case 'irnop':
				$rs = $model->findAll('agent_id = :aid AND status >= 18 AND status NOT IN (100,102) AND cbwf = 1', [':aid' => Yii::app()->user->org]);
			break;
			default:
				$model->mids = explode(',', $s);
				$dp = $model->search(false);
				$rs = $dp->data;
			break;
		}

		foreach($rs as $r){
			if(($r->cbwf & 1) > 0){
				$r->cbwf -= 1;
				$r->custom_log_note = 'Printed';
				$r->save();
			}

			if(!empty($r->mdata['client_entry'])){
				$cltent = $r->mdata['client_entry'];
				if(!empty($cltent['items'])){
					$r->eitems = $cltent['items'];
				}
			}
		}
		oPDF::renderPDF('label_A6', array('tpl' => ($this->type == 'ex'? '_label-ex' : '_label'), 'empty' => false, 'rs' => $rs), 1, $fn.'.pdf');
	}
	
	public function actionMarkPrint(){

	}
	
	public function actionPrint($id, $fn){
		$model = $this->loadModel($id);
		if(($model->cbwf & 1) > 0){
			$model->cbwf -= 1;
			$model->custom_log_note = 'Printed';
			$model->save();
		}
		if(!empty($model->mdata['client_entry'])){
			$cltent = $model->mdata['client_entry'];
			if(!empty($cltent['items'])){
				$model->eitems = $cltent['items'];
			}
		}
		oPDF::renderPDF('label_A6', array('tpl' => ($this->type == 'ex'? '_label-ex' : '_label'), 'empty' => false, 'rs' => [$model]));
	}

	public function actionPrint2(){
		if(!empty($_GET['ids'])){
			$ids = explode(',', $_GET['ids']);
			$rs = [];
			foreach($ids as  $id){
				$model = $this->loadModel($id);
				if(($model->cbwf & 1) > 0){
					$model->cbwf -= 1;
					$model->custom_log_note = 'Printed';
					$model->save();
				}
				if(!empty($model->mdata['client_entry'])){
					$cltent = $model->mdata['client_entry'];
					if(!empty($cltent['items'])){
						$model->eitems = $cltent['items'];
					}
				}
				$rs[] = $model;
			}
			// $this->renderPartial('label_dm', ['model' => $model, 'org' => $this->org]);
			Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
			$this->renderPartial('label_ex2', ['rs' => $rs]);
		}
	}
	
	public function actionUpdate($id){
		$model = $this->loadModel($id);
		if(!Acl::hasAccess('B:Export/SeeAllShipments') && !empty($model->agent_id) && $model->agent_id != Yii::app()->user->org && !preg_match('/^(E|D)AU'.Yii::app()->user->org.'[0-9]{6,9}$/', $model->hbn)) Acl::denied403();
		
		if(!empty($_POST)){
			$_POST = utf8zts::t2sArray($_POST);
			$model->cnor->attributes = $_POST['Cnor'];
			$model->cnor->save();
			$model->cnee->attributes = $_POST['Cnee'];
			$model->cnee->save();
			$model->attributes=$_POST[get_class($model)];
			$model->mdata['client_entry'] = $_POST;
			if(empty($model->cnor_id)) $model->cnor_id = $model->cnor->id;
			if(empty($model->cnee_id)) $model->cnee_id = $model->cnee->id;
			$model->eitems = $_POST['items'];
			$model->state = $model->cnee->state;
			$model->postcode = $model->cnee->postcode;
			$model->save();
			$this->ajaxResult($model, ['id', 'hbn', 'status'], 'Shipment updated successfully.');
		}

		if(!empty($model->mdata['client_entry'])){
			$cltent = $model->mdata['client_entry'];
			if(!empty($cltent['items'])){
				$model->eitems = $cltent['items'];
			}
		}
		$this->render(($model->status < ($this->type == 'ex'? 20 : 30))? 'update' : 'view', ['model' => $model, 'type' => $this->type, 'org' => $this->org]); // && !empty($model->cnee->name)
	}

	public function actionAuPcSuggest(){
		$this->forward('/postcode/suggest');
	}

	public function actionCnCitySuggest(){
		$this->forward('/cnZip/suggestCity');
	}

	public function actionCnSuburbSuggest(){
		$this->forward('/cnZip/suggestSuburb');
	}

	public function actionCnorSuggest(){
		if($this->type == 'co'){
			$model = new CoParcel('search');
		}else{
			$model = new ExParcel('search');
		}
		$rs = $model::model()->with('cnor')->findAll([
				'condition' => 't.agent_id = :a AND (cnor.name LIKE :t OR cnor.tel LIKE :t)',
				'order' => 'cnor.name,cnor.tel',
				'group' => 'cnor.name,cnor.tel',
				'params' => [':a' => Yii::app()->user->org, ':t' => $_GET['term'].'%']
			]);
		$a = [];
		foreach($rs as $r){
			$attr = $r->cnor->attributes;
			unset($attr['id'], $attr['cnid_id'], $attr['owner_id'], $attr['acc']);
			$a[] = $attr + ['label' => $r->cnor->name.'/'.$r->cnor->tel];
		}
		echo json_encode($a);
	}

	public function actionCneeSuggest($id = 0){
		if($this->type == 'co'){
			$rs = CoParcel::model()->with('cnee')->findAll(array(
				'condition' => "t.agent_id = :agt AND cnee.id IN (SELECT MAX(id) FROM addr a WHERE a.city != '' AND a.tel != '' AND a.id != :id AND (a.name LIKE :t OR a.tel LIKE :t) GROUP BY name,tel)",
				'params' => array(':agt' => Yii::app()->user->org, ':t' => $_GET['term'].'%', ':id' => $id),
				'order' => 'cnee.id DESC',
				'limit' => 20,
			));
			$a = array();
			foreach($rs as $s){
				$r = $s->cnee;
				$attr = $r->attributes;
				unset($attr['id'], $attr['cnid_id'], $attr['owner_id'], $attr['acc']);
				$a[] = $attr + array(
					'value' => $r->name,
					'label' => $r->name.' ('.$r->state.'/'.$r->tel.')',
				);
			}
		}else{
			$rs = ExParcel::model()->with('cnee')->findAll(array(
				'condition' => "t.agent_id = :agt AND cnee.id IN (SELECT MAX(id) FROM addr a WHERE a.city != '' AND a.tel != '' AND a.id != :id AND (a.name LIKE :t OR a.tel LIKE :t) GROUP BY name,tel)",
				'params' => array(':agt' => Yii::app()->user->org, ':t' => $_GET['term'].'%', ':id' => $id),
				'order' => 'cnee.id DESC',
				'limit' => 20,
			));

			$a = array();
			foreach($rs as $s){
				$r = $s->cnee;
				$attr = $r->attributes;
				unset($attr['id'], $attr['cnid_id'], $attr['owner_id'], $attr['acc']);
				$a[] = $attr + array(
					'value' => $r->name,
					'label' => $r->name.' ('.$r->city.'/'.$r->tel.')',
					'cnid' => $r->cnid_id,
					'cnid_no' => $r->cnid_id > 0? $r->cnid->no : '',
				);
			}
		}
		echo json_encode($a);
	}

	public function actionProdSuggest(){
		$a = [];
		if(SellRate::hasExRate($this->org->id, 'EC')){
			$prs = ExProdbPrice::model()->with('prod')->findAll(array(
				'condition' => "t.status = 1 AND (prod.code = :t OR CONCAT(',', prod.tag,',') LIKE :cn OR prod.sku = :t OR t.name LIKE :tn OR prod.name_zh LIKE :tn OR t.sn = :t)",
				'params' => [':t' => $_GET['term'], ':tn' => '%'.$_GET['term'].'%', ':cn' => '%,'.$_GET['term'].',%']
				));
			foreach($prs as $r){
				$tr = HS::getUpr($r->prod->hs, 'rate') * 100;
				$tr = empty($tr)? 15 : $tr;
				$a[] = ['pid' => $r->prod->id, 'label' => $r->getName(), 'price' => $r->getPrice(), 'r1' => $tr, 'r2' => '999'];
			}
		}else{
			$prs = ExProdb::model()->findAll(array(
					'condition' => "type = :t AND (code = :n OR CONCAT(',',tag,',') LIKE :tn OR name_zh LIKE :fn OR sku = :n)",
					'params' => array(':t' => ExProdb::$rtypes[$_GET['t']], ':fn' => '%'.$_GET['term'].'%', ':n' => $_GET['term'], ':tn' => '%,'.$_GET['term'].',%'),
				));
			foreach($prs as $r){
				if($_GET['t'] == 'O'){
					if(empty($r->tag)) continue;
					$tgs = explode(',', $r->tag);
					foreach($tgs as $t){
						if(strpos($t, $_GET['term']) !== false && !in_array($t, $a)){
							$tr = HS::getUpr($r->hs, 'rate') * 100;
							$a[] = ['pid' => $r->id, 'label' => $t];
							break;
						}
					}
				}else{
					$tr = HS::getUpr($r->hs, 'rate') * 100;
					$a[] = ['pid' => $r->id, 'label' => $r->name_zh];
				}
			}
		}
		echo json_encode($a);
	}

	public function actionPasteAddr(){
		$p = str_replace('、', ',', trim($_GET['p']));
		$p = AppHelper::semiAngle($p);
		$p = preg_replace('/:\s+/', ':', $p);
		$ps = preg_split('/[\s,;]+/', $p);
		$r = ['name' => '', 'tel' => '', 'addr' => '', 'cnid_no' => ''];
		$k = null;
		foreach($ps as $p){
			if(in_array($p, ['身份证', '收件人', '联系人', '地址', '电话'])) continue;

			if(preg_match('/^(.+):(.+)/', $p, $m)){
				$k = null;
				if(preg_match('/电话|手机|号码/', $m[1])){
					$k = 'tel';
				}elseif(preg_match('/地址|寄到/', $m[1])){
					$k = 'addr';
				}elseif(preg_match('/人|姓名/', $m[1])){
					$k = 'name';
				}elseif(preg_match('/身份证/', $m[1])){
					$k = 'cnid_no';
				}
				$p = $m[2];
			}

			if(preg_match('/^\d{18}$/', $p)){
				$r['cnid_no'] = $p;
			}elseif(preg_match('/([\d\- \(\)]{9,16})/', $p, $m)){
				$r['tel'] = preg_replace('/[ \(\)\-]+/', '', $m[1]);
				$p = preg_replace('/[\d\- \(\)]{9,16}/', '', $p);
				if(($k == 'addr' || mb_strlen($p) > 5 || preg_match('/(省|市|区|县)$/', $p)) && preg_match('/省|市|区|县|镇|村|乡|路|街|号|楼|室/', $p)){
					$r['addr'] .= rtrim($p, '.');
				}elseif(preg_match('/[\x{4e00}-\x{9fa5}·\.]+/u', $p)){
					$r['name'] = rtrim($p, '.');
				}
			}elseif(($k == 'addr' || mb_strlen($p) > 5 || preg_match('/(省|市|区|县)$/', $p)) && preg_match('/省|市|区|县|镇|村|乡|路|街|号|楼|室/', $p)){
				$r['addr'] .= rtrim($p, '.');
			}elseif(preg_match('/[\x{4e00}-\x{9fa5}·\.]+/u', $p)){
				if(mb_strlen($p) == 2 && (CnProvince::model()->count('name LIKE :p', array(':p' => $p."%")) > 0 || CnCity::model()->count('name LIKE :p', array(':p' => $p."%")) > 0)){
						$r['addr'] .= $p;
						continue;
				}
				
				$r['name'] = rtrim($p, '.');
			}elseif(!empty($k)){
				$r[$k] .= $p;
			}
		}
		if(empty(!$r['addr'])){
			$a = new Addr();
			$a->setCnAddr($r['addr']);
			$r['state'] = $a->state;
			$r['city'] = $a->city;
			$r['suburb'] = $a->suburb;
			$r['address'] = $a->address;
			$r['postcode'] = $a->postcode;
		}
		echo json_encode($r);
	}

	public function actionPasteItem(){
		$p = AppHelper::semiAngle(trim($_GET['p']));
		$p = preg_replace('/[ \t]+/', ' ', $p);
		$p = preg_replace('/^.+?:/', '', $p);
		$p = preg_replace('/(\d+)(个|瓶|罐|袋|件|支|盒)/', 'x\\1,', $p);
		$p = preg_replace('/([x\*]+\s*\d+)[\s,;\.]+/', '\\1,', $p);
		$p = rtrim($p, ',');
		$ps = preg_split('/[\r\n,;\.]+/u', $p);
		$r = ['type' => [], 'g' => [], 'q' => []];
		//var_dump($ps);
		foreach($ps as $i=>$g){
			$n = trim($g);
			if(empty($n)) continue;

			if(preg_match('/^(\d+)[ x*]+(.+)$/i', $n, $m)){
				$q = $m[1];
				$n = trim($m[2]);
			}elseif(preg_match('/^(.+)[ x*]+(\d+)$/i', $n, $m)){
				$q = $m[2];
				$n = trim($m[1]);
			}else{
				$qs = [];
				$qs[] = [mb_substr($g,0,1), 2, null];
				$qs[] = [mb_substr($g,-2,1), 0, -2];
				$qs[] = [mb_substr($g,-1,1), 0, -1];
				foreach($qs as $s){
					$q = str_replace(['一','二','两','三','四','五','六','七','八','九'], [1,2,2,3,4,5,6,7,8,9], $s[0]);
					$q = floatval($q);
					if($q > 0){
						$n = trim(mb_substr($g, $s[1],$s[2]));
						break;
					}
				}
			}
			if(empty($q)) continue;

			$t = 'O';
			if(preg_match('/成人奶粉|成人羊奶|全脂|脱脂|德运|安素|蓝胖/', $n)){
				$t = 'M';
			}elseif(preg_match('/婴儿奶粉|[1234一二三四]+段/', $n)){
				$t = 'B';
			}
			$r['type'][$i] = $t;
			$r['g'][$i] = $n;
			$r['q'][$i] = $q;
		}
		if(empty($r['q'])) $r['g'][0] = $_GET['p'];
		echo json_encode($r);
	}
	
	public function actionTracking(){
		if(!empty($_GET['c'])){
			$cs = preg_split('/[\s,;]+/', trim($_GET['c']));
			$rs = [];
			foreach($cs as $c){
				$p = Shipment::model()->find('hbn = :c', array(':c' => $c));

				if(empty($p)){
					echo '<h2>'.$this->t('Consignment not found').': '.$c.'</h2>';
					continue;
				}
				$o = $p->trackingInfo();
				if(empty($o)){
					echo '<h2>'.$c.' '.$this->t('has no tracking info').'</h2>';
					continue;
				}
			echo '<h2>'.$this->t('Consignment').': '.$c.'</h2>';
	echo '<div class="gd-summary">
		<div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <label class="lbl">'.$this->t('Status').': </label>
          <span>'.$o->status.'</span>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <label class="lbl">'.$this->t('Departure Depot').': </label>
          <span>'.$o->odpt.'</span>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <label class="lbl">'.$this->t('Departure Date').': </label>
          <span>'.$o->odate.'</span>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <label class="lbl">'.$this->t('Service').': </label>
          <span>'.$this->t('Express').'</span>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <label class="lbl">'.$this->t('Delivery Depot').': </label>
          <span>'.$o->ddpt.'</span>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <label class="lbl">'.$this->t('Estimated Dispatch Date').': </label>
          <span>'.$o->eta.'</span>
        </div>
      </div>
	</div>';
	  
	echo '<table cellspacing="0" cellpadding="0" border="0" id="lvTrackingGrid" class="gvTable" style="width:100%">
	<thead>
    <tr class="thead">
      <th style="width: 20%" align="left">'.$this->t('Date &amp; Time').' </th>
      <th align="left">'.$this->t('Description').' </th>
      <th style="width: 10%" align="left">'.$this->t('Depot').' </th>
    </tr>
	</thead>
	<tbody>
	';
	foreach($o->tracks as $t){
		$tm = isset($msgmap[$t[0]])? $msgmap[$t[0]]: $t[0];
		echo '<tr>
      <td>'.$t[2].'</td>
      <td>'.(empty($t->mdata['signature'])? '' : '<img src="data:image/png;base64,'.$t->mdata['signature'].'" width="150" /><br />').nl2br($tm).'</td>
      <td>'.$t[1].'</td>
    </tr>';
	}
    
  echo '</tbody></table>';
	if($p->status == 80){
		echo 'EMS: <a href="http://www.kuaidi100.com/all/ems.shtml?mscomnu='.$p->ref.'" target="_blank">'.$p->ref.'</a>';
	}else{
		foreach($p->trans as $ts){
			echo $ts->infoLink().'<br />';
		}
	}
			}
			Yii::app()->end();
		}
		$this->render('tracking');
	}

	public function actionUpid($id){
		$model = $this->loadModel($id);
		$this->render('upid', ['model' => $model]);
	}

	public function loadModel($id){
		if($this->type == 'co'){
			$model = CoParcel::model()->findByPk($id);
		}else{
			$model = ExParcel::model()->findByPk($id);
		}
		
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
}