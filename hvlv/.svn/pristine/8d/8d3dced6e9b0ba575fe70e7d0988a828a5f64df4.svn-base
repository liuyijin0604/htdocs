<style type="text/css">
hr {
	margin: 0 !important;
}
.msg {
	padding: 15px 10px;
}
.msg.active {
	cursor: pointer;
}
<?php if (!empty($test)) echo ".msg.assigned { cursor: pointer; }";
else echo ".msg.assigned { cursor: wait; }";
?>
.msg.closed {
	cursor: pointer;
}
.listcontent {
	word-break: normal;
	display: block;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	color: #CCCCCC;
}
.badge {
	background-color: #D9534F;
}
</style>
<?php if (!empty($crm_msgs)) { ?>
	<?php foreach ($crm_msgs as $crm_msg) { ?>
	<div id="msg_<?=$crm_msg->id?>" class="msg <?php
		switch ($crm_msg->getStatus()) {
			case 'assigned':
				if ($crm_msg->operator_id != Yii::app()->user->id) {
					echo $crm_msg->getStatus();
				} else {
					echo 'active';
				}
				break;
			default:
				echo $crm_msg->getStatus();
				break;
		}
	?>">
		<?php
			if (!empty($crm_msg->lines)) {
				$last_line = $crm_msg->lines[count($crm_msg->lines)-1];
				echo '<span>' . $crm_msg->wechat_name . '</span>';
				if (((!empty($last_line->mdata['read']) && (array_search(Yii::app()->user->id, $last_line->mdata['read']) === false)) || empty($last_line->mdata['read'])) && ($crm_msg->status == 1 || (($crm_msg->status == 2) && ($crm_msg->operator_id == Yii::app()->user->id)))) {
					echo '&nbsp;<span class="badge">New</span>';
				}
				if (date('Ymd') - date('Ymd', strtotime($last_line->time)) > 1) {
					$date = date('m-d H:i', strtotime($last_line->time));
				} else if (date('Ymd') - date('Ymd', strtotime($last_line->time)) == 1) {
					$date = 'Yesterday ' . date('H:i', strtotime($last_line->time));
				} else if (date('Ymd') == date('Ymd', strtotime($last_line->time))) {
					$date = date('H:i', strtotime($last_line->time));
				}
				echo '<small><span class="text-muted pull-right">' . $date . '</span></small><br />';
				echo '<span class="listcontent">' . (empty($last_line->mdata['operator_id']) ? $crm_msg->wechat_name : User::model()->findByPk($last_line->mdata['operator_id'])->getName());
				if (!empty($last_line->mdata['text'])) {
					echo ': ' . $last_line->mdata['text'] . '</span>';
				} else if (!empty($last_line->mdata['pic'])) {
					echo ': [图片]</span>';
				} else if (!empty($last_line->mdata['voice'])) {
					echo ': [语音消息]</span>';
				} else if (!empty($last_line->mdata['ticket'])) {
					echo ': [ticket]</span>';
				} else if ($last_line->type == CrmMsgLine::TYPE_AUTO) {
					echo ': [已自动回复]</span>';
				}
			} else {
				echo '<span>' . $crm_msg->wechat_name . '</span>';
			}
		?>
		<?php
			switch ($crm_msg->getStatus()) {
				case 'active':
					echo '<span class="text-success">等待处理中</span>';
					break;
				case 'assigned':
					if ($crm_msg->operator_id != Yii::app()->user->id) {
						echo '<span class="text-danger">' . User::model()->findByPk($crm_msg->operator_id)->getName() . ' 正在处理中</span>';
					} else {
						echo '<span class="text-success">您正在处理中</span>';
					}
					break;
				case 'closed':
					echo '<span class="text-muted">已处理完</span>';
					break;
				default:
					echo '错误状态';
					break;
			}
		?>
	</div>
	<hr>
	<?php } ?>
<?php } else { ?>
<h2 class="msg">No msg now</h2>
<?php } ?>