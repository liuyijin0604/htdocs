<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'org-zone-map-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl($url),
    ));
    ?>
    <div class="row">
        <label for="aupost">Zone Map Profile - <small>.xlsx File</small>(<a href="/ims/org_zonemap_template.xlsx" target="_blank">Get template file</a>)</label><br>
        <input type="file" name="org-zonemap" id="org_zonemap" />
    </div>
    <?php echo $form->hiddenField($model,'id'); ?>

    <br>

    <p><input id="org_zone_map_import_btn" type="submit" value="Submit" /></p>
    <?php $this->endWidget(); ?>
</div>
<div id="org_zone_map_import_result" style="margin: 10px 0; border: 1px solid;padding:20px; font-weight: bold; font-size: 20px;">
</div>

<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        $('form#org-zone-map-form', panel).data('custom_success', function(r){
            $('#org_zone_map_import_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
            $('#org_zone_map_import_btn', panel).attr('disabled', false);
            return true;
        });
    });
</script>