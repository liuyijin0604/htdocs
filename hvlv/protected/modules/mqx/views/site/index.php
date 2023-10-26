<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'homeLink'=>CHtml::link('主页', array('site/index')),
    'links' => array(
           '投保须知',
    ),
));
?>
<br>
<div class="panel panel-default">
	<div class="panel-heading">投保须知</div>
	<div class="panel-body">
		<p style="font-size: 20px;">
			好处:<br>
				- 不管什么原因，只要确认有问题就直接赔付<br>
				- 全额保险（最高值$200AUD or ￥1000RMB）<br>
				- 48小时内保证理赔完毕<br>
				- 赔额直接到账<br>
				- 具体<a href="https://www.pcaexpress.com.au/zh/出口小包理赔细则-2018年4月更新/" target="_blank">理赔条款</a>请点击详见<br>
		</p>
		<a class="btn btn-success" id="purchase-btn" href="<?=$this->createUrl('order/search')?>">我要投保</a>
		<a class="btn btn-danger" id="compensation-btn" href="<?=$this->createUrl('order/compensation')?>">我要理赔</a>
	</div>
</div>