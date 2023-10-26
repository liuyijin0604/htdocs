<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class PController extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/tab';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	
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
}