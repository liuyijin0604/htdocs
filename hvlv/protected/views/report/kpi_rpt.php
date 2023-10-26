<h2>Kpi Report</h2>
<style type="text/css">
#pl-sum-report-data{
	list-style: none;
	margin: 0;
	padding: 0;
	line-height: 25px;
}
#pl-sum-report-data ul{
	list-style: none;
	margin: 0;
	padding-left: 20px;
}

#pl-sum-report-data .grid-view table.items {
    display: flex;
    flex-flow: column;
    height: 100%;
    width: 100%;
}
#pl-sum-report-data .grid-view table.items thead, #pl-sum-report-data .grid-view table.items tfoot {
    /* head takes the height it requires, 
    and it's not scaled when table is resized */
    flex: 0 0 auto;
    width: calc(100% - 1.15em);
}
#pl-sum-report-data .grid-view table.items tbody {
    /* body takes all the remaining available space */
    flex: 1 1 auto;
    display: block;
	max-height: 400px;
}
#pl-sum-report-data .grid-view table.items tbody tr {
    width: 100%;
}
#pl-sum-report-data .grid-view table.items thead, #pl-sum-report-data .grid-view table.items tfoot, #pl-sum-report-data .grid-view table.items tbody tr {
    display: table;
    table-layout: fixed;
}

#pl-sum-report-data span.exp{
	border: 1px solid #15538b;
	color: #15538b;
	border-radius: 4px;
	padding: 0 2px;
	cursor: pointer;
	font-weight: bold;
}
#pl-sum-report-data span.exp:hover{
	color: #fff;
}
#pl-sum-report-data span.exp:before{
	content: "+";
}
#pl-sum-report-data span.exp.in{
	padding: 0 4px;
}
#pl-sum-report-data span.exp.in:before{
	content: "-";
}
</style>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'pl-all-report-form',
	'enableAjaxValidation' => false,
	'action' => $this->createUrl('report/plReport'),
//	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
)); ?>

	<div class="row rowcol rowleft">
	</div>


	<div class="row rowcol rowleft">
		<?php echo CHtml::label('Month:','fd'); ?>
		<?php echo CHtml::dropDownList('month',date('Y-m'), $monthList,array('prompt'=>'Select')); ?>
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?>
		<?php echo CHtml::submitButton($this->t('Export Details')); ?>
	</div>

<?php $this->endWidget(); ?>
	<div id="loading" style="margin: 10px 0; border: 1px solid;padding:20px; display: none">
		Export is loading.............
	</div>
	<div id="export_sum_result" style="margin: 10px 0; border: 1px solid;padding:20px; display: none">
	</div>

	<ul id="pl-sum-report-data" style="margin-top: 20px; list-style: none;font-size: 1.2em;"></ul>

</div><!-- form -->


<script type="text/javascript">
$(function(){
	var tab_id = '<?=$_GET["tabid"];?>';
	var tab = $('#'+tab_id);
	var panel = tab.data('panel');
	var brl = '<?=Yii::app()->createAbsoluteUrl("report/ajaxKpiReport");?>';

	$('input[name="yt0"]',panel).click(function(e){
		$('#loading').show();
		$('#export_sum_result').hide();
		$('#pl-sum-report-data', panel).addClass('loading').load(brl +'?month='+$('#month', panel).val(), function(){
		$('#loading').hide();
		 $('#pl-sum-report-data', panel).removeClass('loading')

			$('input[name="export_details"]',panel).click(function(e){
				var path = $(this).attr('data-path');
				$('#export_sum_result').empty().hide();
				$('#loading').show();
				$.ajax({
					type : 'GET',
					url : '<?php echo Yii::app()->createAbsoluteUrl("report/ajaxExportKpiReport") ;?>'+"?path="+path,
					dataType: 'html',
					success:function(resp){
						$('#export_sum_result').show();
						$('#export_sum_result').html(resp);
						$('#loading').hide();
					}
				});
			});


		});

		return false;
	});

	$('input[name="yt1"]',panel).click(function(e){
		$('#loading').show();
		$('#export_sum_result').hide();
		$('#pl-sum-report-data', panel).addClass('loading').load(brl +'?path=all/'+$('#fd_'+tab_id, panel).val()+'/'+$('#td_'+tab_id, panel).val()+'/'+$('#org_id', panel).val(), function(){
		$('#loading').hide();
		 $('#pl-sum-report-data', panel).removeClass('loading')

			$('input[name="export_details"]',panel).click(function(e){
				var path = $(this).attr('data-path');
				$('#export_sum_result').empty().hide();
				$('#loading').show();
				$.ajax({
					type : 'GET',
					url : '<?php echo Yii::app()->createAbsoluteUrl("report/ajaxExportPlGroupReport") ;?>'+"?path="+path,
					dataType: 'html',
					success:function(resp){
						$('#export_sum_result').show();
						$('#export_sum_result').html(resp);
						$('#loading').hide();
					}
				});
			});


		});

		return false;
	});

	var afterLoading = function(tl){
		var os = $('a.orpt', tl);
		if(os.length > 0){
			var bu = false;
			os.each(function(i){
				var me = $(this);
				var m = me.attr('href').match(/(.+%2F)(\d+)$/);
				if(!bu && m[1]) bu = m[1];
				$(this).before('<input type="checkbox" class="ogcb" name="oid[]" value="'+m[2]+'" checked /> ');
			});
			tl.append('<a href="#" data-ub="'+bu+'" class="jqm_link ogrpt"> Group Report</a>');
		}
		$('input[name="export_details"]',panel).click(function(e){
				var path = $(this).attr('data-path');
				$('#export_sum_result').empty().hide();
				$('#loading').show();
				$.ajax({
					type : 'GET',
					url : '<?php echo Yii::app()->createAbsoluteUrl("report/ajaxExportPlGroupReport") ;?>'+"?path="+path,
					dataType: 'html',
					success:function(resp){
						$('#export_sum_result').show();
						$('#export_sum_result').html(resp);
						$('#loading').hide();
					}
				});
			});

	}


});

</script>
