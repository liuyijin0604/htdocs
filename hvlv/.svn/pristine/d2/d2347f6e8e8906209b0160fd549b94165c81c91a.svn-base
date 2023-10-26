<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">预打包条码</h3>
    </div>
    <div class="panel-body" style="font-size: 30px;">
        <form role="form" method="post">
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <label for="org">公司：</label>
                        <input type="text" id="org" class="form-control" onfocus="pack.autocomplete(this, 'org', '<?=$this->createUrl('pack/autocomplete', array("type" => "org"))?>')" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <label for="product">商品：</label>
                        <input type="text" id="product" class="form-control" onfocus="pack.autocomplete(this, 'product', '<?=$this->createUrl('pack/autocomplete', array("type" => "product"))?>')" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <label for="box">箱内商品数：</label>
                        <input type="text" id="box" class="form-control" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <label for="quantity">数量：</label>
                        <input type="text" id="quantity" class="form-control" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <button type="button" id="submit" class="btn btn-primary btn-lg" onclick="pack.generateBarcode('<?=$this->createUrl('pack/generate')?>');">生成</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        pack.init();
    });
</script>