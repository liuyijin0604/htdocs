<h1><?=$this->t('Update Intl Charge Code');?></h1>
<!-- <div style="right: 20px;position: absolute;">
<a class="ajax_link copycc" href="<?=$this->createUrl('import/copychargecode',['id' => $model->id]);?>" title="Copy Charge Code"><div class="icon" style="background-position:-304px -496px"></div> Copy Charge Code</a> &nbsp; </div> -->
<?php echo $this->renderPartial('_form_intl_charge_code',['model' => $model]); ?>

<script type="text/javascript">
	$(function(){
		var tab = $('#<?=$_GET["tabid"];?>');
		var panel = tab.data('panel');

		$('form#intl-charge-code-form', tab.data('panel')).on('success', function(e, r){
		// var url = tab.data('url').replace('imParcel/create','/update/'+r.id);
		// tab.data('url', url).trigger('load');
		});

		// $('a.copycc', panel).on('success', function(evt, r){
		// 	myApp.tabs.CreateTab({
		// 		title: r.code,
		// 		url: '/intl/updatechargecode/'+r.id,
		// 	});
		// }).on('click', function(){
		// 	return window.confirm('copy this charge code?');
		// });

	});
</script>
