<h1>Gatepass Sign Summary List</h1>
<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
?>
<?php echo $this->renderPartial('loading_list', array('url'=>"warehouseProcess/getGatePassSignList")); ?>