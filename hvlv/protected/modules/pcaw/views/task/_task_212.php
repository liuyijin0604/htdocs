<div class="container">
<div class="form">
<?php if (!in_array($model->mainTask->type, [3060]) && $model->mainTask->status < 100) { ?>
<div style="right: 20px;text-align:right;">
<a href="<?=$this->createUrl('task/switchType', array('id' => $model->id, 'type' => 2110));?>" class="ajax-link type_switch"><div style="background-position:-16px -480px" class="icon"></div> Switch to Pickup</a>
</div>
<?php } ?>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'wms-task212-form',
	'action' => $this->createUrl('task/updateTask', array('id' => $model->id)),
	'enableAjaxValidation'=>false,
	'htmlOptions' => array(
		'enctype' => 'multipart/form-data')
));
$cnee = new Addr;
?>
		<!-- <div class="row">
				<div class="col col-md-3 col-sm-6">
						<div class="form-group">
								<?php echo $form->labelEx($model, 'Ref - Courier'); ?>
								<?php echo CHtml::textField('WmsTask[ref]', @$model->ref, array('size'=>60, 'class'=>'form-control')); ?>
								(Leave empty if no requirement)
						</div>
				</div>
				<div class="col col-md-3 col-sm-6">

					<div class="col col-sm-8 col-xs-6" style="padding-top: 20px;">
						<a href="#" class="pasting-tog"><span class="glyphicon glyphicon-copy" style="font-size:1.5em;"></span></a>
					</div>

					<div class="pasting-pane" style="margin-top: 55px;display:none; position: absolute; opacity: 0.9; z-index:9; width: 100%; padding-right:25px;">
						<textarea id="addr_paste" class="form-control" style="width:100%; min-height: 280px; font-size: 1.5em; font-weight: bold; color: #14487E; box-shadow: 5px 5px 5px #999;" placeholder="<?=$this->t('Paste address here');?>"></textarea>
					</div>
				</div>
		</div> -->
		<div class="row">
				<div class="col col-md-2 col-sm-4">
	<div class="form-group">
						<?php echo $form->labelEx($cnee, 'company'); ?>
						<?php echo CHtml::textField('mdata[cnee][company]', @$model->mdata['cnee']['company'], array('size'=>60,'class'=>'form-control')); ?>
				</div>
				</div>
				
				 <div class="col col-md-2 col-sm-4">
		<div class="form-group">
								<?php echo $form->labelEx($cnee, 'name', array('required'=>'required')); ?>
		<?php echo CHtml::textField('mdata[cnee][name]', @$model->mdata['cnee']['name'], array('size'=>35,'class'=>'form-control','required'=>'required')); ?>
					</div>
				 </div>
					 <div class="col col-md-2 col-sm-4">
			<div class="form-group">
							 <?php echo $form->labelEx($cnee, 'tel',array('required'=>'required')); ?>
		<?php echo CHtml::textField('mdata[cnee][tel]', @$model->mdata['cnee']['tel'],array('class'=>'form-control','required'=>'required')); ?>
						 </div>
						 </div>
					 </div>

	<div class="row">
						 <div class="col col-md-6 col-sm-6">
			 <div class="form-group">
		<?php echo $form->labelEx($cnee, 'address', array('required'=>'required')); ?>
		<?php echo CHtml::textField('mdata[cnee][address]', @$model->mdata['cnee']['address'], array('size'=>60,'class'=>'form-control','required'=>'required')); ?>
						 </div>
						 </div>
				</div>
			<div class="row">
					<div class="col col-md-2 col-sm-4">
		<div class="form-group">
								<?php echo $form->labelEx($cnee, 'suburb', array('required'=>'required')); ?>
		<?php echo CHtml::textField('mdata[cnee][suburb]', @$model->mdata['cnee']['suburb'],array('class'=>'form-control','required'=>'required')); ?>
					</div>
					</div>
				<div class="col col-md-2 col-sm-4">
		<div class="form-group">
								<?php echo $form->labelEx($cnee, 'city', array('required'=>'required')); ?>
		<?php echo CHtml::textField('mdata[cnee][city]', @$model->mdata['cnee']['city'],array('class'=>'form-control','required'=>'required')); ?>
					</div>
				 </div>
					 <div class="col col-md-2 col-sm-4">
		<div class="form-group">
							 <?php echo $form->labelEx($cnee, 'state', array('required'=>'required')); ?>
				 <?php echo CHtml::textField('mdata[cnee][state]', @$model->mdata['cnee']['state'],array('class'=>'form-control','required'=>'required')); ?>
					</div>
				 </div>
		</div>
		
				<div class="row">
					<div class="col col-md-2 col-sm-4">
		<div class="form-group">
							 <?php echo $form->labelEx($cnee, 'postcode', array('required'=>'required')); ?>
		<?php echo CHtml::textField('mdata[cnee][postcode]', @$model->mdata['cnee']['postcode'],array('class'=>'form-control','required'=>'required')); ?>
					</div>
					</div>
				<div class="col col-md-2 col-sm-4">
		<div class="form-group">
								<?php echo $form->labelEx($cnee, 'country', array('required'=>'required')); ?>
		<?php echo CHtml::dropDownList('mdata[cnee][country]', @$model->mdata['cnee']['country'],in_array($model->job->org_id, [Org::ORGID_3PL_IGEA]) ? Unloco::$countries : WmsTask::$countries,array('class'=>'form-control', 'empty' => 'Select One', 'required' => 'required')); ?>
					</div>
				 </div>
					 <div class="col col-md-2 col-sm-4">
		<div class="form-group">
							 <?php echo $form->labelEx($cnee, 'email'); ?>
		<?php echo CHtml::textField('mdata[cnee][email]', @$model->mdata['cnee']['email'], array('size'=>60,'class'=>'form-control')); ?>
					</div>
				 </div>
			 </div>
		<?php 
		if(!empty($model->id)):?>
				<input type="hidden" value="<?=$model->id?>" name="id">
		<?php endif;?>

		<?php if ($model->mainTask->type == 3040) { ?>
			<div class="form-group">
				<label>Carrier</label>
				<?php
				$ccs = [114 => 'PCA Express', 101 => 'AusPost', 115 => 'FastWay'];
				if(empty($model->mdata['courier'])) $model->mdata['courier'] = 114;
				foreach($ccs as $k=>$v){
					echo '<label class="radio_label"><input type="radio" name="mdata[courier]" value="'.$k.'" '.($k==$model->mdata['courier']? 'checked' : '').'/> '.$v.'</label> &nbsp; ';
				}
				?>
				<a href="<?=$this->createUrl('task/print', array('id' => $model->id, 't' => 'co_label'));?>" target="_blank"><div style="background-position:-128px -576px" class="icon"></div> Courier Label</a>
			</div>
			<?php } ?>

			 <div class="form-group">
		<?php if ($model->mainTask->status < 30 || $model->mainTask->status == 40) { ?>
		<?php echo CHtml::submitButton($this->t('Save'),array('class'=>'btn btn-primary')); ?>
		<?php } ?>
	</div>
		 </div> 
<?php $this->endWidget(); ?>
<br />

<?php if(!empty($model->mdata['shipment_id'])):?>
<h3>Tracking</h3>
<?php
	foreach ($model->mdata['shipment_id'] as $sid){
		$shipment=Shipment::model()->find('id=:id',array(':id'=>$sid)); 
		if(!empty($shipment->trans)){
			foreach($shipment->trans as $ts){
				echo $ts->infoLink().'<br />';
			}
		} else if (!empty($shipment)) {
			if ($model->mdata['shipment_courier_id'] == 101) {
				echo 'Australia Post: ' . '<a style="text-decoration: none;" target="_blank" href="https://auspost.com.au/mypost/track/#/details/' . $shipment->ref . '">' . $shipment->ref . '</a>' . '<br />';
			} else if ($model->mdata['shipment_courier_id'] == 115) {
				echo 'Fast Way: ' . '<a style="text-decoration: none;" target="_blank" href="https://www.fastway.com.au/tools/track/?l=' . $shipment->ref . '">' . $shipment->ref . '</a>' . '<br />';
			} else if ($model->mdata['shipment_courier_id'] == 114) {
				echo 'TLA: ' . '<a style="text-decoration: none;" target="_blank" href="https://toplogistics.com.au/?s=' . $shipment->hbn . '">' . $shipment->hbn . '</a>' . '<br />';
			} else if ($model->mdata['shipment_courier_id'] == 976) {
				echo 'TNT: ' . '<a style="text-decoration: none;" target="_blank" href="https://www.tnt.com/express/en_au/site/home.html">' . $shipment->ref . '</a>' . '<br />';
			} else if ($model->mdata['shipment_courier_id'] == 858) {
				echo 'Startrack: ' . '<a style="text-decoration: none;" target="_blank" href="https://msto.startrack.com.au/track-trace/?id=' . $shipment->ref . '">' . $shipment->ref . '</a>' . '<br />';
			} else if ($model->mdata['shipment_courier_id'] == Org::ORGID_COURIER_SENDLE) {
				echo 'Sendle: ' . '<a style="text-decoration: none;" target="_blank" href="https://track.sendle.com/tracking?ref=' . $shipment->ref . '">' . $shipment->ref . '</a>' . '<br />';
			} else if ($model->mdata['shipment_courier_id'] == Org::ORGID_COURIER_UBI_AP) {
				echo 'Australia Post: ' . '<a style="text-decoration: none;" target="_blank" href="https://auspost.com.au/mypost/track/#/details/' . $shipment->ref . '">' . $shipment->ref . '</a>' . '<br />';
			}
		}
	}
?>

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
	$('a.type_switch').on('success', function(){
		window.location.reload(true);
	});

	$('a.pasting-tog').on('click', function(){
		var p = $('div.pasting-pane');
		if(p.is(':visible')){
			p.fadeOut(200);
		}else{
			p.fadeIn(200);
			$('textarea#addr_paste').focus();
		}
		return false;
	});

	var apt;
	$('textarea#addr_paste').on('keyup', function(){
		clearTimeout(apt);
		var me = $(this);
		var p = encodeURIComponent(me.val().trim());
		if(p == '') return false;
		apt = setTimeout(function(){
			$.get('../../pasteAddr?p='+p, function(r){
				var e = 0;
				if(r.name == ''){
					$('#notifc').notify({message: {html: '收件人姓名无法正确读取'}, type: 'danger'}).show();
					e++;
				}
				if(r.addr == '' || r.postcode == ''){
					$('#notifc').notify({message: {html: '地址信息无法正确读取'}, type: 'danger'}).show();
					e++;
				}
				if(r.tel == ''){
					$('#notifc').notify({message: {html: '收件人电话无法正确读取'}, type: 'danger'}).show();
					e++;
				}
					
				if(e < 2){
					for(var i in r){
						if (i == 'name' && (/^[\u4e00-\u9fa5· \.]+$/.test(r[i]))) {
							$('#mdata_cnee_country').val('CN');
						}
						$('#mdata_cnee_'+i).val(r[i]);
					}
					if(e == 0) me.parent().fadeOut(200);
				}
				return false;
			}, 'json');
		}, 500);
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