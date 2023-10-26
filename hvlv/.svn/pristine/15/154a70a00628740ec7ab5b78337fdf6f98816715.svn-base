<?php if (!empty($crm_msg->lines)) {
	$html = '';
	foreach ($crm_msg->lines as $line) {
		if (date('Ymd') - date('Ymd', strtotime($line->time)) > 1) {
			$date = date('m-d H:i', strtotime($line->time));
		} else if (date('Ymd') - date('Ymd', strtotime($line->time)) == 1) {
			$date = 'Yesterday ' . date('H:i', strtotime($line->time));
		} else if (date('Ymd') == date('Ymd', strtotime($line->time))) {
			$date = date('H:i', strtotime($line->time));
		}
		if (empty($line->mdata['operator_id'])) {
			$html .= '<div class="row"><span class="sender1 pull-left">' . $crm_msg->wechat_name . '&nbsp;&nbsp;&nbsp;' . $date . '</span></div><div class="row">';
			if (!empty($line->mdata['text'])) {
				$html .= '<span class="msg1 label pull-left">' . $line->mdata['text'] . '</span></div>';
			} else if (!empty($line->mdata['pic'])) {
				$html .= '<span class="msg1 label pull-left" style="width:20%">' . '<span src="' . $line->mdata['pic'] . '" class="img" style="cursor:pointer; font-size:15px; font-style:italic;">[点击查看图片]</span></span></div>';
			} else if (!empty($line->mdata['voice'])) {
				$html .= '<span class="msg1 label pull-left">' . '<audio src="' . $line->mdata['voice'] . '" controls="controls">Your browser does not support the audio tag.</audio>' . '</span></div>';
			}
		} else {
			if (!empty($line->mdata['text'])) {
				$html .= '<div class="row"><span class="sender2 pull-right">' . User::model()->findByPk($line->mdata['operator_id'])->getName() . '&nbsp;&nbsp;&nbsp;' . $date . '</span></div><div class="row"><span class="msg2 label pull-right">' . $line->mdata['text'] . '</span></div>';
			} else if (!empty($line->mdata['pic'])) {
				$html .= '<div class="row"><span class="sender2 pull-right">' . User::model()->findByPk($line->mdata['operator_id'])->getName() . '&nbsp;&nbsp;&nbsp;' . $date . '</span></div><div class="row"><span class="msg2 label pull-right" style="width:20%">' . '<span src="' . $line->mdata['pic'] . '" class="img" style="cursor:pointer; font-size:15px; font-style:italic;">[点击查看图片]</span></span></div>';
			} else if (!empty($line->mdata['ticket'])) {
				$html .= '<div class="row msg3"><span>' . User::model()->findByPk($line->mdata['operator_id'])->getName() . '&nbsp;&nbsp;&nbsp;' . $date . '&nbsp;&nbsp;&nbsp;' . $line->mdata['status'] . '&nbsp;Ticket: ' . $line->mdata['no'] . '</span></div>';
			} else if ($line->type == CrmMsgLine::TYPE_AUTO) {
				$html .= '<div class="row msg3"><span>已自动回复于&nbsp;&nbsp;&nbsp;' . $date . '</span></div>';
			}
		}
	}
	echo $html;
} ?>

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {
	$('.img').on('click', function() {
		var src = $(this).attr('src');
		$('img#modal_pic').attr('src', src);
		var margin_left = $('#picModal .modal-dialog').css('margin-left');
		$('#picModal .modal-dialog').css('width', 800).css('margin-left', margin_left - 400);
		$('#picModal').css('display', 'block');
		var modalHeight = Math.max($(window).height() / 2 - $('#picModal .modal-dialog').height() / 2, 0);
		$('#picModal').find('.modal-dialog').css({
			'margin-top': modalHeight
		});
		$('#picModal').modal('show');
	});

	$('img#modal_pic').off('click').on('click', function() {
		$('#picModal').modal('hide');
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>