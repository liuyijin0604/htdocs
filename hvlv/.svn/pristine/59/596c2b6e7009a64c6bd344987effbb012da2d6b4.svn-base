<table width="100%" cellspacing="0" class="chart">
		<tbody>
			<tr style="background: rgba(100,100,100,0.4);">
				<td align="left" width="100">No</td>
                                <td align="left" width="400">Shipment Ref</td>
                                 <td align="left" width="330">awb</td>
				<td align="left"  width="30">pkg</td>
				<td align="left"  width="30">Scanned</td>
                                <td align="left"  width="50">Weight</td>
                                <td align="left"  width="350">Agent</td>
                                <td align="middle"  width="160">eta</td>
                                
			</tr>
		<?php
                $i=0;
 	foreach($rs as $r){
	echo '<tr class="'.($i%2 == 1? 'even' : 'odd').'"><td align="center" valign="top">'.($i+1).'</td><td valign="top" style="word-break:break-all; word-wrap:break-all;">'.(!empty($r->ref)?$r->ref:$r->hbn).'</td><td valign="top" style="word-break:break-all; word-wrap:break-all;"> '.@$r->consol->awb.
                '</td><td valign="top">'.$r->pkg.'</td><td valign="top">'.$r->scanCount().'</td><td valign="top">'.sprintf("%.2f",$r->weight).'</td><td valign="top">'.@$r->agent->name.'</td><td valign="center">'.@$r->consol->eta.'</td></tr>';
			$i++;
             }
        ?>

</tbody>
</table>

