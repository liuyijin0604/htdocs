<div class="row" style="margin: 0; margin-bottom: 20px; padding: 0 15px">
	<div class="col-sm-2" style="padding: 0; padding-top: 20px; padding-bottom: 10px; font-size: 20px">
		<a href="<?=$this->createUrl('site/index', ['version' => 'old'])?>" style="text-decoration: none; float: left">Old Version <div class="glyphicon glyphicon-hand-left"></div></a>
	</div>
	<div class="col-sm-8">
		<h1 style="text-align: center;">3PL-Welcome - <?= Yii::app()->user->name; ?></h1>
	</div>
	<div class="col-sm-2">
	</div>
</div>

<?php if (sizeof(User::getOrgIds()) > 1) {
	if (Yii::app()->session['org_id'] == Yii::app()->user->org) {
		echo '<h1 style="text-align: center;">All</h1>';
	} else {
		echo '<h1 style="text-align: center;">' . Org::model()->findByPk(Yii::app()->session['org_id'])->name . '</h1>';
	}
} ?>

<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('tasknv/bioc')?>"><span class="glyphicon glyphicon-tasks"></span><br/>B2C</a></div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('tasknv/bior')?>"><span class="glyphicon glyphicon-tasks"></span><br/>B2B</a></div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('tasknv/inbound')?>"><span class="glyphicon glyphicon-tasks"></span><br/>Inbound</a></div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('return/list')?>"><span class="glyphicon glyphicon-tasks"></span><br/>Return</a></div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('product/goods')?>"><span class="glyphicon glyphicon-th-list"></span><br/>Product</a></div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('stock/list')?>"><span class="glyphicon glyphicon-th-list"></span><br/>Inventory</a></div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('report/index')?>"><span class="glyphicon glyphicon-th-list"></span><br/>Reports</a></div>
<?php
$listConsolReportOrgId=[
	Org::ORGID_CLIENT_AUSTWAY,
];
if(in_array(Yii::app()->session['org_id'],$listConsolReportOrgId)){
	echo '<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="'.$this->createUrl('report/consol').'"><span class="glyphicon glyphicon-th-list"></span><br/>Consol Reports</a></div>';
}
?>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('report/setting')?>"><span class="glyphicon glyphicon-cog"></span><br/>Setting</a></div>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('accounts/invoice')?>"><span class="glyphicon glyphicon-th-list"></span><br/>Invoice</a></div>
<?php if (Yii::app()->session['org_id'] == Yii::app()->user->org) { ?>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('accounts/index')?>"><span class="glyphicon glyphicon-cog"></span><br/>Account</a></div>
<?php } ?>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item" href="<?=$this->createUrl('site/logout');?>"><span class="glyphicon glyphicon-log-out"></span><br/>Log out</a></div>