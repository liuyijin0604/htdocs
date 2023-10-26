<style type="text/css">
	.warehouseNew{
		color: #DAA569;
	}
	.warehouseClose{
		color: #2E8B57;
	}
	.warehouseLeft{
		color: #FF4500;
	}
</style>


<div class="row">
		<h3><a style="display: block;text-align:right;" href="<?= $this->createUrl('report/getConsolProcessReportList'.$department) ?>" class="tab_link" title="<?= 'Consol Process Report('.$department.')'?>">+ More</a></h3>
		<h3><a style="color:#DAA569"> New </a><a style="color:#2E8B57"> Done </a><a style="color:#FF4500"> Left </a></h3>
		<?php
		$this->widget('zii.widgets.grid.CGridView', array(
			'id' => 'dashboard-wid-whInOut-grid',
			'cssFile' => false,
			'dataProvider' => $monthProvide,
			'summaryText' => '',
			'enablePagination' => true,
			'pager' => array(
				'prevPageLabel' => 'Prev.',
				'maxButtonCount' => 5,
			),
			'columns' => array(
				array('header' => $periodToday, 'type' => 'raw', 'value' => '$data["col"]'),
				array('header' => 'ALL', 'type' => 'raw', 'value' => '$data["totaln"]','htmlOptions' => array('class' => 'warehouseNew')),
				array('header' => 'AIR', 'type' => 'raw', 'value' => '$data["airn"]','htmlOptions' => array('class' => 'warehouseNew')),
				array('header' => 'SEA', 'type' => 'raw', 'value' => '$data["sean"]','htmlOptions' => array('class' => 'warehouseNew')),
				array('header' => 'ALL', 'type' => 'raw', 'value' => '$data["totalc"]','htmlOptions' => array('class' => 'warehouseClose')),
				array('header' => 'AIR', 'type' => 'raw', 'value' => '$data["airc"]','htmlOptions' => array('class' => 'warehouseClose')),
				array('header' => 'SEA', 'type' => 'raw', 'value' => '$data["seac"]','htmlOptions' => array('class' => 'warehouseClose')),
				array('header' => 'ALL', 'type' => 'raw', 'value' => '$data["totall"]','htmlOptions' => array('class' => 'warehouseLeft')),
				array('header' => 'AIR', 'type' => 'raw', 'value' => '$data["airl"]','htmlOptions' => array('class' => 'warehouseLeft')),
				array('header' => 'SEA', 'type' => 'raw', 'value' => '$data["seal"]','htmlOptions' => array('class' => 'warehouseLeft')),
			),
		));
		?>
</div>