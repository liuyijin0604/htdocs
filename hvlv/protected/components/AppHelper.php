<?php
class AppHelper {

	public static function money_format($a,$n,$p=2,$m=2) {
		$n = round($n, $p+2);
		list($w,$d) = strpos($n, '.') > 0? explode('.', $n) : array($n,0);
		$dp = strlen(rtrim($d,'0'));
		if($dp <= $m) $p = $m;
		
		return number_format($n, $p, '.', ',');
	}

	public static function number_format($a,$n,$p=2,$m=2) {
		$n = round($n, $p+2);
		list($w,$d) = strpos($n, '.') > 0? explode('.', $n) : array($n,0);
		$dp = strlen(rtrim($d,'0'));
		if($dp <= $m) $p = $m;
		
		return number_format($n, $p, '.', '');
	}

	public static function tArray($cate, $a){
		foreach($a as $k=>$v){
			$a[$k] = Yii::t($cate, $v);
		}
		return $a;
	}
	
	public static function qty_format($n,$p=0) {
		return number_format($n, $p, '.', ',');
	}

	public static function formDate($d) {
		return substr($d,6,4).'-'.substr($d,3,2)."-".substr($d,0,2);
	}
	
	public static function setting2List($k){
		$a = explode(',', Yii::app()->params['settings'][$k]['value']);
		$r = array();
		foreach($a as $v){
			$r[$v] = $v;
		}
		return $r;
	}

	/**
	 * get all xero related settings
	 * @param $k
	 * @return string
	 */
	public static function getXeroSetting($k){
		if ( isset(Yii::app()->params['settings']['xero_settings']['items'][$k] ) ) {
			return Yii::app()->params['settings']['xero_settings']['items'][$k];
		} else {
			return '';
		}
	}

	public static function codeToSound($c){
		$s = [];
		if(preg_match('/([a-z0]+)(\d+)$/i', $c, $m)){
			$s = str_split(strtolower($m[1]));
			$s[] = $m[2];
		}else{
			$s[] = $c;
		}
		
		return implode('-', $s);
	}

	public static function similar_text_cn($s1, $s2){
		preg_match_all("/./u", $s1, $m1);
		preg_match_all("/./u", $s2, $m2);
		$a1 = array_unique($m1[0]);
		$a2 = array_unique($m2[0]);
		return min(count($a1), count($a2)) - count(array_diff($a2, $a1));
	}

	public static function cnProvince(){
		$a = Yii::app()->cache->get('cnProvices');
		if(empty($a)){
			$rs = CnProvince::model()->findAll('1 ORDER BY weight,init');
			$a = [];
			foreach($rs as $r){
				$p = ['id' => $r->id, 'value' => $r->name, 'label' => $r->init.' - '.$r->name];
				if(1 == CnCity::model()->count('pid = :id', [':id' => $r->id])){
					$c = CnCity::model()->find('pid = :id', [':id' => $r->id]);
					$p['ocid'] = $c->id;
					$p['oczip'] = $c->getZip();
				}
				$a[] = $p;
			}
			Yii::app()->cache->set('cnProvices', $a, 8640000);
		}
		return $a;
	}
	
	public static function statData($rs){
		$data = array();
		for($i = 11; $i > 0; $i--){
			$data[date('Yn', strtotime(date('Y-m-01').' -'.$i.' month'))] = 0;
		}
		$data[date('Yn')] = 0;
		foreach($rs as $r){
			$data[$r['ym']] = (int) round($r['qty']);
		}
		
		return array_values($data);
	}
	
	public static function emptyDate($s){
		if($s == '0000-00-00') return '';
		else return $s;
	}
	
	public static function money2words($num, $currency='dollars'){
		list($num, $dec) = explode(".", $num);
		$grp = array('',' thousand',' million',' billion',' trillion',' quadrillion',' quintrillion',' sextillion',' septillion',' octillion',' nonillion',' decillion');
		$output = "";
	  
		if($num[0] == "-"){
		  $output = "negative ";
		  $num = ltrim($num, "-");
		}else if($num[0] == "+"){
		  $output = "positive ";
		  $num = ltrim($num, "+");
		}
	  
		if($num[0] == "0"){
		  $output .= "zero";
		}else{
		  $num = str_pad($num, 36, "0", STR_PAD_LEFT);
		  $group = rtrim(chunk_split($num, 3, " "), " ");
		  $groups = explode(" ", $group);
	  
		  $groups2 = array();
		  foreach($groups as $g) $groups2[] = self::convertThreeDigit($g[0], $g[1], $g[2]);
	  
		  for($z = 0; $z < count($groups2); $z++){
			 if($groups2[$z] != ""){
				$output .= $groups2[$z].$grp[11 - $z].($z < 11 && !array_search('', array_slice($groups2, $z + 1, -1))
				 && $groups2[11] != '' && $groups[11][0] == '0' ? " and " : ", ");
			 }
		  }
	  
		  $output = rtrim($output, ", ");
		}
		$output .= ' '.$currency;
		if($dec > 0){
		  $output .= " ".self::convertTwoDigit($dec[0], $dec[1]);
		  $output .= " cents";
		}
	  
		return strtoupper($output);
	}
	  
	public static function convertThreeDigit($dig1, $dig2, $dig3){
		$output = "";
		
		if($dig1 == "0" && $dig2 == "0" && $dig3 == "0") return "";
		if($dig1 != "0"){
			$output .= self::convertDigit($dig1)." hundred";
			if($dig2 != "0" || $dig3 != "0") $output .= " and ";
		}
		if($dig2 != "0") $output .= self::convertTwoDigit($dig2, $dig3);
		else if($dig3 != "0") $output .= self::convertDigit($dig3);
		return $output;
	}
	  
	public static function convertTwoDigit($dig1, $dig2){
		$d2 = array('', 'ten','twenty','thirty','forty','fifty','sixty','seventy','eighty','ninety');
		$d1 = array('', 'eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen');
		$d3 = array('','','twenty','thirty','forty','fifty','sixty','seventy','eighty','ninety');
		if($dig2 == "0"){
		  return $d2[$dig1];
		}
		else if($dig1 == "1"){
		  return $d1[$dig2];
		}else{
		  return $d3[$dig1].'-'.self::convertDigit($dig2);
		}
	}
	  
	public static function convertDigit($digit){
		$d = array('zero','one','two','three','four','five','six','seven','eight','nine');
		return $d[$digit];
	}
	
	public static function unlinkRecursive($dir, $deleteRootToo=true){ 
		if(!$dh = @opendir($dir)) return;
		while(false !== ($obj = readdir($dh))){
			if($obj == '.' || $obj == '..') continue;
			if(!@unlink($dir . '/' . $obj)) self::unlinkRecursive($dir.'/'.$obj, true);
		}
		closedir($dh);
		if($deleteRootToo) @rmdir($dir);
		return; 
	}
	
	public static function csvLine($input_text, $delimiter = ',', $text_qualifier = '"') {
		$text = trim($input_text);
		if(is_string($delimiter) && is_string($text_qualifier)) {
			$re_d = '\x' . dechex(ord($delimiter));            //format for regexp
			$re_tq = '\x' . dechex(ord($text_qualifier));    //format for regexp
			$text = preg_replace("/".$re_tq.$re_tq."/","@t@q",$text);
			$fields = array();
			$field_num = 0;
			while(strlen($text) > 0) {
				if($text[0] == $text_qualifier) {
					preg_match('/^' . $re_tq . '((?:[^' . $re_tq . ']|(?<=\x5c)' . $re_tq . ')*)' . $re_tq . $re_d . '?(.*)$/', $text, $matches);
					$value = str_replace('\\' . $text_qualifier, $text_qualifier, $matches[1]);
					$value = preg_replace("/\@t\@q/", $text_qualifier, $value);
					$text = trim($matches[2]);
					$fields[$field_num++] = $value;
				} else {
					preg_match('/^([^' . $re_d . ']*)' . $re_d . '?(.*)$/', $text, $matches);
					$value = $matches[1];
					$text = trim($matches[2]);
					$fields[$field_num++] = $value;
				}
			}
			return $fields;
		} else {
			return false;
		}
	}

	public static function bwf2warning($m, $s=true,$d=false){
		if(is_array($m)){
			$bwf = $m[0];
			$bwfs = $m[1];
		}else{
			$bwf = $m->bwf;
			$bwfs = $m::$bwfs;
		}
		if(empty($bwf)) return $s? '' : [];
		$wa = array();
		foreach($bwfs as $b => $w){
			if(((int) $bwf) & $b){
				if($s){
					$a = '';
					foreach(explode(' ', $w) as $ss){
						if(preg_match('/[\x{4e00}-\x{9fa5}]+/u', $ss)||$d){
							$a .=$ss;
						}else{
							$a.=$ss[0];
						}
					}
					$wa[$b] = $a;
				}else{
					$wa[$b] = $w;
				}
			}
		}
		return $s? '<span class="warn">'.implode('</span> <span class="warn">', $wa).'</span>' : $wa;
	}

	public static function unicode_trim($str) {
		return preg_replace('/^[\pZ\pC]+([\PZ\PC]*)[\pZ\pC]+$/u', '$1', $str);
	}

	public static function addFolderToZip($dir, $zipArchive, $zipdir = ''){ 
		if (is_dir($dir)){
			if ($dh = opendir($dir)){
				//Add the directory
				if(!empty($zipdir)) $zipArchive->addEmptyDir($zipdir);
				// Loop through all the files
				while (($file = readdir($dh)) !== false){
					//If it's a folder, run the function again!
					if(!is_file($dir . $file)){
						// Skip parent and root directories
						if( ($file !== ".") && ($file !== "..")){
							AppHelper::addFolderToZip($dir . $file . "/", $zipArchive, $zipdir . "/" . $file . "/"); 
						}
					}else{
						// Add the files
						$zipArchive->addFile($dir . $file, $zipdir . $file);
					}
				}
			}
		}
	}

	public static function resizeImg($f, $dw){
		$is = @getimagesize($f);
		if(empty($is)){
			Yii::log('Problem reading photo '.$f, 'error');
			return false;
		}
		$dh = round($dw/$is[0] * $is[1]);
		
		if($is[2]==1) {
			$im = @imagecreatefromgif($f);
		}elseif($is[2]==2) {
			$im = @imagecreatefromjpeg($f);
		}elseif($is[2]==3) {
			$im = @imagecreatefrompng($f);
		}elseif($is[2]==6) {
if (!function_exists('imagecreatefrombmp')) { function imagecreatefrombmp($filename) {
	// version 1.00
	if (!($fh = fopen($filename, 'rb'))) {
		trigger_error('imagecreatefrombmp: Can not open ' . $filename, E_USER_WARNING);
		return false;
	}
	// read file header
	$meta = unpack('vtype/Vfilesize/Vreserved/Voffset', fread($fh, 14));
	// check for bitmap
	if ($meta['type'] != 19778) {
		trigger_error('imagecreatefrombmp: ' . $filename . ' is not a bitmap!', E_USER_WARNING);
		return false;
	}
	// read image header
	$meta += unpack('Vheadersize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vcolors/Vimportant', fread($fh, 40));
	// read additional 16bit header
	if ($meta['bits'] == 16) {
		$meta += unpack('VrMask/VgMask/VbMask', fread($fh, 12));
	}
	// set bytes and padding
	$meta['bytes'] = $meta['bits'] / 8;
	$meta['decal'] = 4 - (4 * (($meta['width'] * $meta['bytes'] / 4)- floor($meta['width'] * $meta['bytes'] / 4)));
	if ($meta['decal'] == 4) {
		$meta['decal'] = 0;
	}
	// obtain imagesize
	if ($meta['imagesize'] < 1) {
		$meta['imagesize'] = $meta['filesize'] - $meta['offset'];
		// in rare cases filesize is equal to offset so we need to read physical size
		if ($meta['imagesize'] < 1) {
			$meta['imagesize'] = @filesize($filename) - $meta['offset'];
			if ($meta['imagesize'] < 1) {
				trigger_error('imagecreatefrombmp: Can not obtain filesize of ' . $filename . '!', E_USER_WARNING);
				return false;
			}
		}
	}
	// calculate colors
	$meta['colors'] = !$meta['colors'] ? pow(2, $meta['bits']) : $meta['colors'];
	// read color palette
	$palette = array();
	if ($meta['bits'] < 16) {
		$palette = unpack('l' . $meta['colors'], fread($fh, $meta['colors'] * 4));
		// in rare cases the color value is signed
		if ($palette[1] < 0) {
			foreach ($palette as $i => $color) {
				$palette[$i] = $color + 16777216;
			}
		}
	}
	// create gd image
	$im = imagecreatetruecolor($meta['width'], $meta['height']);
	$data = fread($fh, $meta['imagesize']);
	$p = 0;
	$vide = chr(0);
	$y = $meta['height'] - 1;
	$error = 'imagecreatefrombmp: ' . $filename . ' has not enough data!';
	// loop through the image data beginning with the lower left corner
	while ($y >= 0) {
		$x = 0;
		while ($x < $meta['width']) {
			switch ($meta['bits']) {
				case 32:
				case 24:
					if (!($part = substr($data, $p, 3))) {
						trigger_error($error, E_USER_WARNING);
						return $im;
					}
					$color = unpack('V', $part . $vide);
					break;
				case 16:
					if (!($part = substr($data, $p, 2))) {
						trigger_error($error, E_USER_WARNING);
						return $im;
					}
					$color = unpack('v', $part);
					$color[1] = (($color[1] & 0xf800) >> 8) * 65536 + (($color[1] & 0x07e0) >> 3) * 256 + (($color[1] & 0x001f) << 3);
					break;
				case 8:
					$color = unpack('n', $vide . substr($data, $p, 1));
					$color[1] = $palette[ $color[1] + 1 ];
					break;
				case 4:
					$color = unpack('n', $vide . substr($data, floor($p), 1));
					$color[1] = ($p * 2) % 2 == 0 ? $color[1] >> 4 : $color[1] & 0x0F;
					$color[1] = $palette[ $color[1] + 1 ];
					break;
				case 1:
					$color = unpack('n', $vide . substr($data, floor($p), 1));
					switch (($p * 8) % 8) {
						case 0:
							$color[1] = $color[1] >> 7;
							break;
						case 1:
							$color[1] = ($color[1] & 0x40) >> 6;
							break;
						case 2:
							$color[1] = ($color[1] & 0x20) >> 5;
							break;
						case 3:
							$color[1] = ($color[1] & 0x10) >> 4;
							break;
						case 4:
							$color[1] = ($color[1] & 0x8) >> 3;
							break;
						case 5:
							$color[1] = ($color[1] & 0x4) >> 2;
							break;
						case 6:
							$color[1] = ($color[1] & 0x2) >> 1;
							break;
						case 7:
							$color[1] = ($color[1] & 0x1);
							break;
					}
					$color[1] = $palette[ $color[1] + 1 ];
					break;
				default:
					trigger_error('imagecreatefrombmp: ' . $filename . ' has ' . $meta['bits'] . ' bits and this is not supported!', E_USER_WARNING);
					return false;
			}
			imagesetpixel($im, $x, $y, $color[1]);
			$x++;
			$p += $meta['bytes'];
		}
		$y--;
		$p += $meta['decal'];
	}
	fclose($fh);
	return $im;
}}
			$im = @imagecreatefrombmp($f);
		}
		if(!$im) return false;
		$rim = imagecreatetruecolor($dw, $dh);
		imagealphablending($rim, false);
		imagesavealpha($rim, true);
		imagecopyresampled($rim, $im, 0, 0, 0, 0, $dw, $dh, $is[0], $is[1]);
		imagedestroy($im);
		
		return $rim;
	}

	public static function getDelayClass($d){
		if($d > 10){
			return 'red';
		}elseif($d > 5){
			return 'orange';
		}
		return '';
	}

	public static function hasUniChinese($str){
		return preg_match('/[\x{4e00}-\x{9fa5}]+/u', $str);
	}

	public static function semiAngle($str){
		$arr = ['０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4','５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9','Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E','Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J','Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O','Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T','Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y','Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd','ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i','ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n','ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's','ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x','ｙ' => 'y', 'ｚ' => 'z','（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[','】' => ']', '〖' => '[', '〗' => ']', '”' => '"', '“' => '"', '｛' => '{', '｝' => '}', '《' => '<','》' => '>','％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-','：' => ':', '。' => '.', '、' => ',', '，' => ',', '、' => '.','；' => ';', '？' => '?', '！' => '!', '…' => '-', '‖' => '|', '`' => '\'', '`' => '\'', '｜' => '|', '〃' => '"','　' => ' '];
		return strtr($str, $arr);
	}

	/**
	 * clear and remove all special characters
	 * @param $msg
	 */
	public static function removeSpecialChars($msg){
		// $simpleMsg = Edimsg::strEscape($msg);
		$simpleMsg = AppHelper::semiAngle($simpleMsg);

		if ( AppHelper::hasUniChinese($simpleMsg) ) {
			// remove all chinese characters
			$simpleMsg = preg_replace('/[\x{4e00}-\x{9fa5}]+/u', '', $simpleMsg);
			$simpleMsg = trim($simpleMsg);
		}

		// remove sensitive message
		// $simpleMsg = preg_replace('/[^\w\d,\.\:\"\-\'\?\s]+/', '', $simpleMsg);
		return $simpleMsg;
	}

	/**
	 * get import agent extra settings
	 * @param $k
	 * @return null
	 */
	public static function getAgtSettings($k){
		$f = Yii::app()->basePath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'settings_'.Yii::app()->params['agent_id'].'.php';
		$v = null;
		if(is_file($f)){
			$agtset = include($f);
			if(isset($agtset[$k])) $v = $agtset[$k]['value'];
		}
		return $v;
	}

	function utf8Split($str, $len = 1){
		$arr = array();
		$strLen = mb_strlen($str);
		for ($i = 0; $i < ceil($strLen/$len); $i++){
			$arr[] = mb_substr($str, $i*$len, $len);
		}
		return $arr;
	}

	public static function fixBrokenUTF8($s){
		return preg_replace('/((?:[\x00-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3}){1,100})|./x', '$1', $s);
	}

	public static function safeJsonEncode($o){
		$s = json_encode($o);
		if(json_last_error() == 5){
			if(!function_exists('object_to_array')){
				function object_to_array($object){
					if(is_object($object)) {
						return array_map(__FUNCTION__, get_object_vars($object));
					}elseif(is_array($object)) {
						return array_map(__FUNCTION__, $object);
					}else{
						return $object;
					}
				}
			}
			if(is_object($o)) $o = object_to_array($o);
			$a = var_export($o, true);
			$a = self::fixBrokenUTF8($a);
			eval('$o = '.$a.';');
			return json_encode($o);
		}else{
			return $s;
		}
	}

	public static function mbstr_split($str, $len = 1){
		$sl = mb_strlen($str);
		$res = [];
		$c = ceil($sl / $len);
		for($i = 0; $i < $c; $i++){
			$res[] = mb_substr($str, $i*$len, $len);
		}
		return $res;
	}

	public static function exec($cmd, &$out=[], &$return=0){
		$proc = proc_open($cmd, array(0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("pipe", "w")), $pipes);
		
		if(is_resource($proc)){
			$out = preg_split('/[\n\r]+/', stream_get_contents($pipes[1]));
			fclose($pipes[1]);
			$return = proc_close($proc);
		}
		
		return $out[sizeof($out) - 1];
	}
	public static function getWorkingDays($startDate, $endDate) {
		  $begin = strtotime($startDate);
			 $end = strtotime($endDate);
		if ($begin > $end) {
					return 0;
		 } else {
				$no_days = 0;
				$weekends = 0;
				while ($begin <= $end) {
					$no_days++; // no of days in the given interval
					$what_day = date("N", $begin);
					if ($what_day > 5) { // 6 and 7 are weekend days
						$weekends++;
					};
					$begin += 86400; // +1 day
				};
				$working_days = $no_days - $weekends;

			return $working_days;
		}
	}
	/**
	 * to check if the email group is all valid email. 
	 * group like: "john@gmail.com;smith@hotmail.com" OR　"john@gamil.com"
	 * @param String $emailGroup
	 * @return boolean
	 */
	public static function validEmailGroup($emailGroup){
		if(empty($emailGroup)) return false;
		 foreach(preg_split('/[;,]+/i', $emailGroup) as $to){
			 if(empty(trim($to)))  continue;
			 if(!filter_var($to,FILTER_VALIDATE_EMAIL)){
				 return false;
			 }
		 }  
		 return true;
	}

	public static function ColNumnerToColCode($index)
    {
        return chr(64 + $index);
    }

    public static function mb_str_split($string, $length = 1) {
	    $result = [];
	    $stringLength = mb_strlen($string, 'UTF-8');
	    
	    for ($i = 0; $i < $stringLength; $i += $length) {
	        $result[] = mb_substr($string, $i, $length, 'UTF-8');
	    }
	    
	    return $result[0];
	}


}
