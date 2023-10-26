<h1><?=$this->t('Create Export Consol');?></h1>
<style type="text/css">
.cpctn{ padding: 5px; border: 1px #ccc solid; float: left; margin-right: 10px;}
.cpctn.enable { background: #ccffcc; }
</style>

<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
		'New Consol.+',
	),
));
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'szp-consol-form',
	'enableAjaxValidation'=>false,
));
$model->pol = 'CNSHA';
$model->pod = 'AUSYD';
?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>
	
	<?php if(Yii::app()->user->grp == 40 && !Acl::hasAccess('B:Export/AllDepots')):
		$model->dpt_id = Yii::app()->user->org;
		echo $form->hiddenField($model, 'dpt_id');
	else: ?>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'dpt_id'); ?>
		<?php echo $form->dropDownList($model,'dpt_id',Org::dptList(), array('empty' => 'Select One')); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'pol'); ?>
		<?php echo $form->dropDownList($model,'pol', AppHelper::setting2List('pols')); ?>
	</div>
	
	<?php endif; ?>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'exrate'); ?>
		1AUD = <?php echo $form->textField($model,'exrate',array('size'=>10,'maxlength'=>15)); ?> <a href="http://www.customs.gov.au/site/page4277.asp" target="_blank"><div class="icon" style="background-position:-96px -384px"></div></a>
	</div>

	<div class="row">
	<div class="loading cpli" style="position:absolute;display:none;height:16px;width:16px;left:110px;margin-top:-3px;"></div>
	<?php echo CHtml::label('Clearing Ports','scp'); ?>
	<div class="sortable">
	<?php 
		foreach(SzpChannel::getPocs() as $k=>$p){
			$serverStr = CHtml::label('Service Types','selected_rates').'<div>';
                    
            $serviceTypes=ImportChargeCode::$service_types;
            unset($serviceTypes[1]);
            unset($serviceTypes[2]);
            unset($serviceTypes[4]);
            $serverStr.=CHtml::checkBoxList('selected_service'.$k,'', $serviceTypes,array(
                    'template'=>'{input}{label}',
                    'separator'=>'',
                    'labelOptions'=>array(
                        'style'=> 'padding-right:12px;min-width: 60px;float: left;'),
                    'style'=>'float:left;',) );
            $serverStr .="</div>";


			echo '<div class="cpctn"><label><input class="cpcb" type="checkbox" name="cp['.$k.']" value="1" /> '
			.$p.'</label>Limit Weight: <input class="cpwl" type="text" name="cplm['.$k.']" size="5" />KG<div id="cpi_'.$k.'" class="cpinfo"></div><div class="serviceTypes">'.$serverStr.'</div></div>';
		}
	?>
	</div>
	<p style="clear:both;"><small>Checkbox to toggle, and drag to sort</small></p>
	</div>
	
	<div class="row">
		<label><input type="checkbox" id="otb" value="1" disabled /> Use other than best channel</label>
		<label><input type="checkbox" id="dto" value="1" disabled /> Duplicates through other channel</label>
	</div>

	<div class="row buttons">
		<div style="float:right" id="twt"></div>
		<?php echo CHtml::submitButton('Create'); ?>
	</div>

	<div style="padding: 20px 0" id="msg">
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<div id="result">
</div>

<script type="text/javascript">
$(function(){
	var req = false;
	$('form#szp-consol-form').on('submit', function(){
		return window.confirm('Are you ready to create consol.?');
	}).on('success', function(e, r){
		if(r.done==true)
		{
			window.location.href= "<?=$this->createUrl('szPortal/list');?>";
		}else
		{
			alert(r.msg);
		}
		//tab.trigger('load');
	});

	var cpinfo = function(){
		var dpt = $('#szpConsol_dpt_id').val();
		var exr = $('#szpConsol_exrate').val();
		var qs = '';
		if(dpt == ''){
			alert('Please select depot');
			return false;
		}
		if(exr == ''){
			alert('Please enter exchange rate');
			return false;
		}
		$('input.cpcb:checked').each(function(){
			var wl = Number($(this).parents('.cpctn').find('.cpwl').val());
			qs += '&'+$(this).attr('name')+'='+wl;
		});
		if(qs == '') return false;
		$('.cpli').show();
		if($('input#otb:checked').length > 0) qs += '&otb=1';
		if($('input#dto:checked').length > 0) qs += '&dto=1';

		if(req) req.abort();
		req = $.get('<?=$this->createUrl("szPortal/cpSmart");?>?dpt='+dpt+'&exrate='+exr+qs, function(r){
			var tec = 0, twt = 0, id;
			for(var i in r){
				if(i == 'sum'){
					var msg = 'Avail. Packs: <b>'+r[i].tot+'</b><br />Avail. Weight: <b>'+r[i].tow+'kg</b><br />Duplicate: <b>'+r[i].dup+'</b><br />Total Weight: <b>'+(Math.round(twt*100)/100)+'</b>kg<br />Total Extra: ￥<b>'+tec+'</b>';
					if(Object.keys(r[i].noc).length > 0){
						msg += '<br /><br />No channel: <b>'+Object.keys(r[i].noc).length+'</b>';
						for(id in r[i].noc){
							msg += '<br /><a class="tab_link" href="exParcel/update/'+id+'.app" title="'+r[i].noc[id]+'">'+r[i].noc[id]+'</a>';
						}
					}
					if(Object.keys(r[i].wt0).length > 0){
						msg += '<br /><br />0 Weight: <b>'+Object.keys(r[i].wt0).length+'</b>';
						for(id in r[i].wt0){
							msg += '<br /><a class="tab_link" href="exParcel/update/'+id+'.app" title="'+r[i].wt0[id]+'">'+r[i].wt0[id]+'</a>';
						}
					}
					$('#msg').html(msg);
					continue;
				}
				var s = 'Packs: <b>'+r[i].p+'</b> &nbsp; Weight: <b>'+(Math.round(r[i].w*100)/100)+'</b>kg';
				if(r[i].mp > 0){
					s += '<br /> Excluded Packs: '+r[i].mp+' &nbsp; Weight: '+r[i].mw+'kg';
				}
				if(r[i].ec > 0){
					s += '<br /> Extra Cost: ￥<b>'+r[i].ec+'</b> &nbsp; Weight: '+r[i].ew+'kg';
					tec += r[i].ec;
				}
				twt += r[i].w;
				s += '<input type="hidden" name="ps['+i+']" value="'+r[i].ids+'" />';
				$('#cpi_'+i).html(s);
			}
			$('.cpli').hide();
		}, 'json');
	};

	$('.sortable').sortable().on("sortupdate", cpinfo);
	$('input.cpcb').on('click', function(){
		$('input.cpcb').not(':checked').parents('.cpctn').removeClass('enable').find('.cpinfo').html('');
		$('input.cpcb:checked').parents('.cpctn').addClass('enable');
		cpinfo();
	});
	$('input.cpwl, input#otb, input#dto').on('change', cpinfo);
});
</script>