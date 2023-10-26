<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'quotes-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('quotes/AjaxSaveQuotes'),
    ));
    ?>
    <div class="row">
        <label >Quotes Profile - <small>.xlsx File</small>(<a href="/ims/quotes_template.xlsx" target="_blank">Get template file</a>)</label><br>
        <input type="file" name="quotes-file" id="quotes-file" />
    </div>


    <br>

    <p><input id="quotes_import_btn" type="submit" value="Submit" /></p>
    <?php $this->endWidget(); ?>
</div>
<div id="quotes_import_result" style="margin: 10px 0; border: 1px solid;padding:20px; font-weight: bold; font-size: 20px;">
</div>

<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        $('form#quotes-form', panel).data('custom_success', function(r){
            $('#quotes_import_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
            $('#quotes_import_btn', panel).attr('disabled', false);
            return true;
        });
    });
</script>