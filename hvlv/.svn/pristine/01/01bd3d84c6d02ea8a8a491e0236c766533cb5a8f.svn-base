<div style="right: 40px;position: absolute;">
<a class="tab_link" href="<?=$this->createUrl('szpChannel/create');?>" title="New Channel"><div class="icon" style="background-position:-16px 0"></div> New Channel</a> &nbsp; <!-- <a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('szpChannel/tester');?>" title="Tester"><div class="icon" style="background-position:-160px -624px"></div> Tester</a> -->
<?php if(Yii::app()->user->grp == 0):?>
<!--  &nbsp; <a class="jqm_link" href="<?=$this->createUrl('szpChannel/globalRules');?>" title="Global Rules"><div class="icon" style="background-position:-240px -176px"></div> Global Rules</a> -->
<?php endif; ?>
</div>
<h1><?=$this->t('Channels');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'szp-channel-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'code',
		'name',
		'pod',
		['name' => 'status', 'value' => '$data->getStatus()', 'filter' => CHtml::dropDownList('SzpChannel[status]', $model->status, $this->t($model::$states), ['prompt'=>$this->t('All')]),],
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>array(
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '"Update ".$data->name'),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	$('.search-button').click(function(){
		$('.search-form').toggle();
		return false;
	});
	$('.search-form form').submit(function(){
		$.fn.yiiGridView.update('szp-channel-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#szp-channel-grid').yiiGridView('update');
	});
});
</script>
