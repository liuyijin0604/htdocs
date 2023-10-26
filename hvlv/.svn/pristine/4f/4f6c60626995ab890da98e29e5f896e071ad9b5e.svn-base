<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'chargecode-zone-map-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl($url),
    ));
    ?>
    <div class="row">
        <label for="aupost">Zone Map Profile - <small>.xlsx File</small>(<a href=<?=$template?> target="_blank">Get template file</a>)</label><br>
        <input type="file" name="org-zonemap" id="org_zonemap" />
    </div>



    <?php

    // get latest uploaded zone map file
    $attachements = FileRepo::model()->findAll('fid = :oid and type = :type order by id desc', [':oid' => $model->id,':type'=>$fileType]);
    $index = 1;
    ?>

    <div><ul id="attachements-div">
            <?php
            foreach ( $attachements as $attachement ) {
                $line = '<li>' . $index++ . '. <a target="_blank" href="' . $attachement->getUrl() . '" >' . $attachement->name . '</a></li>';
                echo $line;
            }
            ?>
        </ul></div>



    <?php echo $form->hiddenField($model,'id'); ?>

    <br>

    <p><input id="chargecode_zone_map_import_btn" type="submit" value="Submit" /></p>
    <?php $this->endWidget(); ?>
</div>


<div id="chargecode_zone_map_import_result" style="margin: 10px 0; border: 1px solid;padding:20px; font-weight: bold; font-size: 20px;">
</div>

<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        $('form#chargecode-zone-map-form', panel).data('custom_success', function(r){
            $('#chargecode_zone_map_import_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
            $('#chargecode_zone_map_import_btn', panel).attr('disabled', false);
            return true;
        });
    });
</script>