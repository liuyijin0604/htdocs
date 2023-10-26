<h1><?=$this->t('Create EDI AWB');?></h1>

<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'ediawb-consol-form',
        'enableAjaxValidation'=>false,
    ));
    ?>

    <div class="row">
    <div class="rowcol">
        <?php echo $form->errorSummary($model); ?>
        <label class="required" for="EdiAwbConsol_owner_id" aria-required="true">Customer <span class="required" aria-required="true">*</span></label>
        <?php echo $form->hiddenField($model,'owner_id');
        $acname1 = empty($_GET["tabid"])? 'ediawb_owner_ac' : $_GET["tabid"].'ediawb_owner_ac';
        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name' => $acname1,
            'sourceUrl' => array('org/exAgentSuggest'),
            'value' => '',
            'options' => array(
                'showAnim' => 'fold',
                'minLength' => 2,
                'delay' => 200,
                'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
                'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
            ),
            'htmlOptions' => array(
                'size' => '30',
            ),
        ));
        ?>
    </div>

        <div class="rowcol">
            <?php echo $form->labelEx($model,'awb'); ?>
            <?php echo $form->textField($model,'awb',array('size'=>15,'maxlength'=>50)); ?>
        </div>

        <div class="rowcol">
            <?php echo $form->labelEx($model,'pol'); ?>
            <?php echo $form->dropDownList($model,'pol', AppHelper::setting2List('pols')); ?>
        </div>

        <div class="rowcol">
            <?php echo $form->labelEx($model,'pod'); ?>
            <?php echo $form->dropDownList($model,'pod', AppHelper::setting2List('pols')); ?>
        </div>
        <div class="rowcol">
            <?php echo $form->labelEx($model,'dpt_id'); ?>
            <?php echo $form->dropDownList($model,'dpt_id',Org::dptList(), array('empty' => 'Select One')); ?>
        </div>

    </div>

    <div class="row">
        <div class="row rowcol" style="margin-top: 15px;font-weight: bold;">
          <span>Leg One</span>
        </div>
    <div class="row rowcol">
        <?php echo $form->labelEx($model,'airline'); ?>
        <?php echo $form->textField($model,'airline',array('size'=>15,'maxlength'=>50)); ?>
    </div>

    <div class="row rowcol">
        <?php echo $form->labelEx($model,'flight'); ?>
        <?php echo $form->textField($model,'flight',array('size'=>15,'maxlength'=>50)); ?>
    </div>

    <div class="row rowcol">
        <?php echo $form->labelEx($model,'etd'); ?>
        <?php echo $form->textField($model,'etd', array('size' => 12, 'id' => 'etd_'.$_GET["tabid"],'class' => 'date_input')); ?>
    </div>

    <div class="row rowcol">
        <?php echo $form->labelEx($model,'eta'); ?>
        <?php echo $form->textField($model,'eta', array('size' => 12, 'id' => 'eta_'.$_GET["tabid"],'class' => 'date_input')); ?>
    </div>
</div>

    <div class="row">
        <div class="row rowcol" style="font-weight: bold;">
            <span>Leg Two</span>
        </div>
        <div class="row rowcol">
            <?php echo CHtml::textField('airline','',array('size'=>15,'maxlength'=>50)); ?>
        </div>

        <div class="row rowcol">
            <?php echo  CHtml::textField('flight','',array('size'=>15,'maxlength'=>50)); ?>
        </div>

        <div class="row rowcol">
            <?php echo CHtml::textField('etd2','', array('size' => 12, 'id' => 'etd2_'.$_GET["tabid"],'class' => 'date_input')); ?>
        </div>

        <div class="row rowcol">
            <?php echo CHtml::textField('eta2','', array('size' => 12, 'id' => 'eta2_'.$_GET["tabid"],'class' => 'date_input')); ?>
        </div>
    </div>

    <div class="row">

        <?php echo CHtml::label('Commodity','for_edi_awb_new_goods') ?>
        <div style="margin-top: 10px;"></div>
        <?php
        echo CHtml::checkBoxList('selected_goods',array(),$goods,array(
        'template'=>'{input}{label}',
        'separator'=>'',
        'labelOptions'=>array(
        'style'=> 'padding-right:12px;min-width: 60px;float: left;'),
        'style'=>'float:left;',) );
        ?>
    </div>

    <div class="row"style="margin-top: 20px;">
        <?php echo CHtml::label('Notes','for_edi_awb_new_notes') ?>
        <?php echo CHtml::textArea('notes', '', array('rows'=>4, 'cols' => 60)); ?>
    </div>

    <div class="row buttons" style="margin-top: 20px;">
        <?php echo CHtml::submitButton('Create'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        $('form#ediawb-consol-form', tab.data('panel')).on('success', function(e, r){
            var url = tab.data('url').replace('edi/create','edi/update/'+r.id);
            tab.data('url', url).trigger('load');
        });
    });
</script>