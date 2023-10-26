<h1><?=$this->t('Upload Fastway Manifest');?></h1>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'fastway-manifest-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => [
		'class' => 'ifrm-form',
		'target' => $_GET["tabid"].'_ifrm',
		'enctype' => 'multipart/form-data',
	]
)); ?>

    <div class="row">
        <?php echo CHtml::label('Owner','forfw'); ?>
        <?php echo CHtml::hiddenField('owner','');
        $acname = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name' => $acname,
            'sourceUrl' => array('org/ownerSuggest'),
            'value' => '',
            'options' => array(
                'showAnim' => 'fold',
                'minLength' => 2,
                'delay' => 200,
                'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
                'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
            ),
            'htmlOptions' => array(
                'class' => 'required',
                'size' => '30',
            ),
        ));
        ?>
    </div>

	<div class="row">
		<label for="manifest">Manifest - <small>.csv/.xls/.xlsx File</small></label>
		<input type="file" name="manifest" id="manifest" />
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Upload')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<iframe name="<?=$_GET["tabid"];?>_ifrm" id="<?=$_GET["tabid"];?>_ifrm" src="" width="100%" height="400" border="0" style="border:1px #ccc solid; margin-top:10px;">
</iframe>