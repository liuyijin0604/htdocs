<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
				'id'=>'import-charge-code-form',
				'enableClientValidation'=>true,
				'clientOptions'=>array(
						'validateOnSubmit'=>true,
				),
		));
		?>
 <div class="rowcol">
				<?php echo $form->errorSummary($model); ?>
				<label class="required" for="Importchargecode_owner_id" aria-required="true">Customer <span class="required" aria-required="true">*</span></label>
				<?php echo $form->hiddenField($model,'org_id');
				$acname1 = empty($_GET["tabid"])? 'imchgcode_owner_ac' : $_GET["tabid"].'imchgcode_owner_ac';
				$ownername = empty($model->owner) ? '' : $model->owner->name;
				$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
						'name' => $acname1,
						'sourceUrl' => array('org/clientSuggest'),
						'value' => $ownername,
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

		<div class="row">
				<div class="row rowcol rowcol-left">
						<?php echo CHtml::label('Select Couriers','forimchgcode'); ?>
						<div class="row">
								<?php
								$allOrgRates = OrgRate::model()->findAll("type = 5 and zone_id > 0 AND id!=44 AND json_value(meta,'$.invisible') is null");
								$defSelected = $model->couriersObj;
//                if ( empty($defSelected) ) {
//                    foreach ($allOrgRates as $rate) {
//                        $defSelected[] = $rate['id'];
//                    }
//                }
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
		<div class="row">
				<div class="row rowcol rowcol-left">
						 <?php echo CHtml::label('Select Charge Weight Method','charge_wt'); ?>
						 <?php  echo $form->radioButtonList($model,'charge_wt',ImportChargeCode::$charge_weight,array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp'));?>
				</div>
		</div>
			<div class="row">
				 <div class="row rowcol rowcol-left">
						 <?php echo CHtml::label('Cubic Rate Factor','cubic_rate_factor'); ?>
						 <?php  echo CHtml::radioButtonList('mdata[cubic_rate_factor]', empty($model->mdata['cubic_rate_factor'])?0:$model->mdata['cubic_rate_factor'],ImportChargeCode::$cubic_factors,array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp'));?>
				</div>
			</div>
				
			 <div class="row">
				<div class="row rowcol rowcol-left">
						 <?php echo CHtml::label('Select status mode(default Manifiest)','status_mode'); ?>
						 <?php  echo $form->radioButtonList($model,'status_mode', ImportChargeCode::$status_mode_types,array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp'));?>
			 </div>
		 </div>
		<div class="row">
				 <div class="row rowcol rowcol-left">
						 <?php echo CHtml::label('Select ignore sac','status_mode'); ?>
						 <?php  echo $form->radioButtonList($model,'ignore_sac', ImportChargeCode::$ignore_sac_types,array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp'));?>
				</div>
		</div>
		<div class="row">
				 <div class="row rowcol rowcol-left">
						 <?php echo CHtml::label('Select Allow Remote','status_mode'); ?>
						 <?php  echo CHtml::radioButtonList('mdata[can_remote]', empty($model->mdata['can_remote'])?0:$model->mdata['can_remote'], [0=>'Not Allow Remote',1=>'Allow Remote'],array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp'));?>
				</div>
		</div>
			<div class="row">
				 <div class="row rowcol rowcol-left">
						 <?php echo CHtml::label('Direct to Courier','direct_courier'); ?>
						 <?php  echo CHtml::radioButtonList('mdata[direct_courier]', empty($model->mdata['direct_courier'])?0:$model->mdata['direct_courier'],ImportChargeCode::$direct_courier_types,array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp'));?>
				</div>
			</div>
			<div class="row">
				 <div class="row rowcol rowcol-left">
						 <?php echo CHtml::label('Custom Service Only','no_courier'); ?>
						 <?php  echo CHtml::radioButtonList('mdata[no_courier]', empty($model->mdata['no_courier'])?0:$model->mdata['no_courier'],ImportChargeCode::$custom_service_only,array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp'));?>
						  <?php  echo CHtml::radioButtonList('mdata[no_calculate_invoice]', empty($model->mdata['no_calculate_invoice'])?0:$model->mdata['no_calculate_invoice'],ImportChargeCode::$no_calculate_invoice,array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp'));?>
				</div>
			</div>
			<div class="row">
				 <div class="row rowcol rowcol-left">
						 <?php echo CHtml::label('eParcel Single Item Only','eparcel_single'); ?>
						 <?php  echo CHtml::radioButtonList('mdata[eparcel_single]', @$model->mdata['eparcel_single'], [0 => 'Allow Multiple', 1 => 'Single Only'], array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp'));?>
				</div>
			</div>
			
			<div class="row">
				<div class="row rowcol rowcol-left">
						<?php echo CHtml::label('Service Types','selected_rates'); ?>
						<div class="row">
								<?php
								$SelectedService = empty($model->mdata['selected_service'])?[]:$model->mdata['selected_service'];
								echo CHtml::checkBoxList('mdata[selected_service]',$SelectedService, ImportChargeCode::$service_types,array(
										'template'=>'{input}{label}',
										'separator'=>'',
										'labelOptions'=>array(
												'style'=> 'padding-right:12px;min-width: 60px;float: left;'),
										'style'=>'float:left;',) );
								echo CHtml::checkBox('mdata[cm_address]',!isset($model->mdata['cm_address'])?0:$model->mdata['cm_address'],array("style"=>"padding-right:12px;min-width: 60px;float: left;")),'<label style="padding-right:12px;min-width: 60px;float: left;">Check And Modify Address</label>';
								?>


						</div>
				</div>
			</div>
			<div class="row">
				<div class="row rowcol rowcol-left divBorder">
						<?php echo CHtml::label('General Surcharge','generate_surcharge'); ?>
						<div class="row">
							<div class="row">
								<div class="row rowcol rowcol-left">

									<?php echo CHtml::label('express ','express'); ?>
									<?php echo CHtml::textField('mdata[truck_delivery][express]',isset($model->mdata['truck_delivery']['express'])?$model->mdata['truck_delivery']['express']:0,array('rows'=>5, 'cols' => 60))?>times

									<?php echo CHtml::label('Extra express','Extra-express'); ?>
									<?php echo CHtml::textField('mdata[truck_delivery][extra_express]',isset($model->mdata['truck_delivery']['extra_express'])?$model->mdata['truck_delivery']['extra_express']:0,array('rows'=>5, 'cols' => 60))?>times

								</div>
								<div class="row rowcol rowcol-left">
									<?php echo CHtml::label('Extra Distance','Extra_Distance'); ?>
									<?php echo CHtml::textField('mdata[truck_delivery][extra_distance]',isset($model->mdata['truck_delivery']['extra_distance'])?$model->mdata['truck_delivery']['extra_distance']:0,array('style'=>'width:100px'))?>KM
									<?php echo CHtml::label('Extra Distance Fee','Extra_Distance_Fee'); ?>
									<?php echo CHtml::textField('mdata[truck_delivery][extra_distance_fee]',isset($model->mdata['truck_delivery']['extra_distance_fee'])?$model->mdata['truck_delivery']['extra_distance_fee']:0,array('style'=>'width:100px'))?>AUD/
									<?php echo CHtml::textField('mdata[truck_delivery][extra_distance_limit]',isset($model->mdata['truck_delivery']['extra_distance_limit'])?$model->mdata['truck_delivery']['extra_distance_limit']:0,array('style'=>'width:100px'))?>KM
								</div>

								<div class="row rowcol rowcol-left">
									<?php echo CHtml::label('Extra Width','Extra_Width'); ?>
									<?php echo CHtml::textField('mdata[truck_delivery][extra_width]',isset($model->mdata['truck_delivery']['extra_width'])?$model->mdata['truck_delivery']['extra_width']:0,array('rows'=>5, 'cols' => 60))?>CM

									<?php echo CHtml::label('Extra Width Fee','Extra_Width_Fee'); ?>
									<?php echo CHtml::textField('mdata[truck_delivery][extra_width_fee]',isset($model->mdata['truck_delivery']['extra_width_fee'])?$model->mdata['truck_delivery']['extra_width_fee']:0,array('rows'=>5, 'cols' => 60))?>AUD/CBM
								</div>
							</div>

						</div>
				</div>
			</div>
			</br>

			<div class='row' style="margin-top:50px;">
                <div class='row rowcol'>
                    <?php
                    $setZoneMapLink = '<a class="jqm_link" data-win-class="XXL" href="org/chgsurcharge/'.$model->id.'.app"><div style="background-position:-16px 0" class="icon"></div>'.$this->t('Import Chargecode Surcharge').'</a> &nbsp;';
                        echo $setZoneMapLink;
                    ?>
                </div>
            </div>
		
		<div class="row" style="margin-top:100px;">
				<?php echo $form->labelEx($model,'note'); ?>
				<?php echo $form->textArea($model,'note',array('rows'=>5, 'cols' => 60)); ?>
		</div>

		<div class="row" style="margin-top:100px;">
				<?php echo $form->labelEx($model,'description'); ?>
				<?php echo $form->textArea($model,'description',array('rows'=>5, 'cols' => 60)); ?>
		</div>

		<div class="row">
				<?php echo CHtml::label('Invoice Rate:','forinvoice_rate'); ?>
				<?php
				if ( !$model->isNewRecord ) {
						$setZoneMapLink = '<a class="jqm_link" data-win-class="XL" href="org/chgcodezonemap/'.$model->id.'.app"><div style="background-position:-16px 0" class="icon"></div>'.$this->t('Import Zone Map').'</a> &nbsp;';
						echo $setZoneMapLink;
						$setFlexRateLink = '<a class="jqm_link" data-win-class="XL" href="import/pcarate/' . $model->id . '.app"><div style="background-position:-16px 0" class="icon"></div>' . $this->t('Flex Rate By Zone') . '</a> &nbsp;';
						echo $setFlexRateLink;

						$setRemoetChargeRateLink = '<a class="jqm_link" data-win-class="XL" href="org/chgcodeRemoteChargeRate/' . $model->id . '.app"><div style="background-position:-16px 0" class="icon"></div>' . $this->t('Remote Charge Rate') . '</a>';
						echo $setRemoetChargeRateLink;
				}
				?>
		</div>


		<div class="row imex">
				<?php echo $form->radioButtonList($model,'status', Chargecode::$switch, array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp')); ?>
		</div>


		<div class="row buttons">
				<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
		</div>

		<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
		$(function(){
				var tab = $('#<?=$_GET["tabid"];?>');
				var panel = tab.data('panel');

		});
</script>