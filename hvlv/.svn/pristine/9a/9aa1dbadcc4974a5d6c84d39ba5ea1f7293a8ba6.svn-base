<div style="right: 20px;position: absolute;">
<?php if ($model->type != array_search('CG Delivery', WmsJob::$types)) { ?>

<a class="jqm_link" href="wmsTask/import/<?=$model->id?>.app"><div class="icon" style="background-position:-16px 0"></div> Import task</a>
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-2"><div style="background-position:-48px -688px" class="icon"></div> Export</a>
<div id="<?=$_GET["tabid"];?>-dropdown-2" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="<?=$this->createUrl('wmsJob/export', array('id' => $model->id, 't' => 'cmb_pak'));?>" target="_blank">Combined Packing List</a></li>
		<li><a href="<?=$this->createUrl('wmsJob/export', array('id' => $model->id, 't' => 'cmb_pik'));?>" target="_blank">Combined Picking List</a></li>
		<li><a href="<?=$this->createUrl('wmsJob/export', array('id' => $model->id, 't' => 'odr_pik'));?>" target="_blank">Task Picking List</a></li>
                <li><a href="<?=$this->createUrl('wmsJob/print', array('id' => $model->id));?>" target="_blank">Print Courier Label</a></li>
	</ul>
</div>
<?php } else { ?>

<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-2"><div style="background-position:-48px -688px" class="icon"></div> Export</a>
<div id="<?=$_GET["tabid"];?>-dropdown-2" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="<?=$this->createUrl('cgoods/printbatched', array('id' => $model->id));?>" target="_blank">Print Batched Orders</a></li>
	</ul>
</div>

<?php } ?>
</div>

<p>
<?php if ($model->type != array_search('CG Delivery', WmsJob::$types)) { ?>
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-176px 0" class="icon"></div> New Task</a>
<?php } else { ?>
&nbsp;
<?php } ?>
<a href="<?=$this->createUrl('containerCartage/batchCreate', array('job_id' => $model->id, 'create_tab' => $_GET["tabid"]))?>" title="Batch New Container Cartage" class="tab_link"><div style="background-position:-16px 0" class="icon"></div> Batch CT Cartage</a>
</p>

<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-left">
	<ul class="dropdown-menu" style="overflow:auto;width:430px;max-width:430px;">
<?php
$pr = '';
$prs = [10 => 'Goods In', 12 => 'QA', 20 => 'Goods Out', 30 => 'Picking', 32 => 'Packing', 40 => 'Stock', 50 => 'Pallet', 90 => 'Other'];

foreach(WmsTask::$types as $i => $t){
	if(in_array($i, [1110, 2110, 2120, 3210, 4020, 3050, 3060, 4030, 5010, 6020, 6030, 6040])) continue;
	if ($model->type == WmsJob::TYPE_PICK_LOAD) {
		if (!in_array($i, [WmsTask::TYPE_PICK_PALLET, WmsTask::TYPE_CONTAINER_LOAD])) {
			continue;
		}
		if ($i == WmsTask::TYPE_PICK_PALLET && !empty($model->pickPltTask)) {
			continue;
		}
	}
	$p = substr($i, 0, 2);
	if($p != $pr && !empty($prs[$p])){
		echo '<li style="clear:both;padding-left:10px"><b>', $prs[$p] ,'</b></li>';
	}
	echo '<li style="float:left;width:140px;"><a href="',$this->createUrl('wmsTask/create', array('id'=>$model->id, 'type'=> $i)),'" class="tab_link" title="New Task">',$t,'</a></li>';
	$pr = $p;
}
?>
	</ul>
</div>
<div style="min-height:400px">
<?php
$dp = new WmsTask('search');
$dp->unsetAttributes();
if ($model->type == array_search('CG Delivery', WmsJob::$types)) $dp->type = array_search('CG Delivery', WmsTask::$types);
$dp->job_id = $model->id;
if(!empty($_GET['WmsTask'])) $dp->attributes = $_GET['WmsTask'];

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET["tabid"].'_prod-task-grid',
	'cssFile' => false,
	'dataProvider'=>$dp->search(),
	'filter'=>$dp,
//	'summaryText' => '',
	'columns'=>array(
		array('name' => 'id', 'value' => '$data->getNo()'),
		array('name' => 'awbn', 'type' => 'raw', 'value' => '$data->getAWBN()'),
		'ref',
		array('header' => 'Delivery Expiry', 'type' => 'raw', 'value' => '$data->getDeliveryExpiry()'),
		array('name' => 'type', 'value' => '$data->getType()', 
			'filter'=>CHtml::dropDownList('WmsTask[type]', $dp->type, $this->t(WmsTask::$types), array('prompt'=>$this->t('All'))),),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('WmsTask[status]', $dp->status, $this->t(WmsTask::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'op_name', 'value' => 'empty($data->op)? "" : $data->op->getName()'),
		'schd_time',
		'due_time',
		'compl_time',
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>array
			(
				'update' => array(
					'imageUrl'=>false,
					'url' => 'Yii::app()->createUrl("wmsTask/update", ["id" => $data->id])',
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->getNo()'),
				),
			),
		),
	),
));
?>
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('#fetchTracking', panel).on('success', function(e, r){
		$('#<?=$_GET["tabid"];?>_prod-task-grid', panel).yiiGridView('update');
	});
});
</script>