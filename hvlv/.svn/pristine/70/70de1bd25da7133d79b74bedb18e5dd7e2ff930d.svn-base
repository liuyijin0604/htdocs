<style type="text/css">
.row
{
	height: 50px;
}

input[type="checkbox"]
{
	width: 30px;
	height: 30px;
	vertical-align: middle;
	margin-right: 20px;
}

.other-form span
{
	font-size: 20px;
	vertical-align: middle;
}
</style>

<div class="other-form">
	<form action="<?=$this->createUrl('job/other', ['id' => $model->id])?>" method="post" data-bit="1">
		<div class="row">
			<?php echo CHtml::checkbox('other_plain', @$model->mainTask->mdata['other_plain']); ?> <span>Change plain pallet</span>
		</div>
		<div class="row">
			<?php echo CHtml::checkbox('other_chep', @$model->mainTask->mdata['other_chep']); ?> <span>Change Chep / Loscam</span>
		</div>
		<div class="row">
			<?php echo CHtml::checkbox('other_wood', @$model->mainTask->mdata['other_wood']); ?> <span>Change plastic / wood pallet</span>
		</div>
		<div class="row">
			<span>Other</span>
			<?php echo CHtml::textarea('other_other', @$model->mainTask->mdata['other_other']); ?>
		</div>
		<?php if ($model->mainTask->status < 99) { ?>
			<button type="submit" class="btn btn-primary btn-block" name="search"><span class="icon icon-search"></span>Submit</button>
		<?php } ?>
	</form>
</div>