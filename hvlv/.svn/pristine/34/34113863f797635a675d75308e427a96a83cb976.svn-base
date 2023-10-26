<h1><?=$this->t('Change Courier Label By Pallet');?></h1>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'change-label-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
		<div class="row rowcol rowcol-left">
					<?php echo CHtml::label('Select Couriers','forimchgcode'); ?>
					<div class="row">
							<?php
							$allOrgRates = OrgRate::model()->findAll("type = 5 and zone_id > 0 AND id!=44 AND json_value(meta,'$.invisible') is null");
							$defSelected = [];
							$rateList = CHtml::listData($allOrgRates,'id','name');
							$mdataList = CHtml::listData($allOrgRates,'id','mdata');
							$tempLists=[];
							$tempRateRaw=[];
							foreach ($rateList as $id=>$name){
									if(empty($mdataList[$id]['ddpt_id']))
									{
										if(!in_array($id, ImportChargeCode::$unsetKeys)||in_array($id, $defSelected))
										{
											$tempLists["AU WIDE"][$id] = $name;
										}
									}else
									{
										if(!in_array($id, ImportChargeCode::$unsetKeys)||in_array($id, $defSelected))
										{
											$tempLists[$mdataList[$id]['ddpt_id']][$id] = $name;
										}
									}
							}
							ksort($tempLists);

							foreach ($tempLists as $key => $tempList) {
								echo "<div class='row '>";
								$label = empty(Org::$importWarehouseList[$key])?$key:Org::$importWarehouseList[$key];
								echo "<h3>$label</h3>";
								echo CHtml::checkBoxList('selected_rates',$defSelected,$tempList,array(
									'template'=>'{input}{label}',
									'separator'=>'',
									'labelOptions'=>array(
											'style'=> 'padding-right:12px;min-width: 60px;float: left;'),
									'style'=>'float:left;',) );
								echo "</div>";
							}
							?>

					</div>
			</div>
		</div>

	<div class="row dpt_row">
		<?php echo CHtml::label('hbns/refs','hbns/refs'); ?>
		<?php echo CHtml::textArea('hbns','',array('cols'=>60, 'rows' => 10)); ?>
	</div>
	<div class="row dpt_row">
		<?php echo CHtml::label('pallets','pallets'); ?>
		<?php echo CHtml::textArea('pallets','',array('cols'=>60, 'rows' => 10)); ?>
	</div>

	<div class="row">
		<div class="rowcol">
			<?php echo CHtml::label('Only Check Cost','Only Check Cost'); ?>
			<?php echo CHtml::checkbox('onlyCheckCost',0); ?>
		</div>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Submit')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<div id="result"></div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('form#change-label-form', panel).data('custom_success', function(r){
		var rdiv = $('#result', panel);
		rdiv.empty();
		if(r.done == true){
			$('form#change-label-form', panel).resetForm();
			myApp.notice(r.msg, 5000);
		}else{
			rdiv.append('<h3>Errors:</h3><p class="red" style="font-weight:bold;">'+r.msg+'</p>');
		}
		if(r.warns && r.warns.length > 0){
			rdiv.append('<h3>Notices:</h3><p class="warn">'+r.warns.join('<br />')+'</p>');
		}
		$('input[type=submit]', panel).attr('disabled', false);
	});

});
</script>