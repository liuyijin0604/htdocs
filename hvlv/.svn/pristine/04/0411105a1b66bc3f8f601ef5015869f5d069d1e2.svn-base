<style>
    table,
    th,
    td {
        border: 1px solid;
        border-collapse: collapse;
    }

    table {
        width: 100%;
    }

    th,
    td {
        height: 60px;
    }

    td {
        text-align: center;
    }

    .button {
        margin-top: 14px;
        width: 60px;
        text-align: center;
        float: right;
        height: 26px;
    }
</style>
<form action="<?= $this->createUrl('pickupBookingSlot/setSlots'); ?>" method="POST">
    <input type="text" id="depot" name="depot" value="<?php switch ($_GET['id']) {
                                                            case 0:
                                                                echo "Sydney";
                                                                break;
                                                            case 1:
                                                                echo "Melbourne";
                                                                break;
                                                            case 2:
                                                                echo "Brisbane";
                                                                break;
                                                        } ?>" style="display: none;" />
    <table>
        <tr>
            <th></th>
            <th>:00</th>
            <th>:10</th>
            <th>:20</th>
            <th>:30</th>
            <th>:40</th>
            <th>:50</th>
        </tr>
        <tr>
            <td>Select All</td>
            <td><input type="checkbox" id="col_<?= $_GET['id'] ?>_0" value="selected" /></td>
            <td><input type="checkbox" id="col_<?= $_GET['id'] ?>_1" value="selected" /></td>
            <td><input type="checkbox" id="col_<?= $_GET['id'] ?>_2" value="selected" /></td>
            <td><input type="checkbox" id="col_<?= $_GET['id'] ?>_3" value="selected" /></td>
            <td><input type="checkbox" id="col_<?= $_GET['id'] ?>_4" value="selected" /></td>
            <td><input type="checkbox" id="col_<?= $_GET['id'] ?>_5" value="selected" /></td>
        </tr>
        <?php
        for ($i = 0; $i < 11; $i++) {
            echo "<tr>";
            echo "<td>" . ($i + 7) . "</td>";
            for ($j = 1; $j < 7; $j++) {
                //echo "<td>".$model[$j+6*$i-1]->status."</td>";
                echo "<td><input type='checkbox' id='" . $_GET['id'] . "_" . $i . "_" . $j . "' name='" . ($j + 6 * $i - 1) . "' value='active'";
                if ($model[$j + 6 * $i - 1]->status == 1) {
                    echo " checked></td>";
                } else {
                    echo "></td>";
                }
            }
            echo "</tr>";
        }
        ?>
    </table>
    <button type="submit" class="ui-button ui-widget ui-corner-all button">Save</button>
</form>

<script>
    $(document).ready(function() {
        for (let i = 0; i < 6; i++) {
            $("#col_<?= $_GET['id'] ?>_" + i).change(function() {
                if ($("#col_<?= $_GET['id'] ?>_" + i).is(':checked')) {
                    for (let j = 0; j < 11; j++) {
                        $("#<?= $_GET['id'] ?>_" + j + "_" + (i + 1)).prop('checked', true);
                    }
                } else {
                    for (let j = 0; j < 11; j++) {
                        $("#<?= $_GET['id'] ?>_" + j + "_" + (i + 1)).prop('checked', false);
                    }
                }
            })
        }
    })
</script>