<div class="panel panel-primary">
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
							<button type="button" id="submit" class="btn btn-default" onclick="pack.transfer();">搜 索</button>
						</span>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<div id="msgdiv"></div>

<?php ob_start(); ?>
<script type="text/javascript">
	$(document).ready(function() {
		$(window).data({'barcode_buffer': ''});
		pack.init();
		pack.completeUrl = '<?=$this->createUrl('pack/transfer')?>';

		websocket_connect();

		function websocket_connect() {
			pack.ws = new WebSocket("ws://127.0.0.1:10080");

			pack.ws.onopen = function() {
				pack.weightstatus = true;
				pack.showInfo('msgdiv', 'success', '请扫描原始快递码');
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
				$('#weight').text(msg.data);
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
	});
</script>
<?php $this->registerJS(ob_get_clean()); ?>