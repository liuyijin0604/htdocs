<?php
class EdiAwbConsol extends Consol{

	public static $my_type = 60;

    public $custom_log_note = '';

	public static $states = array(
		10 => 'New',
		20 => 'Confirmed',
		30 => 'Reported',
		40 => 'Acknowledged',
		50 => 'Withdrawn',
		70 => 'Dispatched',
		80 => 'Completed',
		90 => 'Cancelled',
	);

	public static $pols = ['106' => 'AUSYD', '218' => 'AUMEL', '530' => 'AUBNE', '788' => 'AUME2'];

	public static $pocs = array(
		//'CNSHA' => 'Shanghai',
		//'CNCT2' => 'Chengdu (Other)',
		'CNPEK' => 'IE BJ',
		'CNCAN' => 'IE GZ',
		'CNTA2' => 'IE QD',
		'CNCHQ' => 'IE CQ',
		'CNXMN' => 'AJ XM',
		'CNGYA' => 'AJ GY',
		'CNJMN' => 'JM YD',
		'CNJM2' => 'JM SF',
		'CNJNA' => 'KJ JN',
		//'CNSZX' => 'EMS SZ',
		'CNCSX' => 'JX CS',
		'CNCTU' => 'YP CD',
		'CNCA2' => 'GZ SF',
		'CNCA3' => 'GZ EMS',
		'CNTSN' => 'YP TJ',
		'CNTAO' => 'BY QD',
		'CNKMG' => 'WTK KM',
		'HKHKG' => 'Hongkong',
		'A2U' => 'A2U',
		'STO' => 'STO',
	);



	public $ocpd;

	public function rules(){
		$rules = array(
			array('owner_id,dpt_id, airline,pol, pod, awb,etd,etd,flight', 'required', 'on' => 'update'),
			array('ocpd', 'safe', 'on'=>'search'),
			array('owner_id, dpt_id', 'required', 'on' => 'create'),
		);
		return array_merge(parent::rules(), $rules);
	}

	public function relations(){
		$r = parent::relations();
		$r['shipments'] = array(self::HAS_MANY, 'ExParcel', 'consol_id');
		return $r;
	}

	public function getPoc(){
		return Yii::t(strtolower(__CLASS__), self::$pocs[$this->poc]);
	}

	public function genNo(){
		$ndf = date('ymd');
		$sn = self::model()->count('no LIKE :n', array(':n' => 'EA'.$ndf.'%')) + 1;
		return 'EA'.$ndf.sprintf('%02s', $sn).'WB';
	}

	public static function pol2dpt($p){
		foreach(self::$pols as $k => $v){
			if($p == $v) return $k;
		}
		return false;
	}

	public function handlingfee()
	{
		return;
	}

	public function getCourierList(){
		return [];
	}

    public function afterFind(){
        parent::afterFind();
        if ( isset($this->mdata['custom_log_note']) ) {
            $this->custom_log_note = $this->mdata['custom_log_note'];
        }
    }

    public function search($pgn = true, $ps = 30, $ec = false, $forgp = false, $bydate = false, $bySea = false, $defaultOrder = false){
		if(!empty($this->ocpd)){
			if(empty($ec)) $ec = new CDbCriteria;
			if(is_array($ec->with)){
				$ec->with[] = 'shipments.location';
			}else{
				$ec->with = empty($ec->with)? ['shipments.location'] : [$ec->with, 'shipments.location'];
			}
			$ec->together = true;
			$ec->group = 't.id';
			$ec->addCondition('location.id > 0');
		}

		if(Yii::app()->user->grp == 40 && !Acl::hasAccess('B:Export/AllDepots')){
			$this->pol = self::$pols[Yii::app()->user->org];
		}

		return parent::search($pgn, $ps, $ec);
	}
}
