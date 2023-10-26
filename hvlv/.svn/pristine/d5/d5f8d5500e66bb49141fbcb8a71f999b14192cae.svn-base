<?php
if(!empty($err)):
	echo '<p style="color:#c00">'.implode('<br />', $err).'</p>';
else:
?>
<form id="delay-notify-form" class="ifrm-form" enctype="multipart/form-data" method="post" action="import/delay?act=email">
<table class="chart" width="800">
  <thead>
    <tr>
      <th>&nbsp;</th>
      <th>Our Ref</th>
      <th>Your P/O</th>
      <th>Description</th>
      <th>Color</th>
      <th>ETA</th>
      <th>Reason</th>
    </tr>
	</thead>
	<tbody>
<?php
ksort($list);
$i=0;
foreach($list as $l):
?>
	<tr><th colspan="7" align="left"><br /><?php echo (empty($l['org'])? '<span style="color:#c00">Detail Not Found</span> - '.$l[0][6] : CHtml::checkBox('send[]', true, array('value' => $l['org'])).' '.$l[0][6]); ?></th></tr>
	<?php
	unset($l['org']);
	foreach($l as $r){
		echo '<tr>
      <td>'.$r[1].'</td>
      <td>'.$r[2].'</td>
      <td>'.$r[3].'</td>
      <td>'.$r[4].'</td>
      <td>'.$r[5].'</td>
      <td>'.(empty($r[9])? 'TBA' : $r[9]).'</td>
      <td>'.$r[10].'</td>
		</tr>';
	}
	?>
<?php
endforeach;
?>
  </tbody>
</table><br />
<input type="submit" value="Send" />
</form>
<?php
endif;