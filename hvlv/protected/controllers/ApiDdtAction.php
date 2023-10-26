<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ApiDdtAction extends CAction {
	public $ctlr, $debug;

	public function run() {
		$this->ctlr = $this->getController();
		$this->debug = !empty($_POST['test']);

		if(!empty($_POST['method']) && method_exists($this, $_POST['method'])){
			$this->{$_POST['method']}();
		}else{
			throw new CHttpException(400, 'API method not found!');
		}
	}

	public function order(){
		if($this->debug){
			$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
			file_put_contents($tmp.'ddt_api.log', date('Y-m-d H:i:s').' '.json_encode($_POST)."\n", FILE_APPEND);
		}
		$o = new StdClass;
		$o->status = 1;
		$o->msg = 'Success';
		echo json_encode($o);
	}
}
