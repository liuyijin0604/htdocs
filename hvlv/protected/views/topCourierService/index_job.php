<h2>Cargo Job Process</h2>
<div class="row buttons">
    <div class="rowright">
        <?php if (User::checkIsNotTruckUser()) : ?>
            <?php if (Acl::hasAccess("C:systemSetting/cargoProcessSetting")) : ?>
                <div class="rowleft">
                    <?php echo "<a href=\"" . Yii::app()->createUrl("systemSetting/cargoProcessSetting") . "\" class=\"tab_link\" title=\"cargoProcessSetting\"><h2>Cargo Process Setting</h2></a>"; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="rowleft">
            <?php echo "<a href=\"" . Yii::app()->createUrl("topCourierService/viewDriverJobs") . "\" class=\"tab_link\" title=\"viewDriverJobs\"><h2>View Driver Delivery Jobs</h2></a>"; ?>
        </div>

        <?php
        echo CHtml::link("<h2>Cargo Process Summary</h2>", Yii::app()->createUrl("topCourierService/jobSummary"), array('class' => 'tab_link', 'title' => 'Cargo Process Summary'));
        ?>
        <div class="row buttons">
            <?php echo CHtml::submitButton('Fulfill CargoProcess', ['class' => 'fulfillCargoProcess']); ?>
        </div>
    </div>
</div>
<h3>Jobs Overview</h3>
<style>
    .cloumn_red_1 {
        color: red;
        font-weight: bold;
    }

    #cargo_process_job_dp_view .type_row,
    #cargo_main_process_type_id .type_row {
        color: rgb(119, 119, 218);
        text-align: center;
        font-size: 20px;
        font-weight: bold;
    }
</style>


<div style="width:50%">
    <?php
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'job_main_menue2' . $_GET['tabid'],
        'cssFile' => false,
        'dataProvider' => $dataProvider[0],
        'filter' => $dataProvider[1],
        'columns' => array(
            array(
                'name' => 'status', 'headerHtmlOptions' => array('style' => 'display:none'), 'filterHtmlOptions' => array('style' => 'display:none'),
                'htmlOptions' => array('style' => 'display:none'), 'type' => 'raw'
            ),
            array('name' => 'status', 'value' => '@CargoProcessJob::$processTypes[$data["status"]]'),
            'number',
            array('name' => 'day1', 'header' => '<=1 days'),
            array('name' => 'day2', 'header' => '2 days'),
            array('name' => 'day3', 'header' => '>=3 days', 'cssClassExpression' => '$data>0? "cloumn_red_1" : ""'),

        ),
    )); ?>
</div>
<div class="pane" id="cargo_process_job_dp_view" style="width:200px">
    <?php
    $column = [];
    $column[] = array(
        'name' => 'pod_id', 'headerHtmlOptions' => array('style' => 'display:none'), 'filterHtmlOptions' => array('style' => 'display:none'),
        'htmlOptions' => array('style' => 'display:none'), 'type' => 'raw'
    );
    $column[] = array('name' => 'pod', 'type' => 'raw', 'cssClassExpression' => '"type_row"');
    //    $process=ConsolProcess::$states;
    //    unset($process[80]);
    //    foreach($process as $key=>$value){
    //        $column[]=array('name'=>$key,'header'=>$value);
    //    }
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'cargo_main_menue' . $_GET['tabid'],
        'cssFile' => false,
        'dataProvider' => $dataProvider1[0],
        'columns' => $column,
    )); ?>
</div>

<div class="pane" id="cargo_process_job_dp_view_type">
    <?= $this->render('sub_type_list', array('dataProvider2' => $dataProvider2, 'dataProvider3' => $dataProvider3, 'name' => $name)); ?>
</div>
<script>
    $(function() {
        var tab = $("<?= $_GET['tabid'] ?>");
        var panel = tab.data('panel');
        $("#cargo_process_job_dp_view", panel).on('click', "table tbody td", function() {
            var podId = parseInt($(this).parent().children(':nth-child(1)').html());
            var data = {};
            data['pod_id'] = podId;
            $.ajax({
                type: 'GET',
                url: '<?php echo Yii::app()->createAbsoluteUrl("topCourierService/jobTypeList", array('tabid' => $_GET['tabid'])); ?>',
                data: data,
                dataType: 'html',
                success: function(resp) {
                    $('#cargo_process_job_dp_view_type').html(resp);
                },
            });
        });

        $('.fulfillCargoProcess', panel).on('click', function(event) {
            if (confirm('Are you sure to fulfill Cargoprocess?')) {
                var data = {};
                data['status'] = 0;
                $.ajax({
                    type: 'GET',
                    url: '<?php echo Yii::app()->createAbsoluteUrl('topCourierService/fulfillCargoProcess'); ?>',
                    data: data,
                    dataType: 'html',
                    success: function(resp) {
                        myApp.notice('Done', 5000);
                    },
                });
            }
        });
    })
</script>