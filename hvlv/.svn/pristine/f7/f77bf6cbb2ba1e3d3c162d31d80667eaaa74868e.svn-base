<div class="container">
   <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'chargecode-zone-map-form-im',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('accounts/ajaxSaveChargecodeZonemap'),
    ));
    ?>
    <div class="row">
        <div class="form-group">
        <label for="aupost">Zone Map Profile - <small>.xlsx File</small>(<a href="/org_zonemap_template.xlsx" target="_blank">Get template file</a>)</label><br>
        <input type="file" name="org-zonemap" id="org_zonemap" class="form-control-file" />
        </div>
    </div>
   <?php
    // get latest uploaded zone map file
    $attachements = FileRepo::model()->findAll('fid = :oid and type = 55 order by id desc', [':oid' => $model->id]);
    $index = 1;
    ?>
  
    <div><ul id="attachements-div">
            <?php
            foreach ( $attachements as $attachement ) {
                $line = '<li>' . $index++ . '. <a target="_blank" href="../../../'.$attachement->getUrlRelate().'" >' . $attachement->name . '</a></li>';
                echo $line;
            }
            ?>
        </ul></div>
  <?php echo $form->hiddenField($model,'id'); ?>
    <div class="row">
        <div class="form-group">
       <input id="chargecode_zone_map_import_btn" class="btn btn-primary" type="submit" value="Submit" />
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>
<div id="chargecode_zone_map_import_result" style="margin: 10px 0; border: 1px solid;padding:20px; font-weight: bold; font-size: 20px;">
</div>
 <?php ob_start(); ?>
<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        $('form#chargecode-zone-map-form-im', panel).data('custom_success', function(r){
            $('#chargecode_zone_map_import_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
            $('#chargecode_zone_map_import_btn', panel).attr('disabled', false);
            return true;
        });
    });
</script>
<?php $this->registerJS(ob_get_clean()); ?>