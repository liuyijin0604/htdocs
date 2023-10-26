<?php
	$title ='Bagging';

$this->widget('zii.widgets.CBreadcrumbs', array(
    'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
		$title,
	),
));
?>
<center>
<h1><?=ucfirst(Yii::app()->session['scan_warehouse'])?>  Warehouse</h1>
</center>

<h1><?=$title?></h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', [
	'id'=>'scan-form',
	'enableAjaxValidation'=>false,
]);
?>
<div style="float:right;"><label>Auto Print: <input type="checkbox" id="auto_print" name="auto_print" value="1" /></label> &nbsp; <label>Alt Sound: <input type="checkbox" name="sound" value="1" /></label></div>
<div style="font-size: 1.5em;">Barcode: <input style="width: 95%" id="scan" type="text" size="30" name="barcode" autocomplete="off" /></div>

<br>
<br>
<br>
<br>
<?php $this->endWidget();?>

<?php
$this->renderPartial('bag_list', ["model"=>$model]);
?>


<?php ob_start(); ?>
<script type="text/javascript">
$(function(){

	});


});
</script>
<?php $this->registerJS(ob_get_clean()); ?>