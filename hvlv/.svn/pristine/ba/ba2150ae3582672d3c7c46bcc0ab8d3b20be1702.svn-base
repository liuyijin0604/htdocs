<div class="row grid-view">
<table id="tsk_item" class="items">
<thead><tr>
	<th>number_origin</th>
	<th>number_from</th>
	<th>number_to</th>
	<th>weight</th>
	<th>cbm</th>
	<th>pieces</th>
	<th>address</th>
	<th>suburb</th>
	<th>region</th>
	<th>postcode</th>
	<th>contacter</th>
	<th>mobile</th>
	<th>email</th>
	<th>address_type</th>
</tr></thead>
<tbody>
<?php
$numWeight = 0;
$numPieces = 0;
$numCBM = 0;
foreach($model->mdata['records'] as $i=>$listRecordCols){
	echo '<tr class="'.($i%2==0? 'odd' : 'even').'">';
	echo '<td>'.$listRecordCols['number_origin'] .'</td>';
	echo '<td>'.$listRecordCols['number_from'] .'</td>';
	echo '<td>'.$listRecordCols['number_to'] .'</td>';
	echo '<td>'.$listRecordCols['weight'] .'</td>';
	echo '<td>'.$listRecordCols['cbm'] .'</td>';
	echo '<td>'.sizeof($listRecordCols['postfix']).'/'.$listRecordCols['pieces'];
	if(sizeof($listRecordCols['postfix']) == $listRecordCols['pieces']){
		echo ' ✔';
	}
	echo'</td>';
	echo '<td>'.$listRecordCols['address'] .'</td>';
	echo '<td>'.$listRecordCols['suburb'] .'</td>';
	echo '<td>'.$listRecordCols['region'] .'</td>';
	echo '<td>'.$listRecordCols['postcode'] .'</td>';
	echo '<td>'.$listRecordCols['contacter'] .'</td>';
	echo '<td>'.$listRecordCols['mobile'] .'</td>';
	echo '<td>'.$listRecordCols['email'] .'</td>';
	echo '<td>'.$listRecordCols['address_type'] .'</td>';
	echo '</tr>';
	$numWeight += $listRecordCols['weight'];
	$numPieces += $listRecordCols['pieces'];
	$numCBM += $listRecordCols['cbm'];
}
?>

</tbody>
</table>
</div>

<h1>Total Weight : <?=$numWeight?> KG</h1>
<h1>Total Pieces : <?=$numPieces?></h1>
<h1>Total CBM : <?=$numCBM?></h1>