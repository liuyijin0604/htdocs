<?php

class AccountsController extends Controller{
	/**
	 * Declares class-based actions.
	 */
	protected $nonAjax=array('export');

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex(){
		$this->render('index');
	}

	public function actionExport($id){
		if(!empty($_GET['xls'])){
			$this->actionDetail($id);
		}else{
			$model=$this->loadModel($id);
			oPDF::renderPDF('invoice', array('inv'=>$model));
		}
	}

	public function actionDetail($id){
		$model=$this->loadModel($id);
		$xls = new oExcel;
		$mfn = 'Invoice_detail_'.$id;
		$i = 1;
		switch($model->type){
			case 10:
			$xls->addRow($i++, array('HBN', 'Detail', 'Packages', 'Weight', 'CBM', 'Base Rate', 'Rate', 'Unit', 'Amount'));
			$xls->setFont('A1:I1', array('bold' => true));
			$qty = 0;
			$wei = 0;
			$cbm = 0;
			$tot = 0;
			foreach($model->lines as $il){
				if(empty($il->mdata['items'])) continue;
				foreach($il->mdata['items'] as $si=>$r){
					$xls->addRow($i++, array($r[0], $r[1], $r[2], $r[3], $r[4], $r[6], $r[7], $r[8], $r[5]));
					$tot += $r[5];
					$qty += $r[2];
					$wei += $r[3];
					$cbm += $r[4];
				}
			}
			$xls->addRow($i, array('', 'Total:', $qty, $wei, $cbm, '', '', '', $tot));
			$xls->setFont('A'.$i.':I'.$i, array('bold' => true));
			break;
			case 20:
			$xls->addRow($i++, array('HBN', 'Shipper', 'Packages', 'Weight', 'Type', 'Rate', 'Amount'));
			$xls->setFont('A1:G1', array('bold' => true));
			$qty = 0;
			$wei = 0;
			$cbm = 0;
			$tot = 0;
			foreach($model->lines as $il){
				if(empty($il->mdata['items'])) continue;
				foreach($il->mdata['items'] as $si=>$r){
					$xls->addRow($i++, array($r[0], $r[1], 1, $r[2], $r[4].'('.$r[5].')', $r[6], $r[7]));
					$tot += $r[7];
					$qty ++;
					$wei += $r[2];
				}
			}
			$xls->addRow($i, array('', 'Total:', $qty, $wei, '', '', $tot));
			$xls->setFont('A'.$i.':G'.$i, array('bold' => true));
			break;
		}
		$i++;
		$xls->output($mfn.'.xlsx');
	}
	
	public function loadModel($id){
		$model=Invoice::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
}