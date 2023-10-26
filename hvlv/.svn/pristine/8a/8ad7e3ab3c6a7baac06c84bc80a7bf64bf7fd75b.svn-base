<h1 style="text-align: center;">3PL-Welcome - <?= Yii::app()->user->name; ?></h1>
<br>

<?php
foreach (User::getOrgIds() as $org) {
	if ($org == Yii::app()->user->org) {
		$org = Org::model()->findByPk($org);
		echo '<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12" style="height: 140px; display: table; margin-bottom: 25px"><a class="dash-item ajax-link" style="display: table-cell; vertical-align: middle" href="' . $this->createUrl('site/index', ['org_id' => $org->id]) . '">All</a></div>';
	}
}

foreach (User::getOrgIds() as $org) {
	if ($org != Yii::app()->user->org) {
		$org = Org::model()->findByPk($org);
		echo '<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12" style="height: 140px; display: table; margin-bottom: 25px"><a class="dash-item ajax-link" style="display: table-cell; vertical-align: middle" href="' . $this->createUrl('site/index', ['org_id' => $org->id]) . '">' . $org->name . '</a></div>';
	}
}
?>