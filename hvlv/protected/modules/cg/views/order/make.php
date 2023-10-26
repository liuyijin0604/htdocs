<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'homeLink'=>CHtml::link('Home / 主页', array('../' . ($this->layout?$this->layout:'cg') . '/site/index')),
    'links' => array(
           'Make An Order / 订购耗材',
    ),
));
?>
<h1>Make an order / 订购耗材</h1>
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
                    <th class="col-xs-2">Total Qty / 总数量</th>
                    <th class="col-xs-3">Price / 单价</th>
                    <th class="col-xs-2">Sub Total / 小计</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $index => $product) {
                    if (!empty($product->mdata)) { ?>
                <tr class="<?=$index%2==0?'even':'odd'?>" id="<?=$product->id?>">
                    <td><span id="name_<?=$product->id?>">
                        <?php
                            if ($product->mdata['price']) {
                                echo $product->name . " (每" . $product->mdata['moq'] . "个为1个单位)";
                            } else {
                                if ((preg_match('/热敏/', $product->name) && !empty($model->extra['thermal'])) || (preg_match('/缠绕膜/', $product->name) && !empty($model->extra['wrapper'])) || (preg_match('/透明胶带/', $product->name) && !empty($model->extra['tape']))) {
                                    echo $product->name . " (每满" . $product->mdata['bqtf'] . "个纸箱配送1卷 <small class='text-danger'>*且依据之前配送数量调整</small>)" . " (每" . $product->mdata['moq'] . "个为1个单位)";
                                } elseif($product->ean == 'P000003') {
                                    echo $product->name . "(至多可配送1本)";
                                }else{
                                    echo $product->name . " (每" . $product->mdata['moq'] . "个为1个单位)";
                                }
                            }
                        ?>
                        &nbsp;&nbsp;<span id="icon_<?=$product->id?>" class="glyphicon glyphicon-eye-open" style="cursor: pointer;" onclick="view('<?=$product->id;?>');"></span>
                        <?php if (!empty($product->mdata['img'])) { ?>
                            <div id="img_<?=$product->id?>" style="position: absolute; display: none; z-index: 9999;"><img src="<?=Yii::app()->createAbsoluteUrl('/').$product->mdata['img']?>" width="200" /></div>
                        <?php } ?>
                    </td>
                    <td>
                        <?php 
                            echo CHtml::textField('qty['.$product->id.']',0,array('size'=>50,'maxlength'=>255,'class'=>'form-control','onkeyup'=>'cal('.$product->id.')','autocomplete'=>'off'));
                        ?>
                    </td>
                    <td><span id="tqty_<?=$product->id?>">0</span></td>
                    <td>$ <span id="price_<?=$product->id?>">
                        <?php if ($product->mdata['price'] == 0) {
                            echo number_format($product->mdata['price2'] * $discount, 2);
                        } else {
                            echo number_format($product->mdata['price'] * $discount, 2);
                        } ?>
                    </span></td>
                    <td>$ <span id="subt_<?=$product->id?>">0.00</span></td>
                </tr>
                <?php }} $index = (int)$index;?>
                <tr class="<?=($index++)%2==0?'even':'odd'?>">
                    <td></td><td></td><td></td><td>Sub Total / 小计:</td><td>$ <span id="prodt">0.00</span></td>
                </tr>
                <tr class="<?=($index++)%2==0?'even':'odd'?>">
                    <td></td><td></td><td></td><td>Freight / 运费: <small class="text-danger">（*$<?=number_format($freight_free_above, 0)?>以上免运费）</small></td><td>$ <span id="freight">0.00</td>
                </tr>
                <tr class="<?=($index++)%2==0?'even':'odd'?>">
                    <td></td><td></td><td></td><td>Total / 总计:</td><td>$ <span id="total" style="font-size:1.4em; font-weight:bold;">0.00</td>
                </tr>
            </tbody>
        </table>
        <p align="right"><small>* Free for order more than $<?=number_format($freight_free_above, 2)?> / $<?=number_format($freight_free_above, 2)?>以上免运费</small></p>

        <div class="form-group">
            <?php echo $this->renderPartial('account_form', array('model'=>$model, 'form'=>$form, 'order'=>true)); ?>
            <input type="checkbox" id="default_address" />&nbsp;&nbsp;<?php echo $form->labelEx($model, '使用默认地址'); ?>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="radio-inline">
                <h3><input type="radio" name="optionRadio" id="poli"  value="poli" style="margin-top: 12px;" checked /><img src="<?=Yii::app()->createAbsoluteUrl('/')?>/images/poli.png" width="100" /></h3>
            </label>
            <label class="radio-inline">
                <h3><input type="radio" name="optionRadio" id="wechatpay"  value="wechatpay" style="margin-top: 21px;" /><img src="<?=Yii::app()->createAbsoluteUrl('/')?>/images/wechatpay.png" width="100" /></h3>
            </label>
            <label class="radio-inline">
                <h3><input type="radio" name="optionRadio" id="aplipay" value="alipay" style="margin-top: 18px;" /><img src="<?=Yii::app()->createAbsoluteUrl('/')?>/images/alipay.png" width="100" /></h3>
            </label>
            <label class="radio-inline">
                <h3><input type="radio" name="optionRadio" id="trans" value="trans" style="margin-top: 7px;" /><span class="label label-success" style="display: inline-block;width: 100px;">转账</span></h3>
            </label>
            <?php if (!empty($model->extra['consum_postpay'])) { ?>
            <label class="radio-inline">
                <h3><input type="radio" name="optionRadio" id="credit" value="credit" style="margin-top: 7px;" /><span class="label label-warning" style="display: inline-block;width: 100px;">Credit</span></h3>
            </label>
            <?php } ?>
        </div>

        <div class="form-group" style="margin-top: 20px;">
            <?php echo CHtml::Button('Check Out / 支付', array('class'=> 'btn btn-primary ajax-link', 'id' => 'order-btn', 'target'=>'_blank', 'onclick' => 'submit();')); ?>
        </div>
    </div>

    <?php $this->endWidget(); ?>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {
    var info = [];

    if (typeof(Storage) !== "undefined") {
        $('#items tbody').children().each(function() {
            key = $(this).find('input[id^=qty]').attr('id');
            if (sessionStorage.getItem(key)) {
                $(this).find('input[id^=qty]').val(sessionStorage.getItem(key));
            }

            key = $(this).find('span[id^=tqty]').attr('id');
            if (sessionStorage.getItem(key)) {
                $(this).find('span[id^=tqty]').text(sessionStorage.getItem(key));
            }

            key = $(this).find('span[id^=subt]').attr('id');
            if (sessionStorage.getItem(key)) {
                $(this).find('span[id^=subt]').text(sessionStorage.getItem(key));
            }
        });

        if (sessionStorage.getItem('prodt')) {
            $('#prodt').text(sessionStorage.getItem('prodt'));
        }

        if (sessionStorage.getItem('freight')) {
            $('#freight').text(sessionStorage.getItem('freight'));
        }

        if (sessionStorage.getItem('total')) {
            $('#total').text(sessionStorage.getItem('total'));
        }
    }

    $('#items tbody').children().each(function() {
        var id = $(this).attr('id');
        if (id) {
            cal(id);
        }
    });

    $('#account_form input').each(function() {
        if ($(this).attr('id') != 'Org_name') {
            info.push($(this).val());
            $(this).val('');
        }
    });

    $('#default_address').on('change', function() {
        if ($('#default_address').is(':checked')) {
            $('#account_form input').each(function(index, item) {
                if ($(this).attr('id') != 'Org_name') {
                    $(this).val(info[index-1]);
                }
            });
        } else {
            $('#account_form input').each(function() {
                if ($(this).attr('id') != 'Org_name') {
                    $(this).val('');
                }
            });
        }
    });
});

function view(index) {
    $('#img_'+index).toggle();
    if ($('#icon_'+index).attr('class') == 'glyphicon glyphicon-eye-open') {
        $('#icon_'+index).removeClass('glyphicon glyphicon-eye-open');
        $('#icon_'+index).addClass('glyphicon glyphicon-eye-close');
    } else {
        $('#icon_'+index).removeClass('glyphicon glyphicon-eye-close');
        $('#icon_'+index).addClass('glyphicon glyphicon-eye-open');
    }
}

function cal(index) {
    var price = parseFloat($('#price_'+index).text());

    if ($('#name_'+index).text().match(/至多可配送(\d+)本/) && $('#name_'+index).text().match(/至多可配送(\d+)本/)[1] == 1) {
        var qty = parseInt($('#qty_'+index).val());
        qty = isNaN(qty) ? 0 : qty;
        qty = qty >= 1 ? 1 : 0;
        $('#qty_'+index).val(qty);
        $('#tqty_'+index).text(qty);
    } else {
        var qty = parseInt($('#qty_'+index).val());
        qty = isNaN(qty) ? 0 : qty;
        $('#qty_'+index).val(qty);
        var moq = $('#name_'+index).text().match(/每(\d+)个/);
        moq = moq ? parseInt(moq[1]) : 1;
        $('#tqty_'+index).text((qty * moq).toFixed(0));
        $('#subt_'+index).text((qty * moq * price).toFixed(2));

        var prodt = 0;
        var boxq = 0;
        $('#items tbody').children().each(function() {
            var qty2 = $(this).find('input[id^=qty]').val();
            var moq2 = $(this).find('span[id^=name]').text().match(/每(\d+)个/);
            var moq2 = moq2 ? parseInt(moq2[1]) : 1;
            var price2 = $(this).find('span[id^=price]').text();
            if (qty2 && price2 > 0) {
                prodt += qty2 * moq2 * price2;
            }
            name = $(this).find('span[id^=name]').text();
            if (name.match(/(\d+)罐纸箱/)) {
                boxq += qty2 * moq2;
            }
        });

        $('#items tbody').children().each(function() {
            var bqtf = $(this).find('span[id^=name]').text().match(/每满(\d+)个/) ? $(this).find('span[id^=name]').text().match(/每满(\d+)个/)[1] : 0;

            if (bqtf) {
                var qty2 = $(this).find('input[id^=qty]').val();
                var moq2 = $(this).find('span[id^=name]').text().match(/每(\d+)个/);
                var moq2 = moq2 ? parseInt(moq2[1]) : 1;
                var price2 = $(this).find('span[id^=price]').text();
                if ($(this).find('span[id^=name]').text().match(/热敏/)) {
                    var limit = <?=$thermal_limit;?>;
                    var credit = <?=$thermal_credit;?>;
                } else if ($(this).find('span[id^=name]').text().match(/缠绕膜/)) {
                    var limit = <?=$wrapper_limit;?>;
                    var credit = <?=$wrapper_credit;?>;
                } else if ($(this).find('span[id^=name]').text().match(/透明胶带/)) {
                    var limit = <?=$tape_limit;?>;
                    var credit = <?=$tape_credit;?>;
                }
                var free_qty = Math.floor((boxq + credit) / bqtf) - limit;

                qty2 = Math.max(Math.ceil(free_qty / moq2), qty2);
                $(this).find('input[id^=qty]').val(qty2);
                $(this).find('span[id^=tqty]').text(qty2 * moq2);
                $(this).find('span[id^=subt]').text((qty2 * moq2 * price2).toFixed(2));

                if (free_qty > 0) {
                    $(this).find('span[id^=tqty]').append('<span class="text-danger"> (其中免费配送' + free_qty + '个)</span');
                    $(this).find('span[id^=subt]').append('<span class="text-danger"> (-$ ' + (free_qty * price2).toFixed(2) + ')</span>');
                    prodt -= free_qty * price2;
                }
            }
        });

        $('#prodt').text(prodt.toFixed(2));

        var freight = 0;
        if (prodt < Number('<?=$freight_free_above?>') && prodt > 0) {
            freight = Number('<?=$freight_charge_fee?>');
            $('#freight').text(freight.toFixed(2));
        } else {
            $('#freight').text('0.00');
        }

        $('#total').text((prodt+freight).toFixed(2));
    }

    $('#items tbody').children().each(function() {
        qty_id = $(this).find('input[id^=qty]').attr('id');
        qty_value = $(this).find('input[id^=qty]').val();
        sessionStorage.setItem(qty_id, qty_value);

        tqty_id = $(this).find('span[id^=tqty]').attr('id');
        tqty_value = $(this).find('span[id^=tqty]').text();
        sessionStorage.setItem(tqty_id, tqty_value);

        subt_id = $(this).find('span[id^=subt]').attr('id');
        subt_value = $(this).find('span[id^=subt]').text();
        sessionStorage.setItem(subt_id, subt_value);
    });

    sessionStorage.setItem('prodt', $('#prodt').text());
    sessionStorage.setItem('freight', $('#freight').text());
    sessionStorage.setItem('total', $('#total').text());
}
</script>
<?php $this->registerJS(ob_get_clean()); ?>