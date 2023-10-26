<?php

class ExpLabelController extends Controller{

	protected $nonAjax = array('label');

	public function actionScan(){
		if(isset($_POST['bc'])){
			$o = new StdClass;
			//$rmap = include(Yii::app()->basePath.DIRECTORY_SEPARATOR.'ref_map.php');
			//if(!empty($rmap[$_POST['bc']])) $_POST['bc'] = $rmap[$_POST['bc']];
			$p = ExParcel::model()->find('hbn = :bc AND consol_id = :cid', [':cid' => $_POST['cid'], ':bc' => $_POST['bc']]);
			if(empty($p) || (empty($p->ref) && !in_array($p->consol->poc, ['CNCTU', 'CNCT2', 'HKHKG']))){
				$o->msg = $_POST['bc'].' not found';
				$o->nf = 1;
			}else{
				$o->msg = $_POST['bc'].' <a class="print" href="'.$this->createUrl('expLabel/label', array('id' => $p->id)).'" target="prntifm" data-pc="1" style="color:#999">Printed</a>';
				$o->nf = 0;
				if(!empty($_POST['ptz'])){//add to pallet
					$man = Manifest::model()->findByPk($_POST['pid']);
					$mpd = $man->hasMap($p);
					if($mpd){
						$o->msg = $_POST['bc'].' <a class="print" href="'.$this->createUrl('expLabel/label', array('id' => $p->id)).'" target="prntifm" style="color:#999">Printed<span></span></a>';
						$o->nf = 2;
					}
					$man->map($p);
				}
			}
			echo json_encode($o);
			Yii::app()->end();
		}

		$this->render('scan');
	}

	public function actionLabel($id){
		$r = new StdClass;
		$p = ExParcel::model()->findByPk($id);
		$tpl = $p->consol->getLabelTpl();

		$this->renderPartial($tpl, array('p' => $p));
	}

	public function actionPallets($id){
		echo '<option value="">Select One</option>';
		$rs = Manifest::model()->findAll('type = 60 AND consol_id = :cid', [':cid' => $id]);
		foreach($rs as $r){
			echo '<option value="'.$r->id.'">'.$r->ref.'</option>';
		}
	}

	public function actionAddpallet($id){
		$model = new Manifest('create');
		$model->type = 60;
		$model->ref = sprintf('%02d', (Manifest::model()->count('type = 60 AND consol_id = :cid', [':cid' => $id]) + 1));
		$model->consol_id = $id;
		$model->by_id = Yii::app()->user->id;
		$model->save();
		$this->ajaxResult($model);
	}

}