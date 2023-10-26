<h1><?=$this->t('Add Shipment');?></h1>
<div class="form" style="min-height: 300px;">
	<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol-addparcel-form',
	'enableAjaxValidation'=>false,
));
?>
	<label><input type="checkbox" id="otb" name="otb" value="1" /> include other rates</label>
	<label>Weight Limit:</label><input type="text" id="wtltd" value="" />
	<div class="grid-view">
	<table class="items">
<thead>
<tr>
<th id="excon-man-grid_c0"><input type="checkbox" name="recs_all" id="chkbox_all"></th><th><?=$this->t('Connote');?></th><th><?=$this->t('Agent Name');?></th><th><?=$this->t('Weight');?></th><th><?=$this->t('Type');?></th><th><?=$this->t('Goods');?></th><th><?=$this->t('Cnor Name');?></th><th><?=$this->t('Cnee Name');?></th><th><?=$this->t('Warning');?></th><th><?=$this->t('Extra');?></th></tr>
<tr class="filters">
<td>&nbsp;</td><td><input type="text" maxlength="50" name="ExParcel[hbn]" /></td><td><input type="text" name="ExParcel[agent_name]" /></td><td><input type="text" maxlength="10" name="ExParcel[weight]" /></td><td><input type="text" maxlength="10" name="ExParcel[type]" /></td><td><input type="text" maxlength="10" name="ExParcel[prod]" /></td><td><input type="text" name="ExParcel[cnor_name]" /></td><td><input type="text" name="ExParcel[cnee_name]" /></td><td><?php echo CHtml::dropDownList('ExParcel[bwf]', '', $this->t(ExParcel::$bwfs), array('prompt'=>$this->t('All'))); ?></td><td>&nbsp;</td></tr>
</thead>
<tbody>
<?php
ini_set('memory_limit','2G');
switch($model->pol){
	case 'AUSYD':
		$dpts = [106,218,529,530];
		$odpt = 106;
	break;
	case 'AUMEL':
		$dpts = [218];
		$odpt = 218;
	break;
	case 'AUBNE':
		$dpts = [530];
		$odpt = 530;
	break;
}
$rs = ExParcel::model()->findAll('status = 18 AND odpt_id IN ('.implode(',', $dpts).')');
// $rs = ExParcel::model()->with(array(
// 	'manif' => array('with' => 'invoice')
// ))->findAll('t.status = 18 AND t.odpt_id IN ('.implode(',', $dpts).') AND invoice.status IN (8,9)');
$cs = ExChannel::model()->findAll('status = 50');
$dup_allowed = [];
foreach($cs as $c){
	if(!empty($c->mdata['dupq']) && $c->mdata['dupq'] > 1)
		$dup_allowed[$c->code] = $c->mdata['dupq'];
}
//if(in_array($model->poc, $nonly)){
	$rs2 = ExParcel::model()->with('cnee')->findAll("status IN (12, 15) AND odpt_id IN (".implode(',', $dpts).")");
	// $rs2 = ExParcel::model()->with(array(
	// 	'cnee',
	// 	'manif' => array('with' => 'invoice')
	// ))->findAll("t.status IN (12, 15) AND t.odpt_id IN (".implode(',', $dpts).") AND invoice.status IN (8,9)");
	if(!empty($rs2)) $rs = array_merge($rs, $rs2);
//}
$i = 1;
$ecp = [];
$epp = [];
if(empty($_GET['max_pkg']) && $model->poc == 'CNCTU') $_GET['max_pkg'] = 30;
$dupool = ['id' => [], 'idno' => [], 'tel' => [], 'fa' => []];
function isDup(&$dp, $p){
	$vks = ['id' => $p->cnee->cnid_id, 'idno' => empty($p->cnee->cnid_id)? $p->cnee->cnid_no : $p->cnee->cnid->no, 'tel' => $p->cnee->tel, 'fa' => md5($p->cnee->fullAddress().$p->cnee->name)];
	$vcs = [];
	foreach($dp as $k => $l){
		if(empty($vks[$k])) continue;

		$ac = array_count_values($l);
		$vcs[$k] = isset($ac[$vks[$k]])? $ac[$vks[$k]] : 0;

		$dp[$k][] = $vks[$k];
	}

	return empty($vcs)? 0 : max($vcs);
}

function rcvd($pid){
	$sql = 'SELECT dt FROM tracking WHERE pid = :id AND `type` IN (14,15,18) GROUP BY pid ORDER BY dt ASC';
	$dt = Yii::app()->db->createCommand($sql)->bindValues([':id' => $pid])->queryScalar();
	return ceil((time() - strtotime($dt)) / 86400) - 1;
}
foreach($model->shipments as $p) isDup($dupool, $p);
$_GET['dpt'] = $odpt;

$_GET['addparcel_test'] = 1;
foreach($rs as $p){
	if(empty($p->agent) || in_array($p->agent_id, [672])) continue;
	if(!empty($p->mdata['nsn']) && $p->mdata['nsn'] > time()) continue;
	if($odpt != $p->odpt_id || in_array($_GET['dpt'], [218, 530])){
		$l = $p->getLocation(true);
		if(empty($l) || $l->wid != $odpt) continue;
	}
	/*$l = $p->getLocation(true);
	if(in_array($odpt, [106,218])){
		if(empty($l) || $l->wid != $odpt){
			continue;
		}
	}elseif($odpt == 530){
		if(!empty($l)) continue;
	}*/

	$brs = $p->bestRates();
	$pc = 0;
	$cst = 0;
	foreach($brs as $k=>$v){
		if($v == 999) break;
		if($k == $model->poc){
			if(isDup($dupool, $p) < (empty($dup_allowed[$k])? 1 : $dup_allowed[$k])){
				$ec = $cst > 0 && $v > $cst;
				if($ec){
					$ecp[$p->id] = round(($v - $cst) * 100) / 100;
					$epp[$p->id] = $p;
				}else{
					echo '<tr class="'.($i++ & 1 > 0? 'odd' : 'even').'"><td align="center"><input type="checkbox" value="'.$p->id.'" name="recs[]" class="chkbox" /></td><td>'.$p->hbn.'</td><td>'.$p->agent->shortName(2).'</td><td>'.$p->weight.'</td><td>'.$p->goodsType().'</td><td>'.$p->GoodsNames().'</td><td>'.$p->cnor->name.'</td><td>'.$p->cnee->name.'</td><td>'.$p->getWarnings().'</td><td>&nbsp;</td></tr>';
				}
				break;
			}
		}
		if($pc == 0) $cst = $v;
		$pc++;
	}
}
asort($ecp);
foreach($ecp as $k=>$e){
	$p = $epp[$k];
	if($p->weight > 0 && ($e/$p->weight) > (empty($_GET['max_pkg'])? 5 : $_GET['max_pkg'])) continue;
	echo '<tr class="'.($i++ & 1 > 0? 'odd' : 'even').' exc"><td align="center"><input type="checkbox" value="'.$p->id.'" name="recs[]" class="chkbox" /></td><td>'.$p->hbn.'</td><td>'.$p->agent->shortName(2).'</td><td>'.$p->weight.'</td><td>'.$p->goodsType().'</td><td>'.$p->cnor->name.'</td><td>'.$p->cnee->name.'</td><td>'.$p->getWarnings().'</td><td>'.$e.'</td></tr>';
}
?>
</tbody>
</table>
</div>
	<div class="row buttons">
		<div style="float:right" id="twt"></div>
		<?php echo CHtml::submitButton($this->t('Add')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	
	$('form#consol-addparcel-form', win).on('success', function(e, r){
		win.data('opener').trigger('update-parcels-grid');
		win.jqmHide();
	});

	var totWeight = function(){
		var t = 0;
		$('input.chkbox:checked', win).each(function(){
			t += Number($($(this).parents('tr').find('td')[3]).text());
		});
		t = Math.round(t*100)/100;
		$('#twt', win).html('Total weight: <b>'+t+'</b>kg');
	};

	$('input#otb', win).on('click', function(){
		$('input.chkbox, input#chkbox_all', win).prop('checked', false);
		if($(this).is(':checked')){
			$('table.items tr.exc', win).show();
		}else{
			$('table.items tr.exc', win).hide();
		}
	});

	$('table.items tr.exc', win).hide();

	$('.filters input', win).on('change', function(){
		$('input.chkbox, input#chkbox_all', win).prop('checked', false);
		$('table.items tbody tr', win).show();
		if(!$('input#otb', win).is(':checked'))  $('table.items tbody tr.exc', win).hide();
		$('.filters input', win).each(function(i){
			var q = $(this).val().toLowerCase();
			if(q == '') return;
			var nth = $(this).parent().prevAll().length;
			$('table.items tbody tr:visible', win).each(function(){
				var t = $('td',this).eq(nth).text().toLowerCase();
				if(q.indexOf('>') === 0){
					var v = Number(q.substr(1));
					if(Number(t) <= v) $(this).hide();
				}else if(q.indexOf('<') === 0){
					var v = Number(q.substr(1));
					if(Number(t) >= v) $(this).hide();
				}else if(t.indexOf(q) === -1){
					$(this).hide();
				}
			});
		});
	});
	
	win.off('totWeight').on('totWeight', totWeight).trigger('totWeight');

	win.off('click').on('click', 'input#chkbox_all', function(r){
		$('input.chkbox:visible', win).prop('checked', $(this).is(':checked'));
		totWeight();
	}).on('click', 'input.chkbox', totWeight);

	$('#wtltd', win).change('change', function(){
		var wl = Number($(this).val());
		if(wl == 0) return false;
		var tw = 0;
		$('input.chkbox, input#chkbox_all', win).prop('checked', false);
		$('input.chkbox:visible', win).each(function(){
			if(tw > wl) return false;
			tw += Number($($(this).parents('tr').find('td')[3]).text());
			$(this).prop('checked', true);
		});
		totWeight();
	});

});
</script>