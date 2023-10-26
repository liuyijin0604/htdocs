<h2>Rebate Report</h2>
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
    overflow-y: scroll;
    overflow-x: hidden;
}
#pl-sum-report-data .grid-view table.items tbody tr {
    width: 100%;
}
#pl-sum-report-data .grid-view table.items thead, #pl-sum-report-data .grid-view table.items tfoot, #pl-sum-report-data .grid-view table.items tbody tr {
    display: table;
    table-layout: fixed;
}

#pl-sum-report-data li:hover>p{
	color: #090;
	font-weight: bold;
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
	'id' => 'cogs-all-report-form',
	'enableAjaxValidation' => false,
	'action' => $this->createUrl('report/rebateReport'),
//	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
)); ?>

	<div class="row rowcol rowleft">
	<?php echo CHtml::label('Depot:','dt'); ?>
	<?php echo CHtml::dropDownList('wid', null, Org::dptList(), array('empty' => 'All')); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('From Date:','fd'); ?>
		<?php echo CHtml::textField('date[from]', empty($_POST['date']['from'])? date('Y-m-01', strtotime('-1 month')) : $_POST['date']['from'], array('size' => 12, 'id' => 'fd_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>


	<div class="row rowcol">
		<?php echo CHtml::label('To Date:','td'); ?>
		<?php echo CHtml::textField('date[to]', empty($_POST['date']['to'])? date('Y-m-d', strtotime(date('Y-m-01').' -1 day')) : $_POST['date']['to'], array('size' => 12, 'id' => 'td_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?>
		<?php //echo CHtml::submitButton($this->t('Export Details')); ?>
	</div>

<?php $this->endWidget(); ?>

	<div id="export_sum_result" style="margin: 10px 0; border: 1px solid;padding:20px; display: none">
	</div>

	<ul id="pl-sum-report-data" style="margin-top: 20px; list-style: none;font-size: 1.2em;"></ul>

</div><!-- form -->


<script type="text/javascript">
$(function(){
	var tab_id = '<?=$_GET["tabid"];?>';
	var tab = $('#'+tab_id);
	var panel = tab.data('panel');
	var brl = '<?=Yii::app()->createAbsoluteUrl("report/ajaxRebateSumReport");?>';

	$('input[name="yt0"]',panel).click(function(e){
		$('#pl-sum-report-data', panel).addClass('loading').load(brl +'?path='+$('#wid', panel).val()+'/'+$('#fd_'+tab_id, panel).val()+'/'+$('#td_'+tab_id, panel).val(), function(){ $('#pl-sum-report-data', panel).removeClass('loading')});

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
	}

	$('#pl-sum-report-data', panel).on('click', 'span.exp', function(){
		var li = $(this).parent().parent();
		var tl = li.find('>ul');
		var me = $(this);
		if($(this).data('loaded') == 1){
			if($(this).hasClass('in')){
				tl.slideUp();
				$(this).removeClass('in');
			}else{
				tl.slideDown();
				$(this).addClass('in');
			}
		}else{
			li.addClass('loading');
			tl.load(brl +'?path='+$(this).data('path'), function(){
				me.addClass('in').data('loaded', 1);
				li.removeClass('loading');
				afterLoading(tl);
			});
		}
		return false;
	}).on('click', 'a.ogrpt', function(){
		var os = [];
		$(this).parent().find('input.ogcb:checked').each(function(){
			os.push($(this).val());
		});
		$(this).attr('href', $(this).data('ub')+os.join(','));
	});

	$('input[name="yt1"]',panel).click(function(e){
		$('#export_sum_result').empty().hide();
		$.ajax({
			type : 'POST',
			url : '<?php echo Yii::app()->createAbsoluteUrl("report/ajaxExportPlSumReport") ;?>',
			data: $('#cogs-all-report-form', panel).serialize(),
			dataType: 'html',
			success:function(resp){
				$('#export_sum_result').show();
				$('#export_sum_result').html(resp);
			}
		});
		
		return false;
	});

	$('#fd_'+tab_id, panel).on('change', function(){
		var v = $(this).val();
		var ld = new Date(v.substr(0, 4), parseInt(v.substr(5,2)), 0).getDate();
		$('#td_'+tab_id, panel).val(v.substr(0,8) + ld);
	});

});

</script>
