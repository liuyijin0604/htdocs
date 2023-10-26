<?php

class ApiAnzuAction extends CAction{
	public $ctlr, $debug;
	public $logging = false;
	
	public function run(){
		$this->ctlr = $this->getController();
		$this->debug = !empty($_POST['test']);

		if(!empty($this->ctlr->data->action) && method_exists($this, $this->ctlr->data->action)){
			$this->log($this->ctlr->data->action);
			$this->{$this->ctlr->data->action}();
		}else{
			throw new CHttpException(400, 'API method not found!');
		}
	}
	
	public function log($l){
		if(!$this->logging) return;
		$tmp=Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
		file_put_contents($tmp.'anzu_api.log', date('Y-m-d H:i:s').' '.$l."\n",FILE_APPEND);
	}

	public function expInv(){
		$d = $this->ctlr->data;

		if($d->act == 'check'){
			$rs = PickupList::model()->findAll(['condition' => 'status < 100 AND bwf & 1 = 0 AND dpt_id = :wid AND created < CURDATE() AND created >= :cod', 'params' => [':wid' => $d->wid, ':cod' => '2018-03-01'], 'order' => 'created']);
			$html = '<p>Total <b>'.sizeof($rs).'</b> Receipt Lists</p>';
			$db = Yii::app()->getDb();
			$html .= '<ol class="chklst">';
			$gtt = 0;
			$okc = 0;
			foreach($rs as $r){
				$sql = 'SELECT COUNT(id) AS c, fid, id FROM mani_map WHERE mani_id = :id GROUP BY fid having c > 1';
				$qs = $db->createCommand($sql)->bindValues([':id'=> $r->id])->queryAll();
				foreach($qs as $q){
					$sql = 'DELETE FROM mani_map WHERE mani_id = :mid AND fid = :fid AND id != :id';
					$db->createCommand($sql)->bindValues([':mid'=> $r->id, ':fid' => $q['fid'], ':id' => $q['id']])->execute();
				}

				$err = [];
				//add receiptless to receipt list
				$sql = 'SELECT s.id FROM shipment s LEFT JOIN (SELECT mm.fid FROM mani_map mm INNER JOIN manifest m ON m.id = mm.mani_id WHERE m.type = 40 AND m.fwd_id = :aid AND m.created > DATE_SUB(DATE(:d), INTERVAL 7 DAY)) r ON s.id = r.fid WHERE s.created = DATE(:d) AND s.status NOT IN (8,9,10,100,102,104) AND s.agent_id = :aid AND s.type = 20 AND r.fid IS NULL';
				$ss = $db->createCommand($sql)->bindValues([':d'=> $r->created, ':aid' => $r->fwd_id])->queryAll();

				if(!empty($ss)){
					foreach($ss as $s){
						$p = ExParcel::model()->findByPk($s['id']);
						$r->map($p);
					}
				}

				if(empty($r->lines)){
					$r->status = 100;
					$r->save();
					//echo '<li><a href="'.$this->createUrl('pickupList/update', ['id' => $r->id]).'" class="tab_link" title="'.$r->ref.'">'.$r->ref.' ('.$r->owner->name.')</a> - Empty</li>';
					continue;
				}
				$ipc = 0;
				$stt = 0;
				foreach($r->lines as $l){
					$p = $l->mm();
					if(empty($p) || in_array($p->status, [100, 102, 104])) continue;
					$ipc++;
					//check duplicate
					if($p->status == 12){
						$c = $db->createCommand('SELECT s.id FROM shipment s INNER JOIN mani_map m ON s.id = m.fid WHERE m.mani_id = :mid AND s.id != :id AND hbn = :hbn AND s.status >= 14')->bindValues([':mid'=> $r->id, ':id' => $l->fid, ':hbn' => $p->hbn])->queryScalar();
						if(!empty($c)){//cancel duplicate
							$p->status = 100;
							$p->save();
						}
					}

					if($p->status <= 14){//status
						$err[] = '<a href="https://os.pcaex.com'.$this->ctlr->createUrl('exParcel/update', ['id' => $p->id]).'" class="tab_link" title="'.$p->hbn.'">'.$p->hbn.' not received</a>';
					}elseif((empty($p->weight) || $p->weight == 0) || ($p->status > 14 && $p->status < 60 && (!empty($p->agent->extra['wt_check']) && (empty($p->wtck) || $p->wtck == 0)))){ //empty weight in_array($p->goodsType(), ['O', 'X']) && 
						$err[] = '<a href="https://os.pcaex.com'.$this->ctlr->createUrl('exParcel/update', ['id' => $p->id]).'" class="tab_link" title="'.$p->hbn.'">'.$p->hbn.' has no weight</a>';
					}
					if($p->agent_id != $r->fwd_id && (empty($p->agent->accode) || $p->agent->accode != $r->owner->accode)){ //mismatch agent
						$err[] = '<a href="https://os.pcaex.com'.$this->ctlr->createUrl('exParcel/update', ['id' => $p->id]).'" class="tab_link" title="'.$p->hbn.'">'.$p->hbn.' agent mismatch</a>';
					}
					//appear in multiple rl
					$mpl = PickupList::model()->with('lines')->findAll('t.id != :mid AND lines.fid = :id', [':id' => $p->id, ':mid' => $r->id]);
					if(!empty($mpl)){
						$e = '<a href="https://os.pcaex.com'.$this->ctlr->createUrl('exParcel/update', ['id' => $p->id]).'" class="tab_link" title="'.$p->hbn.'">'.$p->hbn.'</a> appears in other receipt list';
						foreach($mpl as $pl){
							$e .= ' <a href="https://os.pcaex.com'.$this->ctlr->createUrl('pickupList/update', ['id' => $pl->id]).'" class="tab_link" title="'.$pl->ref.'">'.$pl->ref.'</a>';
						}
						$err[] = $e;
					}

					$rate = $p->getAgentRate();
					if(empty($rate[1])){ //unable to rate
						$err[] = '<a href="https://os.pcaex.com'.$this->ctlr->createUrl('exParcel/update', ['id' => $p->id]).'" class="tab_link" title="'.$p->hbn.'">'.$p->hbn.' unable to rate</a>';
					}
					if(!empty($rate[2])) $stt += $rate[2];
				}

				if(empty($ipc)) continue;

				if(empty($err)){
					$html .= '<li><a href="https://os.pcaex.com'.$this->ctlr->createUrl('pickupList/update', ['id' => $r->id]).'" class="tab_link" title="'.$r->ref.'">'.$r->ref.' ('.$r->owner->name.')</a> &asymp;$'.round($stt).' - OK</li>';
					$gtt += $stt;
					$okc++;
				}else{
					$html .= '<li class="red"><a href="https://os.pcaex.com'.$this->ctlr->createUrl('pickupList/update', ['id' => $r->id]).'" class="tab_link" title="'.$r->ref.'">'.$r->ref.' ('.$r->owner->name.')</a> - <span class="blocking">'.sizeof($err).' blockings</span><ul><li>';
					$html .= implode('</li><li>', $err);
					$html .= '</li></ul></li>';
				}
			}
			$html .= '</ol>';
			$html .= '<p>('.$okc.') OK Total: &asymp;$'.round($gtt).'</p>';
			
			$this->ctlr->output($html);
		}elseif($d->act == 'create'){
			$week_start = 6;
			$ais = [];
			$ndyet = [];
			$rs = PickupList::model()->findAll(['condition' => 'bwf & 1 = 0 AND dpt_id = :wid AND created < CURDATE() AND created >= :cod', 'params' => [':wid' => $d->wid, ':cod' => '2018-03-01'], 'order' => 'created']);
			foreach($rs as $r){
				if(empty($r->lines) || in_array($r->fwd_id, $ndyet)) continue;
				$bd = $r->billdate();
				if(strtotime($bd) > time()){
					$ndyet[] = $r->fwd_id;
					continue;
				}
				$err = false;
				$pc = 0;
				foreach($r->lines as $l){
					$p = $l->mm();
					if(in_array($p->status, [100,102,104])) continue;

					if($p->status == 12){
						$err = true;
						break;
					}

					if($p->status == 14 || empty($p->weight) || $p->weight == 0){ //missing info
						$err = true;
						break;
					}
					if($p->agent_id != $r->fwd_id && $p->agent->accode != $r->owner->accode){ //mismatch agent
						$err = true;
						break;
					}
					$rate = $p->getAgentRate();
					if(empty($rate[1])){ //no rate
						$err = true;
						break;
					}
					$pc++;
				}
				if($err || $pc == 0){
					unset($ais[$r->fwd_id][$bd]);
					continue;
				}
				$ais[$r->fwd_id][$bd][] = $r;
			}

			foreach($ais as $aid => $bds){
				foreach($bds as $bd => $rs){
					//$inv = Invoice::createExInv($aid, $bd, $rs);
				}
			}
			$r = false;
			$this->ctlr->output($r);
		}
	}
}