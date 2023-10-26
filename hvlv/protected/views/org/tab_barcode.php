<div class="grid-view">
<table class="items">
<thead>
<tr>
<th width="100">Name</th>
<th>Address</th>
<th width="330">Barcode</th>
<th width="80">Function</th>
</tr>
</thead>
<tbody>
<tr>
<td valign="top"><?=$model->name;?></td>
<td valign="top"><?=$model->getAddress();?></td>
<td align="center"><img class="barcode" src="<?=$this->createUrl('barcode/draw', array('code' => 'C128', 'text' => 'AGT-'.sprintf('%06d', $model->id).'-PUS', 'height' => 120));?>" width="300" /><br />AGT-<?=sprintf('%06d', $model->id);?>-PUS</td>
<td align="center" valign="top">Pick Up</td>
</tr>
<tr>
<td valign="top"><?=$model->name;?></td>
<td valign="top"><?=$model->getAddress();?></td>
<td align="center"><a href="http://pos.pca168.com/qr/cns/<?=$model->hash;?>"><img class="barcode" src="<?=$this->createUrl('barcode/qr', array('url' => 'http://pos.pca168.com/qr/cns/'.$model->hash));?>" width="300" /></a></td>
<td align="center" valign="top">eConnote QR</td>
</tr>
</tbody>
</table>
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
});
</script>