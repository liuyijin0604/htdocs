<?php

class InvoiceController extends Controller{

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id){
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	
	public function actionViewCargoInvoice($id)
	{
		$model=$this->loadModel($id);
		$invoice = $model->getCRuleInvoice();
		$this->render('show_cargo_invoice',["data"=>$invoice]);
	}

	public function actionList()
	{
		$model=new CargoProcess('search');
		$model->unsetAttributes();
		$model->driver_id = User::getCurrentUser()->org_id;
		$model->deliveriedStatus = CargoProcess::DELIVERIED;
		if(!empty($_REQUEST['CargoProcess']))
		{
			$model->setAttributes($_REQUEST['CargoProcess']);

			if(!empty($_REQUEST['CargoProcess']['searchFrom']))
			{
				$model->searchFrom=$_REQUEST['CargoProcess']['searchFrom'];
			}
			if(!empty($_REQUEST['CargoProcess']['searchTo']))
			{
				$model->searchTo=$_REQUEST['CargoProcess']['searchTo'];
			}

			if(!empty($_REQUEST['CargoProcess']['searchFromS']))
			{
				$model->searchFrom=$_REQUEST['CargoProcess']['searchFromS'];
			}
			if(!empty($_REQUEST['CargoProcess']['searchToS']))
			{
				$model->searchTo=$_REQUEST['CargoProcess']['searchToS'];
			}
		}

		if(!empty($_REQUEST['yt1']))
		{
			$model->exportInvoiceList();
		}else
		{

			$this->render('list',["model"=>$model]);
		}	
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=CargoProcess::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

}
