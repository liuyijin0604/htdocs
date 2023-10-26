<?php
$tasks = WmsTask::model()->with('job.customer')->findAll(['condition' => 't.id > 80000 AND t.type = 6010 AND t.status IN (10,31) AND t.is_request = 1 AND (JSON_VALUE(customer.meta, "$.op_id") IN (' . implode(',', WmsTask::$op) . ') OR JSON_VALUE(customer.meta, "$.sp_id") IN (' . implode(',', WmsTask::$op) . '))']);
?>

<ul class="table-view lazy-load">
<?php
if (empty($tasks)): ?>
	<li class="table-view-cell">No task</li>
<?php
else:
foreach ($tasks as $r):
if(empty($r)) continue;
?>
	<li class="table-view-cell adhoc-tasks">
		<?php
		$qty = 0;
		foreach ($r->items as $item) {
			$qty += intval($item->mdata['uq']);
		}
		echo '<p style="width: 100%"><span style="width: 35%; display: inline-block">', $r->getNo(), '</span><span style="width: 65%; display: inline-block">', $r->ref, '</span><br /><span style="width: 65%; display: inline-block">', $r->job->customer->name, '</span><span style="width: 35%; display: inline-block">Due Date: ', date('Y-m-d', strtotime($r->due_time)), '</span><br /><span style="width: 100%; display: inline-block">', $r->getAdhocRequest(), '</span>';
		if ($r->status == 10) {
			echo '<a class="pull-right" href="' . $this->createUrl('site/adhocComplete', ['id' => $r->id, 'status' => 10]) . '"><button class="btn" style="font-size: 0.8em; color:white; background-color:#5cb85c; border: 0">Complete (WIP)</button></a>';
		} else if ($r->status == 31) {
			echo '<a class="pull-right" href="' . $this->createUrl('site/adhocComplete', ['id' => $r->id, 'status' => 31]) . '"><button class="btn" style="font-size: 0.8em; color:white; background-color:#5cb85c; border: 0">Complete (QA)</button></a>';
		}
		echo '</p>';
		?>
	</li>
<?php
endforeach;
endif;
?>
</ul>

<script>
$(function() {
	$('.adhoc-tasks a.pull-right').on('touchend', function() {
		if (!confirm('Are you sure')) {
			return false;
		}
	});
});
</script>