<style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    td,
    th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }
</style>
<h1>Manage Warehouse User</h1>

<form id="manage_user">
    <div class="form">
        <div class="row">
            <div class="row rowcol rowleft">
                <label for="warehouse_id">Warehouse</label>
                <select id="warehouse_id" name="warehouse_id">
                    <option value="106">Sydney</option>
                    <option value="218">Melbourne</option>
                    <option value="530">Brisbane</option>
                    <option value="811">Perth</option>
                </select>
            </div>
            <div class="row rowcol">
                <!-- <label for="user_id">User</label>
                <input type="text" id="user_id" name="user_id" /> -->
                <?php echo CHtml::label('User', 'User', array('required' => 'required')); ?>
                <?php echo CHtml::hiddenField('user_id');
                $acname = empty($_GET["tabid"]) ? 'owner_ac' : $_GET["tabid"] . '_owner_ac';
                $this->widget(
                    'zii.widgets.jui.CJuiAutoComplete',
                    array(
                        'name' => $acname,
                        'sourceUrl' => array('org/opSuggest'),
                        'value' => '',
                        'options' => array(
                            'showAnim' => 'fold',
                            'minLength' => 2,
                            'delay' => 200,
                            'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
                            'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val(""); return false; }',
                        ),
                        'htmlOptions' => array(
                            'size' => '50',
                        ),
                    )
                );
                ?>
            </div>
        </div>
        <div class="row">
            <input type="submit" value="Add">
        </div>
    </div>
</form>

<div class="container">
    <h3>Warehouse deconsolidation user list</h3>
    <table id="<?= $_GET['tabid'] . '_user_table' ?>">
        <tr>
            <th>User</th>
            <th>Warehouse</th>
            <th>Active</th>
            <th>Action</th>
        </tr>
        <?php
        foreach ($totalUsers as $user) {
            echo "<tr>";
            echo "<td>" . $user->userName . "</td>";
            echo "<td>" . $user->warehouse . "</td>";
            echo "<td>" . $user->isActive . "</td>";
            echo "<td><button id='btn_" . $user->id . "' class='inactivate-btn'>Inactivate</button></td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>

<script>
    $(function () {
        $('#manage_user').on('submit', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            let formData = new FormData(this);
            formData.append('acName', "<?= $acname ?>");
            $.ajax({
                url: '<?= $this->createUrl("deconsolidation/manageUser") ?>',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                enctype: 'multipart/form-data',

                /* success: function (response) {
                    let res = jQuery.parseJSON(response);
                    console.log(res.msg);
                } */
            });
        });

        $('.inactivate-btn').on('click', function (event) {
            let elementId = event.target.id;
            let userId = elementId.split('_').pop();
            let formData = new FormData;
            formData.append('userId', userId);
            /* $.ajax({
                url = '<?= $this->createUrl("deconsolidation/inactivateUser") ?>',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                enctype: 'multipart/form-data',
            }); */
            $.ajax({
                url: '<?= $this->createUrl("deconsolidation/inactivateUser") ?>',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                enctype: 'multipart/form-data',

                /* success: function (response) {
                    let res = jQuery.parseJSON(response);
                    console.log(res.msg);
                } */
            });
        });
    });


</script>