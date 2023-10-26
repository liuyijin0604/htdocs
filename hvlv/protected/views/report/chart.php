<div class="form">
	<form id="chart-form" action="" type="GET">
		<div class="row rowcol rowleft">
		<label>Frequncy</label>
		<select name="f">
			<option value="d">Daily</option>
			<option value="w" selected>Weekly</option>
			<option value="m">Monthly</option>
		</select>
		</div>
		<div class="row rowcol">
		<label>Y Axis</label>
		<select name="y">
			<option value="q" selected>Qty</option>
			<option value="w">Weight</option>
		</select>
		</div>
		<div class="row rowcol">
		<label>Chart Type</label>
		<select name="t">
			<option value="line" selected>Line</option>
			<option value="bar">Bar</option>
			<option value="barstack">Stacked</option>
		</select>
		</div>
		<div class="row rowcol">
		<?php echo CHtml::label('Agent','org_id'); ?>
		<?php echo CHtml::hiddenField('org_id');
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => empty($_GET["tabid"])? 'org_id_ac' : $_GET["tabid"].'_org_id_ac',
				'sourceUrl' => array('org/ownerSuggest'),
				'value' => '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'size' => '30',
				),
		));
		?>
		</div>
		<div class="row rowcol">
		<button type="submit" style="font-size: 1.2em; padding: 8px 15px;">Chart</button>
		</div>
	</form>
</div>
<canvas id="chart" width="400" height="200"></canvas>
<?php
Yii::app()->clientScript->registerScriptFile('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js');
?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	var myChart = false;
	$('#chart-form', panel).on('submit', function(r){
		$.get('<?=$this->createUrl('report/chart');?>?'+$(this).serialize(), function(r){
			if(myChart !== false) myChart.destroy();
			myChart = new Chart($('#chart', panel)[0], r);
		}, 'json');
		return false;
	});

});
</script>