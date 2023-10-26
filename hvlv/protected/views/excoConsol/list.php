<h1><?=$this->t('Export Consols');?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<?php
$ec = false;
if(Yii::app()->user->grp == 80){
	$ec = new CDbCriteria();
	switch(Yii::app()->user->id){
		case 483: //josiah
			$ec->addInCondition("poc", ['CNXMN', 'CNXM2', 'CNTAO', 'CNTA2']);
		break;
		case 519: //jack
			$ec->addInCondition("poc", ['CNJMN', 'CNJM2', 'CNXIA']);
		break;
	}
}

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'exco-consol-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 30, $ec),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'no','type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("excoConsol/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"'),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('ExcoConsol[status]', $model->status, $this->t(ExcoConsol::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'awb', 'type' => 'raw', 'value' => '$data->AwbTracking()', ),
		array('header' => $this->t('Shipments'), 'value' => '$data->totShipments()'),
		array('header' => $this->t('Weight'), 'value' => '$data->totWeight()'),
		array('name' => 'pol', 'filter'=>CHtml::dropDownList('ExcoConsol[pol]', $model->pol, $this->t(AppHelper::setting2List('pods')), array('prompt'=>$this->t('All'))),),
		array('name' => 'poc', 'value' => '$data->getPoc()', 'filter'=>CHtml::dropDownList('ExcoConsol[poc]', $model->poc, $this->t(ExChannel::getPocs(true)), array('prompt'=>$this->t('All'))),),
		'etd',
		array('header' => $this->t('Occupied'), 'value' => '$data->isOccupied()? "Yes" : "No"', 'filter'=>CHtml::dropDownList('ExcoConsol[ocpd]', $model->ocpd, ['Y' => 'Yes'], array('prompt'=>$this->t('All'))),),
		
		/*'created',
		
		'airline',
		'flight',
		'eta',
		'meta',
		*/
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',
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
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});
	$('.search-form form', panel).submit(function(){
		$.fn.yiiGridView.update('exco-consol-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#exco-consol-grid', panel).yiiGridView('update');
	});
});
</script>
