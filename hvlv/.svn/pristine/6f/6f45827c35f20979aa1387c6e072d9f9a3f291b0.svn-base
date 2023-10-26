<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ApiProductAction extends CAction {
	public $ctlr, $debug, $user;
	public static $currencies = array(
		'AUD' => '1',
		'USD' => '2',
		'RMB' => '3',
	);

	public function run() {
		$this->ctlr = $this->getController();
		$this->debug = !empty($_POST['test']);
		$this->user = empty($this->ctlr->user)? false : User::model()->findByPk($this->ctlr->user);

		if(!empty($_POST['method']) && method_exists($this, $_POST['method'])){
			$this->log(json_encode($_POST));
			$this->{$_POST['method']}();
		}else{
			throw new CHttpException(400, 'API method not found!');
		}
	}

	public function log($l){
		$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
		file_put_contents($tmp.'product_api.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
	}

	public function create(){
		$d = $this->ctlr->data;
		$o = new stdClass;
		$o->status = 1;
		$p = ExProdb::model()->find('sku = :sku', [':sku' => $d->sku]);
		if(empty($p)){
			$o->message = 'New product, need verification.';
			$p = new ExProdb;
		}else{
			$pp = ExProdbPrice::model()->find('pid = :pid', [':pid' => $p->id]);
			if(empty($pp)) $pp = new ExProdPrice;
			$pp->pid = $p->id;
			$pp->code = $d->code;
			$pp->name = $d->name_zh;
		}
		echo json_encode($o);
	}

	public function get(){
		$d = $this->ctlr->data;
		$o = new stdClass;
		
		echo json_encode($o);
	}

	public function update(){
		$d = $this->ctlr->data;
		$o = new stdClass;

		
		echo json_encode($o);
	}

}
