<?php

class oListController extends Controller
{

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id){
		$model = $this->loadModel($id);
		$this->render('view',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id){
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		$model = $this->loadModel($id);
		if(!empty($_POST['inactive'])){
			foreach($_POST['inactive'] as $p => $la){
				$p = empty($p)? $id : $p;
				foreach($la as $i => $n){
					if(empty($i) || strpos($i, 'new_') !== false) continue;
					$li = oList::model()->findByPk($i);
					$li->active = 0;
					$li->item = $n;
					$li->save();
				}
			}
		}
		if(!empty($_POST['active'])){
			$pmap = array();
			foreach($_POST['active'] as $p => $la){
				$p = empty($p)? $id : $p;
				$k = 1;
				foreach($la as $i => $n){
					if(empty($i) || strpos($i, 'new_') !== false){
						$li = new oList;
						$isnew = true;
					}else{
						$li = oList::model()->findByPk($i);
						$isnew = false;
					}
					$li->active = 1;
					$li->weight = $k++;
					$li->item = $n;
					$li->pid = isset($pmap[$p])? $pmap[$p] : $p;
					$li->save();
					if($isnew){
						$pmap[$i] = $li->id;
					}
				}
			}
		}
		if(!empty($li)){
			$this->ajaxResult($li);
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Lists and search.
	 */
	public function actionList(){
		$model=new oList('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['oList']))
			$model->attributes=$_GET['oList'];

		$this->render('list',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=oList::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='o-list-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
