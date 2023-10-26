<style type="text/css">
label.left {
	float: left;
	min-width: 70px;
}
</style>

	<div>
		<a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('topCourierService/viewVehicle');?>" title="New Vehicle" style="float:right"><div class="icon" style="background-position:-16px 0"></div>New Vehicle</a>  
	</div>


  
  <h1>Cargo Process Vehicle</h1>

	<?php
	$objCargoProcessVehicle = new CargoProcessVehicle('search');

	if(isset($_GET['CargoProcessVehicle'])){
		$objCargoProcessVehicle->setAttributes($_GET['CargoProcessVehicle']);
	}

	$this->widget('zii.widgets.grid.CGridView', [
		'id'=>'cargo_process_vehicle_list',
		'cssFile' => false,
		'dataProvider'=>$objCargoProcessVehicle->search(),
		'filter'=>$objCargoProcessVehicle,
		'columns'=>[
            ['header' => 'Plate Nnmber', 'name' => 'plate_number'],
            ['header' => 'Type', 'name' => 'type', 'value' => 'CargoProcessVehicle::dicEnum2Type[$data->type]', 'filter'=>CHtml::dropDownList('CargoProcessVehicle[type]', $objCargoProcessVehicle->type, $this->t([''=>'All']+CargoProcessVehicle::dicEnum2Type)),],
			['header' => 'Length', 'name' => 'length'],
            ['header' => 'Width', 'name' => 'top_width'],
            ['header' => 'Height', 'name' => 'height'],
            ['header' => 'CBM', 'name' => 'min_cbm'],
			['header' => 'Status', 'name' => 'status', 'value' => 'CargoProcessVehicle::dicEnum2Status[$data->status]', 'filter'=>CHtml::dropDownList('CargoProcessVehicle[status]', $objCargoProcessVehicle->status, $this->t([''=>'All']+CargoProcessVehicle::dicEnum2Status)),],
			
			[
				'class' => 'oButtonColumn',
				'template' => '{View}',
				'buttons' => [
					'View' => [
						'imageUrl' => false,
						'visible' => 'true',
						'url' => 'Yii::app()->createUrl("/topCourierService/viewVehicle", ["id" => $data->id])',
						'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => $this->t('View'), 'title' => '$data->id'),
					],
				],
			],
		],
	]);
	?>




</div>