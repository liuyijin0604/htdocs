<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
          'Org List'=>array('accounts/orgList'),
          'chargeCode List'=>array('accounts/chargecodeList'),
          'Create Chargecode',
	),
));
?>
<h1><?=$this->t('Create Import Charge Code');?></h1>

<?php echo $this->renderPartial('_form_charge_code',['model' => $model]); ?>

