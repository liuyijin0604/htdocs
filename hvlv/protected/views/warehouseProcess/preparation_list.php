<h2>Preparation Summary List</h2>

<br>


<div class="form-group">
<h3 style="font-size: 2em;">Preparation:</h3>
<p>
<?= CHtml::label('Gatepass No.','Gatepass No.');?>
<?= CHtml::textField('gatepassNo', '',['class'=>'form-control','style'=>'width:10em;display:inline-block;']);?>
</p>
<p>
<?= CHtml::label('Pickup date','Pickup date');?>
<?= CHtml::dropDownList('pickupDate','', ["tomorrow"=>"today and tomorrow","all"=>"All","future_date"=>"Future Date"],['class'=>'form-control','style'=>'width:12em;display:inline-block;','id'=>'pickupDate']);?><?= CHtml::textField('pickupFutureDate','',['class'=>'form-control','style'=>'width:7em;display:inline-block;','id'=>'pickupFutureDate']);?>
</p>
</div>
<?=CHtml::submitButton('Search',array('class'=>'form-control search','style'=>'width:5em;float:left;','id'=>'search'));// $this->endWidget();?>
<br>
<br>


<div id = "preparation_content">
	<?php echo $this->renderPartial('loading_list', array('url'=>"warehouseProcess/getPreparationList","dptId"=>$dptId)); ?>
</div>

<!-- <iframe src="http://localhost/hvlv/gapsig/gatePass/needProcessParcelList.app?render=partial" style="width: 100%;height:80em;">
	

</iframe> -->
<script type="text/javascript">
	$(function(){
		 var tab = $('#<?=$_GET["tabid"];?>');
   		var panel = $('#<?=$_GET["tabid"];?>').data('panel');
		$('#pickupDate',panel).on('change',function(){
			if($('#pickupDate').val()=='future_date')
			{
				$('#pickupFutureDate').show();
			}else
			{
				$('#pickupFutureDate').hide();
			}
		});
		$('#pickupFutureDate',panel).datetimepicker({
	        forceParse: 0,//设置为0，时间不会跳转1899，会显示当前时间。
	        language: 'zh-CN',//显示中文
	        format: 'yyyy-mm-dd',//显示格式
	        minView: "month",//设置只显示到月份
	        initialDate: new Date(),//初始化当前日期
	        autoclose: true,//选中自动关闭
	        todayBtn: true//显示今日按钮
    	});
    	$('#pickupFutureDate',panel).hide();

    	$('#search',panel).on('click',function(){
             var gatepassNo = $('#gatepassNo').val();
             var pickupDate = $('#pickupDate').val();
             var pickupFutureDate = $('#pickupFutureDate').val();
             $.ajax({
				'url': '<?=$this->createUrl('warehouseProcess/getPreparationList')."?dptId=".$dptId;?>',
				'type': 'POST',
				'data': { 'gatepassNo': gatepassNo,'pickupDate': pickupDate,'pickupFutureDate': pickupFutureDate,'tabid':'<?=$_GET['tabid']?>'},
				success: function(r) {
					$('#preparation_content').html(r);
					var brl = '<?=$this->createUrl('warehouseProcess/getPreparationList');?>';
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

					$('#pl-sum-report-data_<?=$_GET['tabid']?>',panel).on('click', 'span.exp', function(){
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
							nototal = $(this).data('nototal');
							tl.load(brl +'?path='+$(this).data('path')+'&&nototal='+nototal, function(){
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



				}
			});
        });
	});

</script>