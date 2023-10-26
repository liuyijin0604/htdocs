<div style="position: absolute; left: 120px;"><a class="jqm_link" href="<?=$this->createUrl('storage/create');?>"><div class="icon" style="background-position:-16px 0"></div> New Storage</a> &nbsp; <a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div class="icon" style="background-position:-48px -688px"></div> Storage Report</a> &nbsp; <a class="ajax-link" href="<?=$this->createUrl('storage/ajaxImportSortingLabels')?>" target="_blank"><div class="icon" style="background-position:-128px -576px"></div> Print Labels</a>
<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<?php
			foreach(Org::dptList() as $i => $w){
				echo '<li><a href="', $this->createUrl('storage/report', ['id' => $i]), '" target="_blank">',$w,'</a></li>';
			}
		?>
	</ul>
</div>
</div>
<h1><?=$this->t('Storages');?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'storage-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'name',
		'code',
		array('name' => 'type', 'value' => '$data->getType()', 
			'filter'=>CHtml::dropDownList('Storage[type]', $model->type, $this->t(Storage::$types), array('prompt'=>$this->t('All'))),),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('Storage[status]', $model->status, $this->t(Storage::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'cap', 'value' => '$data->cap."%"'),
		'cap_kg',
		'cap_cbm',
		'cap_item',
		array('name' => 'wid', 'value' => 'empty($data->wid)? "" : $data->warehouse->name', 
			'filter'=>CHtml::dropDownList('Storage[wid]', $model->wid, Org::dptList(), array('prompt'=>$this->t('All'))),),
		array('name' => 'pid', 'value' => 'empty($data->pid)? "" : $data->parent->name', ),
		/*
		'code',
		'cx',
		'cy',
		'cz',
		'bwf',
		'notes',
		'meta',
		*/
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view} {update}',
			'buttons'=>array
			(
				'view' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => ''),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});
	$('.search-form form', panel).submit(function(){
		$.fn.yiiGridView.update('storage-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#storage-grid', panel).yiiGridView('update');
	});
});
</script>
