<?php

class WmsTaskManageController extends Controller
{

	public function actionList()
	{
		$this->render('list');
	}

	public function actionObjectGrid()
	{
		if (empty($_POST['WmsTaskObject']['id'])) {
			$object = WmsTaskObject::model()->find('name = :name AND type = :type', [':name' => $_POST['WmsTaskObject']['name'], ':type' => $_POST['WmsTaskObject']['type']]);
			if (empty($object)) {
				$object = new WmsTaskObject;
				$object->attributes = $_POST['WmsTaskObject'];
			}
			$object->status = 1;
			$object->save();
		} else {
			$object = WmsTaskObject::model()->findByPk($_POST['WmsTaskObject']['id']);
			if (!empty($object)) {
				unset($_POST['WmsTaskObject']['id']);
				$object->attributes = $_POST['WmsTaskObject'];
				$object->save();
			}
		}

		$this->ajaxResult($object);
	}

	public function actionObjectGridDelete($id)
	{
		$object = WmsTaskObject::model()->findByPk($id);
		$object->status = 2;
		$object->save();

		$this->ajaxResult($object);
	}

	public function actionObjectGridActive($id)
	{
		$object = WmsTaskObject::model()->findByPk($id);
		$object->status = 1;
		$object->save();

		$this->ajaxResult($object);
	}

	public function actionOperatorSuggest()
	{
		$_GET['term'] = trim($_GET['term']);
		$rs = User::model()->findAll(['condition' => 'fname like :t OR lname like :t', 'params' => array(':t' => '%' . $_GET['term'] . '%'), 'order' => 'id', 'limit' => 20]);

		$a = [];
		foreach ($rs as $r) {
			$a[] = array(
				'value' => $r->id,
				'label' => $r->getName(),
			);
		}

		echo json_encode($a);
	}

	public function actionOperatorGrid()
	{
		if (empty($_POST['WmsTaskOperator']['id'])) {
			$operator = WmsTaskOperator::model()->find('op_id = :op_id', [':op_id' => $_POST['WmsTaskOperator']['op_id']]);
			if (empty($operator)) {
				$operator = new WmsTaskOperator;
				$operator->attributes = $_POST['WmsTaskOperator'];
			}
			$operator->status = 1;
			$operator->save();
		} else {
			$operator = WmsTaskOperator::model()->findByPk($_POST['WmsTaskOperator']['id']);
			if (!empty($operator)) {
				unset($_POST['WmsTaskOperator']['id']);
				$operator->attributes = $_POST['WmsTaskOperator'];
				$operator->save();
			}
		}

		$this->ajaxResult($operator);
	}

	public function actionOperatorGridDelete($id)
	{
		$operator = WmsTaskOperator::model()->findByPk($id);
		$operator->status = 2;
		$operator->save();

		$this->ajaxResult($operator);
	}

	public function actionOperatorGridActive($id)
	{
		$operator = WmsTaskOperator::model()->findByPk($id);
		$operator->status = 1;
		$operator->save();

		$this->ajaxResult($operator);
	}

	public function actionRankOperatorTask($id)
	{
		$operator = WmsTaskOperator::model()->findByPk($id);
		if (empty($_POST)) {
			foreach ($operator->tasks as $task) {
				if (empty($task->mdata['rank'])) {
					$last_rank = WmsTask::model()->find(['condition' => 'op_id = :op_id AND id != :id', 'params' => [':op_id' => $operator->op_id, ':id' => $task->id], 'order' => 'JSON_VALUE(t.meta, "$.rank") DESC']);
					$task->mdata['rank'] = @$last_rank->mdata['rank'] + 1;
					$task->update('meta');
				}
			}

			$this->render('rank_task', ['operator' => $operator]);
		} else {
			if (!empty($_POST['active'])) {
				foreach ($_POST['active'] as $rank => $id) {
					$task = WmsTask::model()->findByPk($id);
					$task->mdata['rank'] = $rank + 1;
					$task->auto_rank = false;
					$task->update('rank');
				}
			}

			$this->ajaxResult($operator);
		}
	}

}