<h2>CargoProcessJob: <?php echo $model->job_name ?></h2>

<div class="form">
	<div class="row rowcol rowleft">
			<?php echo CHtml::label('Driver','driver');?>
			<?php 
				$strDriverName = Org::model()->findByPk($model->driver_id)->name;
				echo CHtml::label($strDriverName,'name')?>
	</div>
	<div class="row rowcol ">
		<?php echo CHtml::label('Date','date');?>
		<?php echo CHtml::label($model->created,'date');?>
	</div>
</div>