<?php if (empty($inv->mdata['loc'])) { ?>
<table width="100%" cellspacing="0" class="chart" style="font-size:0.8em">
	<thead>
		<tr style="background: rgba(100,100,100,0.4);">
			<th align="left" width="100">Job No.</th>
			<th align="left" width="100">Date</th>
			<th align="left">Description</th>
			<th align="right" width="100">Amount</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$i = 0;
	// foreach($inv->lines as $si => $il){
	$listInvLine = InvLine::model()->findAll('inv_id = :inv_id',[':inv_id'=>$inv->id]);
	foreach($listInvLine as $si => $il){
		if($il->ccode == WmsOrgQuote::QUOTE_PALLET_STORAGE_WEEK){
			foreach($il->mdata['items'] as $itm){
				if (empty($itm[3] * $itm[2])) {
					continue;
				}
				echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td valign="top">Storage</td><td valign="top">'.$inv->mdata['billto'].'</td><td>'.($itm[0]. ($itm[1] ? ' x '.$itm[1] : ''). ' (Units: '.$itm[2].')'.(!empty($itm[4])?' Oversize':'')).'</td><td valign="top" align="right">'.AppHelper::money_format('%i', $itm[3] * $itm[2])."</td></tr>\n";
				$i++;
			}
		}
	}
	?>
		<tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
	</tbody>
	<tfoot>
	</tfoot>
</table>
<?php } else { ?>
<table width="100%" cellspacing="0" class="chart" style="font-size:0.8em">
	<thead>
		<tr style="background: rgba(100,100,100,0.4);">
			<th align="left">Product</th>
			<th align="left" width="100">Expiry</th>
			<th align="left" width="100">Batch</th>
			<th align="left" width="100">Pallet</th>
			<th align="left" width="100">Qty</th>
			<th align="left" width="100">In Date</th>
			<th align="left" width="100">Out Date</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$i = 0;
	foreach ($inv->lines as $si => $il) {
		if ($il->ccode == WmsOrgQuote::QUOTE_PALLET_STORAGE_WEEK) {
			$offset = 0;
			foreach ($il->mdata['items'] as $item) {
				if (empty($item[3] * $item[2])) {
					continue;
				}

				$expiry = '';
				if (preg_match('/Exp: (\d{4}\-\d{2}\-\d{2})/', $item[0], $matches)) {
					$expiry = $matches[1];
				}
				$batch = '';
				if (preg_match('/Bat: (.*)\)/', $item[0], $matches)) {
					$batch = $matches[1];
				}
				$product = str_replace('Exp: ' . $expiry, '', $item[0]);
				$product = str_replace('Bat: ' . $batch, '', $product);
				$product = str_replace('(, )', '', $product);
				$product = str_replace('()', '', $product);
				$product = trim($product);

				$prod = WmsProd::model()->find('name = :name AND status = 1', [':name' => $product]);
				if (empty($prod)) {
					$prod = WmsProd::model()->find('name LIKE :name AND status = 1', [':name' => '%' . $product . '%']);
				}

				$plts = array_slice($inv->mdata['loc'], $offset, intval($item[2]));
				$offset += intval($item[2]);
				foreach ($plts as $plt) {
					$loc = WmsLocation::model()->findByPk($plt);

					$wsl = WmsStockLocation::model()->with('stock')->find('stock.prod_id = :prod_id AND stock.org_id = :org_id AND t.location_id = :lid AND t.qty > 0', [':prod_id' => $prod->id, ':lid' => $plt, ':org_id' => !empty($inv->mdata['suborg']) ? $inv->mdata['suborg'] : $inv->to_id]);
					if (empty($wsl)) {
						$wsl = WmsStockLocation::model()->with('stock')->find('stock.prod_id = :prod_id AND stock.org_id = :org_id AND location_id = :lid', [':prod_id' => $prod->id, ':lid' => $plt, ':org_id' => !empty($inv->mdata['suborg']) ? $inv->mdata['suborg'] : $inv->to_id]);
					}
					$qty = $wsl->qty;
					$wsls = WmsStockLedger::model()->with('stock')->findAll('stock.prod_id = :prod_id AND stock.org_id = :org_id AND location_id = :lid AND qty_out > 0 AND ts >= :from', [':prod_id' => $prod->id, ':org_id' => !empty($inv->mdata['suborg']) ? $inv->mdata['suborg'] : $inv->to_id, ':lid' => $plt, ':from' => date('Y-m-d 00:00:00', strtotime($inv->mdata['billfrom']))]);
					foreach ($wsls as $wsl) {
						$qty += $wsl->qty_out;
					}

					$in_date = '';
					$in = WmsStockLedger::model()->with('stock')->find(['condition' => 'stock.prod_id = :prod_id AND stock.org_id = :org_id AND t.qty_in > 0 AND t.location_id = :location_id', 'params' => [':prod_id' => $prod->id, ':org_id' => !empty($inv->mdata['suborg']) ? $inv->mdata['suborg'] : $inv->to_id, ':location_id' => $plt], 'order' => 'ts ASC']);
					if (!empty($in)) {
						$in_date = date('Y-m-d', strtotime($in->ts));
					}

					$out_date = '';
					$remain = false;
					foreach ($loc->stocks as $stock) {
						if ($stock->qty > 0) $remain = true;
					}
					if (!$remain) {
						$last = WmsStockLedger::model()->find(['condition' => 't.location_id = :location_id AND t.qty_out > 0', 'params' => [':location_id' => $plt], 'order' => 't.ts DESC']);
						if (!empty($last) && strtotime($last->ts) < strtotime($inv->mdata['billto']) + 86400) {
							$out_date = date('Y-m-d', strtotime($last->ts));
						}
					}

					echo '<tr class="' . ($i%2 == 1 ? 'even' : 'odd') . '"><td valign="top">' . $prod->name . '</td><td valign="top">' . $expiry . '</td><td valign="top">' . $batch . '</td><td valign="top">' . $loc->name . '</td><td valign="top">' . $qty . '</td><td valign="top">' . @$in_date . '</td><td valign="top">' . @$out_date . '</td></tr>'."\n";
					$i++;
				}
			}
		}
	}
	?>
	</tbody>
	<tfoot>
	</tfoot>
</table>
<?php } ?>