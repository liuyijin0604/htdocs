<h3><a href="https://www.airnewzealand.co.nz/international-cargo-track-and-trace" target="_blank">Air New Zealand</a></h3><br />
<?php
if(empty($data->error)):
?>
<div class="grid-view">
<table class="items">
	<tbody><tr>
			<th>Awb</th>
			<th>Origin</th>
			<th>Destination</th>
			<th>Pieces</th>
			<th>Weight</th>
	</tr>
			<tr>
<?php foreach(['awb', 'origin', 'destination', 'pieces', 'weight'] as $k){
	echo '<td>'.$data->details->{$k}.'</td>';
}
?>
			</tr>
</tbody></table>
<br />
		<h4>Booking information</h4>
		<table class="items">
	<tbody><tr>
			<th>Origin</th>
			<th>Destination</th>
			<th>Flight</th>
			<th>Pieces</th>
			<th>Weight</th>
			<th>Status</th>
			<th>Etd</th>
			<th>Eta</th>
	</tr>
<?php
foreach($data->segments as $lag){
	echo '<tr>';
	foreach(['origin', 'destination', 'flight', 'pieces', 'weight', 'status', 'etd', 'eta'] as $k){
	echo '<td>'.$lag->{$k}.'</td>';
	}
	echo '</tr>';
}
?>
</tbody></table>
<br />
		<h4>History</h4>
		<table class="items">
		<tbody>
<?php
foreach($data->history as $log){
	echo '<tr><td>'.$log.'</td></tr>';
}
?>
</tbody></table>
</div>
<?php
else:
	echo '<p style="color:#c00">'.strip_tags($data->error).'</p>';
endif;
?>