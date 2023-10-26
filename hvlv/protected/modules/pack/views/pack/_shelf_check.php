<h1 style="width: 300px; text-align: center; float: left; position: relative; left: 50%; transform: translate(-50%)">货架 <?=$_GET['shelf_no']?> 检查中</h1> <h3 style="text-align: center; float: left; position: relative; left: -300px; margin-left: 0.64%; cursor: pointer; background-color: #14487E; border: 3px solid #be3426"><a href="<?=Yii::app()->createUrl('pack/pack/shelfCheck', ['shelf_no' => (3-$_GET['shelf_no'])])?>" style="text-decoration: none; color: #FFFFFF">切换到货架 <?=3-$_GET['shelf_no']?></a></h3>
<div style="clear: both"></div>
<br>

<?php
for ($i = 1; $i <= 21; $i++) {
	if (!empty($boxes[$i])) {
		$batch = $boxes[$i];
		if ($batch->task->status == 99) {
			$text = $batch->task->getNo() . '<br>已完成';
			$color = 'background-color: #4cd964';
		} else {
			$remain = $batch->task->getBatchSortingRemainByTask();
			if ($remain > 0) {
				$text = $batch->task->getNo() . '<br>在分拣';
				$color = 'background-color: #FC7272';
			} else {
				$text = $batch->task->getNo() . '<br>在打包';
				$color = 'background-image: linear-gradient(to right, #4cd964 50%, #FC7272 50%)';
			}
		}
	} else {
		$text = '空';
		$color = 'background-color: #4cd964';
	}

	echo '<div style="width: 13%; float: left; margin-left: 0.64%; margin-right: 0.64%"><a class="dash-item ajax-link" style="min-height: 200px; font-size: 40px; position: relative; cursor: pointer; ' . $color . '"><div class="row">箱子 ' . $i . '</div><div style="position: absolute; top: 60%; left: 50%; transform: translate(-50%, -50%)">' . $text . '</div></a></div>';
}
?>

<script type="text/javascript">
$(function() {
	setInterval(function() {
		window.location.reload();
	}, 5e3);
});
</script>