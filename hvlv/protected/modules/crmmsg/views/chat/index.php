<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
		'Cases',
	),
));
?>
<div id="chat_main">
	<div class="row">
		<div class="col-xs-2" id="list"></div>
		<div class="col-xs-8" id="box" style="padding: 0;"></div>
		<div class="col-xs-2" id="tickets"></div>
		<div id="modal"></div>
	</div>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {
	var msg_id;
	var panel_id = 1;
	var panel_len = 1;

	renderList();
	renderBox();
	renderTickets();

	window.clearInterval(window.interval);
	window.interval = setInterval(function() {
		if (window.location.pathname != '/crmmsg/chat/index.app') {
			window.clearInterval(window.interval);
			return;
		}

		if (msg_id) {
			refreshBox();
			renderTickets(msg_id);
		} else {
			renderListPanel();
		}
	}, 20 * 1000);

	function renderList() {
		$.ajax({
			type: 'GET',
			url: '<?=$this->createUrl("chat/list")?>',
			success: function(r) {
				if (r) {
					$('#list').html(r);
				}
			}
		});
	}

	function renderListPanel() {
		var test = '<?=!empty($test) ? $test : ''?>';
		$.ajax({
			type: 'GET',
			url: '<?=$this->createUrl("chat/renderPanel")?>',
			data: { 'type' : panel_id, 'panel_len' : panel_len, 'test' : test },
			success: function(r) {
				if (r) {
					if (panel_id == 1) {
						panel = '#active_panel';
					} else if (panel_id == 2) {
						panel = '#closed_panel';
					}
					$('.panel-body', panel).html(r);

					$('#active_panel').off('show.bs.collapse').on('show.bs.collapse', function() {
						panel_id = 1;
						renderListPanel();
						$('#closed_panel').collapse('hide');
					});

					$('#closed_panel').off('show.bs.collapse').on('show.bs.collapse', function() {
						panel_id = 2;
						renderListPanel();
						$('.panel-body', this).css('max-height', '562px');
						$('#active_panel').collapse('hide');
					});

					$('#closed_panel .panel-body').scroll(function() {
						if ($(this).prop('scrollHeight') - $(this).scrollTop() == 561) {
							$('#closed_panel #next').show();
						} else {
							$('#closed_panel #next').hide();
						}
					});

					$('#closed_panel #next').off('click').on('click', function() {
						$('#closed_panel #next').hide();
						panel_len += 1;
						renderListPanel();
					});

					$('.msg.active').off('click').on('click', function() {
						msg_id = $(this).attr('id').split('_')[1];
						sessionStorage.setItem('msg_id', msg_id);
						renderChatBox();
						renderTickets();
					});
					$('.msg.closed').off('click').on('click', function() {
						msg_id = $(this).attr('id').split('_')[1];
						sessionStorage.setItem('msg_id', msg_id);
						renderChatBox();
						renderTickets();
					});
					<?php if (!empty($test)) { ?>
					$('.msg.assigned').off('click').on('click', function() {
						msg_id = $(this).attr('id').split('_')[1];
						sessionStorage.setItem('msg_id', msg_id);
						renderChatBox();
						renderTickets();
					});
					<?php } ?>
					$('#msg_' + msg_id).css('background', '#EEEEEE');
				}
			}
		});
	}

	function renderBox() {
		var url = '<?=$this->createUrl("chat/renderBox")?>';
		if (msg_id) {
			url = url.replace('.app', '/id/' + msg_id);
		}

		$.ajax({
			type: 'GET',
			url: url,
			success: function(r) {
				if (r) {
					r = JSON.parse(r);
					$('#box').html(r.data);
					renderChatBox();
				}
			}
		});
	}

	function renderChatBox() {
		var url = '<?=$this->createUrl("chat/renderChatbox")?>';
		if (msg_id) {
			url = url.replace('.app', '/id/' + msg_id);
		}

		$.ajax({
			type: 'GET',
			url: url,
			success: function(r) {
				if (r) {
					r = JSON.parse(r);
					$('.chatbox').html(r.data);
					$('.chatbox').animate({ scrollTop: $('.chatbox').prop('scrollHeight')}, 0);
					renderListPanel();
				}
			}
		});
	}

	function renderTickets() {
		var url = '<?=$this->createUrl("chat/renderTickets")?>';
		if (msg_id) {
			url = url.replace('.app', '/id/' + msg_id);
		}

		$.ajax({
			type: 'GET',
			url: url,
			success: function(r) {
				if (r) {
					r = JSON.parse(r);
					$('#tickets').html(r.data);
					$('.ticket').off('click').on('click', function() {
						var ticket_id = $(this).attr('id').split('_')[1];
						$.ajax({
							type: 'POST',
							url: '<?=$this->createUrl("chat/ticket")?>'.replace('.app', '/id/' + msg_id + '/ticket_id/' + ticket_id),
							success: function(r) {
								r = JSON.parse(r);
								$('#modal').html(r.data);
								$('#ticketModal').modal('show');
							}
						});
					});
				}
			}
		});
	}

	function refreshBox() {
		$.ajax({
			type: 'GET',
			url: '<?=$this->createUrl("chat/refreshBox")?>'.replace('.app', '/id/' + msg_id),
			success: function(r) {
				if (r) {
					r = JSON.parse(r);
					r.forEach(function(value, key, r) {
						if (value.text) {
							$('.chatbox').append('<div class="row"><span class="sender1 pull-left">' + value.wechat_name + '&nbsp;&nbsp;&nbsp;' + value.time + '</span></div><div class="row"><span class="msg1 label pull-left">' + value.text + '</span></div>');
						} else if (value.pic) {
							$('.chatbox').append('<div class="row"><span class="sender1 pull-left">' + value.wechat_name  + '&nbsp;&nbsp;&nbsp;' + value.time + '</span></div><div class="row"><span class="msg1 label pull-left" style="width:20%">' + '<span src="' + value.pic + '" class="img" style="cursor:pointer; font-size:15px; font-style:italic;">[点击查看图片]</span></span></div>');
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
						} else if (value.voice) {
							$('.chatbox').append('<div class="row"><span class="sender1 pull-left">' + value.wechat_name  + '&nbsp;&nbsp;&nbsp;' + value.time + '</span></div><div class="row"><span class="msg1 label pull-left">' + value.voice + '</span></div>');
						} else if (value.auto) {
							$('.chatbox').append('<div class="row msg3"><span>已自动回复于&nbsp;&nbsp;&nbsp;' + value.time + '</span></div>');
						}
					});
					$('.chatbox').animate({ scrollTop: $('.chatbox').prop('scrollHeight')}, 1000);
				}
				renderListPanel();
			}
		});
	}
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>