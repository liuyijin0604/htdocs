<h3>Generate Pallet Label</h3>
<?php

?>
<div class="container">
    <form id="generate_label_form">
        <div class="row">
            <label for="label_count">Number of Labels(no more than 10 labels)</label>
            <br />
            <input type="number" min="0" max="10" id="label_count" name="label_count" style="width: 250px;" required />
        </div>
        <br />
        <div class="row">
            <input type="submit" value="Generate" />
        </div>
    </form>
    <div id="download">

    </div>
</div>

<script>
    $(function() {
        $('form#generate_label_form').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $('#download').empty();
            let formData = new FormData(this);
            $.ajax({
                'url': '<?= $this->createUrl("warehouseProcess/generateLabel") ?>',
                type: 'POST',
                data: formData,
                enctype: 'multipart/form-data',
                cache: false,
                contentType: false,
                processData: false,
                success: function(res) {
                    $('#download').append('<p>Label generated <a href="<?=$this->createUrl('warehouseProcess/downloadLabel')?>?postFix=' + res + '" target="_blank">Click here</a> to download label</p>');
                }
            })
        })
    })
</script>
