<?php
	$filtersForm=new FiltersForm;
	if (isset($_GET['FiltersForm'])) {
		$filtersForm->filters=$_GET['FiltersForm'];

	}
	$warehouseProcessService = new WarehouseProcessService();
	$provide = $warehouseProcessService->getPreparationOldGatepassWithoutResorting(0,"");
	$size = count($provide);

?>

<div style="width:100%;" id="wid_imghwr">
	<h3><a style="display: block;text-align:right;" href="<?= $this->createUrl('warehouseProcess/processPage').'?tab=held_shipment_resorting&&dptId=0' ?>" class="tab_link" title="History Gatepass">+ More</a></h3>
	<h3><?=$size." Gatepasses"?></h3>
   <?php 

   // $this->widget('zii.widgets.grid.CGridView', [
   //  'id'=>'wid_imghwr',
   //  'htmlOptions'=>['style'=>'width: 100%'],
   //  'cssFile' => false,
   //  'dataProvider'=>$dataprovider,
   //  'columns'=>[
   //      ['name'=>'no','header'=>'Gatepass No.',"value"=>'"<a target=\"_blank\" href=\"".Yii::app()->createUrl("gatepass/print",["id"=>$data["no"]])."\">".$data["no"]."</a>"','type'=>'raw'],
   //      ['name'=>'hbn'],
   //      ['name'=>'ref'],
   //      ['name'=>'location','type'=>'raw'],
   //      ['name'=>'scanout'],
   //      ['name'=>'status'],
   //      ['name'=>'packages'],
   //      ['name'=>'weight'],
   //      ['name'=>'driver'],
   //      ['name'=>'gatepass_time'],
   //      ['name'=>'isResort'],
   //  ],
   // ]); 

   ?>
</div>