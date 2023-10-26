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
		<a style="width: 10em" type="button" role="menuitem" tabindex="-1" target="_blank" href="#" data-baseurl="<?=$this->createUrl('report/export', array('type' => 'goods-return'))?>" class="export_search btn btn-default" id="goods-return">Export</a>
	</div>
</div>
<?php
$task = new WmsTask();
$task->unsetAttributes();
$ec = new CDbCriteria;
$ec->addCondition('t.type IN (3020,3030)');
$ec->addCondition('(t.bwf & 128) > 0');

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
	'id' => 'goods-return-grid',
	'filter' => $task,
	'type' => 'striped bordered',
	'headerOffset' => 40,
	'responsiveTable' => true,
	'dataProvider' => $task->search(true, 30, $ec),
	'template' => "{items}\n{pager}",
	'columns' => array(
		array('header' => 'Return Date', 'value' => '$data->getReturnDate()'),
		array('name' => 'ref', 'header' => 'Order#'),
		array('name' => 'cnee_name', 'header' => 'Customer Name', 'value' => '$data->getCneeName()'),
		array('name' => 'cnee_address', 'header' => 'Address', 'value' => '$data->getCneeAddress()'),
		array('name' => 'cnee_state', 'header' => 'State', 'value' => '$data->getCneeState()'),
		array('name' => 'cnee_postcode', 'header' => 'PostCode', 'value' => '$data->getCneePostcode()'),
	),
));
?>