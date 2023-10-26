<?php

class TrackingController extends PController
{
	public function actionIndex($code){
		$p = Shipment::model()->find('(hbn = :c OR ref = :c) AND status > 0 AND status != 100', array(':c' => $code));
		if(empty($p)){
			$csl = ChangeShipmentLabel::model()->find('pref=:pref', [':pref'=>$code]);
			if(!empty($csl)){
				$p = $csl->shipment;
			}
		}
		if(empty($p) || empty($p->tracks)){
			$o = false;
		}else{
			$o = $p->trackingInfo(empty($_GET['ttt'])? 0 : $_GET['ttt']);
		}
		echo json_encode($o);
	}

	public function actionAwb($code){
		$this->render('awb');
	}

	public function actionReadVvc(){
		$f = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'vvc_'.md5($_POST['data']).'.jpg';
		file_put_contents($f, base64_decode($_POST['data']));
		$gi = getimagesize($f);
		exec('/usr/bin/convert" '.$f.' -level "35,40%,0.45" -monochrome -negate -morphology Thinning "4x4:0,0,0,0, 0,1,1,0, 0,1,1,0, 0,0,0,0" -morphology Thinning:5 LineEnds -morphology Thinning "3:0,0,0, 0,1,0, 0,0,0" -negate -crop '.($gi[0]-2).'x'.($gi[1]-2).'+1+1 '.$f);
		$out = [];
		exec('/usr/bin/tesseract" -psm 8 '.$f.' stdout', $out);
		unset($f);
		return empty($out[0])? '' : $out[0];
	}

}