<h2><?=$this->t('System Settings');?></h2>
<div class="form">
<form name="settings-form" action="<?php echo Yii::app()->request->requestUri; ?>" method="post">
<?php
$ss = Yii::app()->params['settings'];
foreach($ss as $k=>$p):
	$id = 'settings_'.$k;
?>
	<div class="row">
		<?php echo CHtml::label($p['label'], $id); ?>
		<?php 
		if(empty($p['opts'])){
			echo CHtml::textField('Settings['.$k.'][value]', $p['value'], array('id' => $id));
		}else{
			echo CHtml::dropDownList('Settings['.$k.'][value]', $p['value'], $p['opts'], array('id' => $id));
		}
		?>
	</div>
<?php endforeach; ?>
<input type="submit" name="submit" value="Save" />
</form>
</div>