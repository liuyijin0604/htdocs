<h1><?=$this->t('Missing Accrual 3PL Jobs');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'missing-accrual-3pl-job-list-grid',
	'cssFile' => false,
	'dataProvider'=>$modeldp,
	'columns'=>array(
		array('name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("ediJob/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"',),
        'awb',
        array('name' => 'owner_name', 'value' => 'empty($data->owner)? "" : $data->owner->name'),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('EdiJob[status]', $model->status, $this->t($model::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'dpt_id', 'value' => '$data->depot->name', 
			'filter'=>CHtml::dropDownList('EdiJob[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), array('prompt'=>$this->t('All'))),),
        'created',
        'due',
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	tab.bind('onOpen', function(){
		$('#missing-accrual-3pl-job-list-grid', panel).yiiGridView('update');
	});

});
</script>
