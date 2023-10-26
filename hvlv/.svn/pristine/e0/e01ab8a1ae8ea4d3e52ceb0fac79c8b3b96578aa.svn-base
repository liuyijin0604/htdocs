<!DOCTYPE html>

<head>
</head>

<body>
    <h1>Import Interstate Rate</h1>
    <div class="form">
        <form id="import_interstate_rate_form">
            <div class="row">
                <label for="org_id">Org Id(0 for universal rate)</label>
                <input type="text" id="org_id" name="org_id" />
            </div>
            <!-- <div class="row">
                <label for="charge_type">Charge By</label>
                <select id="charge_type" name="charge_type">
                    <option value="1">Weight</option>
                    <option value="2">Pallet</option>
                </select>
            </div> -->
            <div class="row">
                <label for="import_file">Import File</label>
                <input type="file" id="import_file" name="import_file" />
            </div>
            <div class="row">
                <input type="submit" value="Submit" />
            </div>
        </form>
    </div>
    <div class="container" id="interstate_rate_import_result">

    </div>
    <script type="text/javascript">
        $(function() {
            $('form#import_interstate_rate_form').on('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                let formData = new FormData(this);

                $.ajax({
                    url: '<?=$this->createUrl("interstateChargeRate/import")?>',
                    type: 'POST',
                    data: formData,
                    enctype: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        let r = jQuery.parseJSON(response);
                        $("#interstate_rate_import_result").append(r.errorMessage);
                        myApp.notice('success', 5000);
                    }
                })
            })
        })
    </script>
</body>

</html>