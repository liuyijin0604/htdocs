<h2><?=Yii::t('Inspection','Inspection')?></h2>
<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
?>
<?php echo $this->renderPartial('loading_list', array('url'=>"warehouseProcess/getInspectionList")); ?>

<script type="text/javascript">
	function deleteInspectionFile(id)
	{
		var thisData = {"id":id};
				if( confirm('Are you sure to delete file?')){
						$.ajax({
					            url: '<?=$this->createUrl("warehouseProcess/deleteInspectionFile")?>',
					            type: "post",
					            data: thisData,
					            success: function(r) {
						             $('#inspection-excofile-grid').yiiGridView("update");
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });	
						
				}
				return false;
	};
	
	function freshInspectionListDirect()
	{
		var panel = $('#inspection_grid_view');
		var brl = '<?=$this->createUrl("warehouseProcess/getInspectionList");?>';
		var q = $('.filters input, .filters select', panel).serialize()+'&'+$('.search-form form', panel).serialize();
		brl = brl + '?' + q;

		$('#loading_<?=$_GET['tabid']?>').show();
		$('#export_sum_result_<?=$_GET['tabid']?>').hide();
		$('#pl-sum-report-data_<?=$_GET['tabid']?>').addClass('loading').load(brl, function(){
			$('#loading_<?=$_GET['tabid']?>').hide();
			$('#pl-sum-report-data_<?=$_GET['tabid']?>').removeClass('loading');
		});
	}


	function freshInspectionList()
	{
		if (event.keyCode==13)
		{
			freshInspectionListDirect();
		}
	}
</script>