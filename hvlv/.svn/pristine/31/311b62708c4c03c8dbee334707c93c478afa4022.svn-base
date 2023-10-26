<style>
@-webkit-keyframes blinker {
	50% {
		opacity: 0;
	}
}

.badge {
	animation: blinker 2s linear infinite;
}

.badge a {
	text-decoration: none;
	color: white;
}
</style>
<div class="row" style="margin: 20px 0">
	<div class="col-xs-12" style="padding: 0">
		<a style="width: 10em" type="button" role="menuitem" tabindex="-1" target="_blank" href="#" data-baseurl="<?=$this->createUrl('report/export', array('type' => 'inbound-delivery-advice'))?>" class="export_search btn btn-default" id="inbound-delivery-advice">Export</a>
	</div>
</div>
<?php
$task = new WmsTask();
$task->unsetAttributes();
$ec = new CDbCriteria;
$ec->addCondition('t.type IN (1010,1020,1030) AND t.status IN (10,20)');

if (isset($_GET['WmsTask'])) {
	$task->attributes = $_GET['WmsTask'];
}

// get the orgids and by org_ids
if (Yii::app()->user->grp != 0) {
	$task->job_ids = Pcaw::jobIds(User::getOrgIds());
}

$ec->addCondition('not t.bwf & 16 > 0');
if (Yii::app()->session['org_id'] != Yii::app()->user->org) {
	$ec->with = ['job'];
	$ec->addCondition('job.org_id = ' . Yii::app()->session['org_id']);
}
$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'fixedHeader' => true,
	'id' => 'inbound-delivery-advice-grid',
	'filter' => $task,
	'type' => 'striped bordered',
	'headerOffset' => 40,
	'responsiveTable' => true,
	'dataProvider' => $task->search(true, 30, $ec),
	'template' => "{items}\n{pager}",
	'columns' => array(
		array('name' => 'id', 'header' => 'PCAE Order#', 'value' => '$data->getNo()'),
		array('name' => 'ref', 'header' => 'Purchase Order#'),
		array('name' => 'create_date', 'header' => 'Inbound Order Date', 'value' => '$data->getCreateDate()'),
		array('header' => 'Item QTY', 'value' => '$data->countUq()'),
		array(
			'class' => 'application.extensions.booster.TbButtonColumn',
			'template' => '{export}',
			'buttons' => array(
				'export' => array(
					'visible' => 'true',
					'icon' => 'glyphicon glyphicon-download',
					'url' => 'Yii::app()->createUrl("pcaw/report/exportSingle", ["type" => "inbound-delivery-advice", "id" => $data->id])',
					'options' => array('target' => '_blank', 'label' => 'Export', 'title' => 'Export'),
				),
			),
		),
	),
));
?>