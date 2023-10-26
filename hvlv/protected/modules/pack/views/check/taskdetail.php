<?php
if ($result === false) { 
    echo "<div class=\"alert alert-danger\" style=\"text-align:center; font-size:32px; margin-top:2px\">" . $text . "</div>";
    echo "<script>$('#input').val(''); $('#input').focus(); </script>";
} else {
?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">订单商品详情</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>订单号 / 快递单号</th>
                                <th>条形码</th>
                                <th>名称</th>
                                <th>备注</th>
                                <th>数量</th>
                                <th>已扫商品数量</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product) { ?>
                            <tr id="tr_<?=$product["ean"]?>">
                                <td><?=$product["taskid"]?></td>
                                <td><?=$product["ean"]?></td>
                                <td><?=$product["name_zh"] . " - " . $product["name"]?></td>
                                <td><?=$product["note"]?></td>
                                <?php $extraQuantity = $product["pq"] ? $product["pq"] . "板" : ($product["cq"] ? $product["cq"] . "箱" : ""); ?>
                                <td><?=$product["uq"] . "件" . ($extraQuantity ? "(" . $extraQuantity . ")" : "") ?></td>
                                <td id="scan_<?=$product["ean"]?>">0</td>
                                <td id="icon_<?=$product["ean"]?>"></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div id="msgdiv"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        pack.products = [];
        <?php foreach($products as $product) { ?>
            pack.products['<?=$product["ean"]?>'] = {'name':'<?=$product["name_zh"] . " - " . $product["name"]?>', 'quantity':'<?=$product["uq"]?>', 'scanquantity':0};
            pack.products.length ++;
        <?php } ?>
    </script>
<?php } ?>