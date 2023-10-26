<h1 style="text-align: center;">Welcome - <?= Yii::app()->user->name; ?></h1>
<?php if (!empty(Yii::app()->user->incomplete)) { ?>
    <h3 style="height:35px;"><a id="msg" class="text-danger" style="cursor:pointer; text-decoration:none;"><span class="glyphicon glyphicon-exclamation-sign"></span>请修改密码并补全基本信息</a></h3>
<?php } ?>
<div class="row dyo_list">

    <?php
        $isDriver =Yii::app()->user->getState('driver') ? true : false;
    ?>
    <?php if ( $isDriver ) : ?>
        <!-- <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link"
                                                             href="<?= $this->createUrl('order/make',['op' => '1']); ?>"><span
                    class="glyphicon glyphicon-wrench"></span><br/>Make Client Order</a></div> -->

        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link"
                                                             href="<?= $this->createUrl('order/viewcorders'); ?>"><span
                    class="glyphicon glyphicon-equalizer"></span><br/>View Orders<br/>查看订单</a></div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link"
                                                             href="<?= $this->createUrl('order/makedelivery'); ?>"><span
                    class="glyphicon glyphicon-equalizer"></span><br/>Confirm a Delivery<br/>确认收货</a></div>
    <?php else : ?>
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link"
                                                         href="<?= $this->createUrl('order/make'); ?>"><span
                class="glyphicon glyphicon-wrench"></span><br/>Make Order<br/>订购耗材</a></div>

    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link"
                                                         href="<?= $this->createUrl('order/history'); ?>"><span
                class="glyphicon glyphicon-equalizer"></span><br/>Order History<br/>订单历史</a></div>
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link"
                                                         href="<?=$this->createUrl('accounts/update')?>"><span
            class="glyphicon glyphicon-cog"></span><br/>Accounts<br/>我的账户</a></div>
    <?php endif; ?>

    <?php if (!empty(Yii::app()->user->redirect)) { ?>
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item"
                                                         href="<?= $this->createUrl('../'.Yii::app()->user->redirect.'/site/index'); ?>"><span
                class="glyphicon glyphicon-log-out"></span><br/>Back To <?=strtoupper(Yii::app()->user->redirect);?><br/>返回<?=strtoupper(Yii::app()->user->redirect);?></a></div>
    <?php } ?>

    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item"
                                                         href="<?= $this->createUrl('site/logout'); ?>"><span
                class="glyphicon glyphicon-log-out"></span><br/>Logout<br/>退出</a></div>
</div>
<?php ob_start(); ?>
<script type="text/javascript">
$(function() {
  $('#msg').effect('shake', 'slow');
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>
