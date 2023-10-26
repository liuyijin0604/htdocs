<div style="right: 20px;position: absolute;">
<a class="tab_link" href="<?=$this->createUrl('wmsJob/create');?>" title="New Job"><div class="icon" style="background-position:-16px 0"></div> New Job</a> &nbsp; <a class="tab_link" href="<?=$this->createUrl('wmsJob/wkinv');?>" title="Weekly Invoice"><div class="icon" style="background-position:-64px -256px"></div> Weekly Invoice</a>
</div>

<h1><?=$this->t('WMS Jobs');?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'wms-job-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl("wmsJob/update", ["id" => $data->id])."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"'),
		array('name' => 'type', 'value' => '$data->getType()', 
			'filter'=>CHtml::dropDownList('WmsJob[type]', $model->type, $this->t(WmsJob::$types), array('prompt'=>$this->t('All'))),),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('WmsJob[status]', $model->status, $this->t(WmsJob::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'cust_name', 'value' => 'empty($data->customer)? "" : $data->customer->shortName(4)'),
		'po',
		'ref',
		array('name' => 'dpt_id', 'value' => '$data->getBranch()', 'filter'=>CHtml::dropDownList('WmsJob[dpt_id]', $model->dpt_id, Org::dptList3PL(), ['prompt'=>$this->t('All'),'class' => 'form-control']),),
		array('name' => 'sales_name', 'value' => 'empty($data->sales)? "" : $data->sales->getName()'),
		array('name' => 'pm_name', 'value' => 'empty($data->pm)? "" : $data->pm->getName()'),
		'created',
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view}{update}',
			'buttons'=>array
			(
				'view' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->no'),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});
	$('.search-form form', panel).submit(function(){
		$.fn.yiiGridView.update('wms-job-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#wms-job-grid', panel).yiiGridView('update');
	});
});
</script>
