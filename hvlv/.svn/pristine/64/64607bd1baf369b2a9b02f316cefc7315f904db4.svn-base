<div class="grid-view">
	<table class="items">
		<thead>
			<tr>
				<th></th>
				<th colspan="6">In</th>
				<th colspan="7">Out</th>
			</tr>
			<tr>
				<th width="100">Date</th>
				<th width="100">Scheduled Task</th>
				<th width="100">Completed Task</th>
				<th width="80">%</th>
				<th width="100">Scheduled Unit</th>
				<th width="100">Completed Unit</th>
				<th width="80">%</th>
				<th width="100">Scheduled Task</th>
				<th width="100">Completed Task</th>
				<th width="80">%</th>
				<th width="100">Scheduled Unit</th>
				<th width="100">Completed Unit</th>
				<th width="80">%</th>
				<th width="100">Completed Weight</th>
			</tr>
		</thead>
		<tbody style="font-size: 18px; text-align: right">
			<?php
			$from = !empty($_GET['from']) ? date('Y-m-d', strtotime($_GET['from'])) : '2020-01-01';
			$to = !empty($_GET['to']) ? date('Y-m-d', strtotime($_GET['to'])) : date('Y-m-d');
			echo '<input type="hidden" id="from_hidden" value="' . $from . '">';
			echo '<input type="hidden" id="to_hidden" value="' . $to . '">';
			$list = WmsDashboard::model()->findAll(['condition' => 'date >= :from AND date <= :to', 'params' => [':from' => $from, ':to' => $to], 'order' => 'date DESC']);
			foreach ($list as $item) { ?>
			<tr>
				<td style="text-align: left"><?=$item->date?></td>
				<td><?=$item->in_schd_task?></td>
				<td><?=$item->in_compl_task?></td>
				<td><?=round($item->in_compl_task / max(1, $item->in_schd_task) * 100, 2) . '%'?></td>
				<td><?=$item->in_schd_unit?></td>
				<td><?=$item->in_compl_unit?></td>
				<td><?=round($item->in_compl_unit / max(1, $item->in_schd_unit) * 100, 2) . '%'?></td>
				<td><?=$item->out_schd_task?></td>
				<td><?=$item->out_compl_task?></td>
				<td><?=round($item->out_compl_task / max(1, $item->out_schd_task) * 100, 2) . '%'?></td>
				<td><?=$item->out_schd_unit?></td>
				<td><?=$item->out_compl_unit?></td>
				<td><?=round($item->out_compl_unit / max(1, $item->out_schd_unit) * 100, 2) . '%'?></td>
				<td><?=$item->out_compl_weight . ' kg'?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>