<?php
class modiPDF{
	public $head, $tail, $xref_idx, $pgs_idx;
	public $objs = [], $omap = [];

	public function __construct($f){
		$content = file_get_contents($f, FILE_BINARY);
		preg_match_all('/(\d+ \d+ obj\n+)(.+?)(\n+endobj[\n\r]*)/ms', $content, $m);
		if(empty($m)) die('Incorrect PDF Format');
		$this->head = substr($content, 0, strpos($content, $m[1][0]));
		$p = strlen($this->head);
		foreach($m[1] as $i => $s){
			$len = strlen($m[1][$i].$m[2][$i].$m[3][$i]);
			$this->objs[$i] = [$m[1][$i], $m[2][$i], $m[3][$i], $p, $len];
			if(preg_match('/Type\/XRef/i', $m[2][$i])) $this->xref_idx = $i;
			if(preg_match('/Type\/Pages/i', $m[2][$i])) $this->pgs_idx = $i;
			$this->omap[preg_replace('/^(\d+ \d+).+$/ms', '$1', $m[1][$i])] = &$this->objs[$i];
			$p += $len;
		}

		preg_match('/startxref.+/ims', $content, $m);
		$this->tail = $m[0];
	}

	public function delObj($id){
		if(!isset($this->omap[$id])) return;
		$this->omap[$id][5] = 'D';
		if(isset($this->omap[$id][1]) && preg_match('/\/Length (\d+ \d+) R/i', $this->omap[$id][1], $m)) $this->delObj($m[1]);
	}

	public function pageCount(){
		if(preg_match('/Pages.*?Count (\d+)/ims', $this->objs[$this->pgs_idx][1], $m)){
			return $m[1];
		}
		return 1;
	}

	public function removePage($pg){
		$pgd = &$this->objs[$this->pgs_idx];
		$pgd[1] = preg_replace_callback('/(Pages\/Kids \[\n*)([^\]]+)(\n*\].*?Count )(\d+)/ims', function($m) use ($pg, &$pgd){
			$lo = strlen($m[1].$m[2].$m[3].$m[4]);
			$mi = 0;
			$r = false;
			$m[2] = preg_replace_callback('/(\d+ \d+)( R\n*)/ims', function($m2) use ($pg, &$mi, &$r){
				if($mi++ == $pg - 1){
					$this->delObj($m2[1]);
					preg_match_all('/([^\/ ]+) *(\d+ \d+) R/', $this->omap[$m2[1]][1], $ms);
					foreach($ms[2] as $i => $r){
						if(preg_match('/Parent/i', $ms[1][$i])) continue;
						$this->delObj($r);
					}
					$r = true;
					return '';
				}
				return $m2[1].$m2[2];
			}, $m[2]);
			$rd = $m[1].$m[2].$m[3].($r? $m[4]-1 : $m[4]);
			$pgd[5] = strlen($rd) - $lo;
			return $rd;
		}, $pgd[1]);
	}

	public function replace($f, $t){
		$r = false;
		foreach($this->objs as $i => &$obj){
			if($i == $this->xref_idx || preg_match('/Type\/ObjStm/i', $obj[1]) || (!empty($obj[5]) && $obj[5] == 'D')) continue;
			$obj[1] = preg_replace_callback('/(<<.*?FlateDecode>>.*?stream\n)(.*?)(\nendstream)/ms', function($m) use($i, &$obj, $f, $t, &$r){
				$d = gzuncompress($m[2]);
				$g = false;
				if(is_array($f)){
					foreach($f as $fi){
						if(stripos($d, $fi) > -1){
							$g = true;
							break;
						}
					}
				}else{
					$g = stripos($d, $f) > -1;
				}
				if($g){
					$r = true;
					$d = str_ireplace($f, $t, $d);
					$d = gzcompress($d);
					$dl = strlen($d);
					$obj[5] = $dl - strlen($m[2]);
					if(preg_match('/Length (\d+ \d+)/i', $m[1], $o)){
						$this->updateLengthObj($o[1], $dl);
					}
				}else{
					$d = $m[2];
				}
				return $m[1].$d.$m[3];
			}, $obj[1]);
			$obj[1] = preg_replace_callback('/(<<.*?Subtype\s*\/XML>>\nstream\n)(.*?)(\nendstream)/ms', function($m) use($i, &$obj){
				$d = gzcompress(preg_replace('/[ \n\r]{2,}/', ' ', $m[2]));
				$dl = strlen($d);
				$obj[5] = $dl - strlen($m[2])+strlen('/Filter/FlateDecode');
				if(preg_match('/Length (\d+ \d+)/i', $m[1], $o)){
					$this->updateLengthObj($o[1], $dl);
				}
				return str_replace('XML>>', 'XML/Filter/FlateDecode>>', $m[1]).$d.$m[3];
			}, $obj[1]);
		}
		return $r;
	}

	public function rawText(){
		$txt = [];
		foreach($this->objs as $i => &$obj){
			if($i == $this->xref_idx || preg_match('/Type\/ObjStm/i', $obj[1])) continue;
			if(preg_match('/(<<.*?FlateDecode>>.*?stream[\n\r]*)(.*?)(\nendstream)/ms', $obj[1], $m)){
				preg_match_all('/Td\s*\((.+?)\)Tj/i', gzuncompress($m[2]), $ts);
				foreach($ts[1] as $t){
					$txt[] = preg_replace('/[\s]{2,}/', ' ', trim($t));
				}
			}
		}
		return implode("\n", $txt);
	}

	protected function updateLengthObj($o, $len){
		$len = (string) $len;
		foreach($this->objs as &$obj){
			if(preg_match('/^'.$o.'/', $obj[0])){
				if(strlen($obj[1]) != strlen($len)){
					$obj[5] = strlen($len) - strlen($obj[1]);
				}
				$obj[1] = $len;
				break;
			}
		}
	}

	public function output($out = 1, $fn = ''){
		$ops = [];
		$d = 0;
		$pdf = $this->head;
		foreach($this->objs as $i => &$obj){
			if(!empty($obj[5]) && $obj[5] == 'D'){
				$ops[$obj[3]] = 0;
				$d -= $obj[4];
				continue;
			}
			if($d != 0) $ops[$obj[3]] = $obj[3] + $d;
			if($i == $this->xref_idx){
				//update tail
				$this->tail = preg_replace('/(startxref[\n\r]*)\d+/', '${1}'.($obj[3]+$d), $this->tail);

				//update xref
				$obj[1] = preg_replace_callback('/(<<.*?FlateDecode>>.*?stream\n)(.*?)(\nendstream)/ms', function($m) use(&$obj, $ops){
					$d = gzuncompress($m[2]);
					if(preg_match('/\/W \[(\d+) (\d+) (\d+)\]/i', $m[1], $w)){
						$getV = function($b){
							$v = 0;
							foreach(array_reverse(unpack('C*', $b)) as $i => $t){
								$v += pow(256, $i) * $t;
							}
							return $v;
						};
						$getC = function($v, $l){
							$b = [];
							for($j=$l; $j>0; $j--){
								$v -= ($t = floor($v / pow(256, $j-1))) * pow(256, $j-1);
								$b[] = $t;
							}
							return pack('C*', ...$b);
						};
						unset($w[0]);
						foreach($w as &$wi) $wi = intval($wi);
						$lseg = array_sum($w);
						$dc = 0;
						for ($i=0; $i < strlen($d); $i+=$lseg) {
							if($getV(substr($d, $i+0, $w[1])) != 1) continue;
							$v2 = $getV(substr($d, $i+$w[1], $w[2]));
							if(isset($ops[$v2])){
								if($ops[$v2] == 0){
									$d = substr_replace($d, $getC(0, $w[1]), $i, $w[1]);
									$dc++;
								}
								$d = substr_replace($d, $getC($ops[$v2], $w[2]), $i+$w[1], $w[2]);
							}
						}
					}
					$d = gzcompress($d);
					$lm1 = strlen($m[1]);
					$lm2 = strlen($m[2]);
					$ld = strlen($d);
					if($lm2 != $ld){
						$m[1] = preg_replace('/\/Length\s+\d+/i', '/Length '.$ld, $m[1]);
					}
					if($dc > 0){
						$m[1] = preg_replace_callback('/(\/Size\s+)(\d+)/i', function($s) use ($dc){
							return $s[1].(intval($s[2]) - $dc);
						}, $m[1]);
					}
					$m[1] = preg_replace('/\/ID[^\/]+/i', '', $m[1]);
					$obj[5] = strlen($m[1]) - $lm1 + $ld - $lm2;
					return $m[1].$d.$m[3];
				}, $obj[1]);
			}
			$pdf .= $obj[0].$obj[1].$obj[2];
			if(!empty($obj[5])) $d += $obj[5];
		}

		if($out == 1){
			header("Cache-Control: maxage=1");
			header('Content-Type: application/pdf');
			header('Content-Disposition: inline; filename="'.$fn.'"');
			echo $pdf.$this->tail;
		}elseif($out == 2){
			return file_put_contents($fn, $pdf.$this->tail);
		}else{
			return $pdf.$this->tail;
		}
	}
}
