<?php

class ShipmentController extends Controller{
    
    
        protected $org, $type;

	public function beforeAction($action){
		if(!Yii::app()->user->isGuest){
			$this->org = Org::model()->findByPk(Yii::app()->user->org);
		}

		return parent::beforeAction($action);
	}
    public function actionIndex(){
       
    }
    	public function actionList(){
		$model=new ImParcel('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ImParcel']))
			$model->attributes=$_GET['ImParcel'];
                if(Yii::app()->user->grp>10)
                $model->agent_id=Yii::app()->user->org;
            	$model->searchHeld = true;

		$this->render('shipment_list',array(
			'model'=>$model,'org'=>$this->org,
		));
	}
        
        public function actionView($id){
            $model=$this->loadModel($id);
            
            $this->render('view',array('model'=>$model,'org'=>$this->org));
        }
        /**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model= ImParcel::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
        
}

