<h1><?=Yii::t('whscan','Put in Pallet')." and ".Yii::t('whscan','Put Away')?></h1>
<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
?>
<div style="margin-bottom:15px;font-size: 1.5em;">
	ContainerNo/AWB: <input class="form-control" type="text" name="sort_myContainerNo" placeholder="ContainerNo/AWB" id="sort_myContainerNo" autocomplete="off" />
</div>
<!-- <p>Not put away today: <a  id="strCount"><?php //echo $count ?></a> Updated at: <a id=strTime><?php //echo $time ?></a></p> -->
<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'sortheld-form',
		'action'=>$this->createUrl('shipment/putawayAndSortHeld'),
		'enableAjaxValidation' => false,
	)); ?>
	
	<div style="margin-bottom:15px;">
		<input class="barcode required form-control" type="text" name="shipment" placeholder="Shipment" id="shipment_psh" autocomplete="off" value="<?=@$barcode?>"/>
	</div>

	<div class="input-group">
		<input class="barcode required form-control" type="text" name="pallet_label" placeholder="pallet label / Unknown:Any Location" id="pallet_label_psh" autocomplete="off" />
		<label class="input-group-addon"><input type="checkbox" class="klocation" /> Lock</label>
	</div>

	<div class="input-group">
		<label class="input-group-addon">Recommend Location:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id = "recommend_location" style="font-weight:bold;font-size: 2.5em;"></span></label>
	</div>
	<div class="input-group"><input id="ground_label_psh" class="barcode required form-control" type="search" placeholder="ground label" name="ground_label" />
		<label class="input-group-addon"><input type="checkbox" class="lock_ground_label" /> Lock</label>
	</div>


	<?php $this->endWidget(); ?>
</div>
<br />
<div id="res"></div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {

	$('input#pallet_label_psh').on('blur', function(e) {
		if ($('input#pallet_label_psh').val() != "") {
			$.ajax({
				'url': '<?=$this->createUrl('warehouseProcess/getLocationRecommendation');?>',
				'type': 'GET',
				'data': {'pallet_no': $('input#pallet_label_psh').val()},
				success: function(r) {
					r = JSON.parse(r);
					if(r['success'] == true)
					{
						$('#recommend_location').html(r['recommendLocation']);
					}else
					{
						$('#recommend_location').html("Without Recommend");
					}
				}
			});
		}
	})


	$('input#shipment_psh, #pallet_label_psh, input#ground_label_psh').focus().on('keydown', function(e) {
		if (e.which == 13) {
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function(){
		var containerNo = "";
		containerNo = $('#sort_myContainerNo').val()

		if($('#shipment_psh').val()!='' && $('#pallet_label_psh').val()=='')
		{
			$('input#pallet_label_psh').focus();
			return false;
		}

		if($('#shipment_psh').val()!='' && $('#pallet_label_psh').val()!='')
		{
			$.ajax({
				'url': '<?=$this->createUrl('shipment/held');?>',
				'type': 'POST',
				'data': { 'shipment': $('input#shipment_psh').val(), 'location': $('input#pallet_label_psh').val(),'search_container_no':containerNo },
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
					$('input#shipment_psh').val('').focus();
					if(!$('input.klocation').prop('checked')) $('input#pallet_label_psh').val('');
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

<?php echo $this->renderPartial('loading_list', array('url'=>"warehouseProcess/getPutawaySortHeldList")); ?>