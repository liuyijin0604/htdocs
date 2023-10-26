<div id = "today_held_content">
	<?php echo $this->renderPartial('loading_list', array('url'=>"warehouseProcess/getTodayHeldList")); ?>
</div>

<div class="row">
	<label style="float: left;font-size: 2em;">Today Held Scan:</label>&nbsp;&nbsp;
	<div style="font-size: 1.5em;"><input style="width: 95%" id="heldCheckScanBarcode<?=$_GET['tabid']?>" type="text" size="30" name="barcode" autocomplete="off" value="<?=@$barcode?>" /></div>
	<?=CHtml::hiddenField('checkedShipment','',array("id"=>'checkedShipment'.$_GET['tabid'],"style"=>"width:1px;"));?>
</div>
<div class="row" id ="wroning<?=$_GET['tabid']?>" style="font-size: 48px;color:red;">

</div>
<?php 
echo CHtml::htmlButton('Save Scan Check',array("id"=>"save_scan_check".$_GET['tabid']));
?>
<?php 
$this->widget('application.extensions.booster.TbExtendedGridView', [
	'fixedHeader'=>true,
  'type'=>'striped bordered',
  'headerOffset'=>40,
  'responsiveTable'=>true,
  'template' => "{summary}\n{items}\n{pager}",
	'id'=>'export_held_shipments_scan_check'.$_GET['tabid'],
	'cssFile' => false,
	'dataProvider'=>$dataProvider,
	'filter'=>$filter,
	'columns'=>[
		'hbn',
		'ref',
		array('header'=>'Agent','name'=>'agent_name','value'=>'$data->agent->name'),
		array('name' => 'status', 'value' => '$data->getStatus()'),
		'weight',
		'pkg',
		array('name' => 'Scanned','header'=>'Check-In Scan','value'=>'$data->heldScanNumType==0?$data->getOutPkg():$data->palletNum'),
		array('header' => 'rack','type'=>'raw','value' => '$data->getRackName(false,true)'),
		array('name' => 'Scan Check','header'=>'Held Check Scan','type'=>'raw','value' => 'Chtml::numberField($data->id.$_GET["tabid"],$data->heldScanNum,["title"=>$data->id,"class"=>"scanCheckField","style"=>"width:60px;"]).Chtml::hiddenField("checkedShipment".$data->id.$_GET["tabid"],$data->scanedHeldBarcodes).Chtml::hiddenField("log".$data->id.$_GET["tabid"],$data->scanedHeldBarcodesLog)'),
		array('name' => 'diff','header'=>'Diff','type'=>'raw','value' => 'Chtml::numberField("diff".$data->id.$_GET["tabid"],($data->heldScanNumType==0?$data->getOutPkg():$data->palletNum)-$data->heldScanNum,["title"=>$data->id,"class"=>"diffField","style"=>"width:60px;"]).Chtml::hiddenField("cis".$data->id.$_GET["tabid"],($data->heldScanNumType==0?$data->getOutPkg():$data->palletNum)-$data->heldScanNum)')
	],
]);

?>

<script type="text/javascript">
	$('#heldCheckScanBarcode<?=$_GET['tabid']?>').keydown(function(event)
	{
		if(event.keyCode ==13){
			barcode = $('#heldCheckScanBarcode<?=$_GET['tabid']?>').val();
		    $.ajax({
                url: '<?=$this->createUrl("warehouseProcess/heldCheckScanBarcode")?>'+'?barcode='+barcode,
                type: "post",
                data: [],
                dataType:"json",
                contentType: false,
                success: function(rs) {
                	code = "";
                	if(rs.code==undefined)
                	{
                		for(let i=0;i<rs.length;i++)
                		{
	                			let r = rs[i];
	                			if(r.code!="not_found")
				                {
				                	var checkShipmentsId = '#checkedShipment'+r.id+'<?=$_GET['tabid']?>';
				                	var checkShipments = $(checkShipmentsId).val();
				                	if(checkShipments.indexOf(r.barcode)<0)
				                	{
					                	var textField = r.id+'<?=$_GET["tabid"]?>';
					                	var value = $('#'+textField).val()*1;
					                	$('#'+textField).val(value+1);
					                	$(checkShipmentsId).val($(checkShipmentsId).val()+","+r.barcode);
					                	$('#wroning<?=$_GET['tabid']?>').html(r.msg);
					                	var scanLog = $('#log'+r.id+'<?=$_GET['tabid']?>').val();
					                	if(scanLog=="")
					                	{
					                		scanLog = [];
					                	}else
					                	{
					                		scanLog = jQuery.parseJSON(scanLog);
					                	}
					                	var thisScanLog = {"barcode":r.barcode,"time":getNowFormatDate()};
					                	scanLog.push(thisScanLog);
					                	$('#diff'+r.id+'<?=$_GET['tabid']?>').val($('#cis'+r.id+'<?=$_GET['tabid']?>').val()-$('#'+textField).val());
					                	$('#log'+r.id+'<?=$_GET['tabid']?>').val(JSON.stringify(scanLog));
				                	}else
				                	{
				                		$('#wroning<?=$_GET['tabid']?>').html(r.msg);
				                		code = r.code;
				                	}


				                }else
				                {
				                	$('#wroning<?=$_GET['tabid']?>').html(r.msg);
				                }
				                code = r.code;
                		}
                	}else
                	{
                		let r = rs;
	                	console.log(r);
		                if(r.code!="not_found"&&r.code!="not_found_pallet"&&r.code!="not_found_pallet_is_empty")
		                {
		                	var checkShipmentsId = '#checkedShipment'+r.id+'<?=$_GET['tabid']?>';
				              var checkShipments = $(checkShipmentsId).val();
		                	if(checkShipments.indexOf(r.barcode)<0)
		                	{
			                	var textField = r.id+'<?=$_GET["tabid"]?>';
			                	var value = $('#'+textField).val()*1;
			                	$('#'+textField).val(value+1);
			                	$(checkShipmentsId).val($(checkShipmentsId).val()+","+r.barcode);
			                	$('#wroning<?=$_GET['tabid']?>').html(r.msg);
			                	var scanLog = $('#log'+r.id+'<?=$_GET['tabid']?>').val();
			                	if(scanLog=="")
			                	{
			                		scanLog = [];
			                	}else
			                	{
			                		scanLog = jQuery.parseJSON(scanLog);
			                	}
			                	var thisScanLog = {"barcode":r.barcode,"time":getNowFormatDate()};
			                	scanLog.push(thisScanLog);
			                	$('#diff'+r.id+'<?=$_GET['tabid']?>').val($('#cis'+r.id+'<?=$_GET['tabid']?>').val()-$('#'+textField).val());
			                	$('#log'+r.id+'<?=$_GET['tabid']?>').val(JSON.stringify(scanLog));
			                	code = r.code;
		                	}else
		                	{
		                		$('#wroning<?=$_GET['tabid']?>').html(r.msg);
		                		code = r.code;
		                	}

		                }else
		                {
		                	$('#wroning<?=$_GET['tabid']?>').html(r.msg);
		                	code = r.code;
		                }
	              	}

                var audio=new Audio();
								audio.src='https://os.toplogistics.com.au/site/voice/' + code + '.mp3';
								audio.play();
                },
                error: function(e) {
                    console.log(e);
                }
            });
            $('#heldCheckScanBarcode<?=$_GET['tabid']?>').val("");
            return false;
		}
	})
	 function getNowFormatDate() {
      var date = new Date();
      var seperator1 = "-";
      var seperator2 = ":";
      var month = date.getMonth() + 1;
      var strDate = date.getDate();
      if (month >= 1 && month <= 9) {
          month = "0" + month;
      }
      if (strDate >= 0 && strDate <= 9) {
          strDate = "0" + strDate;
      }
      var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
              + " " + date.getHours() + seperator2 + date.getMinutes()
              + seperator2 + date.getSeconds();
      return currentdate;
  }

	$("#save_scan_check<?=$_GET['tabid']?>").on('click',function(){
		if(!confirm("Are you sure to save the scan check record?")) return false;
		var scanCheckObject = $(".scanCheckField");
		var data = [];
		var scoSize = scanCheckObject.length - 1;
		for (var i = 0; i <= scoSize; i++) {
			var sObj = $("#"+scanCheckObject[i].id);
			var id = sObj.attr("title");
			var scanCheckData = sObj.val();
			var scanLog = $('#log'+id+'<?=$_GET['tabid']?>').val();
			var diff = $('#diff'+id+'<?=$_GET['tabid']?>').val();
			var combo = {"id":id,"scanCheckData":scanCheckData,"scanLog":scanLog,"diff":diff}
			data.push(combo);
		}
		if(data!=[])
		{
			$.ajax({
	                url: '<?=$this->createUrl("warehouseProcess/saveHeldScanCheck")?>',
					type: "post",
					data: {"data":data},
					dataType: 'json',
	                success: function(r) {
	                	alert("success");
	                },
	                error: function(e) {
	                    alert("success");
	                }
	            });
		}
	});

</script>
