<!DOCTYPE html>
<html>

<head>

</head>

<body>
    <h1>Export Cargo Cost</h1>
    <br />
    <div class="container">
        <form id="export_cost_form">
            <div class="row">
                <div class="col-lg-4">
                    <div class="input-group">
                        <label for="driver_id" class="form-label">Select Driver</label>
                        <br />
                        <select name="driver_id" id="driver_id">
                            <option value="">Select</option>
                            <?php
                                $setting = SystemSetting::model()->findByAttributes(array('key'=>'cargo_driver'));
                                if (!empty($setting) && !empty($setting->mdata['driver_list'])) {
                                    $drivers = $setting->mdata['driver_list'];
                                    ksort($drivers);
                                    foreach ($drivers as $oid => $name) {
                                        echo '<option value="' . $oid . '">' . $name . '</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="input-group">
                        <label for="cost_type" class="form-label">Select a Type</label>
                        <br />
                        <select id="cost_type" name="cost_type">
                            <option value="B2B">B2B/FBA</option>
                            <option value="B2C">B2C</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="input-group">
                        <label for="from_date" class="form-label">From</label>
                        <br />
                        <input type="date" class="form-control" id="from_date" name="from_date" autocomplete="off" required>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="input-group">
                        <label for="to_date" class="form-label">To</label>
                        <br />
                        <input type="date" class="form-control" id="to_date" name="to_date" autocomplete="off" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <input type="submit" value="Export" class="btn btn-primary" style="margin-top: 25px;">
                </div>
            </div>
        </form>
    </div>

    <script>
        $(function() {
            $('#export_cost_form').on('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                var formData = $(this)
                $.ajax({
                    type: "POST",
                    url: "<?= $this->createUrl('report/cargoInvoice'); ?>",
                    data: formData.serialize(),
                    dataType: "json",
                    encode: true,
                    success: function(res) {
                        var response = res;
                        location.href = "https://os.toplogistics.com.au/report/downloadCost?costType=" + response.costType + "&dateFrom=" + response.dateFrom + "&dateTo=" + response.dateTo + "&driverId=" + response.driverId;
                    }
                });
            });
        });
    </script>
</body>

</html>