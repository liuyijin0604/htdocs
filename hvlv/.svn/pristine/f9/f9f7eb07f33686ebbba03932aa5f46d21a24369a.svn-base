<?php

class ajfCommand extends CConsoleCommand {
	private $db;
	private $args;
	private $debug = false;
	public $tmp, $lidf, $ts, $day, $hr, $mn;

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
		$pid = $this->tmp.'ajf.pid';
		$this->lidf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'ajf.php';

		if(is_file($pid) && filectime($pid) > time() - 1800 && !$this->debug) return false;
		file_put_contents($pid, '1');
		
		if(!empty($args[0]) && method_exists($this, $args[0])){
			$this->{$args[0]}();
		}else{
			$this->fetchID();
		}
		unlink($pid);
	}

	public function fetchID(){
		$urlBase = 'http://222.190.116.170:3800/';
		$c = new curl($urlBase.'management/login');
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_REFERER, $urlBase);
		$c->setopt(CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0', 'Cache-Control: no-cache', 'Content-Type: application/x-www-form-urlencoded; charset=utf-8', 'X-MicrosoftAjax: Delta=true', 'X-Requested-With: XMLHttpRequest'));
		$c->setopt(CURLOPT_POST, true);
		$postr = $c->asPostString(array(
			'email' => '801506005',
			'password' => 'lgpgio',
		));
		$c->setopt(CURLOPT_POSTFIELDS, $postr);
		if($this->debug) echo "Logging in...\n";
		if(!$c->exec()) die("Problem open page /management/login\n");
		$cookie = empty($c->header['Set-Cookie'])? '' : $c->header['Set-Cookie'];
		$c = new curl($urlBase.'supply/express_maintain');
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_REFERER, $urlBase);
		$c->setopt(CURLOPT_COOKIE, $cookie);
		$c->setopt(CURLOPT_POST, true);
		$postr = $c->asPostString(array(
			'start_date' => date('Y-m-d H:i:s', strtotime('-1 day')),
			'end_date' => date('Y-m-d H:i:s'),
		));
		$c->setopt(CURLOPT_POSTFIELDS, $postr);
		if($this->debug) echo "Getting list...\n";
		if(!$c->exec()) die("Problem open page /supply/express_maintain\n");
		$html = $c->result;

		preg_match_all('/supply\/express_show_detail\?glide_no=0*([^"\']+)/ms', $html, $m);
		$got = [];
		$t = 0;
		$lid = include($this->lidf);
		foreach($m[0] as $i => $u){
			if($m[1][$i] < $lid || in_array($m[1][$i], $got)) continue;
			sleep(1);
			if($this->debug) echo 'Getting '.$m[1][$i]."\n";
			$c = new curl($urlBase.$u);
			$c->setopt(CURLOPT_FOLLOWLOCATION, true);
			$c->setopt(CURLOPT_REFERER, $urlBase);
			$c->setopt(CURLOPT_COOKIE, $cookie);
			if(!$c->exec()){
				if($this->debug) echo "Problem open page ".$u."\n";
				continue;
			}else{
				$p = $c->result;
				preg_match_all('/(收件人姓名|电话|身份证号码):<\/td><td>\&nbsp;([^<]+)<\/td>/Ums', $p, $dm);
				preg_match_all('/class=\'photo\' src="([^"]+png|jpg|gif)"/Ums', $p, $pm);
				//print_r($dm[2]);
				//print_r($pm[1]);
				if(empty($pm[1])){
					if($this->debug) echo "Photo Incomplete ".$dm[2][2]."\n";
					continue;
				}
				$cnid = CnID::model()->find('no = :n OR meta LIKE :c', [':n' => $dm[2][2], ':c' => '%"'.$m[1][$i].'#%']);
				if($cnid){
					if($this->debug) echo 'Already got ID '.$dm[2][2]."\n";
				}else{
					if($this->debug) echo 'ID '.$dm[2][2];
					$cnid = new CnID;
					$cnid->status = 10;
					$cnid->no = strtoupper($dm[2][2]);
					$cnid->mobile = preg_replace('/[^\d]+/', '', $dm[2][1]);
					$cnid->name = $dm[2][0];
					$cnid->mdata['ajf'] = $m[1][$i];
					//$cnid->save();
					$fids = [];
					foreach($pm[1] as $j=>$pho){
						$tf = tempnam($this->tmp, "idp");
						@file_put_contents($tf, file_get_contents($pho));
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
					$t++;
					if($this->debug) echo " - saved\n";
				}
				$got[] = $m[1][$i];
			}
		}
		if(!empty($got)) file_put_contents($this->lidf, "<?php\nreturn ".array_pop($got).';');

		//fetch previous problem ids
		$rs = CnID::model()->findAll("bwf > 0 AND meta LIKE '%ajf%' AND status = 10");
		foreach($rs as $cnid){
			$id = $cnid->mdata['ajf'];
			if($id > 0){
				sleep(1);
				if($this->debug) echo 'Getting '.$id."\n";
				$u = 'supply/express_show_detail?glide_no=0'.$id;
				$c = new curl($urlBase.$u);
				$c->setopt(CURLOPT_FOLLOWLOCATION, true);
				$c->setopt(CURLOPT_REFERER, $urlBase);
				$c->setopt(CURLOPT_COOKIE, $cookie);
				if(!$c->exec()){
					if($this->debug) echo "Problem open page ".$u."\n";
					continue;
				}else{
					$p = $c->result;
					preg_match_all('/(收件人姓名|电话|身份证号码):<\/td><td>\&nbsp;([^<]+)<\/td>/Ums', $p, $dm);
					preg_match_all('/class=\'photo\' src="([^"]+png|jpg|gif)"/Ums', $p, $pm);
					//print_r($dm[2]);
					//print_r($pm[1]);
					if(empty($pm[1])){
						if($this->debug) echo "Photo Incomplete ".$dm[2][2]."\n";
						continue;
					}
					if($this->debug) echo 'ID '.$dm[2][2];
					$cnid->no = $dm[2][2];
					$cnid->mobile = $dm[2][1];
					$cnid->name = $dm[2][0];
					$fids = [];
					foreach($pm[1] as $j=>$pho){
						$tf = tempnam($this->tmp, "idp");
						file_put_contents($tf, file_get_contents($pho));
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
					if($this->debug) echo " - saved\n";
				}
			}
		}

		echo 'Total '.$t." IDs saved\n";
	}

}
