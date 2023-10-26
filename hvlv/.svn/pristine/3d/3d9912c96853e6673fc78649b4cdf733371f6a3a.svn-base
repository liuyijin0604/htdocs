<div class="row" style="margin: 0; margin-bottom: 20px; padding: 0 15px">
	<div class="col-sm-2" style="padding: 0; padding-top: 20px; padding-bottom: 10px; font-size: 20px">
		<?php
		if(!empty($_GET['org_id'])){
			$org_id = $_GET['org_id'];
		}else{
			$org_id = Yii::app()->user->org;
		}
		$dpt="";
		if ($this->dpt_id == 106) {
			$dpt = "SYD";
		?>
			<a href="<?= $this->createUrl('site/index', ['dpt_id' => '218','org_id'=>$org_id]) ?>" style="text-decoration: none; float: left">MELBOURNE <div class="glyphicon glyphicon-hand-left"></div></a>
		<?php
		} else if ($this->dpt_id == 218) {
			$dpt = "MEL";
		?>
			<a href="<?= $this->createUrl('site/index', ['dpt_id' => '530','org_id'=>$org_id]) ?>" style="text-decoration: none; float: left">BRISBANE <div class="glyphicon glyphicon-hand-left"></div></a>
		<?php
		} else if($this->dpt_id == 530){
			$dpt = "BNE";
		?>
			<a href="<?= $this->createUrl('site/index', ['dpt_id' => '106','org_id'=>$org_id]) ?>" style="text-decoration: none; float: left">SYDNEY <div class="glyphicon glyphicon-hand-left"></div></a>
		<?php
		}
		?>
	</div>
	<div class="col-sm-8">
		<h1 style="text-align: center;">3PL-Welcome - <?= Yii::app()->user->name; ?> (<?= $dpt ?>)</h1>
	</div>
	 <?php //if (sizeof(User::getOrgIds()) == 1) { ?>
		<div class="col-sm-2" style="padding: 0; padding-top: 20px; padding-bottom: 10px; font-size: 20px">
		<?php
		$dpt="";
		if ($this->dpt_id == 106) {
			$dpt = "SYD";
		?>
			<a href="<?= $this->createUrl('site/index', ['dpt_id' => '530','org_id'=>$org_id]) ?>" style="text-decoration: none; float: right">BRISBANE <div class="glyphicon glyphicon-hand-right"></div></a>
		<?php
		} else if ($this->dpt_id == 218) {
			$dpt = "MEL";
		?>
			<a href="<?= $this->createUrl('site/index', ['dpt_id' => '106','org_id'=>$org_id]) ?>" style="text-decoration: none; float: right">SYDNEY <div class="glyphicon glyphicon-hand-right"></div></a>
		<?php
		} else if($this->dpt_id == 530){
			$dpt = "BNE";
		?>
			<a href="<?= $this->createUrl('site/index', ['dpt_id' => '218','org_id'=>$org_id]) ?>" style="text-decoration: none; float: right">MELBOURNE <div class="glyphicon glyphicon-hand-right"></div></a>
		<?php
		}
		?>
			<!-- <a href="<?= $this->createUrl('site/index', ['version' => 'new']) ?>" style="text-decoration: none; float: right">
				<div class="glyphicon glyphicon-hand-right"></div> New Version
			</a> -->
		</div>
	<?php //} ?> 
</div>

<?php if (sizeof(User::getOrgIds()) > 1) {
	if (Yii::app()->session['org_id'] == Yii::app()->user->org) {
		echo '<h1 style="text-align: center;">All</h1>';
	} else {
		echo '<h1 style="text-align: center;">' . Org::model()->findByPk(Yii::app()->session['org_id'])->name . '</h1>';
	}
} ?>

	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?= $this->createUrl('task/index', ['dpt_id' => $this->dpt_id,'org_id'=>$org_id]) ?>"><span class="glyphicon glyphicon-tasks"></span><br />Task List</a></div>
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?= $this->createUrl('return/list') ?>"><span class="glyphicon glyphicon-tasks"></span><br />Return List</a></div>
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?= $this->createUrl('product/goods') ?>"><span class="glyphicon glyphicon-th-list"></span><br />Product List</a></div>
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?= $this->createUrl('product/storage',['org_id'=>$org_id]) ?>"><span class="glyphicon glyphicon-th-list"></span><br />Stock List</a></div>
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?= $this->createUrl('accounts/invoice') ?>"><span class="glyphicon glyphicon-th-list"></span><br />Invoice List</a></div>
	<?php if (Yii::app()->session['org_id'] == Yii::app()->user->org) { ?>
		<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?= $this->createUrl('accounts/index') ?>"><span class="glyphicon glyphicon-cog"></span><br />Accounts</a></div>
	<?php } ?>
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item" href="<?= $this->createUrl('site/logout'); ?>"><span class="glyphicon glyphicon-log-out"></span><br />Log out</a></div>