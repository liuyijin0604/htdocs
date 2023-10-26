<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ApiConsolAction extends CAction {
	public $ctlr, $debug, $user;

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
		file_put_contents($tmp.'consol_api.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
	}

	public function create(){
		$o = new stdClass;
		if($this->debug){
			$o->status = 1;
			$o->msg = 'Success';
			echo json_encode($o);
			return true;
		}

		$d = $this->ctlr->data;
		if(!empty($d->ref) && ImcoConsol::model()->count('awb = :r', [':r' => $d->ref]) > 0){
			$o->status = 0;
			$o->msg = 'Consol '.$d->ref.' already exists';
			echo json_encode($o);
			return false;
		}else{
			$c = new ImcoConsol;
			$c->awb = $d->ref;
			$c->type = 15;
			$c->dpt_id = 105;
			$c->pol = 'CNSZX';
			$c->pod = 'AUSYD';
			$c->save();
			$err = [];
			if(!empty($d->connotes)){
				foreach($d->connotes as $s){
					$s = ImParcel::model()->find('hbn = :h AND consol_id = 0 AND status < 30', [':h' => $s[0]]);
					if(empty($s)){
						$err[] = 'Shipment '.$s[0].' not found.';
					}else{
						$s->consol_id = $c->id;
						$s->status = 30;
						$s->weight = $s[1];
						$s->mdata['charge'] = $s[2];
						$s->save();
					}
				}
			}
			$o->status = 1;
			$o->no = $c->no;
			$o->msg = empty($err)? 'Success' : implode(', ', $err);
		}
		
		echo json_encode($o);
	}

	public function addParcel(){
		$d = $this->ctlr->data;
		$o = new stdClass;
		$c = ImcoConsol::model()->find('no = :n', [':n' => $d->no]);
		if(empty($c)){
			$o->status = 0;
			$o->msg = 'Consol not found';
		}elseif(!empty($d->connotes)){
			$err = [];
			foreach($d->connotes as $s){
				$s = ImParcel::model()->find('hbn = :h AND consol_id = 0 AND status < 30', [':h' => $s[0]]);
				if(empty($s)){
					$err[] = 'Shipment '.$s[0].' not found.';
				}else{
					$s->consol_id = $c->id;
					$s->status = 30;
					$s->weight = $s[1];
					$s->mdata['charge'] = $s[2];
					$s->save();
				}
			}
			$o->status = 1;
			$o->no = $c->no;
			$o->msg = empty($err)? 'Success' : implode(', ', $err);
		}
		echo json_encode($o);
	}

	public function delParcel(){
		$d = $this->ctlr->data;
		$o = new stdClass;
		$c = ImcoConsol::model()->find('no = :n', [':n' => $d->no]);
		if(empty($c)){
			$o->status = 0;
			$o->msg = 'Consol not found';
		}elseif(!empty($d->connotes)){
			$err = [];
			foreach($d->connotes as $s){
				$s = ImParcel::model()->find('hbn = :h AND consol_id = 0 AND status = 30', [':h' => $s[0]]);
				if(empty($s)){
					$err[] = 'Shipment '.$s[0].' not found.';
				}else{
					$s->consol_id = 0;
					$s->status = 25;
					$s->save();
				}
			}
			$o->status = 1;
			$o->no = $c->no;
			$o->msg = empty($err)? 'Success' : implode(', ', $err);
		}
		echo json_encode($o);
	}

	public function get(){
		$d = $this->ctlr->data;
		$o = new stdClass;
		$c = ImcoConsol::model()->find('no = :n', [':n' => $d->no]);
		if(empty($c)){
			$o->status = 0;
			$o->msg = 'Consol not found';
		}else{
			$o->connotes = [];
			foreach($c->shipments as $s){
				$o->connotes[] = [$s->hbn, $s->weight, empty($s->mdata['charge'])? 0 : $s->mdata['charge']];
			}
			$o->status = 1;
			$o->no = $c->no;
			$o->msg = 'Success';
		}
		echo json_encode($o);
	}
}
