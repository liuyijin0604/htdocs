<ul class="table-view lazy-load">
	<li class="table-view-cell">
		<a href="<?=$this->createUrl('job/bulkTask', ['ct' => $ct]);?>" class="cell-action pull-right modal_link" title="Bulk Task <?=$ct?>" data-ignore="push"><span class="icon icon-compose"></span></a>
		<?php
			if (in_array($tasks[0]->type, [1010, 1020, 1030])) {
				$type = 'Pallet In';
			} else if (in_array($tasks[0]->type, [3010, 3020, 3030])) {
				$type = 'Pallet Out';
			}
			echo '<p>' . $ct . ' ' . $type . '</p>';
			echo '<p>' . $tasks[0]->job->customer->name . '</p>';
		?>
	</li>
</ul>