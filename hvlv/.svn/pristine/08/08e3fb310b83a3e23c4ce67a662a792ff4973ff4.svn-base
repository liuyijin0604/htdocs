<style type="text/css">
#parcels span {
	font-style: italic;
}
#parcels span.status_0 {
	color: #c00;
}
#parcels span.status_1{
	color: #0c0;
	font-weight: bold;
}
.download {
	font-size: 20px;
	font-style: italic;
	color: blue;
}
</style>
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
							<button type="button" id="submit" class="btn btn-default" onclick="weigh.scanShipment();">搜 索</button>
						</span>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<div id="msgdiv"></div>

<div class="panel panel-primary">
	<div class="panel-body" style="font-size: 30px;">
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				当前包裹重量为：<span id="weight" class="text-success"></span> kg
			</div>
		</div>
	</div>
</div>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">已扫包裹</h3>
	</div>
	<div class="panel-body" id="parcels">
	</div>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
	$(document).ready(function() {
		$(window).data({'barcode_buffer': ''});
		weigh.init();
		weigh.completeUrl = '<?=$this->createUrl('shipment/weighComplete')?>';
		weigh.batchUrl = '<?=$this->createUrl('shipment/scanBatch')?>';

		websocket_connect();

		function websocket_connect() {
			weigh.ws = new WebSocket("ws://127.0.0.1:10080");

			weigh.ws.onopen = function() {
				weigh.weightstatus = true;
				weigh.showInfo('msgdiv', 'success', '请扫描快递单号');
			}

			weigh.ws.onclose = function() {
				if (window.location.href.match(/shipment\/weigh/g)) {
					weigh.showInfo('msgdiv', 'fail', '请打开称重工具 <a href="/weigh.zip" class="download">点此下载</a>');
					weigh.weightstatus = false;
					websocket_reconnect();
				}
			}

			weigh.ws.onerror = function(e) {
				weigh.showInfo('msgdiv', 'fail', '请打开称重工具 <a href="/weigh.zip" class="download">点此下载</a>');
				weigh.weightstatus = false;
			}

			weigh.ws.onmessage = function(msg) {
				$('#weight').text(parseFloat(msg.data).toFixed(2));
			}
		}

		function websocket_reconnect() {
			setTimeout(function() {
				websocket_connect();
			}, 1e3);
		}

		$(window).unload(function() {
			weigh.ws.close();
		});

		weigh.sendShipmentWeight();
	});
</script>
<?php $this->registerJS(ob_get_clean()); ?>