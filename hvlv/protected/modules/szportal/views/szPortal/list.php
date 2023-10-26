<h1><?=$this->t('SZ Portal Export Consols');?></h1>

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
	'id'=>'szPortal-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 30, $ec),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'no','type' => 'raw'),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('ImcoConsol[status]', $model->status, $this->t(ImcoConsol::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'awb', 'type' => 'raw', 'value' => '$data->AwbTracking(true)', ),
		array('header' => $this->t('Shipments'), 'value' => '$data->totShipments()'),
			array('header' => $this->t('pkg'), 'value' => '$data->totPacks()'),
			array('header' => $this->t('pkgtck'), 'value' => '$data->totPacksTCK()'),
			array('header' => $this->t('CBM'), 'value' => '$data->totImCBM()'),
			array('header' => $this->t('CBMTCK'), 'value' => '$data->totImCBMTCK()'),
			array('header' => $this->t('Weight'), 'value' => '$data->totWeight()'),
			array('header' => $this->t('Wtck'), 'value' => '$data->totWtck()'),
			array('name' => 'pol', 'filter'=>CHtml::dropDownList('ImcoConsol[pol]', $model->pol, $this->t(AppHelper::setting2List('pols')), array('prompt'=>$this->t('All'))),),
			array('name' => 'pod', 'filter'=>CHtml::dropDownList('ImcoConsol[pod]', $model->pod, $this->t(AppHelper::setting2List('pods')), array('prompt'=>$this->t('All'))),),
			array('name' => 'poc', 'value' => '$data->getPoc()', 'filter'=>CHtml::dropDownList('ImcoConsol[poc]', $model->poc, $this->t(SzpChannel::getPocs(true)), array('prompt'=>$this->t('All'))),),
			'created',
		// array('header' => $this->t('Occupied'), 'value' => '$data->isOccupied()? "Yes" : "No"', 'filter'=>CHtml::dropDownList('ImcoConsol[ocpd]', $model->ocpd, ['Y' => 'Yes'], array('prompt'=>$this->t('All'))),),
		
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
	$('.search-button').click(function(){
		$('.search-form').toggle();
		return false;
	});
	$('.search-form form').submit(function(){
		$.fn.yiiGridView.update('szPortal-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	// tab.bind('onOpen', function(){
	// 	$('#szPortal-grid', panel).yiiGridView('update');
	// });
});
</script>
