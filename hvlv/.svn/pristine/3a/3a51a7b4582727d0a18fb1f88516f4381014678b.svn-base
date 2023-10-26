<?php
//$a needs to be consequtive days;
function dailyStorage($a, $free = 0, $ppd = 1, $debug = 0){
	$p = [];
	$i=0;
	$fee = 0;
	foreach($a as $d => $s){
		$p[$d] = $s[0];
		while($s[1] < 0){
			if($p[$i] == 0) $i++; //mark finished batch
			if($p[$i] < abs($s[1])){
				$s[1] += $p[$i];
				$p[$i] = 0;
			}else{
				$p[$i] += $s[1];
				$s[1] = 0;
			}
		}
		foreach($p as $k=>$v){
			if($k > ($d - $free) || $v == 0) continue;
			$fee += $v * $ppd;
			if($debug) echo 'D'.($d+1).':P'.$k.': '.$v.' x '.$ppd."\n";
		}
	}
	if($debug) echo 'Total Daily Storage: $'.$fee."\n";
	return $fee;
}

function weeklyStorage($a, $free = 0, $ppd = 1, $debug = 0){
	$p = [];
	$i=0;
	$fee = 0;
	foreach($a as $d => $s){
		$p[$d] = $s[0];
		if($d % 7 == 0) $ctd = [];
		while($s[1] < 0){
			if($p[$i] == 0) $i++; //mark finished batch
			if($p[$i] < abs($s[1])){
				$s[1] += $p[$i];
				$p[$i] = 0;
			}else{
				$p[$i] += $s[1];
				$s[1] = 0;
			}
		}
		foreach($p as $k=>$v){
			if($k > ($d - $free * 7) || $v == 0 || in_array($k, $ctd)) continue;
			$ctd[] = $k;
			$fee += $v * $ppd;
			if($debug) echo 'D'.($d+1).':P'.$k.': '.$v.' x '.$ppd."\n";
		}
	}
	if($debug) echo 'Total Weekly Storage: $'.$fee."\n";
	return $fee;
}

function valid($a, $ppd = 1, $debug = 0){
	$t = 0;
	$fee = 0;
	foreach($a as $d => $s){
		$t += $s[0] + $s[1];
		$fee += $t * $ppd;
		if($debug) echo 'D'.$d.':'.$t."\n";
	}

	if($debug) echo 'Valid total: '.$fee."\n";
	return $fee;
}

// for($i = 0; $i < 100; $i++){
/*	$a = [];
	$t = 0;
	for($d = 0; $d < rand(10,100); $d++){
		$r = rand(0, 200);
		$m = $t == 0? $r : $t;
		$s = [$r, rand(0, $m) * -1];
		$t += $s[0] + $s[1];
		$a[] = $s;
	}*/
$a = [
[100, -40],
[100, -40],
[0, -100],
[200, 0],
[0, 0],
[0, 0],
[0, 0],
[0, 0],
[0, 0],
];
	//$v = valid($a,1,1);
	$c = dailyStorage($a,0,1,1);
	$c = weeklyStorage($a,1,1,1);
	//echo ($c == $v? 'T': 'F'),"\t", $c, ' = ', $v, "\n";
// }

/*
Option 2 (backwards calc)
1. Current stock, each IN batch
2. Count days from IN/Invoice date
3. +OUT movements in the calc period

*/
