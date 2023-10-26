<head>
    <style>
        #consol_managment .type_row {
            color: rgb(119, 119, 218);
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }

        .flex-container {
            display: flex;
        }

        .flex-child {
            flex: 1;
        }

        .pallet-request-report-table {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        .pallet-request-report-table td,
        .pallet-request-report-table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .pallet-request-report-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .pallet-request-report-table tr:hover {
            background-color: #ddd;
        }

        .pallet-request-report-table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #04AA6D;
            color: white;
        }
    </style>
</head>
<div style="right: 20px;position: absolute;">
    <a href="<?= $this->createUrl('cargoProcessPlan/create') ?>" class="tab_link" title="Create">
        <div style="background-position:-48px -688px" class="icon"></div>Create
    </a>
</div>
<h1><?= $this->t('Pallet Delivery Request'); ?></h1>
<div class="row" style="display: none;">
    <?php echo CHtml::checkbox('auto_refresh', ''), $this->t(' <b>Auto refresh</b>'); ?>
</div>

<div class="container" id="pallet_request_summary" style="display:block;">
<table class="pallet-request-report-table">
    <tr>
        <th>New</th>
        <th>Today Complete</th>
        <th>MTD Complete %</th>
    </tr>
    <tr>
        <td><?= $newRecordsCount ?></td>
        <td><?= $todayCompleteCount ?></td>
        <td><?php echo ($newRecordsCount + $mtdCompleteCount) > 0 ? number_format(($mtdCompleteCount/($newRecordsCount + $mtdCompleteCount)*100), 2, '.', '') . "%" . "  (" . $mtdCompleteCount . "/" . ($newRecordsCount + $mtdCompleteCount) . ")" : "NaN"; ?></td>
    </tr>
</table>
</div>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'cargo-process-plan-grid',
    'selectableRows' => 2,
    'cssFile' => false,
    'dataProvider' => $model->search(true, 30, @$ec),
    'filter' => $model,
    'columns' => array(
        array('name' =>'schedule_time','htmlOptions'=>array('style'=>'width: 120px')),
        array('name' =>'ref','htmlOptions'=>array('style'=>'width: 150px')),
        array('name' => 'dpt_id', 'value' => '$data->getBranch()', 'htmlOptions'=>array('style'=>'width: 80px'),'filter'=>CHtml::dropDownList('CargoProcessPlan[dpt_id]', $model->dpt_id, Org::dptList3PL(), ['prompt'=>$this->t('All'),'class' => 'form-control']),),
        array('name' => 'pallets_note','htmlOptions'=>array('style'=>'width: 300px')),
        array('header' => 'Delivery Status','value'=>'$data->getCjobStatus()','htmlOptions'=>array('style'=>'width: 200px'),'filter'=>CHtml::dropDownList('CargoProcessPlan[status]', $model->status, CargoProcessPlan::$cargoplanlist_states, ['prompt'=>$this->t('All'),'class' => 'form-control']),),
        //array('name' => 'status', 'value' => '$data->getStatus()','htmlOptions'=>array('style'=>'width: 80px'), 'filter'=>CHtml::dropDownList('CargoProcessPlan[status]', $model->status, CargoProcessPlan::$cargoplan_states, ['prompt'=>$this->t('All'),'class' => 'form-control']),),
        array('header' => 'Delivery Date','value'=>'$data->getCjobTime()','htmlOptions'=>array('style'=>'width: 120px')),
        array('header' => 'Driver','value'=>'$data->getCjobDriver()'),
        array('name' => 'creater', 'value' => '$data->getCreaterName()','filter'=>CHtml::dropDownList('CargoProcessPlan[creater]',$model->creater,$arrUser,['prompt'=>$this->t('All')]),'htmlOptions'=>array('style'=>'width: 120px')),
        array('header' => 'Invoice No','value'=>'$data->getInvoiceNo()','htmlOptions'=>array('style'=>'width: 90px')),
        array('header' => 'Consol Number', 'value' => '$data->getConsolNumber()'),
        array(
            'class' => 'oButtonColumn',
            'template' => '{update}',
            'buttons' => array(
                // 'view' => array(
                //     'imageUrl' => false,
                //     'options' => array('class' => 'jqm_link grid_view_btn'),
                // ),
                'update' => array(
                    'imageUrl' => false,
                    'visible' => 'true',
                    'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->getNo()'),
                ),
            ),
        ),
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

        $('a.export_search', panel).on('mousedown', function() {
            var q = $('.filters input, .filters select', panel).serialize();
            var href = $(this).data('baseurl') + '&' + q;
            href = href.replace('.app&', '?');
            $(this).attr('href', href);
        });

        $('a.export', panel).on('mousedown', function() {
            var href = $(this).data('baseurl');
            $('.select-on-check', panel).each(function() {
                if ($(this).prop('checked') === true) {
                    href += '&ids[]=' + $(this).val();
                }
            });
            href = href.replace('/.app?', '?');
            $(this).attr('href', href);
        });

        $('a.batch_export_search', panel).on('mousedown', function() {
            var q = $('.filters input, .filters select', panel).serialize();
            var href = $(this).data('baseurl') + '&' + q;
            $('.select-on-check', panel).each(function() {
                if ($(this).prop('checked') === true) {
                    href += '&ids[]=' + $(this).val();
                }
            });
            href = href.replace('/.app?', '?');
            $(this).attr('href', href);
        });
    });
</script>