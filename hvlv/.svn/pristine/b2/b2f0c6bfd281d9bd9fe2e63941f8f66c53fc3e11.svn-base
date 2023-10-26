<?php
/*
this controller is used to manage the jobs and tasks
 * which include import goods and manage out putgoods
 */
class TasknvController extends Controller
{

	public function actionBioc()
	{
		$model = WmsJob::model()->find('org_id=:oid and status=10 and week(`created`,1)=:week', array(':oid' => Yii::app()->user->org, ':week' => intval(date('W'))));
		if (empty($model)) {
			$model = new WmsJob();
			$model->org_id = Yii::app()->user->org;
			$model->status = 10;
			$model->type = 90;
			$model->created = date('Y-m-d');
			$model->ref = '3PL_' . date('Y-m-d');
			$model->save();
		}

		$task = new WmsTask();
		$task->unsetAttributes();
		$ec = new CDbCriteria;
		$ec->addCondition('t.type IN (3020,3030)');

		if (isset($_GET['WmsTask'])) {
			$task->attributes = $_GET['WmsTask'];
		}

		// get the orgids and by org_ids
		if (Yii::app()->user->grp != 0) {
			$task->job_ids = Pcaw::jobIds(User::getOrgIds());
		}

		$this->render('list_bio', array('model' => $model, 'task' => $task, 'ec' => $ec, 'type' => 'B2C'));
	}

	public function actionBior()
	{
		$model = WmsJob::model()->find('org_id=:oid and status=10 and week(`created`,1)=:week', array(':oid' => Yii::app()->user->org, ':week' => intval(date('W'))));
		if (empty($model)) {
			$model = new WmsJob();
			$model->org_id = Yii::app()->user->org;
			$model->status = 10;
			$model->type = 90;
			$model->created = date('Y-m-d');
			$model->ref = '3PL_' . date('Y-m-d');
			$model->save();
		}

		$task = new WmsTask();
		$task->unsetAttributes();
		$ec = new CDbCriteria;
		$ec->addCondition('t.type IN (3020,3030)');

		if (isset($_GET['WmsTask'])) {
			$task->attributes = $_GET['WmsTask'];
		}

		// get the orgids and by org_ids
		if (Yii::app()->user->grp != 0) {
			$task->job_ids = Pcaw::jobIds(User::getOrgIds());
		}

		$this->render('list_bio', array('model' => $model, 'task' => $task, 'ec' => $ec, 'type' => 'B2B'));
	}

	public function actionInbound()
	{
		$model = WmsJob::model()->find('org_id=:oid and status=10 and week(`created`,1)=:week', array(':oid' => Yii::app()->user->org, ':week' => intval(date('W'))));
		if (empty($model)) {
			$model = new WmsJob();
			$model->org_id = Yii::app()->user->org;
			$model->status = 10;
			$model->type = 90;
			$model->created = date('Y-m-d');
			$model->ref = '3PL_' . date('Y-m-d');
			$model->save();
		}

		$task = new WmsTask();
		$task->unsetAttributes();
		$ec = new CDbCriteria;
		$ec->addCondition('t.type IN (1010,1020,1030)');

		if (isset($_GET['WmsTask'])) {
			$task->attributes = $_GET['WmsTask'];
		}

		// get the orgids and by org_ids
		if (Yii::app()->user->grp != 0) {
			$task->job_ids = Pcaw::jobIds(User::getOrgIds());
		}

		$this->render('list_inbound', array('model' => $model, 'task' => $task, 'ec' => $ec, 'type' => 'Inbound'));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = WmsJob::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $model;
	}

}
