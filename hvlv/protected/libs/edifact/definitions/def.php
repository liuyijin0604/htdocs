<?php
$dir = strtoupper($argv[1]);
$edsd = $dir.DIRECTORY_SEPARATOR.'EDSD.'.$dir;
$edmd = $dir.DIRECTORY_SEPARATOR.'EDMD'.DIRECTORY_SEPARATOR;

function improved_var_export($variable, $return = false){
    if ($variable instanceof stdClass){
        $result = '(object) '.improved_var_export(get_object_vars($variable), true);
    }elseif(is_array($variable)){
        $array = array ();
        foreach($variable as $key => $value){
            $array[] = var_export($key, true).' => '.improved_var_export($value, true);
        }
        $result = 'array ('.implode(', ', $array).')';
    }else{
        $result = var_export($variable, true);
    }

    if(!$return){
        print $result;
        return null;
    }else{
        return $result;
    }
}

if(is_file($edsd) && !is_file($dir.DIRECTORY_SEPARATOR.'edsd.php')){	//convert EDSD
	$segs = new stdClass;
	$fc = file_get_contents($edsd);
	if(is_file($edsd.'.ext')) $fc .= file_get_contents($edsd.'.ext');
	$sds = preg_split('/[\n\r]+'.chr(196).'+[\n\r]+/', $fc);
	unset($sds[0]);
	//determine EDSD/TRSD format
	$ls = preg_split('/[\n\r]+/', rtrim($sds[1]));
	$edsd = true;
	foreach($ls as $i=>$l){
		if(trim(substr($l, 0, 3)) == '010'){
			if(empty(trim(substr($l, 55, 1)))) $edsd = false;
			break;
		}
	}
	
	foreach($sds as $s){
		$ls = preg_split('/[\n\r]+/', rtrim($s));
		$isfunc = false;
		$isnote = false;
		$sl = array();
		foreach($ls as $i=>$l){
			if($edsd){
				$sl[0] = trim(substr($l, 0, 3));
				$sl[1] = trim(substr($l, 4, 2));
				$sl[2] = trim(substr($l, 7, 4));
				$sl[3] = trim(substr($l, 12, 42));
				$sl[4] = trim(substr($l, 55, 1));
				$sl[5] = (int) trim(substr($l, 57, 4));
				$tl = explode('..', trim(substr($l, 62, 8)));
				$sl[6] = $tl[0];
				$sl[7] = empty($tl[1])? 0 : (int) $tl[1];
			}else{//trsd
				$sl[0] = trim(substr($l, 0, 3));
				$sl[1] = trim(substr($l, 4, 1));
				$sl[2] = trim(substr($l, 6, 4));
				$sl[3] = trim(substr($l, 12, 46));
				$sl[4] = trim(substr($l, 59, 1));
				$sl[5] = 1;
				$tl = explode('..', trim(substr($l, 62, 8)));
				$sl[6] = $tl[0];
				$sl[7] = empty($tl[1])? 0 : (int) $tl[1];
			}
			
			//seg def
			if($i == 0){
				$segs->{$sl[2]} = new StdClass;
				$sg = &$segs->{$sl[2]};
				$sg->desc = trim(substr($l, 12, 55));
				$sg->func = '';
				$sg->note = '';
				$sg->data = array();
				continue;
			}
			
			//determin note start
			if($edsd){
				if($sl[2] == 'Note'){
					$isnote = true;
					continue;
				}
				
				//append notes string
				if($isnote){
					$sg->note .= trim(substr($l, 12, 55)).' ';
					continue;
				}
			}else{
				if(trim(substr($l, 10, 4)) == 'Note'){
					$isnote = true;
					$isfunc = false;
				}
				
				//append notes string
				if($isnote){
					if(!empty($sl[0])){
						$isnote = false;
					}else{
						$sg->note .= trim(substr($l, 16, 55)).' ';
						continue;
					}
				}
			}
			
			//determin function start
			if($sl[2] == 'Func') $isfunc = true;
			
			//append function string
			if($isfunc){
				if(!empty($sl[0])){
					$isfunc = false;
				}else{
					$sg->func .= trim($edsd? substr($l, 17, 50) : substr($l, 16, 55)).' ';
					continue;
				}
			}
			
			//determine data
			$sdex = false;
			if(!empty($sl[0])){
				$pos = ltrim($sl[0], '0');
				$sg->data[$pos] = new stdClass;
				$sd = &$sg->data[$pos];
				$sd->code = $sl[2];
				$sd->def = $sl[3];
				if(empty($sl[4])){
					$sdex = true;
				}else{
					$sd->mc = strtoupper($sl[4]) == 'M';
					$sd->repeat = $sl[5];
					$sd->type = $sl[6];
					$sd->length = $sl[7];
				}
				$sd->comp = array();
				continue;
			}
			
			//append extra data def
			if($sdex){
				$sd->def .= ' '.$sl[3];
				if(!empty($sl[4])){
					$sd->mc = strtoupper($sl[4]) == 'M';
					$sd->repeat = $sl[5];
					$sd->type = $sl[6];
					$sd->length = $sl[7];
					$sdex = false;
				}
				continue;
			}
			
			//determine component
			if(empty($sl[0]) && !empty($sl[2])){
				if(empty($sd)) var_dump($sl);
				$comsize = sizeof($sd->comp);
				$sd->comp[$comsize] = new stdClass;
				$sdc = &$sd->comp[$comsize];
				$sdc->def = $sl[3];
				$sdcex = false;
				if(empty($sl[4])){
					$sdcex = true;
				}else{
					$sdc->mc = strtoupper($sl[4]) == 'M';
					$sdc->repeat = $sl[5];
					$sdc->type = $sl[6];
					$sdc->length = $sl[7];
				}
				continue;
			}
			
			//append extra component def
			if($sdcex){
				$sdc->def .= ' '.$sl[3];
				if(!empty($sl[4])){
					$sdc->mc = strtoupper($sl[4]) == 'M';
					$sdc->repeat = $sl[5];
					$sdc->type = $sl[6];
					$sdc->length = $sl[7];
					$sdcex = false;
				}
				continue;
			}
		}
		$sg->func = trim($sg->func);
		$sg->note = trim($sg->note);
	}
	file_put_contents($dir.DIRECTORY_SEPARATOR.'edsd.php', "<?php\nreturn ".improved_var_export($segs, true).';');
}

//convert EDMD
function noParent($c){
	if(empty($c->children)) return;
	foreach($c->children as $s){
		unset($s->parent);
		noParent($s);
	}
}

foreach(glob($edmd.'*.'.$dir) as $f){
	if(is_file($f)){
		$c = file_get_contents($f);
		$c = preg_split('/Pos\s+Tag Name\s+S\s+R/', $c);
		if(sizeof($c) == 2){
			$c[1] = preg_replace(array('/'.chr(196).'/', '/'.chr(179).'/', '/'.chr(217).'|'.chr(191).'|'.chr(193).'/'), array('-','|','+'), $c[1]);
			$cs = preg_split('/[\n\r]+/', $c[1]);
			$sl = array();
			$msg = new stdClass();
			$msg->path = '/';
			$msg->children = array();
			$sp = &$msg;
			$lv = 0;
			foreach($cs as $l){
				if(empty(trim($l))) continue;
				$sl[0] = ltrim(trim(substr($l, 0, 4)),'0');
				if(empty($sl[0])) continue;
				$sl[1] = trim(substr($l, 7, 3));
				$sl[2] = trim(substr($l, 10, 35));
				$sl[3] = trim(substr($l, 53, 1));
				$sl[4] = trim(substr($l, 57));
				$sl[5] = '';
				if(preg_match('/(\d+)([^\d]+)$/', $sl[4], $m)){
					$sl[4] = $m[1];
					$sl[5] = trim($m[2],' -');
				}
				if($sl[0] == 'Anne') break;
				
				$seg = new stdClass;
				$seg->sq = (int) $sl[0];
				$seg->mc = strtoupper($sl[3]) == 'M';
				$seg->repeat = (int) $sl[4];
				$seg->group = false;
				$seg->path = $sp->path;
				$seg->children = array();
				$seg->parent = false;
				
				if(preg_match('/^\-+ Segment group (\d+)\s+\-+$/', $sl[2], $m)){
					$seg->group = true;
					$sl[1] = 'sg_'.$m[1];
					$seg->parent = $sp;
					$seg->path = $seg->parent->path.$sl[1].'/';
					$sp->children[$sl[1]] = $seg;
					$sp = &$sp->children[$sl[1]];
					continue;
				}elseif(preg_match('/(\++)/', $sl[5], $m)){
					$sp->children[$sl[1]] = $seg;
					$end = strlen($m[1]);
					while($end-- > 0){
						$sp->path = $sp->parent->path;
						$sp = &$sp->parent;
					}
					continue;
				}
				
				$sp->children[$sl[1]] = $seg;
			}
		}else{
			echo $f." Segment table not found!\n";
		}
		noParent($msg);
		file_put_contents($edmd.substr(basename($f),0,6).'.php', "<?php\nreturn ".improved_var_export($msg, true).';');
	}
}