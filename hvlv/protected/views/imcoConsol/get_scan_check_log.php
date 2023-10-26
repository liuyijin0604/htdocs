<div style="font-size:16px;font-weight: bold">
<?php
	foreach ($scanLog as $key => $value) {
		echo $value["barcode"]." : ".$value["time"];
	}
?>
</div>