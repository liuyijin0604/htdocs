<?php

class CoParcelController extends Controller{

	protected $nonAjax = array('export','crmmigrate');

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
		$model=new CoParcel;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['CoParcel'])){
			$model->setScenario('create');
			$model->attributes=$_POST['CoParcel'];
			//get owner_id;
			$u = User::model()->find('org_id = :id', array(':id' => $_POST['CoParcel']['owner_id']));
			$model->owner_id = empty($u)? $_POST['CoParcel']['agent_id'] : $u->id;
			$model->eitems = $_POST['items'];
			if(!empty($_POST['meta'])){
				foreach($_POST['meta'] as $k => $v){
					$model->mdata[$k] = $v;
				}
			}
			$cnor = new Addr;
			$cnor->attributes = $_POST['Cnor'];
			$cnor->save();
			$model->cnor_id = $cnor->id;
			$cnee = new Addr;
			$cnee->attributes = $_POST['Cnee'];
			$cnee->save();
			$model->cnee_id = $cnee->id;
			$model->save();
			$this->ajaxResult($model);
		}

		$model->pkg = 1;

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

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['CoParcel']) && $model->status < 50){
			$model->attributes = $_POST['CoParcel'];
			$model->eitems = $_POST['items'];
			if(!empty($_POST['meta'])){
				foreach($_POST['meta'] as $k => $v){
					$model->mdata[$k] = $v;
				}
			}
			$model->save();
			$model->cnor->attributes = $_POST['Cnor'];
			$model->cnor->save();
			$model->cnee->attributes = $_POST['Cnee'];
			$model->cnee->save();
			$this->ajaxResult($model);
		}
		
		if(isset($_GET['tab'])){
			Acl::hasAccess($this->CaName.'/'.$_GET['tab'], true);

            if ( $_GET['tab'] == 'crm' ) {
                $this->render('/crm/tab_' . $_GET['tab'], array('model' => $model));
            } else {
                $this->render('tab_' . $_GET['tab'], array('model' => $model));
            }

		}else{
			$this->render('update',array('model'=>$model));
		}
	}


	public function actionNotes($id){
		$model=$this->loadModel($id);
		if(!empty($_POST['notes'])){
			$log = Log::add($model, 6, array('notes' => $_POST['notes']));
			$this->ajaxResult($log);
		}
	}

	public function actionCneeSuggest($id=0){
		$rs = Addr::model()->findAll(array(
			'condition' => "t.tel != '' AND t.id != :id AND (t.name LIKE :t OR t.tel LIKE :t)",
			'params' => array(':t' => $_GET['term'].'%', ':id' => $id),
			'group' => 'name,tel',
			'limit' => 20,
		));
		$a = array();
		foreach($rs as $r){
			$attr = $r->attributes;
			unset($attr['id'], $attr['cnid_id'], $attr['owner_id'], $attr['acc']);
			$a[] = $attr + array(
				'value' => $r->name,
				'label' => $r->name.' ('.$r->state.'/'.$r->tel.')',
			);
		}
		echo json_encode($a);
	}

    /**
     * run migration scripts from web app
     */
    private function runMigrationTool(){
        $commandPath = Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands';
        $runner = new CConsoleCommandRunner();
        $runner->addCommands($commandPath);
        $commandPath = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
        $runner->addCommands($commandPath);
        $args = array('yiic', 'migrate', '--interactive=0');
        ob_start();
        $runner->run($args);
        return htmlentities(ob_get_clean());
    }

    // run my DB migration for CRM system
    public function actionCRMMigrate(){
       echo  $this->runMigrationTool();
    }

	public function actionTrackingGrid($id){
		if(empty($_POST['Tracking']['id'])){//add
			$pt = new Tracking;
			$pt->pid = $id;
		}else{
			$pt = Tracking::model()->findByPk($_POST['Tracking']['id']);
		}
		$pt->attributes = $_POST['Tracking'];
		$pt->save();
		$this->ajaxResult($pt);
		
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id){
		if(Yii::app()->request->isPostRequest){
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
		$model=new CoParcel('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['CoParcel']))
			$model->attributes=$_GET['CoParcel'];

		$this->render('list',array(
			'model'=>$model,
		));
	}

	public function actionExport(){
		$model=new CoParcel('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['CoParcel']))
			$model->attributes=$_GET['CoParcel'];

		if(!Acl::hasAccess('B:Courier/SeeAllShipments')) $model->agent_id = Yii::app()->user->org;
		$dp = $model->search(false);
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, array('WBN', 'Status', 'Agent', 'Sender', 'Sender Tel', 'Cnee Name', 'Cnee Tel', 'Address', 'State', 'Delay'));

		foreach($dp->data as $r){
			if(empty($r->cnee)) continue;
			$ln = $r->getLastLog();
			$dd = empty($ln)? 0 : ceil((time() - strtotime($ln->time)) / 86400);
			$xls->addRow($i++, array($r->hbn, $r->getStatus(), empty($r->agent)? '' : $r->agent->name, $r->cnor->name, '="'.$r->cnor->tel.'"', $r->cnee->name, '="'.$r->cnee->tel.'"', $r->cnee->address, $r->cnee->state, $dd));
		}
		$xls->output('imp_search_export_'.time().'.xlsx');
	}

	public function actionFetchTracking($id){
		$model=$this->loadModel($id);
		@exec(Yii::app()->basePath.DIRECTORY_SEPARATOR.'yiic tracking update '.$model->hbn);
		echo json_encode('done');
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=CoParcel::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
}
