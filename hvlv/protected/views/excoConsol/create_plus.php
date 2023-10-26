<h1><?=$this->t('Create Export Consol');?></h1>
<style type="text/css">
.cpctn{ padding: 5px; border: 1px #ccc solid; float: left; margin-right: 10px;}
.cpctn.enable { background: #ccffcc; }
</style>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'exco-consol-form',
	'enableAjaxValidation'=>false,
));
$model->pol = 'AUSYD';
$model->pod = 'CNCAN';
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
		foreach(ExChannel::getPocs() as $k=>$p){
			echo '<div class="cpctn"><label><input class="cpcb" type="checkbox" name="cp['.$k.']" value="1" /> '.$p.'</label>Limit Weight: <input class="cpwl" type="text" name="cplm['.$k.']" size="5" />KG<div id="cpi_'.$k.'" class="cpinfo"></div></div>';
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
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	var req = false;
	
	$('form#exco-consol-form', panel).on('submit', function(){
		return window.confirm('Are you ready to create consol.?');
	}).on('success', function(e, r){
		tab.trigger('load');
	});

	var cpinfo = function(){
		var dpt = $('#ExcoConsol_dpt_id', panel).val();
		var exr = $('#ExcoConsol_exrate', panel).val();
		var qs = '';
		if(dpt == ''){
			myApp.alert('Please select depot');
			return false;
		}
		if(exr == ''){
			myApp.alert('Please enter exchange rate');
			return false;
		}
		$('input.cpcb:checked', panel).each(function(){
			var wl = Number($(this).parents('.cpctn').find('.cpwl').val());
			qs += '&'+$(this).attr('name')+'='+wl;
		});
		if(qs == '') return false;
		$('.cpli', panel).show();
		if($('input#otb:checked', panel).length > 0) qs += '&otb=1';
		if($('input#dto:checked', panel).length > 0) qs += '&dto=1';

		if(req) req.abort();
		req = $.get('<?=$this->createUrl("excoConsol/cpSmart");?>?dpt='+dpt+'&exrate='+exr+qs, function(r){
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
				$('#cpi_'+i, panel).html(s);
			}
			$('.cpli', panel).hide();
		}, 'json');
	};

	$('.sortable', panel).sortable().on("sortupdate", cpinfo);
	$('input.cpcb', panel).on('click', function(){
		$('input.cpcb', panel).not(':checked').parents('.cpctn').removeClass('enable').find('.cpinfo').html('');
		$('input.cpcb:checked', panel).parents('.cpctn').addClass('enable');
		cpinfo();
	});
	$('input.cpwl, input#otb, input#dto', panel).on('change', cpinfo);
});
</script>