<h2>Create Courier Label</h2>
<div class="form">
<?php
$form = $this->beginWidget('CActiveForm', array(
	'id' => 'clear_log_form',
	'enableAjaxValidation' => false,

));?>
<div class="row">
	<?php echo CHtml::label('Label Type', 'label_type'); ?>
	<?php echo CHtml::dropDownList('label_type', '', array(
		'eParcel-syd' => 'eParcel Sydney',
		'eParcel-syd-express' => 'eParcel Sydney Express',
		'eParcel-mel' => 'eParcel Melbourne',
		'eParcel-bne' => 'eParcel Brisbane',
		'eParcel-per' => 'eParcel Perth',
		'fastway-syd' => 'Fastway Sydney',
		'fastway-mel' => 'Fastway Melbourne',
		// 'startrack-syd' => 'Startrack Sydney',
		// 'startrack-mel' => 'Startrack Melbourne',
		// 'tnt-syd' => "TNT Sydney",
		// 'tnt-mel' => 'TNT Melbourne',
		// 'tnt-bne' => 'TNT Brisbane',
		'tnt-syd-top' => "TNT Sydney TOP",
		'tnt-mel-top' => 'TNT Melbourne TOP',
		'tnt-bne-top' => 'TNT Brisbane TOP',
		'tnt-per-top' => 'TNT Perth TOP',
		// 'winit-eparcel-syd' => 'Winit LMA eParcel',
		// 'winit-express-syd' => 'Winit LMA Express',
		// 'hunter' => 'Hunter Express',
		//'d2z-syd' => 'D2z Sydney',
		//'d2z-mel' => 'D2z Melbourne',
		//'d2z-bri' => 'D2z Brisbane',
		//'d2z-per' => 'D2z Perth',
		//'d2z-local' => 'D2z Local - Syd',
		'ubi-toll-syd' => 'ubi-toll-syd',
		'ubi-toll-mel' => 'ubi-toll-mel',
		'ubi-local-SYDWW' => 'Ubi-Local-Syd',
		'ubi-local-MELWW' => 'Ubi-Local-Mel',
		'ubi-local-BNE' => 'Ubi-Local-Bne',
		'ubi-local-PER' => 'Ubi-Local-Per',
		// 'gv-4port' => 'GV eParcel 4 Ports',
		// 'd2z-local-mel' => 'D2z Local - Mel',
		// 'eParcel-ecof' => 'ECOF eParcel',
		'pickup' => 'Pick Up',
		'pickup_non_ref' => 'Pick Up Non Ref',
		// 'dfe-syd' => 'DFE Sydney',
		'eiz-allied-syd'=> 'Eiz Allied Sydney',
		'eiz-allied-mel'=> 'Eiz Allied Melbourne',
		'eiz-allied-bne'=> 'Eiz Allied Brisbane',
		'eiz-allied-per'=> 'Eiz Allied Perth',

		'eiz-border-syd'=> 'Eiz Border Sydney',
		'eiz-border-mel'=> 'Eiz Border Melbourne',
		'eiz-border-bne'=> 'Eiz Border Brisbane',
		'eiz-border-per'=> 'Eiz Border Perth',

		'eiz-toll-syd' => 'Eiz Toll Sydney',
		'eiz-toll-mel' => 'Eiz Toll Melbourne',
		'eiz-toll-bne' => 'Eiz Toll Brisbane',
		'dfe-syd-top' => 'DFE Change Label',
		// 'dfe-mel-top' => 'DFE Melbourne TOP',
		// 'dfe-bne-top' => 'DFE Brisbane TOP',
		'eparcel-chukou1' => 'Chukou1 eParcel',
		'mytoll-syd'=> 'MyToll Sydney',
		'mytoll-mel'=> 'MyToll Melbourne',
		'mytoll-bne'=> 'MyToll Brisbane',
		'mytoll-per'=> 'MyToll Perth',
		'sf-syd' => 'SF syd',
		'sf-mel' => 'SF mel',
		'gv-eParcel-syd' => 'GV eParcel Sydney',
'gv-eParcel-mel' => 'GV eParcel Mel',
'gv-eParcel-bne' => 'GV eParcel Bne',
'gv-eParcel-per' => 'GV eParcel Per',
'TLD-mel' => 'TLD Mel'
	), array('prompt' => 'All')); ?>
</div>
<div class="row">
	<?php echo CHtml::label('Shipment HBN:', 'shipment_hbn'); ?>
	<?php echo CHtml::textArea('shipment_hbn', '', array('rows' => 2, 'cols' => 60)); ?>
</div>

<div class="button">
	<?php echo CHtml::submitButton('Create') ?>
</div>

<?php $this->endWidget();?>
	<fieldset style="width:60%;">
		<legend><h2>change Chargcode</h2></legend>
			<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'update-fastway',
		'htmlOptions' => ['target' => 'shipment_result_chargcode', 'class' => 'ifrm-form', 'enctype' => 'multipart/form-data'],
		'enableAjaxValidation' => false,
		'action' => $this->createUrl('imParcel/changeChargecode'),
	));?>
		<div class="row">
			<?php echo CHtml::label('Shipment Ref or HBN', 'shipment_no'); ?>
			<?php echo CHtml::textField('shipment_no', ''); ?>
		</div>
		<div class="row">
		  <?php echo CHtml::label('chargecode', 'chargecode'); ?>
			<?php echo CHtml::textField('chargecode', ''); ?>
		</div>
		<div class="row">
		  <?php echo CHtml::label('Consol No.(Not Required)', 'Consol No.(Not Required)'); ?>
			<?php echo CHtml::textField('consol_no', ''); ?>
		</div>
		<div class="row buttons">
			<?php echo CHtml::submitButton($this->t('Submit')); ?>
		</div>
	<?php $this->endWidget();?>
</fieldset>
	<iframe name="shipment_result_chargcode" id="shipment_result_chargecode" style="border:1px solid black;height:80px;width:50%;"></iframe>
</div>