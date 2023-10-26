<h1>Welcome - <?=Yii::app()->user->name;?></h1>
<div class="row dyo_list">
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('shipment/tracking');?>"><span class="glyphicon glyphicon-search"></span><br />Tracking</a></div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('shipment/create');?>"><span class="glyphicon glyphicon-barcode"></span><br />New Shipment</a></div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('shipment/manage');?>"><span class="glyphicon glyphicon-list"></span><br />Manage Shipments</a></div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('shipment/weigh');?>"><span class="glyphicon glyphicon-scale"></span><br />Weigh Shipments</a></div>
<?php if(!Yii::app()->user->isGuest && Yii::app()->user->grp == 80): ?>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('accounts/index');?>"><span class="glyphicon glyphicon-usd"></span><br />Accounts</a></div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('reports/index');?>"><span class="glyphicon glyphicon-stats"></span><br />Reports</a></div>
<?php endif; ?>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('tools/index');?>"><span class="glyphicon glyphicon-wrench"></span><br />Tools</a></div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('app/settings');?>"><span class="glyphicon glyphicon-cog"></span><br />Settings</a></div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link"
                                                         href="<?= $this->createUrl('hc/make'); ?>"><span
                class="glyphicon glyphicon-gift"></span><br/>Make HC Order</a></div>

    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link"
                                                         href="<?= $this->createUrl('hc/history'); ?>"><span
                class="glyphicon glyphicon-equalizer"></span><br/>HC Order History</a></div>
</div>