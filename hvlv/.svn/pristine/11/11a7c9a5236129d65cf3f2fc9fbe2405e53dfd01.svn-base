<?php
class CKEditorWidget extends CInputWidget{
	public $version = '4.5.4';
	public $dist = 'standard';

	public $ckBasePath, $defaultValue, $config, $id, $name;

	public function run(){
//		if(!isset($this->ckBasePath)) $this->ckBasePath = '//cdn.ckeditor.com/'.$this->version.'/'.$this->dist.'/';
                  $this->ckBasePath="js/ckeditor/";
		if(!empty($this->model)){
			if(!isset($this->attribute)) throw new CHttpException(500,'"attribute" have to be set!');
			$nid = $this->resolveNameID();
			$this->name = $nid[0];
			$this->id = $nid[1];
			$this->defaultValue = $this->model->{$this->attribute};
		}else{
			if(!isset($this->name)) throw new CHttpException(500,'"name" have to be set!');
		}
		$controller=$this->controller;
		$action=$controller->action;
		
		echo CHtml::textArea($this->name, $this->defaultValue, array('id'=> 'ota-'.$this->id, 
			'rows'=>5, 
			'cols'=>50,
	   ));
		Yii::app()->clientScript->registerScriptFile($this->ckBasePath . 'ckeditor.js');
		Yii::app()->clientScript->registerScript($this->getId(), "
			$(function(){
				var i = 0;
				var si = setInterval(function(){
					if(i++ > 20){
						clearInterval(si);
						return false;
					}
					if(typeof CKEDITOR != 'undefined'){
						var taID = 'ota-".$this->id."';
						var instance = CKEDITOR.instances[taID];
						if (instance) { instance.destroy(true); }
						CKEDITOR.replace(taID, ".json_encode($this->config).");
						clearInterval(si);
						return true;
					}
				}, 100);
			});
		");
	}
}
