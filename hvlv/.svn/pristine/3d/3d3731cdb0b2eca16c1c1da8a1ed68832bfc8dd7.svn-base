<?php

class SzpChannelController extends Controller
{
	protected $nonAjax = [];
	protected $skipAcl = [];

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id){
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate(){
		$model=new SzpChannel;

		if(isset($_POST['SzpChannel']))
		{
			$model->attributes=$_POST['SzpChannel'];
			if(!empty($_POST['mdata'])){
				foreach($_POST['mdata'] as $k=>$v){
					$model->mdata[$k] = $v;
				}
			}
			$model->save();
			$this->ajaxResult($model);
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id){
		$model=$this->loadModel($id);

		if(!empty($_POST)){
			$up = false;
			if(isset($_POST['SzpChannel'])){
				$model->attributes=$_POST['SzpChannel'];
				$up = true;
			}
			if(!empty($_POST['mdata'])){
				if(isset($_POST['mdata']['ccf'])){
					foreach(['idnonly', 'ftc', 'altCnee', 'allowType_B', 'allowType_M', 'allowType_O', 'allowType_X', 'actService_0', 'actService_10', 'actService_20'] as $k){
						if(!isset($_POST['mdata'][$k])) $_POST['mdata'][$k] = 0;
					}
				}
				foreach($_POST['mdata'] as $k=>$v){
					$model->mdata[$k] = $v;
				}
				$up = true;
			}
			if($up) $model->save();
			$this->flushBRcache();
			$this->ajaxResult($model);
		}

		if(isset($_GET['tab'])){
			Acl::hasAccess($this->CaName.'/'.$_GET['tab'], true);
			$this->render('tab_' . $_GET['tab'], array('model' => $model));
		}else{
			$this->render('update',array('model'=>$model));
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id){
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists and search.
	 */
	public function actionList(){
		$model=new SzpChannel('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['SzpChannel']))
			$model->attributes=$_GET['SzpChannel'];

		$this->render('list',array(
			'model'=>$model,
		));
	}

	public function actionGlobalRules(){
		$model=new SzpChannel('search');
		$model->id = -1;
		Meta::getAll($model);
		if(Yii::app()->user->grp == 0 && !empty($_POST['mkv'])){
			foreach($_POST['mkv'] as $k => $v){
				$model->_mkv[$k] = $v;
			}
			Meta::saveAll($model);
			$this->flushBRcache();
			$this->ajaxResult($model);
		}
		$this->render('grules', ['model' => $model]);
	}

	public function actionTester(){
		if(!empty($_POST['tests'])){
			$grid = [];
			foreach($_POST['tests'] as $t){
				if(is_string($t)){
					parse_str(urldecode($t), $t);
					unset($t['yt0']);
				}
				$p = new ExParcel;
				$p->agent_id = empty($t['a'])? 215 : $t['a'];
				$p->weight = empty($t['w'])? 1 : $t['w'];
				$p->state = $t['s'];
				$p->odpt_id = 106;
				$p->styp = $t['st'];
				$p->cnee = new Addr;
				$p->cnee->state = $p->state;
				$p->cnee->cnid_id = 100;
				$p->eitems['type'][0] = $t['t'];
				$p->eitems['g'][0] = $t['g'];
				$p->eitems['q'][0] = $t['q'];
				$p->eitems['hs'][0] = '';
				$pd = ExProdb::model()->find('name_zh = :n', [':n' => $t['g']]);
				$p->eitems['pid'][0] = empty($pd)? 0 : $pd->id;
				switch($t['t']){
					case 'B':
						$v = 88;
					break;
					case 'M':
						$v = 50;
					break;
					case 'O':
						$v = 54;
					break;
				}
				$p->eitems['v'][0] = $v;
				$brs = $p->bestRates(false, false);
				$cs = array_keys($brs);
				$crs = [];
				foreach($brs as $k=>$v){
					$crs[SzpChannel::getName($k)] = sprintf('%0.2f', $v);
				}
				$t['st'] = $p->getStyp();
				$grid[] = '<tr><td>'.implode('</td><td>', $t).'</td><td><b title="'.htmlspecialchars(var_export($crs, true)).'">'.((empty($brs) || $brs[$cs[0]] == 999)? 'N/A' : SzpChannel::getName($cs[0])).'</b></td></tr>';
			}
			$r = ['done' => true, 'msg' => '', 'grid' => implode('\n', $grid)];
			echo json_encode($r);
			return;
		}
		$this->render('tester');
	}

	public function actionNotes($id){
		$model=$this->loadModel($id);
		if(!empty($_POST['notes'])){
			$log = Log::add($model, 6, array('notes' => $_POST['notes']));
			$this->ajaxResult($log);
		}
	}

	public function actionNewRate($id){
		$model = new OrgRate('create');
		$model->type  = 10;

		if(isset($_POST['OrgRate'])){
			$exc=$this->loadModel($id);
			$model->name = $exc->name;
			$model->code  = $exc->code;
			$model->updated = date('Y-m-d H:i:s');
			$model->attributes=$_POST['OrgRate'];
			if(!empty($_POST['mdata'])){
				foreach($_POST['mdata'] as $k=>$v){
					$model->mdata[$k] = $v;
				}
			}
			if(strtotime($model->vto) < time()) $model->addError('vto', 'Valid to date can not be set to past date.');
			$model->save();
			$this->ajaxResult($model);
		}
		$this->render('rates', array('model'=>$model,));
	}

	public function actionRates($id){
		$model = OrgRate::model()->findByPk($id);
		if($model===null) throw new CHttpException(404,'The requested page does not exist.');

		if(isset($_POST['OrgRate'])){
			$model->attributes=$_POST['OrgRate'];
			if(!empty($_POST['mdata'])){
				foreach($_POST['mdata'] as $k=>$v){
					$model->mdata[$k] = $v;
				}
			}
			if(strtotime($model->vto) < time()) $model->addError('vto', 'Valid to date can not be set to past date.');
			$model->save();
			$this->ajaxResult($model);
		}
		$this->render('rates',array('model'=>$model,));
	}

	public function actionRatesGrid($id){
		if(isset($_POST['ZoneRate'])){
			if(empty($_POST['ZoneRate']['id'])){//create
				$model = new ZoneRate;
				$model->rate_id = $id;
			}else{//update
				$model = ZoneRate::model()->findByPk($_POST['ZoneRate']['id']);
			}

			$model->attributes = $_POST['ZoneRate'];
			$model->save();
			$this->ajaxResult($model);
		}
	}

	public function actionRateCheck($id){
		$ps = CnProvince::model()->findAll('weight < 50');
		$rs = ZoneRate::model()->findAll('base+item+perkg > 0 AND rate_id = :rid', [':rid' => $id]);
		$pns = $prs = [];
		foreach($ps as $p) $pns[] = $p->name;
		foreach($rs as $r){
			if($r->zone == 'ALL') $r->zone_name = implode(',', $pns);
			foreach(explode(',', $r->zone_name) as $p){
				if(isset($prs[$p])){
					if($r->weight_lo < $prs[$p][0]) $prs[$p][0] = $r->weight_lo;
					if($r->weight_hi > $prs[$p][1]) $prs[$p][1] = $r->weight_hi;
				}else{
					$prs[$p] = [$r->weight_lo, $r->weight_hi];
				}
			}
		}
		$err = $warn = [];
		foreach($pns as $p){
			if(empty($prs[$p])){
				$err[] = $p.' has no rate';
				continue;
			}
			if($prs[$p][0] > 0 || $prs[$p][1] < 10){
				$warn[] = $p.' limited weight ['.$prs[$p][0].','.$prs[$p][1].']';
			}
		}
		if(empty($err) && empty($warn)){
			echo '<p class="green">Rates Ok!</p>';
		}else{
			if(!empty($err)){
				echo '<p class="red">Errors:<br />';
				foreach($err as $e) echo $e.'<br />';
				echo '</p>';
			}
			if(!empty($warn)){
				echo '<p>Warning:<br />';
				foreach($warn as $e) echo $e.'<br />';
				echo '</p>';
			}
		}
	}

	public function actionCopyRate($id){
		$model = OrgRate::model()->findByPk($id);
		if(strtotime($_GET['cutoff']) < time()){
			$model->addError('vto', 'Valid to date can not be set to past date.');
		}else{
			$model->vto = $_GET['cutoff'];
			$model->save();
			$oid = $model->id;
			$model->id = null;
			$model->vfrom = $_GET['cutoff'];
			$model->vto = null;
			$model->isNewRecord = true;
			if($model->save()) $model->copyZonesFrom($oid);
		}

		$this->ajaxResult($model);
	}

	protected function flushBRcache(){
		//Yii::app()->db->createCommand("TRUNCATE TABLE YiiCache")->execute();
		Yii::app()->cache->flush();
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=SzpChannel::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='ex-channel-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
