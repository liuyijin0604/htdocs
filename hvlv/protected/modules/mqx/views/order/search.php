<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
		'homeLink'=>CHtml::link('主页', array('site/index')),
		'links' => array(
					 '我要投保',
		),
));
?>
<br>
<div class="panel panel-success">
	<div class="panel-heading">我要投保</div>
	<div class="panel-body">
		<form role="form" action="<?=$this->createUrl('order/detail')?>" method="get">
			<div class="form-group">
				<label><span class="required">*</span> 运单号 (多个运单号请以符号分割)</label>
				<textarea name="trackno" class="form-control" placeholder="请输入您的运单号" id="trackno" rows="5" required></textarea>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-success">检索</button>
				<a class="btn btn-default" href="<?=$this->createUrl('site/index')?>">返回</a>
			</div>
		</form>
	</div>
</div>