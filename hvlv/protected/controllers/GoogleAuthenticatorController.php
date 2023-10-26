<?php

class GoogleAuthenticatorController extends Controller
{
	 /**
	 * Declares class-based actions.
	 */
    protected $nonAjax = array('googleAuthenicatorCode','verifyCode');
    protected $skipAcl = array('googleAuthenicatorCode','verifyCode');
    protected $skipLogin = array('googleAuthenicatorCode','verifyCode');
    
    public function beforeAction($action){
        $this->layout="sales";
        return parent::beforeAction($action);
    }

    public function actions(){
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha'=>array(
                'class'=>'CCaptchaAction',
                'backColor'=>0xEBF0FA,
                'maxLength' => 4,
                'minLength' => 4,
                'livechatWebhook' => 'application.controllers.ApiLiveChatWebhookAction',
            ),
        );
    }

    public function actionGoogleAuthenicatorCode(){
        $this->renderPartial('google_authenticator_code');
    }

    public function actionVerifyCode(){
        $GoogleAuthenticator = new GoogleAuthenticator();
        $valid = $GoogleAuthenticator->verifyCode($_POST['secret'], $_POST['code']);
        
        if($valid){
            echo '{"done":true}';
            return ;
        }
        else{
            echo '{"done":false,"msg":"invalid code"}';
            return ;
        }
    }

}
