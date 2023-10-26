<?php

class ChargeCodeController extends Controller
{
	protected $nonAjax = ['list', 'export'];

	/**
	 *
	 */
	public function actionList()
	{
		$this->render('list');
	}

	/**
	 * export all
	 */
	public function actionExport()
	{
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, array('Code', 'Name', 'Type', 'Tax Code', 'Description'));
		$xls->setColWidth(array(10, 25, 10, 15, 30));
		$glcodes = Chargecode::model()->findAll('status = 1 AND id > 0');
		foreach ($glcodes as $r) {
			$xls->addRow($i++, array($r->code, $r->name, $r->type, $r->tax_code, $r->description));
		}
		$xls->output('glcode_export_' . time() . '.xlsx');
		Yii::app()->end();
	}

	/**
	 *
	 */
	public function actionGrid()
	{
		if (isset($_POST['Chargecode'])) {
			if (empty($_POST['Chargecode']['id'])) {
				$model = new Chargecode();
			} else {
				$model = Chargecode::model()->findByPk($_POST['Chargecode']['id']);
			}
			$model->attributes = $_POST['Chargecode'];
			$model->save();
			$this->ajaxResult($model);
		}
	}

	public function actionDpmt(){
		$a = array_keys($_POST['dpmt']);
		$model = $this->loadModel($a[0]);
		if(!empty($_POST['dpmt'])){
			$model->dpmt = $_POST['dpmt'][$a[0]];
			$model->save();
			$this->ajaxResult($model);
		}
	}

	/**
	 * auto complete for charge code selection
	 */
	public function actionChargeCodeFixed()
	{
		$a = array();
		$chargeTypes = EdiJob::getChargeItemTypes();
		foreach ($chargeTypes as $k => $v) {
			$a[] = array(
				'value' => $k,
				'label' => $v,
			);
		}
		echo json_encode($a);
	}

	/**
	 * auto complete for import charge code selection
	 */
	public function actionChargeCodeImportFixed()
	{
		$a = array(
			//  ['value' => '83000', 'label' => '83000:Import income'],
			//  ['value' => '1050.00.10', 'label' => '1050.00.10:WAREHOUSE SERVICES REVENUE'],
			['value' => '91034', 'label' => '91034:COS import warehouse service/material cost'],
			//  ['value' => '91034', 'label' => '91034:WAREHOUSE MATERIAL COST'],
			['value' => '91030', 'label' => '91030:COS import terminal handling'],
			['value' => '91031', 'label' => '91031:COS import local courier'],
			// ['value' => '1030.00.10', 'label' => '1030.00.10:DOCUMENTATION REVENUE'],
			['value' => '91032', 'label' => '91032:COS import customs clearance'],
			//  ['value' => '2040.00.10', 'label' => '2040.00.10:WAREHOUSE STORAGE COST'],
			//     ['value' => '2060.00.10', 'label' => '2060.00.10:IMPORT - DUTY COST'],
			//   ['value' => '2030.00.10', 'label' => '2030.00.10:IMPORT - DOCUMENTATION COST'],
			//   ['value' => '2080.00.10', 'label' => '2080.00.10:    IMPORT - TRANSPORTATION COST'],
			//  ['value' => '2011.00.10', 'label' => '2011.00.10:    IMPORT - AIRFREIGHT COST'],
			['value' => '91033', 'label' => '91033:COS import air freight/Ocean'],
		);
		echo json_encode($a);
	}

	/**
	 *
	 */
	public function actionChargeCodeExportFixed()
	{
		$a = array(
			['value' => '91010', 'label' => 'COS commercial documentation'],
			['value' => '91014', 'label' => 'COS export/Import commercial local'],
			//    ['value' => '2020.00.30', 'label' => 'EDF Surcharge (Missing FWB)'],
			['value' => '91012', 'label' => 'COS export /import commercial parcel arifreight/ocean'],
		);
		echo json_encode($a);
	}

	public function actionChargeCode3PLFixed()
	{
		$a = array(
			['value' => '91022', 'label' => '3PL Cost - Warehouse Service'],
			['value' => '91022', 'label' => '3PL Cost - Warehouse Material'],
			['value' => '91014', 'label' => 'Air/Sea Cost - Cartage'],
			['value' => '91009', 'label' => 'Air/Sea Cost - Other'],
		);
		echo json_encode($a);
	}

	/**
	 * auto complete for charge code selection
	 */
	public function actionChargeCodeSuggest()
	{
		$rs = Chargecode::model()->findAll(array(
			'condition' => 'status = 1 AND (code LIKE :n OR name LIKE :n)',
			'params' => array(':n' => '%' . $_GET['term'] . '%'),
			'order' => 'code',
			'limit' => 20,
		));
		$a = array();
		foreach ($rs as $r) {
			$a[] = array(
				'value' => $r->code,
				'label' => $r->name,
			);
		}
		echo json_encode($a);
	}

	public function actionChargeCodeSuggest2()
	{
		$rs = Chargecode::model()->findAll('status = 1 AND code LIKE :t OR name LIKE :t', [':t' => '%' . $_GET['term'] . '%']);
		
		$a = array();
		foreach ($rs as $r) {
			if($_GET['dpmt'] > 1 && $r->dpmt > 0 && $r->dpmt != $_GET['dpmt']) continue;
			$a[] = array(
				'value' => $r->code,
				'label' => $r->name,
				'tax' => $r->tax_code,
			);
		}
		echo json_encode($a);
	}

	/**
	 * used to import original data from xls only
	 */
	private function importFromXls()
	{
		$oxls = new oExcel;
		$postfile = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'ChartOfAccounts20161028.xlsx';
		$oxls->load($postfile);
		$data = $oxls->getAll();
		unset($data[1]);

		foreach ($data as $chargecode) {
			$code = $chargecode[1];
			$name = $chargecode[2];
			$type = $chargecode[3];
			$taxCode = $chargecode[4];
			$description = $chargecode[5];

			// save to my DB
			$mChargeCode = new Chargecode();
			$mChargeCode->code = $code;
			$mChargeCode->name = $name;
			$mChargeCode->type = $type;
			$mChargeCode->tax_code = $taxCode;
			$mChargeCode->description = $description;

			$mChargeCode->save();
		}

	}

	public function actionSyncXero()
	{
		if (empty($_GET['confirm'])) {
			$this->render('sync_xero', array('tabid' => $_GET['tabid']));
		} else {
			$xero = new XeroAPI;
			$accounts = $xero->get('Accounting\Account');

			foreach ($accounts as $account) {
				if (!empty($account['Code'])) {
					$chargecode = Chargecode::model()->find('status = 1 AND code = :code', array(':code' => $account['Code']));
					if ($chargecode) {
						$chargecode->name = $account['Name'];
						$chargecode->description = $account['Name'];
						$chargecode->type = Chargecode::$types[$account['Type']];
						$chargecode->tax_code = Chargecode::$taxTypes[$account['TaxType']];
						$chargecode->class = Chargecode::$classes[$account['Class']];
						$chargecode->save();
					} else {
						$chargecode = new Chargecode;
						$chargecode->code = $account['Code'];
						$chargecode->name = $account['Name'];
						$chargecode->description = $account['Name'];
						$chargecode->type = Chargecode::$types[$account['Type']];
						$chargecode->tax_code = Chargecode::$taxTypes[$account['TaxType']];
						$chargecode->class = Chargecode::$classes[$account['Class']];
						$chargecode->save();
					}
				} else if ($account['Type'] == 'BANK') {
					$bank = BankAccount::model()->find('code = :code', array(':code' => $account['BankAccountNumber']));
					if (empty($bank)) {
						$bank = new BankAccount;
						$bank->code = $account['BankAccountNumber'];
						$bank->created = date('Y-m-d H:i:s');
						$bank->account_number = $account['BankAccountNumber'];
					}
					$bank->xero_id = $account['AccountID'];
					$bank->name = $account['Name'];
					$bank->save();
				}
			}

			$r = ['done' => true, 'msg' => 'Sync with Xero successfully'];
			echo json_encode($r);
			Yii::app()->end();
		}
	}

	public function loadModel($id)
	{
		$model=Chargecode::model()->findByPk($id);
		if ($model===null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	// Uncomment the following methods and override them if needed
	/*
public function filters()
{
// return the filter configuration for this controller, e.g.:
return array(
'inlineFilterName',
array(
'class'=>'path.to.FilterClass',
'propertyName'=>'propertyValue',
),
);
}

public function actions()
{
// return external action classes, e.g.:
return array(
'action1'=>'path.to.ActionClass',
'action2'=>array(
'class'=>'path.to.AnotherActionClass',
'propertyName'=>'propertyValue',
),
);
}
 */
}
