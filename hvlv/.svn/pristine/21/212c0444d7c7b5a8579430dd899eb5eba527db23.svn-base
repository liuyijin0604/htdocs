<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'org-non-delivery-areas-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('org/AjaxSaveOrgNonDeliveryAreas'),
    ));
    ?>
    <div class="row">
        <input type="file" name="org_non_delivery_areas" id="org_non_delivery_areas" />
    </div>
    <?php echo $form->hiddenField($model,'id'); ?>

    <br>

    <p><input id="org_non_delivery_areas_btn" type="submit" value="Submit" /></p>
    <?php $this->endWidget(); ?>
</div>
<!--
<div id="org_non_delivery_areas_result" style="margin: 10px 0; border: 1px solid;padding:20px; font-weight: bold; font-size: 20px;">
-->
</div>

<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        $('form#org-non-delivery-areas-form', panel).data('custom_success', function(r){
            $('#org_non_delivery_areas_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
            $('#org_non_delivery_areas_btn', panel).attr('disabled', false);
            return true;
        });
    });
</script>