<?php
$log = new Log;
$log->model = get_class($model);
$log->type = 6;
// $log->lid = $model->id;
$tasks = $model->mainTask->subTasks;
$ids = [$model->mainTask->id];
$task_types = [$model->mainTask->id => 'Main'];
foreach ($tasks as $task) {
	$ids[] = $task->id;
	$task_types[$task->id] = $task->getType();
	if ($task->getType() == 'Request') {
		$users[$task->id] = !empty($model->mainTask->op_id) ? User::model()->findByPk($model->mainTask->op_id)->getName() : '';
	} else if ($task->getType() == 'Process') {
		$users[$task->id] = !empty($model->mainTask->mdata['proc_id']) ? User::model()->findByPk($model->mainTask->mdata['proc_id'])->getName() : '';
	} else if ($task->getType() == 'QA') {
		$users[$task->id] = !empty($model->mainTask->mdata['qa_id']) ? User::model()->findByPk($model->mainTask->mdata['qa_id'])->getName() : '';
	}
}
$log->lid = $ids;

$fr = new FileRepo('search');
$fr->unsetAttributes();
if(empty($_GET['FileRepo'])){
	$fr->status = 20;
	$fr->theTypes = [84];
}else{
	$fr->attributes=$_GET['FileRepo'];
	$fr->type = 84;
}
$fr->fid = $ids;

$transactions = [];
foreach ($log->search()->getData() as $data) {
	$transactions[] = [
		$data->time,
		$data->getUser(),
		'Notes',
		$task_types[$data->lid],
		$data->getExtra(),
	];
}
foreach ($fr->search()->getData() as $data) {
	$transactions[] = [
		$data->date,
		$users[$data->fid],
		'Files',
		$task_types[$data->fid],
		"<a href=\"" . Yii::app()->baseUrl . "/filerepo/" . $data->hash . "/" . $data->name . "\" target=\"_blank\">" . $data->name . "</a>",
	];
}

usort($transactions, function($a, $b) {
	return strtotime($a[0]) < strtotime($b[0]);
});
?>

<div class="grid-view">
	<table class="items">
		<thead>
			<tr>
				<th style="width: 15%">Time</th>
				<th style="width: 10%">User</th>
				<th style="width: 10%">Type</th>
				<th style="width: 10%">Tab</th>
				<th style="width: 55%">Detail</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($transactions)) { ?>
				<tr class="odd"><td colspan="5" class="empty">No results found.</td></tr>
			<?php } ?>
			<?php foreach ($transactions as $k => $item) { ?>
			<tr class="<?=$k % 2 == 0 ? 'odd' : 'even'?>">
				<td><?=$item[0]?>
				<td><?=$item[1]?>
				<td><?=$item[2]?>
				<td><?=$item[3]?>
				<td><?=str_replace("\n", '<br>', $item[4])?>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>