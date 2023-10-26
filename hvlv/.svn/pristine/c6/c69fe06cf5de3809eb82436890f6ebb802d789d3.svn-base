<h3>Re-print Pallet Label</h3>
<?php

?>
<div class="container">
    <form id="reprint_label_form">
        <div class="row">
            <label for="pallet_no">Pallet No.(s) (separate by space or enter)</label>
            <br />
            <textarea id="pallet_no" name="pallet_no" cols="30" rows="15"></textarea>
        </div>
        <br />
        <div class="row">
            <input type="submit" value="Generate" />
        </div>
    </form>
    <div id="download_reprint_label">

    </div>
</div>

<script>
    $(function() {
        $('form#reprint_label_form').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $('#download_reprint_label').empty();
            let formData = new FormData(this);
            $.ajax({
                'url': '<?= $this->createUrl("warehouseProcess/reprintLabel") ?>',
                type: 'POST',
                data: formData,
                enctype: 'multipart/form-data',
                cache: false,
                contentType: false,
                processData: false,
                success: function(res) {
                    let onlyNumber = /^\d+$/.test(res);
                    if (onlyNumber) {
                        $('#download_reprint_label').append('<p>Label generated <a href="<?=$this->createUrl('warehouseProcess/downloadLabel')?>?postFix=' + res + '" target="_blank">Click here</a> to download label</p>');
                    } else {
                        $('#download_reprint_label').append('<p>' + res + '</p>'); 
                    }  
                }
            })
        })
    })
</script>
