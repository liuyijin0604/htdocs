<style type="text/css">
.download-menu.alt li > a:hover{
	background-color: #c00;
}
#res .ui-tabs .ui-tabs-nav li a{ padding: 0.2em 1em; }
#res .ui-tabs .ui-tabs-panel { padding:5px; font-size: 0.8em; max-height: 100px; overflow: auto;}
</style>
<div style="position:absolute">
<p>Shipments: <b><?=$model->totShipments();?></b> 
	&nbsp; Total Weight: <b><?=$model->totWeight();?>KG</b> 
	&nbsp; Ship Weight: <b><?=$model->totShipWeight();?>KG</b> 
	&nbsp; Total Wtck: <b><?=$model->totWtck();?>KG</b> 
	&nbsp; Total Packs: <b><?=$model->totPacks();?></b>
	&nbsp; Total PacksTCK: <b><?=$model->totPacksTCK();?></b>
	&nbsp; Total CBM: <b><?=$model->totImCBM();?></b>
	&nbsp; Total CBMTCK: <b><?=$model->totImCBMTCK();?></b>
	<?php
if(!empty($model->mdata['aiz'])) echo 'CRN: <b style="font-size:16px;color: #080;">', $model->mdata['aiz'], '</b>';?>
</p>
</div>
 <?php $url=Yii::app()->request->baseUrl."/consolPort?id=".$model->id."&no=".$model->no."&poc=".$model->poc; ?>

<div style="text-align:right">
<!-- <a href="#" class="grat"><div style="background-position:-96px -384px" class="icon"></div> <?=$this->t('Ratio');?></a> &nbsp;
<a href="#" data-dropdown="#dropdown-1"><div style="background-position:-96px -768px" class="icon"></div> <?=$this->t('Download');?></a> &nbsp; 
<a href="#" data-dropdown="#dropdown-2"><div style="background-position:-48px -688px" class="icon"></div> <?=$this->t('Export Manifest');?></a> -->
<!-- <a id="copy_address" href="<?=$url?>"  ><span class="icon"></span><?=$this->t('Copy Url');?></a> -->
<input type="hidden" id="address_tocopy" value=<?="/consolPort?id=".$model->id."&no=".$model->no."&poc=".$model->poc;?> />
</div>

<div id="dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu download-menu">
		<li><a href="<?=$this->createUrl('szPortal/download', array('id'=>$model->id, 'type'=> 'id'));?>" target="_blank">身份证 正反</a></li>
		<li><a href="<?=$this->createUrl('szPortal/download', array('id'=>$model->id, 'type'=> 'id', 'joint' => 1));?>" target="_blank">身份证 合并</a></li>
		<!--li><a href="<?=$this->createUrl('szPortal/download', array('id'=>$model->id, 'type'=> 'id', 'word' => 1));?>" target="_blank">身份证 Word</a></li-->
		<li><a href="<?=$this->createUrl('szPortal/download', array('id'=>$model->id, 'type'=> 'id_valid'));?>" target="_blank">身份证验证</a></li>
		<li><a href="<?=$this->createUrl('szPortal/download', array('id'=>$model->id, 'type'=> '3in1'));?>" target="_blank">3 合 1</a></li>
		<li><a href="<?=$this->createUrl('szPortal/download', array('id'=>$model->id, 'type'=> 'rcpt'));?>" target="_blank">小票 JPG</a></li>
		<li><a href="<?=$this->createUrl('szPortal/download', array('id'=>$model->id, 'type'=> 'rcpt', 'photo' => 1));?>" target="_blank">小票照片 JPG</a></li>
		<li><a href="<?=$this->createUrl('szPortal/download', array('id'=>$model->id, 'type'=> 'rcpt', 'ft' => 'pdf'));?>" target="_blank">小票 PDF</a></li>
		<!--li><a href="<?=$this->createUrl('szPortal/download', array('id'=>$model->id, 'type'=> 'rcpt', 'ft' => 'doc'));?>" target="_blank">小票 Word</a></li-->
		<li><a href="<?=$this->createUrl('szPortal/download', array('id'=>$model->id, 'type'=> 'label'));?>" target="_blank">快递面单 JPG</a></li>
		<li><a href="<?=$this->createUrl('szPortal/download', array('id'=>$model->id, 'type'=> 'label', 'ft' => 'pdf'));?>" target="_blank">快递面单 PDF</a></li>
		<li><a href="<?=$this->createUrl('szPortal/download', array('id'=>$model->id, 'type'=> 'auth', 'ft' => 'pdf'));?>" target="_blank">授权书 PDF</a></li>
		<li><a href="<?=$this->createUrl('szPortal/download', array('id'=>$model->id, 'type'=> 'pdf'));?>" target="_blank">PCA 面单</a></li>
	</ul>
</div>
<div id="res" style="position:absolute; right: 10px; top: 30px; width: 320px; min-height: 40px; max-height: 120px; overflow: show; display: none;">
</div>
<div id="dropdown-2" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
<?php
	echo '<li><a href="',$this->createUrl('szPortal/export', array('id'=>$model->id, 'type'=> 'TZM')),'" target="_blank">','调整舱单</a></li>';
	echo '<li><a href="',$this->createUrl('szPortal/export', array('id'=>$model->id, 'type'=> 'TZM', 'o' => true)),'" target="_blank">','调整舱单(原始)</a></li>';

	echo '<li><a href="',$this->createUrl('szPortal/export', array('id'=>$model->id, 'type'=> $model->poc)),'" target="_blank">','<b>'.SzpChannel::getName($model->poc).' 舱单</b></a></li>';

	foreach(SzpChannel::getPocs() as $i => $cp){
		if($i == $model->poc) continue;
		echo '<li><a href="',$this->createUrl('szPortal/export', array('id'=>$model->id, 'type'=> $i)),'" target="_blank">', $cp ,' 舱单</a></li>';
	}
	//echo '<li><a href="',$this->createUrl('szPortal/export', array('id'=>$model->id, 'type'=> 'CQYD')),'" target="_blank">','CQ YD Manifest','</a></li>';
	//echo '<li><a href="',$this->createUrl('szPortal/export', array('id'=>$model->id, 'type'=> 'BJODR')),'" target="_blank">','Beijing Order Manifest','</a></li>';
	//echo '<li><a href="',$this->createUrl('szPortal/export', array('id'=>$model->id, 'type'=> 'BJINB')),'" target="_blank">','Beijing Inbound Manifest','</a></li>';
	//echo '<li><a href="',$this->createUrl('szPortal/export', array('id'=>$model->id, 'type'=> 'BJSUM')),'" target="_blank">','Beijing Summary Manifest','</a></li>';
	//echo '<li><a href="',$this->createUrl('szPortal/export', array('id'=>$model->id, 'type'=> 'SZODR')),'" target="_blank">','Shenzhen Order Manifest','</a></li>';
	echo '<li><a href="',$this->createUrl('szPortal/export', array('id'=>$model->id, 'type'=> 'KMEMS')),'" target="_blank">','KM EMS 舱单','</a></li>';
	//echo '<li><a href="',$this->createUrl('szPortal/export', array('id'=>$model->id, 'type'=> 'CZTruck')),'" target="_blank">','CZ Truck Manifest','</a></li>';
	echo '<li><a href="',$this->createUrl('szPortal/export', array('id'=>$model->id, 'type'=> 'Timely')),'" target="_blank">','Timely Report','</a></li>';
	//echo '<li><a href="',$this->createUrl('szPortal/export', array('id'=>$model->id, 'type'=> 'CNZNG')),'" target="_blank">','Zhanjiang Manifest','</a></li>';
	//echo '<li><a href="',$this->createUrl('szPortal/export', array('id'=>$model->id, 'type'=> 'KMEMS-old')),'" target="_blank">','KM Old Manifest','</a></li>';
	//echo '<li><a href="',$this->createUrl('szPortal/export', array('id'=>$model->id, 'type'=> 'QDEMS')),'" target="_blank">','QD EMS Manifest','</a></li>';
	//echo '<li><a href="',$this->createUrl('szPortal/export', array('id'=>$model->id, 'type'=> 'CA2INV')),'" target="_blank">','YP GZ Invoice','</a></li>';
	// echo '<li><a href="',$this->createUrl('szPortal/export', array('id'=>$model->id, 'type'=> 'XAEMS')),'" target="_blank">','西安邮政EMS','</a></li>';
?>
	</ul>
</div>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol-form',
	'enableAjaxValidation'=>false,
));
?>
	<?php echo $form->errorSummary($model); ?>
	<?php
	$enable = "enabled";
	if(@$model->mdata["completeConsol"]==true||$model->status>Consol::CONFIRM_STATUS)
	{
		$enable = "disabled";
	}
	?>
	<div style="padding: 2em;">
		<div class="row rowcol rowleft">
			<?php echo $form->labelEx($model,'awb'); ?>
			<?php echo $form->textField($model,'awb',array('size'=>15,'maxlength'=>50,$enable=>$enable)); ?>
			<?php echo $form->error($model,'awb'); ?>
		</div>

		<div class="row rowcol">
			<?php echo $form->labelEx($model,'flight'); ?>
			<?php echo $form->textField($model,'flight',array('size'=>15,'maxlength'=>50,$enable=>$enable)); ?>
			<?php echo $form->error($model,'flight'); ?>
		</div>

		<div class="row rowcol">
			<?php echo $form->labelEx($model,'exrate'); ?>
			1AUD = <?php echo $form->textField($model,'exrate',array('size'=>10,'maxlength'=>15,$enable=>$enable)); ?> <a href="http://www.customs.gov.au/site/page4277.asp" target="_blank"><div class="icon" style="background-position:-96px -384px"></div></a>
			<?php echo $form->error($model,'exrate'); ?>
		</div>
		<div class="row rowcol rowleft">
			<?php echo $form->labelEx($model,'pol'); ?>
			<?php echo $form->dropDownList($model,'pol', AppHelper::setting2List('pols'),[$enable=>$enable]); ?>
			<?php echo $form->error($model,'pol'); ?>
		</div>

		<div class="row rowcol">
			<?php echo $form->labelEx($model,'pod'); ?>
			<?php echo $form->dropDownList($model,'pod', AppHelper::setting2List('pods'),[$enable=>$enable]); ?>
			<?php echo $form->error($model,'pod'); ?>
		</div>


		<div class="row rowcol">
			<?php echo $form->labelEx($model,'poc'); ?>
			<?php echo $form->dropDownList($model,'poc', SzpChannel::getPocs(true), array('empty' => 'Select One',$enable=>$enable)); ?>
			<?php echo $form->error($model,'poc'); ?>
		</div>

		<div class="row rowcol">
			<?php echo $form->labelEx($model,'etd'); ?>
			<?php echo $form->textField($model,'etd', array('size' => 12, 'id' => 'etd_','class' => 'date_input',$enable=>$enable)); ?>
			<?php echo $form->error($model,'etd'); ?>
		</div>

		<div class="row rowcol">
			<?php echo $form->labelEx($model,'eta'); ?>
			<?php echo $form->textField($model,'eta', array('size' => 12, 'id' => 'eta_','class' => 'date_input',$enable=>$enable)); ?>
			<?php echo $form->error($model,'eta'); ?>
		</div>

		<div class="row rowcol">
			<?php echo CHtml::label($this->t('AWB Weight'),'for_awb_weight'); ?>
			<?php echo CHtml::textField('mdata[awb_check_wt]', @$model->mdata['awb_check_wt'], array('size'=>5,$enable=>$enable)); ?>Kg
		</div>

		<div class="row buttons">
			<input type="hidden" name="confirm" id="confirm"/>
			<?php echo CHtml::submitButton($this->t('Save')); ?>
			<?php if($model->status < 20){
				echo '&nbsp;', CHtml::button($this->t('Confirm'), array('class' => 'confirm'));
			}elseif($model->status == 20){
				if(@$model->mdata["scanComplete"])
				{
					echo '&nbsp;', CHtml::button($this->t('CompleteConsol'), ['class' => 'completeConsol']);
				}else
				{
					echo '&nbsp;', CHtml::button($this->t('Unlock'), ['class' => 'unlock']);
				}
			}?>
		</div>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<?php if($model->status > 0): ?>

<div style="position: absolute;">
<!-- <a href="#" data-dropdown="#dropdown-3"><div class="icon" style="background-position:-192px -80px"></div><?=$this->t('Add/Remove shipments');?></a> -->
<div id="dropdown-3" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-left">
	<ul class="dropdown-menu">
		<li><a href="<?=$this->createURL('szPortal/addParcel', array('id' => $model->id));?>" class="jqm_link"><?=$this->t('Add Shipments');?></a></li>
		<li><a href="<?=$this->createUrl('szPortal/bulkParcels', array('id' => $model->id));?>" class="jqm_link"><?=$this->t('Bulk Actions');?></a></li>
		<li><a id="rmgdcw" href="<?=$this->createUrl('szPortal/removeGdcw', array('id' => $model->id));?>" class="ajax_link"><?=$this->t('Remove GD/CW');?></a></li>
	</ul>
</div>
</div>
	<br/>
<?php
endif;

//map gd
if($model->status < 20){
	foreach($model->shipments as $s){
		if(($s->bwf & 24) > 0){
			$s->cleanItems();
			if($s->mapGoods()) $s->save();
		}
	}
}	

$parcel = new ImParcel('search');
if(isset($_GET['ImParcel'])){
	$parcel->unsetAttributes();
	$parcel->attributes=$_GET['ImParcel'];
}
$parcel->consol_id = $model->id;

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'imParcel-grid',
	'cssFile' => false,
	'dataProvider'=>$parcel->search(),
	'filter'=>$parcel,
	'rowCssClassExpression' => '
		($row%2 ? "odd" : "even" )." ".$data->getColorCls()
	',
	'columns'=>array(
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',),
		'ref',
		array('name' => 'agent_name', 'value' => 'empty($data->agent)? "" : $data->agent->shortName(2)',),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('ImParcel[status]', $parcel->status, $this->t(ImParcel::$states), array('prompt'=>$this->t('All'))),),
		//array('header' => 'Goods', 'type' => 'raw', 'value' => '$data->getGoods()', 'filter' => null),
		'weight',
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
		array(
			'class'=>'oButtonColumn',
			'template'=>'{Swap} {delete}',
			'buttons'=>array(
				'Swap' => array(
					'imageUrl' => false,
					'url' => 'Yii::app()->createURL("szPortal/swapParcel", array("id" => $data->id))',
					'visible' => '$data->consol->status < 20',
					'options' => array('class' => 'jqm_link grid_swap_btn', 'label'=>$this->t('Swap'), 'title' => '$data->hbn'),
				),
				'delete' => array(
					'imageUrl'=>false,
					'visible' => '$data->consol->status < 20',
					'options' => array('class' => 'grid_delete_btn', 'label'=>$this->t('Remove from Consol.')),
					'url' => 'Yii::app()->createURL("szPortal/removeParcel", array("id" => $data->id))',
				),
			),
		),
	),
));
?>

<script type="text/javascript">
$(function(){
	
	$('form#consol-form').on('success', function(e, r){
		if(r.thread > 0){
			var res = $('#res').data('tid', r.thread);
			res.everyTime(1e4, 'threadInfo', function(){
				if(res.data('tid') == undefined) res.stopTime('threadInfo');

				$.getJSON('szPortal/showThread/'+res.data('tid'), function(r){
					if(r.comp){
						res.fadeOut();
						res.stopTime('threadInfo');
						tab.load();
						alert(r.msg, false);
					}else if(r.sub > 0){
						res.data('tid', r.sub);
						alert(r.msg, false);
					}else{
						if(!res.visible) res.fadeIn();
						res.html(r.html);
					}
				});
			});
		}else{
			window.location.reload();
		}
	});

	$('a.grat').on('click', function(e,r){
		$.getJSON('szPortal/goodsRatio/<?=$model->id;?>.app', function(r){
			var mn = {'B1' : 'B 1/2', 'B2': 'B 3/4', 'M': 'M', 'P': '小安素', 'O': 'O', 'U': 'UGG'};
			var sm = {0 : '普通', 10 : 'VIP', 20 : '玄武'};
			var d = '<div id="ratio-tabs"><ul><li><a href="#rtab-1">比例</a></li><li><a href="#rtab-2">税金</a></li><li><a href="#rtab-3">服务</a></li></ul>';
			d += '<div id="rtab-1"><table class="items" style="min-width: 300px"><tbody>';
			for(var i in r.rs){
				d += '<tr><td>'+mn[i]+'</td><td>'+(r.rs[i][0] > 0? r.rs[i][0] : '-')+'</td><td>'+(r.rs[i][1] > 0? Math.round(r.rs[i][1])+'kg' : '-')+'</td><td>'+(r.rs[i][0] > 0 && r.tt > 0? (Math.round(r.rs[i][0] / r.tt * 10000) / 100)+'%' : '-')+'</td><td>'+(r.rs[i][1] > 0 && r.tw > 0? (Math.round(r.rs[i][1] / r.tw * 10000) / 100)+'%' : '-')+'</td></tr>';
			}
			d += '</tbody></table></div>';
			d += '<div id="rtab-2"><table class="items" style="min-width: 280px"><tbody>';
			var tq = tv = 0;
			for(var i in r.rs){
				d += '<tr><td>'+mn[i]+'</td><td>'+(r.rs[i][2] > 0? r.rs[i][2] : '-')+'</td><td>'+(r.rs[i][3] > 0? '￥'+Math.round(r.rs[i][3]) : '-')+'</td><td>'+(r.rs[i][2] > 0 && r.tt > 0? (Math.round(r.rs[i][2] / r.tt * 10000) / 100)+'%' : '-')+'</td></tr>';
				tq += r.rs[i][2];
				tv += r.rs[i][3];
			}
			d += '</tbody><thead><tr><td>Total</td><td>'+tq+'</td><td>￥'+tv+'</td><td>'+(r.tt > 0? (Math.round(tq / r.tt * 10000) / 100)+'%' : '-')+'</td></tr></thead></table></div>';
			d += '<div id="rtab-3">';
			if(r.svs.length > 0){
				var sl = [];
				for(i in r.svs) sl.push(sm[i]+': '+ r.svs[i]);
				d +='<p>'+sl.join(', &nbsp;')+'</p>';
			}
			if(r.dup.length > 0){
				d += '<p style="color:#c00;">重复件('+r.dup.length+'): '+r.dup.join(', ')+'</p>';
			}
			d += '</div></div>';
			$('#res').html(d).fadeIn();
			$('#ratio-tabs').tabs().tabs('refresh');
		});
		return false;
	})<?=$model->status < 20? ".trigger('click')" : '';?>;

	$(document).off('click','#imParcel-grid a.grid_delete_btn');
	

	$('a#rmgdcw').on('click', function(){
		return window.confirm('Are you sure to remove all GD/CW shipments?');
	}).on('success', function(e, r){
		alert('Done!');
		window.location.reload();
	});

	$('input.unlock').off('click').on('click', function(){
		var  cn= prompt("Please enter 'Unlock <?=$model->no;?>'");
		if(cn == 'Unlock <?=$model->no;?>'){
			$.get('<?=$this->createURL('szPortal/unlock',['id'=>$model->id]);?>', function(){
				window.location.reload();
			});
		}
	});

	$('.confirm').off('click').on('click', function(){
		$('#confirm').val("confirm");
		$('#consol-form').submit();
	});

	$('input.completeConsol').off('click').on('click', function(){
		var  cn= prompt("Please enter 'Complete <?=$model->no;?>'");
		if(cn == 'Complete <?=$model->no;?>'){
			$('#consol-form').submit();
			$.get('<?=$this->createURL('szPortal/completeConsol',['id'=>$model->id]);?>', function(){
				window.location.reload();
			});
		}
	});
		
		
	$("#copy_address").on('click',function(e){
		e.preventDefault();
		$.get('szPortal/todoTask?id='+<?=$model->id?>,function(msg){
			if(msg==0){
				var result=confirm('The task already created before, are you sure to create it again?');
				if(result){
					$.get('szPortal/todoTask?id='+<?=$model->id?>+'&create=1');
				}
			}
		});
		
		copyToClipboard();
		notice('Copy Successfully',500);
	});
	
	function copyToClipboard() {
		var $temp = $("<input>");
	
		$("body").append($temp);
		$temp.val("https://os.toplogistics.com.au"+$("#address_tocopy").val()).select();
		document.execCommand("copy");
		$temp.remove();
	}

	function notice(msg, timeout, t) {
		t = t || 'body';
		timeout = timeout || 5000;
		$('<div class="app-notice">'+msg+'</div>').appendTo(t).fadeIn().delay(timeout).fadeOut(500,function(){$(this).remove();});
	}

});
</script>