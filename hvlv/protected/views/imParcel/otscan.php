<?php

if(!empty($model->scan_data[50])){
	foreach($model->scan_data[50] as $a => $v){
		$sn = "";
		if(!empty($pbs))
		{
			foreach ($pbs as $key => $pb) {
				if($pb->sub_ref ==$a)
				{
					$sn = $pb->sn;
				}
			}
		}
		echo '<p>'.$a.': '.$sn.': '.$v.' <a class="undo" href="'.$this->createUrl('imParcel/otScanned',['id' => $model->id, 'undo' => $a]).'">undo</a></p>';
	}
}
?>
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	$('a.undo', win).on('click', function(){
		if(window.confirm('Are you sure?')){
			$.get($(this).attr('href'), function(){
				win.data('opener').trigger('onOpen');
				win.jqmHide();
			});
		}
		return false;
	});
});
</script>