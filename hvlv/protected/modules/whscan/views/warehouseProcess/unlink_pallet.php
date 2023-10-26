<h1>Unlink pallet</h1>
<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
    echo '<a class="dash-item ajax-link" href="' . $this->createUrl('site/index', ['scan_warehouse' => 'sydney']) . '"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
    return;
}
?>

<div class="form">
    <form id="scan_info">
        <div id="selection">
            <input type="radio" id="pallet" name="type_selection" value="Pallet" checked>
            <label for="pallet">Pallet</label>
            <input type="radio" id="shipment" name="type_selection" value="Shipment">
            <label for="shipment">Shipment</label>
        </div>
        <div class="container" id="pallet_no_container">
            <input type="text" class="form-control" name="pallet_no" placeholder="Pallet Number" id="pallet_no" autocomplete="off" />
        </div>
        <div class="container" id="barcode_container" style="display: none;">
            <input type="text" class="form-control" name="barcode" placeholder="Barcode" id="barcode" autocomplete="off"/>
        </div>
    </form>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
    $(function() {
        $('input[type=radio][name=type_selection]').change(function() {
            if (this.value == 'Pallet') {
                $("#barcode_container").hide();
                $("#pallet_no_container").show();
            } else if (this.value == "Shipment") {
                $("#barcode_container").show();
                $("#pallet_no_container").hide();
            }
        });

        $('input#pallet_no').focus().on('keydown', function(e) {
            if (e.which == 13) {
                $(this).trigger('afterBarcode');
                return false;
            }
        }).on('afterBarcode', function() {
            var palletNumber = '';
            var typeSelection = '';
            var barcode = '';
            palletNumber = $('input#pallet_no').val();
            typeSelection = $("input[type=radio][name=type_selection]:checked").val();
            barcode = $("input#barcode").val();
            if (confirm('Are you sure to unlink this pallet?') == true) {
                $.ajax({
                    'url': '<?= $this->createUrl('warehouseProcess/unlinkPallet'); ?>',
                    'type': 'POST',
                    'data': {
                        'palletNumber': palletNumber,
                        'typeSelection': typeSelection,
                        'barcode': barcode
                    },
                    success: function(r) {
                        r = JSON.parse(r);
                        alert(r.status + ' ' + r.message);
                    }
                });
                $('#pallet_no').val('');
                $('#barcode').val('');
            } else {
                console.log('Yo!');
            }
        });

        $('input#barcode').focus().on('keydown', function(e) {
            if (e.which == 13) {
                $(this).trigger('afterBarcode');
                return false;
            }
        }).on('afterBarcode', function() {
            var typeSelection = '';
            var barcode = '';
            typeSelection = document.querySelector('input[name="type_selection"]:checked').value;
            barcode = $("input#barcode").val();
            if (confirm('Are you sure to unlink this pallet?') == true) {
                $.ajax({
                    'url': '<?= $this->createUrl('warehouseProcess/unlinkPallet'); ?>',
                    'type': 'POST',
                    'data': {
                        'typeSelection': typeSelection,
                        'barcode': barcode
                    },
                    success: function(r) {
                        r = JSON.parse(r);
                        alert(r.status + ' ' + r.message);
                    }
                });
                $('#pallet_no').val('');
                $('#barcode').val('');
            } else {
                console.log('Yo!');
            }
        });
    });
</script>