<?php

class SalesfunnelCustomerLoginController extends Controller
{
	 /**
	 * Declares class-based actions.
	 */
    protected $nonAjax = array('customerLogin','showLoginPage','captcha');
    protected $skipAcl = array('customerLogin','showLoginPage','captcha');
    protected $skipLogin = array('customerLogin','showLoginPage','captcha');
    
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
            ),
        );
    }

    public function readRelatedEmail(){
        $submit_id = $_GET['submit_id'];
        $submissionModel = SalesfunnelRequirementsSubmission::model()->findbypk($submit_id);        
        $model = new ImportsMail('search');
        $model->unsetAttributes();
        $ids = [-1];
        // echo $submissionModel->email;
        // Yii::app()->end();

        $relatedEmails = ImportsMail::model()->findAll(" from_email = :from_email OR to_email = :to_email",[":from_email"=>$submissionModel->email, ":to_email"=>$submissionModel->email]);
        if (!empty($relatedEmails)&&isset($relatedEmails)){
            $ids = array_column($relatedEmails, "id");
        }
        // echo json_encode($relatedEmails);
        // Yii::app()->end();

        $model->emailIds = $ids;
        $obj = ['model'=>$model,'submissionModel'=>$submissionModel];
        return $obj;
    }

    public function actionCheckEmails(){
        $obj = $this->readRelatedEmail();
        //$_GET['model'] = $obj['model'];
        if($obj['model']->emailIds != [-1]){
            echo '{"done":true,"submit_id":'.$obj['submissionModel']->id.'}';
            return ;
        }
        else{
            echo '{"done":false,"msg":"no more email yet."}';
            return ;
        }  
    }

    public function actionShowCommunicationEmail(){
        $obj = $this->readRelatedEmail();
        //$_GET['model'] = $obj['model'];
        if($obj['model']->emailIds != [-1]){
            // print_r(json_encode($_GET['model']->emailIds));
            // Yii::app()->end();
            $_GET['tabid'] = 1;
            $obj = $this->readRelatedEmail();
            //$this->redirect('customerIndex');
            $this->render('emails',['model'=>$obj['model']]);
            // echo '{"done":true,"submit_id":'.$obj['submissionModel']->id.'}';
            return ;
        }
        else{
            echo '{"done":false,"msg":"no more email yet."}';
            return ;
        }    
        //$this->renderPartial("//customerservice/emails",["model"=>$model]);
    }


    public function actionCustomerLogin(){
        // $auth = array();
        /*dev login*/
        if(in_array(Yii::app()->getRequest()->serverName, array('localhost','alpha'))){
            $ca = $this->createAction('captcha');
        }        
        // echo json_encode($_POST);
        // Yii::app()->end();
        if(isset($_POST['RequirementLoginForm'])){
            $model=new RequirementLoginForm;
            $model->attributes=$_POST['RequirementLoginForm'];
            if($model->validate()){
               if($model->login()){
                // print_r($_POST['submissionModel']->id);
                // Yii::app()->end();
                $this->renderPartial('sales_funnel_customer_index',['submisson_id'=>@Yii::app()->user->submission_id]);
                //$this->renderPartial('sales_funnel_customer_index',['model'=>$obj['model'],'submissionModel'=>$_POST['submissionModel']]);
               }
               else{
                 // echo '{"done":false,"msg":"Login detail is not correct."}';
                 // return ;
                $this->redirect('invalidLogin');
                return ;
               }
            }
            else{
                 // echo '{"done":false,"msg":"Login detail is not correct."}';
                 // return ;
                $this->redirect('invalidLogin');
                return ;
               }
            // echo json_encode($model->email);
            // Yii::app()->end();
        }

        if(Yii::app()->user->isGuest){
            $auth['valid'] = false;
        }else{
            $auth['valid'] = true;
            $auth['name'] = Yii::app()->user->name;
            $auth['rpc'] = Yii::app()->user->rpc;
            $this->renderPartial('sales_funnel_customer_index',['submisson_id'=>@Yii::app()->user->submission_id]);
        }
        // $this->renderPartial('sales_funnel_customer_login',['model'=>$model]);
        // $this->renderPartial('sales_funnel_customer_login');
    
    }

    public function actionInvalidLogin()
    {
        $this->renderPartial("sales_funnel_customer_login", ['invalid' => 1]);
      
    }

    public function actionCustomerIndex(){
        $this->renderPartial('sales_funnel_customer_index');
    }

    public function actionShowLoginPage(){
        $this->renderPartial('sales_funnel_customer_login');
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);
        $_GET['tabid'] = 1;
        $this->render("email_detail", ['model' => $model]);

      
    }

    public function loadModel($id)
    {
        $model = ImportsMail::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $model;
    }
}
