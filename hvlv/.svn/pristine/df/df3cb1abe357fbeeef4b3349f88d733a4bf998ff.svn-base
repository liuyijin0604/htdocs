<?php
$hash = empty($_GET['ExParcel'])? '' : '-'.hash('crc32b', json_encode($_GET['ExParcel']));
$data = Yii::app()->cache->get('wid_expdelay_data'.$hash);
if(empty($data)){
	$q = 'status IN (14,15,18,80,90)';
	$p = [];
	if(!empty($_GET['ExParcel']['hbn'])){
		$q .= ' AND hbn LIKE :hbn';
		$p['hbn'] = '%'.$_GET['ExParcel']['hbn'].'%';
	}
	if(!empty($_GET['ExParcel']['status'])){
		$q .= ' AND status = :st';
		$p['st'] = $_GET['ExParcel']['status'];
	}
	$dq = 2;
	$dqs = 'gt';
	if(!empty($_GET['ExParcel']['delay'])){
		$dq = $_GET['ExParcel']['delay'];
		$dqs = 'eq';
		if($dq[0] == '>'){
			$dqs = 'gt';
			$dq = (int) substr($dq,1);
		}elseif($dq[0] == '<'){
			$dqs = 'lt';
			$dq = (int) substr($dq,1);
		}else{
			$dq = (int) $dq;
		}
	}

	$rs = ExParcel::model()->findAll($q, $p);
	$data = [];
	foreach($rs as $r){
		$ln = $r->getLastLog();
		if(empty($ln)) continue;
		$dd = ceil((time() - strtotime($ln->time)) / 86400);
		$ad = false;
		switch($dqs){
			case 'eq':
				$ad = $dd == $dq;
			break;
			case 'gt':
				$ad = $dd > $dq;
			break;
			case 'lt':
				$ad = $dd < $dq && $dd > 2;
			break;
		}
		if($ad){
			$data[] = ['id' => $r->id, 'hbn' => $r->hbn, 'status' => $r->getStatus(), 'delay' => $dd];
		}
	}
	Yii::app()->cache->set('wid_expdelay_data'.$hash, $data, 300);
}

$sort = new CSort();
$sort->attributes = array('delay',);
$sort->defaultOrder = array(
	'delay'=>CSort::SORT_DESC,
);

$dp = new CArrayDataProvider($data, array(
	'keyField' => 'id',
	'sort' => $sort,
	'pagination' => array(
		'pageSize' => '15',
		'route' => 'dash/widget',
		'params' => ['name' => 'expdelay', 'ExParcel[hbn]' => empty($_GET['ExParcel']['hbn'])? '' : $_GET['ExParcel']['hbn'], 'ExParcel[status]' => empty($_GET['ExParcel']['status'])? '' : $_GET['ExParcel']['status'], 'ExParcel[delay]' => empty($_GET['ExParcel']['delay'])? '' : $_GET['ExParcel']['delay']],
	),
));

$model = new ExParcel('search');
$model->unsetAttributes();
if(!empty($_GET['ExParcel'])) $model->attributes = $_GET['ExParcel'];

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'dashboard-wid-expdelay-grid',
	'cssFile' => false,
	'dataProvider'=>$dp,
	'summaryText' => '',
	'enablePagination' => true,
	'filter' => $model,
	'pager'=>array(
		'prevPageLabel'=>'Prev.',
		'maxButtonCount' => 5,
	),
	'rowCssClassExpression' => '
		( $row & 2 ? $this->rowCssClass[1] : $this->rowCssClass[0] ) ." ".
		AppHelper::getDelayClass($data["delay"])
	',
	'columns'=>array(
		array('name' => 'hbn', 'header' => 'HBN', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl("exParcel/update", array("id" => $data["id"]))."\" class=\"tab_link\" title=\"".$data["hbn"]."\">".$data["hbn"]."</a>"',),
		array('name' => 'status', 'header' => 'Status',
			'filter'=>CHtml::dropDownList('ExParcel[status]', $model->status, $this->t(ExParcel::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'delay', 'header' => 'Delay',
			'filter'=>CHtml::textField('ExParcel[delay]', $model->delay, array('placeholder'=>'5, >5, <5')),),
	),
));