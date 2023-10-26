

<h1><?=$this->t('Cost Validation');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'imco-consol-cost-validation-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true,30,false,false,true),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"',),
		array('name' => 'awb'),
        array('header' => 'Customer','value' => '$data->getOwnerName()'),
        array('header' => 'Courier','value' => '$data->getCourierName()'),
        array('header' => 'Cleared Num', 'value' => '$data->getClearedNum()'),
        array('header' => 'Pushed Num', 'value' => '$data->getPushedNum()'),
        array('header' => 'Pending Num', 'value' => '$data->getPendingNum()'),
        array('header' => 'Our Cost', 'value' => '$data->getOurCost()'),
        array('header' => 'Real Cost', 'value' => '$data->getRealCost()'),
        //array('header' => 'Alert', 'value' => ''),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{details}{update}',
			'buttons'=>array
			(
				'details' => array(
					'imageUrl'=>false,
                    'visible'=>'true',
					'options' => array('class' => 'jqm_link grid_view_btn','label' => 'details','title' => '$data->no'),
                    'url' => 'Yii::app()->createUrl("imcoConsol/costDetail", ["fid" => $data->id])'
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
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	tab.bind('onOpen', function(){
		$('#imco-consol-cost-validation-grid', panel).yiiGridView('update');
	});
});
</script>
