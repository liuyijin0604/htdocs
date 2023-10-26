<h1><?=$this->t('Delay Notification');?></h1>
  <div id="<?=$_GET["tabid"];?>-spec" class="pane">
	<form id="delay-form" class="ifrm-form" enctype="multipart/form-data" method="post" action="import/delay?act=import">
	  <label for="import_file">Excel File: </label>
	  <input type="file" name="import" id="import_file" /> &nbsp;
	  <input type="submit" value="Upload" />
	  <p><small>.xls or .xlsx file supported</small></p>
	</form>
  </div>
<div id="step2">
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('form#delay-form', tab.data('panel')).ajaxForm({
		success: function(r) {
			$('#step2').html(r);
			$('form#delay-notify-form', tab.data('panel')).ajaxForm({
				success: function(r) {
					$('#step2').html(r);
				}
			});
		}
	});
});
</script>
