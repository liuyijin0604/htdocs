<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<!--↑↑模板中请务必使用HTML5的标准DOCTYPE↑↑-->
<meta name="generator" content="ecstore.b2c" />

<script src="http://www.imopark.com/public/app/site/lang/zh_CN/js/lang.js"></script><link href="http://www.imopark.com/public/app/site/statics/css_mini/typical.css" rel="stylesheet" media="screen, projection" /><script src="http://www.imopark.com/public/app/site/statics/js_mini/moo.min.js"></script><script src="http://www.imopark.com/public/app/site/statics/js_mini/ui.min.js"></script>	


<title>购物车_摩登网—摩登百货旗下购物网 跨境购物,全球正品,海外直供 </title><meta name="keywords" content="购物车__摩登网—摩登百货旗下购物网 跨境购物,全球正品,海外直供 " /><meta name="description" content="购物车__摩登网—摩登百货旗下购物网 跨境购物,全球正品,海外直供 " /><link rel="icon" href="http://www.imopark.com/public/app/b2c/statics/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="http://www.imopark.com/public/app/b2c/statics/favicon.ico" type="image/x-icon" />
<link href="http://www.imopark.com/public/app/b2c/statics/css_mini/basic.min.css" rel="stylesheet" media="screen, projection" /><script src="http://www.imopark.com/public/app/b2c/lang/zh_CN/js/lang.js"></script>
<script>
var Shop = {"url":{"shipping":"\/index.php\/cart-shipping.html","total":"\/index.php\/cart-total.html","region":"\/index.php\/tools-selRegion.html","payment":"\/index.php\/cart-payment.html","purchase_shipping":"\/index.php\/cart-purchase_shipping.html","purchase_def_addr":"\/index.php\/cart-purchase_def_addr.html","purchase_payment":"\/index.php\/cart-purchase_payment.html","get_default_info":"\/index.php\/cart-get_default_info.html","diff":"\/index.php\/product-diff.html","fav_url":"\/index.php\/member-ajax_fav.html","datepicker":"http:\/\/www.imopark.com\/public\/app\/site\/statics\/js_mini","placeholder":"http:\/\/www.imopark.com\/public\/app\/b2c\/statics\/images\/imglazyload.gif"},"base_url":"\/"};
</script>

  <script src="http://www.imopark.com/public/app/b2c/statics/js_mini/shop.min.js"></script>
<link rel="stylesheet" href="http://www.imopark.com/index.php/widgetsproinstance-get_css-default-Y2FydC0oMSkuaHRtbA==.html" /><link type="text/css" rel="stylesheet" href="http://www.imopark.com/skin/style/style.css" />
<link type="text/css" rel="stylesheet" href="http://www.imopark.com/skin/style/main.css" />
</head>
<body>
<script type="text/javascript" src="http://www.imopark.com/skin/script/common.js"></script>
<script type="text/javascript" src="http://www.imopark.com/skin/script/scrollTop.js"></script>
<script src="http://www.imopark.com/skin/script/jquery-1.7.2.min.js"></script>
<script>
var jq=jQuery.noConflict();
jq(function(){
jq.getJSON("/index.php/tools-ajax_islogin.html", function(json){
	if(json.islogin=='ok'){
	   jq("#login_bar").html("您好 "+json.uname+" <a href='http://www.imopark.com/index.php/member.html'>我的摩登</a> <a href='/index.php/passport-logout.html'>退出</a>");
	}
});
});
</script>

<div class="mini-headers">
   <div class="wrap2">
	<h1><a href="/"><img src="http://www.imopark.com/skin/images/mlogo.jpg"></a></h1>
	<dl><img src="http://www.imopark.com/skin/images/bz_bg.gif"></dl>
    </div>
</div>
<div style="clear:both"></div><div class="ebg">
<div class="wrap2">

<div id="main">
<div class="checkout">
<div style="clear:both"></div>
  <!-- checkout主体 -->
  <div id="order_container" class="order-container"><br />
<b style="font-size: 28px;padding: 20px;">订单详情: <span><?=$d['odr_no'];?></span></b>
    <!-- checkout内容 -->
    <form action="/index.php/order-create.html" method="post">
            <input type="hidden" name="purchase[addr_id]" value="" />
            <input type="hidden" name="purchase[def_area]" value="" />
      <input type="hidden" name="md5_cart_info" value="2a8451f3fc8752a1cc967d4a9fea97a8" />
      <input type="hidden" name="extends_args" id="op_extends_args" value="{"get":[],"post":{"modify_quantity":null}}" />
      <!-- 订单信息填写 -->
      <div id="order_main" class="order-main">
        <!-- 收货信息 -->
<div id="order_shipping" class="order-section order-shipping" data-validatemsg="请完整填写收货信息并确认收货信息">
  <div class="order-section-title"><b>1收货信息</b></div>
  <div class="order-section-content">
  
 
  
    <!-- 完整收货信息 -->
    <table class="view-shipping fold">
      <input type="hidden" name="def_addr_id" value="">
<!--tbody>
    <tr>
      <th>收货地区：</th>
      <td><input type="hidden" name="def_addr_id" value=""></td>
    </tr>
    <tr>
      <th>收货地址：</th>
      <td></td>
    </tr>
        <tr>
      <th>收货人姓名：</th>
      <td></td>
    </tr>
    </tbody-->
    </table>
    
    
    
    
    <!-- 常用收货地址列表 -->
<div class="change-shipping select-addr" id="change_shipping">
   
    <ul>
<li id="addr_462" class="">
  <label for="for_shipping_462">



              <div class="addr-info"><br />
                <p class="name"><?=$r->cnee->name;?></p>
                <p class="addr"><?=$r->cnee->getCnFullAddress();?></p>
                <p class="phone"><?=$r->cnee->tel;?></p>
              </div>
                            
                            

</label>
  <div style="display:none"><input type="radio" checked="checked" class="action-change-shipping" value="{&quot;addr_id&quot;:462,&quot;area&quot;:&quot;1438&quot;}" id="for_shipping_462" name="address"></div>
</li>
</li><li style="display: none;" class="last">
          <input type="radio" data-validatemsg="请选择一个收货地址" vtype="requiredcustom" class="action-change-shipping" value="0" style="display:none" id="for_shipping_0" name="address">
          <label for="fro_shipping_0"></label>
      </li>
      
   </ul>
    </div>
   
    
    
  </div>
</div>
<div class="order-section" name="delivery">
  <div class="order-section-title"><b>2 商品清单</b></div>
<!-- 商品清单 -->
<table id="cart_main" class="cart-main">
  <colgroup>
    <col class="cart-1">
    <col class="cart-2">
    <col class="cart-3">
    <col class="cart-4">
    <col class="cart-5">
    <col class="cart-6">
  </colgroup>
  <thead>
    <tr>
              <th class="cart-1"><span>商品</span></th>
        <th class="cart-2"><span>销售价</span></th>
        <th class="cart-3"><span>数量</span></th>
        <th class="cart-4"><span>优惠金额</span></th>
        <th class="cart-5"><span>金币</span></th>
        <th class="cart-6"><span>小计</span></th>
             
       
     
    </tr>
  </thead>
  <!-- 购物车条目 -->
  <tbody class="cart-item">
  <!-- 主商品 -->
  <?php
  $sum = 0;
  $frt = 0;
  $weight = empty($p->mdata['dcwt'])? $p->weight : $p->mdata['dcwt'];
  $weight = ($weight < 1)? 1 : $weight;
  //$frt = 20 + ceil($weight - 1)* 8;
  $tq = 0;
  foreach($d['item'] as $i =>$p):
  $sum += $p['t'] * $p['q'];
  $tq += $p['q'];
  ?>
  <tr class="cart-product">
    <td width="500">
     <a target="_blank" href="/index.php/product-1533.html"><?=$p['g'];?></a> 

    </td>
    <td class="p-price">￥<?=($p['t']+$p['d']);?></td>
     <!--判断是不是预售商品-->
         <td><?=$p['q'];?></td>
    <!--判断是不是预售商品-->
          <td class="p-discount">￥<?=$p['d'];?></td>
      <td class="p-integral">
                -
              </td>
      <td class="p-subtotal">￥<?=($p['t'] * $p['q']);?></td>
       </tr>
    <?php endforeach; ?>
    <!-- 赠品 --><!--判断是不是预售商品-->
       <!-- 商品促销 -->
  <tr>
    <td class="p-promotion" colspan="6">
      <ul>
              </ul>
    </td>
  </tr>
  <!-- 配件 -->
  </tbody>
</table>


</div><!-- 支付方式模块 -->
<div id="order_payment" class="order-section order-payment" data-validatemsg="请选择一种支付方式并确认支付方式" data-linkagemsg="因为配送方式变更，请重新确认支付方式">
  <div class="order-section-title"><b>3 支付信息</b></div>
  <div class="order-section-content">
    <table class="view-payment">
          </table>
    <div class="change-payment  payment-list">
<p>支付交易号：<b><?=$d['trs_no'];?></b></p>
<p>支付状态： <b>已付清</b></p>
<p>支付方式：<b>支付宝</b><br /><img src="http://pic.shopex.cn/pictures/newsimg/1169028139.jpg" alt=""></p>
          </div>
     <div style="clear:both"></div>
  </div>
  <div style="clear:both"></div>
</div>
 <div style="clear:both"></div>
      </div>
      <!-- 结算信息 -->
      <table id="order_clearing" class="order-clearing">
        <caption>结算信息</caption>
        <tbody>
          <tr>
            <td class="order-infor">
                              <!-- 判断是不是预售商品-->

              <div id="order_remark" class="order-remark" style="width:300px"><label for="">订单备注：</label></div>
            </td>

            <!-- 订单价格 -->
            <td id="order_price" class="order-price">
             <div class="inner">
<ul>
  <li class="goods">
  <span class="label"><em>商品总金额：</em></span>
    <span class="price"><b>￥<?=$sum;?></b></span>
  </li>
  <li class="">
    <span class="label"><em>运费：</em></span>
    <span class="price">￥<?=$frt;?></span>
  </li>

      <li class="total">
    <span class="label">
                <em>总金额：</em></span>
      <span class="price"><b>￥<?=$sum+$frt;?></b></span>
    </li>
  </ul>
</div>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
  </div>
</div>
<div style="clear:both; height:30px;"></div>
</div>
<script type="text/javascript">
var Order = function() {
    var self = this;
    // this.member = !!Memory.get('member');
        this.shipping = {
        module: $('order_shipping'),
        load: 'cart-shipping_edit.html',
        save: 'cart-shipping_save.html',
        remove: 'cart-shipping_delete.html',
        confirm: 'cart-shipping_confirm.html'
    };
    Object.append(this.shipping, {
        edit: this.shipping.module.getElement('.action-edit-shipping'),
        view: this.shipping.module.getElement('.view-shipping'),
        fill: this.shipping.module.getElement('.fill-shipping'),
        change: this.shipping.module.getElement('.change-shipping')
    });
    this.delivery = {
        module: $('order_delivery'),
        list: 'cart-delivery_change.html',
        confirm: 'cart-delivery_confirm.html'
    };
    Object.append(this.delivery, {
        edit: this.delivery.module.getElement('.action-edit-delivery'),
        view: this.delivery.module.getElement('.view-delivery'),
        change: this.delivery.module.getElement('.change-delivery')
    });
    this.payment = {
        module: $('order_payment'),
        list: 'cart-payment_change.html',
        confirm: 'cart-payment_confirm.html'
    };
    Object.append(this.payment, {
        edit: this.payment.module.getElement('.action-edit-payment'),
        view: this.payment.module.getElement('.view-payment'),
        change: this.payment.module.getElement('.change-payment')
    })
        this.coupon = {
        module: $('order_coupon'),
        add: '/',
        remove: 'cart-removeCartCoupon-coupon.html'
    };
        this.isFastbuy = '';
    this.total = {
        module: $('order_price'),
        url: 'cart-total.html',
        update: function(callback) {
            var data = self.shipping.change.toQueryString() + '&' + self.delivery.change.toQueryString() + '&' + self.payment.change.toQueryString() + (self.invoice ? '&' + self.invoice.module.toQueryString() : '') + (self.deduction ? '&' + self.deduction.module.toQueryString() : '') + self.isFastbuy + '&extends_args=' + encodeURIComponent($('op_extends_args').value);
            self.update(self.total.url, data, self.total.module, callback);
        }
    };
    this.update = function(url, data, update, callback) {
        var Update = new Class({
            Extends: Request,
            options: {
                url: url,
                update: update,
                method: 'post',
                evalScripts: true,
                link: 'cancel',
                headers: {
                    Accept: 'text/html, application/xml, text/xml, */*'
                }
            },
            success: function(text){
                var options = this.options;
                var response = this.response;
                var json;
                try{
                    json = JSON.validate(text) ? JSON.decode(text, this.options.secure) : null;
                    if(json && json.error) return Message.error(json.error);
                }catch(e){}
                response.html = text.stripScripts(function(script){
                    response.javascript = script;
                });
                if (options.update){
                    options.update.set('html', response.html);
                    Browser.exec(response.javascript);
                }
                callback && callback(response.html, response.javascript);
            }
        });
        new Update(url, update, callback).send(data);
    };
    this.send = function(url, data, callback, update) {
        url = url || self[url];
        new Request({
            url: url,
            link: 'cancel',
            onComplete: function(rs){
                var json;
                try{
                    json = JSON.validate(rs) ? JSON.decode(rs) : null;
                    if(json && json.error) return Message.error(json.error);
                }catch(e){}
                callback && callback.call(this, rs, json);
            }
        }).send(data);
    };
}
var order = new Order();

Object.merge(validatorMap, {
    onesecond: function(element, v, type, parent){
        return parent.getElements('input[type=' + type + '][vtype='+ element.get('vtype') +']').some(function(el){
            el.onblur = function(){validate(this)};
            return el.value.trim() != '';
        });
    },
    requiredcustom: function(element, v, type, parent){
        var name = element.name;
        if(!parent.getElements('input[type=' + type + ']' + name ? '[name="' + name + '"]' : '').some(function(el) {
            return el.checked == true && el.value != '0';
        })) {
            showWarn(element, element.get('data-validatemsg'));
            return false;
        }
        return true;
    }
});

function selectArea(sels) {
    var selected = '';
    sels.each(function(s){
        if(s.isDisplayed()) {
            var text = s[s.selectedIndex].text.trim().clean();
            if(['北京','天津','上海','重庆'].indexOf(text)>-1) return;
            selected += text;
        }
    });
    // var arr = $('change_shipping').getElement('.action-change-shipping:checked');
    // if(arr) {
    //     var val = JSON.decode(arr.value);
    //     val.area = sels[0].getParent().getElement('input[name=area]').value;
    //     arr.value = JSON.encode(val);
    // }

    //$('op_splice_area').innerHTML = selected;
    //$('addr_area').value = selected;
    //$('address').value = $('address').value.replace(selected, '');
};

function hideWarn(el, self) {
    el = self ? el : el.getParent('.order-section-content');
    el.retrieve('tips_instance', {hide: function(){}}).hide();
}
function showWarn(el, msg) {
    formTips.warn(msg, el.getParent('.order-section-content')).toElement().setStyle('margin-left', '4%');
    return false;
}
//==选中地址
/*
jq(".action-confirm-s").click(function(){
	var id = jq(this).attr('id');
	jq('#for_shipping_'+id).attr("checked",true);
	loadShipping(this, 'confirm');
	jq(".change-shipping li").removeClass("selected");
	jq('#addr_'+id).addClass('selected');	
});
*/
//==添加新地址[弹出]
jq(".JS-add-addr").click(function(){
	var id = jq(this).attr('id');
	jq('#for_shipping_'+id).attr("checked",false);
	jq("#for_shipping_"+id).trigger("click");
	jq(".last").show();
});



order.total.update();

$('main').addEvents({
    'click': function(e){
        var el = $(e.target);

        //= 点击document隐藏删除确认框
        var dtc = $$('.dialog-tips-container')[0];
        var target = $(document.body).retrieve('dialog-tip:show');
        var element;
        if(dtc && !dtc.contains(el) && target && !target.contains(el)) dtc.retrieve('instance').hide();

        if(el.getParent('.order-section')) {
            el.getParent('.order-section').removeClass('highlight');
            el.getParent('.order-section-content') && hideWarn(el);
        }
        //修复FF点击select闪烁的问题
        if(el.getParent('.fill-shipping') && el.getParent('li') && (el.match('option') || el.match('select') || el.match('input[type=text]'))) {
            e.preventDefault();
        }
    },
	 //= 选择送货地址 1342991281
    'click:relay(.action-confirm-s)': function(e) {
		var id = jq(this).attr('id');
	    jq('#for_shipping_'+id).attr("checked",true);
	    loadShipping(this, 'confirm');
	    jq(".change-shipping li").removeClass("selected");
	    jq('#addr_'+id).addClass('selected');	
		fold('payment', 'notice', true);
    },
	 //= 选择配送方式 1342991281
    'click:relay(.action-change-delivery)': function(e) {
		//var id = jq(this).attr('id');
	    //jq('#for_delivery_'+id).attr("checked",true);
            hideWarn(this);
            loadDelivery(this);
		
		//window.location.href="/index.php/cart-checkout.html"; 
		
		jq(".delivery_list li").removeClass("selected")

		jq(this).parent().addClass('selected');
			
    },
	 //= 选择支付方式 1342991281
    'click:relay(.action-payment)': function(e) {
		var id = jq(this).attr('id');
	    jq('#pay_app_id_'+id).attr("checked",true);
	     
		 
		 hideWarn(this);
         loadPayment(this);
	    jq(".payment-list li").removeClass("selected");
	    jq(this).addClass('selected');	
    },
	
	
    //= 商品必填
    'click:relay(.action-confirm-goods)': function(e) {
        if(validate(this.getParent('.fill-goods'), 'all')) {
            loadGoods(this);
        }
    },
    //= 修改商品必填信息
    'click:relay(.action-edit-goods)': function(e) {
        e.stop();
        fold('goods', this);
    },
    //= 修改/取消修改
    'click:relay(.action-edit-shipping)': function(e) {
        e.stop();
        if(this.hasClass('action-cancel')) {
            var id = order.shipping.view.getElement('input[name=def_addr_id]').value;
            var radio = $('for_shipping_'+id);
            if(radio){
                switchSelected(radio.set('checked', true));
                radio.getParent('li').getElement('label').set('html', radio.getParent('li').retrieve('html:source'));
            }
        }
        fold('shipping', this);
        fold('delivery', 'notice', this.hasClass('action-cancel'));
        fold('payment', 'notice', this.hasClass('action-cancel'));
    },
    'click:relay(.action-edit-delivery)': function(e) {
        e.stop();
        fold('shipping', false);
        order.shipping.edit[!this.hasClass('action-cancel') ? 'addClass' : 'removeClass']('fold');
        fold('delivery', !this.hasClass('action-cancel'));
        fold('payment', 'notice', this.hasClass('action-cancel'));
    },
    'click:relay(.action-edit-payment)': function(e) {
        e.stop();
        fold('shipping', false);
        fold('delivery', false);
        fold('payment', !this.hasClass('action-cancel'));
    },
    //= 添加到常用地址
    'click:relay(.action-add-address)': function(e) {
        e.stop();
        var parent = this.getParent('.fill-shipping');
        if(validate(parent, 'all')) {
            addShipping(this);
        }
    },
    //= 编辑地址
    'click:relay(.action-edit-address)': function(e) {
        switchSelected(this, true);

    },
	 //=直接关闭
    'click:relay(.action-fclose-address)': function(e) {
		 jq('.fill-shipping').css('display','none');
		 jq(".last").css('display','none');
    },
    //= 保存修改
    'click:relay(.action-save-address)': function(e) {
        e.stop();
        var parent = this.getParent('li');
        if(validate(parent, 'all')) {
            var checked = parent.getElement('.action-change-shipping');
            var area = parent.getElement('input[name=area]').value;
            var val = JSON.decode(checked.value);
            val.area = area.substr(area.lastIndexOf(':')+1);
            checked.value = JSON.encode(val);
            saveShipping(this);
        }
    },
    //= 删除地址
    'click:relay(.action-delete-address)': function(e) {
        e.stop();
        var msg = '删除后不可恢复，确认删除此收货地址吗？';
        if(this.getParent('li').get('rel') == 'inuse') msg = '此地址为修改前正在使用的收货地址，' + msg;
        Dialog.confirm(msg, function(e){
            e && deleteAddress(this);
        }.bind(this));
    },
    //= 确认收货地址
    'click:relay(.action-confirm-shipping)': function(e) {
        var parent = this.getParent('.change-shipping') || this.getParent('.fill-shipping');
        var li = parent.getElement('li.selected');
        if (li) {
            var checked = li.getElement('.action-change-shipping:checked');
            var fill = li.getElement('.fill-shipping');

            if(fill){
                if(validate(fill, 'all')) {
                    var val = JSON.decode(checked.value);
                    var area = fill.getElement('input[name=area]').value;
                    val.area = area.substr(area.lastIndexOf(':')+1);
                    checked.value = JSON.encode(val);
                    addShipping(checked, 'confirm');
                }
            }
            else {
                loadShipping(checked, 'confirm');
            }
            hideWarn(parent, true);
        }
        else if(validate(parent, 'all')) {
            addShipping(this, 'confirm');
        }
    },
    //= 确认配送方式
	/*
    'click:relay(.action-confirm-delivery)': function(e) {
        if(validate(this.getParent('.change-delivery'), 'all')) {
            //order.shipping.edit.removeClass('fold');
            hideWarn(this);
            loadDelivery(this);
        }
    },
	*/
    //= 确认支付方式
    'click:relay(.action-confirm-payment)': function(e) {
        if(validate(this.getParent('.change-payment'), 'all')) {
            hideWarn(this);
            loadPayment(this);
        }
    },
    //= 订单优惠收起/展开
    'click:relay(.action-toggle)': function(e) {
        e.stop();
        this.set('text', this.hasClass('btn-collapse') ? '+' : '-').toggleClass('btn-collapse').toggleClass('btn-expand').getParent('h3').getNext('.content').toggle();
    },
    //= 使用订单优惠券
    'click:relay(.action-confirm-coupon)': function(e) {
        var parent = this.getParent('.item');
        var p = order.coupon.module;
        var ul = p.getElement('.usedlist');
        var cpn = ul.getElements('.action-cancel-coupon');
        var coupon = parent.getElement('select') || parent.getElement('input[type=text]');
        var value = coupon.value;
        if(coupon.match('select')) {
            var cpn_id = coupon.getSelected().get('data-coupon');
        }
        if(!value) return;
        if(cpn.length) {
          if(cpn.get('rel').indexOf('coupon_'+value) > -1 || cpn.get('data-code').indexOf(value) > -1 || cpn.get('data-coupon').indexOf(cpn_id) > -1) {
            coupon.value = '';
            return Dialog.alert('此优惠券使用中！');
          }
        }
        if(cpn.get('data-coupon').indexOf(String.from(cpn_id)) > -1){
            coupon.value = '';
            return Dialog.alert('同一类优惠券一次只能用一张！');
        }
        new Request({
            url: '/index.php/cart-add-coupon.html',
            link: 'cancel',
            onRequest: function() {
                p.getElements('input, select, button').set('disabled', true);
            },
            onComplete: function(rs) {
                rs = JSON.decode(rs);
                p.getElements('input, button').set('disabled', false);
                if(p.getElement('select').options.length > 1) p.getElement('select').disabled = false;
                if(rs.error){
                    return Dialog.alert('优惠券添加失败，' + rs.error);
                }
                var data = rs.data;
                if(data) {
                    if(data.length) {
                        p.getElement('.used').removeClass('fold');

                        updateCoupon(ul, data);

                        coupon.value = '';
                        order.total.update();
                    } else {
                        Dialog.alert('优惠券添加失败，请明确优惠券的适用范围。');
                    }
                    p.getParent('form').getElement('input[name=md5_cart_info]').value = rs.md5_cart_info;
                }
            }
        }).post(parent.toQueryString() + '&is_fastbuy=&response_json=true');
    },
    //= 取消优惠券
    'click:relay(.action-cancel-coupon)': function(e) {
        e.stop();
        var p = order.coupon.module;
        var select = p.getElement('select');
        var data = 'cpn_ident=' + this.get('rel') + '&is_fastbuy=&response_json=true';
        order.send(order.coupon.remove, data, function(rs, json){
            var ul = p.getElement('.usedlist');
            var data = json.data;
            updateCoupon(ul, data);
            if(!data) {
                p.getElement('.used').addClass('fold');
            }
            p.getParent('form').getElement('input[name=md5_cart_info]').value = json.md5_cart_info;
            order.total.update();
        });
    },
    //= 使用金币
    'click:relay(.action-confirm-score)': function(e) {
        var parent = this.getParent('.scoreinput');
        var value = parent.getElement('.action-input-score').value;
        if(!value) return;
        order.send(order.deduction.use, parent, function(rs){
            rs = {
                price: priceControl.format(rs),
                score: value
            };
            var tpl = '抵扣金币：<strong>{score}</strong> 抵扣金额：<b>{price}</b> <a href="javascript:void(0);" class="lnklike action-cancel-score">[取消使用]</a>';
            parent.addClass('fold');
            var xtip = $('xtips_container');
            if(xtip && xtip.isVisible()) xtip.retrieve('tips').hide();
            parent.getNext('.usedscore').set('html',tpl.substitute(rs)).removeClass('fold');
            order.total.update();
        });
    },
    //= 取消金币
    'click:relay(.action-cancel-score)': function(e) {
        e.stop();
        var p = order.deduction.module;
        p.getElement('.usedscore').addClass('fold');
        p.getElement('.scoreinput').removeClass('fold');
        p.getElement('.action-input-score').value = '';
        setPrice(0);
        order.total.update();
    },
    //= 提交订单
    'click:relay(.action-submit-order)': function(e) {
        if(!validateOrder()) {
            e.stop();
        }
    },
    //= 配送时间
    'change:relay(.action-assign-times)': function(e){
        var p = this.getParent();
        p.getElement('.assign-times')[this.checked ? 'show' : 'hide']();
        if(!this.checked) {
            p.getElement('.action-select-special').value = '任意日期';
            p.getElement('.action-select-times').value = '任意时间段';
        }
    },
    //= 指定配送时间
    'change:relay(.action-select-special)': function(e){
        this.getParent().getElement('.special-delivery-day')[this[this.selectedIndex].value == 'special' ? 'show' : 'hide']();
    },
    //= 选择/添加新收货地址
    'change:relay(.action-change-shipping)': function(e){
        var cond = this.value == 0;
        var li = this.getParent('li');
        var update = li.getElement('address');
        if(cond && li.getAllPrevious().length >= 10) {
            e.stop();
            li.getParent().getElement('li.selected .action-change-shipping').checked = true;
            return formTips.warn('最多添加10条，想要使用新收货地址，请先删除一条收货地址', update, {where: 'after', store: this.getParent('.change-shipping')});
        }
        switchSelected(this, cond);
    },
    //= 选择配送方式
	/*
    'change:relay(.action-change-delivery)': function(e){
        var parent = this.getParent('tr');
        if(parent&&parent.getSiblings('.sub').length) {
            parent.getSiblings('.sub').getElement('input[name=is_protect]').set('disabled', true);
            $(parent.id + '_sub') && $(parent.id + '_sub').getElement('input[name=is_protect]').set('disabled', false);
        }
    },
	*/
    //= 选择支付方式
	/*
    'change:relay(.action-change-payment)': function(e){
        var selected = this.getParent('tr').addClass('selected').getSiblings('tr.selected')[0];
        selected && selected.removeClass('selected');
    },
	*/
    //= 选择发票
    'change:relay(.action-select-invoice)': function(e){
        var cont = this.getParent('tr').getAllNext('tr');
        var need = this.getParent('.order-section-content').getElement('input[name="payment[is_tax]"]');
        cont[this.value == 'false' ? 'addClass' : 'removeClass']('fold');
        need.value = this.value == 'false' ? 'false' : 'true';
        order.total.update();
    }
});

//= 折叠/展开指定项
function fold(type, isfold, mod) {
    if(!type) return;
    type = type + '';
    var parent = order[type].module;
    var fill = parent.getElements('[class^=fill-]')
    var change = parent.getElement('[class^=change-]');
    var view = parent.getElement('[class^=view-]');
    var edit = parent.getElement('[class^=action-edit-]');
    var notice = parent.getElement('.notice');
    if(typeOf(isfold)=='element') {
        if(type == 'goods') {
            mod = 'fill';
        }
        fold(type, !isfold.hasClass('action-cancel'), mod);
    }
    else if(isfold === false) {
        view.removeClass('fold');
        fill && fill.addClass('fold');
        //change && change.addClass('fold');
        notice && notice.addClass('fold');
        edit&&(edit.removeClass('fold').removeClass('action-cancel').innerHTML = '[修改]');
    }
    else if(isfold == 'notice' && notice) {
        if(mod) {
            Memory.set(type + '.stat', !edit || edit.hasClass('fold') ? notice && !notice.hasClass('fold') ? 'none' : 'noselect' : edit && edit.hasClass('action-cancel') ? 'editing' : 'view');
        }
        var stat = Memory.get(type + '.stat');
        if(!stat || stat === 'none') return;
        if(notice.hasClass('fold')) {
            notice.removeClass('fold');
            hideWarn(notice);
            parent.removeClass('highlight');
            view.addClass('fold');
            change&&change.addClass('fold');
            edit&&edit.addClass('fold');
        }
        else {
            notice.addClass('fold');
            if(stat == 'view') {
                view.removeClass('fold');
            }
            else {
                change&&change.removeClass('fold');
            }
            if(stat != 'noselect') {
                edit&&edit.removeClass('fold');
            }
        }
    }
    else {
        var who = mod !== 'fill' ? change : fill;
        who && who.removeClass('fold');
        view.addClass('fold');
        notice && notice.addClass('fold');
        edit&&(edit.addClass('action-cancel').innerHTML = '[取消修改]');
    }
}

//= 载入商品必填
function  loadGoods(el) {
    var parent = order.goods.module;
    var data = parent.getElement('.fill-goods');
    var view = parent.getElement('.view-goods');
    var goods_info = data.toJSON(true);

    fold('goods', false);
    Cookie.write('checkout_b2c_goods_buy_info', goods_info, {path:Shop.base_url});
    order.goods.filled(goods_info);
}
//设当前选中状态
function switchSelected(el, load) {
	//关闭选择 1342991281
   //var parent = (el.match('li') ? el : el.getParent('li')).addClass('selected');
  
    var parent = el.getParent('li');
    //var label = parent.getElement('label');
    //parent.retrieve('html:source') || label && parent.store('html:source', label.innerHTML);
	/*
    if(el.match('li') || el.hasClass('action-edit-address')) {
        if(Browser.ie) order.shipping.change.getElement('.action-change-shipping:checked').checked = false;
        parent.getElement('.action-change-shipping').checked = true;
    }
	*/
    load && loadShipping(el);
   // var selected = parent.getSiblings('li.selected')[0];
   // if(selected) {
        //selected.removeClass('selected').retrieve('html:source') && selected.getElement('label').set('html', selected.retrieve('html:source'));
    //}
}
//= 保存收货地址
function saveShipping(el) {
    var parent = el.getParent('li');
    var update = parent.getElement('label');
    order.update(order.shipping.save, parent, update, function(rs){
        switchSelected(parent);
        if(parent.hasClass('selected')) {
			/*
            order.shipping.edit&&order.shipping.edit.addClass('fold');
            order.delivery.edit&&order.delivery.edit.addClass('fold');
            order.payment.edit&&order.payment.edit.addClass('fold');
			*/
			var el=parent.getElement('.action-confirm-s');
			loadShipping(el, 'confirm');
			fold('payment', 'notice', true);
        }
    });
}
//= 添加收货地址
function addShipping(el, method) {
    var method = method || 'save';
    var parent = el.getParent('li') || el.getParent('.fill-shipping');
    var update = parent.getElement('label') || el.getParent('.order-section-content').getElement('.view-shipping');
    var li;
    order.send(order.shipping.save, parent, function(rs){
		jq(".last").hide(); //关闭占位
        if(el.match('input')) {
            if(el.value == 0) {
                addAddress(update,parent,rs,true);
            }
            else {
                update.set('html', rs);
            }
        }
        else {
            addAddress(update,parent,rs);
        }
        if(method == 'confirm') {
            loadShipping(el.getParent('.order-section-content').getElement('.change-shipping .action-change-shipping:checked'), 'confirm');
            fold('shipping');
        }
    });
}
function addAddress(update, parent, rs, unfold) {
    var li;
    if(update.tagName == 'LABEL') {
        li = new Element('li', {html: rs}).inject(parent, 'before');
        update.set('html', parent.retrieve('html:source'));

    }
    else if(update.tagName == 'TABLE') {
        var inject = parent.addClass('fold').getParent().getElement('.change-shipping');
        li = new Element('li', {html: rs}).inject(inject, 'top');
        unfold&&inject.removeClass('fold');
    }
    switchSelected(li);
    order.shipping.edit&&order.shipping.edit.addClass('fold');
}
//= 删除收货地址
function deleteAddress(el) {
    var parent = el.getParent('li');
    var change = order.shipping.change;
    var data = parent.getElement('.action-change-shipping');
    order.send(order.shipping.remove, data.name+'='+data.value, function(rs){
        hideWarn(change, true);
        if(parent.hasClass('selected')) {
			fold('delivery', 'notice', true);
			fold('payment', 'notice', true);
			/*
            order.shipping.edit&&order.shipping.edit.addClass('fold');
            order.delivery.edit&&order.delivery.edit.addClass('fold');
            order.payment.edit&&order.payment.edit.addClass('fold');
			*/
        }
        parent.destroy();
        var cases = change.getElement('.action-change-shipping');
        if(cases.value == 0) {
            order.shipping.edit&&order.shipping.edit.addClass('fold');
            cases.checked = true;
            switchSelected(cases, true);
        }
    });
}
//= 载入填写收货信息区域
function loadShipping(el, method) {
    method = method || 'load';
    var parent;
    var update;
    if(method == 'load') {
        parent = el.getParent('li');
        update = parent.getElement('label');
    }
    else {
        parent = el.getParent('.change-shipping') || el.getParent('.fill-shipping');
        update = parent.getParent().getElement('.view-shipping');
    }
    order.update(order.shipping[method], parent, update, function(rs){
        bindDatepicker();
        if(method == 'confirm') {
            fold('shipping', false);

            var region = 'area=' + (parent.getElement('li.selected') ? JSON.decode(parent.getElement('li.selected').getElement('.action-change-shipping').value).area : parent.getElement('input[name=area]').value);
            linkage('delivery', region);
        }
    });
}
function loadDelivery(el) {
    var mod = order.delivery;
    var data = mod.change;
    var update = mod.view;
    order.update(mod.confirm, data.toQueryString() + order.isFastbuy, update, function(rs){
        fold('delivery', false);
        linkage('payment', data);
    });
}
function loadPayment(el) {
    var mod = order.payment;
    var data = mod.change;
    var update = mod.view;
    order.update(mod.confirm, data, update, function(rs){
        fold('payment', false);
        order.total.update();
    });
}
function linkage(next, data){
    var mod = order[next];
    var notice = mod.module.getElement('.notice');
    var change = mod.change;
    var view = mod.view;
    var edit = mod.edit;
    order.update(mod.list, data, change, function(rs){
        showWarn(change, mod.module.get('data-linkagemsg'));
        fold(next);
        edit&&edit.addClass('fold');
    });
}
function setPrice(el, rate) {
    var area = order.deduction.module;
    var value = typeOf(el) == 'element' ? priceControl.format(el.value * rate) : el;
    area.getElement('.action-deduct-price').set('text', value);
}

function updateCoupon(el, data){
    var tpl = '<li><i title="{name}">{coupon}-{name}</i><a href="javascript:void(0);" class="lnklike action-cancel-coupon" rel="{obj_ident}" data-coupon="{cpns_id}">[取消使用]</a></li>';
    var html = '';
    if(data && data.length) {
        data.each(function(d){
            html += tpl.substitute(d);
        });
    }
    el.innerHTML = html;
}

function validateOrder(){
    var error = ['goods', 'shipping', 'delivery', 'payment'].some(function(view, i){
        var mod = order[view];
        if(!mod) return;
        if(mod.module && mod.view.hasClass('fold')) {
            mod.module.addClass('highlight');
            Dialog.alert(mod.module.get('data-validatemsg'), function(){
                new Fx.Scroll(window,{duration:250, link:'ignore'}).toElementEdge(mod.module);
            });
            return true;
        }
        mod.module.removeClass('highlight');
    });
    if(error) return false;
    return true;
}

if(order.deduction) {
    var inputscore = order.deduction.module.getElement('.action-input-score');
    var inputscorevalue = '';
    //= 金币输入
    inputscore.addEvents({
        'inputchange': function(e){
            var parent = this.getParent('.content');
            var max = Math.min(parent.getElement('.action-max-score').get('text'), parent.getElement('.action-user-score').get('text'));
            var rate = parent.getElement('input[name="point[rate]"]').value;
            var price = parent.getElement('.action-deduct-price');
            if(this.value == 0) {
                this.value == '';
                return;
            }
            if(isNaN(this.value)) {
                this.value = inputscorevalue;
                //setPrice(0);
                return;
            }
            if(!this.value.test(/^(0|[1-9][0-9]*)?$/)) {
                this.value = this.value.substr(0, this.value.length - 1);
                return;
            }
            if(Number(this.value) > max) {
                inputscorevalue = this.value = max;
                setPrice(this, rate);
                return this.tips('本次能使用的最大金币为' + max);
            }
            inputscorevalue = this.value;
            setPrice(this, rate);
        },
        'enter': function(e){
            e.stop();
        }
    });
}
//= 最大字符300
var textarea = $('order_clearing').getElement('.action-remark-textarea').addEvent('inputchange', function(e){
    if(this.value.length > 300) {
        this.tips('订单备注最多输入300字').value = this.value.substr(0, 300);
    }
});
</script>
<div style="clear:both"></div>

<div id="footer" class="footer">
	<div class="wrap copyright">
		<p>CopyRight 2013 imopark.com.All rights reserved  <a href="#">粤ICP备09124602号-2</a>  使用本网站即表示接受摩登网用户协议,</p>
		<p>版权所有 广州摩登百货电子商务有限公司</p>
		<div class="f-icon">
			<a href="#"><img src="http://www.imopark.com/themes/default/images/f_i_1.jpg"></a>
			<a href="#"><img src="http://www.imopark.com/themes/default/images/f_i_5.jpg"></a>
		</div>
	</div>
</div>

<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1255286969'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "w.cnzz.com/q_stat.php%3Fid%3D1255286969' type='text/javascript'%3E%3C/script%3E"));</script>    </body>
</html>
