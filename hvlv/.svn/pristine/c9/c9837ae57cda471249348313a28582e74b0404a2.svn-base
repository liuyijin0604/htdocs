<?php

class AppController extends Controller{
	
	protected $skipAcl = array('menu', 'voice', 'feedback', 'live');
	
	public function actions(){
		return array(
			'help'=>array(
				'class'=>'CHelpAction',
			),
		);
	}
	
	/**
	 * Show User Menu
	 */
	public function actionMenu(){
		Yii::app()->name == 'PEP'? $this->renderPartial('menu_pep') : $this->renderPartial('menu');
	}

	public function buildSubMenu($sm){
		$l = '';
		if(empty($sm['items'])) return $l;

		foreach($sm['items'] as $m){
			if(!empty($m['items'])){
				$sl = $this->buildSubMenu($m);
				$l .= empty($sl)? '' : '<li><a href="javascript:void(0)">'.$this->t($m[0]).' &raquo;</a><ul class="ext_menu">'.$sl.'</ul></li>';
				continue;
			}
			if(!Acl::hasAccess('C:'.$m[0]) && empty($m[2])) continue;
			$m[1] = $this->t($m[1]);
			$l .= '<li><a href="'.$this->createUrl($m[0]).'" ' . (!empty($m[3]) ? 'target="_blank"' : 'class="menuLink"') . ' title="'.$m[1].'"' . (!empty($m[3]) ? ' target="_blank"' : '') . '>'.$m[1].'</a></li>';
		}
		return $l;
	}
	
	/**
	 * Keep Live
	 */
	public function actionLive(){
		
	}
	
	/**
	 * Feedback
	 */
	public function actionFeedback(){
		if(!empty($_POST['FeedbackForm'])){
			include_once('PHPMailer/class.phpmailer.php');
			$mail = new PHPMailer();
			$mail->From     = Yii::app()->user->email;
			$mail->FromName = Yii::app()->user->name;
			$mail->Subject  = 'APPSI '.$this->t('Feedback');
			$mail->Body = $_POST['FeedbackForm']['comment'];
			$mail->AddAddress(Yii::app()->params['adminEmail']);
			$mail->AddAddress(Yii::app()->params['supportEmail']);
			$mail->Send();
			$r = new stdClass;
			$r->done = true;
			$r->msg = $this->t('Send Successfully');
			echo json_encode($r);
			Yii::app()->end();
		}
		$model = new FeedbackForm;
		$this->render('feedback', array('model'=>$model));
	}
	
	/**
	 * App Settings
	 */
	public function actionSettings(){
		if(!empty($_POST['Settings'])){
			$f = Yii::app()->basePath.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'settings.php';
			$set = Yii::app()->params['settings'];
			foreach($set as $k=>$v){
				$set[$k]['value'] = $_POST['Settings'][$k]['value'];
			}
			Yii::app()->params['settings'] = $set;
			$r = var_export(Yii::app()->params['settings'], true);
			file_put_contents($f, "<?php\nreturn ".$r.';');
			$r = new stdClass;
			$r->done = true;
			$r->msg = 'Settings Saved!';
			echo json_encode($r);
			Yii::app()->end();
		}
		$this->render('settings');
	}
	
	public function actionTemplates(){
		$model=new FileRepo('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['FileRepo']))	$model->attributes=$_GET['FileRepo'];
		$model->type = 90;
		$model->status = 20;
		$this->render('templates',array(
			'model'=>$model,
		));
	}

	public function actionChatGPT(){
		if (!empty($_POST['question'])) {
			// $result = $_POST['result'];			
			// $result .= "Your Question: &#13;&#10;  ".$_POST['question']."&#13;&#10; &#13;&#10;";
			// echo $result;
			$respond = ChatgptAPI::generateEssayToolAPI($_POST['question'], false);
			echo $respond;
		}
		else{
			$this->render('chatGPT_panel');
		}		
	}

	public function actionBlackListCneeName()
	{
		$model = new BlacklistCneeName('search');
		$model->unsetAttributes();  // clear any default values

		if(isset($_GET['BlacklistCneeName']))
		{
			$model->attributes = $_GET['BlacklistCneeName'];
		}

		$this->render('cnee_name_blacklist', array(
			'model' => $model,
		));
	}

	public function actionUpdateBlackListCneeName($id=-1)
	{
		if ($id == -1) {
			$model = new BlacklistCneeName();
			$model->created = date('Y-m-d H:i:s');
			$model->name = User::getCurrentUser()->getFullName();
		}
		else{
			$model = BlacklistCneeName::model()->findByPk($id);
			$model->updated = date('Y-m-d H:i:s');
		}

		if(isset($_POST['BlacklistCneeName']))
		{
			$model->attributes = $_POST['BlacklistCneeName'];
			// if($model->save())
			// {
			// 	$this->redirect(array('view', 'id' => $model->id));
			// }
			$model->save();
			$this->ajaxResult($model);
		}

		$this->render('cnee_name_blacklist_create', array(
			'model' => $model,
		));
	}

	public function actionDeleteBlackListCneeName($id=0)
	{
		if (!empty($id)) {
			$model = BlacklistCneeName::model()->findByPk($id);
			$model->active = 0;
			$model->save();
			// $model->delete();
			// $this->ajaxResult($model);
			echo json_encode(['done'=>true,'msg'=>'Deleted']);
			return ;
		}
	}


}