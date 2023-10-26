<div class="panel panel-primary" style="display: none;">
	<div class="panel-heading">
		<h3 class="panel-title">请扫码</h3>
	</div>
	<div class="panel-body">
		<form role="form" method="post">
			<div class="row">
				<div class="col-xs-12">
					<div class="input-group">
						<input type="hidden" id="type" value="whole">
						<input type="text" id="input" class="form-control" autocomplete="off" readonly="readonly" />
						<span class="input-group-btn">
							<button type="button" id="submit" class="btn btn-default" onclick="pack.scanTask();">搜 索</button>
						</span>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<div id="msgdiv"></div>

<div class="panel panel-primary" id="search_task_bar">
	<div class="panel-heading">
		<h3 class="panel-title">查找Task</h3>
	</div>
	<div class="panel-body" style="font-size: 30px;">
		<div class="row">
			<div class="col-sm-6 col-xs-12">
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

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">打包信息</h3>
	</div>
	<div class="panel-body" style="font-size: 30px;">
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				当前订单为：<span id="taskid" class="text-danger">未扫描</span>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-5 col-xs-12">
				当前包裹重量为：<input type="text" id="weight" class="text-success" size="10" /> kg
			</div>
			<div class="col-sm-3 col-xs-12">
				是否使用了耗材：<span id="material" class="text-success">是</span>
			</div>
			<div class="col-sm-4 col-xs-12">
				面单备注：<span id="label" class="text-success"></span>
			</div>
		</div>
		<div class="row" id="dim" style="margin-top: 20px">
			<div class="col-sm-12 col-xs-12">
				三边：长 <input type="text" id="depth" class="text-success" size="3" /> cm X 宽 <input type="text" id="width" class="text-success" size="3" /> cm X 高 <input type="text" id="height" class="text-success" size="3" /> cm
			</div>
		</div>
		<!-- <div class="row">
			<div class="col-sm-6 col-xs-12">
				拍照备注：<span id="mark" class="text-success"></span>
			</div>
		</div> -->
	</div>
</div>

<div id="taskdetail"></div>

<button type="button" class="btn btn-info btn-lg" id="showDim" data-toggle="modal" data-target="#dimModal" style="display: none">Open Modal</button>
<div id="dimModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content" style="width: 170%; height: 500px; position: relative; right: 35%; top: 200px;">
			<div class="modal-header"></div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal" id="modalClose">Close</button>
			</div>
		</div>
	</div>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
	$(document).ready(function() {
		$(window).data({'barcode_buffer': ''});
		pack.init();
		pack.detailUrl = '<?=$this->createUrl('pack/detail')?>';
		pack.completeUrl = '<?=$this->createUrl('pack/complete')?>';
		pack.printUrl = '<?=$this->createUrl('pack/print')?>';
		pack.checkUrl = '<?=$this->createUrl('pack/check')?>';
		pack.boxUrl = '<?=$this->createUrl("pack/box")?>';
		pack.singleItemUrl = '<?=$this->createUrl("pack/singleItem")?>';
		pack.dimUrl = '<?=$this->createUrl("pack/dim")?>';
		pack.holdTaskUrl = '<?=$this->createUrl("pack/holdTask")?>';

		websocket_connect();

		function websocket_connect() {
			pack.ws = new WebSocket("ws://127.0.0.1:10080");

			pack.ws.onopen = function() {
				pack.showInfo('msgdiv', 'success', '请扫描拣货完成生成的二维码 或 分货箱条码');
			}

			pack.ws.onclose = function() {
				if (window.location.href.match(/whole/g)) {
					pack.showInfo('msgdiv', 'fail', '请打开称重工具，并重新打开此页面 <a href="https://os.pcaex.com/Weigh.zip" target="_blank">(点击下载)</a>');
					pack.weightstatus = false;
					websocket_reconnect();
				}
			}

			pack.ws.onerror = function(e) {
				pack.showInfo('msgdiv', 'fail', '请打开称重工具，并重新打开此页面 <a href="https://os.pcaex.com/Weigh.zip" target="_blank">(点击下载)</a>');
				pack.weightstatus = false;
			}

			pack.ws.onmessage = function(msg) {
				if (msg.data > 0) $('#weight').val(msg.data);
			}
		}

		function websocket_reconnect() {
			setTimeout(function() {
				websocket_connect();
			}, 1e3);
		}

		$(window).unload(function() {
			pack.ws.close();
		});

		$('#search_task_btn').on('click', function() {
			var search_task_id = $('#search_task').val();
			search_task_id = search_task_id.replace('T', '');
			search_task_id = 'T' + search_task_id + ' complete';

			$('#input').val(search_task_id);
			$('#submit').trigger('click');
		});
	});
</script>
<?php $this->registerJS(ob_get_clean()); ?>