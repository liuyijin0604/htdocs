<h1 style="text-align: center;">Welcome - <?=Yii::app()->user->name;?> - <?=ucfirst(Yii::app()->session['scan_warehouse'])?>  Warehouse</h1>
<?php
if (empty(Yii::app()->session['scan_warehouse'])):
?>
<div class="row">
	<div class="col-md-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('site/chooseWarehouse', ['scan_warehouse' => 'szpOffice']);?>"><span class="glyphicon glyphicon-wrench"></span><br/>SZ Portal Office</a></div>

	<div class="col-md-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('site/chooseWarehouse', ['scan_warehouse' => 'szp']);?>"><span class="glyphicon glyphicon-wrench"></span><br/>SZ Portal Warehouse</a></div>
</div>
<?php elseif(Yii::app()->session['scan_warehouse']=="szpOffice"): ?>
<div class="row dyo_list">
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('szPortal/createplus');?>"><span class="glyphicon glyphicon-wrench"></span><br/>New Consol.+</a></div>

	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('szPortal/list');?>"><span class="glyphicon glyphicon-list"></span><br/>Manage Consol.</a></div>
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('szpChannel/list');?>"><span class="glyphicon glyphicon-list"></span><br/>Manage Channel</a></div>
		<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('site/logout');?>"><span class="glyphicon glyphicon-usd"></span><br/>Logout</a></div>
</div>

<?php elseif(Yii::app()->session['scan_warehouse']=="szp"): ?>
<div class="row dyo_list">
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('shipment/scan', ['op' => 'checkin']);?>"><span class="glyphicon glyphicon-wrench"></span><br/>Scan In Consol</a></div>

	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('shipment/scan', ['op' => 'tocheck']);?>"><span class="glyphicon glyphicon-list"></span><br/>Check In& Weighing& Sorting</a></div>

	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('shipment/bagging');?>"><span class="glyphicon glyphicon-list"></span><br/>Bagging</a></div>


</div>
<?php endif;?>