<ul class="table-view lazy-load">
<?php if(empty($dp->data)): ?>
	<li class="table-view-cell">No Result</li>
<?php
else:
foreach($dp->data as $r):
?>
	<li class="table-view-cell">
				<?php
				echo $r->getType(), '/', $r->no, ' ', $r->getStatus(), '<p>',$r->customer->name,'<br />Ref: ',$r->ref,' &nbsp; PO: ', $r->po,'<div class="more">';
				foreach($r->tasks as $p){
					if($p->is_request > 0) continue;
					if($p->status <= 10 || $p->status >99) continue;
					echo '<div class="row"><div class="col-3">', $p->getNo(), '</div><div class="col-3">', $p->getType(), '</div><div class="col-3">', $p->getStatus(),'</div><div class="col-3"><a href="', $this->createUrl('job/task', ['id' => $r->id]),'" class="cell-action modal_link" title="Task ',$r->no,'/',$p->getNo(),'" data-ignore="push"><span class="icon icon-compose"></span></a></div></div>';
				}
				echo '</div>','</p>';
				?>
	</li>
<?php
endforeach;
endif;
?>
</ul>
