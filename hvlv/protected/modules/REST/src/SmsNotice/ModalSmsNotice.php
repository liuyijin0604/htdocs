<?php

class ModalSmsNotice{
    
    static function Create($strPhone , $strMessage,$cargoProcessId = 0){
        $objModal = new self;
        $objModal->CreateFromPhoneAndMessage($strPhone , $strMessage,$cargoProcessId);
        return $objModal;
    }
    
    private $orm = null;
    
    function CreateFromORM($objORM){
        $this->orm = $objORM;
        
    }
    
    function CreateFromPhoneAndMessage($strPhone , $strMessage,$cargoProcessId = 0){
        $objOrmSmsNotice = new OrmSmsNotice;
        $objOrmSmsNotice[Field::phone] = $strPhone;
        $objOrmSmsNotice[Field::message] = $strMessage;
        $objOrmSmsNotice[Field::is_processed] = false;
        $objOrmSmsNotice[Field::cargo_process_id] = $cargoProcessId;
        $objOrmSmsNotice->save();
        $this->orm = $objOrmSmsNotice;
    }
    
    public function funcOrm2StdClass(){
        $array = [];
        $array[Field::id] =  $this->orm[Field::id];
        $array[Field::phone] =  $this->orm[Field::phone];
        $array[Field::message] =  $this->orm[Field::message];
        // $array[Field::is_processed] =   $this->orm[Field::is_processed];

        $obj = new stdClass;
        foreach($array as $key=>$value){
            $obj->$key=$value;
        }
        
        return $obj;
    }
    
    public function funcSetIsProceddedTrue(){
        $this->orm[Field::is_processed]=true;
        $this->orm->save();
    }
    
    
}

