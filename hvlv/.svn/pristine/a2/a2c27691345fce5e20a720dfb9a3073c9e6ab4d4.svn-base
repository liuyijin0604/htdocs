<?php
echo "<h2>Scan Log</h2>";
$modelClass = get_class($model);
$log = new ShipmentScan();
$log->unsetAttributes();
$log->pid = $model->id;
if(!empty($_GET['ShipmentScan']))
{
	$log->setAttributes($_GET['ShipmentScan']);
}
$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>$_GET["tabid"].'_scan-log-grid',
			'cssFile' => false,
			'summaryText'=>'',
			'dataProvider'=> $log->search(true,50),
			'filter'=> $log,
			'columns'=>array(
				array(
		            'name'=>'user_id',
		            'value'=>'$data->getUser()',
		        ),
				array(
		            'name'=>'type',
		            'value'=>'$data->getType()',
		            'filter'=>CHtml::dropDownList('ShipmentScan[type]', $log->type, $this->t(ShipmentScan::$types), ['prompt'=>$this->t('All')])
		        ),
				array(
		            'name'=>'pno'
		        ),
		        array(
		            'name'=>'scan_time'
		        ),
			),
		));
?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

});
</script>