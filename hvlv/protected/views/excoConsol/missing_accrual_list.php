
<h1><?=$this->t('Export Consols Accrual Missing');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'exco-consol-no-accrual-grid',
	'cssFile' => false,
	'dataProvider'=>$modeldp,
	'columns'=>array(
		array('name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("excoConsol/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"',),
        array('name' => 'awb', 'value' => '$data->allAwb()'),
        array('name' => 'status', 'value' => '$data->getStatus()',
            'filter'=>CHtml::dropDownList('ExcoConsol[status]', $model->status, $this->t(ExcoConsol::$states), array('prompt'=>$this->t('All'))),),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	tab.bind('onOpen', function(){
		$('#exco-consol-no-accrual-grid', panel).yiiGridView('update');
	});
});
</script>
