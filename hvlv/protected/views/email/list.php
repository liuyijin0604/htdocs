<?php
$this->breadcrumbs=array(
	'Emails'=>array('index'),
	$this->t('List'),
);
?>

<h1><?=$this->t('Emails');?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'email-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('name'=>'type','value'=>'$data->getType()',
			'filter'=>CHtml::dropDownList('Email[type]', $model->type, $this->tarray(Email::$types), array('prompt'=>$this->t('All'))),),
		'subject',
		array('name'=>'from','value'=>'$data->by->getName()'),
		'sent',
		'to',
		/*'subject',
		'body',
		'attachments',
		*/
		array(
			'class'=>'CButtonColumn',
			'template'=>'{view}',
			'buttons'=>array
			(
				'view' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'jqm_link grid_view_btn', 'label'=>$this->t('View')),
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
		$.fn.yiiGridView.update('email-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#email-grid', tab.data('panel')).yiiGridView('update');
	});
});
</script>

