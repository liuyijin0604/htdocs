<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController{

	public $layout = false;

	public $fullLayout = 'wms';
	
	protected $breadcrumbs=[];

	protected $nonAjax=[];

	protected $skipAcl=[];

	protected $CaName = '';
	
	//set language
	public function init(){

        // check to see if user login session expired
        // if expired just go to login page directly
        if ( Yii::app()->user->isGuest && $this->getId() !== 'site' ) {
            $this->redirect('/site/index');
        }

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
		Yii::app()->clientScript->registerScript($this->getId().$id, preg_replace('/<script[^>]+>(.+)<\/script>/ms', '\\1',$js));
	}

	public function render($v, $p=[], $r=false){
		if(Yii::app()->user->isGuest && !in_array($this->getAction()->getId(), $this->skipAcl)){
			$this->beginContent('/layouts/wms');
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

    public function tarray($as){
        $r = array();
        foreach($as as $i=>$a){
            $r[$i] = $this->t($a);
        }
        return $r;
    }

    public function hasRole($a, $admin=true){
        $auth = false;
        $a = explode(',', $a);
        if($admin) $a[] = 'Admin';
        if(!empty($a)){
            foreach($a as $r){
                if($auth = $this->checkRole($r)) break;
            }
        }
        return $auth;
    }

    protected function checkRole($r){
        if(!isset(Yii::app()->user->grp)) return false;
        switch($r){
            case 'Admin':
                $auth = Yii::app()->user->grp == 0;
                break;
            case 'Manager':
                $auth = Yii::app()->user->grp == 10;
                break;
            case 'Broker':
                $auth = Yii::app()->user->grp == 20;
                break;
            case 'Operator':
                $auth = Yii::app()->user->grp == 30;
                break;
            case 'Warehouse':
                $auth = Yii::app()->user->grp == 40;
                break;
            case 'AgentManager':
                $auth = Yii::app()->user->grp == 50;
                break;
            case 'AgentOperator':
                $auth = Yii::app()->user->grp == 55;
                break;
            case 'Client':
                $auth = Yii::app()->user->grp == 60;
                break;
            case 'Sub Client':
                $auth = Yii::app()->user->grp == 70;
                break;
            case 'AuAll':
                $auth = in_array(Yii::app()->user->grp, array(10,20,30));
                break;
            default:
                $auth = false;
                break;
        }
        return $auth;
    }

	public function ajaxResult($model, $attr=array(), $msg=''){
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
		echo json_encode($r);
		Yii::app()->end();
	}
}