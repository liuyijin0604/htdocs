<h2><?=$this->t('Order');?> - <?=$model->hbn;?></h2>
<div class="bs-callout bs-callout-info" id="callout-help-text-accessibility">
    <p>注：订单只有在确认付款后才有效。</p>
</div>
<div style="text-align: center">
<?php
$label_img = substr($this->createUrl('client/edoLabel'),0,-5).'/'.$model->id.'/'.$model->hbn.'.jpg';
?>
<img id="label" src="<?=$label_img?>" class="img-responsive" />
</div>
<div class="row" style="padding: 20px 0;">
	<div class="col col-xs-4 col-sm-2">
		<a href="<?=$label_img?>?save=1" id="save_btn" class="btn btn-primary btn-lg" target="_blank"><?=$this->t('Save Label');?></a>
	</div>
	<!--div class="col col-xs-4 col-sm-2">
		<button type="button" id="pay_online" class="btn btn-primary btn-lg" data-uri="<?=$this->createUrl('client/paypal', ['id' => $model->id, 'c' => $model->hbn]);?>"><?=$this->t('Online Payment');?></button>
	</div-->
	<div class="col col-xs-4 col-sm-2">
		<button type="button" id="pay_cash" class="btn btn-info btn-lg"><?=$this->t('Cash Payment');?></button>
	</div>
</div>
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	posApp.pleaseWait = '<?=$this->t("Please Wait");?>';
	$('#save_btn').on('click', function(){
		alert('<?=$this->t("Please long press the image to save");?>');
		return false;
	});

	$('#pay_cash').on('click', function(){
		alert('<?=$this->t("Please make cash payment to the counter, and make sure staff has validated this order, thanks.");?>');
		return false;
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>