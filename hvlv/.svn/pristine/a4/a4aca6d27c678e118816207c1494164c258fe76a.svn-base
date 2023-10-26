<div class="form">
<div style="right: 20px;position: absolute;">
<a href="<?=$this->createUrl('wmsTask/print', array('id' => $model->id, 't' => 'co_label'));?>" target="_blank"><div style="background-position:-128px -576px" class="icon"></div> Courier Label</a>
<a href="<?=$this->createUrl('wmsTask/switchType', array('id' => $model->id, 'type' => 2110));?>" class="ajax_link type_switch"><div style="background-position:-16px -480px" class="icon"></div> Switch to Pickup</a>
<?php if (empty($model->mdata['return'])) { ?>
<a href="<?=$this->createUrl('wmsTask/return', array('id' => $model->id));?>" class="jqm_link"><div style="background-position:-160px -32px" class="icon"></div> Return</a>
<?php } ?>
</div>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'wms-task212-form',
	'enableAjaxValidation'=>false,
));
$cnee = new Addr;
?>
  <div class="row">
    <?php echo $form->labelEx($model, 'Ref - Courier'); ?>
    <?php echo CHtml::textField('WmsTask[ref]', @$model->ref, array('size'=>60)); ?>
  </div>
    <div class="row">
        <?php echo $form->labelEx($cnee, 'company'); ?>
        <?php echo CHtml::textField('mdata[cnee][company]', @$model->mdata['cnee']['company'], array('size'=>60)); ?>
    </div>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($cnee, 'name', array('required'=>'required')); ?>
		<?php echo CHtml::textField('mdata[cnee][name]', @$model->mdata['cnee']['name'], array('size'=>35, 'required'=>'required')); ?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($cnee, 'tel', array('required'=>'required')); ?>
		<?php echo CHtml::textField('mdata[cnee][tel]', @$model->mdata['cnee']['tel'], array('required'=>'required')); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($cnee, 'address', array('required'=>'required')); ?>
		<?php echo CHtml::textField('mdata[cnee][address]', @$model->mdata['cnee']['address'], array('size'=>60, 'required'=>'required')); ?>
	</div>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($cnee, 'suburb', array('required'=>'required')); ?>
		<?php echo CHtml::textField('mdata[cnee][suburb]', @$model->mdata['cnee']['suburb'], array('required'=>'required')); ?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($cnee, 'city', array('required'=>'required')); ?>
		<?php echo CHtml::textField('mdata[cnee][city]', @$model->mdata['cnee']['city'], array('required'=>'required')); ?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($cnee, 'state', array('required'=>'required')); ?>
		<?php echo CHtml::textField('mdata[cnee][state]', @$model->mdata['cnee']['state'], array('required'=>'required')); ?>
	</div>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($cnee, 'postcode', array('required'=>'required')); ?>
		<?php echo CHtml::textField('mdata[cnee][postcode]', @$model->mdata['cnee']['postcode'], array('required'=>'required')); ?>
	</div>
	<div class="row rowcol">
		<?php echo $form->labelEx($cnee, 'country', array('required'=>'required')); ?>
		<?php echo CHtml::dropDownList('mdata[cnee][country]', @$model->mdata['cnee']['country'], in_array($model->job->org_id, array_merge([Org::ORGID_3PL_IGEA, Org::ORGID_3PL_SMART_HUB], Org::$easyships)) ? Unloco::$countries : WmsTask::$countries, array('required'=>'required', 'empty' => 'Select One')); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($cnee, 'email'); ?>
		<?php echo CHtml::textField('mdata[cnee][email]', @$model->mdata['cnee']['email'], array('size'=>60)); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($cnee, 'Client Tracking No'); ?>
		<?php echo CHtml::textField('mdata[trackingno]', @$model->mdata['trackingno'], array('size'=>60)); ?>
	</div>
	<div class="row">
		<?php echo 'Auto Choose Courier '.CHtml::checkbox('autochoose', 0);?>
	</div>
	<div class="row">
		<label>Carrier</label>
		<?php
		// $ccs = [114 => 'PCA Express', 101 => 'AusPost', 115 => 'FastWay', 858 => 'Startrack', 976 => 'TNT', Org::ORGID_COURIER_SENDLE => 'Sendle'];
		$ccs = [
			0 => 'Default',
			114 => 'TLA Express',
			3752 => 'My Toll',
			//Org::ORGID_COURIER_AUPOST_EXPRESS =>'AP Express'
		];
		if($model->dpt_id == Org::PCAE_DEPARTMENT_SYDNEY || $model->dpt_id == Org::PCAE_DEPARTMENT_MELBOURNE){
			$ccs[101]='AusPost';
			$ccs[115]='FastWay';
			$ccs[976]='TNT';
			$ccs[Org::ORGID_COURIER_SF]='SF';
			$ccs[3702]='Allied';
			$ccs[3079]='UBI TOLL';
			$ccs[3994]='UBI AP';
		}

		if($model->dpt_id == Org::PCAE_DEPARTMENT_SYDNEY){
		 	$ccs[Org::ORGID_COURIER_AUPOST_EXPRESS] = 'AP Express';
			$ccs[Org::ORGID_COURIER_BORDER] = 'Border';
		}
		
		
		if(empty($model->mdata['courier'])) $model->mdata['courier'] = 0;
		foreach($ccs as $k=>$v){
			echo '<label class="radio_label"><input type="radio" name="mdata[courier]" value="'.$k.'" '.($k==$model->mdata['courier']? 'checked' : '').'/> '.$v.'</label> &nbsp; ';
		}
		?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Save')); ?>
	</div>
  
<?php $this->endWidget(); ?>
<br />
<?php if(!empty($model->mdata['shipment_id'])):?>
<div class="row rowcol">
<h3>Shipment</h3>
<?php 
    foreach ($model->mdata['shipment_id'] as $sid){
       $shipment=Shipment::model()->find('id=:id',array(':id'=>$sid)); 
       $pm='imParcel';
       $title=$sid;
       if(!empty($shipment)){
             $title=empty($shipment->ref)?$shipment->hbn:$shipment->ref;
           if($shipment->type==20){
                  $pm='exParcel';
             }
         echo "<div><a class='tab_link' href='".$this->createUrl("$pm/update",array('id'=>$sid))."' title='".$title."'>".$title."</a>&nbsp;&nbsp;&nbsp;<a class='tab_link' href='".$this->createUrl("$pm/update",array('id'=>$sid,'tab'=>'gatepass'))."' title='".$title."'>Gatepass</a></div>";
     
    }

  }
?>
</div>
<?php endif;?>
<?php if(!empty($model->mdata['cancel_shipment_id'])):?>
<div class="row rowcol" style="position:relative; left: 100px">
<h3>Cancel Shipment</h3>
<?php 
    foreach ($model->mdata['cancel_shipment_id'] as $sid){
       $shipment=Shipment::model()->find('id=:id',array(':id'=>$sid)); 
       $pm='imParcel';
       $title=$sid;
       if(!empty($shipment)){
             $title=empty($shipment->ref)?$shipment->hbn:$shipment->ref;
           if($shipment->type==20){
                  $pm='exParcel';
             }
         echo "<div><a class='tab_link' href='".$this->createUrl("$pm/update",array('id'=>$sid))."' title='".$title."'>".$title."</a></div>";
     
    }

  }
?>
</div>
<?php endif;?>
</div><!-- form -->
<?php if (in_array($model->job->org_id, [Org::ORGID_3PL_SUNNYA])) { ?>
<div class="row rowcol">
	<?php echo Chtml::label('板数: ' . @$model->mainTask->mdata['plt_sunnya'], 'plts'); ?>
</div>
<?php } ?>
<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	$('a.type_switch').on('click', function(){
		setTimeout(function() {
			tab.trigger('reload_tab');
			$('#wmstask-tabs :nth-child(4) a', panel).html('Pickup');
		}, 2e2);
	});

	function country() {
		if ($('#mdata_cnee_country', panel).val() === 'CN') {
			$('#mdata_cnee_city', panel).attr('required', true);
			$('#mdata_cnee_city', panel).attr('aria-required', true);
			$('#mdata_cnee_city', panel).addClass('required');
			$('#mdata_cnee_city', panel).prev().find('span').html('*');
		} else {
			$('#mdata_cnee_city', panel).removeAttr('required');
			$('#mdata_cnee_city', panel).removeAttr('aria-required');
			$('#mdata_cnee_city', panel).removeClass('required');
			$('#mdata_cnee_city', panel).prev().find('span').html('');
		}
	}

	setTimeout(country, 1);

	$('#mdata_cnee_country', panel).on('change', function() {
		country();
	});
});
</script>