<div id="calendar">
<?php
if (empty($_GET['type'])) {
	$_GET['type'] = 'week';
}
if (empty($_GET['date'])) {
	$_GET['date'] = date('Y-m-d');
}
echo CHtml::dropDownList('type', $_GET['type'], ['month' => 'Month', 'week' => 'Week'], ['style' => 'font-size: 30px; position: absolute']);
echo CHtml::hiddenField('date', $_GET['date']); ?>
?>
</div>

<div id="_calendar">
</div>

<script>
$(function() {
	var tab = $('#<?=$_GET["tabid"]?>');
	var panel = tab.data('panel');

	$('select#type', panel).on('change', function() {
		var url = '<?=Yii::app()->createUrl("ediJob/RenderCalendar")?>';
		url = url.replace('.app', '');
		url += '?type=' + $(this).val() + '&date=' + $('#date', panel).val() + '&height=' + panel.height();
		$('#_calendar', panel).load(url);
	}).trigger('change');

	$(panel).off('click', 'a.more').on('click', 'a.more', function() {
		var url = '<?=Yii::app()->createUrl("ediJob/RenderCalendar")?>';
		url = url.replace('.app', '');
		url += '?type=week&date=' + $(this).data('date') + '&height=' + panel.height();
		$('#_calendar', panel).load(url);

		$('select#type option[value="week"]', panel).prop('selected', 'selected');
		$('#date').val($(this).data('date'));
	});

	$(panel).off('click', 'span.prev, span.next').on('click', 'span.prev, span.next', function() {
		var url = '<?=Yii::app()->createUrl("ediJob/RenderCalendar")?>';
		url = url.replace('.app', '');
		url += '?type=' + $('select#type', panel).val() + '&date=' + $(this).data('date') + '&height=' + panel.height();
		$('#_calendar', panel).load(url);

		$('#date').val($(this).data('date'));
	});
});
</script>