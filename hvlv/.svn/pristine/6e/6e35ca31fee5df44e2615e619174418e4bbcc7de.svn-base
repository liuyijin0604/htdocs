<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'aupost_cost_import_form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('import/AjaxAupostCostImport'),
    ));
    ?>
    <div class="row">
        <label for="aupost">AuPost Cost Import - <small>.csv File</small></label> <br>
        <input type="file" name="aupost" id="aupost" />
    </div>
    <br>
    <p><input id="aupost_cost_import_btn" type="submit" value="Submit" /></p>
    <?php $this->endWidget(); ?>
</div>
<div id="aupost_cost_import_result" style="margin: 10px 0; border: 1px solid;padding:20px; font-weight: bold; font-size: 20px;">
</div>

<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        $('form#aupost_cost_import_form', panel).data('custom_success', function(r){
            $('#aupost_cost_import_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
            $('#aupost_cost_import_btn', panel).attr('disabled', false);
            return true;
        });
    });
</script>