<h1><?=$this->t('Customer Imports Shipments');?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
));
?>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'im-parcel-grid-customer',
	'cssFile' => false,
	'dataProvider'=>$model->search(true,30,false,false),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',),
		'ref',
              'agent_id',
            array('name'=>'awb_name','header'=>'awb','type'=>'raw','value'=>'empty($data->consol)?"":$data->consol->awb',),
//        array('header' => 'Awb', 'type' => 'raw', 'value' => 'empty($data->consol)? "" : $data->consol->awb',),
        'pkg',
		array('name' => 'consol_no', 'type'=>'raw', 'value' => 'empty($data->consol_id)? "" : "<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" => $data->consol_id))."\" class=\"tab_link\" title=\"".$data->consol->no."\">".$data->consol->no."</a>"',),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('ImParcel[status]', $model->status, $this->t(ImParcel::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
		'postcode',
		'weight',
                array('header' => 'Clear Log', 'type' => 'raw',   'value'=>function($data){return CHtml::tag('div', array('title'=>@$data->mdata['clear_log'],),$data->getClearLog());},),
		array('header' => 'Tranship', 'value' => '$data->getTranships()'),
		array('name' => 'created', 'value' => 'substr($data->created,0,10)'),
                array('header'=>'ChargeCode','value'=>'$data->getChargecode()'),
//		array('name' => 'bwf', 'header' => 'Warnings', 'type' => 'raw', 'value' => '$data->getWarnings()','filter'=>CHtml::dropDownList('ImParcel[bwf]', $model->bwf, $this->t(ImParcel::$bwfs), array('prompt'=>$this->t('All'))),),
		array('header'=>'value','value'=>'$data->dvalue'),
                array('header'=>'extra Info','type'=>'raw','value'=>'$data->getWarnings()','filter'=>CHtml::dropDownList('ImParcel[bwf]',$model->bwf,$this->t(ImParcel::$bwfs),array('prompt'=>'All'))),
                array(
			'class'=>'oButtonColumn',
			'template'=>'{Log}{update}',
			'buttons'=>array
			(
				'Log' => array(
					'imageUrl'=>false,
					'url'=>'Yii::app()->createUrl("imParcel/clearLog", ["id" => $data->id])',
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->hbn'),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	var resetFilters = function(){
		$('.search-form form', panel).trigger('reset');
		$('#im-parcel-grid', panel).yiiGridView('update', {data: 'CoParcel=reset'});
	};

	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});

	$('.search-form form', panel).on('submit', function(){
		$('#im-parcel-grid', panel).yiiGridView('update', {data: $('.filters input, .filters select', panel).serialize() + '&' + $(this).serialize()});
		return false;
	}).find('.reset_btn').click(resetFilters);

});
</script>
