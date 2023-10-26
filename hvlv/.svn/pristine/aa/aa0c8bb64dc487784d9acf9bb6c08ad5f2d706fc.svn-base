<?php

class ClaimController extends PController{
    
    public function actionIndex(){
           $crm= Crm::model()->find('no=:no',array(':no'=>$_GET['no']));
           if(empty($_GET['t'])||!in_array($_GET['t'], Crm::$crm_form_types)){
                  throw new CHttpException(400,"Method not found!");
           }
           if(!empty($crm)){
                 $hash=isset($_GET['h'])?$_GET['h']:'';
              if(HashVerify::verify($hash, $crm->id, get_class($crm))){
                   $this->render('claim',array('model'=>$crm));  
              }else{
                   throw new CHttpException(400,"Not Valid!");
              }
               
             }else{
                   throw new CHttpException(400,"Ticket Not found!");
             }
     }
     public function actionClaimForm($id){
         $model=Crm::model()->findByPk($id);
         if(!empty($model->crm_comp)){
             $model->crm_comp->mdata['claim_signature']=$_POST['claim_signature'];
             $model->crm_comp->mdata['ip']= $this->getRealIpAddr();
             $model->crm_comp->mdata['client_name']=$_POST['client_name'];
             $model->crm_comp->mdata['client_telphone']=$_POST['client_telphone'];
             $model->crm_comp->mdata['client_email']=$_POST['client_email'];
             $model->crm_comp->bank_name=$_POST['bank_name'];
             $model->crm_comp->loss_fee=$_POST['loss_fee'];
             $model->crm_comp->freight_fee=$_POST['freight_fee'];
             $model->crm_comp->claim_amount=$_POST['claim_amount'];
             $model->crm_comp->bsb=$_POST['bank_bsb'];
             $model->crm_comp->account_name=$_POST['account_name'];
             $model->crm_comp->account_number=$_POST['account_number'];
             $model->crm_comp->mdata['client_note']=$_POST['claim_note'];
             $model->crm_comp->mdata['payment_method']=$_POST['payment_method'];
             $model->crm_comp->flag= $model->crm_comp->flag|1;
             if($_POST['payment_method']==0){
                   $model->crm_comp->flag= $model->crm_comp->flag&(~4);
                   $model->crm_comp->flag= $model->crm_comp->flag|2;
             }else if($_POST['payment_method']==1){
                 $model->crm_comp->flag= $model->crm_comp->flag&(~2);
                 $model->crm_comp->flag= $model->crm_comp->flag|4;
             }
             // Log::log2file($model->no." ".json_encode($_POST), 'claim_handing', "claims");
             if($model->crm_comp->save()){
                 // Log::log2file($model->no."  sucessfully", 'claim_handing', "claims");
                 $model->addNotes("Customer submit a claim form",0,false);
              echo 'done';
             }else{
                  Log::log2file($model->no."  ".json_encode($model->crm_comp->getErrors()), 'claim_handing', "claims");
             }
             return false;
         }else{
             return false;
         }
     }
          function getRealIpAddr(){
             $ip='';
             if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
                      $ip = $_SERVER['HTTP_CLIENT_IP'];
                   } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    } else {
       
                    $ip = $_SERVER['REMOTE_ADDR'];
                     }
                  return $ip;
           }
      
}

