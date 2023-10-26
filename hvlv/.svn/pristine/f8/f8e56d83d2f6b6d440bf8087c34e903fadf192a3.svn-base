<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'links' => array(
        'Update Manifest',
    ),
));
?>

<h1><?=$this->t('Update Manifest');?> <?php echo $model->id; ?> - <i><?=$model->getStatus();?></i></h1>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'manifest-form',
	'enableAjaxValidation'=>false,
));
?>


    <div class="row">
                <div class="col col-sm-3">
                    <div class="form-group">
                     <?php echo CHtml::dropdownList('status', $model->status, $model->statusList(), array('empty' => 'Select One', 'class' => 'required')); ?>
	                </div>
                </div>
	</div>

<div class="form-group">
    <div class="buttons">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>
</div>

<?php $this->endWidget(); ?>

</div><!-- form -->


<?php ob_start(); ?>

<script type="text/javascript">
$(function(){

});
</script>
<?php $this->registerJS(ob_get_clean(),8); ?>