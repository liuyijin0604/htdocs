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
		<a style="width: 10em" type="button" role="menuitem" tabindex="-1" target="_blank" href="#" data-baseurl="<?=$this->createUrl('report/export', array('type' => 'consumer-backorder'))?>" class="export_search btn btn-default" id="consumer-backorder">Export</a>
	</div>
</div>
<?php
$task = new WmsTask();
$task->unsetAttributes();
$ec = new CDbCriteria;
$ec->addCondition('t.type IN (3020,3030) AND t.status = 10');

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
	'id' => 'consumer-backorder-grid',
	'filter' => $task,
	'type' => 'striped bordered',
	'headerOffset' => 40,
	'responsiveTable' => true,
	'dataProvider' => $task->search(true, 30, $ec),
	'template' => "{items}\n{pager}",
	'columns' => array(
		array('name' => 'id', 'header' => 'PCAE Order#', 'value' => '$data->getNo()'),
		array('name' => 'ref', 'header' => 'Sale Order#'),
		array('name' => 'cnee_name', 'header' => 'Customer Name', 'value' => '$data->getCneeName()'),
		array('name' => 'cnee_company', 'header' => 'Company', 'value' => '$data->getCneeCompany()'),
		array('name' => 'cnee_address', 'header' => 'Address', 'value' => '$data->getCneeAddress()'),
		array('name' => 'cnee_city', 'header' => 'City', 'value' => '$data->getCneeCity()'),
		array('name' => 'cnee_state', 'header' => 'State', 'value' => '$data->getCneeState()'),
		array('name' => 'cnee_country', 'header' => 'Country', 'value' => '$data->getCneeCountry()'),
		array('name' => 'cnee_suburb', 'header' => 'Suburb', 'value' => '$data->getCneeSuburb()'),
		array('name' => 'cnee_postcode', 'header' => 'PostCode', 'value' => '$data->getCneePostcode()'),
		array('name' => 'cnee_email', 'header' => 'Email', 'value' => '$data->getCneeEmail()'),
		array('header' => 'Item QTY', 'value' => '$data->countUq()'),
		array(
			'class' => 'application.extensions.booster.TbButtonColumn',
			'template' => '{export}',
			'buttons' => array(
				'export' => array(
					'visible' => 'true',
					'icon' => 'glyphicon glyphicon-download',
					'url' => 'Yii::app()->createUrl("pcaw/report/exportSingle", ["type" => "consumer-backorder", "id" => $data->id])',
					'options' => array('target' => '_blank', 'label' => 'Export', 'title' => 'Export'),
				),
			),
		),
	),
));
?>