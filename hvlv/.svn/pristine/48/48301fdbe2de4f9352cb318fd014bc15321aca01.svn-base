<?php

class UploadCrmController extends PController{
	protected $debug = false;

	public function beforeAction($action){
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Cache-Control');
		header('Access-Control-Max-Age: 3600');
		return parent::beforeAction($action);
	}
        
        

        public function actionCheckConnote(){
           $o=array('status'=>0, $msg='' );
            if(isset($_POST['connote'])&&!empty($_POST['connote'])){
              $shipment=Shipment::model()->find('hbn=:r OR ref=:r',array(':r'=>trim($_POST['connote'])));
            }
            $type=$_POST['type'];
            if(in_array($type,array(20,30))){
                if(empty($shipment)){
                    $o['status']=0;
                    $o['msg']='单号没找到';
                    echo json_encode($o);return;
                }
                if($shipment->type==20){
                     $crm=new ExCrm();
                     //$crm->parcel_id=$shipment->id;
                     $crm->open_by=0;
                     $crm->telephone=$_POST['tel'];
                     $crm->email=$_POST['email'];
                     $crm->level=1;
                     if($type==20){
                          if(ExCrm::hasTickets($shipment->id, ExCrm::TYPE_PARCEL_QUERY, false)){
                             $o['status']=0;
                             $o['msg']=$shipment->hbn.' 包裹查询票已经在处理中，请不要重复建立。';
                             echo json_encode($o);return;
                         }
                         $crm->type=50;
                     }else if($type==30){
                         if(ExCrm::hasTickets($shipment->id, ExCrm::TYPE_COMPENSATION, true)){
                             $o['status']=0;
                             $o['msg']=$shipment->hbn.' 理赔票已经建立过';
                             echo json_encode($o);return;
                         }
                         $crm->type=30;
                        if(!empty($_POST['reason'])){
                            $crm->mdata['reason']=$_POST['reason'];
                        }
                     }
                     $crm->create_time=date('Y-m-d H:i:s');
                     $crm->last_update_time=date('Y-m-d H:i:s');
                     $crm->status=0;
                     $crm->source=20;
                     $crm->mdata['desc']=$_POST['desc'];
                     $crm->mdata['cust_name']=$_POST['name'];
                     if($crm->save()){
                         $crm->addNotes('new Ticket created', 0,false);
                         CrmMap::createCrmMaps($crm->id,new ExParcel(),[$shipment->id]);
                     $crm=Crm::model()->findByPk($crm->id);
                     $this->saveImage($crm->id);
                     $o['status']='done';
                     $o['msg']='No:'.$crm->no;
                     }else{
                          $o['status']=0;
                     }
                    echo json_encode($o);return;
               }else{
                  //import develop later
                    $o['status']=0;
                    $o['msg']='单号没找到';
                    echo json_encode($o);return;
              }
            }else if(in_array($type, array(10))){
                if(empty($shipment)||$shipment->type==20){
                  $crm=new ExCrm();
                  $crm->open_by=0;
                  $crm->level=1;
                  $crm->type=90;
                  $crm->telephone=$_POST['tel'];
                  $crm->email=$_POST['email'];
                  $crm->create_time=date('Y-m-d H:i:s');
                  $crm->last_update_time=date('Y-m-d H:i:s');
                  $crm->status=0;
                  $crm->source=20;
                  $crm->mdata['desc']=$_POST['desc'];
                  $crm->mdata['cust_name']=$_POST['name'];
                  if($crm->save()){
                     $crm->addNotes('new Ticket created', 0,false);
                     $crm=Crm::model()->findByPk($crm->id);
                     if(!empty($shipment)){
                          CrmMap::createCrmMaps($crm->id,new ExParcel(),[$shipment->id]);
                     }
                     $this->saveImage($crm->id);
                    $o['status']='done';
                     $o['msg']='No:'.$crm->no;
                     }else{
                          $o['status']=0;
                     }
                       echo json_encode($o);return;
                }else if(!empty($shipment)&&$shipment->type==10){
                    $o['status']=0;
                    $o['msg']='单号没找到';
                    echo json_encode($o);return;
                }
                  
            }else if(in_array($type,array(40))){
                  $crm=new ExCrm();
                  $crm->level=1;
                  $crm->open_by=0;
                  $crm->type=80;
                  $crm->telephone=$_POST['tel'];
                  $crm->email=$_POST['email'];
                  $crm->create_time=date('Y-m-d H:i:s');
                  $crm->last_update_time=date('Y-m-d H:i:s');
                  $crm->status=0;
                  $crm->source=20;
                  $crm->mdata['desc']=$_POST['desc'];
                  $crm->mdata['cust_name']=$_POST['name'];
                 if($crm->save()){
                     $crm->addNotes('new Ticket created', 0,false);
                     $crm=Crm::model()->findByPk($crm->id);
                     $this->saveImage($crm->id);
                     $o['status']='done';
                     $o['msg']='No:'.$crm->no;
                     }else{
                          $o['status']=0;
                     }
                       echo json_encode($o);return;
               }   
                 echo json_encode($o);return;
        }
        
        
        public function saveImage($id){
               $td = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR;
               foreach($_FILES as $k=>$f){
			$fd = base64_decode(preg_replace('/data:image\/[^;]+;base64,/', '', file_get_contents($f['tmp_name'])));
			if(strlen($fd) < 100) continue;
			$tf = tempnam($td, "file".$k);
			file_put_contents($tf, $fd);
			$img = AppHelper::resizeImg($tf, 600);
			if($img) @imagejpeg($img, $tf);
			$fn = 'customer_attachment_'.$k.'.jpg';
			$fid = FileRepo::storeFile($tf, $fn, 100, $id);
                        unlink($tf);
		}
        }
        
       public function actionIndex(){
             if(empty($_GET['r'])){
                 return false;
             }else{
                 $no=trim($_GET['r']);
                 $crm = Crm::model()->find('no=:c AND status != 100', array(':c' => $no));
		if(empty($crm) || empty($crm->notes)){
			$o = false;
		}else{
			$o = $crm->trackingInfo();
		}
		echo json_encode($o);
           }
	
   }
}

