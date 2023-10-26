<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'homeLink'=>CHtml::link('Home / 主页', array('../' . ($this->layout?$this->layout:'cg') . '/site/index')),
    'links' => array(
          'Order History / 订单历史'=>array('../' . ($this->layout?$this->layout:'cg') . '/order/history'),
           'Order Details / 订单详情',
    ),
));
?>
<h1>Order Details / 订单详情</h1>
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'cg-order-make-form',
        'enableAjaxValidation'=>false,
    ));
    ?>

    <div class="form-group table-responsive">
        <table id="items" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th class="col-xs-3">Product / 品名</th>
                    <th class="col-xs-2">Qty / 数量</th>
                    <th class="col-xs-2">Total QTY / 总数量</th>
                    <th class="col-xs-2">Price / 单价</th>
                    <th class="col-xs-2">Sub Total / 小计</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $index => $product) { ?>
                <tr class="<?=$index%2==0?'even':'odd'?>">
                    <td><span id="name_<?=$product['id']?>">
                        <?php
                            if ($product['price']) {
                                echo $product['name'] . " (每" . $product['moq'] . "个为1个单位)";
                            } else {
                                if ($product['ean'] == 'P000002' && !empty($model->extra['thermal'])) {
                                    echo $product['name'] . " (每满" . $product['moq'] . "个纸箱配送1卷)";
                                } else if ($product['ean'] == 'P000003') {
                                    echo $product['name'] . "(至多可配送1本)";
                                } else {
                                    echo $product['name'];
                                }
                            }
                        ?>
                    </td>
                    <td><?=$product['qty']?></td>
                    <td><?=$product['tqty']?></td>
                    <td>$ <?=number_format($product['price'], 2)?></td>
                    <td>$ <?=$product['subt']?></td>
                </tr>
                <?php } ?>
                <tr class="<?=($index++)%2==0?'even':'odd'?>">
                    <td></td><td></td><td></td><td>Sub Total / 小计:</td><td>$ <span id="prodt"><?=number_format($prodt, 2)?></span></td>
                </tr>
                <tr class="<?=($index++)%2==0?'even':'odd'?>">
                    <td></td><td></td><td></td><td>Freight / 运费:</td><td>$ <span id="freight"><?=number_format($freight, 2)?></td>
                </tr>
                <tr class="<?=($index++)%2==0?'even':'odd'?>">
                    <td></td><td></td><td></td><td>Total / 总计:</td><td>$ <span id="total"><?=number_format($total, 2)?></td>
                </tr>
            </tbody>
        </table>

        <div class="form-group"><?php echo $this->renderPartial('account_form', array('model'=>$model, 'form'=>$form)); ?></div>

        <?php if ($status == 10) { ?>
            <div class="form-group">
                <?php echo CHtml::Button('Pay / 支付', array('class'=> 'btn btn-primary', 'id' => 'order-btn', 'target'=>'_blank', 'onclick' => 'submit();')); ?>
            </div>
        <?php } ?>
    </div>

    <?php $this->endWidget(); ?>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {
    var action = $('form').attr('action');
    if ('<?=$status?>' == 10) {
        $('form').attr('action', action.replace('.app', '/pay/true.app'));
    }
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>