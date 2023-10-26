<?php

/**
 * This is the model class for table "crm".
 *
 * The followings are the available columns in table 'crm':
 * @property string $id
 * @property String $no The ticket no;
 * @property integer $operator_id
 * @property string $hbn
 * @property integer $open_by
 * @property integer $assign_to
 * @property integer $link_id
 * @property String email
 * @property String  telephone
 * @property integer $main_type import/export;
 * @property integer type   //like id not uploading /genral query.
 * @property string $create_time
 * @property string $close_time
 * @property string $last_update_time
 * @property integer $status
 * @property integer $bwf
 * @property string $meta;
 */
class Crm extends CActiveRecord
{

    public $nolog=false;
    public $custom_log_note;
    public $tids,$statuses,$shipnos,$opener,$currenter,$closer,$assigner,$agent,$customer_name,$sender_name,$consol_no,$flag,$bwfs,$comp_port,$the_main_ticket;
    public static  $main_types=array(
        10=>'ImCrm',
        20=>'ExCrm',
    );
    /*   'cf'=>'claim form'
     * 
     */
    public static $crm_form_types=array(
        'cf',
    );
    public static $the_bwfs=array(
        2=>'Finance',
        4=>'Finance Complete',
    );
    public static $my_type = 0;
    public $mdata;
    public static $states = array(
        0  => 'pending',
        10 => 'Op Processing',
        20 =>  'Manager Processing',
        30 => 'Finance Processing',
        90 =>'close',
        100=>'Delete',
    );
   public function tableName()
	{
		return 'crm';
	}

	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('main_type,open_by,type,source', 'required'),
			array('operator_id,status,type,level,source,closed_by,bwf,link_id', 'numerical', 'integerOnly'=>true),
			array('create_time, main_type,open_by,close_time,due,assign_to,closed_by,link_id,email,telephone,meta', 'safe'),
			array('id, operator_id,main_type,open_by,opener,currenter,customer_name,bwf,link_id,comp_port,sender_name,the_main_ticket,consol_no,bwfs,agent,flag,closer,assigner,shipnos,assign_to,last_update_time,meta,tids,statuses,no,email,telephone,main_type,create_time,close_time, status,closed_by,type,level,source,due', 'safe', 'on'=>'search'),
		);
	}
        public function __construct($scenario='insert'){
            parent::__construct($scenario);
            if(static::$my_type>0) $this->main_type= static::$my_type;
        }
        protected function instantiate($attr){
		$cls = isset(self::$main_types[$attr['main_type']])? self::$main_types[$attr['main_type']] : get_class($this);
		return new $cls(null);
	}
        public function defaultScope(){
            return empty(static::$my_type) ? [] : ['condition' => $this->dbConnection->quoteColumnName($this->getTableAlias(false,false).'.main_type').'='.static::$my_type];
        }
        public function unsetAttributes($name=NULL){
            parent::unsetAttributes($name=NULL);
            if(static::$my_type > 0) {
                $this->main_type= static::$my_type;
            }
        }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
                    'user' => array(self::BELONGS_TO, 'User', 'operator_id'),
                    'open_user' => array(self::BELONGS_TO, 'User', 'open_by'),
                    'assign_user' => array(self::BELONGS_TO, 'User', 'assign_to'),
                    'close_user' => array(self::BELONGS_TO, 'User', 'close_by'),
                    'shipments'=> array(self::MANY_MANY,'Shipment', 'crm_map(crm_id, fid)'),
                    'main_ticket'=>array(self::BELONGS_TO,'Crm','link_id'),
                    'sub_tickets'=>array(self::HAS_MANY,'Crm','link_id'),
                    'notes' =>array(self::HAS_MANY,'CrmLog','crm_id'),   
                    'crm_comp'=>array(self::HAS_ONE,'CrmComp','crm_id'),
		);
	}

	/**
	 * @return array customized attr0ibute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Ticket No.',
			'operator_id' => 'Operator',
			'create_time' => 'Create Time',
			'close_time' => 'Close Time',
                        'email'=>'Email',
                        'link_id'=>'Link Id',
                        'telephone'=>'Telephone',
			'status' => 'Status',
                        'type' => 'Type',
                        'level' => 'Level',
                        'source' => 'Source',
                        'note' => 'recent note'
		);
	}
	
    /**
     *  list all crm items based on pagination
     * @param bool $showMyTickets
     * @return CActiveDataProvider
     */
    public function search() {
        $criteria = new CDbCriteria;
        $with = array();

        if(!empty($this->currenter)){
            $with[]='user';
            $criteria->compare("CONCAT(user.fname,'',user.lname)",$this->currenter,true);
        }
        if(!empty($this->opener)){
           $with[]='open_user';
           $criteria->compare("CONCAT(open_user.fname,'',open_user.lname)",$this->opener,true); 
        }
        if(!empty($this->assigner)){
            $with[]='assign_user';
            $criteria->compare("CONCAT(assign_user.fname,'',assign_user.lname)",$this->assigner,true); 
        }
        if(!empty($this->agent)){
           $sql='SELECT t.id FROM `crm` t INNER JOIN `crm_map` s ON t.id=s.crm_id INNER JOIN `shipment` m  ON s.fid=m.id INNER JOIN `org` o ON m.agent_id=o.id WHERE o.id = :agent OR o.name Like  :agent_name';
           $rs=Yii::app()->db->createCommand($sql)->bindValues([':agent'=>$this->agent,':agent_name'=>"%".$this->agent."%"])->queryAll();
           $ids=[];
           foreach ($rs as $r){
           $ids[]=$r['id'];   
           }
           $criteria->addInCondition('t.id',$ids);
        }
        if(!empty($this->customer_name)){
            $sql='SELECT t.id FROM  `crm` t INNER JOIN `crm_map` s ON t.id=s.crm_id INNER JOIN `shipment` m  ON s.fid=m.id INNER JOIN `addr` a ON m.cnee_id=a.id WHERE a.name  Like :name';
            $rs=Yii::app()->db->createCommand($sql)->bindValues([':name'=>"%".$this->customer_name."%"])->queryAll();
            $ids=[];
            foreach ($rs as $r){
              $ids[]=$r['id'];   
            }
           $criteria->addInCondition('t.id',$ids);
         }
         if(!empty($this->sender_name)){
            $sql='SELECT t.id FROM  `crm` t INNER JOIN `crm_map` s ON t.id=s.crm_id INNER JOIN `shipment` m  ON s.fid=m.id INNER JOIN `addr` a ON m.cnor_id=a.id WHERE a.name  Like :name';
            $rs=Yii::app()->db->createCommand($sql)->bindValues([':name'=>"%".$this->sender_name."%"])->queryAll();
            $ids=[];
            foreach ($rs as $r){
              $ids[]=$r['id'];   
            }
           $criteria->addInCondition('t.id',$ids);
         }
         if(!empty($this->consol_no)){
            $sql='SELECT t.id FROM  `crm` t INNER JOIN `crm_map` s ON t.id=s.crm_id INNER JOIN `shipment` m  ON s.fid=m.id INNER JOIN `consol` c ON m.consol_id=c.id WHERE c.no  Like :consol_no';
            $rs=Yii::app()->db->createCommand($sql)->bindValues([':consol_no'=>"%".$this->consol_no."%"])->queryAll();
            $ids=[];
            foreach ($rs as $r){
              $ids[]=$r['id'];   
            }
           $criteria->addInCondition('t.id',$ids); 
         }
         if(!empty($this->flag)){
             $with[]='crm_comp';
            $criteria->addCondition('crm_comp.flag & ' . $this->flag . ' > 0');
         }
         if(!empty($this->bwfs)){
              $criteria->addCondition('t.bwf & ' . $this->bwfs . ' > 0');
         }    
         if(!empty($this->comp_port)){
             $with[]='crm_comp';
             $criteria->compare('crm_comp.port', $this->comp_port);
         }
         if(!empty($this->the_main_ticket)){
             $with[]='main_ticket';
             $criteria->compare('main_ticket.no', $this->the_main_ticket,true);
         }
        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.assign_to', $this->assign_to);
        $criteria->compare('t.type', $this->type);
        $criteria->compare('t.link_id', $this->link_id);
        $criteria->compare('t.no', $this->no, true);
        $criteria->compare('t.open_by', $this->open_by);
        $criteria->compare('t.telephone', $this->telephone, true);
        $criteria->compare('t.email', $this->email, true);
        $criteria->compare('t.level', $this->level);
        $criteria->compare('t.status', $this->status);
        if(empty($this->status)){
                $criteria->addCondition('t.status != 100 AND t.status!=90');
         }
        $criteria->compare('t.last_update_time', $this->last_update_time, true);
        $criteria->compare('t.create_time', $this->create_time, true);
        $criteria->compare('t.source', $this->source);
        if (!empty($this->tids)) {
            $criteria->addInCondition('t.type', $this->tids);
        }
        if (!empty($this->shipnos)) {
            $sql='SELECT t.id FROM  `crm` t INNER JOIN `crm_map` s ON t.id=s.crm_id INNER JOIN `shipment` m  ON s.fid=m.id WHERE (m.hbn  Like :hbn OR ref like :hbn) ORDER BY id DESC';
            $rs=Yii::app()->db->createCommand($sql)->bindValues([':hbn'=>"%".$this->shipnos."%"])->queryAll();
              $ids=[];
            foreach ($rs as $r){
              $ids[]=$r['id'];   
            }
           $criteria->addInCondition('t.id',$ids);
        }
        if (!empty($this->statuses)) {
            $criteria->addInCondition('t.status', $this->statuses);
        }
      if ( !empty($with) ) {
            $criteria->with = array_unique($with);
            $criteria->together = true;
        }

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder'=>'t.status ASC,t.id DESC',
            ),
            'pagination'=>array(
                'pageSize'=>'30',
            ),
        ));
    }

    public function getUser(){
        if( empty($this->operator_id) || !isset($this->user) ) {
            return 'System';
        }else{
            return $this->user->getFullName();
        }
    }
      public function getAssignUser(){
        if( empty($this->assign_to) || !isset($this->assign_user) ) {
            return 'System';
        }else{
            return $this->assign_user->getFullName();
        }
    }
     public function getOpenUser(){
        if( empty($this->open_by) || !isset($this->open_user) ) {
            return 'System';
        }else{
            return $this->open_user->getFullName();
        }
    }
    public function getAgentName(){
        if(!empty($this->shipments)){
            $agents=[];
            $name='';
            foreach ($this->shipments as $shipment){
                if(isset($shipment->agent))
                    $agents[$shipment->agent->id]=$shipment->agent;
              }
              foreach($agents as $agt){
                  $name.=(empty($name)?'':';').$agt->name;
              }
              return $name;
        }else{
            return '';
        }
    }
    public function getCustomerName() {
        if (!empty($this->shipments)) {
            $cnee_names = [];
            $name = '';
            foreach ($this->shipments as $shipment) {
                if (isset($shipment->cnee))
                    $cnee_names[$shipment->cnee->name] = $shipment->cnee->name;
            }
            foreach ($cnee_names as $cname) {
                $name .= (empty($name) ? '' : ';') . $cname;
            }
            return $name;
        } else {
            if(!empty($this->mdata['cust_name'])){
                return $this->mdata['cust_name'];
            }
            return '';
        }
    }
    public  function getShipperName(){
         if (!empty($this->shipments)) {
            $cnor_names = [];
            $name = '';
            foreach ($this->shipments as $shipment) {
                if (isset($shipment->cnor))
                    $cnor_names[$shipment->cnor->name] = $shipment->cnor->name;
            }
            foreach ($cnor_names as $cname) {
                $name .= (empty($name) ? '' : ';') . $cname;
            }
            return $name;
        } else {
            return '';
        } 
    }

    public function updateCurrentUser(){
        if(!empty(Yii::app()->user->id)){
            $this->operator_id=Yii::app()->user->id;
            $this->update('operator_id');
        }
        if($this->status==0){
            $this->status=10;
        }
        $this->last_update_time=date('Y-m-d H:i:s');
        $this->update(['last_update_time','status']);
    }
    public function getShipNos($l=0){
        $nos = [];
	foreach($this->shipments as $i=>$ship){
	   if($l > 0 && $i+1 > $l) break;
           $nos[]='<a href="'.Yii::app()->createURL( $ship->type==10?"imParcel/update":"exParcel/update",array("id"=>$ship->id)).'" class="tab_link" title="'.$ship->hbn.'">'.$ship->hbn.'</a>';
	 
	}
               return implode(',', $nos).($l > 0 && sizeof($this->shipments) > $l? '..('.sizeof($this->shipments).')' : '');
       }
       public function  getshipmentNo(){
           $nos=[];
           foreach($this->shipments as $i=>$ship){
               $nos[]=$ship->hbn;
           }
           return implode(';', $nos);
       }

              public function getConsolNos($l=0){
          $nos = [];
	  foreach($this->shipments as $i=>$ship){
            if(empty($ship->consol)) break;
	    if($l > 0 && $i+1 > $l) break;
           $nos[]='<a href="'.Yii::app()->createURL( $ship->consol->type==15?"ImcoConsol/update":"ExcoConsol/update",array("id"=>$ship->consol->id)).'" class="tab_link" title="'.$ship->consol->no.'">'.$ship->consol->no.'</a>';
	 
	}
               return implode(',', $nos).($l > 0 && sizeof($this->shipments) > $l? '..('.sizeof($this->shipments).')' : '');
       }

    /**
     * get all ticket types
     * const 1 means CRM type parent id in oList table
     * @return array
     */
    public function getTypes(){
        $crm_type_model = oList::model()->subList(1);
        $types = array();
        foreach ($crm_type_model as $type ) {
            $types[$type->id] = $type->item;
        }
        return $types;

    }

    /**
     * get all ticket levels
     * const 5 means CRM level parent id  in oList table
     * @return array
     */
    public function getLevels(){

        $crm_level_model = oList::model()->subList(5);
        $levels = array();
        foreach ($crm_level_model as $level ) {
            $levels[$level->id] = $level->item;
        }
        return $levels;
    }

    /**
     * get all ticket sources where message come from
     * const 6 means CRM level parent id  in oList table
     * @return array
     */
    public function getSources(){

        $crm_source_model = oList::model()->subList(15);
        $sources = array();
        foreach ($crm_source_model as $source ) {
            $sources[$source->id] = $source->item;
        }
        return $sources;
    }



    public function getLevel(){
        if(empty($this->level) || !isset($this->crm_level) ){
            return '普通';
        }else{
            return $this->crm_level->item;
        }
    }

    /**
     * get last activity for current ticket
     */
    public function getLastNote(){
        $crm_log = new CrmLog();
        $rt = $crm_log->find('crm_id = :n order by id desc',array(':n' => $this->id));
        if ( isset($rt) ) {
            return $rt['note'];
        } else {
            return '';
        }
    }

  
       
    public function getStatus(){

        if  ( $this->status == 90) {
            // ticket still open
            return '<span class="ticket-close">' . self::$states[$this->status] . '</span>';
        } else {
            return '<span class="ticket-open">' . self::$states[$this->status] . '</span>';
        }

    }


    public function beforeSave() {
        if ($this->isNewRecord&&empty($this->create_time)){
            $this->create_time = date('Y-m-d H:i:s');
        }
        if(!empty($this->mdata)) $this->meta= json_encode($this->mdata);
            return parent::beforeSave();
    }
    
    public function afterFind(){
        if(!empty($this->meta))
            $this->mdata= json_decode ($this->meta,true);
    }

    
     public function afterSave(){
		if(!$this->nolog && !empty($this)){
			$extra = empty($this->custom_log_note)? array() : array('note' => $this->custom_log_note);
			Log::add($this, $this->isNewRecord? 3 : 4, array_merge(array('status' => $this->getStatus()), $extra));
		}
     }
     
     public function trackingInfo(){
         
         if(empty($this->notes)) return false;
         $o=new stdClass();
         $o->tracks=[];
         foreach($this->notes as $track){
             $o->tracks[]=array($track->time,$track->note,$track->getProcessMethod());
            
         }
         
         return $o;
        
     }

     /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Crm the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
