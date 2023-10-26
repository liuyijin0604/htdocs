<?php
$hash = empty($_GET['CoParcel'])? '' : '-'.hash('crc32b', json_encode($_GET['CoParcel']));
$data = Yii::app()->cache->get('wid_copdelay_data'.$hash);
if(empty($data)){
	$q = 'status > 40 AND status < 90';
	$p = [];
	if(!empty($_GET['CoParcel']['hbn'])){
		$q .= ' AND hbn LIKE :hbn';
		$p['hbn'] = '%'.$_GET['CoParcel']['hbn'].'%';
	}
	if(!empty($_GET['CoParcel']['status'])){
		$q .= ' AND status = :st';
		$p['st'] = $_GET['CoParcel']['status'];
	}
	$dq = 2;
	$dqs = 'gt';
	if(!empty($_GET['CoParcel']['delay'])){
		$dq = $_GET['CoParcel']['delay'];
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

	$rs = CoParcel::model()->findAll($q, $p);
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
	Yii::app()->cache->set('wid_copdelay_data'.$hash, $data, 300);
}

$sort = new CSort();
$sort->attributes = array('delay',);
$sort->defaultOrder = array(
	'delay'=>CSort::SORT_DESC,
);

$model = new CoParcel('search');
$model->unsetAttributes();
if(!empty($_GET['CoParcel'])) $model->attributes = $_GET['CoParcel'];

$dp = new CArrayDataProvider($data, array(
	'keyField' => 'id',
	'sort' => $sort,
	'pagination' => array(
		'pageSize' => '15',
		'route' => 'dash/widget',
		'params' => ['name' => 'copdelay', 'CoParcel[hbn]' => empty($_GET['CoParcel']['hbn'])? '' : $_GET['CoParcel']['hbn'], 'CoParcel[status]' => empty($_GET['CoParcel']['status'])? '' : $_GET['CoParcel']['status'], 'CoParcel[delay]' => empty($_GET['CoParcel']['delay'])? '' : $_GET['CoParcel']['delay']],
	),
));

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'dashboard-wid-copdelay-grid',
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
		array('name' => 'hbn', 'header' => 'HBN', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl("CoParcel/update", array("id" => $data["id"]))."\" class=\"tab_link\" title=\"".$data["hbn"]."\">".$data["hbn"]."</a>"',),
		array('name' => 'status', 'header' => 'Status',
			'filter'=>CHtml::dropDownList('CoParcel[status]', $model->status, $this->t(CoParcel::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'delay', 'header' => 'Delay',
			'filter'=>CHtml::textField('CoParcel[delay]', $model->delay, array('placeholder'=>'5, >5, <5')),),
	),
));
