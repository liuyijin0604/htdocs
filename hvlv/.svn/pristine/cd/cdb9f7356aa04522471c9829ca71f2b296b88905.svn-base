<style type="text/css">
	.cargoNew{
		
		color: #DAA569;
        text-align: center;
	}
	.cargoClose{
		color: #2E8B57;
        text-align: center;
	}
	.cargoLeft{
		color: #FF4500;
        text-align: center;
	}
</style>

<?php
$model = new Dash();
if (!empty($_GET['ImParcel'])) $model->attributes = $_GET['ImParcel'];
$filtersForm = new FiltersForm;
if (isset($_GET['FiltersForm'])) {
	$filtersForm->filters = $_GET['FiltersForm'];
}
$monthProvide = $model->getWareHouseReminder();

$filteredDataMonth = $filtersForm->filter($monthProvide);
$monthProvide = new CArrayDataProvider($filteredDataMonth);
$monthProvide->pagination = ['pageSize' => 100,];
?>
<div class="row">
		<?php
		$this->widget('zii.widgets.grid.CGridView', array(
			'id' => 'dashboard-wid-tpls-month-grid',
			'cssFile' => false,
			'dataProvider' => $monthProvide,
			'summaryText' => '',
			'enablePagination' => true,
			'pager' => array(
				'prevPageLabel' => 'Prev.',
				'maxButtonCount' => 5,
			),
			'columns' => array(
				array('header' => 'Total Left', 'type' => 'raw', 'value' => '$data["numTotalLeft"]','htmlOptions' => array('class' => 'cargoLeft')),
				//array('header' => 'Syd Left', 'type' => 'raw', 'value' => '$data["sydLeft"]','htmlOptions' => array('class' => 'cargoLeft')),
				array('header' => 'Syd Today', 'type' => 'raw', 'value' => '$data["sydToday"]','htmlOptions' => array('class' => 'cargoClose')),
				array('header' => 'Syd Left', 'type' => 'raw', 'value' => 'Chtml::link($data["sydLeft"]-$data["sydToday"],"/report/GetWareHouseReminder?dpt_id=106",array("class"=>"tab_link","title"=>"WareHouse Reminder(SYD)","style"=>"color:red"))','htmlOptions' => array('class' => 'cargoLeft')),
				array('header' => 'Mel Today', 'type' => 'raw', 'value' => '$data["melToday"]','htmlOptions' => array('class' => 'cargoClose')),
				array('header' => 'Mel Left', 'type' => 'raw', 'value' => 'Chtml::link($data["melLeft"]-$data["melToday"],"/report/GetWareHouseReminder?dpt_id=218",array("class"=>"tab_link","title"=>"WareHouse Reminder(MEL)","style"=>"color:red"))','htmlOptions' => array('class' => 'cargoLeft')),
				array('header' => 'Bne Today', 'type' => 'raw', 'value' => '$data["bneToday"]','htmlOptions' => array('class' => 'cargoClose')),
				array('header' => 'Bne Left', 'type' => 'raw', 'value' => 'Chtml::link($data["bneLeft"]-$data["bneToday"],"/report/GetWareHouseReminder?dpt_id=530",array("class"=>"tab_link","title"=>"WareHouse Reminder(BNE)","style"=>"color:red"))','htmlOptions' => array('class' => 'cargoLeft')),
			),
		));
		//Chtml::link($data["melLeft"],"/report/getCargoProcessReportListMel",array("class"=>"tab_link"))
		?>
</div>