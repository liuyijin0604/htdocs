<div style="position: absolute; right: 20px;">
<a href="<?=$this->createUrl('imcoConsol/aoutScanAll', ['id' => $model->id]);?>" id="scan_all" class="ajax_link">Scan All</a>

<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-aout-dropdown"><div style="background-position:-48px -688px" class="icon"></div> Export</a>
<div id="<?=$_GET["tabid"];?>-aout-dropdown" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="<?=$this->createUrl('imcoConsol/exportAout', ['id' => $model->id]);?>" target="_blank">Outturn Report</a></li>
		<li><a href="<?=$this->createUrl('imcoConsol/exportApLodge', ['id' => $model->id]);?>" target="_blank">Lodgement Manifest</a></li>
		<li><a href="<?=$this->createUrl('imcoConsol/exportTollApLodge', ['id' => $model->id]);?>" target="_blank">Toll Lodgement Manifest</a></li>
		<li><a href="<?=$this->createUrl('imcoConsol/exportStartrackApLodge', ['id' => $model->id]);?>" target="_blank">StarTrack Lodgement Manifest</a></li>
		<li><a href="<?=$this->createUrl('imcoConsol/exportStartrackMelApLodge', ['id' => $model->id]);?>" target="_blank">StarTrack Mel Lodgement Manifest</a></li>
		<li><a href="<?=$this->createUrl('imcoConsol/printAllLabels', ['id' => $model->id]);?>" target="_blank">Print Labels</a></li>
		<li><a href="<?=$this->createUrl('imcoConsol/printBagTags', ['id' => $model->id]);?>" target="_blank">Print Bag Tags</a></li>
	</ul>
</div>
<?php echo CHtml::button('update Scan', ['class' => 'update_scan_btn','style'=> 'margin-left:20px;']);?>
</div>
<?php
if (empty($model->mdata['aoutsent'])) {
	echo CHtml::button('Send AIROUT', ['class' => 'airout_btn']);
} else {
	echo CHtml::button('Resend AIROUT', ['class' => 'airout_btn']);
}
echo ' ', CHtml::checkbox('to_cus', true) . 'to cus';

$p = new ImParcel('search');
if (!empty($_GET['ImParcel'])) {
	$p->setAttributes($_GET['ImParcel']);
}
$p->consol_id = $model->id;

$this->widget('zii.widgets.grid.CGridView', [
	'id'=>'aout-grid_'.$_GET["tabid"],
	'cssFile' => false,
	'dataProvider'=>$p->search(),
	'filter'=>$p,
	'columns'=>[
		'hbn',
		'ref',
		'can',
		['header'=>'Agent','name'=>'agent_name','value'=>'$data->agent->name'],
		['name' => 'status', 'value' => '$data->getStatus()',
			'filter'=>CHtml::dropDownList('ImParcel[status]', $p->status, $this->t(ImParcel::$states), ['prompt'=>$this->t('All')]),],
		['name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',],
		'postcode',
		'weight',
		'pkg',
		['name' => 'bag_tag', 'value' => '@$data->bag_tag->bag_tag','filter'=>CHtml::textField('ImParcel[bagTag]', $p->bagTag)],
		['name'=>'scan_no','header'=>'unScan','value'=>'$data->pkg - $data->getOutPkg()'],
		['header' => 'Scanned', 'value' => '$data->getOutPkg()', ],
		["header"=>"pallet",'type' => 'raw',"value"=>'CHtml::textfield("pallet".$data->id, @$data->mdata["amzon_pallet"], ["class"=>"pallet_no","style"=>"width:40px;"])',"filter"=>false],
		["header"=>"pallet type",'type' => 'raw',"value"=>'CHtml::dropDownList("pallet_type".$data->id, @$data->mdata["amzon_pallet_type"], ImParcel::$amzonPalletTypes, ["prompt"=>"SELECT","class"=>"pallet_type","style"=>"width:40px;"])',"filter"=>false],
		['header' => 'Time', 'value' => '$data->getScanTime()', ],
		['header'=>'ChargeCode','value'=>'$data->getChargecode()'],
		[
			'class'=>'oButtonColumn',
			'template'=>'{Scanall} {view} {update}',
			'buttons'=>[
				'view' => [
					'imageUrl'=>false,
					'url'=>'Yii::app()->createUrl("imParcel/otScanned", ["id" => $data->id])',
					'options' => ['class' => 'jqm_link grid_view_btn', 'target' => '_blank'],
				],
				'Scanall' => [
					'imageUrl' => false,
					'url' => 'Yii::app()->createURL("imParcel/scanAll", ["id" => $data->id])',
					'visible' => '$data->scan_no > 0',
					'options' => ['class' => 'ajax_link grid_edit_btn ship_scan_all', 'label'=>$this->t('Scan All'), 'title' => '$data->hbn'],
				],
				'update' => [
					'imageUrl' => false,
					'url' => 'Yii::app()->createURL("imParcel/update", ["id" => $data->id])',
					'visible' => 'true',
					'options' => ['class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->hbn', 'data-id' => '$data->id'],
				],
			],
		],
	],
]);
?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	const maniResult = function(r){
		if(r == 'done'){
			myApp.notice('Done', 5000);
		}else{
			myApp.alert(r, false);
		}
		var t = $('#imco-consol-tabs', panel);
		t.tabs('load', t.tabs('option','active'));
	};

	$(panel).off('change', '.pallet_no, .pallet_type').on('change', '.pallet_no, .pallet_type', function(){
		const tr = $(this).parents('tr');
		$.ajax({
			url: '<?=$this->createUrl("imParcel/palletChange")?>',
			type: "post",
			data: {"pallet":$('.pallet_no', tr).val(),"pallet_type":$('.pallet_type', tr).val(),"id":$('.grid_edit_btn', tr).data('id')},
			success: function(r) {
				if(r=='done')
				 {
					myApp.notice('Done', 5000);
				 }else
				 {
					myApp.alert(r, false);   
				 }
			 },
			error: function(e) {
				console.log(e);
			}
		});
	});

	$('.airout_btn', panel).click(function(){
		if(confirm('Are you sure to send Outturn report to ICS?')){
			$(this).hide();
			$.get('<?=$this->createUrl("imcoConsol/airout", ["id" => $model->id]);?>' + '?to_cus=' + $('#to_cus', panel).prop('checked'), maniResult);
		}
		return false;
	});

	$('.aupost_btn', panel).click(function(){
		if(confirm('Are you sure to send AuPost lodgement?')){
			$(this).hide();
			$.get('<?=$this->createUrl("imcoConsol/aupost", ["id" => $model->id]);?>', maniResult);
		}
		return false;
	});

	$('.fastway_btn', panel).click(function(){
		if(confirm('Are you sure to send Fastway lodgement?')){
			$(this).hide();
			$.get('<?=$this->createUrl("imcoConsol/fastway", ["id" => $model->id]);?>', maniResult);
		}
		return false;
	});

	$('.send_scan_btn', panel).click(function(){
		if(confirm('Are you sure to send for scan?')){
			$(this).hide();
			$.get('<?=$this->createUrl("imcoConsol/sendScan", ["id" => $model->id]);?>', maniResult);
		}
		return false;
	});

	$('.toll_btn', panel).click(function(){
		if(confirm('Are you sure to send Toll lodgement?')){
			$(this).hide();
			$.get('<?=$this->createUrl("imcoConsol/tollpost", ["id" => $model->id]);?>', maniResult);
		}
		return false;
	});

	panel.off('click', '.ship_scan_all').on('click', '.ship_scan_all', function(){
		return window.confirm('Scan all?');
	});
	
	  $('.d2z_send_btn', panel).click(function(){
		if(confirm('Are you sure to send D2Z lodgement?')){
			$(this).hide();
			$.get('<?=$this->createUrl("imcoConsol/d2zPost", ["id" => $model->id]);?>', maniResult);
		}
		return false;
	});
		$('.d2z_country_send_btn', panel).click(function(){
		if(confirm('Are you sure to send D2Z Country lodgement?')){
			$(this).hide();
			$.get('<?=$this->createUrl("imcoConsol/d2zCountryManifest", ["id" => $model->id]);?>', maniResult);
		}
		return false;
	});
		$('.lma_send_btn', panel).click(function(){
		if(confirm('Are you sure to send LMA lodgement?')){
			$(this).hide();
			$.get('<?=$this->createUrl("imcoConsol/lmaManifest", ["id" => $model->id]);?>', maniResult);
		}
		return false;
	});

		$('.tnt_btn', panel).click(function(){
		if(confirm('Are you sure to send TNT lodgement?')){
			$(this).hide();
			$.get('<?=$this->createUrl("imcoConsol/tntManifest", ["id" => $model->id]);?>', maniResult);
		}
		return false;
	});
	$('.startrack_btn', panel).click(function(){
		if(confirm('Are you sure to send StarTrack lodgement?')){
			$(this).hide();
			$.get('<?=$this->createUrl("imcoConsol/startrack", ["id" => $model->id]);?>', maniResult);
		}
		return false;
	});
	$('.letter_btn', panel).click(function(){
		if(confirm('Are you sure to send Letter lodgement?')){
			$(this).hide();
			$.get('<?=$this->createUrl("imcoConsol/emps", ["id" => $model->id]);?>', maniResult);
		}
		return false;
	});
	  $('.update_scan_btn', panel).click(function(){
		if(confirm('Are you sure to update the scan')){
			$(this).hide();
			$.get('<?=$this->createUrl("imcoConsol/updateScan", ["id" => $model->id]);?>', maniResult);
		}
		return false;
	});

	$('.hunter_btn', panel).click(function() {
		if (confirm('Are you sure to send Hunter lodgement?')) {
			$(this).hide();
			$.get('<?=$this->createUrl("imcoConsol/hunterManifest", ["id" => $model->id]);?>', maniResult);
		}
		return false;
	});

	$('.d2z_btn', panel).click(function() {
		if (confirm('Are you sure to send D2z lodgement?')) {
			$(this).hide();
			$.get('<?=$this->createUrl("imcoConsol/d2zManifest", ["id" => $model->id]);?>', maniResult);
		}
		return false;
	});

	$('.ubi_btn', panel).click(function() {
		if (confirm('Are you sure to send Ubi lodgement?')) {
			$(this).hide();
			$.get('<?=$this->createUrl("imcoConsol/ubiManifest", ["id" => $model->id]);?>', maniResult);
		}
		return false;
	});

	$('.dfe_btn', panel).click(function() {
		if (confirm('Are you sure to send Dfe lodgement?')) {
			$(this).hide();
			$.get('<?=$this->createUrl("imcoConsol/dfeManifest", ["id" => $model->id]);?>', maniResult);
		}
		return false;
	});

	tab.bind('onOpen', function(){
		$('#aout-grid', panel).yiiGridView('update');
	});

	$('a#scan_all', panel).on('click', function(){
		return window.confirm('Are you sure to scan all?');
	}).on('succes', function(){
		tab.trigger('onOpen');
	});
				
	$("#scan_all",panel).on('success',function(r,data){
		if(data.done==1){
		  myApp.notice('Done', 5000);
	  }else
	  {
		 myApp.notice('Scan All is processing', 5000);
	  }
	});


});
</script>