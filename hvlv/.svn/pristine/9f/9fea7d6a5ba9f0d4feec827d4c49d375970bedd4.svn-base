<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
          'Org List'=>array('accounts/orgList'),
          'chargeCode List'=>array('accounts/chargecodeList'),
          'ChargeCode-'.$model->chargecode  
	),
));
?>
<div class="container">
<h3><?=$this->t('Update Import Charge Code');?>-<?=$model->chargecode?></h3>
<?php echo $this->renderPartial('_form_charge_code',['model' => $model]); ?>
</div>
<?php ob_start(); ?>
<script type="text/javascript">
    $(function(){
        
    })
</script>
<?php $this->registerJS(ob_get_clean(),8); ?>