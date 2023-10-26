<!DOCTYPE html>

<head>
</head>

<body>
    <div class="container">
        <div class="row">
            <h1>Deconsolidation List</h1>
        </div>
        <div class="row">
            <h4>Waiting for process:
                <?= count($list) ?>
            </h4>
        </div>
        <?php
        foreach ($list as $record) {
            if ($record->status == Deconsolidation::STATUS_WAREHOUSE_PROCESSING) {
                echo '<div class="row">';
                echo '<div class="panel panel-default">';
                echo '<div class="panel-heading">' . $record->shipment->ref . ' -- ' . $record->shipment->hbn . '</div>';
                echo '<div class="panel-body">';
                echo '<p>Packages: ' . $record->shipment->pkg . '</p>';
                echo '<p>Weight: ' . $record->shipment->weight . '</p>';
                echo '<p>Assign to warehouse time: ' . @$record->op_complete_time . '</p>';
                echo '<p>Due Time: <span style="color:red;">' . $record->getDueDate() . '</span></p>';
                echo '</div>';
                echo '<div class="panel-footer" style="text-align: right;"><input type="button" class="btn btn-danger error-report-btn" id="error_' . $record->id . '" value="Error Submit" data-toggle="modal" data-target="#error_submit_modal" />&nbsp;&nbsp;<input type="button" class="btn btn-primary warehouse-process-done-btn" id="' . $record->id . '" value="Done"/></div>';
                echo '</div></div>';
            } else {
                echo '<div class="row">';
                echo '<div class="panel panel-default">';
                echo '<div class="panel-heading"><span style="background:red;color:white;">Error Check</span>&nbsp;&nbsp;' . $record->shipment->ref . ' -- ' . $record->shipment->hbn . '</div>';
                echo '<div class="panel-body">';
                echo '<p>Error: ' . @$record->mdata['errors'] . '</p>';
                echo '</div>';
                echo '<div class="panel-footer" style="text-align: right;"><input type="button" class="btn btn-danger error-check-report-btn" id="error_check_' . $record->id . '" value="Cannot Find" data-toggle="modal" data-target="#error_check_submit_modal" />&nbsp;&nbsp;<input type="button" class="btn btn-primary error-check-done-btn" id="error_check_complete_' . $record->id . '" value="Found Parcel(s)"/></div>';
                echo '</div></div>';
            }
        }
        ?>

        <!-- The modal -->
        <div class="modal fade" id="error_submit_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="modalLabel">Error Submit</h4>
                    </div>
                    <form id="error_form">
                        <div class="modal-body">
                            <input type="text" id="deconsolidation_id" name="deconsolidation_id"
                                style="display:none;" />
                            <div class="row" style="margin-left: 10px;">
                                <label for="error_content">Error Details:</label>
                                <br />
                                <textarea id="error_content" name="error_content" rows="5" cols="40"
                                    required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="submit" class="btn btn-primary" value="Submit" />
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="error_check_submit_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="modalLabel">Error Check Result</h4>
                    </div>
                    <form id="error_check_form">
                        <div class="modal-body">
                            <input type="text" id="error_check_deconsolidation_id" name="error_check_deconsolidation_id"
                                style="display:none;" />
                            <div class="row" style="margin-left: 10px;">
                                <label for="error_check_content">Error Check Details:</label>
                                <br />
                                <textarea id="error_check_content" name="error_check_content" rows="5" cols="40"
                                    required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="submit" class="btn btn-primary" value="Submit" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover();
            $('.warehouse-process-done-btn').on('click', function (e) {
                let deconsolidationId = e.target.id;
                if (confirm('Are you sure warehouse deconsolidation process done?')) {
                    $.get('<?=$this->createUrl("deconsolidation/warehouseProcessDone")?>' + '?id=' + deconsolidationId, function (response) {
                        window.location.reload();
                    })
                }
            });

            $('.error-check-done-btn').on('click', function(e) {
                let deconsolidationId = e.target.id;
                if (confirm('Are you sure error check done?')) {
                    $.get('<?=$this->createUrl("deconsolidation/errorCheckDone")?>' + '?id=' + deconsolidationId, function (response) {
                        window.location.reload();
                    })
                }
            });

            $('.error-report-btn').on('click', function (e) {
                let deconsolidationId = e.target.id.split("_").pop();
                $('input#deconsolidation_id').val(deconsolidationId);
            });

            $('.error-check-report-btn').on('click', function(e) {
                let deconsolidationId = e.target.id.split("_").pop();
                $('input#error_check_deconsolidation_id').val(deconsolidationId);
            });

            $('form#error_form').on('submit', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                let formData = new FormData(this);
                $.ajax({
                    url: '<?= $this->createUrl("deconsolidation/errorReport") ?>',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    enctype: 'multipart/form-data',

                    success: function (response) {
                    window.location.reload();
					myApp.notice("Assign Done");
                }
                });
            });

            $('form#error_check_form').on('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                let formData = new FormData(this);
                $.ajax({
                    url: '<?= $this->createUrl("deconsolidation/errorCheckReport") ?>',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    enctype: 'multipart/form-data',

                    success: function (response) {
                    window.location.reload();
					myApp.notice("Assign Done");
                    }
                });
            });
        });
    </script>
</body>

</html>