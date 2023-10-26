<div class="pane">
    <h1>Tell Me Revenue(Test)</h1>
    <div class="form" id="date-form">
        <div>
            <input type="hidden" name='tabid' value="<?= $_GET['tabid'] ?>" />
        </div>
        <div class="row rowcol rowleft">
            <?php echo CHtml::label('Start Date', 'start_date'); ?>
            <?php echo CHtml::textField('start_date', date('Y-m-d'), array('class' => 'date_input', 'id' => 'start_date' . $_GET['tabid'])); ?>
        </div>
        <div class="row rowcol">
            <?php echo CHtml::label('End Date', 'end_date'); ?>
            <?php echo CHtml::textField('end_date', date('Y-m-d'), array('class' => 'date_input', 'id' => 'end_date' . $_GET['tabid'])); ?>
        </div>
        <div class="row rowcol rowleft">
            <?php echo CHtml::label('New Chargecode', 'new_chargecode'); ?>
            <?php echo CHtml::textField('new_chargecode', "", array('id' => 'new_chargecode' . $_GET['tabid'])); ?>
        </div>
        <div class="form">
            <br>
            <br>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton($this->t('Export Details'), array('class' => 'export_detail')); ?>
        </div>
        <div class="uploading"><img src="https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif" width="24" /> Loading</div>
        <div id="shipment-import-cost-report-view">
           
        </div>
    </div>
    <style>
        .uploading {
            position: relative;
            clear: both;
            width: 150px;
            font-size: 1.4em;
            font-weight: bold;
            color: #BC3426;
            line-height: 32px;
            z-index: 99;
            padding: 15px 5px;
            margin-bottom: -50px;
            display: none;
        }
    </style>
    <div id="export_import_cost_detail" style="margin: 10px 0; border: 1px solid;padding:20px;display: none;"></div>
</div>
<script>
    $(function() {
        var tab = $('#<?= $_GET["tabid"]; ?>');
        var panel = tab.data('panel');

        $('.export_detail', panel).click(function(e) {
            e.preventDefault();
            e.stopPropagation();
            var data = {};
            data['data_source'] = [];
            data['available_couriers'] = [];
            data['test_couriers'] = [];
            data['start_date'] = $("#start_date<?= $_GET['tabid'] ?>", panel).val();
            data['end_date'] = $("#end_date<?= $_GET['tabid'] ?>", panel).val();
            data['new_chargecode'] = $("#new_chargecode<?= $_GET['tabid'] ?>", panel).val();
            data['inc_bfe'] = $('input[name="inc_bfe"]', panel).prop('checked');
            data['partial'] = 11;
            $('.uploading', panel).fadeIn();
            $('#export_import_cost_detail', panel).html('');
            $.ajax({
                type: 'GET',
                url: '<?php echo Yii::app()->createAbsoluteUrl("importsCost/ajaxExportImportsCourierRevenue"); ?>',
                data: data,
                dataType: 'html',
                success: function(resp) {
                    $('#export_import_cost_detail', panel).show();
                    $('#export_import_cost_detail', panel).html(resp);
                    $('.uploading', panel).fadeOut();
                }
            });
        });

        $('form#test-zone-map-form', panel).data('custom_success', function(r) {
            if (r.success == 1) {
                $("#testCouriers", panel).html(r.msg);
                myApp.alert("import success");
            }
            $("#test_zone_map_import_btn", panel).attr("disabled", false);
        });

        $('form#test-pca-zone-map-form', panel).data('custom_success', function(r) {
            if (r.success == 1) {
                $("#pcaTestZone", panel).html(r.msg);
                myApp.alert("import success");
            }
            $("#test_pca_zone_map_import_btn", panel).attr("disabled", false);
        });

        $('form#test-weight-zone-map-form', panel).data('custom_success', function(r) {
            if (r.success == 1) {
                $("#weightZone", panel).html(r.msg);
                myApp.alert("import success");
            }
            $("#test_weight_zone_map_import_btn", panel).attr("disabled", false);
        });

        $('.showAllAvailableCouriersButton', panel).click(function() {
            if ($('#allAvailableCouriers', panel).css("display") == "none") {
                $('#allAvailableCouriers', panel).show();
            } else {
                $('#allAvailableCouriers', panel).hide();
            }
        });
    });

    function deleteTestCouriers(id) {
        if (confirm("Are you sure to delete the Test Data?")) {
            var tab = $('#<?= $_GET["tabid"]; ?>');
            var panel = tab.data('panel');
            $.ajax({
                type: 'POST',
                url: '<?php echo Yii::app()->createAbsoluteUrl("importsCost/deleteTestCouriers"); ?>',
                dataType: 'html',
                data: {
                    'id': id
                },
                success: function(resp) {
                    $("#testCouriers", panel).html(resp);
                }
            });
        }
    }

    function deleteZoneMapImport(id, type) {
        if (confirm("Are you sure to delete the Test Data?")) {
            var tab = $('#<?= $_GET["tabid"]; ?>');
            var panel = tab.data('panel');
            $.ajax({
                type: 'POST',
                url: '<?php echo Yii::app()->createAbsoluteUrl("importsCost/deleteZoneMapImport"); ?>',
                dataType: 'html',
                data: {
                    'id': id,
                    'type': type
                },
                success: function(resp) {
                    if (type == 0) {
                        $("#pcaTestZone", panel).html(resp);
                    } else {
                        $("#weightZone", panel).html(resp);
                    }
                }
            });
        }
    }
</script>