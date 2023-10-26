<?php
if ($wait['type'] === 'in') {
	foreach ($wait['items'] as $name => $qty) {
		echo $name . ': ' . $qty . '<br />';
	}
} else if ($wait['type'] === 'out') {
	foreach ($wait['items'] as $name => $plts) {
		echo $name . ': ';
		foreach ($plts as $plt) {
			echo '<span style="color: ' . ($plt['status'] ? 'green' : 'red') . '">' . $plt['name'] . '</span>&nbsp;';
		}
		echo '<br />';
	}
}
?>