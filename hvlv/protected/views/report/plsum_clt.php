<?php
$ttl = '';
if(preg_match('/^\d+$/', $pt[5])){
	$o = Org::model()->findByPk($pt[5]);
	$ttl = $o->name;
}
echo '<h2>', $ttl, ' ', $pt[1],' : ',$pt[2],'</h2>';
$sql = "SELECT dpmt, org_id, grp1, grp2, gl, SUM(IF(actual_amt > 0 OR acc = 1, actual_amt, amt) * IF(c.type='Direct Costs' OR c.type='Expense', -1, 1)) AS grs, SUM(IF(actual_amt > 0 OR acc = 1, actual_amt,amt) * IF(c.type='Direct Costs' OR c.type='Expense', 0, 1)) AS rev, SUM(amt * IF(c.type='Direct Costs' OR c.type='Expense', 1, 0) * IF(acc = 0, 1, 0)) AS ac, SUM(actual_amt * IF(c.type='Direct Costs' OR c.type='Expense', 1, 0)) AS at, s.weight, s.items FROM pl_ledger p INNER JOIN chargecode c ON p.gl = c.id INNER JOIN shipment s on p.fid = s.id WHERE gl != 98 AND dpt_id = ".$pt[0]." AND date >= '".$pt[1]."' AND date <= '".$pt[2]."' ";
if(!empty($pt[3])) $sql .= 'AND dpmt = '.$pt[3].' ';
if(!empty($pt[5])){
	if(preg_match('/[,\d]+/', $pt[5])){
		$sql .= 'AND org_id IN ('.$pt[5].') ';
	}else{
		$sql .= 'AND org_id = '.$pt[5].' ';
	}
}
$sql .= ' GROUP BY fid';

$rs = Yii::app()->db->createCommand($sql)->queryAll();
$cs = [];
if($pt[3] == 20){
	$cs = ['Type', 'Pcs', 'Weight'];
}
$t = '<div class="grid-view"><table class="items"><thead><tr><th>'.implode('</th><th>', $cs).'</th><th>Rev.</th><th>Acr Cost</th><th>Actl Cost</th><th>GP</th><th>Margin</th></tr></thead><tbody>';
$i = 0;

$sum = [];
foreach(['B1/2', 'B3/4', 'M', 'O', 'UGG', '小安素'] as $k){
	$sum[$k] = ['ext' => [0, 0], 'rev' => 0, 'ac' => 0, 'at' => 0, 'grs' => 0];
}
foreach($rs as $r){
	if($pt[3] == 20){
		$eitems = json_decode($r['items'], true);

		if(empty($eitems['type'])) $typ = 'X';
		else{
			$typs = array_unique($eitems['type']);
			$typ = 'X';
			switch(sizeof($typs)){
				case 1:
					$typ = array_pop($typs);
				break;
				case 2:
					$typ = in_array('O', $typs)? 'X' : 'B';
				break;
			}
		}

		$gm = empty($eitems['g'])? '' : implode('', $eitems['g']);
		$d = 'O';

		if($typ == 'M'){
			if(preg_match('/小安素/', $gm)) $d = '小安素';
			else $d = 'M';
		}elseif($typ == 'B'){
			if(preg_match('/一段|二段|1段|2段/', $gm)) $d = 'B1/2';
			else $d = 'B3/4';
		}else{
			if(preg_match('/UGG|鞋|靴|围巾|Scarf/i', $gm)) $d = 'UGG';
		}

		$sum[$d]['ext'][0]++;
		$sum[$d]['ext'][1] += $r['weight'];
		$sum[$d]['rev'] += $r['rev'];
		$sum[$d]['ac'] += $r['ac'];
		$sum[$d]['at'] += $r['at'];
		$sum[$d]['grs'] += $r['grs'];
	}
}

$tt = ['rev' => 0, 'grs' => 0, 'ac' => 0, 'at' => 0];
$fes = [];
foreach($sum as $n => $s){
	if($pt[3] == 20){
		$s['ext'][1] = round($s['ext'][1]);
	}
	$t .= '<tr><td>'.$n.'</td><td>'.implode('</td><td>', $s['ext']).'</td><td>'.AppHelper::money_format('', $s['rev']).'</td><td>'.AppHelper::money_format('', $s['ac']).'</td><td>'.AppHelper::money_format('', $s['at']).'</td><td>'.AppHelper::money_format('', $s['grs']).'</td><td>'.($s['rev'] > 0? round($s['grs'] / $s['rev'] * 100, 2).'%' : 'n/a').'</td></tr>';
	foreach($tt as $k => $v){
		$tt[$k] += $s[$k];
	}

	foreach($s['ext'] as $k => $v){
		$fes[$k] += $v;
	}
}

echo $t.'</tbody><tfoot><tr><td align="right"><b>Total</b></td><td>'.implode('</td><td>', $fes).'</td><td>'.AppHelper::money_format('', $tt['rev']).'</td><td>'.AppHelper::money_format('', $tt['ac']).'</td><td>'.AppHelper::money_format('', $tt['at']).'</td><td>'.AppHelper::money_format('', $tt['grs']).'</td><td>'.($tt['rev'] > 0? round($tt['grs'] / $tt['rev'] * 100, 2).'%' : 'n/a').'</td></tfoot></table></div>';