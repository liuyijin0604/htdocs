
<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'links' => array(
        'Manage Consol',
    ),
));
?>

<h1><?=$this->t('Consols');?></h1>


<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'consol-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'no', 'header' => 'No', 'type' => 'raw', 'value' => '$data->status < 0? "<a href=\"".Yii::app()->createURL("consol/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>" : $data->no',),
		array('name' => 'status', 'header' => 'Status', 'value' => '$data->getStatus()', 'filter'=>CHtml::dropDownList('ImcoConsol[status]', $model->status, $this->tarray(ImcoConsol::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'awb', 'header' => 'AWB'),
	array('name' => 'flight', 'header' => 'Flight'),
		array('name' => 'pol', 'header' => 'POL'),
		array('name' => 'pod', 'header' => 'POD'),
		array('name' => 'eta', 'header' => 'ETA'),

		array(
			'class'=>'oButtonColumn',
			'template'=>'{view} {update} {addTrack}',
			'buttons'=>array
			(
				'view' => array(
					'imageUrl'=>false,
					'visible'=> '$data->status > 10',
					'options' => array('class' => 'tab_link grid_view_btn', 'title' => '$data->awb','target' => '_top'),
				),
				'update' => array(
					'imageUrl'=>false,
					'visible'=> '$data->status < 20',
                  //  'url' => 'Yii::app()->createUrl("consol", ["update" => $data->id])',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->awb','target' => '_blank'),
				),
                'addTrack' => array(
                    'imageUrl'=>false,
                    'visible'=> '$data->status < 20',
                    'label' => 'addTrack',
                    'url' => 'Yii::app()->createUrl("consol", ["addtrack" => $data->id])',
                    'options' => array('class' => 'tab_link grid_gallery_btn','target' => '_blank'),
                ),
			),
		),
	),
)); ?>


<?php ob_start(); ?>
<script type="text/javascript">
$(function(){

	$('.search-button').click(function(){
		$('.search-form').toggle();
		return false;
	});
	$('.search-form form').submit(function(){
		$.fn.yiiGridView.update('consol-grid', {
			data: $(this).serialize()
		});
		return false;
	});

	$('#consol-grid').yiiGridView('update');

});
</script>
<?php $this->registerJS(ob_get_clean(),8); ?>