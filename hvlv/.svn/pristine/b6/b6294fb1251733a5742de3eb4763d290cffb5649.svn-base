<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ApiRcptAction extends CAction {
	public $ctlr, $debug, $user, $tmp;

	public function run() {
		$this->ctlr = $this->getController();
		$this->debug = !empty($_POST['test']);
		$this->user = empty($this->ctlr->user)? false : User::model()->findByPk($this->ctlr->user);
		$this->tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;

		//$this->log(json_encode($_POST));
		if(!empty($_POST['method']) && method_exists($this, $_POST['method'])){
			$this->{$_POST['method']}();
		}else{
			$this->upload();
		}
	}

	public function log($l){
		$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR;
		file_put_contents($tmp.'rcpt_api.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
	}

	public function create(){
		$d = $this->ctlr->data;
		$ts = ['coles', 'woolworth', 'cw', 'misc', 'costco'];

		if(empty($d->tpl)) $d->tpl = $ts[rand(0,4)];

		$rd['date'] = empty($d->date)? date('Y-m-d', strtotime('-'.rand(30,5).' day')).' '.rand(7,22).':'.rand(10,60) :$d->date;
		$rd['bg'] = rand(1,5);
		$rd['sid'] = rand(0,4);
		$rd['no'] = rand(1000000,9999999);
		$rd['mn'] = rand(1000000,9999999);
		$rd['ro'] = sprintf('%0.2f', rand()/getrandmax()*2-1);

		if(!isset($d->photo)) $d->photo = false;
		$model = new ExParcel;
		$model->consol = new ExcoConsol;
		$model->consol->exrate = 1;
		foreach($d->items as $i => $p){
			$model->eitems['pid'][$i] = 0;
			$model->eitems['gen'][$i] = $p->name;
			$model->eitems['g'][$i] = $p->name;
			$model->eitems['q'][$i] = $p->qty;
			$model->eitems['t'][$i] = $p->price;
		}

		$td = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime";
		if($d->photo){
			$tf = tempnam($td, "rcpt");
			oPDF::renderImage('receipt_'.$d->tpl, array('r' => $model, 'd' => $rd), 2, $tf);
			RcptMaker::makeReal($tf, $tf, true);
			unlink($tf);
		}else{
			oPDF::renderImage('receipt_'.$d->tpl, array('r' => $model, 'd' => $rd));
		}
	}
}
