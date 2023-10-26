<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class CHelpAction extends CViewAction{
	
	public $basePath='help';
	
	public function run(){
		parent::run();
	}
	
	public function resolveView($viewPath){
		if(Yii::app()->language == 'zh_cn')	$this->basePath = 'help_zh';
		$viewPath = preg_replace('/\\'.Yii::app()->components['urlManager']->urlSuffix.'$/', '', $viewPath);
		$viewPath = str_replace('/', '.', $viewPath);
		if(preg_match('/^\w[\w\.\-]*$/',$viewPath)){
			$view=strtr($viewPath,'.','/');
			if(!empty($this->basePath))
				$view=$this->basePath.'/'.$view;
			if($this->getController()->getViewFile($view)!==false){
				$this->view=$view;
			}else{
				$this->view=$this->basePath.'/'.$this->defaultView;
			}
			return;
		}
		throw new CHttpException(404,Yii::t('yii','The requested view "{name}" was not found.',
			array('{name}'=>$viewPath)));
	}
}