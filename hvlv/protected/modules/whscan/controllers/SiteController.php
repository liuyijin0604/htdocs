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
		if(isset($_GET['classic'])) Yii::app()->session['classic'] = $_GET['classic'];
		if(!Yii::app()->request->isAjaxRequest) $this->layout = 'whscan';
		if(isset($_GET['cn']) || !isset(Yii::app()->session['cn'])) Yii::app()->session['cn'] = isset($_GET['cn']);
		$this->render('index');
	}
        
        
    public function actionIndex1(){
        $this->render('index');
    }
	
	/**
	 * Authenticate
	 */
	public function actionAuth(){
		$auth = array();
		/*dev login*/
		// if(in_array(Yii::app()->getRequest()->serverName, array('localhost','alpha'))){
		// 	$ca = $this->createAction('captcha');
		// 	$_POST['LoginForm'] = array(
		// 		'user' => 'gero@s1pcaex', //admin
		// 		//'user' => 'forest@prioritycargo.com.au',
		// 		//'user' => 'manager@yuehang.com', //client
		// 		//'user' => 'export@system', //export
		// 		'pwd' => 'password',
		// 		'vvc' => $ca->getVerifyCode(),
		// 	);
		// }

		// if(isset($_POST['LoginForm'])){
		// 	$model=new LoginForm;
		// 	$model->attributes=$_POST['LoginForm'];
		// 	if($model->validate()) $model->login();
		// }

		if(isset($_POST['LoginForm'])){
			$model=new LoginForm;	
	    	$ca = $this->createAction('captcha');	    	
			$_POST['LoginForm']['vvc'] = $ca->getVerifyCode();
			$model->attributes=$_POST['LoginForm'];
			if($model->login()){
				$user = User::getCurrentUser();
				Yii::app()->user->logout();	
				$userEmail = $user->email;
				$ga = new GoogleAuthenticator();
				if(isset($user->extra['googleAuthSecret'])&&!empty($user->extra['googleAuthSecret'])){
					$secret = $user->extra['googleAuthSecret'];									
				}
				else{					
					$secret = $ga->generateSecret();
					$user->extra['googleAuthSecret'] = $secret;
					$user->update('meta');					
				}
				if(isset($user->extra['googleAuthLogin'])&&!empty($user->extra['googleAuthLogin'])){
					$qrCodeUrl = 0;	
				}
				else{	
					$qrCodeUrl = $ga->getQRCodeGoogleUrl('Top-Logistics', $secret, $userEmail);	
				}	
				echo '{"done":true,"qrCodeUrl":"'.$qrCodeUrl.'"}';
            	return ;
			}
			else{
				Yii::app()->user->logout();
				echo '{"done":false,"msg":"invalid login"}';
				return ;
			}			
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

	public function actionGoogleAuthen(){
		$model=new LoginForm;	
    	$ca = $this->createAction('captcha');	    	
		$_POST['LoginForm']['vvc'] = $ca->getVerifyCode();
		$model->attributes=$_POST['LoginForm'];			
		if($model->login()){
			$user = User::getCurrentUser();
		}					
		else{
			Yii::app()->user->logout();
			echo '{"done":false,"msg":"invalid login"}';
			return ;

		}
		if(isset($_POST['LoginForm']['code'])){
			$GoogleAuthenticator = new GoogleAuthenticator();
		    $valid = $GoogleAuthenticator->verifyCode($user->extra['googleAuthSecret'], $_POST['LoginForm']['code']);
		    if($valid){
		    	if(empty($user->extra['googleAuthLogin'])){
					$user->extra['googleAuthLogin'] = 1;
					$user->update('meta');
				}
		    	echo '{"done":true}';
		    	return ;
		    	// $this->render("index");
		    }
		    else{
		    	Yii::app()->user->logout();
				echo '{"done":false,"msg":"invalid code"}';
				return ;
		    }
		}
	}

	public function actionGenerateCaptcha()
	 {
		  $username = $_POST['username'];
		  $user = User::model()->find('email=:email', array(':email' => $username));
		  if(!empty($user)){
		  	$phone = $user->phone;
		    $ca = $this->createAction('captcha');
		    $captcha = $ca->getVerifyCode();
		    $service = new Service();
		    $results=!empty($service->getCacheData('smscode'))?$service->getCacheData('smscode'):[];
		   	$results=array_merge($results,[[ModalRealTimeSmsNotice::id=>0,ModalRealTimeSmsNotice::phone=>$phone,ModalRealTimeSmsNotice::message=>$captcha]]);
		    $service->setCacheData('smscode',$results);
            $lastFourDigits = $phone;
            $service2 = new Service();
            $service2->setCacheData('username'.strtoupper($_POST['username']),$captcha);
            $user2 = $service2->getCacheData('username');
		    echo $lastFourDigits;
		  }else{
		  	echo json_encode(array('error' => 'User not found'));
		    return;
		  }
	 }

	 public function actionSendSms()
	 {
		  $username = $_POST['username'];
		  $user = User::model()->find('email=:email', array(':email' => $username));
		  if(!empty($user))
		  {
		  	$phone = $user->phone;
		    $ca = $this->createAction('captcha');
		    $captcha = $ca->getVerifyCode();
		    $sms = new Sms();
		    $sms->no = $phone;
		    $sms->msg = $captcha;
		    $result = $sms->sendByAPI();
            if ($result == "SUCCESS") {
            $lastFourDigits = $phone;
            echo $lastFourDigits;
	        } else {
	          echo json_encode(array('error' => 'Failed to send SMS'));
	        }
	      } else
	      {
	        echo json_encode(array('error' => 'User not found'));
	      }
    }

	/**
	 * Check Session
	 */
	public function actionCheckSession(){
		echo time() - Yii::app()->session['last_action'];
	}

    public function actionChooseWarehouse(){
    	if(!empty(User::getCurrentUser())&&User::getCurrentUser()->type!=User::AU_WAREHOUSE_OPERATOR&&(User::getCurrentUser()->dpt_id==Org::TLA_DEPARTMENT_SYDNEY||User::getCurrentUser()->dpt_id==Org::TLA_DEPARTMENT_SHENZHEN))
    	{
        	Yii::app()->session['scan_warehouse']=$_GET['scan_warehouse'];
    	}else
    	{
    		Yii::app()->session['scan_warehouse']= strtolower(Org::$warehouse_list_id_re[User::getCurrentUser()->dpt_id]);
    	}
        $this->render("index");
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

	private function setLoginTime()
	{
		$dataArr = [date('Y-m-d H:i:s')];
		Service::setCacheData("loginTime".date("Ymd").User::currentUserID(),$dataArr,3800);
		// print_r(Service::getCacheData("loginTime".date("Ymd").User::currentUserID()));		
	}
}