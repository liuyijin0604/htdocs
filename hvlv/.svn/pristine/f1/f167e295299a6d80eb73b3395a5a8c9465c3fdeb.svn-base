<?php
class EFancyBox extends CWidget
{
	public $id;
	public $target;
	public $mouseEnabled=true;
	public $thumbs=false;
	public $deletable=false;
	
	// @ array of config settings for fancybox
	public $config=array();
	
	// function to init the widget
	public function init(){
		// if not informed will generate Yii defaut generated id, since version 1.6
		if(!isset($this->id))
			$this->id=$this->getId();
		if(isset($this->config['helpers']['thumbs']))
			$this->thumbs=true;
		if(isset($this->config['helpers']['deletable']))
			$this->deletable=true;
		// publish the required assets
		$this->publishAssets();
	}
	
	// function to run the widget
    public function run()
    {
		$config = CJavaScript::encode($this->config);
		Yii::app()->clientScript->registerScript($this->getId(), "
			$('$this->target').fancybox($config);
		");
	}
	
	// function to publish and register assets on page 
	public function publishAssets()
	{
		$assets = dirname(__FILE__).'/assets';
		$baseUrl = Yii::app()->assetManager->publish($assets);
		if(is_dir($assets)){
			Yii::app()->clientScript->registerCoreScript('jquery');
			Yii::app()->clientScript->registerScriptFile($baseUrl . '/jquery.fancybox.pack.js', CClientScript::POS_HEAD);
			Yii::app()->clientScript->registerCssFile($baseUrl . '/jquery.fancybox.css');
			// if mouse actions enbled register the js
			if ($this->mouseEnabled) {
				Yii::app()->clientScript->registerScriptFile($baseUrl . '/jquery.mousewheel-3.0.6.pack.js', CClientScript::POS_HEAD);
			}
			if ($this->thumbs) {
				Yii::app()->clientScript->registerScriptFile($baseUrl . '/helpers/jquery.fancybox-thumbs.js', CClientScript::POS_HEAD);
				Yii::app()->clientScript->registerCssFile($baseUrl . '/helpers/jquery.fancybox-thumbs.css');
			}
			if ($this->deletable) {
				Yii::app()->clientScript->registerScriptFile($baseUrl . '/helpers/jquery.fancybox-deletable.js', CClientScript::POS_HEAD);
				Yii::app()->clientScript->registerCssFile($baseUrl . '/helpers/jquery.fancybox-deletable.css');
			}
		} else {
			throw new Exception('EFancyBox - Error: Couldn\'t find assets to publish.');
		}
	}
}