<?php

class ReceivalController extends Controller{

	protected $nonAjax = array('report');

	public function actionScan(){
		if(isset($_POST['barcode'])){
			switch($_GET['tab']){
				case 'export':
					$r = $this->exportScan($_POST['barcode']);
				break;
			}
			echo json_encode($r);
			Yii::app()->end();
		}
		
		if(isset($_GET['tab'])){
			$this->render('tab_'.$_GET['tab'], array('tab' => $_GET['tab']));
		}else{
			$this->render('scan');
		}
	}

	protected function exportScan($hbn){
		$r = new StdClass;
		$p = ExParcel::model()->find('hbn = :n', array(':n' => $hbn));
		$alloc = true;
		$savep = false;

		if(empty($p)){
			$p = new ExParcel('create');
			$p->hbn = $hbn;
			$p->status = 14;
			$cnor = new Addr;
			$cnor->country = 'Australia';
			$cnor->save();
			$p->cnor_id = $cnor->id;
			$cnee = new Addr;
			$cnee->country = 'PR China';
			$cnee->save();
			$p->cnee_id = $cnee->id;
			$savep = true;
			$r->msg = 'New Item: '.$hbn;
			$r->color = '#c00';
			$p->nolog = true;
			$p->save();
			$p->nolog = false;
		}else{
			$r->msg = "Found Shipment: ".$hbn;
			$r->color = '#0c0';

			if(in_array($p->status, [8,9,10,12])){
				$p->status = empty($p->cnee->name)? 14 : 15;
				$savep = true;
			}else{
				$savep = false;
			}

			if($p->status >= 60 && $p->status < 101){
				$r->msg .= ', duplicate consignment';
				$r->sound = 'warning-duplicate.mp3';
				$alloc = false;
			}else{
				$sl = StorageLog::findItem($p);
				if($sl){
					if($sl->storage->wid != $_POST['wid'] || $sl->sid == 130){// moving warehouse
						$sl->out();
					}else{
						$r->msg .= ', already at '.$sl->storage->name;
						$r->sound = 'already_at-'.AppHelper::codeToSound($sl->storage->name).'.mp3';
						$alloc = false;
					}
				}
			}
		}

		if($alloc && in_array($_POST['wid'], [106,218,530])){
			$s = Storage::allocate($_POST['wid'], 60, $p, empty($_POST['bsid'])? null : $_POST['bsid']);
			if($s){
				$r->msg .= ', store at '.$s->name;
				$r->sound = AppHelper::codeToSound($s->name).'.mp3';
				$p->custom_log_note = 'stored at '.$s->name;
				$savep = true;
			}else{
				$r->msg .= '. Unable to allocation storage space all full!';
				$r->sound = 'storage_full.mp3';
			}
		}

		if(empty($p->odpt_id)){// || $p->odpt_id != $_POST['wid']
			$p->odpt_id = $_POST['wid'];
			$savep = true;
		}

		if($savep) $p->save();

		return $r;
	}

	public function actionReport(){
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, array('Location','WBN','Weight','Time','Name','Address','State','Agent', 'Agent ID', 'Type', 'Qty', 'Rating Code', 'Rate', 'Charge'));
		if($_POST['wid'] != 106){
			$sql = 'SELECT s.id, s.hbn, a.name, a.address, a.state, s.weight FROM shipment s LEFT JOIN addr a ON (s.cnee_id = a.id) WHERE s.odpt_id = :wid AND s.id IN (SELECT pid FROM tracking WHERE `type` IN (14,15,18) AND dt >= :d AND dt < DATE_ADD(:d, INTERVAL 1 DAY) GROUP BY pid ORDER BY dt ASC)';
			$rs = Yii::app()->db->createCommand($sql)->bindValues([':wid' => $_POST['wid'], ':d' => $_POST['date']])->queryAll();
			$ts = strtotime($_POST['date']);
			foreach($rs as $r){
				$dt = Yii::app()->db->createCommand('SELECT dt FROM tracking WHERE `type` IN (14,15,18) AND pid = :id ORDER BY dt ASC LIMIT 1')->bindValues([':id' => $r['id']])->queryScalar();
				$dts = strtotime($dt);
				$p = ExParcel::model()->findByPk($r['id']);
				$typ = $p->goodsType();
				$weight = $p->chargeWeight();
				$rate = $p->getAgentRate();
				if($dts < $ts || $dts >= $ts+86400) continue;
				$xls->addRow($i++, array('', $r['hbn'], $weight, $dt, $r['name'], $r['address'], $r['state'], empty($p->agent)? '' : $p->agent->name, $p->agent_id, $typ, empty($p->eitems['q'])? 0 : array_sum($p->eitems['q']), $rate[0], $rate[1]->perkg, $rate[2]));
			}
			$xls->output($_POST['date'].'_receival_report.xlsx');
			Yii::app()->end();
		}

		$rs = StorageLog::model()->with('storage')->findAll(array(
				'condition' => 'storage.wid = :wid AND storage.type = :type AND DATE(t.in_dt) = :d',
				'params' => array(':wid' => $_POST['wid'], ':type' => 60, ':d' => $_POST['date']),
				'order' => 'in_dt',
			));
		foreach($rs as $r){
			$p = $r->mm();
			$xls->addRow($i++, array($r->storage->name, $p->hbn, $p->weight, $r->in_dt, $p->cnee->name, $p->cnee->address, $p->cnee->state));
		}
		$xls->output($_POST['date'].'_receival_report.xlsx');
	}

	public function actionBsopt($id){
		$rs = Storage::model()->findAll('status = 1 AND wid = :id AND type = :t', [':id' => $id, ':t' => $_GET['type']]);
		$ra = [];
		foreach($rs as $r){
			$ra[] = '<option value="'.$r->id.'">'.$r->name.'</option>';
		}
		echo implode("\n", $ra);
	}

	public function actionBlSuggest(){
		$rs = Storage::model()->findAll('status = 1 AND wid = :id AND type = 60 AND (name LIKE :t OR code LIKE :t) LIMIT 20', [':id' => $_GET['wid'], ':t' => $_GET['term'].'%']);
		$a = array();
		foreach($rs as $r){
			$a[] = array(
				'value' => $r->code,
				'label' => $r->name,
			);
		}
		echo json_encode($a);
	}

	public function actionBatch(){
		if(!empty($_FILES['manifest'])){
			$xls = new oExcel;
			if(!$xls->supported($_FILES['manifest']['name'])){
				$xls->getError(true);
				exit(0);
			}
			$xls->load($_FILES['manifest']['tmp_name']);
			$c = 0;
			$err = [];
			$sheetsArray = $xls->xls->getAllSheets();
			foreach ($sheetsArray as $n=>$sheet){
				$xls->goSheet($n);
				$data = $xls->getAll();
				foreach($data as $l=>$r){
					if(empty($r[1])) continue;
					$hbn = $r[1];
					$p = ExParcel::model()->find('hbn = :n', array(':n' => $hbn));
					$savep = true;
					$alloc = true;
					if(!$p){
						$p = new ExParcel('create');
						$p->hbn = $hbn;
						$p->status = 14;
						$cnor = new Addr;
						$cnor->country = 'Australia';
						$cnor->save();
						$p->cnor_id = $cnor->id;
						$cnee = new Addr;
						$cnee->country = 'PR China';
						$cnee->save();
						$p->cnee_id = $cnee->id;
					}else{
						if(in_array($p->status, [8,9,10,12])){
							$p->status = empty($p->cnee->name)? 14 : 15;
							$savep = true;
						}else{
							$savep = false;
						}

						if($p->status >= 60 && $p->status < 101){
							$err[] = 'duplicate consignment '.$hbn;
							$alloc = false;
						}else{
							$sl = StorageLog::findItem($p);
							if($sl){
								if($sl->storage->wid != $_POST['wid']){// moving warehouse
									$sl->out();
								}else{
									$alloc = false;
								}
							}
						}
					}

					if(!empty($_POST['bsid']) && $alloc && in_array($_POST['wid'], [106,218,530])){
						$s = Storage::allocate($_POST['wid'], 60, $p, $_POST['bsid']);
						if($s){
							$p->custom_log_note = 'stored at '.$s->name;
							$savep = true;
						}else{
							$err[] = 'Unable to allocation storage space all full!';
						}
					}

					if(empty($p->odpt_id)){
						$p->odpt_id = $_POST['wid'];
						$savep = true;
					}
					if($savep){
						$p->save();
						$c++;
					}
				}
			}
			$o = new StdClass;
			$o->done = true;
			$o->msg = (empty($err)? '': implode("\n", $err)."\n").$c.' parcels received.';
			echo json_encode($o);
		}
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters(){
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions(){
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}