<div class="row">
	<div class="col-12 input-addon">
		<input type="search" placeholder="Length (cm)" name="meta[length]" value="<?=$model->depth?>" />
		<span><div class="toggle klength"><div class="toggle-handle"></div></div></span> cm
	</div>
	<div class="col-12 input-addon">
		<input type="search" placeholder="Width (cm)" name="meta[width]" value="<?=$model->width?>" />
		<span><div class="toggle kwidth"><div class="toggle-handle"></div></div></span> cm
	</div>
</div>
<div class="row">
	<div class="col-12 input-addon">
		<input type="search" placeholder="Height (cm)" name="meta[height]" value="<?=$model->height?>" />
		<span><div class="toggle kheight"><div class="toggle-handle"></div></div></span> cm
	</div>
	<div class="col-12 input-addon">
		<input type="search" placeholder="Weight (kg)" name="meta[weight]" value="<?=$model->wt?>" />
		<span><div class="toggle kweight"><div class="toggle-handle"></div></div></span> kg
	</div>
</div>
<div class="row">
	<?php echo '<div class="col-6">' . CHtml::radioButtonList('meta[type]', empty($model->extra['type']) ? 'plastic' : $model->extra['type'], array('plastic' => '塑料板', 'chep' => '熏蒸板'), array('separator' => '</div><div class="col-6">', 'style' => 'margin: 10px 0')) . '</div>'; ?>
</div>
<div class="row">
	<?php echo '<div class="col-6">' . CHtml::radioButtonList('meta[port]', empty($model->extra['port']) ? array_keys(oList::kvp('ex_port'))[0] : $model->extra['port'], oList::kvp('ex_port'), array('separator' => '</div><div class="col-6">', 'style' => 'margin: 10px 0')) . '</div>'; ?>
</div>
<div class="row">
	<?php echo '<div class="col-4 plt-col-4">是否换板</div><div class="col-2">' . CHtml::radioButtonList('meta[change]', empty($model->extra['change']) ? 'no' : $model->extra['change'], array('no' => '否', 'yes' => '是'), array('separator' => '</div><div class="col-6">', 'style' => 'margin: 10px 0')) . '</div>'; ?>
</div>
<div class="row">
	<?php echo '<div class="col-4 plt-col-4">是否缠绕膜</div><div class="col-2">' . CHtml::radioButtonList('meta[chanraomo]', empty($model->extra['chanraomo']) ? 'no' : $model->extra['chanraomo'], array('no' => '否', 'yes' => '是'), array('separator' => '</div><div class="col-6">', 'style' => 'margin: 10px 0')) . '</div>'; ?>
</div>
<div class="row">
	<?php echo '<div class="col-4 plt-col-4">是否井字带</div><div class="col-2">' . CHtml::radioButtonList('meta[jingzidai]', empty($model->extra['jingzidai']) ? 'no' : $model->extra['jingzidai'], array('no' => '否', 'yes' => '是'), array('separator' => '</div><div class="col-2">', 'style' => 'margin: 10px 0')) . '</div>'; ?>
</div>
<div class="row">
	<?php echo '<div class="col-4 plt-col-4">是否固脚</div><div class="col-2">' . CHtml::radioButtonList('meta[gujiao]', empty($model->extra['gujiao']) ? 'no' : $model->extra['gujiao'], array('no' => '否', 'yes' => '是'), array('separator' => '</div><div class="col-2">', 'style' => 'margin: 10px 0')) . '</div>'; ?>
	<?php echo '<div class="col-4">' . CHtml::textfield('meta[gujiaogeshu]', empty($model->extra['gujiaogeshu']) ? '' : $model->extra['gujiaogeshu'], ['placeholder' => '固脚个数']) . '</div>'; ?>
</div>
<div class="row">
	<div class="col-12">
		<textarea placeholder="note" name="note"></textarea>
	</div>
</div>