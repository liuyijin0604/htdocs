<h1><?=$this->t('Import Parcel Third Party Label Info');?></h1>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'iptp-label-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
		<div class="row rowcol rowcol-left">
					<?php echo CHtml::label('Select Couriers','forimchgcode'); ?>
					<div class="row">
							<?php
							$allOrgRates = OrgRate::model()->findAll("type = 5 and zone_id > 0 AND id!=44 AND json_value(meta,'$.invisible') is null and code like '%plt%' and code like '%toll%'");
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
								echo CHtml::radioButtonList('selected_rates',0,$tempList,array(
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

	<div class="row" style="margin-top: 20px;">
		<label for="postw-batch">Info file<a href="ims/parcel_third_party_info.xlsx" target="_blank">(get Packages Template)</a></label>
		<input type="file" name="info_file" id="info_file" />
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
	$('form#iptp-label-form', panel).data('custom_success', function(r){
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