<?php

class ContainerCartageController extends Controller
{

	public function actionCreate($org_id, $task_id)
	{
		$model = ContainerCartage::model()->find('task_id = :task_id', [':task_id' => $task_id]);
		if (!empty($model)) {
			$this->actionUpdate($model->id);
			return;
		}

		$model = new ContainerCartage;
		$model->org_id = $org_id;
		$model->task_id = $task_id;

		if (empty($_POST)) {
			$this->render('create', ['model' => $model]);
		} else {
			$model->attributes = $_POST['ContainerCartage'];
			$model->save();
			$this->ajaxResult($model, ['id']);
		}
	}

	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		if (empty($_POST)) {
			if (isset($_GET['tab'])) {
				Acl::hasAccess($this->CaName . '/' . $_GET['tab'], true);
				$this->render('tab_' . $_GET['tab'], ['model' => $model]);
			} else {
				$this->render('update', ['model' => $model]);
			}
		} else {
			$model->attributes = $_POST['ContainerCartage'];
			$model->save();
			$this->ajaxResult($model);
		}
	}

	public function actionList()
	{
		if (empty(Yii::app()->cache->get('container-cartage-status' . session_id()))) {
			Yii::app()->cache->set('container-cartage-status' . session_id(), 1);
		}
		$this->render('list');
	}

	public function actionRenderList()
	{
		if (isset($_GET['status'])) {
			Yii::app()->cache->set('container-cartage-status' . session_id(), $_GET['status']);
		}
		$this->render('_list');
	}

	public function actionComplete($id)
	{
		$model = $this->loadModel($id);
		$model->status = 2;
		$model->update('status');
		$this->ajaxResult($model);
	}

	public function actionCancel($id)
	{
		$model = $this->loadModel($id);
		$model->status = 0;
		$model->update('status');
		$this->ajaxResult($model);
	}

	public function actionPrint($id)
	{
		$model = $this->loadModel($id);
		if (empty($_POST)) {
			$_GET['actab'] = 1;
			$this->render('update', array('model' => $model));
		} else {
			$model->mdata['printed'] = true;
			$model->update('meta');
			$this->ajaxResult($model);
		}
	}

	public function actionBatchCreate($job_id)
	{
		$job = WmsJob::model()->findByPk($job_id);
		$model = new ContainerCartage;
		$model->org_id = $job->org_id;

		if (empty($_POST)) {
			$this->render('batch_create', ['model' => $model]);
		} else {
			if (empty($_POST['qty'])) {
				echo json_encode(['done' => false, 'msg' => 'QTY is empty']);
				Yii::app()->end();
			} else {
				for ($i = 0; $i < $_POST['qty']; $i++) {
					$task = new WmsTask;
					$task->job_id = $job->id;
					$task->type = WmsTask::TYPE_CONTAINER_LOAD;
					$task->status = 20;
					$task->link_id = 0;
					$task->is_request = 1;
					$task->save();

					$ct = new ContainerCartage;
					$ct->attributes = $_POST['ContainerCartage'];
					$ct->task_id = $task->id;
					$ct->save();
				}

				$this->ajaxResult($model);
			}
		}
	}

	public function loadModel($id)
	{
		$model = ContainerCartage::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $model;
	}

}