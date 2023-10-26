<?php

/**
 * This is the model class for table "sea_process".
 *
 * The followings are the available columns in table 'sea_process':
 * @property integer $id
 * @property integer $pid
 * @property integer $type
 * @property integer $status
 * @property string $start_time
 * @property string $date
 * @property integer $meta
 */
class SeaProcess extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $mdata,$nolog=false,$custom_log_note='';
        public function tableName()
	{
		return 'sea_process';
	}
        
        public static $states=array(       //0==>delete process
            10=>'Waiting PreAlert And Arrival Notice',// etd 7 days
            20=>'Waiting Request Manifest', // before eta 4 days
            30=>'Waiting DO',               // after eta 4 days
            40=>'Waiting Outtun',
            50=>'Waiting Decomposition',
            100=>'Sea Process done',
        );
        public static $sea_process_types=array(
            1=>'Sea Bulk',
            2=>'Sea Total',
        );
        
        const TYPE_SEA_BULK=1;        
        
        const STATE_WAITING_PREALERT=10;
        const STATE_WAITING_REQUEST_MANIFEST=20;
        const STATE_WAITING_DO=30;
        const STATE_WAITING_OUTTURN=40;
        const STATE_WAITING_DECOMPOSITION=50;
        const STATE_SEA_PROCESS_DONE=100;

        /**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pid, type, status, start_time, date', 'required'),
			array('pid, type, status', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, pid, type, status, start_time, date, meta', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
                    'shipment' => array(self::BELONGS_TO, 'Shipment', 'pid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'pid' => 'Pid',
			'type' => 'Type',
			'status' => 'Status',
			'start_time' => 'Start Time',
			'date' => 'Date',
			'meta' => 'Meta',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('pid',$this->pid);
		$criteria->compare('type',$this->type);
		$criteria->compare('status',$this->status);
		$criteria->compare('start_time',$this->start_time,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('meta',$this->meta);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        public function getStatus(){
		return isset(static::$states[$this->status])? Yii::t(strtolower(__CLASS__), static::$states[$this->status]) : $this->status;
	}        
        public function beforeSave(){
            if(!empty($this->mdata)){ $this->meta= json_encode($this->mdata);}
            return true;
        }
        public function afterFind(){
            if(!empty($this->meta)){ $this->mdata= json_decode($this->meta,true);}
        }
        public function afterSave(){
            if(!$this->nolog && !empty($this)){
                $extra = empty($this->custom_log_note) ? [] : ['note' => $this->custom_log_note];
                Log::add($this, $this->isNewRecord ? 3 : 4, array_merge(array('status' => $this->getStatus()), $extra));
           }
        }
        private function preEmailLog($type,$notify){
            $email_log=new Emailog();
            $email_log->type= $type;
            $email_log->fid= $this->pid;
            $email_log->dt=date('Y-m-d H:i:s');
            $email_log->status=10;
            $email_log->prepTemplate();
            $email_log->tpl->assignThese([
                 'CLIENT_NAME'=>$notify->name,
             ]);
            $email_log->subject=$email_log->tpl->subject;
            $email_log->body=$email_log->tpl->getContent();
            $email_log->mdata['to']=$notify->email;
            $email_log->mdata['fromName']='TLA Imports';
            $email_log->mdata['from']='imports@toplogistics.com.au';
//            $email_log->mdata['cc']='imports@toplogistics.com.au';
            return $email_log;
        }
        private function getReceiverEmailAddress($shipment,&$notify){
            $reply=array('status'=>true,'msg'=>'');
            if(!empty($shipment->notifier->name)&&empty($shipment->notifier->email)){
                $reply['status']=false;
                $reply['msg']='Please supply notify party email';
                return $reply;
            }else if(empty($shipment->cnee->email)){
                $reply['status']=false;
                $reply['msg']='Please supply consignee email';
                return $reply;
            }else{
                if(!empty($shipment->notifier->email)){
                    $notify=$shipment->notifier;
                }else{
                   $notify=$shipment->cnee;
                }
                if(!AppHelper::validEmailGroup($notify->email)){
                  $reply['status']=false;
                  $reply['msg']='The email is not valid,Please Check!'; 
                }
            }
            return $reply;
        }
        
         public function updateStatus($status){
           $this->status=$status;
           $this->date=date('Y-m-d H:i:s');
           $this->update(['status','date']);
        }
   
        
        /**
         *  send Pre ALert and arrival notice 
         */
        public function sendPreAlertAndNoticeArrival(){
            $reply=array('status'=>true,'msg'=>'');
            $files=array(); $notify=new Addr();
            $tempDirectory = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
            if(!file_exists($tempDirectory)) {mkdir($tempDirectory);}
            $shipment=$this->shipment;
            $result=$this->getReceiverEmailAddress($shipment, $notify); //check if get valid email to send.
            if(!$result['status']){return $result;}
            $fileName=$tempDirectory.DIRECTORY_SEPARATOR.'pre_alert_'.$shipment->getRef().'.pdf';
            oPDF::renderPDF('pre_alert', array('model' => $shipment), 2, $fileName);
            $fileName2=$tempDirectory.DIRECTORY_SEPARATOR.'arrival_notice_'.$shipment->getRef().'.pdf';
            oPDF::renderPDF('arrival_notice', array('model' => $shipment), 2, $fileName2);
            $files[]=[$fileName, basename($fileName)];
            $files[]=[$fileName2, basename($fileName2)];//arrival notice
            $email_log= $this->preEmailLog(Emailog::SEA_PRE_ALERT,$notify);
            $o=$email_log->sendEmail($files);
            if($o['status']){ $this->updateStatus(self::STATE_WAITING_REQUEST_MANIFEST);}
            AppHelper::unlinkRecursive($tempDirectory);
            $reply['status']=$o['status'];
            $reply['msg']=$o['msg'];
            return $reply;
        }
        
        public function sendRequestManifest(){
            $reply=array('status'=>true,'msg'=>'');
            $files=array();$notify=new Addr();
            $tempDirectory = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
            if(!file_exists($tempDirectory)) {mkdir($tempDirectory);}
            $shipment=$this->shipment;
            $result=$this->getReceiverEmailAddress($shipment, $notify); //check if get valid email to send.
            if(!$result['status']){return $result;}
            $fileName=$tempDirectory.DIRECTORY_SEPARATOR.'manifest_'.$shipment->getRef().'.pdf';
            oPDF::renderPDF('sea_manifest', array('model' => $shipment), 2, $fileName);
            $files[]=[$fileName, basename($fileName)];
            $email_log= $this->preEmailLog(Emailog::SEA_REQUEST_MANIFEST,$notify);
            $o=$email_log->sendEmail($files);
            if($o['status']){
                $this->updateStatus(self::STATE_WAITING_DO);
            }
            AppHelper::unlinkRecursive($tempDirectory);
            $reply['status']=$o['status'];
            $reply['msg']=$o['msg'];
            return $reply;
        }
        
        public function sendDo(){
            $reply=array('status'=>true,'msg'=>'');
            $files=array();$notify='';
            $tempDirectory = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
            if(!file_exists($tempDirectory)) {mkdir($tempDirectory);}
            $shipment=$this->shipment;
            $result=$this->getReceiverEmailAddress($shipment, $notify); //check if get valid email to send.
            if(!$result['status']){return $result;}
            $fileName=$tempDirectory.DIRECTORY_SEPARATOR.'sea_order_'.$shipment->getRef().'.pdf';
            oPDF::renderPDF('sea_order', array('model' => $shipment), 2, $fileName);
            $files[]=[$fileName, basename($fileName)];
            $email_log= $this->preEmailLog(Emailog::SEA_DO,$notify);
            $o=$email_log->sendEmail($files);
            if($o['status']){
                $this->updateStatus(self::STATE_WAITING_OUTTURN);
            }
            AppHelper::unlinkRecursive($tempDirectory);
            $reply['status']=$o['status'];
            $reply['msg']=$o['msg'];
            return $reply;
        }
        public function sendOutturn(){
            $reply=array('status'=>true,'msg'=>'');
            $files=array();$notify='';
            $tempDirectory = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
            if(!file_exists($tempDirectory)) {mkdir($tempDirectory);}
            $shipment=$this->shipment;
            $result=$this->getReceiverEmailAddress($shipment, $notify); //check if get valid email to send.
            if(!$result['status']){return $result;}
            $fileName=$tempDirectory.DIRECTORY_SEPARATOR.'sea_outturn_'.$shipment->getRef().'.pdf';
            oPDF::renderPDF('sea_outturn', array('model' => $shipment), 2, $fileName);
            $files[]=[$fileName, basename($fileName)];
            $email_log= $this->preEmailLog(Emailog::SEA_OUTTURN,$notify);
            $o=$email_log->sendEmail($files);
            if($o['status']){
                $this->updateStatus(self::STATE_WAITING_DECOMPOSITION);
            }
            AppHelper::unlinkRecursive($tempDirectory);
            $reply['status']=$o['status'];
            $reply['msg']=$o['msg'];
            return $reply;
        }
        
        /**
         * show the information about the operation
         * that need to be take.
         */
        public function getOperationStatus(){
            $msg='';
            if($this->status>=100){
                $msg.="<p style='color:green'>The process is done!</p>";
            }else if(empty($this->shipment->consol->eta)||empty($this->shipment->consol->etd)){
                if(empty($this->shipment->consol->eta)){
                    $msg.="<p style='color:red'>Please Input the ETA</p>";   
                }
                if(empty($this->shipment->consol->etd)){
                    $msg.="<p style='color:red'>Please Input the ETD</p>";   
                }
            }else {
                $etd=new DateTime($this->shipment->consol->etd,new DateTimeZone('Australia/Sydney'));
                $eta=new DateTime($this->shipment->consol->eta,new DateTimeZone('Australia/Sydney'));
                $now = new DateTime('now', new DateTimeZone('Australia/Sydney'));
                $etdGap = $etd->diff($now)->format("%r%a");
                $etaGap = $eta->diff($now)->format("%r%a");
                if($this->status== SeaProcess::STATE_WAITING_PREALERT){
                    if($etdGap<=7){
                        $msg.="<p style='color:green'>The Alert will be send within ".intval(7-$etdGap)." days</p>";
                    }else{
                        $msg.="<p style='color:red'>The 7 days from etd has passed, please send Pre alert Manually.</p>";
                    }
                }elseif ($this->status== SeaProcess::STATE_WAITING_REQUEST_MANIFEST) {
                    if($etaGap<=0){
                         $msg.="<p style='color:green'>The Request Manifest will be send within ".intval(-4-$etaGap)." days</p>";
                    }else {
                         $msg.="<p style='color:red'>The eta has passed, please send Manifest Manually.</p>";
                    }
                }else if ($this->status== SeaProcess::STATE_WAITING_DO){
                     if($etaGap<=4){
                          $msg.="<p style='color:green'>The DO will be send within ".intval(4-$etaGap)." days</p>";
                     }else{
                         $msg.="<p style='color:red'>The 4 days from eta has passed, please send DO Manually.</p>";
                     }
                } else if($this->status== SeaProcess::STATE_WAITING_OUTTURN){
                      $msg.="<p style='color:green'>Please do the Outtun Manually.</p>";
                 }else if ($this->status== SeaProcess::STATE_WAITING_DECOMPOSITION){
                     $msg.="<p style='color:green'>Decomposition on Process.</p>";
                 }
               
            }
            return $msg;
        }
        
        /**
         * 7-9day from etd send pre alert
         * 4-0 day before eta send manifest
         * 4 day after eta send do (sea order)
         *  
         */
        public function doProcessForward(){
           if(!empty($this->shipment->consol->etd)&&!empty($this->shipment->consol->eta)){
                  $etd=new DateTime($this->shipment->consol->etd,new DateTimeZone('Australia/Sydney'));
                  $eta=new DateTime($this->shipment->consol->eta,new DateTimeZone('Australia/Sydney'));
                  $now = new DateTime('now', new DateTimeZone('Australia/Sydney'));
                  $etdGap = $etd->diff($now)->format("%r%a");
                  $etaGap = $eta->diff($now)->format("%r%a");
                  if($this->status== SeaProcess::STATE_WAITING_PREALERT){
                      if($etdGap>=7&&$etaGap<=9){  //etd 7~9 day will send prealert
                          $this->sendPreAlertAndNoticeArrival();
                      }
                  }else if($this->status== SeaProcess::STATE_WAITING_REQUEST_MANIFEST){
                      if($etaGap>=-4&&$etaGap<0){  //etd 7~9 day will send prealert
                          $this->sendRequestManifest();
                      }
                  }else if($this->status== SeaProcess::STATE_WAITING_DO){
                      if($etaGap==4){
                          $this->sendDo();
                      }
                  }
             }
        }
        
      public function deleteProcess(){
          $this->status=0;
          $this->update('status');
      }
    
        /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SeaProcess the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
