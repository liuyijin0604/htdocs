<?php

class jygCommand extends CConsoleCommand {
	private $db;
	private $args;
	private $debug = false;
	public $getdays = 15;
	public $tmp, $ts, $day, $hr, $mn;
	//public $urlBase = 'http://go2.jinying.com:8000/';
	public $urlBase = 'http://45.115.36.123:8000/';

	public function run($args) {
		$this->db = Yii::app()->getDb();
		$this->args = $args;
		$this->ts = time();
		$this->day = date('N');
		$this->hr = date('h');
		$this->mn = ltrim(date('i'), 0);
		foreach($this->args as $ag){
			if($ag == '-d') $this->debug = true;
		}
		$this->tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
		$pid = $this->tmp.'jyg.pid';

		if(is_file($pid) && filectime($pid) > time() - 3600 && !$this->debug) return false;
		file_put_contents($pid, '1');
		
		if(!empty($args[0]) && method_exists($this, $args[0])){
			$this->{$args[0]}();
		}else{
			$this->fetchID();
		}
		unlink($pid);
	}

	public function saveID($c, $id, $eid = 0){
		$postr = $c->asPostString(array(
				'do' => 'orderInfo',
				'post_idx' => $id,
			));
		$c->setopt(CURLOPT_POSTFIELDS, $postr);
		if($this->debug) echo "Getting ID...\n";
		if(!$c->exec()) die("Problem getting ID\n");
		$r = json_decode($c->result)[0];
		if(empty($r)){
			if($this->debug) echo "Problem reading ID data:\n".$c->result."\n";
			return false;
		}
		if($this->debug) echo 'ID '.$r->id_card_no."\n";
		$cnid = empty($eid)? new CnID : CnID::model()->findByPk($eid);
		$cnid->status = 10;
		$cnid->no = strtoupper($r->id_card_no);
		$cnid->mobile = $r->receiver_mobile;
		$cnid->name = $r->receiver_name;
		$cnid->mdata['jyg'] = $id;
		//$cnid->save();
		$fids = [];
		
		foreach([$r->id_card_img_back, $r->id_card_img_forword] as $j=>$s){
			$tf = tempnam($this->tmp, "idp");
			@file_put_contents($tf, file_get_contents($this->urlBase.'uploads/camera/'.$s));
			if(filesize($tf) < 10240){
				unlink($tf); 
				continue;
			}
			$img = AppHelper::resizeImg($tf, 600);
			if($img) @imagejpeg($img, $tf);
			$fids[] = FileRepo::storeFile($tf, $cnid->no.'_'.($j+1).'.jpg', 60, $cnid->id);
			unlink($tf);
		}

		$cnid->front = empty($fids[0])? 0 : $fids[0];
		$cnid->back = empty($fids[1])? 0 : $fids[1];
		$cnid->save();
		$cnid->joinPhoto();
		$cnid->save();
		if($this->debug) echo $r->id_card_no." - saved\n";

		return true;
	}

	public function fetchID(){
		//login
		$c = new curl($this->urlBase.'ajax/user/manage_login');
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_REFERER, $this->urlBase.'management/login');
		$c->setopt(CURLOPT_HTTPHEADER, array('Host: go2.jinying.com','User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0', 'Cache-Control: no-cache', 'Content-Type: application/x-www-form-urlencoded; charset=utf-8', 'X-MicrosoftAjax: Delta=true', 'X-Requested-With: XMLHttpRequest'));
		$c->setopt(CURLOPT_POST, true);
		$postr = $c->asPostString(array(
			'name' => '811604003',
			'password' => '@asdfggg',
		));
		$c->setopt(CURLOPT_POSTFIELDS, $postr);
		if($this->debug) echo "Logging in...\n";
		if(!$c->exec()) die("Problem logging in\n");
		$cookie = empty($c->header['Set-Cookie'])? '' : $c->header['Set-Cookie'];

		//get count
		$c->setopt(CURLOPT_URL, $this->urlBase.'management/ImportData');
		$c->setopt(CURLOPT_REFERER, $this->urlBase.'/management/ImportData');
		$c->setopt(CURLOPT_COOKIE, $cookie);
		$postr = $c->asPostString(array(
			'do' => 'count',
			'query' => ['timeBegin' => date('Y-m-d', strtotime('-'.$this->getdays.' Day')), 'timeEnd' => date('Y-m-d', strtotime('-1 Day'))]
		));
		$c->setopt(CURLOPT_POSTFIELDS, $postr);
		if($this->debug) echo "Getting count...\n";
		if(!$c->exec()) die("Problem reading count\n");
		$r = json_decode($c->result);
		$count = $r->count;
		echo $count." items found\n";
		$tpgs = ceil($count / 100);

		//get list pages
		$os = [];
		while($tpgs > 0){
			$postr = $c->asPostString(array(
				'do' => 'query',
				'page' => $tpgs,
				'query' => ['page' => $tpgs, 'rows' => 100, 'timeBegin' => date('Y-m-d', strtotime('-'.$this->getdays.' Day')), 'timeEnd' => date('Y-m-d', strtotime('-1 Day')), 'timeSelect' => 0],
			));
			$c->setopt(CURLOPT_POSTFIELDS, $postr);
			if($this->debug) echo "Getting list...\n";
			if(!$c->exec()) die("Problem getting list\n");
			$r = json_decode($c->result);
			array_reverse($r->rows);
			foreach($r->rows as $o){
				if(isset($os[$o->id_card_no])) continue;
				if($o->pay_time > time() - 86400) continue;
				if(CnID::model()->count('no = :n AND status IN (10, 15, 18, 20)', [':n' => $o->id_card_no]) > 0) continue;
				$os[$o->id_card_no] = $o->eb_order_goods_id;
			}
			$tpgs--;
		}

		$t = 0;

		if(!empty($os)){
			sort($os);
			foreach($os as $o){
				if($this->saveID($c, $o)) $t++;
			}
		}

		//fetch previous problem ids
		$rs = CnID::model()->findAll("bwf > 0 AND meta LIKE '%jyg%' AND status = 10");
		foreach($rs as $cnid){
			if($this->saveID($c, $cnid->mdata['jyg'], $cnid->id)) $t++;
		}

		echo 'Total '.$t." IDs saved\n";
	}

}
