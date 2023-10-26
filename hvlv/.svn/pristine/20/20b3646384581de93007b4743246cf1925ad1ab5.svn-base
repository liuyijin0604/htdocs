<h1 style="text-align: center;">Welcome - <?= Yii::app()->user->name; ?></h1>
<div class="row dyo_list">
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
		<a class="dash-item ajax-link" href="<?= $this->createUrl('chat/index'); ?>"><span class="glyphicon glyphicon-equalizer"></span><br/>View Cases<br/>查看Cases</a>
	</div>
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
		<a class="dash-item" href="<?= $this->createUrl('site/logout'); ?>"><span class="glyphicon glyphicon-log-out"></span><br/>Logout<br/>退出</a>
	</div>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {
  $('#msg').effect('shake', 'slow');
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>
