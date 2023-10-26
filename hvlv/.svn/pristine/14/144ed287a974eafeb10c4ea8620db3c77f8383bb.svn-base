<div class="form">

	<?php $form = $this->beginWidget('CActiveForm', array(
		'action' => Yii::app()->createUrl($this->route),
		'method' => 'get',
	)); ?>


	<div class="row rowcol rowleft">
		<?php echo $form->label($model, 'Week');
		?>
	</div>
	<div class="row rowcol rowleft">
		<?php echo  $form->dropDownList($model, 'week',CargoProcessJob::model()->getCJobByWeek(),['style'=>"width: 200px;"]);
		?>
	</div>
	<div class="row rowcol rowleft">
		<?php echo $form->label($model, 'Driver');
		?>
	</div>
	<div class="row rowcol rowleft">
		<?php echo $form->dropDownList($model, 'driver_id',CargoProcessJob::model()->getDriverList(),['style'=>"width: 200px;"]);
		?>
	</div>
		

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Search')); //array('style'=>'margin-left:10px;')
		?>
		
	</div>

	<?php $this->endWidget(); ?>

</div><!-- search-form -->