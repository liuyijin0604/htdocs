<h1>Import RTC</h1>

<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'rtc-import-map-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('rts/AjaxImportRtc'),
    ));
    ?>
    <div class="row">
        <label for="aupost">rtc import - <small>.xlsx File</small>(<a href="/ims/rtc_template.xlsx" target="_blank">Get template file</a>)</label><br>
        <input type="file" name="rtc_form" id="rtc_form" />
    </div>

    <br>

    <p><input id="rtc_import_btn" type="submit" value="Submit" /></p>
    <?php $this->endWidget(); ?>
</div>


<div id="rtc_import_result" style="margin: 10px 0; border: 1px solid;padding:20px; font-weight: bold; font-size: 20px;">
</div>

<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        $('form#rtc-import-map-form', panel).data('custom_success', function(r){
            $('#rtc_import_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
            $('#rtc_import_btn', panel).attr('disabled', false);
            return true;
        });
    });
</script>