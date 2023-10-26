<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController{

	public $layout = false;

	public $fullLayout = 'pos';
	
	protected $breadcrumbs=[];

	protected $nonAjax=[];

	protected $skipAcl=[];

	protected $CaName = '';
	
	//set language
	public function init(){
		if(isset($_GET['lang'])){
			Yii::app()->session['lang'] = $_GET['lang'];
		}elseif(empty(Yii::app()->session['lang'])){
			if(Yii::app()->params['settings']['lang']['value'] == 'auto'){//auto detect
				if(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])){
					$lang=$_SERVER["HTTP_ACCEPT_LANGUAGE"];
					$lary=explode(",",$lang);
					$lnp=explode(";",$lary[0]);
					$lang = substr($lnp[0],0,2);
					Yii::app()->session['lang'] = $lang == 'zh'? 'zh_cn' : $lang;
				}else{
					Yii::app()->session['lang'] = 'en';
				}
			}else{
				Yii::app()->session['lang'] = Yii::app()->params['settings']['lang']['value'];
			}
		}
		Yii::app()->language = Yii::app()->session['lang'];
	}

	//common filters
	public function filters() {
		return array('ajaxOnly', 'accessControl');
	}
	
	public function filterAccessControl($filterChain) {
		if(Yii::app()->user->isGuest && !in_array($this->getAction()->getId(), $this->skipAcl)){
			if(Yii::app()->request->isAjaxRequest){
				echo '<script type="text/javascript">posApp.showLogin();</script>';
				Yii::app()->end();
			}
		}
		
		if($this->getAction()->getId() != 'checkSession'){
			Yii::app()->session['last_action'] = time();
		}

		$filterChain->run();
	}
	
	public function filterAjaxOnly($filterChain){
		if(!Yii::app()->request->isAjaxRequest && (empty($this->nonAjax) || !in_array($this->getAction()->getId(), $this->nonAjax))) $this->layout = $this->fullLayout;
		$filterChain->run();
	}

	public function registerJS($js,$id=1){
		Yii::app()->clientScript->registerScript($this->getId().$id, preg_replace('/<script[^>]+>(.+)<\/script>/ms', '\\1',$js), CClientScript::POS_END);
	}

	public function render($v, $p=[], $r=false){
		if(Yii::app()->user->isGuest && !in_array($this->getAction()->getId(), $this->skipAcl)){
			$this->beginContent('/layouts/pos');
			$this->endContent();
		}else{
			return parent::render($v, $p, $r);
		}
	}
	
	public function t($s, $m=array()){
		if(is_array($s)){
			foreach($s as $k=>$a){
				$s[$k] = Yii::t($this->getId(), $a, $m);
			}
			return $s;
		}else{
			return Yii::t($this->getId(), $s, $m);
		}
	}
	
	public function ajaxResult($model, $attr=array(), $msg='', $ej=[]){
		$es = $model->getErrors();
		$r = new stdClass;
		$r->done = false;
		$r->msg = '';
		if(isset($model->isCreate)){
			$r->isCreate = $model->isCreate;
		}
		foreach($attr as $k=>$a){
			if(is_string($k) && !empty($a)) $r->{$k} = $a;
			if(isset($model->{$a})) $r->{$a} = $model->{$a};
		}
		if(empty($es)){
			$r->done = true;
			$r->msg = empty($msg)? $this->t('{model} Saved Successfully', array('{model}' => get_class($model))) : $msg;
		}else{
			foreach($es as $e){
				foreach($e as $el){
					$r->msg .= $el.'<br />';
				}
			}
		}

		if(!empty($ej)){
			foreach($ej as $k=>$v){
				$r->{$k} = $v;
			}
		}
		echo json_encode($r);
		Yii::app()->end();
	}
}