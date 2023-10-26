<?php
if(!defined('EDI_DEF_PATH'))	define('EDI_DEF_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'definitions'.DIRECTORY_SEPARATOR);

class ediParser{
	public $exchange;
	public $edi;
	public $edi_raw = array();
	public $errors = array();
	public $validate_seg = true;
	public $validate_msg = true;
	protected $un_def;
	protected $ver_def;
	protected $msg_def;
	private $_lc;
	
	function __construct($data=null,$vs=true,$vm=true){
		$this->un_def = include(EDI_DEF_PATH.'UN'.DIRECTORY_SEPARATOR.'edsd.php');
		$this->validate_seg = $vs;
		$this->validate_msg = $vm;
		$this->exchange = new StdClass;
		$this->edi = new StdClass;
		if ($data!==null) $this->load($data);
	}

	public function load($data){
		$data = preg_replace("/^UNA:\+\.\? '/", '', $data);
		$data=preg_split('/[\r\n]+/', trim($data));

		//split if oneliner and remove whitespace (UNWRAP)
		if (count($data)==1){
			$d1=preg_split("/\'/i", $data[0]);
			$d2=array();
			foreach($d1 as &$l){
				$l = trim($l);
				if(empty($l)) continue;
				$d2[] = $l."'";
			}
			$data = $d2;
		}

		foreach($data as $i=>$l){
			$l = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $l); //basic sanitization, remove non printable chars
			if (empty($l)){
				unset($data[$i]);
				continue;
			}
			if (substr($l,-1) != "'"){
				$this->errors[]='Segment not ended correctly at line '.$i. " => ". $l;
			}
			$this->splitSegment($l, $i);
		}
		
		if($this->validate_msg) $this->validMsg();
		
		unset($this->un_def, $this->ver_def, $this->msg_def);
		
		return $this->edi;
	}

	private function splitSegment($str,$i=0){ //segment
		$str = preg_replace("/'$/", '', $str); //remove ending '
		$matches=preg_split("/\?[:'\+\?]{1}(*SKIP)(*FAIL)|\+/s", $str); //split except release character
		foreach ($matches as &$value){ 
			if (preg_match("/\?[:'\+\?]{1}(*SKIP)(*FAIL)|['\+\?]/s", $value)) $this->errors[]="There's a not escaped character in the data; string ". $str; //no char except : without release character
			//$value=str_replace("?","",$value); //delete release char
			$value=$this->splitData($value);
		}
		if($this->validate_seg) $this->validLine($matches, $i);
		else $this->edi_raw[] = $matches;
	}
	
	private function validLine($l, $i){
		$err = array();
		if(isset($this->un_def->{$l[0]})){
			$s = $this->validSeg($l, $this->un_def->{$l[0]});
			$err = $s->error_sum;
			$this->exchange->{$l[0]} = $s;
			if($l[0] == 'UNH'){//get definitions
				$vdf = EDI_DEF_PATH.$this->getExchValue('UNH',20,2).DIRECTORY_SEPARATOR.'edsd.php';
				if(is_file($vdf)){
					$this->ver_def = include($vdf);
				}else{
					$err[] = 'Error EDI Version '.$this->getExchValue('UNH',20,2)." definition not found.";
				}
				$mdf = EDI_DEF_PATH.$this->getExchValue('UNH',20,2).DIRECTORY_SEPARATOR.'EDMD'.DIRECTORY_SEPARATOR.$this->getExchValue('UNH',20,0).'.php';
				if(is_file($mdf)){
					$this->msg_def = include($mdf);
					$this->msgDefAddParnet($this->msg_def);
					$this->_cpath = $this->msg_def;
				}else{
					$err[] = 'Error EDI Message '.$this->getExchValue('UNH',20,0)." definition not found.";
				}
			}
		}elseif(isset($this->ver_def->{$l[0]})){
			$s = $this->validSeg($l, $this->ver_def->{$l[0]});
			$err = $s->error_sum;
		}else{
			$err[] = 'Segment '.$l[0].' not defined';
		}
			
		if(!in_array($l[0], array('UNB','UNZ')) && !empty($s)){
			$s->code = $l[0];
			$this->edi_raw[] = $s;
		}
		
		if(!empty($err)) $this->errors[] = 'Error Line '.$i.':'.$l[0].":".implode(", ", $err)."\n";
		
	}
	
	private function msgDefAddParnet(&$t){
		if(empty($t->children)) return;
		foreach($t->children as $k=>&$d){
			$d->parent = $t;
			$this->msgDefAddParnet($d);
		}
	}
	
	private function validSeg($l, $d){
		$i = 1;
		$err = array();
		$d = unserialize(serialize($d)); //deep clone object
		foreach($d->data as $p=>&$co){
			if(empty($co->comp)){//single comp
				$co->value = isset($l[$i])? $l[$i] : '';
				$this->validData($co);
				if($co->error) $err[] = $p.' '.$co->error;
			}else{//multi comp
				if(isset($l[$i]) && is_string($l[$i])) $l[$i] = [$l[$i]];
				$j = 0;
				foreach($co->comp as $q=>&$da){
					$da->value = isset($l[$i][$j])? $l[$i][$j] : '';
					$this->validData($da, $co->mc);
					if($da->error) $err[] = $p.':'.$q.' '.$da->error;
					$j++;
				}
			}
			$i++;
		}
		
		$d->error_sum = $err;
		
		return $d;
	}
	
	public static function breakMultiMsg($data){
		$ms = preg_split('/[\'\n\r]+UNH\+/', $data);
		if(sizeof($ms) > 2){//multiple
			$msgs = array();
			$unb = $ms[0];
			$lm = preg_split('/[\'\n\r]+UNZ\+/', $ms[sizeof($ms) -1]);
			$ms[sizeof($ms) -1] = $lm[0];
			$unz = 'UNZ+'.$lm[1];
			unset($ms[0]);
			foreach($ms as $msg){
				$msgs[] = $unb."'\nUNH+".$msg."'\n".$unz;
			}
			return $msgs;
		}
		return array($data);
	}
	
	private function validData(&$d, $pmc=true){
		$err = array();
		if($d->mc && $pmc && $d->value == '') $err[] = 'Can not be empty';
		if(strlen($d->value) > $d->length) $err[] =  $d->value.':'.$d->length.'Too long';
		if($d->type == 'n' && !empty($d->value) && !is_numeric($d->value)) $err[] = 'Numberic only';
		if($d->type == 'a' && !empty($d->value) && is_numeric($d->value)) $err[] = 'Alpha only';
		
		$d->error = empty($err)? false : implode(', ',$err);
	}
	
	private function validMsg(){
		if(empty($this->msg_def)) return;
		
		if(empty($r)) $r = $this->msg_def;
		$rp = 0;
		$this->vmWalker($this->msg_def, $rp);
		$pc = -1;
		foreach($this->_lc as $c){
			if($c != $pc + 1){
				$this->errors[] = $this->edi_raw[$pc+1]->code.' in '.($pc+2).' is omitted';
			}
			$pc = $c;
		}
	}
	
	private function vmWalker($r, &$rp){
		$m = false;
		foreach($r->children as $k=>&$v){
			if($k == $this->edi_raw[$rp]->code){
				$m = true;
				$this->appendEdi($v, $rp);
			}elseif($v->group){
				$v->ptr = 0;
				while($this->vmWalker($v, $rp)){
					$m = true;
					$v->ptr++;
				};
			}elseif($v->mc){
				if(empty($v->parent) || (empty($r->parent) || $r->mc))
					$this->errors[] = $k.' in '.$v->path.' is required but missing';
			}
		}
		
		return $m;
	}
	
	private function appendEdi(&$d, &$rp){
		$ep = &$this->edi;
		$path = trim($d->path,'/');
		if(!empty($path)){
			$path = explode('/', $path);
			$ptrs = array();
			$pd = $d;
			while(isset($pd->parent)){
				array_unshift($ptrs, isset($pd->ptr)? $pd->ptr : false);
				$pd = $pd->parent;
			}
			foreach($path as $i=>$p){
				if(!isset($ep->{$p})){
					$ep->{$p} = array();
				}
				if(!isset($ep->{$p}[$ptrs[$i]])) $ep->{$p}[$ptrs[$i]] = new stdClass;
				$ep = &$ep->{$p}[$ptrs[$i]];
			}
		}
		$s = &$this->edi_raw[$rp];
		
		$this->_lc[] = $rp;
		
		if($d->repeat > 1){
			if(!isset($ep->{$s->code})) $ep->{$s->code} = array();
			if(sizeof($ep->{$s->code}) > $d->repeat) $this->errors[] = $s->code.' in '.$d->path.' not allow more than '.$d->repeat.' repeats';
			$ep->{$s->code}[] = $s;
			$rp++;
			if($s->code == $this->edi_raw[$rp]->code){
				$this->appendEdi($d, $rp);
			}
		}else{
			$ep->{$s->code} = $s;
			$rp++;
		}
	}

	private function splitData($str){ //composite data element
		$arr = preg_split("/\?[:'\+\?]{1}(*SKIP)(*FAIL)|:/s", $str);
		if(count($arr) == 1) return preg_replace('/\?([:\'\+\?]{1})/', '\\1', $str);
		foreach($arr as $k=>$v){
			$arr[$k] = preg_replace('/\?([:\'\+\?]{1})/', '\\1', $v);
		}
		return $arr;
	}
	
	public function getExchTime(){
		$d = $this->getExchValue('UNB', 40, 0);
		$t = $this->getExchValue('UNB', 40, 1);
		return date('Y-m-d H:i:s', strtotime('20'.$d.' '.$t));
	}
	
	public function getExchValue($s, $d, $c=false){
		return $this->getSegValue($this->exchange->{$s}, $d, $c);
	}
	
	public function getEdiValue($s, $d, $c=false){
		return $this->getSegValue($this->edi->{$s}, $d, $c);
	}
	
	public function getSegValue($s, $d, $c = false){
		if($s === false) return false;
		if($c === false) return isset($s->data[$d]->value)? $s->data[$d]->value : null;
		return isset($s->data[$d]->comp[$c]->value)? $s->data[$d]->comp[$c]->value : null;
	}
	
	public function getSegValueJoined($s, $d, $space = false){
		if($s === false) return false;
		if(empty($s->data[$d]->comp)) return null;
		$vj = '';
		foreach($s->data[$d]->comp as $c){
			$vj .= $c->value;
			if($space) $vj .= ' ';
		}
		return trim($vj);
	}
	
	public function findSeg($ss, $d, $c = false, $q = '', $m = false){
		if(!is_array($ss)) return false;
		$ms = array();
		foreach($ss as $s){
			if($this->getSegValue($s, $d, $c) == $q){
				if($m){
					$ms[] = $s;
				}else{
					return $s;
				}
			}
		}
		if($m) return empty($ms)? false : $ms;
		
		return false;
	}
	
	public function findSegGrp($sgs, $s, $d, $c = false, $q = '', $m = false){
		if(!is_array($sgs)) return false;
		$ms = array();
		foreach($sgs as $sg){
			if(!isset($sg->{$s})) continue;
			if(is_array($sg->{$s})){
				foreach($sg->{$s} as $si){
					if($this->getSegValue($si, $d, $c) == $q){
						if($m){
							$ms[] = $sg;
						}else{
							return $sg;
						}
					}
				}
			}else{
				if($this->getSegValue($sg->{$s}, $d, $c) == $q){
					if($m){
						$ms[] = $sg;
					}else{
						return $sg;
					}
				}
			}
		}
		if($m) return empty($ms)? false : $ms;
		
		return false;
	}

	public function getErrors(){
		return empty($this->errors)? false : $this->errors;
	}

	public function getJson(){
		return json_encode($this->edi);
	}

//end of class
}
