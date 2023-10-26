<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
		'Shipments' => array('customerService/index'),
           'Check Detail',
	),
));
?>
<h2><?=$this->t("Shipment Reference Number").":".$model->Shipment->ref?></h2>
<?= $model->s_note ?>
<br>
 