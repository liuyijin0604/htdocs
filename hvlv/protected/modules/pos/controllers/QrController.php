<?php
class QrController extends Controller{
	public $layout = 'client';
	protected $skipAcl = ['cns','edo','dev'];

	public function init(){
		Yii::app()->language = 'zh_cn';
	}

	public function filters() {
		return array();
	}

	public function actionCns($hash){
		$org = Org::model()->find('`status` = 1 AND `type` IN (60,65) AND `hash` = :h', [':h' => $hash]);
		if(empty($org)) throw new CHttpException(404,'The requested page does not exist.');

		if(!empty($_POST)){
			$captcha=Yii::app()->createController('pos/site')[0]->createAction("captcha");
			$code = $captcha->verifyCode;
			$model = new ExParcel;
			if($code != strtolower($_POST['vvc'])) $model->addError('id', '验证码错误');
			if(empty($model->cnor)) $model->cnor = new Addr;
			if(empty($model->cnee)) $model->cnee = new Addr;
			$model->cnor->attributes = $_POST['Cnor'];
			$model->cnee->attributes = $_POST['Cnee'];
			$model->attributes=$_POST['ExParcel'];
			$model->agent_id = $org->id;
			$model->hbn = $model->genHbn();
			if(empty($model->cnor_id)) $model->cnor_id = $model->cnor->id;
			if(empty($model->cnee_id)) $model->cnee_id = $model->cnee->id;
			$model->eitems = $_POST['items'];
			$model->status = 8;
			$model->state = $model->cnee->state;
			$model->postcode = $model->cnee->postcode;

			//cnor
			foreach(['name', 'tel'] as $a){
				if(empty($model->cnor->{$a})){
					$model->addError('cnor_id', '请填写发件人'.$this->t(ucfirst($a)));
				}
			}

			//cnee
			foreach(['name', 'tel', 'state', 'city', 'address', 'postcode'] as $a){
				if(empty($model->cnee->{$a})){
					$model->addError('cnee_id', '请填写收件人'.$this->t(ucfirst($a)));
				}
			}

			if(empty($model->weight)){
				$model->addError('weight', '请填写收包裹重量');
			}elseif($model->weight < 1){
				$model->weight = 1;
			}

			$model->validItems();
			
			if($model->hasErrors()){
				$this->ajaxResult($model, ['id', 'hbn']);
			}else{
				$model->cnor->save();
				$model->cnee->save();
				$model->cnor_id = $model->cnor->id;
				$model->cnee_id = $model->cnee->id;
				$model->mdata['client_entry'] = $_POST;
				$model->save();
				$this->ajaxResult($model, ['id', 'hbn'], 'Shipment created successfully.');
			}
		}

		Yii::app()->session['hash'] =  $hash;
		$this->render('cns', ['model' => new ExParcel, 'org' => $org]);
	}

	public function actionEdo($hash){
		$org = Org::model()->find('`status` = 1 AND `type` IN (60,65) AND `hash` = :h', [':h' => $hash]);
		if(empty($org)) throw new CHttpException(404,'The requested page does not exist.');

		if(!empty($_POST)){
			$captcha=Yii::app()->createController('pos/site')[0]->createAction("captcha");
			$code = $captcha->verifyCode;
			$model = new ExDirect;
			if($code != strtolower($_POST['vvc'])) $model->addError('id', '验证码错误');
			if(empty($model->cnor)) $model->cnor = new Addr;
			if(empty($model->cnee)) $model->cnee = new Addr;
			$model->cnor->attributes = $_POST['Cnor'];
			$model->cnee->attributes = $_POST['Cnee'];
			$model->attributes=$_POST['ExDirect'];
			$model->agent_id = $org->id;
			$model->hbn = $model->genHbn();
			if(empty($model->cnor_id)) $model->cnor_id = $model->cnor->id;
			if(empty($model->cnee_id)) $model->cnee_id = $model->cnee->id;
			$model->eitems = $_POST['items'];
			$model->status = 10;
			$model->state = $model->cnee->state;
			$model->postcode = $model->cnee->postcode;

			//cnor
			foreach(['name', 'tel'] as $a){
				if(empty($model->cnor->{$a})){
					$model->addError('cnor_id', '请填写发件人'.$this->t(ucfirst($a)));
				}
			}

			//cnee
			foreach(['name', 'tel', 'state', 'city', 'address', 'postcode'] as $a){
				if(empty($model->cnee->{$a})){
					$model->addError('cnee_id', '请填写收件人'.$this->t(ucfirst($a)));
				}
			}

			$model->validItems();
			
			if($model->hasErrors()){
				$this->ajaxResult($model, ['id', 'hbn']);
			}else{
				$model->cnor->save();
				$model->cnee->save();
				$model->cnor_id = $model->cnor->id;
				$model->cnee_id = $model->cnee->id;
				$model->mdata['client_entry'] = $_POST;
				$model->save();
				$this->ajaxResult($model, ['id', 'hbn'], 'Order created successfully.');
			}
		}

		Yii::app()->session['hash'] =  $hash;
		$this->render('edo', ['model' => new ExDirect, 'org' => $org]);
	}

	public function actionDev($hash){
		$org = Org::model()->find('`status` = 1 AND `type` IN (60,65) AND `hash` = :h', [':h' => $hash]);
		if(empty($org)) throw new CHttpException(404,'The requested page does not exist.');

		if(!empty($_POST)){
			$model = new ExParcel;
			$this->ajaxResult($model);
		}
		$this->render('dev', ['model' => new ExParcel, 'org' => $org]);
	}

	public function registerJS($js,$id=1){
		Yii::app()->clientScript->registerScript($this->getId().$id, preg_replace('/<script[^>]+>(.+)<\/script>/ms', '\\1',$js));
	}

	public function actionShow(){
		Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
		$model = User::model()->findByPk(Yii::app()->user->id);
		$qr = new TCPDF2DBarcode('http://pos.pca168.com/qr/cns/'.$model->org->hash, 'QRCODE,H');
		header('Content-Type: image/svg+xml');
		$qr->getBarcodeSVG(8, 8, 'black');
	}
}