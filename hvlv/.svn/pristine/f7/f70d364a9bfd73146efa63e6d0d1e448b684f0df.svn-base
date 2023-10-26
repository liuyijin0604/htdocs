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

<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink' => CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
		$type . ' List',
	),
));
?>
<h2><?=$type?> List</h2>
<div class="row">
	<div class="col-xs-12">
		<div class="dropdown" style="display: inline-block; margin-right: 1em">
			<button class="btn btn-default dropdown-toggle" style="width: 10em" type="button" id="menu1" data-toggle="dropdown">Create Tasks <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
				<li role="presentation"><a class="ajax-link" role="menuitem" tabindex="-1" href="<?=$this->createUrl('task/createTask?type=3020&id='.$model->id)?>">Pick Carton</a></li>
				<li role="presentation"><a class="ajax-link" role="menuitem" tabindex="-1" href="<?=$this->createUrl('task/createTask?type=3030&id='.$model->id)?>">Pick Unit</a></li>
				<li role="presentation"><a class="ajax-link" role="menuitem" tabindex="-1" href="<?=$this->createUrl('task/createTask?type=3040&id='.$model->id)?>">From Office</a></li>
			</ul>
		</div>
		<div class="dropdown" style="display: inline-block; margin-right: 1em">
			<button class="btn btn-default dropdown-toggle" style="width: 10em" type="button" id="menu1" data-toggle="dropdown">Import Tasks <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
				<li role="presentation"><a role="menuitem" tabindex="-1"   class="tracking-modal-link" href="<?=$this->createUrl('task/Import?type=3030&id='.$model->id)?>">Import Pick Carton</a></li>
				<li role="presentation"><a role="menuitem" tabindex="-1"   class="tracking-modal-link" href="<?=$this->createUrl('task/Import?type=3030&id='.$model->id)?>">Import Pick Unit</a></li>
			</ul>
		</div>
		<div class="dropdown" style="display: inline-block; margin-right: 1em">
			<button class="btn btn-default dropdown-toggle" style="width: 10em" type="button" id="menu1" data-toggle="dropdown">Export <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
				<li role="presentation"><a role="menuitem" tabindex="-1" target="_blank" href="<?=$this->createUrl('task/export', array('type' => 'courier'))?>">Couriers</a></li>
				<li role="presentation"><a role="menuitem" tabindex="-1" target="_blank" href="#" data-baseurl="<?=$this->createUrl('task/export', array('type' => 'search'))?>" class="export_search">Tasks</a></li>
				<?php
				$wmsapi = WmsAPI::model()->find('org_id = :org_id AND type = :type', [':org_id' => Yii::app()->user->org, ':type' => WmsAPI::WMS_API_TYPE_SHOPIFY]);
				if (!empty($wmsapi)) {
				?>
				<li role="presentation"><a role="menuitem" tabindex="-1" target="_blank" href="#" data-baseurl="<?=$this->createUrl('task/export', array('type' => 'shopify'))?>" class="export_search">Shopify Fulfill</a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>

<br>
<?php
$ec->addCondition('not t.bwf & 16 > 0');
if (Yii::app()->session['org_id'] != Yii::app()->user->org) {
	$ec->with = ['job'];
	$ec->addCondition('job.org_id = ' . Yii::app()->session['org_id']);
}
if ($type == 'B2B') {
	$ec->addCondition('JSON_VALUE(t.meta, "$.b2") = "b"');
} else if ($type == 'B2C') {
	$ec->addCondition('JSON_VALUE(t.meta, "$.b2") = "c" OR JSON_VALUE(t.meta, "$.b2") IS NULL');
}
$ec->order = 'CASE t.bwf WHEN 128 THEN 1 ELSE 2 END';
$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'fixedHeader' => true,
	'id' => 'task_grid_view',
	'filter' => $task,
	'type' => 'striped bordered',
	'headerOffset' => 40,
	'responsiveTable' => true,
	'dataProvider' => $task->search(true, 30, $ec),
	'template' => "{summary}\n{items}\n{pager}",
	'columns' => array(
		array('name' => 'source', 'value' => '$data->getSource()', 'filter' => CHtml::dropDownList('WmsTask[source]', $task->source, $this->t(WmsTask::$sources), array('prompt' => $this->t('All'), 'class' => 'form-control'))),
		array('name' => 'create_date', 'header' => 'Date', 'value' => '$data->getCreateDate()'),
		array('name' => 'id', 'header' => 'PCAE No', 'value' => '$data->getNo()'),
		array('name' => 'ref', 'header' => 'Order No'),
		array('name' => 'cnee_name', 'header' => 'Ship Name', 'value' => '$data->getCneeName()'),
		array('name' => 'cnee_full_address', 'header' => 'Ship Address', 'value' => '$data->getCneeFullAddress()'),
		array('name' => 'status', 'header' => 'Order Status', 'type' => 'raw', 'value' => '"<span style=\"color: " . $data->getColor() . "\">" . $data->getClientStatusBIO() . "</span>"', 'filter' => CHtml::dropDownList('WmsTask[status]', $task->status, $this->t(WmsTask::$states_client_bio), array('prompt' => $this->t('All'), 'class' => 'form-control'))),
		array(
			'class' => 'application.extensions.booster.TbButtonColumn',
			'template' => '{Update} &nbsp',
			'buttons' => array(
				'Update' => array(
					'visible' => 'true',
					'icon' => 'glyphicon glyphicon-edit',
					'url' => 'Yii::app()->createUrl("pcaw/task/updateTask",array("id" => $data->id))',
					'options' => array('class' => 'ajax-link', 'label' => $this->t('Tracking'), 'title' => 'Tracking'),
				),
			),
		)
	),
));
?>

<!-- Tracking Modal -->
<div class="modal fade" id="modal-tracking" tabindex="-1" role="dialog" aria-labelledby="modal-tracking-label" aria-hidden="true">
	<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-body">
		</div>
		<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?=$this->t('Close');?></button>
		</div>
	</div>
	</div>
</div>
 <?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('body').off('click', 'a.tracking-modal-link').on('click', 'a.tracking-modal-link', function(e){
		$('#modal-tracking').modal();
		$('#modal-tracking .modal-body').load($(this).attr('href'));
		e.preventDefault();
	});

	$('a.export_search').on('mousedown', function() {
		var q = $('.filters input, .filters select').serialize();
		var href = $(this).data('baseurl') + '&' + q;
		href = href.replace('.app&', '?');
		$(this).attr('href', href);
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>