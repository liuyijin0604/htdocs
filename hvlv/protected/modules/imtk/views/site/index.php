<h1 style="text-align: center;">Welcome - <?= Yii::app()->user->name; ?></h1>
<br>

<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    <a class="dash-item ajax-link"  href="<?=$this->createUrl('shipment/list')?>"><span class="glyphicon glyphicon-tasks"></span><br/>Tracking Shipment</a>
</div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    <a class="dash-item " href="<?=$this->createUrl('site/logout');?>"><span class="glyphicon glyphicon-log-out"></span><br/>Log out</a>
</div>
<script type="text/javascript">
	
	window.location.href="../ims";
</script>