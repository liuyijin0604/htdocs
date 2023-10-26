<table width="100%" cellspacing="0" class="chart">
		<tbody>
			<tr style="background: rgba(100,100,100,0.4);">
				<td align="middle" width="50">No</th>
                <td align="middle" width="350">Shipment Ref</td>
                <td align="middle" width="300">awb</td>
				<td align="middle"  width="50">pkg</td>
                <td align="middle"  width="200">eta/location</td>
                                
			</tr>
		<?php
                $i=0;
 	foreach($rs as $r){
		$strLocation = $r->getRackName();
	echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">'.($i+1).'</td><td valign="top" style="word-break:break-all; word-wrap:break-all;" ><table><tr><td>'.(!empty($r->ref)?$r->ref:$r->hbn).'</td></tr><tr><td style="border-top:1px solid #000;">'.$r->hbn.'</td></tr></table></td><td valign="top" style="word-break:break-all; word-wrap:break-all;" > '.@$r->consol->awb.'</td><td valign="top">'.$r->pkg."/".($r->pkg-$r->scan_no).'</td><td valign="center" style="word-break:break-all; word-wrap:break-all;" >'.@$r->consol->eta.'<br/>'.$strLocation.'</td></tr>';
			$i++;
             }
        ?>
          <tr><td class="plc1">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
</tbody>
</table>

