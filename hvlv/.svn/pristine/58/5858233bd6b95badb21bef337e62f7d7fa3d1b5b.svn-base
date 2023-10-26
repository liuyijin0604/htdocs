<style type="text/css">
	.cargoNew{
		color: #DAA569;
	}
	.cargoClose{
		color: #2E8B57;
	}
	.cargoLeft{
		color: #FF4500;
	}
</style>
<div style="right: 20px;position: absolute;">
</div>
<h1><?= $this->t('3PL Task Report Syd'); ?></h1>
<div class="row" style="display: none;">
    <?php echo CHtml::checkbox('auto_refresh', ''), $this->t(' <b>Auto refresh</b>'); ?>
</div>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'cargo-process-plan-grid',
    'selectableRows' => 2,
    'cssFile' => false,
    'dataProvider' => $model->search(true, 30, $ec),
    // 'filter' => $model,
    'columns' => array(
        array('header'=>'Date','name' => 'create_time','value' => 'date("Y-m-d",strtotime($data->create_time))','htmlOptions'=>array('style'=>'width: 75px')),
        array('header' => 'New Task','value' => '$data->mdata["todayNew"]["totalcount"]','htmlOptions' => array('class' => 'cargoNew')),
        array('header' => 'Revenue','value' => '$data->getInvoice()','htmlOptions' => array('class' => 'cargoNew')),
        array('header' => 'GP','value' => '$data->getGP()','htmlOptions' => array('class' => 'cargoNew')),
        array('header' => 'Left Task','value' => '$data->mdata["todayLeft"]["totalcount"]','htmlOptions' => array('class' => 'cargoLeft')),
        array('header' => 'Done Task','value' => '$data->mdata["todayDone"]["totalcount"]','htmlOptions' => array('class' => 'cargoClose')),
        array('header' => 'Completion','value' => '$data->getCompletion()','htmlOptions' => array('class' => 'cargoClose')),
    ),
));
?>
<script type="text/javascript">
    $(function() {
        var tab = $("#<?= $_GET['tabid']; ?>");
        var panel = tab.data('panel');
        $('.search-button', panel).click(function() {
            $('.search-form', panel).toggle();
            return false;
        });
        $('.search-form form', panel).on('submit', function() {
            var refs = $('#CargoProcessPlan_refs').val();
            refs = refs.split(/[\s,;]+/);
            if (refs.length > 200) {
                myApp.alert('refs is too long', false);
                return false;
            }
            $.fn.yiiGridView.update('cargo-process-plan-grid', {
                data: $(this).serialize()
            });
            return false;
        });
        tab.bind('onOpen', function() {
            $('#cargo-process-plan-grid', panel).yiiGridView('update');
        });

        $('#auto_refresh', panel).on('change', function() {
            if ($('#auto_refresh', panel).prop('checked') == true) {
                window.clearInterval(window.interval);
                window.interval = setInterval(function() {
                    $('#cargo-process-plan-grid', panel).yiiGridView('update');

                    tab.on('close', function() {
                        window.clearInterval(window.interval);
                    });
                }, 10000);
            } else {
                window.clearInterval(window.interval);
            }
        });
    });
</script>