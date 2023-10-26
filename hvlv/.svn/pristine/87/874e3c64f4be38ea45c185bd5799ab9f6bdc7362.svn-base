<?php

class QuotesController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', // allow all users to perform 'index' and 'view' actions
				'actions' => array('index', 'view'),
				'users' => array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions' => array('create', 'update'),
				'users' => array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions' => array('admin', 'delete'),
				'users' => array('admin'),
			),
			array('deny', // deny all users
				'users' => array('*'),
			),
		);
	}

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
		$model = new Quotes;
		$modelR['route'] = new Route;

		$airline_list = Quotes::airlineList();
		$first_airline_id = 0;
		foreach ($airline_list as $k => $airline) {
			$first_airline_id = $k;
			break;
		}
		$modelR['alist'] = $first_airline_id;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Quotes'])) {
			//  $model->attributes=$_POST['Quotes'];
			$id = '';
			//check if already exist the route, if exist get the id for the related quotes
			if (!empty($_POST['Route']['flight_no'])) {
				$flight_no = $_POST['Route']['flight_no'];
			} else if (!empty($_POST['flight'])) {
				$flight_no = ' ' . implode(' ', $_POST['flight']) . ' ';
			}
			$routeExist = Route::model()->find('flight_no=:flight_no AND code= :code And departure like :departure AND airline_id=:airline_id',
				array(':flight_no' => $flight_no, ':code' => explode('-', $_POST['Route']['destination'])[0],
					':departure' => empty(explode('-', $_POST['Route']['departure'])[1]) ? explode('-', $_POST['Route']['departure'])[0] : explode('-', $_POST['Route']['departure'])[1],
					':airline_id' => $_POST['airline_name']));

			if (!empty($routeExist)) {
				$id = $routeExist->id;
			} else {
				$modelR['route']->attributes = $_POST['Route'];
				$modelR['route']->airline_id = $_POST['airline_name'];
				$modelR['route']->status = '1';
				$modelR['route']->destination = empty(explode('-', $_POST['Route']['destination'])[1]) ? explode('-', $_POST['Route']['destination'])[0] : explode('-', $_POST['Route']['destination'])[1];
				$modelR['route']->code = explode('-', $_POST['Route']['destination'])[0];
				$modelR['route']->departure = empty(explode('-', $_POST['Route']['departure'])[1]) ? explode('-', $_POST['Route']['departure'])[0] : explode('-', $_POST['Route']['departure'])[1];
				$modelR['route']->deptime = @$_POST['Route']['deptime'];
				if (!empty($_POST['deptime'])) {
					$modelR['route']->mdata['deptimes'] = implode(' ', $_POST['deptime']);
				}
				if (!empty($_POST['flight'])) {
					$modelR['route']->flight_no = ' ' . implode(' ', $_POST['flight']) . ' ';
				}

				if ($modelR['route']->save()) {
					$id = $modelR['route']->id;
				}
			}

			if ($_POST['uld_type'] == 'pallet') {
				$range = $_POST['range'];
				$price = $_POST['price'];
				$sec_fuel = $_POST['sec_fuel'];

				$temp = $range;
				asort($temp);
				$temp = array_values($temp);
				if ($temp != $range) {
					$range = array_values(array_reverse($range));
					$price = array_values(array_reverse($price));
					$sec_fuel = array_values(array_reverse($sec_fuel));
				}

				$n = sizeof($range);
				for ($i = 0; $i < $n; $i++) {
					$modelQ = new Quotes;
					$modelQ->attributes = $_POST['Quotes'];
					$modelQ->route_id = $id;
					$modelQ->uld_type = $_POST['uld_type'];
					$modelQ->wt_lo = $range[$i];
					$modelQ->wt_hi = empty($range[$i + 1]) ? null : $range[$i + 1];
					$modelQ->pkg = $price[$i];
					$modelQ->sec_fuel = $sec_fuel[$i];
					$quotesExist = Quotes::model()->find('wt_lo=:wt_lo AND (wt_hi=:wt_hi OR wt_hi is NULL)AND route_id=:route_id AND uld_type =:uld_type And date_eff =:date_eff AND date_exp=:date_exp',
						array(':route_id' => $modelQ->route_id, ':uld_type' => $modelQ->uld_type, ':date_eff' => $modelQ->date_eff, ':date_exp' => $modelQ->date_exp, ':wt_lo' => $modelQ->wt_lo, ':wt_hi' => $modelQ->wt_hi));
					if (!$quotesExist) {
						$modelQ->save();
					}
				}

			} else {
				$modelQ = new Quotes;
				$modelQ->attributes = $_POST['Quotes'];
				$modelQ->route_id = $id;
				$modelQ->uld_type = $_POST['uld_type'];
				$modelQ->wt_lo = 0;
				$modelQ->wt_hi = 0;
				$quotesExist = Quotes::model()->find('wt_lo=:wt_lo AND (wt_hi=:wt_hi OR wt_hi is NULL)AND route_id=:route_id AND uld_type =:uld_type And date_eff =:date_eff AND date_exp=:date_exp',
					array(':route_id' => $modelQ->route_id, ':uld_type' => $modelQ->uld_type, ':date_eff' => $modelQ->date_eff, ':date_exp' => $modelQ->date_exp, ':wt_lo' => $modelQ->wt_lo, ':wt_hi' => $modelQ->wt_hi));
				if (!$quotesExist) {
					$modelQ->save();
				}
			}
			$this->ajaxResult($model);
			$this->ajaxResult($modelQ);
		}

		$this->render('create', array(
			'model' => $model,
			'modelR' => $modelR,
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

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Quotes'])) {
			$model->attributes = $_POST['Quotes'];
			if ($model->save()) {
				$this->redirect(array('view', 'id' => $model->id));
			}

			$this->ajaxResult($model);
		}

		$modelR['route'] = $model->route;
		$airline_list = Quotes::airlineList();
		$first_airline_id = 0;
		foreach ($airline_list as $k => $airline) {
			$first_airline_id = $k;
			break;
		}
		$modelR['alist'] = $first_airline_id;

		$this->render('update', array(
			'model' => $model,
			'modelR' => $modelR,
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider = new CActiveDataProvider('Quotes');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionImport()
	{

		$this->render('import_quotes', array());

	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model = new Quotes('search');
		$model->unsetAttributes(); // clear any default values
		if (isset($_GET['Quotes'])) {
			$model->attributes = $_GET['Quotes'];
		}
		$this->render('admin', array(
			'model' => $model,
		));
	}
	public function actionAjaxSaveQuotes()
	{

		$resp = array('success' => 1, 'msg' => 'import successfully');

		$template_file = empty($_FILES['quotes-file']) ? array() : $_FILES['quotes-file'];
		if (empty($template_file['tmp_name']) || !is_uploaded_file($template_file['tmp_name'])) {
			$resp['msg'] = 'Invalid template file';
//            echo json_encode($resp);
			return;

		} else {
			$xls = new oExcel;
			$xls->load($template_file['tmp_name']);
			$data = $xls->getAll();

		}
		// import the data to save
		$this->importQuotesToSave($data);
		echo json_encode($resp);

	}

	private function importQuotesToSave($data)
	{
		$index = 0;
		foreach ($data as $row) {
			$index++;
			if (empty($row[2])) {
				continue;
			}

			if (preg_match('/departure/', strtolower($row[1]))) {
				$range = [];
				if (!empty($data[$index + 1])) {
					for ($i = 13; $i <= sizeof($row); $i++) {
						if (strlen($row[$i]) == 0) {
							continue;
						}

						$range[] = $row[$i];
					}
					$range[0] = '0';
				}
				continue;
			}
			if (strlen($row[1]) != 0) {
				$departure = $row[1];
				$departure = explode('-', $departure);

				if (strlen($departure[0]) == 3) {

					$departure[0] = $this->findUncode($departure[0]);
				}

			}

			// check if the airline already exist, if exist, get id. if not ,create the new airline
			$airlineExist = Airline::model()->find('name=:name OR code=:code', array(':name' => $row[3], ':code' => $row[3]));
			if (empty($airlineExist)) {
				$modelA = new Airline;
				$modleA->name = $row[3];
				$modleA->code = $row[3];
				$modleA->save();
				$id = $modleA->id;
			} else {
				$id = $airlineExist->id;
			}
//
			$destination = $row[2];
			$destination = explode('/', $destination);
			$m = 0;
			foreach ($destination as $des) {
				$destination[$m] = explode('-', $des);
				$m++;
			}
//
			foreach ($destination as $des) {
				if (strlen($des[0]) == 3) {
					$des[1] = $des[0];
					$des[0] = empty($this->findUncode($des[1])) ? $des[0] : $this->findUncode($des[1]);
				} elseif (empty($des[1])) {
					$des[1] = $des[0];
				}

				Route::model()->updateAll(array('stops' => $row[5], 'days' => $row[6], 'info' => $row[10], 'cca_charge' => $row[8], 'security_fuel' => $row[9], 'awb_p' => $row[7]),
					'flight_no=:flight_no AND destination like :destination And departure like :departure AND airline_id=:airline_id',
					array(':flight_no' => $row[4], ':destination' => "$des[0]%", ':departure' => "$departure[0]%", ':airline_id' => $id));
				$routeExist = Route::model()->find('flight_no=:flight_no AND destination like :destination And departure like :departure AND airline_id=:airline_id',
					array(':flight_no' => $row[4], ':destination' => "$des[0]%", ':departure' => "$departure[0]%", ':airline_id' => $id));

				if ($routeExist) {
					$routeid = $routeExist->id;

				} else {
					$modelR = new Route;
					$modelR->airline_id = $id;
					$modelR->flight_no = $row[4];
					$modelR->status = '1'; //default 1;
					$modelR->destination = $des[0];
					$modelR->code = $des[1];
					$modelR->departure = $departure[0];
					$modelR->stops = $row[5];
					$modelR->days = $row[6];
					$modelR->info = $row[10];
					$modelR->cca_charge = $row[8];
					$modelR->awb_p = $row[7];
					$modelR->security_fuel = $row[9];
					$modelR->save();
					$routeid = $modelR->id;

				}
				$n = sizeof($range);

				for ($i = 0; $i < $n; $i++) {
					$modelQ = new Quotes;
					$modelQ->route_id = $routeid;
					if (is_numeric($range[$i])) {
						$modelQ->uld_type = 'pallet';
						$modelQ->wt_lo = $range[$i];
						$modelQ->wt_hi = (empty($range[$i + 1]) || !is_numeric($range[$i + 1]) || empty($row[$i + 14])) ? null : $range[$i + 1];
						$modelQ->pkg = $row[$i + 13];
						$modelQ->min = $row[12];
					} else {
						$modelQ->uld_type = $range[$i];
						$modelQ->wt_lo = 0;
						$modelQ->wt_hi = 0;
						$modelQ->pkg = 0;
						$modelQ->min = $row[13 + $i];
					}
					preg_match_all('/(\d{4}-\d{2}-\d{2})/', $row[11], $date_input);
					$modelQ->date_eff = empty($date_input[0][0]) ? date('Y-m-d') : $date_input[0][0];
					$modelQ->date_exp = empty($date_input[0][1]) ? date('Y-m-d', strtotime(date('Y-m-d') . ' + 1 year')) : $date_input[0][1];

					Quotes::model()->updateAll(array('pkg' => $modelQ->pkg, 'min' => $modelQ->min),
						'wt_lo=:wt_lo AND wt_hi=:wt_hi AND route_id=:route_id AND uld_type =:uld_type And date_eff =:date_eff AND date_exp=:date_exp',
						array(':route_id' => $modelQ->route_id, ':uld_type' => $modelQ->uld_type, ':date_eff' => $modelQ->date_eff, ':date_exp' => $modelQ->date_exp, ':wt_lo' => $modelQ->wt_lo, ':wt_hi' => $modelQ->wt_hi));

					$quotesExist = Quotes::model()->find('wt_lo=:wt_lo AND (wt_hi=:wt_hi OR wt_hi is NULL)AND route_id=:route_id AND uld_type =:uld_type And date_eff =:date_eff AND date_exp=:date_exp',
						array(':route_id' => $modelQ->route_id, ':uld_type' => $modelQ->uld_type, ':date_eff' => $modelQ->date_eff, ':date_exp' => $modelQ->date_exp, ':wt_lo' => $modelQ->wt_lo, ':wt_hi' => $modelQ->wt_hi));
					if (!$quotesExist) {
						$modelQ->save();
					}
				}
			}
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Quotes the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = Quotes::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Quotes $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'quotes-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	private function findUncode($code)
	{
		$rs = Unloco::model()->find(array('condition' => 'port like :port', 'params' => array(':port' => $code)));
		if (!empty($rs)) {
			return explode(' ', $rs->name)[0] . (empty(explode(' ', $rs->name)[1]) || explode(' ', $rs->name)[1] != 'Kong' ? '' : ' ' . explode(' ', $rs->name)[1]);
		} else {
			return $code;
		}

	}

	public function actionSearch()
	{
		$rs = Unloco::model()->findAll(array(
			'condition' => '(flag=2 or flag=3) AND  (country like "CN" OR country like "AU" OR country like "HK") AND (port LIKE :n OR name LIKE :n)',
			'params' => array(':n' => '%' . $_GET['term'] . '%'),
			'limit' => 20, 'together' => true,
		));
		$a = array();

		foreach ($rs as $r) {
			$a[] = array(
				'value' => $r->port . '-' . explode(' ', $r->name)[0] . (empty(explode(' ', $r->name)[1]) || explode(' ', $r->name)[1] != 'Kong' ? '' : ' ' . explode(' ', $r->name)[1]),

			);
		}
//                var_dump($a);
		echo json_encode($a);
	}

}
