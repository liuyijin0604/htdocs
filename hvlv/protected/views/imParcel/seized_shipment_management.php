<?php
?>
<style>
    .round-border {
        border: 2px solid red;
        border-radius: 5px;
        padding: 5px;
    }
</style>
<h1>
    Seized Shipment Management
</h1>
<div style="right: 20px;position: absolute;">
    <a class="tab_link grid_edit_btn" title="Seized Shipment Record" href="<?= $this->createUrl('seizedShipment/admin'); ?>">Seized Shipment Record</a>
</div>
<br />
<div class="form">
    <form id="seized_shipment_form">
        <div class="row">
            <lable for="hbns"><b>Hbn/Ref</b><span style="color: red;">&nbsp;&nbsp;Multiple hbns separate by ; Space Enter</span></lable>
            <br />
            <textarea id="hbns" name="hbns" cols="80" rows="30"></textarea>
        </div>
        <div class="row">
            <label for="consol_no"><b>Consol No.(Optional)</b></label>
            <input type="text" id="consol_no" name="consol_no" />
        </div>

        <div class="row">
            <h4 style="border-left: 4px solid red;">Action</h4>
            <div class="col">
                <input type="checkbox" id="full" name="full" checked>
                <label for="full" style="display: inline-block;">Fully Seized<span style="color: red;">(海走)</span></label>&nbsp;&nbsp;&nbsp;
            </div>
            <div class="col">
                <input type="checkbox" id="partial" name="partial">
                <label for="partial" style="display: inline-block;">Partially Seized<span style="color: red;">(海走)</span></label>&nbsp;&nbsp;&nbsp;
            </div>
            <div class="col">
                <input type="checkbox" id="inspection" name="inspection">
                <label for="inspection" style="display: inline-block;">LCL Inspection<span style="color: red;">(海走)</span></label>&nbsp;&nbsp;&nbsp;
            </div>
            <div class="col">
                <input type="checkbox" id="return" name="return">
                <label for="return" style="display: inline-block;">LCL Return<span style="color: red;">(海还)</span></label>&nbsp;&nbsp;&nbsp;
            </div>
        </div>
        <br />



        <div class="row">
            <h4 style="border-left: 4px solid red;">Billing</h4>
            <div class="col">
                <input type="checkbox" id="force_billing" name="force_billing">
                <label for="force_billing" style="display: inline-block;">Force Billing</label>&nbsp;&nbsp;&nbsp;
            </div>
        </div>
        <br />


        <div class="row">
            <label for="upload_file">Upload File</label>
            <input type="file" id="upload_file" name="upload_file" />
        </div>
        <br />
        <div class="row">
            <input type="submit" class="button" />
        </div>
    </form>
</div>
<br />
<div class="container" id="result_container">
    <h3>Result:</h3>
    <p id="result"></p>
</div>

<script type="text/javascript">
    $(function() {
        $('#inspection').on('change', function() {
            if ($('input#inspection').prop('checked') == true) {
                $('input#full').prop('checked', false);
                $('input#partial').prop('checked', false);
                $('input#return').prop('checked', false);
            }
        });

        $('#partial').on('change', function() {
            if ($('input#partial').prop('checked') == true) {
                $('input#full').prop('checked', false);
                $('input#return').prop('checked', false);
                $('input#inspection').prop('checked', false);
            }
        });

        $('#return').on('change', function() {
            if ($('input#return').prop('checked') == true) {
                $('input#full').prop('checked', false);
                $('input#partial').prop('checked', false);
                $('input#inspection').prop('checked', false);
            }
        });

        $('#full').on('change', function() {
            if ($('input#full').prop('checked') == true) {
                $('input#return').prop('checked', false);
                $('input#partial').prop('checked', false);
                $('input#inspection').prop('checked', false);
            }
        });

        $('#seized_shipment_form').on('submit', function(e) {
            $('#result').empty();
            e.preventDefault();
            e.stopImmediatePropagation();

            let formData = new FormData(this);

            $.ajax({
                url: '<?= $this->createUrl("imParcel/seizedShipmentManagement") ?>',
                type: 'POST',
                data: formData,
                enctype: 'multipart/form-data',
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    let res = jQuery.parseJSON(response);
                    if (res.msg == 'Done') {
                        myApp.notice('Done', 5000);
                    } else {
                        $('p#result').html(res.msg);
                    }
                }
            })
        })
    })
</script>