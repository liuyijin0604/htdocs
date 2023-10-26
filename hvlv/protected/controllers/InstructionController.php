<?php

class InstructionController extends Controller
{

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view', array(
			'model' => $this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new Instruction;

		if(isset($_POST['Instruction']))
		{
			$model->attributes = $_POST['Instruction'];
			// if($model->save())
			// {
			// 	$this->redirect(array('view', 'id' => $model->id));
			// }
			$model->save();
			$this->ajaxResult($model);
		}

		$this->render('create', array(
			'model' => $model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		if(isset($_POST['Instruction']))
		{
			$model->attributes = $_POST['Instruction'];
			// if($model->save())
			// {
			// 	$this->redirect(array('view', 'id' => $model->id));
			// }
			$model->save();
			$this->ajaxResult($model);
		}

		$this->render('update', array(
			'model' => $model,
		));
	}

	/**
	 * Lists and searches models.
	 */
	public function actionList()
	{
		$model = new Instruction('search');
		$model->unsetAttributes();  // clear any default values

		if(isset($_GET['Instruction']))
		{
			$model->attributes = $_GET['Instruction'];
		}

		$this->render('list', array(
			'model' => $model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = Instruction::model()->findByPk($id);
		if($model === null)
		{
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax'] === 'instruction-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

}
