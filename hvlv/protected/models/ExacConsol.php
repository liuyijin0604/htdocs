<?php
class ExacConsol extends Consol{

	public static $my_type = 20;

	public static $states = array(
		10 => 'New',
		20 => 'Confirmed',
		30 => 'Reported',
		40 => 'Acknowledged',
		50 => 'Withdrawn',
		70 => 'Dispatched',
		80 => 'Completed',
		100 => 'Cancelled',
	);

	public function rules(){
		$rules = array(
			array('owner_id', 'required'),
		);
		return array_merge(parent::rules(), $rules);
	}

    public function relations(){
        $r = parent::relations();
        $r['shipments'] = array(self::HAS_MANY, 'ExAfs', 'consol_id');
        return $r;
    }

	public function genNo(){
		$n = 'AF'.date('ymd', strtotime($this->created));
		$s = self::model()->count('no LIKE :n', [':n' => $n.'%']) + 1;
		return $n.sprintf('%02d',$s).substr($this->pol,2);
	}

	public function newESM(){
		$em = new Edimsg;
		$em->dt = date('Y-m-d H:i:s');
		$em->type = 'ESM';
		$em->sender = Yii::app()->params['ics']['testing']? Yii::app()->params['ics']['test_site'] : Yii::app()->params['ics']['prod_site'];
		$em->receiver = Yii::app()->params['ics']['customs_id'];
		$em->status = 19;
		$em->fid = $this->id;
		if(empty($this->etd) || $this->etd == '0000-00-00') $em->addError('msg', 'ETD missing');
		$ils = '';
		$ss = [];
		$j = 1;
		$trans = Yii::app()->db->beginTransaction();
		try{
		foreach($this->shipments as $i=>$p){
			if(!empty($ss) && !in_array($p->id, $ss)) continue;
			$goods = AppHelper::semiAngle($p->getGoods());
			if(AppHelper::hasUniChinese($goods)){
				//$em->addError('msg', $p->hbn.': goods detail has Chinese');
				$goods = preg_replace('/[\x{4e00}-\x{9fa5}]+/u', '', $goods);
			}
			$goods = trim($goods);
			$goods = Edimsg::strEscape($goods);
			//if(empty($goods)) $goods = 'Health Product';
			$goods = rtrim(substr($goods, 0, 128), '?');
			if(empty($goods)) $em->addError('msg', $p->hbn.': goods detail missing');

			if(AppHelper::hasUniChinese($p->cnor->name)) $em->addError('msg', $p->hbn.': goods detail missing');
			  
			//RFF+HWB:".$p->hbn."'
		$ils .= "CNI+".$j++."'
CNT+11:".$p->pkg."'
RFF+TL:".(empty($p->exm)? 'EXLV' : $p->exm)."'
LOC+28+CN::5'
NAD+GO+++".substr(Edimsg::strEscape($p->cnor->name), 0, 35)."'
GID+1'
FTX+AAA+++".$goods."'
";
			$p->status = 30;
			$p->save();
		}
			$trans->commit();
		} catch (Exception $ex) {
			$trans->rollback();
			throw $ex;
		}

		if($em->hasErrors()) return $em;
		$em->save();
		$em->mid = sprintf('%06s', substr($em->id, -6));
		$vn = Edimsg::model()->count('type = :t AND fid = :fid', [':t' => 'ESM', ':fid' => $this->id]) + 1;
		
		$msg = "UNH+".$em->mid."+CUSCAR:D:99B:UN'
BGM+87:::ESM+".$this->no."-".$vn.":".$vn."+9'
TDT+20+++6'
DTM+136:".date("Ymd", strtotime($this->etd)).":102'
GIS+C:121:95'
CNT+11:".($j-1)."'
";
		$msg .= $ils;
		$mc = substr_count($msg, "\n")+1;
		$msg .= "UNT+".$mc."+".$em->mid."'";
		$em->msg = $msg;
		$em->status = 20;
		if(Yii::app()->params['ics']['testing']) $em->mdata['test'] = 1;
		$em->save();
		
		return $em;
	}
	
	public function withdrawESM(){
		$em = new Edimsg;
		$em->dt = date('Y-m-d H:i:s');
		$em->type = 'ESM';
		$em->sender = Yii::app()->params['ics']['testing']? Yii::app()->params['ics']['test_site'] : Yii::app()->params['ics']['prod_site'];
		$em->receiver = Yii::app()->params['ics']['customs_id'];
		$em->status = 20;
		$em->fid = $this->id;
		$em->save();
		$em->mid = sprintf('%06s', substr($em->id, -6));
		$vn = Edimsg::model()->count('type = :t AND fid = :fid', [':t' => 'ESM', ':fid' => $this->id]) + 1;
		
		$msg = "UNH+".$em->mid."+CUSCAR:D:99B:UN'
BGM+87:::ESM+".$this->no.":".$vn."+50'
RFF+AIZ:".$this->mdata['aiz']."'
TDT+20+++6'
DTM+136:".date("Ymd", strtotime($this->etd)).":102'
GIS+C:121:95'
CNT+11:".$this->totPacks()."'
";
		$mc = substr_count($msg, "\n")+1;
		$msg .= "UNT+".$mc."+".$em->mid."'";
		$em->msg = $msg;
		if(Yii::app()->params['ics']['testing']) $em->mdata['test'] = 1;
		$em->save();
		
		return $em;	
	}
}
