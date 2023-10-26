<div style="position: absolute; left: 200px;"><a class="jqm_link" href="<?=$this->createUrl('exprod/create');?>"><div class="icon" style="background-position:-16px 0"></div> New Product</a> &nbsp; <a class="jqm_link" href="<?=$this->createUrl('exprod/impexp');?>" title="Import Export"><div class="icon" style="background-position:-288px -688px"></div> Import/Export</a><!--a class="tab_link" href="<?=$this->createUrl('exprod/tagUpdate');?>" title="Tag Update"><div class="icon" style="background-position:-288px -688px"></div> Batch Tag Update</a-->
<?php if(ExParcel::model()->count('status < 100 AND bwf & 8 > 0') > 0):?>
 &nbsp; <!--a class="tab_link" href="<?=$this->createUrl('exprod/unmatched');?>" title="Unmatched Products"><div class="icon" style="background-position:-128px -304px"></div> Unmatched Products</a-->
<?php endif; ?>
</div>
<h1><?=$this->t('Manage Products');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'ex-prodb-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'type', 'value' => '$data->getType()', 
			'filter'=>CHtml::dropDownList('ExProdb[type]', $model->type, $this->t(ExProdb::$types), array('prompt'=>$this->t('All'))),),
		'name',
		'name_zh',
		'brand',
		'model',
		'sku',
		'code',
		'price',
		'weight',
		'hs',
		/*
		'unit',
		'tag',
		'note',
		*/
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>array
			(
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '"Product ".$data->name'),
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
		$.fn.yiiGridView.update('ex-prodb-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#ex-prodb-grid', panel).yiiGridView('update');
	});
});
</script>
