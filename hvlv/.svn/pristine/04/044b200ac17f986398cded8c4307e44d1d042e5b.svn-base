<?php
class oPDF{
	
	public static function procPipe($cmd, $in=false, $theout=true){
		$proc = proc_open($cmd, array(0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("pipe", "w")), $pipes);
		$out = '';
		
		if (is_resource($proc)) {
			if($in){
				fwrite($pipes[0], $in);
				fclose($pipes[0]);
			}
			if($theout){
				$out = stream_get_contents($pipes[1]);
				fclose($pipes[1]);
			}
			$trace = stream_get_contents($pipes[2]);
			fclose($pipes[2]);
			proc_close($proc);
			if($theout && empty($out)) throw new Exception($trace);
		}
		
		return $out;
	}

	public static function renderReport($report, $params=array(), $out=1, $fn=''){
		$html = self::renderReportHTML($report, $params);
		$pdf = self::html2pdf($html);
		return self::output($pdf, $out, $fn);
	}

    public static function renderPDFTest($view, $params=array(), $out=1, $fn=''){
        $html = self::renderHTML($view, $params);
        echo $html;
    }


	public static function renderPDF($view, $params=array(), $out=1, $fn=''){
		$html = self::renderHTML($view, $params);
		$pdf = self::html2pdf($html);
		return self::output($pdf, $out, $fn);
	}
	
	public static function renderImage($view, $params=array(), $out=1, $fn=''){
		$html = self::renderHTML($view, $params);
		return self::html2image($html, $out, $fn);
	}
	
	public static function html2pdf($html, $out=0, $fn=''){
		$pdf = '';
		$cmd = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'? 'C:\Progra~1\wkhtmltopdf\bin\wkhtmltopdf.exe' : '/usr/bin/wkhtmltopdf';
		preg_match('/<meta\s+name="wkhtmltopdf"\s+content="([^"]+)"/', $html, $m);
		if(!empty($m[1])) $cmd .= ' '.str_replace("'", '"', $m[1]);
		if(strlen($html) > 1048567){// use temp file
			$fn = tempnam(Yii::app()->basePath."/runtime", "wkht");
			rename($fn, $fn.'.html');
			file_put_contents($fn.'.html', $html);
			self::procPipe($cmd.' "'.$fn.'.html" "'.$fn.'.pdf"', false, false);
			$pdf = '';
			if(is_file($fn.'.pdf')){
				$pdf = file_get_contents($fn.'.pdf');
				unlink($fn.'.pdf');
			}
			unlink($fn.'.html');
		}else{
			$pdf = self::procPipe($cmd.' - -', $html);
		}

		return self::output($pdf, $out, $fn);
	}
	
	public static function html2image($html, $out=1, $fn=''){
		$img = '';
		$cmd = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'? 'C:\Progra~1\wkhtmltopdf\bin\wkhtmltoimage.exe' : '/usr/bin/wkhtmltoimage';
		preg_match('/<meta\s+name="wkhtmltoimage"\s+content="([^"]+)"/', $html, $m);
		if(!empty($m[1])) $cmd .= ' '.str_replace("'", '"', $m[1]);
		$img = self::procPipe($cmd.' - -', $html);

		if($out == 1){
			if(empty($fn)) $fn = preg_match('/\/([^\/]+\.jpg)$/i', Yii::app()->request->getRequestUri(), $m)? $m[1] : 'print_'.time().'.jpg';
			header("Cache-Control: maxage=1");
			header('Content-Type: image/jpeg');
			header('Content-Disposition: inline; filename="'.$fn.'"');
			echo $img;
			Yii::app()->end();
		}elseif($out == 2){
			return self::save($img, $fn);
		}else{
			return $img;
		}
	}
	
	public static function output($pdf, $out, $fn){
		if($out==1){
			if(empty($fn)) $fn = preg_match('/\/([^\/]+\.pdf)$/i', Yii::app()->request->getRequestUri(), $m)? $m[1] : 'print_'.time().'.pdf';
			header("Cache-Control: maxage=1");
			header('Content-Type: application/pdf');
			header('Content-Disposition: inline; filename="'.$fn.'"');
			echo $pdf;
			Yii::app()->end();
		}elseif($out == 2){
			return self::save($pdf, $fn);
		}else{
			return $pdf;
		}
	}
	
	public static function save($pdf, $fn=''){
		if(empty($fn)) $fn = tempnam(Yii::app()->basePath."/runtime", "pd_");
		file_put_contents($fn, $pdf);
		return $fn;
	}

	public static function renderReportHTML($view, $params=array()){
		$controller = isset(Yii::app()->controller)? Yii::app()->controller : Yii::app()->getCommand();
		return $controller->renderFile(Yii::app()->basePath.'/views/_report/'.$view.'.php', $params, true);
	}

	public static function renderHTML($view, $params=array()){
		$controller = isset(Yii::app()->controller)? Yii::app()->controller : Yii::app()->getCommand();
		return $controller->renderFile(Yii::app()->basePath.'/views/_pdf/'.$view.'.php', $params, true);
	}
	
	public static function mergePDF($in=array(), $out = 1, $unlink = true, $fn = ''){
		$cmd = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'? 'C:\Progra~1\wkhtmltopdf\pdftk.exe' : '/usr/bin/pdftk';
        $pdf = self::procPipe($cmd.' '.implode(' ', $in).' cat output -');
		if($unlink){
			foreach(array_unique($in) as $f){
				if(is_file($f)) @unlink($f);
			}
		}
		return self::output($pdf, $out, $fn);
	}

	public static function pdftotxt($pdfPath, $fn = ''){
		$cmd = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'? Yii::app()->basePath.DIRECTORY_SEPARATOR.'libs\pdftotext.exe' : Yii::app()->basePath.DIRECTORY_SEPARATOR.'libs/pdftotext';
		system($cmd.' "'.$pdfPath.'" "'.$fn.'"');
        self::procPipe($cmd.' "'.$pdfPath.'" "'.$fn.'"', false, false);
		$content = file_get_contents($fn);
		//unlink($pdfPath);
		unlink($fn);
		return $content;
	}
	
}