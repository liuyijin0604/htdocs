<?php
class RcptMaker{

	//receipt maker
	private static $tpl_metrics = [
		//[x, y, width, height, rotate, hue]
		['tpl_001', 220, 178, 400, 1000, -1.35, 1],
		['tpl_002', 145, 365, 490, 780, -1.2, 1],
		['tpl_003', 140, 225, 485, 1140, 0, 1],
		['tpl_004', 210, 185, 354, 1150, -0.6, 0],
		['tpl_005', 260, 130, 310, 1200, 0, 0],
		['tpl_006', 165, 150, 510, 980, 0, 1],
		['tpl_007', 160, 350, 480, 930, -1, 0],
		['tpl_008', 205, 150, 420, 1060, -1.5, 0],
		['tpl_009', 250, 125, 350, 1200, 2, 0],
		['tpl_010', 220, 260, 330, 1050, -4, 1],
		['tpl_011', 286, 80, 310, 1100, 0, 0],
		['tpl_012', 195, 210, 440, 900, 0.1, 1],
		['tpl_013', 195, 190, 450, 950, 0.75, 0],
		['tpl_014', 250, 98, 360, 1200, 0, 1],
		['tpl_015', 225, 80, 400, 1200, 0, 0],
		['tpl_016', 200, 135, 480, 1000, 0, 0],
		['tpl_017', 270, 95, 390, 1100, 0.1, 0],
		['tpl_018', 140, 245, 460, 1100, -2.3, 1],
		['tpl_019', 210, 180, 370, 920, -4.9, 1],
		['tpl_020', 265, 90, 320, 1100, 0, 1],
		['tpl_021', 160, 160, 510, 1000, 0, 1],
		['tpl_022', 270, 85, 320, 1150, 0, 1],
	];

	public static function getTpl($is){
		shuffle(self::$tpl_metrics);
		foreach(self::$tpl_metrics as $t){
			$h =  $t[3] / $is[0] * $is[1];
			if($h < 500) $h = 600;
			if($t[4] >= $h && $t[4] <= $h * 1.5) return [$t, $h];
		}
		return [false, false];
	}

	public static function makeReal($if, $of, $out=false){
		$magick = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'? 'C:\Progra~1\ImageMagick-7.0.4-Q16\magick.exe' : '/usr/bin/convert';
		$tpl_dir = dirname(Yii::app()->basePath).DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'rcpt_tpls';

		$rtt = [0, 90, 180, 270];
		$tf = @tempnam('tmp', 'rct');
		AppHelper::exec($magick.' '.$if.' -trim '.$tf);
		$is = getimagesize($tf);
		$tpl = self::getTpl($is);
		if(empty($tpl[0])){
			//echo $if." too high\n";
			return false;
		}
		AppHelper::exec($magick.' \( '.$tpl_dir.'/'.$tpl[0][0].'.jpg -resize "844x'.round(($tpl[1]*1.2)/$tpl[0][4] * 1500).'!" \) \( '.$tf.' -resize "'.$tpl[0][3].'x" -rotate '.$tpl[0][5].' \) -compose Darken_Intensity -geometry +'.$tpl[0][1].'+'.round(($tpl[1]*1.2)/$tpl[0][4] * $tpl[0][2]).' -composite -rotate '.$rtt[rand(0,3)].' -modulate '.rand(90,110).','.rand(40,80).','.($tpl[0][6] > 0? rand(0,200) : 100).' -quality 75 '.$of);
		if($out){
			header("Cache-Control: maxage=1");
			header('Content-Type: image/jpeg');
			readfile($of);
		}
		unlink($tf);
		return true;
	}
}