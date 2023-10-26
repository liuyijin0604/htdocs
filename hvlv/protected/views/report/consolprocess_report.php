<style type="text/css">
	.consolNew{
		color: #DAA569;
	}
	.consolDone{
		color: #2E8B57;
	}
	.consolLeft{
		color: #FF4500;
	}
</style>
<div style="right: 20px;position: absolute;">
</div>
<h1><?= $this->t('Consol Process Report'); ?></h1>
<div class="row" style="display: none;">
    <?php echo CHtml::checkbox('auto_refresh', ''), $this->t(' <b>Auto refresh</b>'); ?>
</div>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'consol-process-plan-grid',
    'selectableRows' => 2,
    'cssFile' => false,
    'dataProvider' => $model->search(true, 30, $ec),
    // 'filter' => $model,
    'columns' => array(
        array('header'=>'Date','name' => 'create_time','value' => 'date("Y-m-d",strtotime($data->create_time))','htmlOptions'=>array('style'=>'width: 75px')),
        array('header' => 'ALL','value' => '$data->getConsolTodayNew()','htmlOptions' => array('class' => 'consolNew')),
        array('header' => 'AIR','value' => '$data->getConsolAirTodayNew()','htmlOptions' => array('class' => 'consolNew')),
        array('header' => 'SEA','value' => '$data->getConsolSeaTodayNew()','htmlOptions' => array('class' => 'consolNew')),
        array('header' => 'ALL','value' => '$data->getConsolTodayDone()','htmlOptions' => array('class' => 'consolDone')),
        array('header' => 'AIR','value' => '$data->getConsolAirTodayDone()','htmlOptions' => array('class' => 'consolDone')),
        array('header' => 'SEA','value' => '$data->getConsolSeaTodayDone()','htmlOptions' => array('class' => 'consolDone')),
        array('header' => 'ALL','value' => '$data->getConsolTodayLeft()','htmlOptions' => array('class' => 'consolLeft')),
        array('header' => 'AIR','value' => '$data->getConsolAirTodayLeft()','htmlOptions' => array('class' => 'consolLeft')),
        array('header' => 'SEA','value' => '$data->getConsolSeaTodayLeft()','htmlOptions' => array('class' => 'consolLeft')),
    ),
));
?>
