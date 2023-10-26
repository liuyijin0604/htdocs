<?php

class ExAfsController extends Controller{

	protected $nonAjax = array('export', 'download', 'label');

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id){
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	public function actionUpdate($id){
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ExAfs'])){
			$model->attributes = $_POST['ExAfs'];
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
			$this->render('tab_'.$_GET['tab'], array('model'=>$model));
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
	
	public function actionLabel($id){
		$p=$this->loadModel($id);
		$tpl = 'label_chex';
		$html = $this->renderPartial('../expLabel/'.$tpl, array('p'  => $p), true);
		
		oPDF::html2pdf($html, 1, $p->hbn.'.pdf');
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
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=ExAfs::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
}
