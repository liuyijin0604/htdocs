<?php
/* @var $this QuotesController */
/* @var $model Quotes */
/* @var $form CActiveForm */
?>
<div class="wide form" style="margin-left: 30px">
<p class="note">Fields with <span class="required">*</span> are required.</p>
<br>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	    'enableAjaxValidation'=>false,
	'method'=>'get',
)); ?>

       <div class="row">
           <span style="white-space:nowrap;float:left;position: relative; left:-20px"><?php echo $form->label($model,'CODE OR destination'); ?></span>
        <?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
          'name'=>'Quotes[destination]',
            'id'=>'destination'.$_GET["tabid"],
          'sourceUrl' => array('quotes/search'),
          'options'=>array(
                'minLength'=>'1',
          ),
        'htmlOptions'=>array(
              'style'=>'height:20px;',
         ),
         )); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'departure'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
          'name'=>'Quotes[departure]',
          'id'=>'departurte'.$_GET["tabid"],
          'source'=> $this->createUrl('quotes/search'), 
         'options'=>array(
                'minLength'=>'2',
             'autoFocus'=>'false'	
              
          ),
        'htmlOptions'=>array(
              'style'=>'height:20px;',
         ),
         )); ?>
		
	</div>
	<div class="row">
		<?php echo $form->label($model,'ULD type'); ?>
		<?php echo CHtml::dropDownList('Quotes[uld_type]',$model->uld_type,array('pallet'=>'Pallet','AKE'=>'AKE','PMC'=>'PMC'),array('prompt'=>$this->t('All')));
		 ?>
	</div>
	<div class="row">
		<?php echo $form->label($model,'weight'); ?>
		<?php echo $form->textField($model,'wt_lo'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'date_eff'); ?>
		<?php echo CHtml::textField('Quotes[date_eff]', empty($model->date_eff) ? date('Y-m-d') : $model->date_eff, ['class' => 'date_input']); ?>
	</div>
    
	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->