<?php
if(!defined('EDI_DEF_PATH'))	define('EDI_DEF_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'definitions'.DIRECTORY_SEPARATOR);

class ediCreator{
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
	
	function __construct($ver,$msg){
		$this->un_def = include(EDI_DEF_PATH.'UN'.DIRECTORY_SEPARATOR.'edsd.php');
		$vdf = EDI_DEF_PATH.$ver.DIRECTORY_SEPARATOR.'edsd.php';
		if(is_file($vdf)){
			$this->ver_def = include($vdf);
		}else{
			die('Version '.$ver.' unknown.');
		}
		$mdf = EDI_DEF_PATH.$ver.DIRECTORY_SEPARATOR.'EDMD'.DIRECTORY_SEPARATOR.strtoupper($msg).'.php';
		if(is_file($mdf)){
			$this->msg_def = include($mdf);
			$this->msgDefAddParnet($this->msg_def);
		}else{
			die('Message '.$msg.' unknown.');
		}
		$this->exchange = new StdClass;
		$this->edi = new StdClass;
	}
	
	public function addLine($path, $s, $a){
		$ps = explode("/", trim($path,'/'));
		$r = $this->edi;
		foreach($ps as $p){
			if(!isset($r->{$p})) $r->{$p} = unserialize(serialize($this->msg_def->{$s}));
		}
		//$this->edi->
		//$this->edi->{$s} = unserialize(serialize($this->msg_def->{$s}));
	}
	
	private function msgDefAddParnet(&$t){
		if(empty($t->children)) return;
		foreach($t->children as $k=>&$d){
			$d->parent = $t;
			$this->msgDefAddParnet($d);
		}
	}
	
	private function validLine($l, $i){
		$err = array();
		if(isset($this->un_def->{$l[0]})){
			$s = $this->validSeg($l, $this->un_def->{$l[0]});
			$err = $s->error_sum;
			$this->exchange->{$l[0]} = $s;
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
	
	private function validSeg($l, $d){
		$i = 1;
		$err = array();
		$d = unserialize(serialize($d)); //deep clone object
		foreach($d->data as $p=>&$co){
			if(empty($co->comp)){//single comp
				$co->value = empty($l[$i])? '' : $l[$i];
				$this->validData($co);
				if($co->error) $err[] = $p.' '.$co->error;
			}else{//multi comp
				$j = 0;
				foreach($co->comp as $q=>&$da){
					$da->value = empty($l[$i][$j])? '' : $l[$i][$j];
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
	
	private function validData(&$d, $pmc=true){
		$err = array();
		if($d->mc && $pmc && empty($d->value)) $err[] = 'Can not be empty';
		if(strlen($d->value) > $d->length) $err[] =  $d->value.':'.$d->length.'Too long';
		if($d->type == 'n' && !empty($d->value) && !is_numeric($d->value)) $err[] = 'Numberic only';
		if($d->type == 'a' && !empty($d->value) && is_numeric($d->value)) $err[] = 'Alpha only';
		
		$d->error = empty($err)? false : implode(', ',$err);
	}

	public function getErrors(){
		return empty($this->errors)? false : $this->errors;
	}

	public function getJson(){
		return json_encode($this->edi);
	}

//end of class
}
