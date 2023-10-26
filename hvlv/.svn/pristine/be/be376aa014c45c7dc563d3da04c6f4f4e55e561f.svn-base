<?php
class jjFJM extends CFormModel
{
	public $db;
	
	public function __construct(){
		try{
			$this->db = new PDO('sqlite:'.Yii::app()->basePath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'FJM.db', null, null, array(PDO::ATTR_PERSISTENT => true));
		}catch(PDOException $e) {
			die ("SQLite error: " . $e->getMessage());
		}
	}

	public function fetchOne($sql){
		$sth = $this->db->query($sql);
		return $sth->fetch(PDO::FETCH_ASSOC);
	}

	public function fetchAll($sql){
		$sth = $this->db->query($sql);
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}

	/*public function getFJM($addr){
		return $this->fetchOne("SELECT FJBM, SFMC, CSMC FROM FJMB WHERE 
		(('".$addr."' LIKE SF||'%'||CS||'%'||DQ||'%') OR ('".$addr."' LIKE SF||'%'||CS||'%') OR
		('".$addr."' LIKE SF||'%'||DQ||'%') OR ('".$addr."' LIKE CS||'%'||DQ||'%') OR
		('".$addr."' LIKE CS||'%') OR ('".$addr."' LIKE DQ2||'%'))
		ORDER BY (LENGTH('".$addr."') - LENGTH(REPLACE('".$addr."',SF,''))-
		LENGTH(REPLACE('".$addr."',CS,''))) DESC,
		(LENGTH('".$addr."')-LENGTH(REPLACE('".$addr."',DQ,''))-
		LENGTH(REPLACE('".$addr."',DQ2,''))) DESC 
		LIMIT 0,1");
	}*/

	public function getCode($cnee){
		$qs = [
			"SF2 = '".trim($cnee->state)."' AND CS2 = '".trim($cnee->city)."' AND DQ2 = '".trim($cnee->suburb)."'",
			"SF2 = '".trim($cnee->state)."' AND CS2 = '".trim($cnee->city)."'",
			"SF2 = '".trim($cnee->state)."' AND DQ2 = '".trim($cnee->suburb)."'",
			"CS2 = '".trim($cnee->city)."' AND DQ2 = '".trim($cnee->suburb)."'",
			"CS2 = '".trim($cnee->city)."'",
		];

		foreach($qs as $q){
			$r = $this->fetchOne("SELECT FJBM, SFMC, CSMC FROM FJMB WHERE ".$q." LIMIT 0,1");
			if(!empty($r)) break;
		}

		return empty($r)? ['FJBM' => '', 'SFMC' => mb_substr($cnee->state, 0, 2), 'CSMC' => $cnee->city] : $r;
	}

	public static function model($c=__CLASS__){
		return new $c;
	}
}
