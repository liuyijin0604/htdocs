<?php

class EdimsgController extends Controller{

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id){
		$em = Edimsg::model()->findByPk($id);
		$msg = '';
		if($em->rid > 0 && $em->type != 'CONTRL'){
			$msg = preg_match("/[\r\n]+/", $em->rmsg->msg)? $em->rmsg->msg : str_replace("'", "'\n", $em->rmsg->msg)."\n\n";
		}
		$msg .= preg_match("/[\r\n]+/", $em->msg)? $em->msg : str_replace("'", "'\n", $em->msg);
		echo '<pre>'.$msg.'</pre>';
	}

	public function actionChangeToError($id){
		$em = Edimsg::model()->findByPk($id);

		if($em->status == 30){
			$em->status = 44;
			$em->update();
			echo json_encode(['done'=>true,'msg'=>'Process Done']);
		}
		else{
			echo json_encode(['done'=>false,'msg'=>'Status has been charged']);
		}
	}

	public function actionConfirmError($id){
		$tabid = @$_GET['tabid'];
		$model = Edimsg::model()->findByPk($id);
		$this->render('confirm', ['model' => $model, 'tabid' => $tabid]);
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=EdiMsg::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='emailog-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
