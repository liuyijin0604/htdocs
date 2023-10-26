<style>
    .display_none {
		display: none;
	}
</style>
<div class="form" style="padding-left: 20px;">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'action' => Yii::app()->createUrl($this->route),
		'method' => 'get','id'=>'job_list'
	)); ?>
	<div class="row">
		<div class="col col-md-6 col-sm-12">
			<div class="row">
				<?php echo CHtml::label('StrDate&nbsp&nbsp&nbsp','StartDate')." ".CHtml::textField('CargoProcessJob[StartDate]','',['class' => 'date_input'])?>
			</div>
			<br />
			<div class="row">
				<?php echo  CHtml::label('EndDate&nbsp','EndDate')." ".CHtml::textField('CargoProcessJob[EndDate]','',['class' => 'date_input'])?>
			</div>
		</div>
		<div class="col col-md-6 col-sm-12">
			<div class="row">
				
			</div>
			<br />
			<div class="row">
				
			</div>
		</div>
	</div>
	<div class="display_none">
		<?php echo CHtml::telField('w',0,['id'=>'w'])?>
	</div>
	<br />
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Search')); //array('style'=>'margin-left:10px;')
		?>
		<?php echo CHtml::button('Today', array(
			"id" => 'tod',
		)); ?>
		<?php echo CHtml::button('Tomorrow', array(
			"id" => 'tom',
		)); ?>
	</div>

	<?php $this->endWidget(); ?>
</div><!-- search-form -->
<script type="text/javascript">
	$(function() {
		var tab = $('#<?= $_GET["tabid"]; ?>');
		var panel = tab.data('panel');

		$('#tod', panel).click(function() {
			$('#CargoProcessJob_StartDate',panel).val('');
			$('#CargoProcessJob_EndDate',panel).val('');
			$('#w', panel).val(1);
			$('#job_list',panel).submit();
			$('#w', panel).val(0);
		});

		$('#tom', panel).click(function() {
			$('#CargoProcessJob_StartDate',panel).val('');
			$('#CargoProcessJob_EndDate',panel).val('');
			$('#w', panel).val(2);
			$('#job_list',panel).submit();
			$('#w', panel).val(0);
		});
	});
</script>