<?php

class MessageController extends Controller{
	
	protected $skipAcl = array('index', 'load', 'create', 'status');

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionStatus($id){
		$model = $this->loadModel($id);
		if($model && !empty($_GET['status'])){
			$model->status = $_GET['status'];
			$model->save();
		}
		echo 'done';
	}
	
	public function actionLoad(){
		$rt = array();
		$rs = Message::model()->findAll('to_id = :id AND status < 2', array(':id' => Yii::app()->user->id));
		$shown = empty($_POST['ids'])? array() : $_POST['ids'];
		foreach($rs as $r){
			if(in_array($r->id, $shown)) continue;
			$o = new StdClass;
			$o->id = $r->id;
			$o->msg = $r->msg;
			$o->from = $r->getFrom();
			$o->type = $r->from_id > 0? 'msg' : 'notify';
			$rt[] = $o;
		}
		echo json_encode($rt);
	}

	public function actionNotice(){
		$user = User::getCurrentUser();
		if($user->active==0)
		{
			Yii::app()->user->logout();
			$this->redirect(Yii::app()->homeUrl);
		}
		$service = new Service();
		$importsMailData = $service->getCacheData('TodayImportsMail0');
		$wmsMailData = $service->getCacheData('TodayImportsMail1');
		$taskData = $service->getCacheData('TodayTaskData');
		$count = Message::model()->count('to_id = :id AND status = 0', array(':id' => Yii::app()->user->id));
		$importsEmailNumbers = (empty($importsMailData[User::currentUserID()])?0:$importsMailData[User::currentUserID()]['today_left']);
		$wmsEmailNumbers = (empty($wmsMailData[User::currentUserID()])?0:$wmsMailData[User::currentUserID()]['today_left']);
		$taskNumbers = (empty($taskData[User::currentUserID()])?0:$taskData[User::currentUserID()]['today_left']);
		echo $count.','.$importsEmailNumbers.','.$wmsEmailNumbers.','.$taskNumbers;
	}
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate(){
		$model=new Message;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Message'])){
			$model->attributes=$_POST['Message'];
			$model->from_id = Yii::app()->user->id;
			$model->status = 0;
			$model->save();
			$this->ajaxResult($model);
		}
	}

	/**
	 * Lists and search.
	 */
	public function actionIndex(){
		$model=new Message('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Message']))
			$model->attributes=$_GET['Message'];
			
		if(isset($_GET['tab'])){
			if($_GET['tab']=='inbox')
			{
				$filtersForm=new FiltersForm;
				if (isset($_GET['FiltersForm'])) {
					$filtersForm->filters=$_GET['FiltersForm'];
				}

				$provide=TlaTaskService::getUserMessageProvide();
				$filterData=$filtersForm->filter($provide);
				$dataProvider=new CArrayDataProvider($filterData);
				$dataProvider->pagination=['pageSize'=>10];
				$sort=new CSort();
				$sort->defaultOrder = "pod";
				$dataProvider->sort=$sort;
				$this->render('tab_'.$_GET['tab'], array('model'=>$model,'dataProvider'=>[$dataProvider,$filtersForm]));
				return;



			}
			$this->render('tab_'.$_GET['tab'], array('model'=>$model));
		}else{
			$this->render('index',array('model'=>$model));
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=Message::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='message-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
