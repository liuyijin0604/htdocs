<h1>Date Range</h1>

<div class="form">
	<?php 
		if($type=='Total'){
			$form = $this->beginWidget('CActiveForm', array(
				'id' => 'courier-list-form',
				'enableAjaxValidation' => false,
				'action' => $this->createUrl('customProcess/exportHistoryCustomKPIReport'),
				'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
			));
		}else{
			$form = $this->beginWidget('CActiveForm', array(
				'id' => 'courier-list-form',
				'enableAjaxValidation' => false,
				'action' => $this->createUrl('customProcess/exportHistoryCustomKPIDetailReport'),
				'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
			));
		}
		
	 ?>

	<?php 
		echo '<div class="row ">'. CHtml::label('Please select the data range',''). '</div>';
		echo '<div class="row rowcol">'. CHtml::textField('startdate',date("Y-m-d",strtotime("now")),['class' => 'date_input', 'id' => 'startdate']). ' To </div>';
		echo '<div class="row rowcol">'. CHtml::textField('enddate',date("Y-m-d",strtotime("now")),['class' => 'date_input', 'id' => 'enddate']). '</div>';
	?>

	<div class="row buttons">
		<?php 
			echo CHtml::submitButton($this->t('Generate')); 
		?>
	</div>

	<?php $this->endWidget(); ?>
</div>