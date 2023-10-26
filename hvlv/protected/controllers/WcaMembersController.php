<?php

class WcaMembersController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new WcaMembers;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['WcaMembers']))
		{
			$model->attributes=$_POST['WcaMembers'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}
        
        
         /**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
        public function actionWca(){
            $model=new WcaMembers();
            $model->unsetAttributes();
//            if(isset($_GET['WcaMembers'])){
//                $model->setAttributes($_GET['WcaMembers']);
//            }
           $this->render('wca_admin',array('model'=>$model));
        }
        
        public function actionImportWCA(){
             $resp=array('done'=>1,'msg'=>'import successfully');
            if(isset($_FILES['wca_members'])){
              $file=empty($_FILES['wca_members'])?array():$_FILES['wca_members'];
              if(empty($file['tmp_name'])) {
                  $resp['msg']='no file found!';
                  echo json_encode($resp); return;
              }
              $xls= new oExcel;
              $xls->supported($file['name']);
              $xls->load($file['tmp_name']);
              $data=$xls->getAll();
             if(!preg_match("/".implode(';', $data[1])."/i",'name;email;tel')){
                 $resp['msg']='Template is Wrong';
                 echo json_encode($resp); return;
             }
             unset($data[1]);// remove the title;
             foreach($data as $d){
                 if(empty($d[2]))   continue;  // the email field can not be empty;
                 $wca=WcaMembers::model()->find('email=:email',array(':email'=>trim($d[2])));
                 if(empty($wca)){
                     $wca=new WcaMembers();
                 }
                 $wca->name=$d[1];
                 $wca->email=trim($d[2]);
                 $wca->phone=$d[3];
                 $wca->dt=date('Y-m-d H:i:s');
                 $wca->sent=0;
                 if(!$wca->save())  var_dump($wca->getErrors());
               }
               echo json_encode($resp);
               return;
              }
            
          $this->render('wca_import');  
        }
        
        
        
        public function actionCreateWCA(){
            $model=new Emailog();
            $model->type=$_GET['type'];
            $model->fid=empty($_GET['fid'])?11:$_GET['fid'];  
            if(isset($_POST['Emailog'])){
                $model->attributes=$_POST['Emailog'];
                foreach($_POST['extra'] as $k=>$v){
                    $model->mdata[$k]=$v;
                }
                $model->status=10;
                $model->save();
                if(!empty(Yii::app()->session['uploads'][$_POST['ppupload']][2])){
		       foreach(Yii::app()->session['uploads'][$_POST['ppupload']][2] as $fid){
			         $fr = FileRepo::model()->findByPk($fid);
			         $fr->fid = $model->id;
			         $fr->save();
				}
			}
                if(empty($model->scheduled)) $model->wcaSend ();
                $this->ajaxResult($model,array('id'),'Email Sent Successfully');
           }
           $model->prepTemplate();
           $model->subject=$model->tpl->subject;
           $model->body=$model->tpl->getContent();
           $this->render('_form_wca',array('model'=>$model));
        }
        
        
        /*
         * 
         */
       public function actionPretpl(){
           $model= EmailTpl::model()->find('`slug`="client"');
           if(empty($model)){
               $model=new EmailTpl();
           }
           if(isset($_POST['EmailTpl'])){
               $model->setAttributes($_POST['EmailTpl']);
               $model->save();
               $this->ajaxResult($model);
               
           }
           $this->render('pre_tpl',array('model'=>$model));
       }
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['WcaMembers']))
		{
			$model->attributes=$_POST['WcaMembers'];
			$model->save();
                        $this->ajaxResult($model);
                }

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('WcaMembers');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new WcaMembers('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['WcaMembers']))
			$model->attributes=$_GET['WcaMembers'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return WcaMembers the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=WcaMembers::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param WcaMembers $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='wca-members-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
