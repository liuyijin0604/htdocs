<?php

class DirectController extends Controller{
	/**
	 * Declares class-based actions.
	 */
	protected $skipAcl=array('auPcSuggest','cnCitySuggest','cnSuburbSuggest','prodSuggest', 'tracking');

	protected $type;

	public function init(){
		if(!Yii::app()->user->isGuest){
			$org = Org::model()->findByPk(Yii::app()->user->org);
			$this->type = $org->type == 30? 'co' : 'ex';
		}
		parent::init();
	}

	public function actionCreate(){
		$model = new ExDirect;

		if(!empty($_POST)){
			if(empty($model->cnor)) $model->cnor = new Addr;
			if(empty($model->cnee)) $model->cnee = new Addr;
			$model->cnor->attributes = $_POST['Cnor'];
			$model->cnor->save();
			$model->cnee->attributes = $_POST['Cnee'];
			$model->cnee->save();
			$model->attributes=$_POST[get_class($model)];
			$model->agent_id = Yii::app()->user->org;
			$model->hbn = $model->genHbn();
			$model->cref = $_POST['meta']['cref'];
			if(empty($model->cnor_id)) $model->cnor_id = $model->cnor->id;
			if(empty($model->cnee_id)) $model->cnee_id = $model->cnee->id;
			$model->eitems = $_POST['items'];
			$model->status = 10;
			$model->state = $model->cnee->state;
			$model->postcode = $model->cnee->postcode;
			$model->save();
			$this->ajaxResult($model, ['id', 'hbn'], 'Shipment created successfully.');
		}

		$this->render('create', ['model' => $model, 'type' => $this->type]);
	}

	public function actionManage(){
		$model = new ExDirect('search');
		$model->unsetAttributes();
		$model->dbCriteria->order = 't.id DESC';
		if(!empty($_GET['ExDirect'])) $model->attributes = $_GET['ExDirect'];
		
		if(isset(Yii::app()->user->org) && Yii::app()->user->org > 1) $model->agent_id = Yii::app()->user->org;
		$this->render('manage', ['model' => $model, 'type' => $this->type]);
	}

	public function actionExport(){
		$model = new ExDirect('search');
		$model->unsetAttributes();
		$model->dbCriteria->order = 't.id DESC';
		if(!empty($_GET['ExDirect'])) $model->attributes = $_GET['ExDirect'];
		if(Yii::app()->user->org > 1) $model->agent_id = Yii::app()->user->org;
		$dp = $model->search(false);

		$criteria = new CDbCriteria;
		$criteria->condition = "t.created >= '".$_POST['start']."' AND t.created < DATE_ADD('".$_POST['end']."', INTERVAL 1 DAY)";

		$dp = $model->search(false, 0, $criteria);

		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, array('Connote', 'Reference', 'Status', 'Sender', 'Sender Tel', 'Cnee Name', 'Cnee Tel', 'Address', 'State', 'Weight', 'Goods Type', 'Date'));

		foreach($dp->data as $r){
			if(empty($r->cnee)) continue;
			$ts = [];
			foreach($r->eitems['type'] as $t){
				$ts[] = $t;
			}
			$ts = array_unique($ts);
			$xls->addRow($i++, array('="'.$r->hbn.'"', empty($r->cref)? '' : $r->cref, $r->getStatus(), $r->cnor->name, '="'.$r->cnor->tel.'"', $r->cnee->name, '="'.$r->cnee->tel.'"', $r->cnee->address, $r->cnee->state, $r->weight, implode(' ', $ts), $r->created));
		}
		$xls->output('shipment_export_'.time().'.xlsx');
	}
	
	public function actionUpdate($id){
		$model = $this->loadModel($id);
		if(!Acl::hasAccess('B:Export/SeeAllShipments') && !empty($model->agent_id) && $model->agent_id != Yii::app()->user->org) Acl::denied403();
		
		if(!empty($_POST)){
			if(!empty($_POST['pay'])){
				if($model->status == 10) $model->status = 20;
			}else{
				$model->cnor->attributes = $_POST['Cnor'];
				$model->cnor->save();
				$model->cnee->attributes = $_POST['Cnee'];
				$model->cnee->save();
				$model->attributes=$_POST[get_class($model)];
				if(empty($model->cnor_id)) $model->cnor_id = $model->cnor->id;
				if(empty($model->cnee_id)) $model->cnee_id = $model->cnee->id;
				$model->eitems = $_POST['items'];
				$model->cref = $_POST['meta']['cref'];
				$model->state = $model->cnee->state;
				$model->postcode = $model->cnee->postcode;
			}
			$model->save();
			$this->ajaxResult($model, [], 'Shipment updated successfully.');
		}
		$this->render(($model->status < 50 && !empty($model->cnee->name))? 'update' : 'view', ['model' => $model, 'type' => $this->type]);
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
			$model = new ExDirect('search');
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
				'condition' => "t.agent_id = :agt AND cnee.tel != '' AND cnee.id != :id AND (cnee.name LIKE :t OR cnee.tel LIKE :t)",
				'params' => array(':agt' => Yii::app()->user->org, ':t' => $_GET['term'].'%', ':id' => $id),
				'group' => 'cnee.name,cnee.tel',
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
			$rs = ExDirect::model()->with('cnee')->findAll(array(
				'condition' => "t.agent_id = :agt AND cnee.city != '' AND cnee.tel != '' AND cnee.id != :id AND (cnee.name LIKE :t OR cnee.tel LIKE :t)",
				'params' => array(':agt' => Yii::app()->user->org, ':t' => $_GET['term'].'%', ':id' => $id),
				'group' => 'cnee.name,cnee.tel',
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
		$this->forward('/exprod/suggest');
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
			}
			Yii::app()->end();
		}
		$this->render('tracking');
	}

	public function loadModel($id){
		$model = ExDirect::model()->findByPk($id);
		
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
}