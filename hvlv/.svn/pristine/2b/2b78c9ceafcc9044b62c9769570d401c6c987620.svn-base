
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'crm-search-report-form',
    'enableAjaxValidation'=>false,
)); ?>

<div class="row">
    <div class="rowcol">

    <?php echo CHtml::label('Operator','opid'),
    CHtml::hiddenField('opid',$model['opid'],array('data-ov' => $model['opid']));
    $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
        'name' => 'to_id',
        'sourceUrl' => array('crm/fromSuggest'),
        'value' => '',
        'options' => array(
            'showAnim' => 'fold',
            'minLength' => 2,
            'delay' => 200,
            'autoFocus' => true,
            'select' => 'js:function(evt, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
            'change' => 'js:function(evt, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
        ),
        'htmlOptions' => array(
            'class' => 'required',
            'size' => '25',
        ),
    ));
    ?>

    </div>

    <div class="rowcol">
        <?php echo CHtml::label('From','for-crm-search'); ?>
        <?php echo CHtml::textField('from_time',$model['from_time'],['class' => 'date_input']); ?>
    </div>

    <div class="rowcol">
        <?php echo CHtml::label('To','for-crm-search'); ?>
        <?php echo CHtml::textField('to_time',$model['to_time'],['class' => 'date_input']); ?>
        <?php echo CHtml::button($this->t('Go'),array('onclick' => 'crm_op_search_send();')); ?>
    </div>

</div>


<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'crm-search-report-grid',
    'cssFile' => false,
    'dataProvider'=>$model['crm_search_provider'],
    'columns'=>array(
        'id',
        'name',
        'created',
        'closed',
        'processed'
    ),
)); ?>


<?php $this->endWidget(); ?>


