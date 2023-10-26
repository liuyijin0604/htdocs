<div style="right: 40px;position: absolute;">
<a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('faq/create');?>" title="<?=$this->t('New FAQ');?>"><div class="icon" style="background-position:-16px 0"></div> <?=$this->t('New FAQ');?></a> &nbsp; 
<a class="tab_link" href="<?=$this->createUrl('faq/sort');?>" title="<?=$this->t('Sort Items');?>"><div class="icon" style="background-position:-272px -720px"></div> <?=$this->t('Sort Items');?></a>
</div>

<h1><?=$this->t('FAQs');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'faq-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'cid', 'value' => '$data->getCate()', 
			'filter'=>$model->cateList(),),
			// CHtml::dropDownList('Faq[cid]', $model->cid, $this->t(), array('prompt'=>$this->t('All'))),),
		'title',
		array('name' => 'cont', 'value' => 'mb_substr(strip_tags($data->cont),0,100)', ),
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
					'options' => array('class' => 'jqm_link grid_edit_btn', 'data-win-class' => 'L', 'label'=>$this->t('Update'), 'title' => ''),
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
		$.fn.yiiGridView.update('faq-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#faq-grid', panel).yiiGridView('update');
	});
});
</script>
