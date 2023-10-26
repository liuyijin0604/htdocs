<div class="panel panel-primary" style="display: none;">
    <div class="panel-heading">
        <h3 class="panel-title">请扫码</h3>
    </div>
    <div class="panel-body">
        <form role="form" method="post">
            <div class="row">
                <div class="col-xs-12">
                    <div class="input-group">
                        <input type="hidden" id="type" value="pack">
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

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">打包信息</h3>
    </div>
    <div class="panel-body" style="font-size: 30px;">
        <div class="row">
            <div class="col-sm-6 col-xs-12">
                当前订单为：<span id="taskid" class="text-danger">未扫描</span>
            </div>
            <div class="col-sm-6 col-xs-12">
                当前包裹重量为：<span id="weight"></span> kg
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-xs-12">
                当前耗材为：<span id="wrapper" class="text-danger">未扫描</span>
            </div>
            <div class="col-sm-6 col-xs-12">
                当前纸箱规格为：<span id="box" class="text-danger">未扫描</span>
            </div>
        </div>
    </div>
</div>

<div id="taskdetail"></div>

<script>
    $(document).ready(function() {
        $(window).data({'barcode_buffer': ''});
        pack.init();
        pack.detailUrl = '<?=$this->createUrl('pack/detail')?>';
        pack.completeUrl = '<?=$this->createUrl('pack/complete')?>';
        pack.printUrl = '<?=$this->createUrl('pack/print')?>';
        pack.checkUrl = '<?=$this->createUrl('pack/check')?>';
        <?php foreach ($boxs as $box) { ?>
            pack.boxs['<?=$box->ean?>'] = '<?=$box->name_zh?>';
        <?php } ?>
        <?php foreach ($wrappers as $wrapper) { ?>
            pack.wrappers['<?=$wrapper->ean?>'] = '<?=$wrapper->name_zh?>';
        <?php } ?>

        var ws = new WebSocket("ws://127.0.0.1:80");
        var close = 0;

        ws.onopen = function() {
            close = 1;
            // $('#weightstate').text("称重工具已连接");
            //console.log("Socket状态: 连接");
        }

        ws.onclose = function() {
            if (close) {
                pack.showInfo('msgdiv', 'fail', '请打开称重工具，并重新打开此页面 <a href="https://os.pcaex.com/Weigh.zip" target="_blank">(点击下载)</a>');
                pack.weightstatus = false;
                // $('#weightstate').text("称重工具已连接");
            }
            //console.log("Socket状态: 关闭");
        }

        ws.onerror = function(e) {
            pack.showInfo('msgdiv', 'fail', '请打开称重工具，并重新打开此页面 <a href="https://os.pcaex.com/Weigh.zip" target="_blank">(点击下载)</a>');
            pack.weightstatus = false;
            // $('#weightstate').text("请打开称重工具，并重新打开此页面 <a href="https://os.pcaex.com/Weigh.zip" target="_blank">(点击下载)</a>");
            //console.log("出错了");
        }

        ws.onmessage = function(msg) {
            // console.log("接收数据: " + msg.data);
            $('#weight').text(msg.data);
        }
    });
</script>