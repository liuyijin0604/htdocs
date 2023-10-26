<div style="position: absolute; right: 20px;">
    <?php 
      $file=$model->fwd_id."_".preg_replace('/[^\d]/','',substr($model->created,0,10));
    ?>
       <a class="tab_link" href="<?=$this->createUrl('exParcel/uimage',array('file'=>$file));?>" title="<?=$file?>_image"><div class="icon" style="background-position:-16px 0"></div>Images</a> &nbsp; 
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div class="icon" style="background-position:-192px -80px"></div> Actions</a>
<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
<?php
	echo '<li><a href="'.$this->createUrl('pickupList/export', array('id'=>$model->id)).'" target="_blank">'.$this->t('Export').'</a></li>';
	if(!$model->isBilled()){
		echo '<li><a class="jqm_link" href="'.$this->createURL('pickupList/addConnote', array('id' => $model->id)).'">'.$this->t('Add Connote').'</a></li>';
	}
	echo '<li><a class="jqm_link" href="'.$this->createURL('pickupList/manageConnote', array('id' => $model->id)).'">'.$this->t('Manage Connote').'</a></li>';
	if(!empty($model->mdata['cc_status']) && $model->mdata['cc_status'] == 10){
		echo '<li><a class="ajax_link" href="'.$this->createURL('pickupList/received', array('id' => $model->id)).'">'.$this->t('Received').'</a></li>';
	}
?>
	</ul>
</div>
</div>

<div class="form">
<div class="row">
<div class="row rowcol" style="padding-right:30px">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'shipment-receipt-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>
	
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'dpt_id'); ?>
		<?php echo $form->dropDownList($model, 'dpt_id', Org::dptList(), array('empty' => 'Select One'), array('class' => 'required')); ?>
	</div>

	<div class="row rowco rowleft">
		<?php echo $form->labelEx($model,'fwd_id'); ?>
		<?php echo empty($model->fwd_id)? '' : $model->owner->name;	?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('Packages','pkgs');?>
		<?php echo $model->countLines(); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('Weight','weight');?>
		<?php echo $model->totWeight(); ?>
	</div>

	<div class="row">
		<?php echo CHtml::label('Receipt #','rnos');?>
		<?php echo CHtml::textArea('mdata[rnos]', @$model->mdata['rnos'], array('rows'=>2, 'cols' => '40')); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('Total Packs','pkgs');?>
		<?php echo CHtml::textField('mdata[pkgs]', @$model->mdata['pkgs'], array('size'=>5)); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('Total Weight','weight');?>
		<?php echo CHtml::textField('mdata[tweight]', @$model->mdata['tweight'], array('size'=>5)); ?>KG
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('Total','total');?>
		$<?php echo CHtml::textField('mdata[total]', @$model->mdata['total'], array('size'=>5)); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('Payment Type','paytype');?>
		<?php echo CHtml::dropDownList('mdata[ptype]', @$model->mdata['ptype'], ['10' => 'Cash', 20 => 'Credit', 30 => 'EFT', 40 => 'Cheque'], array('empty' => 'Select One')); ?>
	</div>
	
	<div class="row">
		<?php echo CHtml::label('Service Grade','sergra');?>
		<?php echo CHtml::radioButtonList('mdata[sergra]', empty($model->mdata['sergra'])? 0 : 1, $this->t(array(0=>'Standard', 1=>'Premium Only')), array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp')); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>
</div>

	<div class="row rowcol">
		<h3>Check connotes</h3>
		<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'scan-form',
			'enableAjaxValidation'=>false,
		));
		?>
		<p>Barcode: <input id="scan" type="text" size="30" name="barcode" /></p>
		<?php
		echo $form->hiddenField($model, 'id');
		$this->endWidget(); ?>
		<div id="result" style="margin-top: 10px; border: 1px solid;padding:20px; font-weight: bold; font-size: 20px; max-height:120px; overflow:auto;">
		</div>
		<audio id="sound" src=""></audio>
	</div>
</div>
</div><!-- form -->
<div style="clear:both;"></div>
<?php
$parcel = new ExParcel('search');
if(isset($_GET['ExParcel'])){
	$parcel->unsetAttributes();
	$parcel->attributes=$_GET['ExParcel'];
}
$mids = $model->getFids();
$parcel->mids = empty($mids)? [-1] : $mids;

//map gd
$dp = $parcel->search();
if(strtotime($model->created) > (time() - 345600)){
	foreach($dp->data as $s){
		if(($s->bwf & 24) > 0){
			$s->cleanItems();
			if($s->mapGoods()) $s->save();
		}
	}
}

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET["tabid"].'_exparcel-grid',
	'cssFile' => false,
	'dataProvider'=>$dp,
	'filter'=>$parcel,
	'columns'=>array(
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("exParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',),
		array('header' => 'Slip', 'value' => '$data->hasConnote('.$model->id.')',),
		array('header'=>'Finish?','type'=>'html',
                 'value'=>'($data->get_icon()==1)?(CHtml::tag( "div",array("class"=>"icon"))):(($data->get_icon()==2)?CHtml::tag( "div",array("class"=>"icon", "style"=>"background-position:-96px 0px")):(($data->get_icon()==3)?CHtml::tag( "div",array("class"=>"icon", "style"=>"background-position:-16px 0px")):CHtml::tag( "div",array("class"=>"icon", "style"=>"background-position:-208px -224px"))) )  ',),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('ExParcel[status]', $parcel->status, $this->t(ExParcel::$states), array('prompt'=>$this->t('All'))),),
		'weight',
		array('name' => 'cnor_name', 'value' => 'empty($data->cnor)? "" : $data->cnor->name',),
		array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
		array('header' => 'client_entry', 'value' => '$data->getClientEntry()'),
		array('name' => 'bwf', 'header' => 'Warnings', 'type' => 'raw', 'value' => '$data->getWarnings()','filter'=>CHtml::dropDownList('ExParcel[bwf]', $parcel->bwf, $this->t(ExParcel::$bwfs), array('prompt'=>$this->t('All'))),),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update} {delete}',
			'buttons'=>array(
				'update' => array(
					'imageUrl'=>false,
					'url' => 'Yii::app()->createURL("exParcel/update", array("id" => $data->id))',
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->hbn'),
				),
				'delete' => array(
					'imageUrl'=>false,
					'url' => 'Yii::app()->createURL("pickupList/remove", array("id" => '.$model->id.', "pid" => $data->id))',
					'visible'=>'$data->agent_id != '.$model->fwd_id,
					'options' => array('class' => 'grid_delete_btn', 'label'=>$this->t('Remove'), 'title' => '$data->hbn'),
				),
			),
		),
	),
));
if(!empty($model->mdata['sig'])){
	echo '<p><img src="'.$model->mdata['sig'].'" /></p>';
}
?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	var rgto;

	$('form#scan-form', panel).data('custom_success', function(r){
		$('#result', panel).prepend($('<p>'+r.msg+'</p>').css('color', r.color).fadeIn());
		$('#sound', panel).attr('src', 'site/voice/'+r.sound+'.mp3');
		$('#sound', panel)[0].play();
		clearTimeout(rgto);
		rgto = setTimeout(function(){ tab.trigger('onOpen'); }, 5e3);
		return true;
	}).on('submit', function(){
		$('input#scan', panel).focus();
	});

	$('input#scan', panel).on('focus', function(){
		$(this).select();
	});

	tab.on('onOpen', function(){
		$('#<?=$_GET["tabid"];?>_exparcel-grid', panel).yiiGridView('update');
	});
});
</script>