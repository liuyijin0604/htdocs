
<h1><?=$this->t('Import Consols Accrual Missing');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'imco-consol-no-accrual-grid',
	'cssFile' => false,
	'dataProvider'=>$modeldp,
	'columns'=>array(
		array('name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"',),
		array('name' => 'awb'),
        'created',
        array('header' => 'Customer','value' => '$data->getOwnerName()'),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('ImcoConsol[status]', $model->status, $this->t($model::$states), array('prompt'=>$this->t('All'))),),
		array('name' => 'dpt_id', 'value' => '$data->depot->name', 
			'filter'=>CHtml::dropDownList('ImcoConsol[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), array('prompt'=>$this->t('All'))),),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	tab.bind('onOpen', function(){
		$('#imco-consol-no-accrual-grid', panel).yiiGridView('update');
	});
});
</script>
