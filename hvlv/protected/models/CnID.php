<?php

/**
 * This is the model class for table "cn_id".
 *
 * The followings are the available columns in table 'cn_id':
 * @property string $id
 * @property string $status
 * @property string $no
 * @property string $name
 * @property string $city
 * @property string $mobile
 * @property string $created
 * @property string $front
 * @property string $back
 * @property string $joint
 * @property string $meta
 * @property string $bwf
 */
class CnID extends CActiveRecord
{
	
	public static $states = array(
		10 => 'New',
		14 => 'Partial Upload',
		15 => 'Client Upload',
		18 => 'Validated',
		20 => 'Matched',
		98 => 'Expired',
		99 => 'Invalid',
	);

	public static $bwfs = array(
		1 => 'Same Sides',
		2 => 'Missing Side',
		4 => 'Generated',
		8 => 'Not Clear',
		16 => 'Blacklisted',
	);

	public $nolog = false;
	public $custom_log_note = '';
	public $mdata = array();
	public $mnos, $mnames;

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'cn_id';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('no, name', 'required'),
			array('status, city, mobile, created, front, back, joint, meta, bwf', 'safe'),
			array('no, city', 'length', 'max'=>30),
			//array('no', 'unique'),
			array('mobile', 'length', 'max'=>30),
			array('name', 'length', 'max'=>50),
			array('front, back, joint', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, status, no, name, city, mobile, created, front, back, joint, bwf, mnos, mnames', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'photo_front' => array(self::BELONGS_TO, 'FileRepo', 'front'),
			'photo_back' => array(self::BELONGS_TO, 'FileRepo', 'back'),
			'photo_joint' => array(self::BELONGS_TO, 'FileRepo', 'joint'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'no' => 'No',
			'mnos' => 'Multiple No.',
			'status' => 'Status',
			'name' => 'Name',
			'mnames' => 'Multiple Names',
			'city' => 'City',
			'mobile' => 'Mobile',
			'created' => 'Created',
			'front' => 'Front',
			'back' => 'Back',
			'meta' => 'Meta',
			'joint' => 'Joint',
		);
	}

	public function beforeSave(){
		$this->name = trim($this->name);
		$this->no = trim($this->no);
		$this->mobile = preg_replace('/[\s\-\+]+/', '', $this->mobile);
		if(empty($this->created)) $this->created = date('Y-m-d H:i:s');
		if($this->status < 18 && !empty($this->photo_front) && !empty($this->photo_back) && $this->photo_front->hash == $this->photo_back->hash) $this->bwf = $this->bwf | 1;
		else $this->bwf = $this->bwf & (~ 1);
		if(empty($this->front) || empty($this->back)){
			$this->bwf = $this->bwf | 2;
		}else{
			if($this->status == 14) $this->status = 15;
			$this->bwf = $this->bwf & (~ 2);
		}
		if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);
		return true;
	}

	public function afterSave(){
		if(!$this->nolog && !empty($this)){
			$extra = empty($this->custom_log_note)? array() : array('note' => $this->custom_log_note);
			Log::add($this, $this->isNewRecord? 3 : 4, array_merge(array('status' => $this->getStatus()), $extra));
		}		
		if(!empty($this->photo_front) && $this->photo_front->name != $this->no.'_1.jpg'){
			$this->photo_front->name = $this->no.'_1.jpg';
			$this->photo_front->save();
		}
		if(!empty($this->photo_back) && $this->photo_back->name != $this->no.'_2.jpg'){
			$this->photo_back->name = $this->no.'_2.jpg';
			$this->photo_back->save();
		}
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return true;
	}

	public function matchCnee($v=false){
		if($this->bwf > 0 || $this->status > 97) return false;
		if($v){
			$smt = microtime();
			echo 'Start matching ID: '.$this->no."\n";
		}

		$rs = [];

		if(!empty($this->mdata['connote'])){ //has connote no
			$p = ExParcel::model()->find('status < 18 AND hbn = :hbn', array(':hbn' => $this->mdata['connote']));
			if($p && $p->cnee->cnid_id == 0) $rs[] = $p->cnee;
		}

		//if(empty($rs)){
			$c = self::model()->count('name = :n AND status IN (15,18,20) AND bwf = 0', array(':n' => $this->name));
			$p = ExParcel::model()->find(['condition' => 'status IN (12,15) AND created > DATE_SUB(NOW(), INTERVAL 60 DAY)', 'order' => 't.id ASC']);
			if(empty($p)) return;
			$min_id = $p->cnee_id;
			if($c == 1){//name is unique
				$rs = Addr::model()->findAll(array(
				'condition' => "t.id > :mid AND name = :n AND cnid_id = 0 AND country != :au",
				'params' => array(':n' => $this->name, ':au' => 'Australia', ':mid' => $min_id),
				));
			}else{
				if(!empty($this->mobile)){//match by name and mobile;
					$rs = Addr::model()->findAll(array(
						'condition' => "t.id > :mid AND name = :n AND cnid_id = 0 AND tel = :m AND country != :au",
						'params' => array(':n' => $this->name, ':m' => $this->mobile, ':au' => 'Australia', ':mid' => $min_id),
						));
				}else{
					$c2 = Addr::model()->findAll(array(
							'condition' => "t.id > :mid AND name = :n AND cnid_id = 0 AND country != :au",
							'params' => array(':n' => $this->name, ':au' => 'Australia', ':mid' => $min_id),
							'group' => 'tel',
							));
					if(sizeof($c2) == 1){//only 1 waiting
						$rs = $c2;
					}
				}
			}

			if(empty($rs) && !empty($this->mobile) && CnID::model()->count('mobile = :t AND status IN (15,18,20) AND bwf = 0', [':t' => $this->mobile]) == 1){ //try match by mobile
				$rs = Addr::model()->findAll(array(
						'condition' => "t.id > :mid AND cnid_id = 0 AND tel = :m AND country != :au",
						'params' => array(':m' => $this->mobile, ':au' => 'Australia', ':mid' => $min_id),
					));
			}
		//}

		if($v) echo sizeof($rs).' match found ('.(microtime() - $smt).")\n";
		
		foreach($rs as $r){
			$r->cnid_id = $this->id;
			$nc = false;
			if($r->name != $this->name){
				$oname = $r->name;
				//no change if name completely different
				if(AppHelper::similar_text_cn($oname, $this->name) < 1) continue;
				$r->name = $this->name;
				$nc = true;
			}
			$r->auto_match = false;
			$r->save();
			$p = ExParcel::model()->find('cnee_id = :cid', array(':cid' => $r->id));
			if($nc && $p){
				$p->bwf = $p->bwf | 128;
				$p->custom_log_note = 'Consignee Name changed: '.$oname.' > '.$r->name;
			}
			if($p) $p->save();
			if($v) echo $p->hbn." matched\n";
		}
	}

	public static function hasProblemUpload($n){
		return self::model()->count('name = :n AND status IN(14, 15) AND bwf > 0', array(':n' => $n)) > 0;
	}
	
	public function getStatus(){
		return empty(self::$states[$this->status])? '' : self::$states[$this->status];
	}

	public function no2district($tp=0){
		$d = substr($this->no, 0, 6);
		$ds = include(Yii::app()->basePath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'cn_districts.php');
		$c = floor($d / 100) * 100;
		$s = floor($d / 10000) * 10000;
		if(empty($ds[$d])) $d = floor($d / 10) * 10;
		if(empty($ds[$d])) $d = $c;
		if(empty($ds[$d])) return false;
		$t = substr($d, -2, 1);

		$r = '';
		if($tp == 1){
			if($t < 2){
				$r = preg_replace('/市$/', '', $ds[$c][0]).'市公安局'.$ds[$d][0].'分局';
			}elseif($t < 8){
				$r = preg_replace('/县$/', '', $ds[$d][0]).'县公安局';
			}else{
				$r = preg_replace('/市$/', '', $ds[$d][0]).'市公安局';
			}
		}else{
			$r = $ds[$s][0].(in_array($s, [110000, 120000, 310000, 500000])? '市' : '省');

			if($t < 2){
				if($ds[$c][0] == $r){
					$r .= preg_replace('/区$/', '', $ds[$d][0]).'区';
				}else{
					$r .= preg_replace('/市$/', '', $ds[$c][0]).'市'.preg_replace('/区$/', '', $ds[$d][0]).'区';
				}
			}elseif($t < 8){
				$r .= preg_replace('/县$/', '', $ds[$d][0]).'县';
			}else{
				$r .= preg_replace('/市$/', '', $ds[$d][0]).'市';
			}
		}
		return $r;
	}

	public function no2gender(){
		$g = substr($this->no,-2,1);
		return $g%2 == 0? '女' : '男';
	}

	public static function validCnIDNo($id){
		$id = strtoupper($id);
		if(!preg_match('/(^\d{15}$)|(^\d{17}([0-9]|X)$)/', $id)) return false;
		if(15==strlen($id)){ //检查15位
			if(preg_match('/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/', $id, $m))
				return strtotime("19".$m[2] . '-' . $m[3]. '-' .$m[4]) === false? false: true;
		}elseif(preg_match('/^[1-9]\d{5}(19|20)[0-9]{2}(0[1-9]|1[0|1|2])(0[1-9]|[12][\d]|3[01])\d{3}([\dXx])$/', $id, $m)){ //检查18位, 校验位按照ISO 7064:1983.MOD 11-2的规定生成, X代表数字10
			$arr_int = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
			$arr_ch = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
			$sign = 0;
			for( $i = 0; $i < 17; $i++ ){
				$b = (int) $id{$i};
				$w = $arr_int[$i];
				$sign += $b * $w;
			}
			return $arr_ch[$sign % 11] == substr($id, 17, 1);
		}
		return false;
	}

	public function genPhoto(){
		if(!self::validCnIDNo($this->no)) return;
		$c = new AliCloudAPI();
		//$r = json_decode(file_get_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'a.txt'));
		$r = $c->idCardPho($this->name, $this->no);
		if(empty($r) || empty($r->data)) return false;

		if($r->data->code == '1001'){
			file_put_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'data/invalid_id.bsv', $this->name.'|'.$this->no.PHP_EOL, FILE_APPEND);
			return false;
		}

		$img_path = dirname(Yii::app()->basePath).DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR;
		$fonts_path = dirname(Yii::app()->basePath).DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR;
		$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
		$id1 = $tmp.$this->no.'_1.jpg';
		$id2 = $tmp.$this->no.'_2.jpg';
		$idp = $tmp.$this->no.'_p.jpg';

		file_put_contents($idp, base64_decode($r->data->idCardPhoto));

		$im = @imagecreatefromjpeg($img_path.'cnid_s1.jpg');
		$black = imagecolorallocate($im, 0, 0, 0);

		$font_d = $fonts_path.'ocrb.ttf';
		$font_c = $fonts_path.'msyh.ttc';
		$magick = '/usr/bin/convert';
		//$magick = 'magick';

		imagettftext($im, 16, 0, 135, 105, $black, $font_c, $this->name);
		imagettftext($im, 14, 0, 135, 146, $black, $font_c, $this->no2gender());
		imagettftext($im, 14, 0, 135, 186, $black, $font_c, substr($this->no, 6,4));
		imagettftext($im, 14, 0, 216, 186, $black, $font_c, substr($this->no, 10,2));
		imagettftext($im, 14, 0, 272, 186, $black, $font_c, substr($this->no, 12,2));
		$addr = $this->no2district();
		$ads = AppHelper::utf8Split($addr, 11);
		foreach($ads as $c => $a){
			imagettftext($im, 14, 0, 135, 225 + ($c*22), $black, $font_c, $a);
			$lc = mb_strlen($addr);
		}

		imagettftext($im, 17, 0, 215, 335, $black, $font_d, $this->no);
		imagejpeg($im, $id1);

		//add mosaic address
		AppHelper::exec($magick.' '.$id1.' \( '.$img_path.'mosaic.png -crop '.(209-$lc*19).'x20 \) -geometry +'.(136 + $lc*19).'+'.(210 + $c*22).' -composite '.$id1);
		$c++;
		AppHelper::exec($magick.' '.$id1.' \( '.$img_path.'mosaic.png -crop '.(19 * rand(7,11)).'x20+'.(209-rand($lc, $lc+3)*19).' \) -geometry +136+'.(210 + $c*22).' -composite '.$id1);
		
		AppHelper::exec($magick.' '.$id1.' \( '.$idp.' -resize "150x190!" \) -compose Darken_Intensity -geometry +382+85 -composite '.$id1);
		AppHelper::exec($magick.' '.$id1.' '.$img_path.'qgzy.png -geometry +165+230 -composite '.$id1);

		$im = @imagecreatefromjpeg($img_path.'cnid_s2.jpg');
		$black = imagecolorallocate($im, 0, 0, 0);
		imagettftext($im, 14, 0, 248, 296, $black, $font_c, $this->no2district(1));
		imagejpeg($im, $id2);
		AppHelper::exec($magick.' '.$id2.' '.$img_path.'qgzy.png -geometry +165+230 -composite '.$id2);

		$this->save();

		$this->front = FileRepo::storeFile($id1, $this->no.'_1.jpg', 60, $this->id);
		$this->back = FileRepo::storeFile($id2, $this->no.'_2.jpg', 60, $this->id);
		
		$this->nolog = true;
		$this->save();
		$this->refresh();
		if($this->front > 0 && $this->back > 0){
			$this->nolog = false;
			$this->joinPhoto();
			$this->status = 15;
			$this->save();
			$this->matchCnee();
		}

		unlink($id1);
		unlink($id2);
		unlink($idp);

		return true;
	}

	public function joinPhoto(){
		if(empty($this->photo_front) || empty($this->photo_back)) return 0;
		$f1 = $this->photo_front->getFile();
		$f2 = $this->photo_back->getFile();

		//width 600;
		$w = 600;
		$im1 = AppHelper::resizeImg($f1, $w);
		$im2 = AppHelper::resizeImg($f2, $w);
		if(!$im1 || !$im2){
			$this->bwf = $this->bwf | 2;
			return false;
		}
		$h = imagesy($im1) + imagesy($im2);
		
		//join photo
		$jim = imagecreatetruecolor($w, $h);
		imagecopy($jim, $im1, 0, 0, 0, 0, $w, imagesy($im1));
		imagecopy($jim, $im2, 0, imagesy($im1), 0, 0, $w, imagesy($im2));
		$tfn = tempnam(Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime", 'IDJ');
		imagejpeg($jim, $tfn);
		$fid = FileRepo::storeFile($tfn, $this->no.'-joint.jpg', 60, $this->id);
		unlink($tfn);
		$this->joint = $fid;
		return $fid;
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search(){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id,true);
		if(empty($this->status)) $this->status = '<98';
		$criteria->compare('status',$this->status,true);
		$criteria->compare('no',$this->no,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('front',$this->front,true);
		$criteria->compare('back',$this->back,true);
		$criteria->compare('joint',$this->joint,true);

		if(!empty($this->bwf)){
			$criteria->addCondition('bwf & ' . $this->bwf . ' > 0');
		}

		if(!empty($this->mnos)){
			$ns = preg_split('/[\s,;]+/', trim($this->mnos));
			if(sizeof($ns) > 200) $ns = array_slice($ns, 0, 200);
			$criteria->addInCondition("no", $ns);
		}

		if(!empty($this->mnames)){
			$ns = preg_split('/[\s,;]+/', trim($this->mnames));
			if(sizeof($ns) > 200) $ns = array_slice($ns, 0, 200);
			$criteria->addInCondition("name", $ns);
		}

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
    			'defaultOrder'=>'bwf DESC, t.id DESC',
  			),
			'pagination'=>array(
				'pageSize'=>'30',
            ),
		));
	}

	public function getWarnings($s=true){
		return AppHelper::bwf2warning($this, $s);
	}

	public static function youtuOCR($side = 0, $f){
		$data = json_encode([
			'app_id' => '10094712',
			'image' => base64_encode(file_get_contents($f)),
			'seq' => '',
			'card_type' => $side,
		]);

		$c = new curl('https://api.youtu.qq.com/youtu/ocrapi/idcardocr');
		//$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_SSL_VERIFYHOST, false);
		$c->setopt(CURLOPT_TIMEOUT, 60);
		$c->setopt(CURLOPT_POST, true);
		$c->setopt(CURLOPT_POSTFIELDS, $data);
		$c->setopt(CURLOPT_HTTPHEADER, array('Authorization:'.self::youtuAuth(), 'Content-Type:text/json', 'Content-Length:' . strlen($data)));

		if(!$c->exec()) return false;
		return json_decode($c->result);
	}

	public static function youtuAuth(){
		$qqID = '3162692881';
		$AppID = '10094712';
		$SecretID = 'AKIDeFj6LS9G0c6Wo2eg2FKG4taFmAf6Odie';
		$SecretKey = 'viWDmCWjolgUDoJgu7C8xdBfK0eNyRp4';
		$s = 'u='.$qqID.'&a='.$AppID.'&k='.$SecretID.'&e='.(time()+36000).'&t='.(time()-36000).'&r='.rand(10000000,99999999).'&f=';
		return base64_encode(hash_hmac('sha1', $s, $SecretKey, true).$s);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CnID the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
