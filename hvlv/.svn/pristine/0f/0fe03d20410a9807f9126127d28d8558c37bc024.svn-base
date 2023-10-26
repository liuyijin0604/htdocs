<?php

class SiteController extends Controller
{
	public $layout = 'client';
	/**
	 * Declares class-based actions.
	 */
	protected $nonAjax=array('index','help','captcha','error','logout','insurance');
	
	public function filterAccessControl($filterChain){
		$filterChain->run();
	}
	
	public function actions(){
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xEBF0FA,
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex(){
		$this->render('index');
	}

	public function actionHelp(){
		$this->render('index');
	}

	public function actionInsurance(){
		$this->render('insurance');
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
		$this->redirect(Yii::app()->homeUrl);
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