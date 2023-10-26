<h1>Import Works!</h1>
<form id="import_form">
    <label for="import_file">Import File</label> <br />
    <input type="file" id="import_file" name="import_file" />
    <br />
    <br />
    <input type="submit" value="submit" />
</form>

<div class="container" id="result_container">

</div>

<script type="text/javascript">
    $(function(){
        $('#import_form').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var formData = new FormData(this);
            $.ajax({
                url: "<?= $this->createUrl('wordReplace/import')?>",
                type: 'POST',
                data: formData,
                enctype: 'multipart/form-data',
                cache: false,
                contentType: false,
                processData: false,
                success: function(res) {
                    var response = jQuery.parseJSON(res);
                    if (response.success == true) {

                    } else {
                        
                    }
                }
            })
        })
    })
</script>