<h1 style="text-align: center; font-size: 50px; margin-bottom: 10px">
	<?php $prev = date('Y-m-d', strtotime($_GET['date'] . ' - 7 days')); $next = date('Y-m-d', strtotime($_GET['date'] . ' + 7 days')); ?>
	<span class="prev" style="cursor: pointer" data-date="<?=$prev?>">《</span>
	&nbsp;&nbsp;<?=date('F Y', strtotime($_GET['date']))?>&nbsp;&nbsp;
	<span class="next" style="cursor: pointer" data-date="<?=$next?>">》</span>
</h1>
<?php
$from = date('Y-m-d', strtotime($_GET['date'] . ' - ' . date('N', strtotime($_GET['date'])) . ' days'));
$to = date('Y-m-d', strtotime($from . ' + 6 days'));

$items_by_date;
$items = CalendarItem::model()->findAll('date >= :from AND date <= :to', [':from' => $from, ':to' => $to]);
foreach ($items as $item) {
	$items_by_date[$item->date][strtotime($item->date . ' ' . date('H:i:s', strtotime($item->time)))][] = $item;
}

// days title
$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
echo '<div style="width: 5%; float: left">&nbsp;</div>';
for ($i = 0; $i <= 6; $i++) {
	echo '<div style="width: 13.5%; float: left; position: relative; text-align: center; font-size: 20px">' . $days[$i] . '</div>';
}

echo '<div style="width: 5%; float: left; padding-top: 45px; height: 4900px">';
for ($i = 0; $i < 24; $i++) {
	echo '<div style="width: 100%; height: 200px; border: 1px solid #CACACA; position: relative; box-sizing: border-box"><div style="top: 50%; left: 50%; transform: translate(-50%, -50%); position: absolute; font-size: 30px">' . sprintf('%02d', $i) . '</div></div>';
}
echo '</div>';

$date = $from;
while (strtotime($date) <= strtotime($to)) {
	echo '<div class="calendar-item" style="width: 13.5%; float: left; padding-top: 10px; height: 4900px; box-sizing: border-box; position: relative; font-size: 15px">';
	// echo '<a class="jqm_link" href="' . Yii::app()->createUrl('ediJob/createCalendarItem', ['date' => $date]) . '" title="Create Calendar Item"><div class="icon" style="background-position:-16px 0; position: absolute; right: 10px; top: 15px; cursor: pointer"></div></a>';
	if ($date == $_GET['date']) {
		echo '<div style="width: 100%; text-align: center; margin-bottom: 10px; height: 25px"><span style="border-radius: 10px; background-color: #15538B; color: #DFE8F6">&nbsp;' . date('d', strtotime($date)) . '&nbsp;</span></div>';
	} else {
		echo '<div style="width: 100%; text-align: center; margin-bottom: 10px; height: 25px">' . date('d', strtotime($date)) . '</div>';
	}

	for ($i = 0; $i < 24; $i++) {
		$time = strtotime($date . ' ' . date('H:i:s', strtotime(sprintf('%02d', $i) . ':00:00')));
		echo '<div style="width: 100%; height: 200px; border: 1px solid #CACACA; padding: 2%; box-sizing: border-box">';
		$count = 4;
		if (!empty($items_by_date[$date][$time])) {
			foreach ($items_by_date[$date][$time] as $item) {
				$count --;
				echo '<div class="calendar-week-item" style="width: 44%; height: 43.5%; margin: 2%; background-color: #CEDCF0; cursor: pointer; padding: 2px; float: left"><p style="word-wrap: break-word">' . $item->ref . '</p><a class="jqm_link" href="' . Yii::app()->createUrl('ediJob/updateCalendarItem', ['id' => $item->id]) . '" title="Update Calendar Item"></a></div>';
				if ($count == 2 || $count == 0) echo '<div style="clear: both"></div>';
				if ($count == 0) break;
			}
		}
		while ($count-- > 0) {
			echo '<div class="calendar-week-item" style="width: 44%; height: 43.5%; margin: 2%; cursor: pointer; float: left"><a class="jqm_link" href="' . Yii::app()->createUrl('ediJob/createCalendarItem', ['date' => $date, 'time' => date('H:i:s', $time)]) . '" title="Create Calendar Item"></a></div>';
			if ($count == 2 || $count == 0) echo '<div style="clear: both"></div>';
		}
		echo '</div>';
	}

	echo '</div>';
	$date = date('Y-m-d', strtotime($date . ' + 1 day'));
}
?>

<script type="text/javascript">
$(function () {
	$('.calendar-week-item').on('dblclick', function() {
		$('a', this).trigger('click');
	});
});
</script>