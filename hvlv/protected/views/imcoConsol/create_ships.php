<h1><?=$this->t('Create Import Consol');?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol-form',
	'enableAjaxValidation'=>false,
));
$model->pol = 'CNSHA';
$model->pod = 'AUSYD';
?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<?php echo $form->errorSummary($model); ?>
	
	<div class="row rowcol">
		<?php echo $form->labelEx($model,'dpt_id'); ?>
		<?php echo $form->dropDownList($model,'dpt_id',Org::dptList(), array('empty' => 'Select One')); ?>
	</div>
		<div class="row rowcol">
			<?php 
			$model->service=10;
			echo $form->labelEx($model,'service'); ?>
			<?php echo $form->dropDownList($model,'service', ImcoConsol::$services);?>
		</div>

		<div class="row rowcol">
			<?php 
			echo CHTML::label('FAK<span class="required">*</span>','FAK<span class="required">*</span>'); 
			echo CHTML::dropDownList('ImcoConsol[mdata][is_fak]','0',Consol::$fakService);
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
		<?php echo $form->dropDownList($model,'pol', AppHelper::setting2List('pols')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'pod'); ?>
		<?php echo $form->dropDownList($model,'pod', AppHelper::setting2List('pods')); ?>
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
		<?php echo CHTML::label('Detention Date','Detention Date',['style'=>'display:none']); ?>
		<?php echo CHTML::textField('ImcoConsol[mdata][detention_date]','', array('size' => 12, 'id' => 'detention_date_'.$_GET["tabid"],'class' => 'date_input','style'=>'display:none')); ?>
	</div>

	   <div class="row">
		<div class="row rowcol rowcol-left">
			<?php echo CHtml::label('Service Types','selected_rates'); ?>
			<div class="row">
				<?php
			   echo CHtml::checkBoxList('selected_service','', array_slice(ImportChargeCode::$service_types,3,null, true),array(
					'template'=>'{input}{label}',
					'separator'=>'',
					'labelOptions'=>array(
						'style'=> 'padding-right:12px;min-width: 60px;float: left;'),
					'style'=>'float:left;',) );
				?>

			</div>
		</div>
	</div>
	<div class="row">
		<div class="row rowcol rowcol-left">
			<?php echo CHtml::label('ignore depot','ignore depot'); ?>
			<div class="row">
				<?php
			   echo CHtml::checkBoxList('ignore_depot','', ['1'=>' '],array(
					'template'=>'{input}{label}',
					'separator'=>'',
					'labelOptions'=>array(
						'style'=> 'padding-right:12px;min-width: 60px;float: left;'),
					'style'=>'float:left;',) );
				?>

			</div>
		</div>
	</div>
	<div class="row">
	<label>Consignment numbers</label>
	<textarea name="hbns" rows="10" cols="40"></textarea>
</div>

<div class="row">
	<label>ZW Storage Putcode</label>
	<textarea name="putcodes" rows="10" cols="40"></textarea>
</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Create'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
		var panel=tab.data('panel');
	$('form#consol-form', tab.data('panel')).on('success', function(e, r){
		var url = tab.data('url').replace('imcoConsol/createShips','imcoConsol/update/'+r.id);
		tab.data('url', url).trigger('load');
	});
	
	tab.data('panel').off('click').on('click', 'input#chkbox_all', function(r){
		$('input.chkbox', tab.data('panel')).attr('checked', this.checked);
	});
		$('#ImcoConsol_service',panel).on('change',function(){
			if($(this).val()==10){
					   $('label[for="ImcoConsol_awb"]',panel).html('AWB No.');
				$('label[for="ImcoConsol_airline"]',panel).html('Airline');
				$('label[for="ImcoConsol_flight"]',panel).html('Flight No.');
				$('label[for="Detention Date"]',panel).hide();
				$('input[name="ImcoConsol[mdata][detention_date]"]',panel).hide();
			}else{
				$('label[for="ImcoConsol_awb"]',panel).html('Ocean Bill');
				$('label[for="ImcoConsol_airline"]',panel).html('Vessel id(IMO)');
				$('label[for="ImcoConsol_flight"]',panel).html('Voyage');
				$('label[for="Detention Date"]',panel).show();
				$('input[name="ImcoConsol[mdata][detention_date]"]',panel).show();
			 }
		});
	$('#ImcoConsol_dpt_id', tab.data('panel')).off('change').on('change', function(){
		$('#con-man-grid', tab.data('panel')).yiiGridView('update', {
			data: { 'ImcoConsol[dpt_id]': $(this).val() }
		});
	});
});
</script>