
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'erp-agentreport-form',
    'enableAjaxValidation'=>false,
)); ?>

<div class="row" style="margin-top: 50px;">
    <div class="rowcol">
        <?php echo CHtml::label('Agent','to_id'),
        $form->hiddenField($model['whdata_list'],'to_id', array('data-ov' => $model['whdata_list']->to_id));
        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name' => 'to_id',
            'sourceUrl' => array('ErpProductRoute/fromSuggest'),
            'value' => empty($model['whdata_list']->to_id)? '' : $model['whdata_list']->agent->name,
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
        <?php echo CHtml::label('To','foragent'); ?>
        <?php echo CHtml::textField('agent_to_date',$model['agent_end_time'],['class' => 'date_input']); ?>
        <?php echo CHtml::button($this->t('Refresh'),array('onclick' => 'agentsend();')); ?>

    </div>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'erp-agentreport-grid',
    'cssFile' => false,
    'dataProvider'=>$model['agentdata_provider'],
    'columns'=>array(
        'id',
        'name',
        'total',
        'consumed',
        'left'
    ),
)); ?>

<?php $this->endWidget(); ?>