<?php

/*
 * Manage stock
 *
 */

class StockController extends Controller
{

	/*
	 * for the storage list
	 */

	public function actionList()
	{
		$model = new WmsStock('search');
		$model->unsetAttributes();
		if (!empty($_GET['WmsStock'])) {
			$model->setAttributes($_GET['WmsStock']);
		}
		$model->orgids = User::getOrgIds();
		if (empty($model->orgids)) {
			$model->orgids = array_merge([1], User::getOrgIds()); //array_merge make sure it's not empty, because when empty, the search method will not use the  orgids field.
		}
		$this->render('list', array('model' => $model));
	}

	public function actionView($id)
	{
		$this->render('view', array('prod_id' => $id));
	}

	public function actionExport()
	{
		$prods = [];
		$stocks = WmsStock::model()->findAll('org_id = :org_id AND (bwf & 2) = 0', [':org_id' => Yii::app()->user->org]);
		foreach ($stocks as $stock) {
			$prods[] = $stock->prod_id;
		}

		$model = new WmsProd('search');
		$model->unsetAttributes();
		if (!empty($_GET['WmsProd'])) {
			$model->setAttributes($_GET['WmsProd']);
		}
		$model->type = WmsProd::WMS_PROD_PHYSICAL;
		$ec = new CDbCriteria;
		if (!empty($prods)) {
			$ec->addCondition('t.id IN (' . implode(',', $prods) . ')');
		} else {
			$ec->addCondition('1 = 0');
		}
		$data = $model->search(false, 0, $ec)->getData();

		$xls = new oExcel;
		$xls->setTitle('Summary');
		$i = 1;
		$xls->setColWidth(array(15, 40, 15, 15, 15, 15, 15));
		$xls->addRow($i++, array('Item Code / SKU', 'Description', 'Barcode', 'SOH', 'Allocated To Current Order', 'Available Stock', 'Minimum Stock Alert'));
		foreach ($data as $r) {
			$xls->addRow($i++, array($r->getSku(Yii::app()->user->org), $r->name, $r->ean, $r->getStockQty(Yii::app()->user->org, 'qty'), $r->getStockQty(Yii::app()->user->org, 'qty_res'), $r->getStockQty(Yii::app()->user->org, 'avail'), $r->getMinStockAlert(Yii::app()->user->org)));
		}

		$xls->createSheet('Details');
		$xls->goSheet(1);
		$xls->setTitle('Details');
		$i = 1;
		$xls->setColWidth(array(15, 40, 15, 15, 15, 15, 15, 15));
		$xls->addRow($i++, array('Item Code / SKU', 'Description', 'Barcode', 'SOH', 'Allocated To Current Order', 'Available Stock', 'Expiry', 'Batch'));
		foreach ($data as $r) {
			$stocks = WmsStock::model()->findAll('prod_id = :prod_id AND org_id = :org_id', [':prod_id' => $r->id, ':org_id' => Yii::app()->user->org]);
			foreach ($stocks as $stock) {
				$xls->addRow($i++, array($stock->prod->getSku(Yii::app()->user->org), $stock->prod->name, $stock->prod->ean, $stock->getQty() + $stock->qty_res, $stock->qty_res, $stock->getQty(), $stock->expiry, $stock->batch));
			}
		}

		$xls->goSheet(0);
		$xls->output('stock_search_export_' . time() . '.xlsx');
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = WmsStock::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $model;
	}

}
