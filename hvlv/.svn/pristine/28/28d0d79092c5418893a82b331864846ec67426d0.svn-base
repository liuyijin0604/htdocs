<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'ex-channel-rules-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row rowcol">
		<?php echo '重复件数: ', CHtml::textField('mdata[dupq]', @$model->mdata['dupq'], ['size' => 5]);?>
	</div>
	<div class="row rowcol">
		<?php echo '奶粉完税价规则: ', CHtml::dropDownList('mdata[ftc]', @$model->mdata['ftc'], [1 => '完税价格/kg', 2 => '完税价格/罐', 0 => '不参考完税价']), ' </label>';?>
	</div>
	<div class="row rowcol">
		<?php echo '<label>', CHtml::checkBox('mdata[altCnee]', @$model->mdata['altCnee']),' 模糊匹配收件人</label>'; ?>
	</div>
<div style="margin-top: 1em;clear:both">
<b style="float: left; margin-right: 10px;">服务类型: </b>
<?php
foreach(ExParcel::$styps as $t => $n):
?>
<div class="row rowcol">
	<?php echo '<label>', CHtml::checkBox('mdata[actService_'.$t.']', @$model->mdata['actService_'.$t]),' 允许 ',$n,'</label>'; ?>
</div>
<?php endforeach; ?>
</div>
<?php foreach(['B', 'M', 'O', 'X'] as $t): ?>
<div style="margin-top: 1em;">
<div class="row rowcol rowleft">
	<?php echo '<label>', CHtml::checkBox('mdata[allowType_'.$t.']', @$model->mdata['allowType_'.$t]),' 允许货物 ',$t,'</label>'; ?>
</div>
<div class="row rowcol">
	<?php echo '件数: ', CHtml::textField('mdata[minQty_'.$t.']', @$model->mdata['minQty_'.$t], ['size' => 2]), ' - ', CHtml::textField('mdata[maxQty_'.$t.']', @$model->mdata['maxQty_'.$t], ['size' => 2]); ?>
</div>
<div class="row rowcol">
	<?php echo '屏蔽关键词: ', CHtml::textField('mdata[excl_'.$t.']', @$model->mdata['excl_'.$t], ['size' => 50]); ?>
</div>
</div>
<?php endforeach; ?>

	<div class="row">
		<?php echo CHtml::label('自定义函数','ccf');?>
		<span>function check(string $type, int $qty, string $goods, float $weight, object &$parcel):boolean{</span><br />
		<?php echo CHtml::textArea('mdata[ccf]', @$model->mdata['ccf'], ['rows' => '10', 'cols' => '80', 'readonly' => Yii::app()->user->grp > 0]); ?><br />
		<span>}</span>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('保存'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->