<style type="text/css">
span[class*="sender"] {
	padding-top: 5px;
	padding-bottom: 5px;
	font-family: Georgia;
	font-weight: normal;
	color: grey;
	font-size: 15px;
	line-height: 20px;
}
.sender1 {
	margin-left: 5px;
}
.sender2 {
	margin-right: 5px;
}
span[class*="msg1"], span[class*="msg2"] {
	max-width: 40%;
	padding: 8px;
	font-size: 15px;
	font-family: Georgia;
	font-weight: normal;
	color: black;

	/*换行*/
	word-break: normal;
	display: block;
	white-space: pre-wrap;
	word-wrap: break-word;
	overflow: hidden;
	text-align: left;
}
.msg1 {
	background-color: #FFFFFF;
	margin-left: 5px;
}
.msg2 {
	background-color: #90EE90;
	margin-right: 5px;
}
.msg3 {
	text-align: center;
	padding-top: 15px;
	padding-bottom: 5px;
	font-family: Georgia;
	font-weight: normal;
	color: grey;
	font-size: 15px;
	line-height: 20px;
}
</style>
<form action="<?=$this->createUrl('job/note', ['id' => $model->id]);?>" method="post" id="note-form" data-bit="1">
	<textarea class="required" type="search" name="notes"></textarea>
	<button type="submit" class="btn btn-primary btn-block">Send</button>
</form>
<div id="note-content">
<?php
$h = '<div class="row msg3"><span>--- History ---</span></div>';
foreach ($logs as $log) {
	if (date('Ymd') - date('Ymd', strtotime($log->time)) > 1) {
		$date = date('m-d H:i', strtotime($log->time));
	} else if (date('Ymd') - date('Ymd', strtotime($log->time)) == 1) {
		$date = 'Yesterday ' . date('H:i', strtotime($log->time));
	} else if (date('Ymd') == date('Ymd', strtotime($log->time))) {
		$date = date('H:i', strtotime($log->time));
	}
	if ($log->user_id != Yii::app()->user->id) {
		$h .= '<div class="row"><span class="sender1 pull-left">' . $log->getUser() . '&nbsp;&nbsp;&nbsp;' . $date . '</span></div><div class="row"><span class="msg1 label pull-left">' . $log->getExtra() . '</span></div>';
	} else {
		$h .= '<div class="row"><span class="sender2 pull-right">' . $log->getUser() . '&nbsp;&nbsp;&nbsp;' . $date . '</span></div><div class="row"><span class="msg2 label pull-right">' . $log->getExtra() . '</span></div>';
	}
}
echo $h;
?>
</div>