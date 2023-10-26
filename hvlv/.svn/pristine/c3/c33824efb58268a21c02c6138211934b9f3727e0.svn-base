<?php $cnee = new Addr; ?>

<div class="row">
	<div class="col col-md-2 col-sm-4">
		<div class="form-group">
			<?php echo $form->labelEx($cnee, 'company'); ?>
			<?php echo CHtml::textField('mdata[cnee][company]', @$model->mdata['cnee']['company'], array('size' => 60, 'class' => 'form-control')); ?>
		</div>
	</div>
				
	<div class="col col-md-2 col-sm-4">
		<div class="form-group">
			<?php echo $form->labelEx($cnee, 'name', array('required' => 'required')); ?>
			<?php echo CHtml::textField('mdata[cnee][name]', @$model->mdata['cnee']['name'], array('size' => 35, 'class' => 'form-control', 'required' => 'required')); ?>
		</div>
	</div>
	
	<div class="col col-md-2 col-sm-4">
		<div class="form-group">
			<?php echo $form->labelEx($cnee, 'tel', array('required' => 'required')); ?>
			<?php echo CHtml::textField('mdata[cnee][tel]', @$model->mdata['cnee']['tel'], array('class' => 'form-control', 'required' => 'required')); ?>
		</div>
	</div>
</div>

<div class="row">
	<div class="col col-md-6 col-sm-6">
		<div class="form-group">
			<?php echo $form->labelEx($cnee, 'address', array('required' => 'required')); ?>
			<?php echo CHtml::textField('mdata[cnee][address]', @$model->mdata['cnee']['address'], array('size' => 60, 'class' => 'form-control', 'required' => 'required')); ?>
		</div>
	</div>
</div>

<div class="row">
	<div class="col col-md-2 col-sm-4">
		<div class="form-group">
			<?php echo $form->labelEx($cnee, 'suburb', array('required' => 'required')); ?>
			<?php echo CHtml::textField('mdata[cnee][suburb]', @$model->mdata['cnee']['suburb'], array('class' => 'form-control', 'required' => 'required')); ?>
		</div>
	</div>

	<div class="col col-md-2 col-sm-4">
		<div class="form-group">
			<?php echo $form->labelEx($cnee, 'city'); ?>
			<?php echo CHtml::textField('mdata[cnee][city]', @$model->mdata['cnee']['city'], array('class' => 'form-control')); ?>
		</div>
	</div>

	<div class="col col-md-2 col-sm-4">
		<div class="form-group">
			<?php echo $form->labelEx($cnee, 'state', array('required' => 'required')); ?>
			<?php echo CHtml::textField('mdata[cnee][state]', @$model->mdata['cnee']['state'], array('class' => 'form-control', 'required' => 'required')); ?>
		</div>
	</div>
</div>
		
<div class="row">
	<div class="col col-md-2 col-sm-4">
		<div class="form-group">
			<?php echo $form->labelEx($cnee, 'postcode', array('required' => 'required')); ?>
			<?php echo CHtml::textField('mdata[cnee][postcode]', @$model->mdata['cnee']['postcode'], array('class' => 'form-control', 'required' => 'required')); ?>
		</div>
	</div>

	<div class="col col-md-2 col-sm-4">
		<div class="form-group">
			<?php echo $form->labelEx($cnee, 'country', array('required' => 'required')); ?>
			<?php if (empty($model->mdata['cnee']['country'])) $model->mdata['cnee']['country'] = 'AU'; ?>
			<?php echo CHtml::dropDownList('mdata[cnee][country]', @$model->mdata['cnee']['country'], WmsTask::$countries, array('class' => 'form-control', 'empty' => 'Select One', 'required' => 'required')); ?>
		</div>
	</div>

	<div class="col col-md-2 col-sm-4">
		<div class="form-group">
			<?php echo $form->labelEx($cnee, 'email'); ?>
			<?php echo CHtml::textField('mdata[cnee][email]', @$model->mdata['cnee']['email'], array('size' => 60, 'class' => 'form-control')); ?>
		</div>
	</div>
</div>