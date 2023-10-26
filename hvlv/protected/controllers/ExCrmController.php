<?php

class ExCrmController extends Controller{
    
    
    
    public function actionCrmManage(){
        
        if(!empty($_POST)){
           if(!empty($_POST['the_meta_items'])){
               $meta_item= json_decode($_POST['the_meta_items'],true);
               foreach (ExCrm::$types as $m=>$n){
                   CrmManage::model()->deleteAll('type=20 and tid=:tid',array(':tid'=>$m));
               }
               foreach($meta_item as $key=>$value){
                   foreach ($value as $k=>$v){
                       if(empty($v))       continue;
                           $manage=new CrmManage();
                           $manage->type=20;
                           $manage->tid=$k;
                           $manage->uid=$v;
                           $manage->date=date('Y-m-d');
                           $manage->status=1;
                           $manage->save();
                       }
               }
            
           }
           $manage=new CrmManage();
           $this->ajaxResult($manage);
        }
        $this->render('manage_ticket');
    }
      public function _plQuery(){
		$sql = "SELECT type, COUNT(*) as number FROM `crm` WHERE main_type=20 AND status!=90 Group by type";
		return Yii::app()->db->createCommand($sql)->queryAll();
	}
        public function _plQuery1($type){
		$sql = "SELECT status, COUNT(*) as number FROM `crm` WHERE main_type=20 AND status!=90 AND type=".$type." Group by status ";
                return Yii::app()->db->createCommand($sql)->queryAll();
	}
    public function actionAjaxTicketsSum($path){
        	$pt = explode('/', $path);
                $rs = $this->_plQuery();
		$ls = '';
                if(sizeof($pt)<2){
			$sum = 0;
			foreach($rs as $r){
				$ls .= '<li><p><span class="exp" data-path="'.$path.'/'.$r['type'].'" data-loaded="0"></span> '. ExCrm::$types[$r['type']].' &nbsp;&nbsp; Number: '.$r['number'].'</p><ul></ul></li>';
				$sum+=$r['number'];
			}
			echo '<li><p>Total - Number: '.$sum.'</p><ul>'.$ls.'</ul></li>';
                }else{
                    $this->ajaxSubSumReport($pt, $path);
                }
		
    }
    public function ajaxSubSumReport($pt,$path){
             if(sizeof($pt)==2){
		$rs = $this->_plQuery1($pt[1]);
                foreach ($rs as $r){
	         echo '<li><p><span class="exp" data-path="'.$path.'/'.$r['status'].'" data-loaded="0"></span>'. ExCrm::$states[$r['status']].' - Number:'.$r['number'].'</p><ul></ul></li>';
                }
              }else{
                 echo '';
              }
	}
        
    public function actionTicketList(){
        $model=new ExCrm('search');
        $model->unsetAttributes(); 
        //add access filter
        $provide=[];
        $total=0;
        if(Acl::hasAccess("B:Excrm/manager")||Acl::hasAccess("B:Excrm/finance")){
            $sql="SELECT type, COUNT(*) as no,COUNT(if(status=0,1,null)) as n0, COUNT(if(status=10,1,null)) as n10, COUNT(if(status=20,1,null)) as n20, COUNT(if(status=30,1,null)) as n30 FROM `crm` WHERE main_type=20 and status not in (90,100) GROUP by type";
            $rs=Yii::app()->db->createCommand($sql)->queryAll();   
            foreach ($rs as $index=>$r){
                $total+=intval($r['no']);
                $provide[]=array('id'=>$index,'rawType'=>$r['type'],'type'=> ExCrm::$types_cn[$r['type']],'new'=>$r['n0'],'op'=>$r['n10'],'manager'=>$r['n20'],'finance'=>$r['n30']);
            }
//        }else 
//            if(Acl::hasAccess("B:Excrm/finance")){
//            $model->statuses=[ExCrm::STATE_FINANCE];
//             $sql="SELECT type, COUNT(*) as no,COUNT(if(status=0,1,null)) as n0, COUNT(if(status=10,1,null)) as n10, COUNT(if(status=20,1,null)) as n20, COUNT(if(status=30,1,null)) as n30 FROM `crm` WHERE main_type=20 and status not in (90,100) GROUP by type";
//            $rs=Yii::app()->db->createCommand($sql)->queryAll();   
//            foreach ($rs as $index=>$r){
//                $total+=intval($r['no']);
//                $provide[]=array('id'=>$index,'rawType'=>$r['type'],'type'=> ExCrm::$types_cn[$r['type']],'new'=>$r['n0'],'op'=>$r['n10'],'manager'=>$r['n20'],'finance'=>$r['n30']);
//            }
        }else{
           $model->tids= array_merge(CrmManage::getAccessTypes(Yii::app()->user->id,20),[0]);
           $sql="SELECT type, COUNT(*) as no,COUNT(if(status=0,1,null)) as n0, COUNT(if(status=10,1,null)) as n10, COUNT(if(status=20,1,null)) as n20, COUNT(if(status=30,1,null)) as n30 FROM `crm` WHERE main_type=20 AND type in (".implode(",", $model->tids).") and status not in (90,100) GROUP by type";
           $rs=Yii::app()->db->createCommand($sql)->queryAll();
              foreach ($rs as $index=>$r){
                $total+=intval($r['no']);
                $provide[]=array('id'=>$index,'rawType'=>$r['type'],'type'=> ExCrm::$types_cn[$r['type']],'new'=>$r['n0'],'op'=>$r['n10'],'manager'=>$r['n20'],'finance'=>$r['n30']);
            }
        }
        if(isset($_GET['ExCrm'])){
            $model->attributes=$_GET['ExCrm'];
        }
        if(!empty($_GET['type'])){
           $model->type=$_GET['type'];
        }
          $filtersForm=new FiltersForm;
          if (isset($_GET['FiltersForm'])) $filtersForm->filters=$_GET['FiltersForm'];
          $filteredData=$filtersForm->filter($provide);
          $dataprovider=new CArrayDataProvider($filteredData); 
          $dataprovider->pagination=array('pageSize' =>10,);
          $sort=new CSort();
          $sort->attributes=array(
             'type'=>array(
                   'asc'=>'type ASC',
                   'desc'=>'type DESC',
                ),
         );
       $sort->defaultOrder = "type ASC";
       $dataprovider->sort=$sort;
       $this->render('excrm_list',array('model'=>$model,'dataProvider'=>[$dataprovider,$filtersForm],'total'=>$total));
    }
    
    public function actionMytickets(){
        $model=new ExCrm('search');
        $model->unsetAttributes(); 
         if(isset($_GET['ExCrm'])){
            $model->attributes=$_GET['ExCrm'];
        }
        $model->assign_to=Yii::app()->user->id;
        $this->render('excrm_my_list',array('model'=>$model));
    }
    
    public function actionFinanceTickets(){
        $model=new ExCrm('search');
        $model->unsetAttributes(); 
        if(Acl::hasAccess("B:Excrm/finance")){
            $model->bwfs=2;
        }else{
            $model->id=-1;
        }
         if(isset($_GET['ExCrm'])){
            $model->attributes=$_GET['ExCrm'];
        }
        $this->render('excrm_finance_list',array('model'=>$model));
    }




    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionUpdate($id){

        // get crm ticket id
        $crmLog = new CrmLog();
        $crmLog->crm_id = $id;
        $model= $this->loadModel($id);
        if(isset($_POST['ExCrm'])){
            $model->attributes=$_POST['ExCrm'];
            $model->save();
            $this->ajaxResult($model);

        }
        if(isset($_POST['CrmComp'])){
             $model->crm_comp->attributes=$_POST['CrmComp'];
             foreach ($_POST['CrmComp']['mdata'] as $key=>$value){
                 $model->crm_comp->mdata[$key]=$value;
             }
             $model->crm_comp->save();
             $this->ajaxResult($model);
        }

        $model = Crm::model()->findByPk($id);
        if (isset($_GET['tab'])) {
            Acl::hasAccess($this->CaName . '/' . $_GET['tab'], true);
            $this->render('tab_' . $_GET['tab'], array('model' => $model,'crmLog'=>$crmLog));
        } else {
            $this->render('update', array(
                'model' => $model,
                'crmLog' => $crmLog,
            ));
        }
    }
    /* this function will be used to send sms to customer
     * 
     */
    public function actionSendSms($id){
        $model=$this->loadModel($id);
        if(isset($_POST['country'])){
            if($_POST['country']=='CN'){
                $no=$_POST['ExCrm']['telephone'];
                $msg=$_POST['msg_to_customer'];
                if(!Sms::SendMessageCn($no, $msg)){
                    $model->addError('msg','failed send,please check telephone number');
                }else{
                    //add log;
                    $model->addNotes('send Msg: '.$msg.' to tel: '. $no, CrmLog::METHOD_SMS);
                }
            }else if($_POST['country']=='AUS'){
                $no=$_POST['ExCrm']['telephone'];
                $msg=$_POST['msg_to_customer'];
                $no=Addr::auTelValid($no);
                if(preg_match('/^04\d{8}$/',$no)){
                   if(!Sms::sendMessageLocal($no, $msg)){
                      $model->addError('msg','failed send');
                    }else{
                         $model->addNotes('send Msg: '.$msg.' to tel: '.$no, CrmLog::METHOD_SMS);
                    }
                }else{
                    $model->addError('msg','the telephone number is not valid!');
                }
           }
            $this->ajaxResult($model);
        }
        
        $this->render('sms_cust',array('model'=>$model));
    }
    
    public function actionSendEmail($id){
        $exCrm= $this->loadModel($id);
        $model=new Emailog();
        $model->type= Emailog::EX_ID_CHASE;
        $model->fid=$id ;
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
                $o=$model->sendEmail();
                if($o['status']){
                   $exCrm->addNotes('Send an email to '.$model->mdata['from'], CrmLog::METHOD_EMAIL);
                }
               $this->ajaxResult($model,array('id'),'Email Sent Successfully');
         }
        $model->prepTemplate();
        $model->subject=$model->tpl->subject;
        $model->body=$model->tpl->getContent();
        $model->mdata['to']=$exCrm->email;
        $model->mdata['from']=Yii::app()->user->email;
        $model->mdata['cc']='';
        $this->render('send_email',array('model'=>$model,'crm'=>$exCrm));
    }
    
    
    public function actionCreate(){
         $model=new ExCrm('search');
         $model->unsetAttributes();
        if(isset($_POST['ExCrm'])){
            $sids=[];
            if(in_array($_POST['ExCrm']['type'],[10,20,30,40,50])){
                $errors=[];
                if(!empty($_POST['connote_no'])){
                    foreach(preg_split('/[\s,;]+/', trim($_POST['connote_no'])) as $h){
                       if(empty($h)) continue;
                       $shipment= Shipment::model()->find('hbn=:r OR ref=:r',array(':r'=>trim($h)));
                        if(empty($shipment)){
                            $errors[]= $h.' 单号不存在！';
                        }else{
                            $sids[]=$shipment->id;
                        }
                    }
                  }else{
                     $errors[]='你所选的ticket 类型必须提供运单号';
               }
                if(!empty($errors)){
                     $model->addError('id', implode(";", $errors)); 
                      $this->ajaxResult($model);
                 }
            }
            $tickets_no=[];
            $model=new ExCrm('search'); 
            $model->attributes=$_POST['ExCrm'];
            $model->open_by=Yii::app()->user->id;
            $model->create_time=date('Y-m-d H:i:s');
            $model->status=0;
            if($model->save()){
                      $model->addNotes($_POST['notes'], 0,false);
                       if(!empty(Yii::app()->session['uploads'][$_POST['ppupload']][2])){
		           foreach(Yii::app()->session['uploads'][$_POST['ppupload']][2] as $fid){
			         $fr = FileRepo::model()->findByPk($fid);
			         $fr->fid = $model->id;
			         $fr->save();
				}
			} 
              $model->afterFind();
             
              if(!empty($sids)){
                  CrmMap::createCrmMaps($model->id, new ExParcel(),$sids);
               }
             }
           $this->ajaxResult($model,[],'Create Succesfully, 单号：'. $model->no);
        }
        
        $this->render('create_ticket',array('model'=>$model));
    }
    
    //create tickets for eparcel information short
     public function actionCreateTickets($id){
         $model=new ExCrm('search');
         $model->unsetAttributes();
        if(isset($_POST['ExCrm'])){
            $shipment= Shipment::model()->findByPk($id);
            $model=new ExCrm('search'); 
            $model->attributes=$_POST['ExCrm'];
            $model->level=1;
            $model->email=$shipment->cnee->email;
            $model->telephone=$shipment->cnee->tel;
            $model->open_by=Yii::app()->user->id;
            $model->create_time=date('Y-m-d H:i:s');
            $model->status=0;
            if($model->save()){
                     $model->addNotes($_POST['notes'], 0,false);
                     $model->afterFind();
                     CrmMap::createCrmMaps($model->id, $shipment, [$shipment->id]);
             }
           $this->ajaxResult($model,[],'Create Succesfully, 单号：'. $model->no);
        }
        
        $this->render('create_t',array('model'=>$model));
    }

    /**
     * create a new CRM ticket
     * @param $id
     */
    public function actionCrmMoreNotes($id){
        $model= $this->loadModel($id);
        if(!empty($_POST['notes'])){
            // $crm = Crm::addLog($model, $_POST);
            $crmlog_model = new CrmLog('search');
            if($_POST['process_method']== CrmLog::METHOD_MANAGER){
                $model->status= ExCrm::STATE_MANAGER;
            }else if($_POST['process_method']== CrmLog::METHOD_FINANCE){
               $resp=$model->pushToFinance();
               if(!$resp['status']){
                   $model->addError('id',$resp['msg']);
                   $this->ajaxResult($model);
               }
           } 
            $crmlog_model->attributes = array(
                'crm_id' => $id,
                'operator_id' => Yii::app()->user->id,
                'note' => $_POST['notes'],
                'process_method'=>$_POST['process_method'],
            );
            if($crmlog_model->save()){
                $model->updateCurrentUser();
            }
            $this->ajaxResult($crmlog_model);
        }
    }
    
    public function actionConnote(){
        if(!empty($_GET['connote'])){
            $shipment= Shipment::model()->find('hbn=:r OR ref=:r',array(':r'=>trim($_GET['connote'])));
            if(!empty($shipment)){
                echo 'done';
                return;
            }
        }
        echo 'not found';
        return;
    }
    
    public function actionNotes($id){
		$model = $this->loadModel($id);
		Log::add($model,Log::LOG_TYPE_NOTES,['notes' => $_POST['notes']]);
		$this->ajaxResult($model);
	}
    public function actionCopyLink($id){
        $model= $this->loadModel($id);
        $link=$model->genHashUrl('cf');
        echo $link;
    }
    
    
      /**
       * the report of the custom process, which include average closing tickets on EMPP, HV ,AQIS during the select Period.
       * @_POST   start_date, end_date;
       */
      public function actionReport(){
        $filtersForm = new FiltersForm;
        $provide = [];
        if (isset($_GET['FiltersForm']))
            $filtersForm->filters = $_GET['FiltersForm'];
        if(!empty($_GET['start_date']) && !empty($_GET['end_date'])){
            $start_time = $_GET['start_date'];
            $end_time = $_GET['end_date'];
            $end_time=date('Y-m-d',strtotime('+1 day',strtotime($end_time)));
            foreach (ExCrm::$types_cn as $key=>$value){
                if($key== ExCrm::TYPE_PORT_CLAIM)                    continue;
                $sql="SELECT COUNT(*) FROM `crm` WHERE main_type=20 AND type=:type AND create_time>=:start_time AND create_time<=:end_time";
                $open=Yii::app()->db->createCommand($sql)->bindValues([':type'=>$key,':start_time'=>$start_time,':end_time'=>$end_time])->queryScalar();
                $sql="SELECT COUNT(*) FROM `crm` WHERE main_type=20 AND type=:type AND close_time>=:start_time AND create_time<=:end_time AND status=90";
                $close=Yii::app()->db->createCommand($sql)->bindValues([':type'=>$key,':start_time'=>$start_time,':end_time'=>$end_time])->queryScalar();
                if($open>0 ||$close>0){
                    $provide[]=['id'=>$key,'name'=>$value,'open'=>$open,'close'=>$close];
                }
            }
          }
        $filteredData = $filtersForm->filter($provide);
        $dataprovider = new CArrayDataProvider($filteredData);
         if (!empty($_GET['partial'])) {
            $this->renderPartial('report_search', array(
                'dataProvider' => [$dataprovider, $filtersForm],
            ));
            return;
         }
          $this->render('report',['dataProvider' => [$dataprovider, $filtersForm]]);
      }
    
          
    public function actionLog($id){
            $model = $this->loadModel($id);
            $this->render('log',array(
			'model'=>$model,
	    ));
     }
     public function actionSmsTp($id){
         $model=$this->loadModel($id);
         $isSms=true;
         if(isset($_GET['isSms'])&&$_GET['isSms']==2){
             $isSms=false;
           }
         echo $model->cnSmsTp($_GET['index'],$isSms);
      }
     
  	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model= ExCrm::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
    
}
