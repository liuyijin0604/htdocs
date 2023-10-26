<table border="1" cellspacing="0" cellpadding="0">
	<?php ksort($products); ?>
	<thead>
		<tr style="background: rgba(100,100,100,0.4);">
			<th>ID</th>
		<?php foreach ($products as $product) {
			echo '<th>' . $product['name'] . '</th>';
		} ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($orders as $order) {
			echo '<tr>';
			echo '<td>' . $order->id . ' - ' . $order->mdata['org'] . '</td>';
			foreach ($products as $index => $product) {
				$flag = 1;
				foreach ($order->items as $item) {
					if ($index == $item->mdata['si']) {
						$flag = 0;
						echo '<td>' . $item->mdata['uq'] . '</td>';
					}
				}
				if ($flag) {
					echo '<td>0</td>';
				}
			}
			echo '</tr>';
		} ?>
	</tbody>
	<tfoot>
		<tr style="background: rgba(100,100,100,0.4);">
			<th>Total</th>
		<?php foreach ($products as $product) {
			echo '<th align="right">' . $product['uq'] . '</th>';
		} ?>
		</tr>
	</tfoot>
</table>