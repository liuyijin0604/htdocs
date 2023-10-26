<?php
$tasks = WmsTask::model()->with('job.customer')->findAll(['condition' => 't.id > 80000 AND t.status IN (20, 30) AND t.is_request = 1 AND (JSON_VALUE(customer.meta, "$.op_id") IN (' . implode(',', WmsTask::$op) . ') OR JSON_VALUE(customer.meta, "$.sp_id") IN (' . implode(',', WmsTask::$op) . '))']);
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
	<li class="table-view-cell">
		<?php
		$qty = 0;
		foreach ($r->items as $item) {
			$qty += intval($item->mdata['uq']);
		}
		echo '<p style="width: 100%"><span style="width: 35%; display: inline-block">', $r->getNo(), '</span><span style="width: 65%; display: inline-block">', $r->ref, '</span><br /><span style="width: 100%; display: inline-block">', $r->job->customer->name, '</span><br /><span style="width: 35%; display: inline-block">SKU: ', count($r->items), ', QTY: ', $qty, '</span><span style="width: 65%; display: inline-block">Due Time: ', $r->due_time, '</span><div class="more">';
		echo '</div>', '</p>';
		?>
	</li>
<?php
endforeach;
endif;
?>
</ul>