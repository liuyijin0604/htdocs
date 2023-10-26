<h1><?=$this->t('Create a receipt');?> <?php echo $model->hbn; ?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'receipt-form',
	'enableAjaxValidation'=>false,
	'action' => $this->createUrl('exParcel/receipt', ['id' => $model->id]),
	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form']
)); ?>

	<div class="row rowcol rowleft">
		<label>Order #:</label>
		<?php echo CHtml::textField('odr_no', '', array('size'=>15,'maxlength'=>30)); ?>
	</div>

	<div class="row rowcol">
		<label>Transaction #:</label>
		<?php echo CHtml::textField('trs_no', '', array('size'=>15,'maxlength'=>30)); ?>
	</div>

	<div class="row rowcol rowleft">
		<label>Receipt Type:</label>
		<label class="radio_label"><input type="radio" name="type" value="aur" checked> Auto </label> &nbsp;
		<label class="radio_label"><input type="radio" name="type" value="imo"> 摩登网</label>
	</div>

	<div class="row rowcol">
		<label>照片:</label>
		<label class="radio_label"><?php echo CHtml::checkBox('photo', 0); ?> 带背景照片</label>
	</div>

	<div class="row">
		<label>Items:</label>
		<table>
		<thead>
		<tr><th>Name</th><th>品名</th><th>Qty</th><th>Value</th><th>Discount</th></tr>
		</thead>
		<tbody>
		<?php
		foreach($model->eitems['g'] as $i=>$g){
			$ne= '';
			if(!empty($model->eitems['pid'][$i])){
				$p = ExProdb::model()->findByPk($model->eitems['pid'][$i]);
				$ne = $p->name;
			}
			echo '<tr><td><input type="text" name="item['.$i.'][gen]" value="'.$ne.'" size="20" /></td><td><input type="text" name="item['.$i.'][g]" value="'.$g.'" size="20" /></td><td><input type="text" name="item['.$i.'][q]" value="'.$model->eitems['q'][$i].'" size="5" /></td><td><input type="text" name="item['.$i.'][t]" value="'.$model->eitems['t'][$i].'" size="5" /></td><td><input type="text" name="item['.$i.'][d]" value="0" size="5" /></td></tr>';
		}
		?>
		</tbody>
		</table>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Show')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->