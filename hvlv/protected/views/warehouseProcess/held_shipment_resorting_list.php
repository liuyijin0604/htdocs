<h2>旧货</h2>
<div id = "preparation_content">
	<?php echo $this->renderPartial('loading_list', array('url'=>"warehouseProcess/getHeldShipmentGatepassList","dptId"=>$dptId)); ?>
</div>
</br>
</br>
</br>
</br>
<h2>Without Resorting History</h2>
<div id = "preparation_content">
	<?php echo $this->renderPartial('_old_gatepass_without_resort', array('dataProvider'=>$dataProvider)); ?>
</div>
