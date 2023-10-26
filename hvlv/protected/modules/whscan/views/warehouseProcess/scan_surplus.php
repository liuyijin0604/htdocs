<h1><?=Yii::t('whscan','Scan Surplus')?></h1>
<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
?>
<!-- <p>Not put away today: <a  id="strCount"><?php //echo $count ?></a> Updated at: <a id=strTime><?php //echo $time ?></a></p> -->
<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'sur_plus_form',
		'action'=>$this->createUrl('shipment/putawayAndSortHeld'),
		'enableAjaxValidation' => false,
	)); ?>
	
	<div style="margin-bottom:15px;font-size: 1.5em;">
		ContainerNo/AWB: <input class="form-control" type="text" name="surplus_myContainerNo" placeholder="ContainerNo/AWB" id="surplus_myContainerNo" autocomplete="off" />
	</div>

	
	<div style="margin-bottom:15px;">
		<input class="required form-control" type="text" name="ground_label_surplus" placeholder="location" id="ground_label_surplus" autocomplete="off"/>
		<input class="barcode required form-control" type="text" name="shipment_surplus" placeholder="Shipment" id="shipment_surplus" autocomplete="off" value="<?=@$barcode?>"/>
	</div>

	<div style="margin-bottom::15px;font-size: 2em;" id="surplus_error_notice" class="red">

	</div>
	<div style="margin-bottom::15px;font-size: 2em;" id="surplus_success_notice" class="green">

	</div>
	
	</div>


	<?php $this->endWidget(); ?>
</div>
<br />
<div id="res"></div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {

	$('input#surplus_myContainerNo').on('blur', function(e) {
		if ($('input#surplus_myContainerNo').val() != "") {
			$.ajax({
				'url': '<?=$this->createUrl('warehouseProcess/checkConsol');?>?containerNo='+$('input#surplus_myContainerNo').val(),
				'type': 'GET',
				'data': {'pallet_no': $('input#surplus_myContainerNo').val()},
				success: function(r) {
					r = JSON.parse(r);
					if(!r['success'])
					{
						$('#surplus_error_notice').html(r['msg']);
					}else
					{
						$('#surplus_error_notice').html("");
					}
				}
			});
		}
	})

	$('input#shipment_surplus, #pallet_label_psh, input#ground_label_psh').focus().on('keydown', function(e) {
		if (e.which == 13) {
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function(){

		if($('#shipment_surplus').val()!='')
		{
			var form = new FormData(document.getElementById("sur_plus_form"));
			$('#shipment_surplus').val("");
			 $.ajax({
			            url: '<?=$this->createUrl('warehouseProcess/scanSurplus')?>',
					    type: "post",
					    data: form,
					    processData: false,
					    contentType: false,
			            success: function(r) {
			            	r = JSON.parse(r)
			                if(r.done)
			                {	$('#surplus_error_notice').html("");
			            		$('#surplus_success_notice').html(r.msg);
			            		$('#shipment_surplus').val("");
								var audio=new Audio();
								audio.src='https://os.toplogistics.com.au/site/voice/received.mp3';
								audio.play();

							 }else
							 {
								$('#surplus_error_notice').html(r.msg);
			            		$('#surplus_success_notice').html("");
			            		$('#shipment_surplus').val("");
			            		var audio=new Audio();
								audio.src='https://os.toplogistics.com.au/site/voice/held.mp3';
								audio.play();
				             }
				         },
			            error: function(e) {
			                console.log(e);
			            }
			        });


			return false;
		}




		if ($('input#ground_label_psh').length == 0 || $('input#ground_label_psh').val() == '') {
			$('input#ground_label_psh').focus();
		} else {
			if($('#recommend_location').html()!="Without Recommend"&&$('#recommend_location').html()!=""&&$('#recommend_location').html()!=$('input#ground_label_psh').val())
			{
				// if(confirm("The location not equals the recommended location. Are you sure to putaway?"))
				// {

				// }else
				// {
				// 	return false;
				// }
			}

			$.ajax({
				'url': '<?=$this->createUrl('shipment/putawaypallet');?>',
				'type': 'POST',
				'data': { 'pallet_label': $('input#pallet_label_psh').val(), 'ground_label': $('input#ground_label_psh').val(),'recommend':$('#recommend_location').html()},
				success: function(r) {
					r = JSON.parse(r);
					var audio=new Audio();
					audio.src='https://os.toplogistics.com.au/site/voice/' + r['sounds']+ '.mp3';
					audio.play();
					if (r['success'] == true) {
						$('#res').html('<span style="color:green; font-size: 2em;">' + r['msg'] + '</span>');
					} else {
						$('#res').html('<span style="color:red; font-size: 2em;">' + r['msg'] + '</span>');
					}
					$('input#pallet_label_psh').val('').focus();
					if(!$('input.lock_ground_label').prop('checked')) $('input#ground_label_psh').val('');
				}
			});
		}
		return false;
	});

});
</script>
<?php $this->registerJS(ob_get_clean()); ?>