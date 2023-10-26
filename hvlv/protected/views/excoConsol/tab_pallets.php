<style type="text/css">
	ul.sortables { list-style:none; min-height:20px; border:1px dashed #ccc; margin-bottom: 10px; clear:both; }
</style>
<div style="position:absolute; right:20px;">
<a href="<?=$this->createUrl('excoConsol/mvPallet', array('id'=>$model->id));?>" class="jqm_link"><div style="background-position:-64px -480px" class="icon"></div> Move Pallets</a>
 &nbsp;
<a href="<?=$this->createUrl('excoConsol/palletRpt', array('id'=>$model->id));?>" target="_blank"><div style="background-position:-96px -768px" class="icon"></div> Pallets Report</a>
 &nbsp;
<a href="<?=$this->createUrl('excoConsol/pltMark', array('id'=>$model->id));?>" target="_blank" class="plt_mark"><div style="background-position:-240px -688px" class="icon"></div> Pallets Mark</a>
</div>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'scon-form',
	'enableAjaxValidation'=>false,
));
?>
<div id="subcons">
</div>
<!--div class="row buttons">
	<?php echo CHtml::submitButton('Save', ['id' => 'scsave_btn']); ?>
	<button type="button" id="nsc_btn" style="display:none;">New Sub Consol</button>
</div-->
<?php $this->endWidget(); ?>
</div><!-- form -->
<?php
$rs = Manifest::model()->findAll('type = 60 AND consol_id = :cid', [':cid' => $model->id]);
if(!empty($rs)){
	$tc = 0;
	$tw = 0;
	$sids = [];
	echo '<ul id="oth_pallets" class="sortables" style="list-style:none;">';
	foreach($rs as $m){
		$w = 0;
		$ids = empty($m)? [] : $m->getFids();
		//$w = $m->totWeight();
		$w = $m->totExShipWeight();
		$c = sizeof($ids);
		if(empty($c)) continue;
		echo '<li data-mid="'.$m->id.'">Pallet #'.$m->ref.' &nbsp; Count: '.$c.' Weight: '.(round($w*100)/100).'kg';
		if(!empty($m->mdata['mvfrom'])){
			$oc = ExcoConsol::model()->findByPk($m->mdata['mvfrom']);
			if(!empty($oc)) echo ' (Plt#'.$m->mdata['opltno'].' from '.$oc->no.')';
		}
		echo '</li>';
		$sids = array_merge($sids, $ids);
		$tc += $c;
		$tw += $w;
	}
	echo '</ul>';
	echo '<p><b>Total: '.$tc.' shipments, '.$tw.'kg</b></p>';

	$w = 0;
	$li = '';
	$ids = [];
	foreach($model->shipments as $s){
		$ids[] = $s->id;
		if(!in_array($s->id, $sids)){
			$li .= '<li>Missing: <a href="'.Yii::app()->createURL("exParcel/update", array("id" => $s->id)).'" class="tab_link" title="'.$s->hbn.'">'.$s->hbn.'</a> / <a href="'.Yii::app()->createURL("excoConsol/removeParcel", array("id" => $s->id)).'" class="remove" >remove from consol.</a></li>';
			$w++;
		}
	}

	if($w < 200) echo '<br /><ul style="color:#c00; list-style:none">', $li, '</ul>';

	if($w > 0){
		echo '<p><a href="'.Yii::app()->createURL("excoConsol/palletClearParcels", array("id" => $model->id)).'" class="remove_all" >Remove all from consol.</a></p>';
	}

	$li = '';
	foreach($sids as $id){
		if(!in_array($id, $ids)){
			$p = ExParcel::model()->findByPk($id);
			$li .= '<li>Overload: <a href="'.Yii::app()->createURL("exParcel/update", array("id" => $id)).'" class="tab_link" title="'.$p->hbn.'">'.$p->hbn.'</a></li>';
		}
	}
	if(!empty($li)) echo '<br /><ul style="color:#c00; list-style:none">', $li, '</ul>';
}
/*try get ECN from PCA
if(!empty($model->mdata['subc'])){
	$got = false;
	foreach($model->mdata['subc'] as $i => $s){
		if(empty($s['ecn'])){
			try{
				$sql = "SELECT meta FROM priority_hvlv.consol WHERE id = ".$model->id;
				$m = Yii::app()->db->createCommand($sql)->queryScalar();
				if($m){
					$m = json_decode($m, true);
					if(!empty($m['subc'][$i]['ecn'])){
						$model->mdata['subc'][$i]['ecn'] = $m['subc'][$i]['ecn'];
						$got = true;
					}
				}
			}catch(Exception $e){
				
			}
		}
	}
	if($got) $model->save();
}*/
?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	var scons = <?=empty($model->mdata['subc'])? '{}' : json_encode($model->mdata['subc']);?>;

	var Subcon = {
		init: function(){
			var that = this;
			for(i in scons){
				this.add(scons[i]);
			}
			$('#nsc_btn', panel).on('click', function(){
				that.add();
				that.dnd();
				that.canSave();
			});
			this.canAdd();
			this.canSave();
			this.dnd();
		},
		canAdd: function(){
			if($('#oth_pallets li', panel).length > 0){
				$('#nsc_btn', panel).show();
				return true;
			}
			$('#nsc_btn', panel).hide();
			return false;
		},
		canSave: function(){
			if($('#subcons .subc', panel).length > 0){	
				$('#scsave_btn', panel).show();
				return true;
			}
			$('#scsave_btn', panel).hide();
			return false;
		},
		add: function(c){
			var i = $('#subcons .subc', panel).length;
			var sc = $('<div class="subc"><h3>Sub Consol. '+(i+1)+'</h3><div class="row rowcol rowleft"><label>AWB No.</label><input class="awb" type="text" name="subc['+i+'][awb]" maxlength="50" size="15" /></div><div class="row rowcol"><label>Flight No.</label><input class="flight" type="text" name="subc['+i+'][flight]" maxlength="50" size="15"></div><div class="row rowcol"><label>ETD</label><input class="date_input etd" type="text" name="subc['+i+'][etd]" size="12"></div><div class="row rowcol"><label>ECN</label><input class="ecn" type="text" name="subc['+i+'][ecn]" size="12" readonly></div><ul class="sortables"></ul><input class="pids" type="hidden" name="subc['+i+'][pids]" /></div>');
			if(c){
				var ept = true;
				for(var k in c){
					$('.'+k, sc).val(c[k]);
					if(c[k] != '') ept = false;
				}
				if(ept) return false;
				var pids = c.pids.split(',');
				$('#oth_pallets li', panel).each(function(){
					if($.inArray($(this).data('mid').toString(), pids) > -1){
						$(this).appendTo($('.sortables', sc));
					}
				});
			}
			$('#subcons', panel).append(sc);
		},
		dnd: function(){
			var that = this;
			$('ul.sortables', panel).sortable({ connectWith: 'ul.sortables', stop: function(evt,ui){
				that.canAdd();
			}});
		}
	};

	//bind reload_tab
	tab.off('reload_tab').on('reload_tab', function(){
		var t = $('.ui-tabs', panel);
		t.tabs('load', t.tabs('option','active'));
	});

	$('#scon-form', panel).on('beforeSerialize', function(){
		$('.subc', panel).each(function(){
			var pids = [];
			$('.sortables li', this).each(function(){
				pids.push($(this).data('mid'));
			});
			$('.pids', this).val(pids.join(','));
		});
	});

	Subcon.init();

	$('a.remove', panel).click(function(){
		if(window.confirm("Are you sure to remove this from consol?")){
			var that = $(this);
			$.get($(this).attr('href'), function(){
				that.parents('li').remove();
			});
		}
		return false;
	});

	$('a.remove_all', panel).click(function(){
		if(window.confirm("Are you sure to remove all from consol?")){
			var that = $(this);
			$.get($(this).attr('href'), function(){
				var t = $('.ui-tabs', panel);
				t.tabs('load', t.tabs('option','active'));
			});
		}
		return false;
	});

	$('a.plt_mark', panel).off('click').on('click', function(){
		var cn= prompt("Skip pallet? (e.g. 03,07)");
		var u = $(this).attr('href').replace(/\?.+$/,'');
		$(this).attr('href', u+'?excl='+cn);
	});

});
</script>