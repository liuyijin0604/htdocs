<?php
class TurtleAPI{
	
	public $url = 'https://api.turtle-express.net/idcards/sync';
	public $key = '2fbe6b2d18f449d1a34f9db70f0537bb';
	public $secret = '56d014182dd20c08cad04a9e66904892';

	public function fetchByDate($df, $dt){
		$data = $this->request('?type=date&startDate='.$df.'&endDate='.$dt);
		foreach($data['idcards'] as $id){
			$this->saveCnID($id);
		}
	}

	public function fetchByName($names){
		$data = $this->request('?type=name&names='.urlencode(implode(',', $names)));
		foreach($data['idcards'] as $id){
			$this->saveCnID($id);
		}
	}

	public function saveCnID($id){
		$cnid = CnID::model()->find('no = :no AND status IN (15,18,20) AND bwf < 3', array(':no' => $id['code']));
		if(!empty($cnid)) return;

		$cnid = CnID::model()->find('no = :no AND status = 14', array(':no' => $id['code']));
		if(empty($cnid)) $cnid = new CnID;
		$cnid->name = $id['name'];
		$cnid->no = $id['code'];
		$cnid->status = 14;
		$cnid->save();

		$ocrSuccess = function($r)use(&$cnid){
			$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR;
			$tf = tempnam($tmp, "idp");

			if(!empty($r->id)){
				$cnid->name = $r->name;
				$cnid->no = $r->id;
				$cnid->save();
				$bin = $r->frontimage;
				$side = 'front';
			}else{
				$bin = $r->backimage;
				$side = 'back';
			}
			
			if(empty($cnid->{$side})){
				file_put_contents($tf, base64_decode($bin));
				$cnid->{$side} = FileRepo::storeFile($tf, $cnid->no.'_'.($side == 'front'? 1 : 2).'.jpg', 60, $cnid->id);
				$cnid->nolog = true;
				$cnid->save();
				$cnid->refresh();
			}
			if($cnid->front > 0 && $cnid->back > 0){
				$cnid->nolog = false;
				$cnid->joinPhoto();
				$cnid->status = 15;
				$cnid->save();
				$cnid->matchCnee();
				// echo $cnid->no, ' saved', PHP_EOL;
			}
			unlink($tf);
		};

		$flip = false;
		$r = CnID::youtuOCR(0, $id['idCardFront']);

		if($r->errorcode === 0){
			$ocrSuccess($r);
		}else{ //flip side and try
			$flip = true;
			$r = CnID::youtuOCR(1, $id['idCardFront']);
			if($r->errorcode === 0) $ocrSuccess($r);
		}
		$r = CnID::youtuOCR($flip? 0 : 1, $id['idCardBack']);
		if($r->errorcode === 0) $ocrSuccess($r);
	}

	public function request($u, $data = []){
		$c = new curl($this->url.$u);
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_SSL_VERIFYHOST, false);
		$ts = microtime(true)*10000;
		$sign = md5($this->key.$this->secret.$ts);
		$c->setopt(CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'x-api-key: '.$this->key, 'x-api-timestamp: '.$ts, 'x-api-sign: '.$sign]);
		// $c->setopt(CURLOPT_CUSTOMREQUEST, "POST");
		$c->exec();
		return json_decode($c->result, true);
	}

}