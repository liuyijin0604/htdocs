<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ApiCnIDAction extends CAction {
	public $ctlr, $debug, $user, $tmp;

	public function run() {
		$this->ctlr = $this->getController();
		$this->debug = !empty($_POST['test']);
		$this->user = empty($this->ctlr->user)? false : User::model()->findByPk($this->ctlr->user);
		$this->tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;

		//$this->log(json_encode($_POST));
		if(!empty($_POST['method']) && method_exists($this, $_POST['method'])){
			$this->{$_POST['method']}();
		}else{
			$this->upload();
		}
	}

	public function log($l){
		$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
		file_put_contents($tmp.'cnid_api.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
	}

	public function check(){
		$d = $this->ctlr->data;
		$cnid = CnID::model()->find('no = :no AND status IN (15, 18, 20)', array(':no' => $d->no));

		$o = new StdClass;
		if(empty($cnid)){
			$o->status = 0;
			$o->msg = 'Not uploaded';
		}else{
			$o->status = 1;
			$o->msg = 'Already Uploaded';
		}
		echo json_encode($o);
	}

	public function noIDList(){
		$rs = ExParcel::model()->findAll('agent_id = :aid AND status = 15', [':aid' => $this->user->org_id]);

		$o = [];
		foreach($rs as $r){
			if($r->cnee->cnid_id > 0 || !empty($r->cnee->cnid_no) || ($r->bwf & 32) > 0) continue;
			$o[] = ['no' => $r->hbn, 'cust_ref' => $r->cref, 'name' => $r->cnee->name, 'mobile' => $r->cnee->tel, 'upload_url' => 'http://au.pca168.com/upload-id.html?connote='.urlencode($r->hbn).'&name='.urlencode($r->cnee->name).'&mobile='.urlencode($r->cnee->tel)];
		}
		echo json_encode($o);
	}

	public function get(){
		$d = $this->ctlr->data;
		if(!empty($d->no)){
			$cnid = CnID::model()->find('no = :no AND status IN (15, 18, 20)', array(':no' => $d->no));
		}elseif(!empty($d->name) && !empty($d->mobile)){
			$cnid = CnID::model()->find('name = :name AND mobile = :m AND status IN (15, 18, 20)', array(':name' => $d->name, ':m' => $d->mobile));
		}elseif(!empty($d->name)){
			$cnid = CnID::model()->find('name = :name AND status IN (15, 18, 20)', array(':name' => $d->name));
		}

		$o = new StdClass;
		if(empty($cnid)){
			$o->status = 0;
			$o->msg = 'Not Available';
		}else{
			$o->status = 1;
			$o->no = $cnid->no;
			$o->name = $cnid->name;
			$o->tel = $cnid->mobile;
			if(isset($d->getPhoto)){
				$o->front = base64_encode(file_get_contents($cnid->photo_front->getFile()));
				$o->back = base64_encode(file_get_contents($cnid->photo_back->getFile()));
			}
		}
		echo json_encode($o);
	}

	public function query(){
		$d = $this->ctlr->data;

		$criteria=new CDbCriteria;
		$criteria->addInCondition("status", [15, 18, 20]);
		
		if(!empty($d->no)){
			$nos = json_decode(json_encode($d->no), true);
			$criteria->addInCondition("no", array_unique($nos));
		}
		if(!empty($d->name)){
			$names = json_decode(json_encode($d->name), true);
			$criteria->addInCondition("name", array_unique($names));
		}

		$rs = CnID::model()->findAll($criteria);

		$o = new StdClass;
		$o->data = [];
		if(empty($rs)){
			$o->status = 0;
			$o->msg = 'Not Available';
		}else{
			$o->status = 1;
			foreach($rs as $cnid){
				$id = [
					'no' => $cnid->no,
					'name' => $cnid->name,
					'tel' => $cnid->mobile,
					];
				if(isset($d->photo)){
					$id['front'] = base64_encode(file_get_contents($cnid->photo_front->getFile()));
					$id['back']  = base64_encode(file_get_contents($cnid->photo_back->getFile()));
				}
				if(isset($d->joint)){
					$id['joint'] = base64_encode(file_get_contents($cnid->photo_joint->getFile()));
				}
				$o->data[] = $id;
			}
		}
		echo json_encode($o);
	}

	public function upload(){
		$d = $this->ctlr->data;
		$err = [];
		if(CnID::validCnIDNo($d->no)){
			$cnid = CnID::model()->find('no = :no AND status IN (10, 15, 18, 20)', array(':no' => $d->no));
			if($cnid){
				if(!empty($d->connote)){
					$p = ExParcel::model()->find('hbn = :n', [':n' => $d->connote]);
					if(empty($p)){
						$err[] = 'Connote not found';
					}elseif($p->status < 18){
						if($p->cnee->name == $cnid->name){
							$p->cnee->cnid_id = $cnid->id;
							$p->cnee->save();
						}else{
							$err[] = 'ID name mismatch';
						}
					}else{
						$err[] = 'ID already matched';
					}
				}else{
					$err[] = 'ID '.$d->no.' already uploaded';
				}
			}else{
				$cnid = new CnID;
				$cnid->status = 14;
				$cnid->name = $d->name;
				$cnid->no = strtoupper($d->no);
				$cnid->mobile = empty($d->mobile)? '' : preg_replace('/[^\d]+/', '', $d->mobile);
				$cnid->mdata['connote'] = empty($d->connote)? '' : $d->connote;
				if(!$cnid->save()) $err[] = 'Saving error, '.print_r($cnid->getErrors(), true);
			
				//save images
				foreach(['front', 'back'] as $j=>$side){
					if(empty($d->{$side})){
						$err[] = 'Missing '.ucfirst($side).' image';
						continue;
					}
					$tf = tempnam($this->tmp, "idp");
					@file_put_contents($tf, preg_match('/^http/', $d->{$side})? file_get_contents($d->{$side}) : base64_decode($d->{$side}));
					if(filesize($tf) < 5120){
						unlink($tf);
						$err[] = ucfirst($side).' image read error';
						continue;
					}
					$img = AppHelper::resizeImg($tf, 600);
					if($img){
						@imagejpeg($img, $tf);
					}else{
						$err[] = ucfirst($side).' image read error';
						continue;
					}
					$cnid->{$side} = FileRepo::storeFile($tf, $cnid->no.'_'.($j+1).'.jpg', 60, $cnid->id);
					unlink($tf);
				}

				$cnid->nolog = true;
				$cnid->save();
				$cnid->refresh();

				if($cnid->front > 0 && $cnid->back > 0){
					$cnid->nolog = false;
					$cnid->joinPhoto();
					$cnid->status = 15;
					$cnid->save();
					$cnid->matchCnee();
				}
			}
		}else{
			$err[] = 'ID number incorrect';
		}

		$o = new StdClass;
		if(empty($err)){
			$o->status = 1;
			$o->msg = 'Success';
		}else{
			$o->status = 0;
			$o->msg = implode('; ', $err);
		}
		echo json_encode($o);
	}
}
