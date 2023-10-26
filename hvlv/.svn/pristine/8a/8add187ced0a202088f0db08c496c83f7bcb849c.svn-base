<?php
echo '<b>'.$_GET['info'].'</b><br />';
if(empty($p) || empty($p->pid)){
	echo '<span style="color:#c00">Pallet not found!</span>';
}else{
	if($p->pid > 99) echo '<span style="color:#c00">'.$p->getType().' already at '.$p->parent->code.'</span>';
	$sl = WmsStockLedger::model()->find('location_id = :l', [':l' => $p->id]);
	if(!empty($sl) && !empty($sl->taskItem)){
		echo '<p>'.$sl->taskItem->task->job->no.'/<a href="', $this->createUrl('job/task', ['id' => $sl->taskItem->task->id]),'" class="modal_link" title="Task ', $sl->taskItem->task->job->no.'/'.$sl->taskItem->task->getNo(), '" data-ignore="push">', $sl->taskItem->task->getNo() ,'</a><br />', $sl->taskItem->task->job->customer->name, '<br />';
		
		if($sl->taskItem->task->type == 1010 && $sl->taskItem->task->status < 99){
			$pt = 0;
			$ps = [];
			foreach($sl->taskItem->task->items as $itm){
				foreach($itm->stockLedgers as $l){
					$pt += ($l->loc->pid == 1)? 1 : 0;
					$ps[] = $l->location_id;
				}
			}

			echo '<div class="row"><div class="col-6">Total Pallets: '.sizeof(array_unique($ps)).'</div><div class="col-6">Pending Put Away: '.$pt.'</div></div>';
		}
		echo '</p>';
	}
}
