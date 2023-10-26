<?php

class ItController extends Controller
{
	protected $nonAjax = ['checkApplicationLog','saveSystemSetting'];
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view', [
			'model'=>$this->loadModel($id),
		]);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCheckApplicationLog()
	{
		$data = file_get_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'application.log');
		$this->render('check_application_log', [
			'data'=>$data,
		]);
	}

	public function actionCheckApiLog()
	{
		$data = '';
		if (!empty($_POST)) {
			$date = date('Y-m-d', strtotime($_POST['date']));
			switch($_POST['api']) {
				case 'auspost':
					$data = file_get_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'auspost'.DIRECTORY_SEPARATOR.'auspost_api_'.$date.'.log');
					break;
				case 'mytoll':
					$data = file_get_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'mytoll'.DIRECTORY_SEPARATOR.'mytoll_api_ELabel' . $date . '.log');
					break;
				case 'tnt':
					$data = file_get_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'tntapi' . DIRECTORY_SEPARATOR.'tnt_api.log');
					break;
				case 'tla-JAVA':
					$data = file_get_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'tla' . DIRECTORY_SEPARATOR . 'tla_api_java'. $date . '.log');
					break;
				case 'xero':
					$data = file_get_contents(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'xero' . DIRECTORY_SEPARATOR . 'xero_api_' . $date . '.log');
					break;
				case 'shipment':
					$data = file_get_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'shipmentapi'.DIRECTORY_SEPARATOR.'shipment_api_' . $date . '.log');
					break;
				case 'ubi':
					$data = file_get_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'UBI' . DIRECTORY_SEPARATOR . 'ubi_api_ELabel' . $date . '.log');
					break;
				case 'etower':
					$data = file_get_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'ETOWER' . DIRECTORY_SEPARATOR . 'etower_api_ELabel'. $date . '.log');
					break;
				case 'border':
					$data = file_get_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'MYBORDER' . DIRECTORY_SEPARATOR . 'MYBORDER_api_ELabel' . $date . '.log');
					break;
				case 'yto':
					$data = file_get_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'yto_global_api' . DIRECTORY_SEPARATOR.'yto_global_api_'  . $date . '.log' );
					break;
			}
			echo $data;
		} else {
			$this->render('check_api_log', ['data' => $data]);
		}
		
	}

	public function actionConnoteRangelist()
	{
		$criteria = new CDbCriteria();
		$filtersForm=new FiltersForm;
		$courierids = "'3466','101','3752','115','4429','3447','3079'";
		// $shipmentHbnStr = "'TMN1474027968','TMN1474028042','TSN1474027884'";
		$criteria->addCondition(" fid in ({$courierids}) ");
		$ConnoteRanges = ConnoteRange::model()->findAll($criteria);

		$filteredData=$filtersForm->filter($ConnoteRanges);
		$dataprovider=new CArrayDataProvider($filteredData);
		$dataprovider->pagination=['pageSize' =>30];

		$this->render('connote_range_list', array('dataprovider' => $dataprovider, 'filtersForm' => $filtersForm));
	}

	/**
	 * Lists and search.
	 */
	public function actionSystemSettingList()
	{
		$model=new SystemSetting('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['SystemSetting']))
			$model->attributes=$_GET['SystemSetting'];

		$this->render('system_setting_list',array(
			'model'=>$model,
		));
	}

	public function actionCreateSystemSetting()
	{
		$model=new SystemSetting();

		$this->render('system_setting_list_update',array(
			'model'=>$model,'tabid'=>$_GET['tabid']
		));
	}

	public function actionUpdateSystemSetting($id)
	{
		$model=SystemSetting::model()->findByPk($id);

		$this->render('system_setting_list_update',array(
			'model'=>$model,'tabid'=>$_GET['tabid']
		));
	}

	public function actionSaveSystemSettingLine($id)
	{
		if(empty($id)){
			$model=new SystemSetting();
		}
		else{
			$model=SystemSetting::model()->findByPk($id);
		}		
		// print_r($model);
		// Yii::app()->end();
		$model->key=$_POST['SystemSetting']['key'];
		$model->mdata=json_decode($_POST['SystemSetting']['meta']);
		$model->meta = json_encode($model->mdata);
		$model->uid=User::getCurrentUser()->id;
		$model->date=date('Y-m-d H:i:s', time());
		if ($model->save()) {
			Yii::app()->cache->delete($model->key);
			Yii::app()->cache_java->delete($model->key);
			echo json_encode(['done'=>true,'msg'=>'Process Done']);
		}
		else{
			echo json_encode(['done'=>false,'msg'=>'Not Done']);
		}
	}

	public function actionDeleteSystemSettingLine($id)
	{
		$model=SystemSetting::model()->findByPk($id);
		$key = $model->key;
		
		if ($model->delete()) {
			Yii::app()->cache->delete($key);
			Yii::app()->cache_java->delete($key);
			echo json_encode(['done'=>true,'msg'=>'Process Done']);
		}
		else{
			echo json_encode(['done'=>false,'msg'=>'Not Done']);
		}
	}

	public function actionCommand()
	{
		if (empty($_POST)) {
			$this->render('command'); 
		} else {
			$commandName = $_POST['command'];
			$function = $_POST['function'];
			$arguments = $_POST['arguments'];
			$commandArgs = [];
			array_push($commandArgs, $function);
			array_push($commandArgs, $arguments);
			Yii::import('application.commands.*');
			switch ($commandName) {
				case 'cron':
					$command = new CronCommand('cron', 'runner');
					break;
				case 'az':
					$command = new azCommand('az', 'runner');
					break;
				case 'ajf':
					$command = new ajfCommand('ajf', 'runner');
					break;
				case 'db':
					$command = new DBCommand('db', 'runner');
					break;
				case 'dxt':
					$command = new DxtCommand('dxt', 'runner');
					break;
				case 'emailPipe':
					$command = new EmailPipeCommand('emailPipe', 'runner');
					break;
				case 'fl':
					$command = new flCommand('fl', 'runner');
					break;
				case 'gz':
					$command = new gzCommand('gz', 'runner');
					break;
				case 'ics':
					$command = new icsCommand('ics', 'runner');
					break;
				case 'iExp':
					$command = new iExpCommand('iExp', 'runner');
					break;
				case 'jr':
					$command = new jrCommand('jr', 'runner');
					break;
				case 'jrf':
					$command = new jrfCommand('jrf', 'runner');
					break;
				case 'jyg':
					$command = new jygCommand('jyg', 'runner');
					break;
				case 'mq':
					$command = new MQCommand('mq', 'runner');
					break;
				case 'pl':
					$command = new plCommand('pl', 'runner');
					break;
				case 'ray':
					$command = new rayCommand('ray', 'runner');
					break;
				case 'thread':
					$command = new ThreadCommand('thread', 'runner');
					break;
				case 'tracking':
					$command = new trackingCommand('tracking', 'runner');
					break;
				case 'ubm':
					$command = new UbmCommand('ubm', 'runner');
					break;
				case 'xero':
					$command = new XeroCommand('xero', 'runner');
					break;
				case 'yx':
					$command = new YXCommand('yx','runner');
					break;
				default:
					$command = new CronCommand('cron', 'runner');
			}
			
			$command->run($commandArgs);
		}	
	}

											
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=ImParcel::model()->findByPk($id);
		if ($model===null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}
}


