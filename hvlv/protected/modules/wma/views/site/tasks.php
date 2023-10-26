<ul class="table-view lazy-load">
<?php
if(empty($dp->data)): ?>
	<li class="table-view-cell">No task</li>
<?php
else:
foreach($dp->data as $r):
if(empty($r->actionTask)) continue;
?>
	<li class="table-view-cell">
		<a href="<?=$this->createUrl('job/task', ['id' => $r->actionTask->id]);?>" class="cell-action pull-right modal_link" title="Task <?=$r->job->no.'/'.$r->getNo();?>" data-ignore="push"><span class="icon icon-compose"></span></a>
		<?php
		echo '<p>', $r->job->no, '/', $r->getNo(),' ',$r->getType(), (((empty($r->mdata[Yii::app()->user->id]) || (!empty($r->mdata['req'])) && ($r->mdata[Yii::app()->user->id] < $r->mdata['req'])) && $r->type == 1010 && !empty($r->mdata['pi_cargo'])) ? ' <span class="badge">New</span>' : ''), '</p><p>',$r->job->customer->name,'<br />Ref: ',$r->ref,' &nbsp; Status: ',$r->getStatus(),' &nbsp; Due: ',$r->due_time,'<div class="more">';
		echo '</div>','</p>';
		?>
	</li>
<?php
endforeach;
endif;
?>
</ul>
