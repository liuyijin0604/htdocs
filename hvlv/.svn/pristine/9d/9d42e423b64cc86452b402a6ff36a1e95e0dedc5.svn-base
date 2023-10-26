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
		if(!Yii::app()->request->isAjaxRequest) $this->layout = 'dplatform';
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
			$ca = $this->createAction('captcha');
			$model=new LoginForm;
			$_POST['LoginForm']['vvc'] = $ca->getVerifyCode();
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
        public function actionChooseWarehouse(){ 
            Yii::app()->session['scan_warehouse']=$_GET['scan_warehouse'];
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

	public function actionGpsTracking()
	{
		if (!empty(User::getCurrentUser())) {
			$driverId = User::getCurrentUser()->org_id;
			if (!empty($driverId) && !empty($_POST['longitude']) && !empty($_POST['latitude']) && !empty($_POST['tracking_type'])) {
				/**
				 * Update real-time driver tracking
				 */
				if ($_POST['tracking_type'] == DriverTracking::TRACKING_REALTIME) {
					$attributes = ['driver_id' => $driverId, 'type' => $_POST['tracking_type']];
					$model = DriverTracking::model()->findByAttributes($attributes);
					if (!empty($model)) {
						// Tracking record already exist, update it
						$model->tracking_time = date('Y-m-d H:i:s');
						$model->longitude = $_POST['longitude'];
						$model->latitude = $_POST['latitude'];
						$model->save();
					} else {
						// Tracking record doesn't exist, create a new record
						$model = new DriverTracking();
						$model->driver_id = User::getCurrentUser()->org_id;
						$model->tracking_time = date('Y-m-d H:i:s');
						$model->longitude = $_POST['longitude'];
						$model->latitude = $_POST['latitude'];
						$model->type = DriverTracking::TRACKING_REALTIME;
						$model->save();
					}
				} elseif ($_POST['tracking_type'] == DriverTracking::TRACKING_DELIVERED || $_POST['tracking_type'] == DriverTracking::TRACKING_DELIVERY_ERROR) {
					/**
					 * For delivered and delivery with issues, keep a record of the GPS
					 */
					if (!empty($_POST['cargo_process_id'])) {
						$model = new DriverTracking();
						$model->driver_id = User::getCurrentUser()->org_id;
						$model->tracking_time = date('Y-m-d H:i:s');
						$model->longitude = $_POST['longitude'];
						$model->latitude = $_POST['latitude'];
						$model->type = $_POST['tracking_type'];
						$model->cargo_process_id = $_POST['cargo_process_id'];
						$model->save();
					}
				}
			}
		}
	}
}