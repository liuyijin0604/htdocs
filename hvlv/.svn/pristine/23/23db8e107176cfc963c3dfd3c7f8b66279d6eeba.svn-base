<ul class="table-view lazy-load">
<?php if(empty($dp->data)): ?>
	<li class="table-view-cell">No Result</li>
<?php
else:
foreach($dp->data as $r):
?>
	<li class="table-view-cell table-view-cell-full">
	<?php
	echo '<div class="row"><div class="col-8">'. $r->shipment->ref.'</div><div class="col-4">'. sprintf('%d', $r->qty). ' pcs</div></div>';
	?>
	</li>
<?php
endforeach;
endif;
?>
</ul>
