<?php
/* @var $this AutoCommandsController */
/* @var $model AutoCommands */
?>
<style>
	#fixed_time_container, #interval_time_container {
		display: none;
	}
</style>
<div class="container">
	<h3>Instructions</h3>
	<p>Input field 'Day': specify which day the function will be executed, leave blank if function runs every day.</p>
	<p>Input field 'Week Day': specify which day in a week the function will be executed, 1-7 => Monday => Sunday, leave blank if function runs every day.</p>
	<p>Input field 'Hour': specify </p>
</div>
<div class="container form">
	<form id="create_auto_command">
		<div class="row">
			<div class="row rowcol">
				<label for="command_name">Command Name</label>
				<input type="text" id="command_name" name="command_name" required/>
			</div>
			<div class="row rowcol">
				<label for="func_name">Function Name</label>
				<input type="text" id="func_name" name="func_name" required/>
			</div>
			<div class="row rowcol">
				<label for="day">Day</label>
				<input type="number" id="day" name="day" min="0" max="31" />
			</div>
			<div class="row rowcol">
				<label for="week_day">Week Day</label>
				<input type="number" id="week_day" name="week_day" min="1" max="7" />
			</div>
		</div>
		<div class="row">
			<div class="row rowcol">
				<p>Type</p>
				<input type="radio" id="time_fixed" name="execution_type" value="time_fixed">
				<label for="time_fixed" style="display: inline;">Fixed</label>
				<input type="radio" id="time_interval" name="execution_type" value="time_interval">
				<label for="time_interval" style="display: inline;">Interval</label>
			</div>
		</div>
		<div class="row" id="fixed_time_container">
			<div class="row rowcol" style="width: 120px;">
				<h4>Hour</h4>
				<!-- <input type="checkbox" id="hour_all"><span style="font-weight: 700;">Select All</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_0" name="hour_0" value="0"><span style="font-weight: 700;">0</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_1" name="hour_1" value="1"><span style="font-weight: 700;">1</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_2" name="hour_2" value="2"><span style="font-weight: 700;">2</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_3" name="hour_3" value="3"><span style="font-weight: 700;">3</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_4" name="hour_4" value="4"><span style="font-weight: 700;">4</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_5" name="hour_5" value="5"><span style="font-weight: 700;">5</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_6" name="hour_6" value="6"><span style="font-weight: 700;">6</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_7" name="hour_7" value="7"><span style="font-weight: 700;">7</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_8" name="hour_8" value="8"><span style="font-weight: 700;">8</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_9" name="hour_9" value="9"><span style="font-weight: 700;">9</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_10" name="hour_10" value="10"><span style="font-weight: 700;">10</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_11" name="hour_11" value="11"><span style="font-weight: 700;">11</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_12" name="hour_12" value="12"><span style="font-weight: 700;">12</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_13" name="hour_13" value="13"><span style="font-weight: 700;">13</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_14" name="hour_14" value="14"><span style="font-weight: 700;">14</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_15" name="hour_15" value="15"><span style="font-weight: 700;">15</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_16" name="hour_16" value="16"><span style="font-weight: 700;">16</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_17" name="hour_17" value="17"><span style="font-weight: 700;">17</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_18" name="hour_18" value="18"><span style="font-weight: 700;">18</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_19" name="hour_19" value="19"><span style="font-weight: 700;">19</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_20" name="hour_20" value="20"><span style="font-weight: 700;">20</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_21" name="hour_21" value="21"><span style="font-weight: 700;">21</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_22" name="hour_22" value="22"><span style="font-weight: 700;">22</span><br />
				<input type="checkbox" class="hour-checkbox" id="hour_23" name="hour_23" value="23"><span style="font-weight: 700;">23</span><br /> -->
				<label for="hour">Hour</label>
				<input type="number" id="hour" name="hour" min="0" max="23" />
			</div>
			<div class="row rowcol">
				<h4>Minute</h4>
				<!-- <input type="checkbox" id="minute_all"><span style="font-weight: 700;">Select All</span><br />
				<input type="checkbox" class="minute-checkbox" id="minute_0" name="minute_0" value="0"><span style="font-weight: 700;">0</span><br />
				<input type="checkbox" class="minute-checkbox" id="minute_5" name="minute_5" value="5"><span style="font-weight: 700;">5</span><br />
				<input type="checkbox" class="minute-checkbox" id="minute_10" name="minute_10" value="10"><span style="font-weight: 700;">10</span><br />
				<input type="checkbox" class="minute-checkbox" id="minute_15" name="minute_15" value="15"><span style="font-weight: 700;">15</span><br />
				<input type="checkbox" class="minute-checkbox" id="minute_20" name="minute_20" value="20"><span style="font-weight: 700;">20</span><br />
				<input type="checkbox" class="minute-checkbox" id="minute_25" name="minute_25" value="25"><span style="font-weight: 700;">25</span><br />
				<input type="checkbox" class="minute-checkbox" id="minute_30" name="minute_30" value="30"><span style="font-weight: 700;">30</span><br />
				<input type="checkbox" class="minute-checkbox" id="minute_35" name="minute_35" value="35"><span style="font-weight: 700;">35</span><br />
				<input type="checkbox" class="minute-checkbox" id="minute_40" name="minute_40" value="40"><span style="font-weight: 700;">40</span><br />
				<input type="checkbox" class="minute-checkbox" id="minute_45" name="minute_45" value="45"><span style="font-weight: 700;">45</span><br />
				<input type="checkbox" class="minute-checkbox" id="minute_50" name="minute_50" value="50"><span style="font-weight: 700;">50</span><br />
				<input type="checkbox" class="minute-checkbox" id="minute_55" name="minute_55" value="55"><span style="font-weight: 700;">55</span><br /> -->
				<label for="minute">Minute</label>
				<input type="number" id="minute" name="minute" min="0" max="59" />
			</div>
		</div>
		<div class="row" id="interval_time_container">
			<div class="row rowcol">
				<label for="hour_interval">Hour Interval</label>
				<input type="number" id="hour_interval" name="hour_interval" min="0" max="23" />
			</div>
			<div class="row rowcol">
				<label for="minute_interval">Minute Interval</label>
				<input type="number" id="minute_interval" name="minute_interval" min="0" max="59" />
			</div>
		</div>
		<div class="row">
			<input type="submit" value="Create">
		</div>
	</form>
</div>

<script type="text/javascript">
	$(function() {
		$('#hour_all').on('change', function() {
			if ($('#hour_all').is(':checked')) {
				$('.hour-checkbox').prop('checked', true);
			} else {
				$('.hour-checkbox').prop('checked', false);
			}
		});
		$('#minute_all').on('change', function() {
			if ($('#minute_all').is(':checked')) {
				$('.minute-checkbox').prop('checked', true);
			} else {
				$('.minute-checkbox').prop('checked', false);
			}
		});

		$('input[name="execution_type"]').on('change', function() {
			if ($('#time_fixed').prop('checked')) {
				$('#fixed_time_container').show();
				$('#interval_time_container').hide();
			}
			if ($('#time_interval').prop('checked')) {
				$('#fixed_time_container').hide();
				$('#interval_time_container').show();
			}
		})

		$('#create_auto_command').on('submit', function(e) {
			e.preventDefault();
			e.stopImmediatePropagation();

			let formData = new FormData(this);
			$.ajax({
				url: '<?= $this->createUrl("autoCommands/create") ?>',
				type: 'POST',
				data: formData,
				enctype: 'multipart/form-data',
				cache: false,
				contentType: false,
				processData: false,

				success: function(){
					myApp.notice('success', 5000);
				}
			})
		})
	});
</script>