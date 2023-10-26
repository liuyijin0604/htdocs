<?php

class SiteController extends Controller{
	/**
	 * Declares class-based actions.
	 */
	protected $nonAjax=array('index','captcha','error','logout', 'voice');
	
	public function filterAccessControl($filterChain){
		$filterChain->run();
	}
	
	public function actions(){
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'foreColor' => 0xAB2F22,
				'maxLength' => 4,
				'minLength' => 4,
				'width' => 120,
				'height' => 60,
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex(){
		if(!Yii::app()->request->isAjaxRequest) $this->layout = 'pos';
		if(isset($_GET['cn']) || !isset(Yii::app()->session['cn'])) Yii::app()->session['cn'] = isset($_GET['cn']);
		$this->render('index');
	}
	
	/**
	 * Authenticate
	 */
	public function actionAuth(){
		$auth = array();
		/*dev login*/
		if(in_array(Yii::app()->getRequest()->serverName, array('localhost','alpha'))){
			$ca = $this->createAction('captcha');
			$_POST['LoginForm'] = array(
				'user' => 'admin@system', //admin
				//'user' => 'forest@prioritycargo.com.au',
				//'user' => 'manager@yuehang.com', //client
				//'user' => 'export@system', //export
				'pwd' => 'password',
				'vvc' => $ca->getVerifyCode(),
			);
		}
		if(isset($_POST['LoginForm'])){
			$model=new LoginForm;
			$model->attributes=$_POST['LoginForm'];
			if($model->validate()) $model->login();
		}

		if(Yii::app()->user->isGuest){
			$auth['valid'] = false;
		}else{
			$auth['valid'] = true;
			$auth['name'] = Yii::app()->user->name;
			$auth['rpc'] = Yii::app()->user->rpc;
		}
		echo json_encode($auth);
	}
	
	/**
	 * Check Session
	 */
	public function actionCheckSession(){
		echo time() - Yii::app()->session['last_action'];
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout(){
		Yii::app()->user->logout();
		$this->redirect('../');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError(){
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest){
	    		echo $error['message'];
	    	}else{
				$this->render('error', $error);
			}
	    }
	}
}