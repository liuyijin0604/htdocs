<?php 
class ExCrm extends Crm{
    
    public static $my_type=20;
    
    const TYPE_ID_CHASE=10;
    const TYPE_INFO_CHASE=20;
    const TYPE_COMPENSATION=30;
    const TYPE_DUTY_COLLECT=40;
    const TYPE_PARCEL_QUERY=50;
    const TYPE_GENERAL_ENQUIRY=90;
    const TYPE_PORT_CLAIM=35;
    
    const STATE_PENDING=0;
    const STATE_OP=10;
    const STATE_MANAGER=40;
    const STATE_FINANCE=50;
    const STATE_AUS_MANA=60;
    const STATE_CANCEL=100;
    
    const OP_CLAIM_AMMOUNT=100;
    const MANAGER_CLAIM_AMMOUNT=200;
    
    public static $types=array(
        10=>'ID chase up',
        20=>'Information chase up',
        30=>'Compensation',
        35=>'Port Claim',
        40=>'Duty Collection ',
        50=>'Parcel Query',
        80=> 'Business Query',
        90=>'General enquiry',
        100=>'Others',
        
    );
    public static $ports=array(
        'XM'=>'厦门',
        'JJ'=> '晋江',
        'XA'=> '西安',
        'KM' => '昆明',
        'CS' =>  '长沙',
        'QD' => '青岛',
    );
    public static $types_cn=array(
        10=>'身份证追踪',
        20=>'信息追踪',
        30=>'理赔',
        35=>'口岸索赔',
        40=>'Duty 征收',
        50=>'包裹查询',
        80=> '生意问询',
        90=>'一般问询',
        100=>'其他的',
        
    );
       public static $flags=array(
            1=>'理赔表已经填写',
            2=>'理赔 Others 支付',
            4=>'理赔 Credit Note 支付',          
        );
         public static $assign_to=array(
            0 =>'no assign',
            91=>'Chu Qi',
            465=>'Joey',
            238=>'Peter', 
            417=>'Barry',
        );
    public static $states = array(
        0  => '新的',
        10 => '客服处理中',
        40 => '经理处理中',
        50 => '财务处理中',
        90 => '关掉',
        100 => '取消',
    );
    
    public static $sources=array(
         1=>'系统',
        13=>'电话',
        12=> '电子邮件',
        11=> '微信',
        10=>'QQ',
        14=>'其他',
        20=> 'Web',
    );
    const  COMP_PORT_LOST=1;
    const  COMP_PORT_BROKEN=2;
    public static $comp_port_type=array(
        1=>'损坏',
        2=>'丢失',
    );  
    public static $comp_port_response=array(
        1=>'清关',
        2=>'派送',
    );
    public static $cn_sms_tp=array(
        1=>'收到咨询',
        2=>'告诉咨询结束',
        3=>'身份证追踪',
        4=>'包裹已投递',
        5=>'填写理赔表'
       
    );
    
    public function cnSmsTp($index,$isSms=true){
        switch ($index){
           case 1: 
               return '尊敬的用户，我们已经收到了你提交'.($this->getTypecn()).'的咨询，咨询单号为'.($this->no).',你可以登录我们的官网 http://t.cn/Rm9g0ut 查询进度。';
           case 2:
               return  '亲爱的客户，您的'.($this->no).'的咨询已处理完毕。你可以登录我们的网站 http://t.cn/Rm9g0ut 查询结果，如有疑问请联系我们的客服95040315891';
           case 3:
                return (isset($this->shipments[0]->cnee->name)?$this->shipments[0]->cnee->name:'客户').'您好！您来自澳洲的包裹 '.(isset($this->shipments[0]->hbn)?$this->shipments[0]->hbn:'').' 至今未成功匹配身份证，请通过网站 http://t.cn/RwBkisk 上传，如有疑问请联系客服电话 95040315891';
           case 4:
                return (isset($this->shipments[0]->cnee->name)?$this->shipments[0]->cnee->name:'客户').'您好！您的包裹 '.(isset($this->shipments[0]->hbn)?$this->shipments[0]->hbn:'').' ，已投递完毕，您可以登录我们官方网站 http://t.cn/RmSxYlq 查询。如有疑问请联系客服电话 95040315891';
           case 5:
               return (isset($this->shipments[0]->cnee->name)?$this->shipments[0]->cnee->name:'客户').'您好，你申请的理赔已经核实，请通过以下链接填写申请表 '. ($isSms?$this->genHashUrl('cf'):'<a href="'.$this->genHashUrl('cf').'">'.$this->genHashUrl('cf').'</a>');
       }
   }
       
       /*notice the customer that the ticket is already
       * closed
       * 
       */
     public function noticeTicketClose(){
         $msg= $this->cnSmsTp(2);
         if(preg_match('/^1\d{10}$/', $this->telephone)){//CN number
             if(Sms::SendMessageCn($this->telephone, $msg)){
                $this->addNotes('send Msg: '.$msg.' to tel: '. $this->telephone, CrmLog::METHOD_SMS,false);
             }
         }else if(preg_match('/^04\d{8}$|^4\d{8}/',$this->telephone)){
             $no=Addr::auTelValid($this->telephone);
             //aupost number
             if(Sms::sendMessageLocal($no, $msg)){
                 $this->addNotes('send Msg: '.$msg.' to tel: '.$no, CrmLog::METHOD_SMS,false);
             }
         }
     }
            

    public function getTypecn(){
		return Yii::t(strtolower(__CLASS__),key_exists($this->type, self::$types_cn)?self::$types_cn[$this->type]:'');
      }
       public function getType(){
		return Yii::t(strtolower(__CLASS__),key_exists($this->type, self::$types)?self::$types[$this->type]:'');
      }
       public function getSource(){
        return key_exists($this->source, self::$sources)?self::$sources[$this->source]:'';
      }
   public function getStatus(){
        return key_exists($this->status, self::$states)?self::$states[$this->status]:$this->status;
    }
   public function addNotes($note,$process_method,$updateUser=true){
            $crmlog_model = new CrmLog();
            $crmlog_model->attributes = array(
                'crm_id' => $this->id,
                'operator_id' => isset(Yii::app()->user->id) ? Yii::app()->user->id:0,
                'note' => $note,
                'process_method'=>$process_method
            );
            $crmlog_model->save();
            if($updateUser){
            $this->updateCurrentUser();
            }
            return true;
    }
    public function afterFind(){
        
        if(empty($this->no)){
            $this->no='E'.strtoupper(substr($this->getType(),0,2)).sprintf("%07s", substr($this->id, -7));
            $this->saveAttributes(['no']);
        }
        if($this->type==30){
            $this->findComp();
        }
        parent::afterFind();
    }
    public function getCompPort(){
       return isset($this->crm_comp->port)?isset(self::$ports[$this->crm_comp->port])?self::$ports[$this->crm_comp->port]:'':'';
    }
    
    public function getPortIssueProcess(){
        return isset($this->crm_comp->mdata['port_claim_area'])?(isset(self::$comp_port_response[$this->crm_comp->mdata['port_claim_area']])?self::$comp_port_response[$this->crm_comp->mdata['port_claim_area']]:""):"";
    }

    public function findComp(){
        if(empty($this->crm_comp)){
               $crmComp=new CrmComp();
               $crmComp->crm_id= $this->id;
               $crmComp->save();
         }else{
             $crmComp= $this->crm_comp;
         }
         return $crmComp;
    }
    /*push to the ticket to finance,
     * 1: op have $100 limit
     * 2: joey have $200 limit
     * 3: over $200 send to peter;
     *@Params
     *  
     *@Return Array
     */
    public function pushToFinance(){
        $resp=['status'=>1,'msg'=>''];
        if(empty($this->crm_comp->claim_amount)||$this->crm_comp->claim_amount<=0||$this->crm_comp->approve_ammount<=0
                ||(empty($this->crm_comp->mdata['payment_method'])&&(empty($this->crm_comp->bank_name)||empty($this->crm_comp->account_name)||empty($this->crm_comp->account_number))))
           {
            $resp['status']=0;
            $resp['msg']='请填写理赔信息表';
            return $resp;
        }

        if($this->crm_comp->approve_ammount<= self::OP_CLAIM_AMMOUNT){
                         //do nothing
        }else if($this->crm_comp->approve_ammount<= self::MANAGER_CLAIM_AMMOUNT){
            if(Acl::hasAccess("B:ExCrm/approveCompSuper")||Acl::hasAccess("B:ExCrm/approveComp")){
                //do nothing
            }else{
                $resp['status']=0;
                $resp['msg']='索赔额度大于100 请提交给你的上一级经理';
            }
        }else {
             if(Acl::hasAccess("B:ExCrm/approveCompSuper")){
                 // do nothing
             }else{
                $resp['status']=0;
                $resp['msg']='索赔额度大于200 请提交给Peter审理';
             }
        }
        $this->status= self::STATE_FINANCE;
        $this->bwf= $this->bwf|2;
        if($this->type== self::TYPE_COMPENSATION){
            if(!empty($this->findComp()->port)&&$this->getPortClaimValue()>0){
               $this->genSubTicket();
            }else{
                // if have port infor===>need to have port claim ticket
                //if not have port infor=> need to cancel port claim tiket when we created the ticket before.
               if($findTicket=$this->findSubTicket()){
                    $findTicket->updatePortClaimTicket();
              }
             }
        }
        return $resp;
    }
   public function genHashUrl($type='cf'){
       switch ($type){
           case 'cf':
               $url="https://os.pcaex.com"."/claim/index?no=".$this->no."&t=".$type."&h=".HashVerify::genHash($this);
               break;
           default :
                $url="https://www.pcaexpress.com.au";
       }
       return $url;    
   }
   public function getExtraInfo(){
       $info='';
       if(isset($this->crm_comp)&&($this->crm_comp->flag&1)>0){
           $info.='<span class="warn">已提交</span>';
       }
       if(isset($this->crm_comp)&&($this->crm_comp->flag&2)>0){
           $info.='<span class="warn">Others Payment</span>';
       }
        if(isset($this->crm_comp)&&($this->crm_comp->flag&4)>0){
           $info.='<span class="warn">Credit Note Payment</span>';
       }
       return $info;
   }
   public static function chaseIdTicket($pids=[],$no='',$email=''){
         $model=new ExCrm();
         $model->type=10;
         $model->open_by=0;
         $model->level= sizeof($pids);
         $model->last_update_time=date('Y-m-d H:i:s');
         $model->status=0;
         $model->telephone=$no;
         $model->source=1;//from system
         $model->email=$email;
         $model->save();
         CrmMap::createCrmMaps($model->id, new ExParcel(), $pids);
     }
     
     public function genSubTicket($type = self::TYPE_PORT_CLAIM) {
         $subTicket=null;
        if (empty($this->sub_tickets)) {
           $this->genTicketIfNotSubTicket($type);
        } else {
            $hasTicket = false;
            foreach ($this->sub_tickets as $sub) {
                if ($sub->type == $type) {
                    $hasTicket = true;
                    $subTicket=$sub;
                    break;
                }
            }
            if (!$hasTicket) {
                $this->genTicketIfNotSubTicket($type);
            }
        }
        if(!empty($subTicket)){//means subTicket is not just created,so we need to update the tickets
            $subTicket->updateExistingTicket();
        }
    }

    /* gen subticket when not have the explicitly type of ticket
      * @Params $type;
      */
     private function genTicketIfNotSubTicket($type){
             $sids=[];
             if (is_array($this->shipments)) {
                foreach ($this->shipments as $p) {
                    $sids[]=$p->id;
                }
            }
             $subTicket=new ExCrm();
             $subTicket->type=$type;
             $subTicket->link_id= $this->id;
             $subTicket->open_by= isset(Yii::app()->user->id)?Yii::app()->user->id:0;
             $subTicket->level= sizeof($sids);
             $subTicket->last_update_time=date('Y-m-d  H:i:s');
             $subTicket->status= $this->status;
             $subTicket->telephone=0;
             $subTicket->source=1;
             $subTicket->bwf=$subTicket->bwf|2;
             $subTicket->email='';
             $subTicket->save();
             if(!empty($sids)){
                  CrmMap::createCrmMaps($subTicket->id, new ExParcel(), $sids);
             }
             if(in_array($type, [self::TYPE_COMPENSATION, self::TYPE_PORT_CLAIM])){
                 $crmComp=$subTicket->findComp();
                 if($type== self::TYPE_PORT_CLAIM){
                     $crmComp->port= $this->crm_comp->port;
                     $crmComp->approve_ammount= $this->getPortClaimValue();
                     $crmComp->mdata['approve_currency']=3;
                     $crmComp->mdata['port_claim_area']= $this->crm_comp->mdata['port_claim_area'];
                     $crmComp->save();
                 }
             }
             return $subTicket;
     } 
     private function updateExistingTicket(){
         switch ($this->type){
             case self::TYPE_PORT_CLAIM:
                 $this->updatePortClaimTicket();
                 break;
             
         }
     }
     /**
      * check if the same parcel already created tickets for specify type
      * like on parcel if already compensation.  
      * @Param  $fid the shipment id
      * @Param $includeClose specify if consider the closed tickets.
      * @param type the type of the Tickets.
      * @return  Boolean 
      */
    public static function  hasTickets($fid,$type,$includeClose=false){
        $sql='SELECT * FROM `crm` t INNER JOIN `crm_map` s ON t.id=s.crm_id WHERE s.fid=:fid AND t.type=:type AND t.status!=100';
        if(!$includeClose){
            $sql.=' AND t.status!=90';
        }
        $r=Yii::app()->db->createCommand($sql)->bindValues([':type'=>$type,':fid'=>$fid])->queryScalar();
        if($r){
            return true;
        }
        return false;
    }
    /* *
      * update the port_claim amount when the main tickets ammounts get changed!
      * AND update the related shipment if the main ticket shipments get chagned!
      */
     private function updatePortClaimTicket(){
         if(!empty($this->main_ticket)){
             $mainTicket= $this->main_ticket;
             $sids=[];
             if(is_array($mainTicket->shipments)){
                 foreach($mainTicket->shipments as $p){   $sids[]=$p->id;  }
             }
             if(!empty($sids)){
                  CrmMap::createCrmMaps($this->id, new ExParcel(), $sids);
             }
              $crmComp= $this->findComp();
              if(empty($mainTicket->crm_comp->port)){
                  $this->status=self::STATE_CANCEL;
                  $this->update('status');
              }else{
                 if($this->status==self::STATE_CANCEL){
                     $this->status=$mainTicket->status;
                     $this->update('status');
                 }
                 $crmComp->port=$mainTicket->crm_comp->port;
              }
              $crmComp->approve_ammount= $mainTicket->getPortClaimValue();
              $crmComp->mdata['approve_currency']=3;
              $crmComp->mdata['port_claim_area']= $mainTicket->crm_comp->mdata['port_claim_area'];
              $crmComp->save();
         }
     }
     
     public function findSubTicket($type=self::TYPE_PORT_CLAIM){
         $returnTicket=null;
         if(!empty($this->sub_tickets)){
             foreach($this->sub_tickets as $ticket){
                 if($ticket->type==$type){
                     $returnTicket=$ticket;
                 }
             }
         }
//         echo $returnTicket->no;
         return $returnTicket;
     }
     public function getPortClaimValue(){
         $value=0;
         foreach ($this->shipments as $shipment){
             if(isset($this->crm_comp->mdata['port_claim_reason'])){
                $value+=$shipment->getPortClaimValue(intval($this->crm_comp->mdata['port_claim_reason']));
            }
         }
         return $value;
     }
}

