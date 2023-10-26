<div style="position:absolute">
<p>Shipments: <b><?=$model->totShipments();?></b> &nbsp; Total Weight: <b><?=$model->totWeight();?>KG</b> &nbsp <?php
if(!empty($model->mdata['aiz'])) echo 'CRN: <b style="font-size:16px;color: #080;">', $model->mdata['aiz'], '</b>';?>
</p></div>
<div style="text-align:right">
<?php if($model->status == 10):?>
<a class="jqm_link" data-win-class="jqmWindow L" href="<?=$this->createUrl('exacConsol/import',['id' => $model->id]);?>"><div style="background-position:-288px -672px" class="icon"></div> Load Manifest</a> &nbsp;
<?php endif; ?>
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-48px -688px" class="icon"></div> Export</a>
</div>

<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
	<li><a href="<?=$this->createUrl('exacConsol/export', array('id' => $model->id));?>" target="_blank">Manifest</a></li>
	<li><a href="<?=$this->createUrl('exacConsol/download', array('id'=>$model->id, 'type'=> 'label'));?>" target="_blank">Connote Labels</a></li>
	<li><a href="<?=$this->createUrl('exacConsol/download', array('id'=>$model->id, 'type'=> 'label-a4'));?>" target="_blank">Connote Labels A4</a></li>
	</ul>
</div>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol-form',
	'enableAjaxValidation'=>false,
));
?>
	<?php echo $form->errorSummary($model); ?>

	<div class="row">
	<label class="required" for="ExacConsol_owner_id" aria-required="true">Shipper <span class="required" aria-required="true">*</span></label>
		<?php echo $form->hiddenField($model,'owner_id');
			$acname1 = empty($_GET["tabid"])? 'owner_ac' : $_GET["tabid"].'_owner_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname1,
				'sourceUrl' => array('org/exAgentSuggest'),
				'value' => empty($model->owner_id)? '' : $model->owner->name,
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

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'awb'); ?>
		<?php echo $form->textField($model,'awb',array('size'=>15,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'airline'); ?>
		<?php echo $form->textField($model,'airline',array('size'=>15,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'flight'); ?>
		<?php echo $form->textField($model,'flight',array('size'=>15,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'pol'); ?>
		<?php echo $form->dropDownList($model,'pol', AppHelper::setting2List('pods')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'pod'); ?>
		<?php echo $form->dropDownList($model,'pod', AppHelper::setting2List('pols')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'etd'); ?>
		<?php echo $form->textField($model,'etd', array('size' => 12, 'id' => 'etd_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'eta'); ?>
		<?php echo $form->textField($model,'eta', array('size' => 12, 'id' => 'eta_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'exrate'); ?>
		1AUD = <?php echo $form->textField($model,'exrate',array('size'=>10,'maxlength'=>15)); ?> <a href="http://www.customs.gov.au/site/page4277.asp" target="_blank"><div class="icon" style="background-position:-96px -384px"></div></a>
		<?php echo $form->error($model,'exrate'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Save'); ?>
		<?php if($model->status == 10 && $model->totPacks() > 0){
			echo '&nbsp;', CHtml::submitButton('Confirm');
		}elseif($model->status == 20){
			echo '&nbsp;', CHtml::button('Unlock', ['class' => 'unlock']);
		}?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<?php
$parcel = new ExAfs('search');
if(isset($_GET['ExAfs'])){
	$parcel->unsetAttributes();
	$parcel->attributes=$_GET['ExAfs'];
}
$parcel->consol_id = $model->id;

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET["tabid"].'_exafs-grid',
	'cssFile' => false,
	'dataProvider'=>$parcel->search(),
	'filter'=>$parcel,
	'columns'=>array(
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("exAfs/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',),
		'ref',
		'cref',
		array('name' => 'agent_name', 'value' => 'empty($data->agent)? "" : $data->agent->shortName(2)',),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('ExAfs[status]', $parcel->status, $this->t(ExAfs::$states), array('prompt'=>$this->t('All'))),),
		'weight',
		array('name' => 'cnor_name', 'value' => 'empty($data->cnor)? "" : $data->cnor->name',),
		array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
		array('name' => 'cnee_addr', 'value' => '$data->cnee->fullAddress(array("city"))'),
		array('name' => 'bwf', 'header' => 'Warnings', 'type' => 'raw', 'value' => '$data->getWarnings()','filter'=>CHtml::dropDownList('ExAfs[bwf]', $parcel->bwf, $this->t(ExAfs::$bwfs), array('prompt'=>$this->t('All'))),),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{delete}',
			'buttons'=>array(
				'delete' => array(
					'imageUrl'=>false,
					'visible' => '$data->consol->status < 20',
					'options' => array('class' => 'grid_delete_btn', 'label'=>$this->t('Cancel')),
					'url' => 'Yii::app()->createURL("exacConsol/removeParcel", array("id" => $data->id))',
				),
			),
		),
	),
));
?>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	
	$('form#consol-form', panel).on('success', function(e, r){
		tab.load();
	});

	$(document).off('click','#<?=$_GET["tabid"];?>_exafs-grid a.grid_delete_btn');
	
	tab.off('update-exafs-grid').on('update-exafs-grid', function(){
		$('#<?=$_GET["tabid"]?>_exafs-grid', panel).yiiGridView('update');
	});
});
</script>