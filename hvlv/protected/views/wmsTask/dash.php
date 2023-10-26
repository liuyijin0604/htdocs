<h1>WmsTask Dashboard</h1>

<h2 style="color:green">待完成</h2>
<?php
foreach ($list['before930'] as $customer => $tasks) {
	echo '<h3>' . $customer . ':</h3> ';
	foreach ($tasks as $k => $task) {
		echo $task->getNo();
		if ($k != count($tasks) - 1) {
			echo ', ';
		}
	}
	echo '<br />';
}
?>
<br />
<br />

<h2>WIP</h2>
<?php
foreach ($list['wip'] as $customer => $tasks) {
	echo '<h3>' . $customer . ':</h3> ';
	foreach ($tasks as $k => $task) {
		echo $task->getNo();
		if ($k != count($tasks) - 1) {
			echo ', ';
		}
	}
	echo '<br />';
}
?>
<br />
<br />

<h2 style="color:red">已完成</h2>
<?php
foreach ($list['complete'] as $customer => $tasks) {
	echo '<h3>' . $customer . ':</h3> ';
	foreach ($tasks as $k => $task) {
		echo $task->getNo();
		if ($k != count($tasks) - 1) {
			echo ', ';
		}
	}
	echo '<br />';
}
?>
<br />
<br />

<h2 style="color:orange">明天</h2>
<?php
foreach ($list['after930'] as $customer => $tasks) {
	echo '<h3>' . $customer . ':</h3> ';
	foreach ($tasks as $k => $task) {
		echo $task->getNo();
		if ($k != count($tasks) - 1) {
			echo ', ';
		}
	}
	echo '<br />';
}
?>