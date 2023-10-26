<div id="loading_<?=$_GET['tabid']?>" style="margin: 10px 0; border: 1px solid;padding:20px; display: none">
		Export is loading.............
</div>
<ul id="pl-sum-report-data_<?=$_GET['tabid']?>" class="report_list" style="margin-top: 20px; list-style: none;font-size: 1.2em;"></ul>

<script type="text/javascript">
$(function(){
		var brl = '<?=$this->createUrl($url);?>';
		function loadData()
		{
			$('#loading_<?=$_GET['tabid']?>').show();
			$('#export_sum_result_<?=$_GET['tabid']?>').hide();
			$('#pl-sum-report-data_<?=$_GET['tabid']?>').addClass('loading_list').load(brl, function(){
			$('#loading_<?=$_GET['tabid']?>').hide();
			 $('#pl-sum-report-data_<?=$_GET['tabid']?>').removeClass('loading_list')


			});

			return false;
	};

	loadData();

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
		$('input[name="export_details"]').click(function(e){
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


	$('#pl-sum-report-data_<?=$_GET['tabid']?>').on('click', 'span.exp', function(){
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
			li.addClass('loading_list');
			nototal = $(this).data('nototal');
			tl.load(brl +'?path='+$(this).data('path')+'&&nototal='+nototal, function(){
				me.addClass('in').data('loaded', 1);
				li.removeClass('loading_list');
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
}	
);

</script>