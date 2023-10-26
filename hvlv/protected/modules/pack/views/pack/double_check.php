<div class="panel panel-primary" id="search_task_bar">
	<div class="panel-heading">
		<h3 class="panel-title">查找Task</h3>
	</div>
	<div class="panel-body" style="font-size: 30px;">
		<div class="row">
			<div class="col-xs-12">
				<div class="input-group">
					<input type="text" id="search_task" class="form-control" />
					<span class="input-group-btn">
						<button type="button" id="search_task_btn" class="btn btn-default">搜 索</button>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-primary" style="display: none">
	<div class="panel-heading">
		<h3 class="panel-title">请扫码</h3>
	</div>
	<div class="panel-body">
		<form role="form" method="post">
			<div class="row">
				<div class="col-xs-12">
					<div class="input-group">
						<input type="text" id="input" class="form-control" autocomplete="off" />
						<span class="input-group-btn">
							<button type="button" id="submit" class="btn btn-default" onclick="">搜 索</button>
						</span>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="res"></div>

<?php ob_start(); ?>
<script type="text/javascript">
	$(document).ready(function() {
		$('#search_task_btn').on('click', function() {
			$('.res').load('<?=Yii::app()->createUrl("pack/pack/doubleCheck")?>?'.replace('.app', '') + 'id=' + $('#search_task').val());
			$('#input').focus();
		});

		$('#input').on('keydown', function(e) {
			if (e.which == 13) {
				$(this).trigger('afterBarcode');
				return false;
			}
		}).on('afterBarcode', function() {
			$('#submit').trigger('click');
		});
	});
</script>
<?php $this->registerJS(ob_get_clean()); ?>