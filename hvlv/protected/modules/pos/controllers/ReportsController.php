<?php

class ReportsController extends Controller{
	/**
	 * Declares class-based actions.
	 */
	protected $nonAjax=array('export');

	public $org;
	
	public function beforeAction($action){
		if(!Yii::app()->user->isGuest){
			$this->org = Org::model()->findByPk(Yii::app()->user->org);
		}
		return parent::beforeAction($action);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex(){
		$this->render('index');
	}

	public function actionView($id){
		if(empty($_GET['t'])) return;
		if($_GET['t'] == 'pl'){
			$model = PickupList::model()->findByPk($id);
			$this->render('pl', ['model' => $model]);
		}
	}

	public function actionDelPickup($id){
		$mm = ManiMap::model()->find('fid = :fid AND mani_id = :mid', [':fid' =>$id, ':mid' => $_GET['mid']]);
		$r = ['done' => false, 'msg' => 'Pickup not found.'];
		if(empty($mm)){
			echo json_encode($r);
			return false;
		}else{
			$p = $mm->mm();
			if($p->status < 12 && $p->agent_id == Yii::app()->user->org){
				$mm->mani_id = 0;
				$mm->save();
			}
			$this->ajaxResult($mm);
		}
	}

	public function actionScan(){
		if(empty($this->org->extra['pos_pu'])) return;
		if(!empty($_POST['bcs'])){
			$r = ['done' => true, 'err' => []];
			$cs = preg_split('/[,]+/', $_POST['bcs']);
			$model=PickupList::model()->find('fwd_id = :oid AND created >= :today', [':oid' => Yii::app()->user->org, ':today' => date('Y-m-d')]);
			if(empty($model)){
				$model = new PickupList('create');
				$model->fwd_id = Yii::app()->user->org;
				$model->ref = $model->fwd_id.'-'.date('ymdH', empty($model->created)? time() : strtotime($model->created));
				$model->dpt_id = 106;
				$model->mdata['cc_status'] = 10;
				$model->save();
			}
			foreach($cs as $c){
				$c = trim(strtoupper($c));
				$p = ExParcel::model()->find('hbn = :h', [':h' => $c]);
				if(!empty($p) && $p->agent_id != Yii::app()->user->org){
					$r['err'][(string) $c] = ' not found';
					continue;
				}else{
					if(empty($p)){
						$p = new ExParcel('create');
						$p->agent_id = Yii::app()->user->org;
						$p->hbn = $c;
						$p->save();
					}
					$map = true;
					$save = false;
					if($p->status < 12){
						$ht = Tracking::model()->count('pid = :pid AND type = :t', array(':pid' => $p->id, ':t' => 12));
						if(empty($ht)) $p->addTracking(12, 'Consignment Picked Up');
					}
					if(empty($p->odpt_id)){
						$p->odpt_id = $model->dpt_id;
						$save = true;
					}

					if($model->hasMap($p)){
						$map = false;
					}else{
						$rs = ManiMap::model()->findAll("fid = :id AND model = 'ExParcel'", [':id' => $p->id]);
						foreach($rs as $m){
							if($m->mani_id == $model->id) continue;
							if($m->manifest->type == 40){
								$r['err'][(string) $c] = 'already picked';
								$map = false;
							}
						}
					}

					if($map){
						$model->map($p);
						$p->agent_id = $model->fwd_id;
						$save = true;
					}
					if($save) $p->save();
				}
			}

			echo json_encode($r);
			Yii::app()->end();
		}
		$this->render('scan');
	}

	public function actionExport($id){
		if(empty($_GET['t'])) return;

		if($_GET['t'] == 'pl'){
			$model = PickupList::model()->findByPk($id);

			$xls = new oExcel;
			$i = 1;
			$xls->addRow($i++, array($model->ref, $model->created));
			$xls->addRow($i++, array('Connote', 'Cust Ref', 'Status', 'Sender', 'Sender Tel', 'Cnee Name', 'Cnee Tel', 'Address', 'State', 'Weight'));

			foreach($model->lines as $l){
				$r = $l->mm();
				$xls->addRow($i++, array('="'.$r->hbn.'"', empty($r->cref)? '' : '="'.$r->cref.'"', $r->getStatus(), $r->cnor->name, '="'.$r->cnor->tel.'"', $r->cnee->name, '="'.$r->cnee->tel.'"', $r->cnee->address, $r->cnee->state, $r->weight));
			}
			$xls->output('pickup_'.$model->ref.'.xlsx');
		}
	}

	public function actionClose($id) {
		$model = PickupList::model()->findByPk($id);
		$model->bwf |= 2;
		$model->update('bwf');
		$this->render('index');
	}
}