<div id="pca_id_uploader"></div>
<script type="text/javascript">
$('#pca_id_uploader').on('onReady', function(){
	$('.id_name').val('<?=$model->cnee->name;?>');
	$('.id_mobile').val('<?=$model->cnee->tel;?>');
	$('.id_connote').val('<?=$model->hbn;?>');
});
</script>
<script src="https://www.pcaexpress.com.au/client/js/upload_id_v2.js" type="text/javascript"></script>