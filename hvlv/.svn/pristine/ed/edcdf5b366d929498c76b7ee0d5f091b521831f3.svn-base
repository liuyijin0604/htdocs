<?php
class ImportWeightCheckController extends Controller
{
    protected $nonAjax=['weightCheckExport'];
   
    public function actionIndex()
    {
       $model = new Reconciliation('search');
		$model->unsetAttributes();
		if (isset($_GET['Reconciliation'])) {
			$model->attributes = $_GET['Reconciliation'];
		}
		$this->render('index', ['model' => $model]);
    }

    public function actionViewWeightDetails($id)
	{
		$model = $this->loadModel($id);

		$model->unsetAttributes();
		if (isset($_GET['ReconciliationLine'])||isset($_GET['ReconciliationLineDeclare'])) {
			$model->attributes = @$_GET['ReconciliationLine'];
			$model->attributes = @$_GET['ReconciliationLineDeclare'];
			if(@$_GET['ReconciliationLine']['agentId']!=null)
			{
				$model->agentId = $_GET['ReconciliationLine']['agentId'];
			}
			if(@$_GET['ReconciliationLineDeclare']['agentId']!=null)
			{
				$model->agentId = $_GET['ReconciliationLineDeclare']['agentId'];
			}
		}

		$model->setAttribute('parent_id', $id);
		$this->render('reconciliation_wc_details', ['model' => $model]);
	}


	public function actionWeightCheckExport($id)
	{

		$model = Reconciliation::model()->findByPk($id);
		$xls = new oExcel;
		$mfn = 'Reconciliation_detail_' . $id;
		$i = 1;
		$xls->addRow($i++, ['Ref', 'Consol', 'Weight(Kg)', 'courier Cubic(M3)', "Customer CBM", 'Customer Weight', 'Customer Bulky Weight', 'Customer Should Charge Weight', 'Charge Weight Diff']);
		$xls->setFont('A1:I1', ['bold' => true]);
//        $totalAmount = 0;
		//        $totalOurAmount = 0;
		foreach ($model->lines as $il) {
			if ($model->client_type == Reconciliation::TNT_CLIENT && !empty($il->mdata['tnt_type']) && $il->mdata['tnt_type'] != 'Shipment') continue;
			$xls->addRow($i++, [$il->shipment_no, !empty($il->consol) ? $il->consol->no : "", $il->weight, $il->courier_cubic, $il->getCustomerCBM(), $il->getCustomerWeight(), $il->getBulkyWeight(), $il->getCubicRate(), $il->getCSChargeWeight(),$il->getChargeWeightDiff()]);
//            $totalAmount += $il->value;
			//            $totalOurAmount += $il->my_value;
		}
//        $xls->addRow($i, array('','','','', 'Total:', $totalAmount, $totalOurAmount, $totalOurAmount - $totalAmount));
		$xls->setFont('A' . $i . ':I' . $i, ['bold' => true]);
		$xls->output($mfn . '.xlsx');
	}

	public function actionDiffInvoiceCheck($id)
	{
		$model = $this->loadModel($_GET["parent_id"], $id);

		$weight = $model->getCdeadwtOrWeight();
		$csChargeWeight = $model->ourChargeWeight();
		$courierWeightByChargeCode = $model->getCourierWeightByChargeCode();
		$diffWeight = $courierWeightByChargeCode-$csChargeWeight;
		$courierWeightInvoiceByChargeCode =  $model->getCourierWeightInvoiceByChargeCode();
		$chargedInvoice = $model->getChargedInvoice();
		$payInBack = $courierWeightInvoiceByChargeCode-$chargedInvoice;
		$cubicRate = $model->getShipmentCubicRate();
		$this->render('invoice_check', ['model' => $model,'cubicRate'=>$cubicRate,'weight'=>$weight,'csChargeWeight'=>$csChargeWeight,'courierWeightByChargeCode'=>$courierWeightByChargeCode,'diffWeight'=>$diffWeight, 'courierWeightInvoiceByChargeCode'=>$courierWeightInvoiceByChargeCode, 'chargedInvoice'=>$chargedInvoice,'payInBack' => $payInBack]);
	}


	public function actionViewWeightDiffReport()
	{
		$id = $_GET['parent_id'];
		$model = Reconciliation::model()->findByPk($id);
		$report = $model->getReconciliationWeightDiffInvoiceReport();
		$this->render('weightDiffReport', ['model' => $model,'report'=>$report,'title'=>'Weight_Diff','url'=>'invoice/generateAllReWDInv']);
	}

	public function loadModel($parentId, $id = 0 )
	{
		$parent = Reconciliation::model()->findByPk($parentId);
		$model = null;
		if(!empty($id))
		{
			if(in_array($parent->getType(),Reconciliation::$declareCourier)||in_array($parent->getType(),Reconciliation::$rtsCourier))
	    	{
				$model = ReconciliationLineDeclare::model()->find('id = :id',[":id"=>$id]);
			}else
			{
				$model = ReconciliationLine::model()->find('id = :id',[":id"=>$id]);
			}
		}else
		{
			if(in_array($parent->getType(),Reconciliation::$declareCourier)||in_array($parent->getType(),Reconciliation::$rtsCourier))
	    	{
				$model = new ReconciliationLineDeclare('search');
			}else
			{
				$model = new ReconciliationLine('search');
			}

		}

		return $model;
	}

}


