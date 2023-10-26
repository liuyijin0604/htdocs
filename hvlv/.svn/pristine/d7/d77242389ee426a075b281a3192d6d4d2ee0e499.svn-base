<?php
class SeaProcessController extends Controller{
    
    public function actionList(){  
        $filtersForm=new FiltersForm;
        $provide=[];
        if(isset($_GET['FiltersForm'])) $filtersForm->filters=$_GET['FiltersForm'];
        $sql="SELECT COUNT(*) as number, status FROM `sea_process` WHERE status>0 AND status<100 GROUP BY status";
        $rs=Yii::app()->db->createCommand($sql)->queryAll();
        $index=1;
        foreach ($rs as $r){
            $provide[]=['id'=>$index++,'number'=>$r['number'],'status'=>$r['status']];
        }
        $filteredData=$filtersForm->filter($provide);
        $dataprovider=new CArrayDataProvider($filteredData); 
        $dataprovider->pagination=array('pageSize' =>10);

        $model=new ImParcel('search');
        $model->sea_process_not_in=[100,0];
        if (isset($_GET['ImParcel'])) {
            $model->setAttributes($_GET['ImParcel']);
        }
         if (isset($_GET['status'])) {
             $name='All';
              if(!empty($_GET['status'])){
                 $model->setAttribute('sea_process_status', $_GET['status']);
                 $name=@SeaProcess::$states[$_GET['status']];
               }
                $this->renderPartial('_sub_shipments', array(
                        'model' => $model,
                        'name' => $name,
                ));
         }else{
              $this->render('list',array('model'=>$model,'dataProvider'=>[$dataprovider,$filtersForm],'name'=>'All'));  
         }
    }
    
    /**
     * the operation tab for sea process
     * @id Integer the id of Imparcel
     */
    
    public function actionOperation($id){
        $model=$this->loadModel($id);
        
        $this->render('operation_tab',array('model'=>$model->sea_process));
    }
    
    public function actionUpdateProcess($id){
        $model= $this->loadSeaProcessModel($id);
        if(!empty($_GET['status'])){
           $model->updateStatus($_GET['status']);
        }
        echo 'done';
    }
    
    /**
     * Send pre alert and notice arrival document via email to customer.
     * @param type $id the sea process id
     */
     public function actionSendPreAlertAndNotice($id){
       $seaProcess= $this->loadSeaProcessModel($id);
       $result=$seaProcess->sendPreAlertAndNoticeArrival();
       if($result['status']){
           echo 'done';
       }else{
           echo $result['msg'];
       }
     }
     public function actionSendSeaManifest($id){
          $seaProcess= $this->loadSeaProcessModel($id);
          $result=$seaProcess->sendRequestManifest();
          if($result['status']){
              echo 'done';
          }else{
             echo $result['msg'];
          }
     }
     public function actionSendSeaDo($id){
          $seaProcess= $this->loadSeaProcessModel($id);
          $result=$seaProcess->sendDo();
          if($result['status']){
              echo 'done';
          }else{
             echo $result['msg'];
          }
     }
     public function actionSendSeaOutturn($id){
          $seaProcess= $this->loadSeaProcessModel($id);
          $result=$seaProcess->sendOutturn();
          if($result['status']){
              echo 'done';
          }else{
             echo $result['msg'];
          }
     }
     public function actionLog($id){
            $shipment = $this->loadModel($id);
            $model=$shipment->sea_process;
            if($model==null) 
                throw new CHttpException(404,'The requested page does not exist.');
                $this->render('log',array(
			'model'=>$model,
		));
            
      }
      public function actionNotes($id){
            $model = $this->loadSeaProcessModel($id);
            Log::add($model,Log::LOG_TYPE_NOTES,['notes' => $_POST['notes']]);
            $this->ajaxResult($model);
     }
     
     public function actionListSpecifyProcess(){
          $model=new ImParcel('search');
          $model->unsetAttributes();
          if(!empty($_GET['ImParcel'])){
              $model->setAttributes($_GET['ImParcel']);
          }
          $model->sea_process_status=$_GET['status'];
          $this->render('_sub_shipments',array('model'=>$model,'name'=> SeaProcess::$states[$_GET['status']]));   
     }


     public function loadSeaProcessModel($id){
        $model= SeaProcess::model()->findByPk($id);
        if($model===null)
                throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }
    public function loadModel($id){
        $model=ImParcel::model()->findByPk($id);
        if($model===null)
                throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }
    
}