<ul class="table-view lazy-load">
<?php if(empty($rs)): ?>
	<li class="table-view-cell">No Result</li>
<?php
else:
$r1 = 1 == sizeof($rs);

foreach($rs as $r):
?>
	<li class="table-view-cell">
		<a href="<?=$this->createUrl('prod/update', ['id' => $r->id]);?>" class="cell-action pull-right modal_link" title="Update Product" data-ignore="push"><span class="icon icon-compose"></span></a>
				<?php
				echo $r->name, '<p>',$r->name_zh,'<br />',$r->ean,'<div',($r1? '' : ' class="more"'),'>', $r->brand,' ',$r->model;
				foreach($r->packs as $p){
					echo '<div class="row"><div class="col-6"><a href="', $this->createUrl('prod/carton', ['id' => $r->id, 'pid' => $p->id]), '" class="modal_link" title="Carton Size">', sprintf('%d', $p->qty), '/', $p->getType(),'</a></div><div class="col-6">', $p->barcode,'</div></div>
					<div class="row"><div class="col-6">', $p->weight,'kg</a></div><div class="col-6">', $p->showDim(),'</div></div>';
				}
				echo '<br /><a href="', $this->createUrl('prod/carton', ['id' => $r->id]), '" class="modal_link" title="Carton Size"><span class="icon icon-plus" style="font-size:1em"></span> Carton Barcode</a></div>','</p>';
				?>
	</li>
<?php
endforeach;
endif;
?>
</ul>
