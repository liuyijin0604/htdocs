<?php
if ($op == 'tocheck')
{
	$title = 'Scan for Check In And Weighing And Sorting';
}else if($op=='checkin')
{
	$title = 'Scan In Consol';
}else
{
	$title ='Scan for Check status';
}

$this->widget('zii.widgets.CBreadcrumbs', array(
    'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
		$title,
	),
));
?>

<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
?>
<center>
<h1><?=ucfirst(Yii::app()->session['scan_warehouse'])?>  Warehouse</h1>
</center>

<h1><?=$title?></h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', [
	'id'=>'scan-form',
	'enableAjaxValidation'=>false,
]);
?>
<div style="float:right;"><label>Auto Print: <input type="checkbox" id="auto_print" name="auto_print" value="1" /></label> &nbsp; <label>Alt Sound: <input type="checkbox" name="sound" value="1" /></label></div>
<div style="font-size: 1.5em;">Barcode: <input style="width: 95%" id="scan" type="text" size="30" name="barcode" autocomplete="off" /></div>

<br>
<br>
<br>
<br>

<?php if ($op == 'checkin') : ?>
	<input type="hidden" name="cid" value="<?php echo $cid; ?>">
<?php endif; ?>
	<input type="hidden" id="scan-code" name="scan_code" value="">
<?php $this->endWidget(); ?>

<div id="result" style="background-color: white;margin-top:10px;">
	<table id="items" class="table table-striped table-bordered" style="font-size: 1.5em;">
		<tbody>
			<tr><td class="status"></td></tr>
			<tr><td class="area"></td></tr>
			<tr><td class="msg"></td></tr>
			<tr><td class="gatepass"></td></tr>
			<tr><td class="console"></td></tr>
			<tr><td class="hold"></td></tr>
		</tbody>
	</table>
</div>
<?php
echo CHtml::button('Print', ['class' => 'print_btn','style'=> 'display:none; margin-left:20px;font-size:xx-large;']);
echo CHtml::button('Print PDF Label', ['class' => 'print_btn_pdf','style'=> 'display:none;margin-left:20px;font-size:xx-large;']);
?>

<input type="hidden" name="scanned-id" value="" id="scaned_shipment_id">
<div id="print-result" style="margin-left: 20px;margin-top:10px;font-size: 1.5em;"></div>
	<table style="text-align: right;width:100%;">
		<tfoot>
			<tr><td><?php echo CHtml::button('Home', ['class' => 'gohome','style'=> 'text-align:right;1.5em']); ?></td></tr>
		</tfoot>
	</table>
</div>
<iframe id="pdf_label" style="display: none;" name="pdf_label" src="" ></iframe>

<?php 
if ($op=='checkin')
{
	$this->widget('application.extensions.booster.TbExtendedGridView', array(
		'id'=>'szPortal-grid',
		'type'=>'striped bordered',
		'headerOffset'=>40,
	    'responsiveTable'=>true,
		'dataProvider'=>$model->search(true, 30 ,false, false, false, false, true),
		'filter'=>$model,
		'template' => "{summary}\n{items}\n{pager}",
		'columns'=>array(
			array('name' => 'no','type' => 'raw',),
			array('name' => 'status', 'value' => '$data->getStatus()', 
				'filter'=>false,),
			array('name' => 'awb', 'type' => 'raw', 'value' => '$data->AwbTracking()', ),
			array('header' => $this->t('Shipments'), 'value' => '$data->totShipments()'),
			array('header' => $this->t('pkg'), 'value' => '$data->totPacks()'),
			array('header' => $this->t('pkgtck'), 'value' => '$data->totPacksTCK()'),
			array('header' => $this->t('CBM'), 'value' => '$data->totImCBM()'),
			array('header' => $this->t('CBMTCK'), 'value' => '$data->totImCBMTCK()'),
			array('header' => $this->t('Weight'), 'value' => '$data->totWeight()'),
			array('header' => $this->t('Wtck'), 'value' => '$data->totWtck()'),
			array('name' => 'pol', 'filter'=>CHtml::dropDownList('ImcoConsol[pol]', $model->pol, $this->t(AppHelper::setting2List('pols')), array('prompt'=>$this->t('All'))),),
			array('name' => 'pod', 'filter'=>CHtml::dropDownList('ImcoConsol[pod]', $model->pod, $this->t(AppHelper::setting2List('pods')), array('prompt'=>$this->t('All'))),),
			array('name' => 'poc', 'value' => '$data->getPoc()', 'filter'=>CHtml::dropDownList('ImcoConsol[poc]', $model->poc, $this->t(SzpChannel::getPocs(true)), array('prompt'=>$this->t('All'))),),
			'created',
			// array('header' => $this->t('Occupied'), 'value' => '$data->isOccupied()? "Yes" : "No"', 'filter'=>CHtml::dropDownList('ImcoConsol[ocpd]', $model->ocpd, ['Y' => 'Yes'], array('prompt'=>$this->t('All'))),),
			
			/*'created',
			
			'airline',
			'flight',
			'eta',
			'meta',
			*/
			array(
				'class'=>'oButtonColumn',
				'template'=>'{Scan_Complete}',
				'buttons'=>array
				(
					'Scan_Complete' => array(
						'imageUrl'=>false,
						'visible'=>'isset($data->mdata["scanComplete"])?false:true',
						'options' => array('class' => 'scan_complete grid_edit_btn ', 'value'=>'$data->id','label'=>$this->t('扫描已完成'), 'title' => '$data->no'),
					),
				),
			),
		),
	)); 
}

?>

<?php if ($op == 'tocheck') : ?>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', [
	'id'=>'update-scan-form',
	'enableAjaxValidation'=>false,
]);
?>
<input type="text" id="shipmentId" name="shipment[id]" />
<label>Connote:</label><input type="text" id="hbn" name="shipment[hbn]" disabled="disabled" />
<p>
<label>Weight:</label><input type="text" id="weight" name="shipment[weight]" disabled="disabled" />
<label>Our Weight:</label><input type="text" id="wtck" name="shipment[wtck]" />
</p>
<p>
	<label>packs:</label><input type="text" id="pkg" name="shipment[pkg]" disabled="disabled" />
	<label>Our packs:</label><input type="text" id="pkgtck" name="shipment[pkgtck]" />
</p>
<p>
<label>Total CBM (WxHxL)</label>
		<?php echo CHtml::textField('cbm', 0, array('size' => 4,'id'=>'cbm',"disabled"=>"disabled")); ?><!-- CM x 
		<?php echo CHtml::textField('dim[h]', 0, array('size' => 4,'id'=>'dimh',"disabled"=>"disabled")); ?>CM x 
		<?php echo CHtml::textField('dim[d]', 0, array('size' => 4,'id'=>'dimd',"disabled"=>"disabled")); ?>CM -->
</p>

<p>
<label>Our Total CBM (WxHxL)</label>
		<?php echo CHtml::textField('dimtck[w]', 0, array('size' => 4,'id'=>'dimwtck')); ?>CM x 
		<?php echo CHtml::textField('dimtck[h]', 0, array('size' => 4,'id'=>'dimhtck')); ?>CM x 
		<?php echo CHtml::textField('dimtck[d]', 0, array('size' => 4,'id'=>'dimdtck')); ?>CM
</p>
<p>
<label id="warnings" style="font-size:4em;">Warnings: </label>
</p>


<?= CHtml::button('update', ['class' => 'updateWeight']);?>
<p><label id="shipmsg" style="font-size: 5em;"></label></p>
<?php $this->endWidget(); ?>



<?php
$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'id'=>'imParcel-grid',
	'dataProvider'=>$parcel->search(true,5),
	'filter'=>$parcel,
	'rowCssClassExpression' => '
		($row%2 ? "odd" : "even" )." ".$data->getColorCls()
	',
	'columns'=>array(
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',),
		'ref',
		array('name' => 'agent_name', 'value' => 'empty($data->agent)? "" : $data->agent->shortName(2)',),
		array('name' => 'status', 'value' => '$data->getStatus()','filter'=>false),
		//array('header' => 'Goods', 'type' => 'raw', 'value' => '$data->getGoods()', 'filter' => null),
		'weight',
		'dvalue',
		//array('name' => 'dvalue', 'header' => 'Value AUD', 'value' => '$data->audVal()'),
		//array('name' => 'exm', 'filter' => CHtml::dropDownList('ExParcel[exm]', $parcel->exm, ExParcel::shortExms(), array('prompt'=>$this->t('All'))), ),
		array('name' => 'cnor_name', 'value' => 'empty($data->cnor)? "" : $data->cnor->name',),
		array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
		array('name' => 'cnee_addr', 'value' => '$data->cnee->fullAddress(array("city"))'),
		//array('name' => 'prod', 'value' => '$data->GoodsNames()'),

		//array('header' => $this->t('Type'), 'value' => '$data->goodsType()'),
		//ray('header' => $this->t('Qty'), 'value' => 'empty($data->eitems["q"])? "-" : array_sum($data->eitems["q"])'),
		// array('header' => $this->t('Location'), 'type' => 'raw', 'value' => '$data->getLocation()', 'filter'=>CHtml::dropDownList('ExParcel[ocpd]', $parcel->ocpd, ['Y' => 'Occupied', 'N' => 'Empty'], array('prompt'=>$this->t('All'))),),
		array('name' => 'bwf', 'header' => $this->t('Warnings'), 'type' => 'raw', 'value' => '$data->getSZPWarnings()','filter'=>CHtml::dropDownList('ImParcel[warnings]', @$parcel['warnings'], $this->t(ImParcel::$warnings), array('prompt'=>$this->t('All'))),),
		// array(
		// 	'class'=>'oButtonColumn',
		// 	'template'=>'{Swap} {delete}',
		// 	'buttons'=>array(
		// 		'Swap' => array(
		// 			'imageUrl' => false,
		// 			'url' => 'Yii::app()->createURL("szPortal/swapParcel", array("id" => $data->id))',
		// 			'visible' => '@$data->consol->status < 20',
		// 			'options' => array('class' => 'jqm_link grid_swap_btn', 'label'=>$this->t('Swap'), 'title' => '$data->hbn'),
		// 		),
		// 		'delete' => array(
		// 			'imageUrl'=>false,
		// 			'visible' => '@$data->consol->status < 20',
		// 			'options' => array('class' => 'grid_delete_btn', 'label'=>$this->t('Remove from Consol.')),
		// 			'url' => 'Yii::app()->createURL("szPortal/removeParcel", array("id" => $data->id))',
		// 		),
		// 	),
		// ),
	),
));
?>

<?php endif; ?>




<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('input#scan').focus();
	$('#result tbody tr').hide();

	$('form#scan-form').on('success', function(e,r){
		$('input#scan-code').val($('input#scan').val());
		$('input#scan').val('').focus();
		$('#print-result').html('');
		var audio=new Audio();
		audio.src='https://os.toplogistics.com.au/site/voice/' + (r.sounds.join('-')) + '.mp3';
		audio.play();
		$('#result tbody tr').hide();
		$(['status', 'area', 'msg', 'area', 'amazon', 'gatepass', 'hold','ubi']).each(function(i){
			if(r[this]) $('#result td.'+this).html(r[this]).parent().show();
			if((r['ubi'])&&(this=='ubi'))
			{
				if(r['area'])
				{
					$oHtml = $('#result td.area').html();
					if(r['area']) $('#result td.area').html($oHtml+', ubi').parent().show();
				}
			}
		});

		if ( r.found == 1 ) {

		<?php if ($op == 'tocheck') : ?>
			$('#shipmentId').val(r.id);
			$('#hbn').val(r.hbn);
			$('#weight').val(r.weight);
			$('#wtck').val(r.wtck);
			$('#pkg').val(r.pkg);
			$('#pkgtck').val(r.pkgtck);
			$('#cbm').val(r.cbm);
			// $('#dimh').val(r.dimh);
			// $('#dimd').val(r.dimd);
			$('#dimwtck').val(r.dimwtck);
			$('#dimhtck').val(r.dimhtck);
			$('#dimdtck').val(r.dimdtck);
			$('#shipmsg').html("");
			$('#warnings').html("warnings:"+r.warnings);
		<?php endif; ?>


			$('#scaned_shipment_id').val(r.id).data('sn', r.sn);

		}

		if (r.print == 1 ) {
			$('.print_btn, .print_btn_pdf').show();
			if($('input#auto_print').prop('checked')) $('input.print_btn_pdf').trigger('click');
		} else {
			$('.print_btn, .print_btn_pdf').hide();
		}

		$('.form').removeClass('red green blue').addClass(r.color);
		$('#szPortal-grid').yiiGridView('update');
		return true;
	}).on('submit', function(){
		$('input#scan').focus();
	});

	<?php if ($op == 'tocheck') : ?>
	$('.updateWeight').click(function(){
		$('form#update-scan-form').submit();
	});

	$('form#update-scan-form').on('success', function(e,r){
		$('#shipmsg').html(r.msg);
	});

	<?php endif; ?>

	$('input#scan').on('focus', function(){
		$(this).select();
	});

	$('.gohome').click(function(e){
		window.location.href = '<?= $this->createURL('/szportal')?>';
	});

	$('.print_btn').click(function(e){
		// disable print button
		$(this).attr('disabled',true);
		$('#print-result').html('Printing In Progress');
		var baseUrl = <?php echo json_encode(Yii::app()->createAbsoluteUrl("szportal/shipment/print")); ?>;
		baseUrl +=  "?id=" + $('#scaned_shipment_id').val()+'&sn='+$('#scaned_shipment_id').data('sn');
		$.ajax({
			type : 'GET',
			url : baseUrl,
			dataType: 'json',
			success:function(resp){
				if ( resp.success == 1 ) {
					$('#print-result').html('Print Done');
				} else {
					$('#print-result').html(resp.msg);
				}
				$('.print_btn').attr('disabled',false);
				$('input#scan').val('');
				$('input#scan').focus();
			}
		});
	});

	$('.print_btn_pdf').click(function(e){
		var pdfFrame = window.frames["pdf_label"];
		$("#pdf_label").attr("src","<?php echo Yii::app()->createAbsoluteUrl("szportal/shipment/connote"); ?>"+"?id="+$('#scaned_shipment_id').val()+'&sn='+$('#scaned_shipment_id').data('sn'));
		$("#pdf_label").load(function(){
			 pdfFrame.focus();
			 pdfFrame.print();
		});
	});   

	$('.scan_complete').click(function(e){
		var id = $(this).attr("value");
		var  cn= prompt("Please enter 'Complete'");
		if(cn == 'Complete'){
			$.get('<?=$this->createURL('shipment/scanComplete');?>'+"?id="+id, function(){
				window.location.reload();
			});
		}

	});


});
</script>
<?php $this->registerJS(ob_get_clean()); ?>