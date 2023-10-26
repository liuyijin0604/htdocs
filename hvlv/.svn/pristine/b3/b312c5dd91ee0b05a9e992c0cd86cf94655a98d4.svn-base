<?php
class Xsd2Php{
	public $xml, $xpath, $f;
	public $ns = 'CainiaoLink\Schemas';
	public $cs = [], $msg = [], $err = [];
	public $debug = true;

	public function loadXsd($f){
		$this->f = $f;
		$this->dom = new \DOMDocument();
		$this->dom->load($f, LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_NOENT | LIBXML_XINCLUDE);
		$this->xpath = new \DOMXPath($this->dom);
		$els = $this->xpath->query('/xs:schema/xs:element');
		$this->readElement($els[0]);
	}

	private function readElement($node){
		$ctse = $this->xpath->query('xs:complexType/xs:sequence|xs:complexType/xs:all', $node);

		if(count($ctse) == 0){
			if($this->debug) $this->msg[] = 'new Property: '. $node->getAttribute('name');
			$p = [];
			foreach(['name', 'type', 'minOccurs', 'maxOccurs'] as $a){
				$v = $node->getAttribute($a);
				if($v !== false) $p[$a] = preg_replace('/^xs:/', '', $v);
			}
			$res = $this->xpath->query('xs:simpleType/xs:restriction', $node);
			if(empty($res[0])) return $p;
			if(empty($p['type'])) $p['type'] = preg_replace('/^xs:/', '', $res[0]->getAttribute('base'));
			foreach($res[0]->childNodes as $n){
				if($n->nodeType != 1) continue;
				switch($n->tagName){
					case 'xs:enumeration':
						$p['restriction']['enum'][] = $n->getAttribute('value');
					break;
					case 'xs:maxLength':
						$p['restriction']['maxLength'] = $n->getAttribute('value');
					break;
					default:
						$this->err[] = $n->tagName. ' unknown';
					break;
				}
			}
			return $p;
		}else{
			$rname = str_replace('_', '', ucwords(strtolower(trim(basename(substr($this->f, 0, -4)))), '_'));
			$className = $node->parentNode->tagName == 'xs:schema'? $rname : $node->getAttribute('name');
			if($this->debug) $this->msg[] = 'new Object: '. $className;
			$els = $this->xpath->query('xs:element', $ctse[0]);
			$o = [
				'name' => ucfirst($className),
				'attributes' => [],
				'file' => basename($this->f),
				'obj' => $node->parentNode->tagName != 'xs:schema',
			];
			foreach($els as $el){
				$p = $this->readElement($el);
				//if(isset($o['attributes'][$p['name']])) $this->err[] = $o['name']. '::'. $p['name']. ' already specified';
				$o['attributes'][$p['name']] = $p;
			}
			
			//if(!empty($this->cs[$o['name']])) $o = $this->mergeProp($this->cs[$o['name']], $o);
			$this->cs[$o['name']] = $o;

			$p = ['name' => $className, 'type' => 'object', 'def' => $this->ns.'\\'.ucfirst($className), 'o' => $o];

			foreach(['minOccurs', 'maxOccurs'] as $a){
				$v = $node->getAttribute($a);
				if($v !== false) $p[$a] = preg_replace('/^xs:/', '', $v);
			}
			return $p;
		}
	}

	public function mergeProp($o, $n){
		foreach($o['attributes'] as $k => $p){
			if(!isset($n['attributes'][$k])){
				$n['attributes'][$k] = $p;
				//$this->err[] = $n['file']. '\\'. $n['name']. '::'.$k.' missing '.PHP_EOL.$o['file'].' - '.json_encode($o['attributes']).PHP_EOL.$n['file'].' - '.json_encode($n['attributes']);
			}else{
				foreach($p as $a=>$v){
					if(!isset($n['attributes'][$k][$a])){
						$n['attributes'][$k][$a] = $v;
						$this->err[] = $n['file']. '\\'. $n['name']. '::'.$k.'->'.$a.' Attribute Missing:'.PHP_EOL.$o['file'].' - '.json_encode($v);
					}elseif(!in_array($a, ['o', 'minOccurs', 'maxOccurs', 'restriction']) && json_encode($v) != json_encode($n['attributes'][$k][$a])){
						$this->err[] = $n['name']. '::'.$k.'->'.$a.' Attribute Different:'.PHP_EOL.$o['file'].' - '.json_encode($v).PHP_EOL.$n['file'].' - '.json_encode($n['attributes'][$k][$a]);
					}
				}
			}
		}

		foreach($n['attributes'] as $k => $p){
			if(!isset($o['attributes'][$k])){
				//$this->err[] = $o['file']. '\\'. $o['name']. '::'.$k.' missing '.PHP_EOL.$n['file'].' - '.json_encode($n['attributes']).PHP_EOL.$o['file'].' - '.json_encode($o['attributes']);
			}else{
				foreach($p as $a=>$v){
					if(!isset($o['attributes'][$k][$a])){
						$this->err[] = $o['file']. '\\'. $o['name']. '::'.$k.'->'.$a.' Attribute Missing:'.PHP_EOL.$n['file'].' - '.json_encode($v);
					}elseif(!in_array($a, ['o', 'minOccurs', 'maxOccurs', 'restriction']) && json_encode($v) != json_encode($o['attributes'][$k][$a])){
						$this->err[] = $n['name']. '::'.$k.'->'.$a.' Attribute Different:'.PHP_EOL.$o['file'].' - '.json_encode($o['attributes'][$k][$a]).PHP_EOL.$n['file'].' - '.json_encode($v);
					}
				}
			}
		}

		return $n;
	}
	
	public function var_export_short($data, $return=true){
		$dump = var_export($data, true);
		$dump = preg_replace('#(?:\A|\n)([ ]*)array \(#i', '[', $dump); // Starts
		$dump = preg_replace('#\n([ ]*)\),#', "\n$1],", $dump); // Ends
		$dump = preg_replace('#=> \[\n\s+\],\n#', "=> [],\n", $dump); // Empties
		//$dump = preg_replace('/[\n\r]+/', "\n\t", $dump); // Empties
		$dump = preg_replace('/ \d+ \=\> /', '', $dump); // Empties
		$dump = preg_replace('/  /', "\t", $dump); // Empties
		//$dump = preg_replace('/\[ /', '[', $dump); // Empties
		//$dump = preg_replace('/ \]/', ']', $dump); // Empties

		if (gettype($data) == 'object') { // Deal with object states
			$dump = str_replace('__set_state(array(', '__set_state([', $dump);
			$dump = preg_replace('#\)\)$#', "])", $dump);
		} else { 
			$dump = preg_replace('#\)$#', "]", $dump);
		}

		if ($return===true) {
			return $dump;
		} else {
			echo $dump;
		}
	}

	public function prepObj($o, $obj = false){
		if($obj) unset($o['name'], $o['file'], $o['obj']);
		foreach($o['attributes'] as $k=>$p){
			$o['attributes'][$k]['required'] = !empty($o['attributes'][$k]['minOccurs']);
			unset($o['attributes'][$k]['name'], $o['attributes'][$k]['minOccurs']);
			if(isset($o['attributes'][$k]['maxOccurs']) && intval($o['attributes'][$k]['maxOccurs']) === 0) unset($o['attributes'][$k]['maxOccurs']);
			if(isset($o['attributes'][$k]['o'])) $o['attributes'][$k]['o'] = $this->prepObj($o['attributes'][$k]['o'], true);
		}
		return $o;
	}

	public function savePhp($dir, $genobj = true){
		foreach($this->cs as $o){
			if(!$genobj && $o['obj']) continue;
			$f = $dir.DIRECTORY_SEPARATOR.$o['name'].'.php';
			if($this->debug) $this->msg[] = 'Save file: '. $f;
			$o = $this->prepObj($o);
			$c = '<?php' . PHP_EOL;
			$ps = $as = [];
			foreach($o['attributes'] as $k=>$p){
				$ps[] = $k;
				$as[$k] = [];
			}

			//$c .= "\t".'public $'.implode(', $', $ps).';'.PHP_EOL;
			$c .= 'return '.$this->var_export_short($o['obj']? $as : $o['attributes'], true).';'.PHP_EOL;
			file_put_contents($f, $c);
		}
	}
}

/* xsd to php*/
$xsd = new Xsd2Php();
foreach(glob('xsd\*.xsd') as $f){
	$xsd->loadXsd($f);
}
$xsd->savePhp('schemas', false);
echo implode(PHP_EOL.PHP_EOL, $xsd->err);
//var_dump($xsd->msg);
