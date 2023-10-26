
<h1> Transform Template </h1>
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'transform-tempate-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('zoneMap/ajaxTransformingTemplate'),
    ));
    ?>


    <div class="row" style="margin-top: 20px;">
        <input type="file" name="template_file" id="template_file" />
        <?php echo CHtml::dropDownList('type',1,[1=>'new_fastway']); ?>
         <?php echo CHtml::dropDownList('t_type',1,[1=>'old_fastway']); ?>
    </div>
    
    <p style="margin-top:20px;"><input id="transform_btn" type="submit" value="Transform template to zoneMap" /></p>


    <?php $this->endWidget(); ?>
</div>
<div id="transform_result" style="margin: 10px 0; border: 1px solid;padding:20px;">
</div>



<script type="text/javascript">


    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');

        $('#transform_btn',panel).click(function(e){
            e.preventDefault();
            $('form#transform-tempate-form', panel).submit();
        });


        $('form#transform-tempate-form', panel).data('custom_success', function(r){
            $('#transform_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
            $('#transform_btn', panel).attr('disabled', false);
            return true;
        });

    });
</script>
