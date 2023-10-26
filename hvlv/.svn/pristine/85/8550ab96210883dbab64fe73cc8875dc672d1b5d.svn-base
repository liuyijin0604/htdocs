<h1 style="text-align: center; font-size: 50px; margin-bottom: 10px">
	<?php $prev = date('Y-m-d', strtotime($_GET['date'] . ' - 1 month')); $next = date('Y-m-d', strtotime($_GET['date'] . ' + 1 month')); ?>
	<span class="prev" style="cursor: pointer" data-date="<?=$prev?>">《</span>
	&nbsp;&nbsp;<?=date('F Y', strtotime($_GET['date']))?>&nbsp;&nbsp;
	<span class="next" style="cursor: pointer" data-date="<?=$next?>">》</span>
</h1>
<?php
$day_first = date('Y-m-01', strtotime($_GET['date']));
$day_last = date('Y-m-d', strtotime($day_first . ' + 1 month - 1 day'));
$from = date('Y-m-d', strtotime($day_first . ' - ' . (date('N', strtotime($day_first)) % 7) . ' days'));
$to = date('Y-m-d', strtotime($day_last . ' + ' . (6 - date('N', strtotime($day_last))) . ' days'));
$limit = 7;

$items_by_date;
$items = CalendarItem::model()->findAll('date >= :from AND date <= :to', [':from' => $from, ':to' => $to]);
foreach ($items as $item) {
	$items_by_date[$item->date][strtotime($item->date . ' ' . date('H:i:s', strtotime($item->time)))][] = $item;
}

$date = $from;
$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
for ($i = 0; $i <= 6; $i++) {
	echo '<div style="width: 14.28%; float: left; position: relative; text-align: center; font-size: 20px">' . $days[$i] . '</div>';
}
while (strtotime($date) <= strtotime($to)) {
	echo '<div class="calendar-item" style="width: 14.28%; float: left; padding: 1%; height: ' . (($_GET['height'] - 140) / 5) . 'px; border: 1px solid #CACACA; box-sizing: border-box; position: relative">';
	echo '<a class="jqm_link" href="' . Yii::app()->createUrl('ediJob/createCalendarItem', ['date' => $date]) . '" title="Create Calendar Item"><div class="icon" style="background-position:-16px 0; position: absolute; right: 10px; top: 10px; cursor: pointer"></div></a>';
	if ($date == $_GET['date']) {
		echo '<div style="width: 100%; text-align: center; margin-top: -5px; margin-bottom: 5px"><span style="border-radius: 10px; background-color: #15538B; color: #DFE8F6">&nbsp;' . date('d', strtotime($date)) . '&nbsp;</span></div>';
	} else {
		echo '<div style="width: 100%; text-align: center; margin-top: -5px; margin-bottom: 5px">' . date('d', strtotime($date)) . '</div>';
	}

	if (!empty($items_by_date[$date])) {
		ksort($items_by_date[$date]);

		$count = 0;
		foreach ($items_by_date[$date] as $items) {
			foreach ($items as $item) {
				$count ++;
				echo '<a class="jqm_link" href="' . Yii::app()->createUrl('ediJob/updateCalendarItem', ['id' => $item->id]) . '" title="Update Calendar Item">' . $item->time . ' ' . mb_substr($item->ref, 0, 10) . (mb_strlen($item->ref) > 10 ? '...' : '') . '</a>';
				if ($count < $limit) {
					echo '<br>';
				} else {
					echo '<a class="more" style="float: right; cursor: pointer; color: #15538B" data-date="' . $date . '">more</a>';
					break 2;
				}
			}
		}
	}

	echo '</div>';
	$date = date('Y-m-d', strtotime($date . ' + 1 day'));
}
?>

<script type="text/javascript">
$(function () {
	// $('.calendar-item').on('mouseover', function() {
	// 	$('.icon', this).show();
	// }).on('mouseleave', function() {
	// 	$('.icon', this).hide();
	// });
});
</script>