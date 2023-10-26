<h2><?=$this->t('Shipment');?> - <?=$model->hbn;?></h2>
<div style="text-align: center">
<?php
$label_img = substr($this->createUrl('client/label'),0,-5).'/'.$model->id.'/'.$model->hbn.'.jpg';
?>
<img id="label" src="<?=$label_img?>" class="img-responsive" />
</div>
<div class="row" style="padding: 20px 0;">
	<div class="col col-xs-4 col-sm-2">
		<a href="<?=$label_img?>?save=1" id="save_btn" class="btn btn-primary btn-lg" target="_blank"><?=$this->t('Save Label');?></a>
	</div>
<?php if(1):?>
	<div class="col col-xs-4 col-sm-2">
		<button type="button" id="print_btn" class="btn btn-primary btn-lg" data-uri="<?=$this->createUrl('client/print', ['id' => $model->id, 'c' => $model->hbn]);?>"><?=$this->t('Print Label');?></button>
	</div>
<?php endif; ?>
	<div class="col col-xs-4 col-sm-2">
		<a href="<?=substr($this->createUrl('qr/cns'), 0, -5).'/'.$model->agent->hash;?>" id="new_btn" class="btn btn-info btn-lg"><?=$this->t('New Shipment');?></a>
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

	$('#print_btn').on('click', function(){
		var btn = $(this);
		posApp.btnLoading(btn);
		$.get($(this).data('uri'), function(r){
			btn.remove();
		});

		return false;
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>