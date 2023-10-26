<h1><?=$this->t('Create Intl Charge Code');?></h1>

<?php echo $this->renderPartial('_form_intl_charge_code',['model' => $model]); ?>

<script type="text/javascript">
	$(function(){
		var tab = $('#<?=$_GET["tabid"];?>');
		var panel = tab.data('panel');

		$('form#intl-charge-code-form', tab.data('panel')).on('success', function(e, r){
			// var url = tab.data('url').replace('imParcel/create','imParcel/update/'+r.id);
			// tab.data('url', url).trigger('load');
		});

	});
</script>
