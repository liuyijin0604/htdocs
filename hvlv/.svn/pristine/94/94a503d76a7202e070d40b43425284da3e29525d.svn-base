<style>
#edijob-airline-form div .rowcol {
	padding: 5px 0;
}
</style>

<h1><?=$model->awb?> Info</h1>
<br />

<div class="form">
<?php
$form = $this->beginWidget('CActiveForm', array(
	'id' => 'edijob-airline-form',
));
$items = EdiJobAirline::model()->findAll('job_id = :job_id', [':job_id' => $model->id]);
if (empty($items)) { ?>

	<div class="rowcol rowleft">

		<div class="rowcol rowleft">
			<h3>Transit 1</h3>
		</div>

		<div class="rowcol">
			<?php echo CHtml::button('Add Transit', ['id' => 'transit_1']); ?>
		</div>

		<div class="rowcol rowleft">
			<?php echo CHtml::label('Flight', 'flight', ['style' => 'padding-bottom: 5px']); ?>
			<?php echo CHtml::textField('flight[1][1]', '', ['size' => 12]); ?>
		</div>

		<div class="rowcol">
			<?php echo CHtml::label('ATD', 'atd', ['style' => 'padding-bottom: 5px']); ?>
			<?php echo CHtml::textField('atd[1][1]', '', ['class' => 'date_input', 'size' => 12, 'id' => $_GET['tabid'] . 'atd_1_1']); ?>
		</div>

		<div class="rowcol">
			<?php echo CHtml::label('ATA', 'ata', ['style' => 'padding-bottom: 5px']); ?>
			<?php echo CHtml::textField('ata[1][1]', '', ['class' => 'date_input', 'size' => 12, 'id' => $_GET['tabid'] . 'ata_1_1']); ?>
		</div>

		<div class="rowcol">
			<?php echo CHtml::label('Pallets', 'plt', ['style' => 'padding-bottom: 5px']); ?>
			<?php echo CHtml::textField('plt[1][1]', '', ['size' => 12]); ?>
		</div>

		<div class="rowcol">
			<a style="cursor: pointer; position: relative; top: 19px" id="add_1_1"><div style="background-position: -16px 0" class="icon"></div></a>
		</div>

	</div>

<?php } else {
	$transit = 9999;
	$last_transit = EdiJobAirline::model()->find(['condition' => 'job_id = :job_id', 'params' => [':job_id' => $model->id], 'order' => 'id DESC'])->transit;
	foreach ($items as $tk => $item) {
		if ($item->transit != $transit) {
			$k = 0;
			$transit = $item->transit;
		} else {
			$k ++;
		}

		if ($k == 0) {
			echo '<div class="rowcol rowleft"><div class="rowcol rowleft"><h3>Transit ' . $transit . '</h3></div>';
			if ($transit == $last_transit) {
				echo '<div class="rowcol">' . CHtml::button('Add Transit', ['id' => 'transit_' . $transit]) . '</div>';
			}
			echo '<div class="rowcol rowleft">' . CHtml::label('Flight', 'flight', ['style' => 'padding-bottom: 5px']) . CHtml::textField('flight[' . $transit . '][' . $k . ']', $item->flight_no, ['size' => 12]) . '</div>';
			echo '<div class="rowcol">' . CHtml::label('ATD', 'atd', ['style' => 'padding-bottom: 5px']) . CHtml::textField('atd[' . $transit . '][' . $k . ']', $item->atd, ['class' => 'date_input', 'size' => 12, 'id' => $_GET['tabid'] . 'atd_' . $transit . '_' . $k]) . '</div>';
			echo '<div class="rowcol">' . CHtml::label('ATA', 'ata', ['style' => 'padding-bottom: 5px']) . CHtml::textField('ata[' . $transit . '][' . $k . ']', $item->ata, ['class' => 'date_input', 'size' => 12, 'id' => $_GET['tabid'] . 'ata_' . $transit . '_' . $k]) . '</div>';
			echo '<div class="rowcol">' . CHtml::label('Pallets', 'plt', ['style' => 'padding-bottom: 5px']) . CHtml::textField('plt[' . $transit . '][' . $k . ']', $item->plt, ['size' => 12]) . '</div>';
		} else {
			echo '<div class="rowcol rowleft">' . CHtml::textField('flight[' . $transit . '][' . $k . ']', $item->flight_no, ['size' => 12]) . '</div>';
			echo '<div class="rowcol">' . CHtml::textField('atd[' . $transit . '][' . $k . ']', $item->atd, ['class' => 'date_input', 'size' => 12, 'id' => $_GET['tabid'] . 'atd_' . $transit . '_' . $k]) . '</div>';
			echo '<div class="rowcol">' . CHtml::textField('ata[' . $transit . '][' . $k . ']', $item->ata, ['class' => 'date_input', 'size' => 12, 'id' => $_GET['tabid'] . 'ata_' . $transit . '_' . $k]) . '</div>';
			echo '<div class="rowcol">' . CHtml::textField('plt[' . $transit . '][' . $k . ']', $item->plt, ['size' => 12]) . '</div>';
		}

		if (empty($items[$tk+1]) || $items[$tk+1]->transit != $item->transit) {
			$top = $k == 0 ? 19 : 3;
			echo '<div class="rowcol"><a style="cursor: pointer; position: relative; top: ' . $top . 'px" id="add_' . $transit . '_' . $k . '"><div style="background-position: -16px 0" class="icon"></div></a></div>';
			echo '</div>';
		}
	}
} ?>


<div class="row buttons">
<?php if ($model->status < 30) { ?>
	<?php echo CHtml::submitButton('Save'); ?>
<?php } ?>
<?php echo '&nbsp;', CHtml::submitButton('货齐', ['id' => 'all_done']); ?>
</div>

<?php $this->endWidget(); ?>
</div>

<script>
$(function() {
	var tab = $('#<?=$_GET["tabid"];?>');
	panel = tab.data('panel');

	$(panel).on('click', 'a[id*="add"]', function() {
		var transit = $(this).attr('id').split('_')[1];
		var line = $(this).attr('id').split('_')[2];
		line = parseInt(line) + 1;

		var html = '<div class="rowcol rowleft"><input size="12" type="text" name="flight[' + transit + '][' + line + ']" id="flight_' + transit + '_' + line + '"></div>';
		html += '<div class="rowcol"><input size="12" type="text" class="date_input" name="atd[' + transit + '][' + line + ']" id="<?=$_GET["tabid"];?>atd_' + transit + '_' + line + '"></div>';
		html += '<div class="rowcol"><input size="12" type="text" class="date_input" name="ata[' + transit + '][' + line + ']" id="<?=$_GET["tabid"];?>ata_' + transit + '_' + line + '"></div>';
		html += '<div class="rowcol"><input size="12" type="text" name="plt[' + transit + '][' + line + ']" id="plt_' + transit + '_' + line + '"></div>';
		$(this).parent().before(html);
		$(this).attr('id', 'add_' + transit + '_' + line);
		$(this).attr('style', 'cursor: pointer; position: relative; top: 3px');
	});

	$(panel).on('click', 'input[id*="transit"]', function() {
		var transit = $(this).attr('id').split('_')[1];
		transit = parseInt(transit) + 1;

		var html = '<div class="rowcol rowleft">';
		html += '<div class="rowcol rowleft"><h3>Transit ' + transit + '</h3></div>';
		html += '<div class="rowcol"><input type="button" value="Add Transit" id="transit_' + transit + '" /></div>';
		html += '<div class="rowcol rowleft"><label style="padding-bottom: 5px" for="flight">Flight</label><input size="12" type="text" name="flight[' + transit + '][1]" id="flight_' + transit + '_1"></div>';
		html += '<div class="rowcol"><label style="padding-bottom: 5px" for="atd">ATD</label><input size="12" type="text" class="date_input" name="atd[' + transit + '][1]" id="<?=$_GET["tabid"];?>atd_' + transit + '_1"></div>';
		html += '<div class="rowcol"><label style="padding-bottom: 5px" for="ata">ATA</label><input size="12" type="text" class="date_input" name="ata[' + transit + '][1]" id="<?=$_GET["tabid"];?>ata_' + transit + '_1"></div>';
		html += '<div class="rowcol"><label style="padding-bottom: 5px" for="plt">Pallets</label><input size="12" type="text" name="plt[' + transit + '][1]" id="plt_' + transit + '_1"></div>';
		html += '<div class="rowcol"><a style="cursor: pointer; position: relative; top: 19px" id="add_' + transit + '_1"><div style="background-position: -16px 0" class="icon"></div></a></div>';
		html += '</div>';

		$(this).parent().parent().after(html);
		$(this).hide();
	});

	$(panel).on('click', '#all_done', function() {
		if (!confirm('确认货已齐?')) {
			return false;
		}
	});
});
</script>