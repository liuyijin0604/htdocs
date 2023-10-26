<?php
namespace CainiaoLink;

class Schema{

	public static function skel($typ, $test_data = [], $emt=false){
		$sn = str_replace('_', '', ucwords(strtolower(trim($typ)), '_'));
		$schema_file = __DIR__.DIRECTORY_SEPARATOR.'schemas'.DIRECTORY_SEPARATOR.$sn.'.php';
		if(!is_file($schema_file)){
			throw new \Exception('Schema '.$schema.' not defined');
			return false;
		}
		$schema = include($schema_file);
		return self::skel_el($schema, $test_data, $emt);
	}

	private static function skel_el($schema, $test_data = [], $emt=false){
		$skel = [];

		foreach($schema as $k=>$rule){
			if($schema[$k]['type'] == 'object'){
				$scm = $schema[$k]['o'];
				if(preg_match('/List$/', $k)){
					foreach($schema[$k]['o']['attributes'] as $kk => $o){
						$scm = $o['o']['attributes'];
						break;
					}
					$skel[$k][] = self::skel_el($scm, $test_data, $emt);
				}else{
					$skel[$k] = self::skel_el($schema[$k]['o']['attributes'], $test_data, $emt);
				}
			}else{	
				if(!$rule['required'] && !$emt)	continue;

				$v = $rule['required']? 'REQUIRED' : '';

				if(!in_array($schema[$k]['type'], ['string', 'unsignedByte'])){
					switch($schema[$k]['type']){
						case 'decimal':
							$v = $rule['required']? 1.00 : 0.00;
						break;
						case 'int':
						case 'integer':
						case 'long':
						case 'unsignedInt':
						case 'unsignedShort':
						case 'unsignedLong':
							$v = $rule['required']? 1 : 0;
						break;
						case 'date':
							$v = $rule['required']? date('Y-m-d') : '';
						break;
						default:
							$v = '';
						break;
					}
				}
				if(!empty($rule['restriction'])){
					foreach($rule['restriction'] as $l => $r){
						switch($l){
							case 'enum':
								$v = $rule['required']? $r[array_rand($r)] : '';
							break;
						}

					}
				}

				$skel[$k] = isset($test_data[$k])? $test_data[$k] : $v;
			}
		}

		return $skel;
	}

	public static function entities(){
		$ts = [];
		foreach(glob(__DIR__.DIRECTORY_SEPARATOR.'schemas'.DIRECTORY_SEPARATOR.'*.php') as $f){
			$schema = include($f);
			$p = substr(basename($f), 0, -4);
			$ts = array_merge($ts, self::entObj($p, $schema));
		}
		//var_dump(array_unique($ts));
	}

	private static function entObj($p, $schema){
		echo $p.PHP_EOL;
		$ts = [];
		foreach($schema as $a => $r){
			$t = $r['type'];
			$ts[] = $t;
			if($r['type'] == 'object'){
				if(preg_match('/List$/', $a)){
					foreach($r['o']['attributes'] as $kk => $o){
						$r = $o;
						$a = $kk;
						$t = 'object_array';
						break;
					}
				}
				echo $p.'.'.$a.' ['.$t.(empty($r['restriction']['maxLength'])? '' : '('.$r['restriction']['maxLength'].')').']'.PHP_EOL;
				$ts = array_merge($ts, self::entObj($a, $r['o']['attributes']));
			}else{
				echo $p.'.'.$a.' ['.$t.(empty($r['restriction']['maxLength'])? '' : '('.$r['restriction']['maxLength'].')').']'.PHP_EOL;
			}
		}
		return $ts;
	}

	public static function validate($typ, $json, $debug = false){
		$sn = str_replace('_', '', ucwords(strtolower(trim($typ)), '_'));
		$schema_file = __DIR__.DIRECTORY_SEPARATOR.'schemas'.DIRECTORY_SEPARATOR.$sn.'.php';
		if(!is_file($schema_file)){
			throw new \Exception('Schema '.$schema.' not defined');
			return false;
		}
		if($debug) echo $schema_file.' used'.PHP_EOL;
		$schema = include($schema_file);
		$json = json_decode($json, true);

		$err = self::valid($schema, $json, $sn, $debug);

		if($debug){
			if(empty($err)){
				echo 'Schema OK!'.PHP_EOL;
			}else{
				echo 'Errors: '.PHP_EOL;
				foreach($err as $e) echo $e.PHP_EOL;
			}
		}
	}

	private static function valid($schema, $data, $path = '', $debug = false){
		$err = [];

		foreach($data as $k=>$v){
			if(!isset($schema[$k])) $err[] = $k.' undefined';
		}

		foreach($schema as $k=>$rule){
			if($rule['required'] && empty($data[$k])){
				$err[] = $path.'.'.$k.' missing';
				continue;
			}

			if(empty($data[$k])) continue;

			if($schema[$k]['type'] == 'object'){
				$scm = $schema[$k]['o'];
				if(preg_match('/List$/', $k)){
					foreach($schema[$k]['o']['attributes'] as $kk => $o){
						$scm = $o['o']['attributes'];
						break;
					}
					foreach($data[$k] as $itm) $err = array_merge($err, self::valid($scm, $itm, $path.'.'.$k.'.'.$kk, $debug));
				}else{
					$err = array_merge($err, self::valid($schema[$k]['o']['attributes'], $data[$k], $path.'.'.$k, $debug));
				}
			}else{
				if(!in_array($schema[$k]['type'], ['string', 'unsignedByte'])){
					switch($schema[$k]['type']){
						case 'decimal':
							if(preg_match('/[^\d\.]+/', $data[$k])) $err[] = $path.'.'.$k.' = '.$data[$k].' is not decimal';
						break;
						case 'int':
						case 'integer':
						case 'long':
						case 'unsignedInt':
						case 'unsignedShort':
						case 'unsignedLong':
							if(preg_match('/[^\d]+/', $data[$k])) $err[] = $path.'.'.$k.' = '.$data[$k].' is not integer';
						break;
						case 'date':
							if(preg_match('/\d{4}\-\d{2}\-\d{2}/', $data[$k])) $err[] = $path.'.'.$k.' = '.$data[$k].' is not date';
						break;
						default:
							if($debug) echo $schema[$k]['type'].' no validation rules'.PHP_EOL;
						break;
					}
				}
				if(!empty($rule['restriction'])){
					foreach($rule['restriction'] as $l => $r){
						switch($l){
							case 'maxLength':
								if(strlen($data[$k]) > $r) $err[] = $path.'.'.$k.' = '.$data[$k].' too long';
							break;
							case 'enum':
								if(!in_array($data[$k], $r)) $err[] = $path.'.'.$k.' = '.$data[$k].' not in enum ['.implode(', ', $r).']';
							break;
						}

					}
				}
			}
		}

		return $err;
	}
}