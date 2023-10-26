<?php

require_once 'protected\modules\REST\src\SplitDelivery\OrmSplitDelivery.php';

class ModelSplitDelivery
{
    function CreateFromStdClass($objStdClass){
        $this->funcSaveORM($objStdClass);
        $this->funcGetOrCreateJob();
        $this->funcCreateTask();
        $this->funcSetPackTask();
        $this->funcSetDeliveryTask();
        $this->funcDeliveryTaskToShipment();
    }
    
    function CreateFromORM($objORM){
        $this->orm = $objORM;
        
        $this->funcGetOrCreateJob();
        $this->funcGetTask();
        // $this->funcSetPackTask();
        // $this->funcSetDeliveryTask();
        // $this->funcDeliveryTaskToShipment();
    }
    
    
    var $orm = null;
    var $job = null;
    var $objTaskMain = null;
    var $objTaskPack = null;
    var $objTaskDelivery = null;
    
    var $objModalDeliveryTaskToShipment = null;
    
    
    
    function __construct() {
        
    }
    
    function getORM(){
        return $this->orm;
    }
    
    function setORM($objORM){
        $this->orm = $objORM;
    }
    
    function funcCreateFromStdClass($objStdClass){
        $this->funcSaveORM($objStdClass);
        $this->funcGetOrCreateJob();
        $this->funcCreateTask();
        
        
        
    }
    
    function funcSaveORM($objStdClass){
        $array = [];
        foreach($objStdClass as $k=>$v){
            $array[$k] = $v;
        }
        
        $objSplitDelivery = new OrmSplitDelivery;
        
        $objSplitDelivery[Str::main_number] = $array[Str::main_number];
        $objSplitDelivery[Str::sub_number] =  $array[Str::sub_number];
        $objSplitDelivery[Str::weight] = $array[Str::weight];
        $objSplitDelivery[Str::actual_volume] = $array[Str::actual_volume];
        $objSplitDelivery[Str::quantity] = $array[Str::quantity];
        $objSplitDelivery[Str::address] = $array[Str::address];
        $objSplitDelivery[Str::suburb] = $array[Str::suburb];
        $objSplitDelivery[Str::postcode] = $array[Str::postcode];
        $objSplitDelivery[Str::state] = $array[Str::state];
        $objSplitDelivery[Str::contact] = $array[Str::contact];
        $objSplitDelivery[Str::phone] = $array[Str::phone];
        $objSplitDelivery[Str::create_time] = date("Y-m-d h:i:s");
        
        $objSplitDelivery[Str::org_id] = $array[Str::org_id];
        $objSplitDelivery[Str::depot_id] = $array[Str::depot_id];
        $objSplitDelivery[Str::courier_id] = $array[Str::courier_id];
        
        
        $objSplitDelivery->save();
        
        $this->orm = $objSplitDelivery;
    }
    
    
    public function funcOrm2StdClass(){
        $array = [];
        $array[Str::main_number] =  $this->orm[Str::main_number];
        $array[Str::main_number] =  $this->orm[Str::main_number];
        $array[Str::sub_number] =   $this->orm[Str::sub_number];
        $array[Str::weight] =  $this->orm[Str::weight];
        $array[Str::actual_volume] =  $this->orm[Str::actual_volume];
        $array[Str::quantity] =  $this->orm[Str::quantity];
        $array[Str::address] =  $this->orm[Str::address];
        $array[Str::suburb] =  $this->orm[Str::suburb];
        $array[Str::postcode] =  $this->orm[Str::postcode];
        $array[Str::state] =  $this->orm[Str::state];
        $array[Str::contact] =  $this->orm[Str::contact];
        $array[Str::phone] =  $this->orm[Str::phone];
        $array[Str::create_time] = $this->orm[Str::create_time];
        
        $array[Str::print_time] = $this->orm->JsonData[Str::print_time];
        
        $obj = new stdClass;
        foreach($array as $key=>$value){
            $obj->$key=$value;
        }
        
        return $obj;
    }
    
    
    function funcGetOrCreateJob(){
        $strMainNumber = $this->orm[Str::main_number];
        
        $objWmsJob = new WmsJob;
        
        // $strSql = 'select * from '.$objWmsJob->tableName().' where ref = "'.$strMainNumber.'"';
        // $objResult = WmsJob::model()->findBySql($strSql);
        
        $objResult = WmsJob::model()->find('ref = :ref',[':ref'=>$strMainNumber]);
        
        if($objResult !== null){
            $objWmsJob = $objResult;
        }
        else{ //create
            $objWmsJob->ref = $strMainNumber;
            $objWmsJob->org_id = $this->orm->org_id;
            $objWmsJob->type = WmsJob::TYPE_OUTWARD;
            $objWmsJob->status = WmsJob::STATUS_NEW;
            
            $objWmsJob->save();
            
        }
        
        $this->job = $objWmsJob;
    }
    
    function funcCreateTask(){

        $strJobId = $this->job->id;
        $strRef = $this->orm[Str::sub_number];
        
        $objTaskMain = new WmsTask;
        $objTaskMain->ref= $strRef;
        $objTaskMain->job_id = $strJobId;
        $objTaskMain->type = WmsTask::TYPE_PICK_UNIT;
        $objTaskMain->status = WmsTask::STATUS_NEW;
        $objTaskMain->is_request = 1;
        $objTaskMain->dpt_id = $this->orm->depot_id;;
        $objTaskMain->save();

        $this->orm->JsonData[Str::task_id] = $objTaskMain->id;
        $this->orm->save();

        $objSubTasks = $objTaskMain->subTasks;
        foreach($objSubTasks as $i=>$objSubTask){
            if($objSubTask->type == WmsTask::TYPE_PACK_ORDER){
                $this->objTaskPack = $objSubTask;
            }
            else if($objSubTask->type == WmsTask::TYPE_PACK_UP){
                $this->objTaskDelivery = $objSubTask;
            }
        }
        
    }
    
    function funcGetTask(){
        // $strSubNumber = $this->orm[Str::sub_number];
        // $objWmsTask = new WmsTask();
        
        // $strSql = 'select * from '.$objWmsTask->tableName().' where ref = "'.$strSubNumber.'"';
        // $result = WmsTask::model()->findAllBySql($strSql);
        
        // if(count($result) !== 1){
        //     throw new Exception(count($result).' task of '.$strSubNumber);
        // }
        
        // $this->objTaskMain = $result[0];
        
        $this->objTaskMain =WmsTask::model()->findByPk($this->orm->JsonData[Str::task_id]);
        
        $objSubTasks = $this->objTaskMain->subTasks;
        foreach($objSubTasks as $i=>$objSubTask){
            if($objSubTask->type == WmsTask::TYPE_PACK_ORDER){
                $this->objTaskPack = $objSubTask;
            }
            else if($objSubTask->type == WmsTask::TYPE_DELIVERY){
                $this->objTaskDelivery = $objSubTask;
            }
        }
        
    }
    
    function funcSetPackTask(){
        $pkg[] = array('wt' => (string)$this->orm[Str::weight], 'w' => '', 'h' => '', 'd' => '', 'nt' => '' );
        $this->objTaskPack->mdata['pkg'] = json_encode($pkg);
        $this->objTaskPack->save();
    }
    
    function funcSetDeliveryTask(){
        $this->objTaskDelivery->type = WmsTask::TYPE_DELIVERY;

        $this->objTaskDelivery->mdata['cnee']['company']="";
        $this->objTaskDelivery->mdata['cnee']['name']=(string)$this->orm[Str::contact];
        $this->objTaskDelivery->mdata['cnee']['tel']=(string)$this->orm[Str::phone];
        $this->objTaskDelivery->mdata['cnee']['address']=(string)$this->orm[Str::address];
        $this->objTaskDelivery->mdata['cnee']['suburb']=(string)$this->orm[Str::suburb];
        $this->objTaskDelivery->mdata['cnee']['city']=(string)$this->orm[Str::suburb];
        $this->objTaskDelivery->mdata['cnee']['state']=(string)$this->orm[Str::state];
        $this->objTaskDelivery->mdata['cnee']['postcode']=(string)$this->orm[Str::postcode];
        $this->objTaskDelivery->mdata['cnee']['country']="AU";
        $this->objTaskDelivery->mdata['cnee']['email']="";
        
        $this->objTaskDelivery->mdata['courier'] = (string)$this->orm[Str::courier_id];
        
        
        $this->objTaskDelivery->save();
        
    }
    
    function funcDeliveryTaskToShipment(){
        $this->objTaskDelivery->toShipment();
    }
    
    function funcScanPrint(){
        foreach ($this->objTaskDelivery->mdata['shipment_id'] as $sid) {
            $p = Shipment::model()->find('id=:id', array(':id' => $sid));
            if (!empty($p)) {
                $rs[] = $p;
            }

        }
        
        oPDF::renderPDF('label_A6', array('rs' => $rs), 1, 'label.pdf');
        
    }
    
    function funcSetPrintTimeNow(){
        $this->orm->JsonData[Str::print_time] =  date("Y-m-d h:i:s");
        $this->orm->save();
    }
    
    function funcSetTaskComplete(){
        $this->objTaskMain->status = WmsTask::STATUS_COMPLETED;
        $this->objTaskMain->save();
    }
    
    
}
