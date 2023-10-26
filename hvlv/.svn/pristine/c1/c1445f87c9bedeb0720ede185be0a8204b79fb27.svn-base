<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public $skipAcl = ['auth', 'captcha', 'error', 'logout', 'voice'];

	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha' => array(
				'class' => 'CCaptchaAction',
				'foreColor' => 0xAB2F22,
				'backColor' => 0xEFEFF4,
				'maxLength' => 4,
				'minLength' => 4,
				'width' => 140,
				'height' => 60,
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		if (!empty($_GET)) {
			$data = '';
			if (!empty($_GET['q'])) {
				$_GET['q'] = trim($_GET['q']);
				if (preg_match('/CT/', $_GET['q'])) {
					$ct = preg_replace('/CT/', '', $_GET['q']);
					$wmstasks = WmsTask::model()->findAll('meta like :no', array(':no' => '%CT%' . $ct . '%'));
					foreach ($wmstasks as $i => $wmstask) {
						if (empty($wmstask->mdata['ct'])) {
							unset($wmstasks[$i]);
						}
					}
					if (!empty($wmstasks)) {
						$data .= $this->renderPartial('bulk_tasks', ['ct' => end($wmstasks[0]->mdata['ct']), 'tasks' => $wmstasks], true);
					}
				} else if (preg_match('/CW\d{6}\d{4}/', $_GET['q'])) {
					$cw = substr($_GET['q'], 2, 6);
					$_GET['q'] = 'T' . $cw;
				} else if (preg_match('/ complete/', $_GET['q'])) {
					$_GET['q'] = str_replace(' complete', '', $_GET['q']);
				} else if (explode(' ', $_GET['q'])) {
					$_GET['q'] = explode(' ', $_GET['q'])[0];
				}

				$shipment = ShipmentScan::getShipmentByBarcode($_GET['q']);
				if (!empty($shipment) && preg_match('/^T(\d{6,7})$/i', $shipment->cref, $matches)) {
					$task = WmsTask::model()->findByPk($matches[1]);
					if (!empty($task) && $task->job->org_id == $shipment->agent_id && ((!empty($task->deliveryTask->mdata['shipment_id']) && in_array($shipment->id, $task->deliveryTask->mdata['shipment_id'])) || (!empty($task->deliveryTask->mdata['return_shipment_id']) && in_array($shipment->id, $task->deliveryTask->mdata['return_shipment_id']))) && !empty($task->mdata['restock_task'])) {
						$_GET['q'] = $task->mdata['restock_task'];
					}
				}
			}

			//Author:Nero date:2021/7/5 Description:Allied barcode search
			if (preg_match('/TNW|ZNU/i',$_GET['q'])){
				$tempmodel = WmsTask::model()->find('JSON_VALUE(meta, "$.trackingno") = :id', [':id' => $_GET['q']]);
				$taskno=$tempmodel->link_id;
				$_GET['q'] = $taskno;
			}
			if (preg_match('/0059|0009|2X|AMQ|33EVH|SF|AOE|TSN|TMN|TBN|TAN||TPN|TFN/i',$_GET['q'])){
				$tempmodel = WmsTask::model()->find('JSON_VALUE(meta, "$.trackingNoForScan") = :id',[':id' => $_GET['q']]);
				if(!empty($tempmodel)){
					$taskno=$tempmodel->link_id;
					$_GET['q'] = $taskno;
				}
			}


			$model = new WmsTask('search');
			$model->unsetAttributes();
			$ec = new CDbCriteria;
			$ec->addCondition('t.is_request = 1 AND t.status > 10 AND (t.schd_time IS NULL OR t.schd_time < DATE_ADD(NOW(), INTERVAL 3 DAY))');
			if (empty($_GET['q'])) {
				$ec->addCondition('t.is_request = 1 AND t.status > 10 AND (t.schd_time IS NULL OR t.schd_time < DATE_ADD(NOW(), INTERVAL 3 DAY)) AND op_id = ' . Yii::app()->user->id);
			}
			if (!empty($_GET['type'])) {
				$ec->addCondition('(t.type = :type)');
				$ec->params[':type'] = $_GET['type'];
			}
			if (!empty($_GET['q'])) {
				$ec->addCondition('(t.ref LIKE :r OR t.id LIKE :d) AND t.status <= 99');
				$ec->params[':r'] = '%' . $_GET['q'] . '%';
				$ec->params[':d'] = '%' . ltrim(preg_replace('/[^\d]+/', '', $_GET['q']), '0');
			} else {
				$ec->addCondition('t.status < 99');
			}
			// $ec->order = 't.due_time ASC, t.schd_time DESC, t.id DESC';
			$ec->order = 'JSON_VALUE(t.meta, "$.rank") ASC';
			$pg = empty($_GET['WmsTask_page']) ? 1 : $_GET['WmsTask_page'];
			$dp = $model->search(true, 10, $ec);
			$more_url = $pg * 10 < $dp->totalItemCount ? $this->createUrl('index', ['WmsTask_page' => $pg + 1, 'q' => empty($_GET['q']) ? '' : $_GET['q']]) : '';
			$data .= $this->renderPartial('tasks', ['dp' => $dp], true);

			$data = str_replace('</ul><ul class="table-view lazy-load">', '', $data);
			$data = preg_replace('/<li class="table\-view\-cell">No task<\/li>/', '', $data, 1);

			echo json_encode(['done' => true, 'data' => $data, 'url' => $more_url]);
			return;
		}
		$this->render('index');
	}

	public function actionHistory()
	{
		$model = new WmsTask('search');
		$model->unsetAttributes();
		$ec = new CDbCriteria;
		$ec->addCondition('t.is_request = 1 AND t.status = 99 AND t.type IN (3020,3030) AND JSON_VALUE(t.meta, "$.dpmt") = "3PL"');
		$ec->order = 't.compl_time DESC';
		$dp = $model->search(true, 10, $ec);
		$this->render('history', ['dp' => $dp]);
	}

	public function actionDashboard()
	{
		$this->render('dash');
	}

	public function actionSettings()
	{
		$user = User::model()->findByPk(Yii::app()->user->id);
		if (!empty($_POST)) {
			if (!empty($_POST['wma_settings'])) {
				$user->extra['wma_settings'] = $_POST['wma_settings'];
				$user->update('meta');
			}
			echo json_encode(['done' => true, 'msg' => 'Settings saved.']);
			Yii::app()->end();
		} else {
			$this->render('settings', ['user' => $user]);
		}
	}

	public function actionAdhocTasks()
	{
		$this->render('adhoc_tasks');
	}

	public function actionAdhocComplete($id, $status)
	{
		$model = WmsTask::model()->findByPk($id);
		if ($status == 10) {
			$model->status = 31;
			$model->update('status');
		} else if ($status == 31) {
			$model->status = 32;
			$model->update('status');
		} else if ($status == 32) {
			$model->status = 99;
			$model->update('status');
		}
		$this->render('adhoc_tasks');
	}

	public function actionAuth()
	{
		/*dev login*/
		if (in_array(Yii::app()->getRequest()->serverName, array('localhost', 'alpha'))) {
			$ca = $this->createAction('captcha');
			// $_POST['LoginForm'] = array(
			//     'user' => 'admin@system',
			//     'pwd' => 'password',
			//     'vvc' => $ca->getVerifyCode(),
			// );
		}
		if (isset($_POST['LoginForm'])) {
			$model = new LoginForm;
			$model->attributes = $_POST['LoginForm'];
			if ($model->validate()) {
				$model->login();
			}

		}

		$auth = [];
		if (Yii::app()->user->isGuest) {
			$auth['valid'] = false;
		} else {
			$auth['valid'] = true;
			$auth['name'] = Yii::app()->user->name;
			$auth['rpc'] = Yii::app()->user->rpc;
		}
		echo json_encode($auth);
	}

	/**
	 * Check Session
	 */
	public function actionCheckSession()
	{
		echo time();
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->request->baseUrl);
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if ($error = Yii::app()->errorHandler->error) {
			if (Yii::app()->request->isAjaxRequest) {
				echo $error['message'];
			} else {
				$this->render('error', $error);
			}
		}
	}
}
