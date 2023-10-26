<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">请扫描条码</h3>
    </div>
    <div class="panel-body">
        <form role="form" method="post">
            <div class="row">
                <div class="col-xs-12">
                    <div class="input-group">
                        <input type="hidden" id="type" value="check">
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

<div id="taskdetail"></div>

<script>
    $(document).ready(function() {
        $(window).data({'barcode_buffer': ''});
        pack.init();
        pack.detailUrl = '<?=$this->createUrl('check/detail')?>';
        pack.completeUrl = '<?=$this->createUrl('check/complete')?>';
    });
</script>