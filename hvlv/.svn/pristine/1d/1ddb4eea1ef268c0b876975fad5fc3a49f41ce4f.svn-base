<?php

class ModalRealTimeSmsNotice{
    
    const id = 'id';
    const phone = 'phone';
    const message = 'message';
    const type = 'type';

    static function Create($strPhone , $strMessage){
        $objModal = new self;
        $objModal->CreateFromPhoneAndMessage($strPhone , $strMessage);
        return $objModal;
    }
    
    private $orm = null;
    
    function CreateFromORM($objORM){
        $this->orm = $objORM;
        
    }
    
    function CreateFromPhoneAndMessage($strPhone , $strMessage){
        $objOrmSmsNotice = new OrmRealTimeSmsNotice;
        $objOrmSmsNotice[Field::phone] = $strPhone;
        $objOrmSmsNotice[Field::message] = $strMessage;
        $objOrmSmsNotice[Field::type] = false;
        $objOrmSmsNotice->save();
        $this->orm = $objOrmSmsNotice;
    }
    
    public function funcOrm2StdClass(){
        $array = [];
        $array[Field::id] =  $this->orm[Field::id];
        $array[Field::phone] =  $this->orm[Field::phone];
        $array[Field::message] =  $this->orm[Field::message];
        $obj = new stdClass;
        foreach($array as $key=>$value){
            $obj->$key=$value;
        }
        
        return $obj;
    }
    
    public function funcSetIsProceddedTrue(){
        $this->orm[Field::type]=true;
        $this->orm->save();
    }
    
}
    class Field{
        const id = 'id';
        const phone = 'phone';
        const message = 'message';
        const type = 'type';    
    }
    

