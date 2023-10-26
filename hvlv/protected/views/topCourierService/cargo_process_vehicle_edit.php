<style>
    .width_label {
        width: 100px;
        display: inline-block;
    }

    .width_input {
        width: 350px;
    }

    .mb{
        margin-bottom: 5px;
    }
</style>


<div>
    <h1>Cargo Process Vehicle:</h1>
    <div>
        <form id="form_vehicle">

            <label class="width_label mb">Plate Number:</label>
            <?php if (empty($objCargoProcessVehicle->plate_number)): ?>
                <input class="width_input" type="text" name="plate_number"></input>
            <?php else: ?>
                <a><?=$objCargoProcessVehicle->plate_number?></a>
            <?php endif; ?>

            <br/>
            <label class="width_label mb" >Type:</label>
            <span id="type">
                <?php if ($objCargoProcessVehicle->type == CargoProcessVehicle::enum_type_big_vehicle): ?>
                    <input value="1" id="type_0" type="radio" name="type">
                    <label for="type_0">Small Vehicle</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input value="2" id="type_1" checked="checked" type="radio" name="type">
                    <label for="type_1">Big Vehicle</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <?php else: ?>
                    <input value="1" id="type_0" checked="checked" type="radio" name="type">
                    <label for="type_0">Small Vehicle</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input value="2" id="type_1" type="radio" name="type">
                    <label for="type_1">Big Vehicle</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <?php endif; ?>
            </span>
            <br/>
            <label class="width_label mb">Length(cm):</label>
            <input class="width_input" type="text" name="length" value="<?=$objCargoProcessVehicle->length?>"></input>
            <br/>
            <label class="width_label mb">Width(cm):</label>
            <input class="width_input" type="text" name="top_width" value="<?=$objCargoProcessVehicle->top_width?>"></input>
            <br/>
            <label class="width_label mb">Height(cm):</label>
            <input class="width_input" type="text" name="height" value="<?=$objCargoProcessVehicle->height?>"></input>
            <br/>
            <label class="width_label mb">CBM:</label>
            <input class="width_input" type="text" name="min_cbm" value="<?=$objCargoProcessVehicle->min_cbm?>"></input>
            <br/>
            <label class="width_label mb">Status:</label>
            <!-- <input class="width_input" type="text" name="status"></input> -->
            <span id="status">
                <?php if ($objCargoProcessVehicle->status == CargoProcessVehicle::enum_status_inactive): ?>
                    <input value="1" id="status_0" type="radio" name="status">
                    <label for="status_0">Active</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input value="0" id="status_1" checked="checked" type="radio" name="status">
                    <label for="status_1">Inactive</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <?php else: ?>
                    <input value="1" id="status_0" checked="checked" type="radio" name="status">
                    <label for="status_0">Active</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input value="<?=CargoProcessVehicle::enum_status_inactive?>" id="status_1" type="radio" name="status">
                    <label for="status_1">Inactive</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <?php endif; ?>
            </span>
            <br/>
            <?php if (!empty($objCargoProcessVehicle->id)): ?>
                <input value="<?=$objCargoProcessVehicle->id?>"  type="hidden" name="id">
            <?php endif; ?>
        </form>

    </div>
    <br/>
    <input type="button" value="submit" onclick="funcSubmit()" >
</div>

<script>
    function funcSubmit(){
        listData = $('#form_vehicle').serializeArray();
        htmlobj = $.ajax({
            type: "POST",
            url: "<?= $this->createUrl('topCourierService/editVehicle'); ?>",
            data: listData,
            async: false
        });
        obj = JSON.parse(htmlobj.responseText);
        if (obj.isSuccess) {
            $('#jqmw_<?=$_GET["tabid"];?>').jqmHide();
            $('#cargo_process_vehicle_list').yiiGridView('update');
            myApp.notice('success', 5000);
            
        }
        else{
            var strError = '';
            for (i = 0; i < obj.listError.length; i++) {
                strError+= obj.listError[i];
            }
            alert (strError);
        }
    }
</script>