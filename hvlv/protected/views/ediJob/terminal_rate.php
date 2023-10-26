<h1>Terminal Rate</h1>
<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', [
		'id' => 'terminal-rate-form',
		'enableAjaxValidation' => false,
	]); ?>

	<div class="grid-view">
		<table class="items">
			<thead>
				<tr>
					<th>Name</th>
					<th>Rate</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($orgs as $org) {
					echo '<tr>';
					echo '<td>' . $org->name . '</td>';
					echo '<td><input type="text" name="rates[' . $org->id . ']" value="' . @$org->extra['terminal_rate'] . '" style="width: 100%" /></td>';
					echo '</tr>';
				} ?>
			</tbody>
		</table>
	</div>

	<div class="button">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>

<script>
	$(function() {
		var tab = $('#jqmw_<?=$_GET["tabid"]?>');
		var win = tab.data('panel');

		$('form#terminal-rate-form', win).on('success', function() {
			setTimeout(function() {
				$('.popCancel', win).trigger('click');
			}, 1e3);
		});
	});
</script>