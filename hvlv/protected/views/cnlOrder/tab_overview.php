<style type="text/css">
.vp { float: left;  min-width: 200px; padding: 0 5px; }
.lbl { text-decoration: underline; }
.va { font-size: 1.2em; min-height: 1.5em; }
</style>
<h3>Order</h3>
<?php
foreach($model->attributes as $k => $v){
	if(in_array($k, ['id', 'meta', 'status'])) continue;
	$kv = $k=='status'? 'states' : $k.'s';
	if(isset($model::$$kv)) $v = $model->{'get'.ucfirst($k)}();
	echo '<div class="vp"><div class="lbl">', $model->getAttributeLabel($k), '</div><div class="va">', $v ,'</div></div>', PHP_EOL;
}
?>
<div style="clear:both;margin-bottom:1em;"></div>

<?php
foreach($model->getParties() as $t => $p){
	echo '<h4>'.ucwords(strtolower(str_replace('_', ' ', $t))).'</h4>';
	foreach($p->attributes as $k => $v){
		if(in_array($k, ['id', 'hash', 'meta'])) continue;
		echo '<div class="vp"><div class="lbl">', $p->getAttributeLabel($k), '</div><div class="va">', $v ,'</div></div>', PHP_EOL;
	}
	echo '<div style="clear:both;margin-bottom:1em;"></div>';
}
?>

<h4>Items</h4>
<?php
foreach($model->items as $t => $p){
	foreach($p->attributes as $k => $v){
		if(in_array($k, ['id', 'meta'])) continue;
		echo '<div class="vp"><div class="lbl">', $p->getAttributeLabel($k), '</div><div class="va">', $v ,'</div></div>', PHP_EOL;
	}
	if(!empty($p->mdata['itemOrderLineList'])){
		foreach($p->mdata['itemOrderLineList'] as $itm){
			echo '<div style="clear:both;"></div>';
			foreach($itm as $k2 => $v2){
				echo '<div class="vp"><div class="lbl">', $k2, '</div><div class="va">', $v2 ,'</div></div>', PHP_EOL;
			}
		}
	}
	echo '<div style="clear:both;margin-bottom:1em;"></div>';
}
?>

<h4>Cargo</h4>
<?php
foreach($model->oCargos as $t => $p){
	foreach($p->attributes as $k => $v){
		if(in_array($k, ['id', 'type', 'order_id', 'containerList', 'packageList', 'meta'])) continue;
		echo '<div class="vp vp-200"><div class="lbl">', $p->getAttributeLabel($k), '</div><div class="va">', $v ,'</div></div>', PHP_EOL;
	}
	if(!empty($p->containerList)){
		echo '<div style="clear:both;margin-bottom:1em;"></div>';
		echo '<h4>Containers</h4>';
		$ds = json_decode($p->containerList, true);
		foreach($ds as $itm){
			echo '<div style="clear:both;"></div>';
			foreach($itm as $k2 => $v2){
				echo '<div class="vp"><div class="lbl">', $k2, '</div><div class="va">', $v2 ,'</div></div>', PHP_EOL;
			}
		}
	}
	if(!empty($p->packageList)){
			echo '<div style="clear:both;margin-bottom:1em;"></div>';
		echo '<h4>Packages</h4>';
		$ds = json_decode($p->packageList, true);
		foreach($ds as $itm){
			echo '<div style="clear:both;"></div>';
			foreach($itm as $k2 => $v2){
				echo '<div class="vp"><div class="lbl">', $k2, '</div><div class="va">', $v2 ,'</div></div>', PHP_EOL;
			}
		}
	}
	echo '<div style="clear:both;margin-bottom:1em;"></div>';
}

if(!empty($model->mdata['services'])){
	echo '<h4>Services</h4>', PHP_EOL;
	foreach($model->mdata['services'] as $s){
		echo '<div class="vp"><div class="va">', $s['serviceId'] ,'</div></div>', PHP_EOL;
	}
}
?>