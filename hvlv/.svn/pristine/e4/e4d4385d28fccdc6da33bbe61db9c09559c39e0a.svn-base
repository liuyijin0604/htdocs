<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'batch-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('import/AjaxSaveCostPlan'),
    ));
    ?>
    <div class="row">
        <label for="aupost">Australia Post Cost Setting - <small>.xlsx File</small>(<a href="/ims/au_post_cost_template.xlsx" target="_blank">Get template file</a>)</label> <br>
        <input type="file" name="aupost" id="aupost" />
    </div>
    <br>
    <div style="margin-bottom: 20px;">
        <?php echo CHtml::label('Valid From:','for-price-tpl'); ?>
        <?php echo CHtml::textField('valid-from-date',date('Y-m-d'),['class' => 'date_input']); ?>
    </div>


    <p><input id="batch_btn" type="submit" value="Submit" /></p>
    <?php $this->endWidget(); ?>
</div>
<div id="batch_result" style="margin: 10px 0; border: 1px solid;padding:20px; font-weight: bold; font-size: 20px;">
</div>

<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        $('form#batch-form', panel).data('custom_success', function(r){
            $('#batch_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
            $('#batch_btn', panel).attr('disabled', false);
            return true;
        });
    });
</script>