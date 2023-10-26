<div class="form">
<?php
if(!empty($err)){
	echo '<span style="color:#c00">'.implode('<br />', $err).'</span>';
}else{
if(!empty($rs[0])){
	echo '<b>'.$rs[0]->stock->prod->name.'</b>';
}
?>
<form class="reloc-data-form" action="<?=$this->createUrl('job/relocEntry');?>" method="post">
<?php
foreach($rs as $i=>$r):
if($r->qty == 0) continue;
?>
<div class="row" style="border-top: 1px solid #aaa; padding-top: 5px;">
	<div class="col-6"><p style="font-size:1em;margin-bottom: 0;">Exp: <?=$r->stock->expiry;?><br />Bat: <?=$r->stock->batch;?></p></div>
	<div class="col-6"><input class="mvq" type="number" placeholder="Qty" name="mv[<?=$r->id;?>]" style="width: 60px" data-stock="<?=$r->stock->stockName();?>" /> / <b><?=sprintf('%d', $r->qty);?></b>
	</div>
</div>
<?php endforeach; ?>
<input type="hidden" name="tloc" value="<?=$pt->id;?>" />
<button type="submit" class="btn btn-primary btn-block">Save</button>
</form>
<?php } ?>
</div>
