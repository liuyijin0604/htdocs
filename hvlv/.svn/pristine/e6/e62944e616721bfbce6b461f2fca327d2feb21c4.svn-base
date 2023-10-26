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
				'homeLink'=>CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
				'links' => array('Task List',),
));
?>
<h2>Task List</h2>
<div class="row">

	<?php if ((empty($invoice_due) && $limit >= 0) || $release) { ?>
	<div class="col-xs-4">
		<div class="dropdown" style="display: inline-block; margin-right: 1em">
			<button class="btn btn-default dropdown-toggle" style="width: 10em" type="button" id="menu1" data-toggle="dropdown">Create Tasks
			<span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
				<li role="presentation"><b>&nbsp In</b></li>
				<li role="presentation"><a class="ajax-link" role="menuitem" tabindex="-1" href="<?=$this->createUrl('task/createTask?type=1010&id='.$model->id)?>">Stock In</a></li>
				<!--<li role="presentation"><a class="ajax-link" role="menuitem" tabindex="-1" href="<?=$this->createUrl('task/createTask?type=1030&id='.$model->id)?>">Container Unload</a></li>-->
				<li role="presentation"><b>&nbsp Out</b></li>
				<!-- <li role="presentation"><a class="ajax-link" role="menuitem" tabindex="-1" href="<?=$this->createUrl('task/createTask?type=3010&id='.$model->id)?>">Pick Pallet</a></li> 
				<li role="presentation"><a class="ajax-link" role="menuitem" tabindex="-1" href="<?=$this->createUrl('task/createTask?type=3020&id='.$model->id)?>">Pick Carton</a></li> -->
				<li role="presentation"><a class="ajax-link" role="menuitem" tabindex="-1" href="<?=$this->createUrl('task/createTask?type=3030&isSpecial=0&id='.$model->id)?>">Stock Out</a></li>
				<li role="presentation"><a class="ajax-link" role="menuitem" tabindex="-1" href="<?=$this->createUrl('task/createTask?type=3030&isSpecial=1&id='.$model->id)?>">Stock Out (Special)</a></li>
				<!--<li role="presentation"><a class="ajax-link" role="menuitem" tabindex="-1" href="<?=$this->createUrl('task/createTask?type=3040&id='.$model->id)?>">From Office</a></li>-->
			</ul>
		</div>
		<div class="dropdown" style="display: inline-block; margin-right: 1em">
			<button class="btn btn-default dropdown-toggle" style="width: 10em" type="button" id="menu1" data-toggle="dropdown">Import Tasks
			<span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
				<li role="presentation"><b>&nbsp In</b></li>
				<li role="presentation"><a role="menuitem" tabindex="-1" class="tracking-modal-link" href="<?=$this->createUrl('task/Import?type=1010&id='.$model->id)?>">Import Stock In</a></li>
				<!--<li role="presentation"><a role="menuitem" tabindex="-1" class="tracking-modal-link" href="<?=$this->createUrl('task/Import?type=1030&id='.$model->id)?>">Import Container Unload</a></li>-->
				<li role="presentation"><b>&nbsp Out</b></li>
				<!-- <li role="presentation"><a role="menuitem" tabindex="-1"   class="tracking-modal-link" href="<?=$this->createUrl('task/Import?type=3010&id='.$model->id)?>">Import Pick Pallet</a></li> 
				<li role="presentation"><a role="menuitem" tabindex="-1"   class="tracking-modal-link" href="<?=$this->createUrl('task/Import?type=3030&id='.$model->id)?>">Import Pick Carton</a></li> -->
				<li role="presentation"><a role="menuitem" tabindex="-1"   class="tracking-modal-link" href="<?=$this->createUrl('task/Import?type=3030&id='.$model->id)?>">Import Stock Out</a></li>
				<!--<li role="presentation"><b>Split Delivery</b></li>
				<li role="presentation"><a role="menuitem" tabindex="-1"   class="tracking-modal-link" href="<?=$this->createUrl('task/Import?type='.WmsTask::TYPE_Split_Delivery.'&id='.$model->id)?>">Split Delivery</a></li>-->
			</ul>
		</div>
	</div>
	<?php } ?>

	<?php if (!empty($invoice_due)) { ?>
	<div class="col-xs-4">
		<span style="color:red">You have invoice</span> <b><?=$invoice_due?></b> <span style="color:red">exceed current credit term,<br/>please arrange payment</span>
	</div>
	<?php } else if ($limit < 0) { ?>
	<div class="col-xs-4">
		<span style="color:red">You have exceed current credit limit,<br/>please arrange payment</span>
	</div>
	<?php } ?>

	<div class="col-xs-2 pull-right">
			<div class="dropdown">
				<button class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">Export
				<span class="caret"></span></button>
				<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
					<li role="presentation"><a role="menuitem" tabindex="-1" target="_blank" href="<?=$this->createUrl('task/export', array('type' => 'courier'))?>">Couriers</a></li>
					<li role="presentation"><a role="menuitem" tabindex="-1" target="_blank" href="#" data-baseurl="<?=$this->createUrl('task/export', array('type' => 'search'))?>" class="export_search">Tasks</a></li>
				</ul>
			</div>
	</div>
	<!-- <?php if (Yii::app()->user->org === '783') { ?>
	<div class="col-xs-2">
		<form action="wmsProd/order/id/<?=Yii::app()->user->org?>">
			<button class="btn btn-default" type="submit">Sync Tasks</button>
		</form>
	</div>
	<?php } ?> -->
</div>

<br>
<?php
$ec = new CDbCriteria;
$ec->addCondition('not t.bwf & 16 > 0');
if (Yii::app()->session['org_id'] != Yii::app()->user->org) {
	$ec->with = ['job'];
	$ec->addCondition('job.org_id = ' . Yii::app()->session['org_id']);
}
// $ec->order = 'CASE t.bwf WHEN 128 THEN 1 ELSE 2 END';
$this->widget('application.extensions.booster.TbExtendedGridView',array(
		'fixedHeader'=>true,
		'id'=>'task_grid_view',
		'filter'=>$task,
		'type'=>'striped bordered',
		'headerOffset'=>40,
		'responsiveTable'=>true,
		'dataProvider'=>$task->search(true, 30, $ec),
		'template' => "{summary}\n{items}\n{pager}",
		'columns'=>array(
				array('name' => 'id', 'type' => 'raw', 'value' => '$data->getNoPCAW()'),
				array('name' => 'id', 'type' => 'raw', 'value' => '$data->getNo()'),
				array('name' => 'source', 'value' => '$data->getSource()', 'filter' => CHtml::dropDownList('WmsTask[source]', $task->source, $this->t(WmsTask::$sources), array('prompt' => $this->t('All'), 'class' => 'form-control'))),
				'ref',
				array('name' => 'cust_name', 'value' => '$data->job->customer->name', 'visible' => sizeof(User::getOrgIds()) > 1 ? true : false),
				// array('name' => 'dpt_id', 'value' => '$data->getBranch()', 'filter' => CHtml::dropDownList('WmsTask[dpt_id]', $task->dpt_id, Org::dptList3PL(), ['prompt' => $this->t('All'), 'class' => 'form-control']),),
				array('name' => 'type', 'value' => '$data->getType()', 'filter'=>CHtml::dropDownList('WmsTask[type]', $task->type, $this->t(WmsTask::$types_client), array('prompt'=>$this->t('All'),'class'=>'form-control')),),
				array('name' => 'status', 'value' => '$data->getClientStatus()', 'filter' => CHtml::dropDownList('WmsTask[status]', $task->status, $this->t(WmsTask::$states_client), array('prompt' => $this->t('All'), 'class' => 'form-control')), 'visible' => !in_array(Yii::app()->user->org, Org::$easyships)),
				array('name' => 'status', 'value' => '$data->getEasyshipStatus()', 'filter' => CHtml::dropDownList('WmsTask[status]', $task->status, $this->t(WmsTask::$states_easyship), array('prompt' => $this->t('All'), 'class' => 'form-control')), 'visible' => in_array(Yii::app()->user->org, Org::$easyships)),
				'schd_time',
				array('name' => 'skus', 'header' => 'SKU', 'value' => '$data->countSKU()'),
				array(
						'class'=>'application.extensions.booster.TbButtonColumn',
						'template'=>'{Update} &nbsp',
						'buttons'=>array(
								'Update' => array(
										'visible'=>'true',
										'icon' => 'glyphicon glyphicon-edit',
										'url' => 'Yii::app()->createUrl("pcaw/task/updateTask",array("id"=>$data->id))',
										'options' => array('class' => 'ajax-link', 'label'=>$this->t('Tracking'), 'title' => 'Tracking'),
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