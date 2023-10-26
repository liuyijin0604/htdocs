<style>    
#warning{
        display:none;
        margin: 10px 0;
        border: 1px solid;
        padding:15px 20px;
        font-size: 14px;
        background: #fe0;
    }
 </style>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'shipment-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' =>[
		'data-bit' => '1',
	]
));
?>
    <?= $form->hiddenField($model,"id")?>
	<div class="form-group buttons">
		<button type="submit" class="btn btn-primary btn-lg"><?=$this->t('确认可以发货');?></button>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('form#shipment-form').on('success', function(r,s,x,f){
        $('#task_grid_view').yiiGridView('update');
        return true;
    }).on('submit', function(){
        $('input#scan').focus();
    });
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>