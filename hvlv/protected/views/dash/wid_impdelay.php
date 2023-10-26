<?php
$hash = empty($_GET['ImParcel'])? '' : '-'.hash('crc32b', json_encode($_GET['ImParcel']));
$data = Yii::app()->cache->get('wid_impdelay_data'.$hash);
if(empty($data)){
	$q = 'status > 40 AND status < 90';
	$p = [];
	if(!empty($_GET['ImParcel']['hbn'])){
		$q .= ' AND hbn LIKE :hbn';
		$p['hbn'] = '%'.$_GET['ImParcel']['hbn'].'%';
	}
	if(!empty($_GET['ImParcel']['status'])){
		$q .= ' AND status = :st';
		$p['st'] = $_GET['ImParcel']['status'];
	}
	$dq = 2;
	$dqs = 'gt';
	if(!empty($_GET['ImParcel']['delay'])){
		$dq = $_GET['ImParcel']['delay'];
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

	$rs = ImParcel::model()->findAll($q, $p);
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
	Yii::app()->cache->set('wid_impdelay_data'.$hash, $data, 300);
}

$sort = new CSort();
$sort->attributes = array('delay',);
$sort->defaultOrder = array(
	'delay'=>CSort::SORT_DESC,
);

$model = new ImParcel('search');
$model->unsetAttributes();
if(!empty($_GET['ImParcel'])) $model->attributes = $_GET['ImParcel'];

$dp = new CArrayDataProvider($data, array(
	'keyField' => 'id',
	'sort' => $sort,
	'pagination' => array(
		'pageSize' => '15',
		'route' => 'dash/widget',
		'params' => ['name' => 'impdelay', 'ImParcel[hbn]' => empty($_GET['ImParcel']['hbn'])? '' : $_GET['ImParcel']['hbn'], 'ImParcel[status]' => empty($_GET['ImParcel']['status'])? '' : $_GET['ImParcel']['status'], 'ImParcel[delay]' => empty($_GET['ImParcel']['delay'])? '' : $_GET['ImParcel']['delay']],
	),
));

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'dashboard-wid-impdelay-grid',
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
		array('name' => 'hbn', 'header' => 'HBN', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl("imParcel/update", array("id" => $data["id"]))."\" class=\"tab_link\" title=\"".$data["hbn"]."\">".$data["hbn"]."</a>"',),
		array('name' => 'status', 'header' => 'Status',
			'filter'=>CHtml::dropDownList('ImParcel[status]', $model->status, $this->t(ImParcel::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'delay', 'header' => 'Delay',
			'filter'=>CHtml::textField('ImParcel[delay]', $model->delay, array('placeholder'=>'5, >5, <5')),),
	),
));
