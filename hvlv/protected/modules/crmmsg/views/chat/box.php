<style type="text/css">
div[class*="chatbox"] {
	border: 1px solid grey;
}
.chatbox {
	height: 500px;
	background-color: #EEEEEE;
	padding: 20px;
	overflow-y: scroll;
}
.chatbox_textbox {
	height: 170px;
	padding: 0;
}
span[class*="sender"] {
	padding-top: 15px;
	padding-bottom: 5px;
	font-family: Georgia;
	font-weight: normal;
	color: grey;
	font-size: 15px;
	line-height: 20px;
}
.sender1 {
	margin-left: 25px;
}
.sender2 {
	margin-right: 25px;
}
span[class*="msg1"], span[class*="msg2"] {
	max-width: 40%;
	padding: 8px;
	font-size: 20px;
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
	margin-left: 25px;
}
.msg2 {
	background-color: #90EE90;
	margin-right: 25px;
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
.toolbox {
	width: 100%;
	height: 25%;
}
.toolbox span {
	cursor: pointer;
}
.textbox {
	width: 100%;
	height: 50%;
	resize: none;
	padding-left: 10px;
	border: 0;
	font-size: 22px;
}
.textbox:focus {
	outline: 0;
}
#ticketModal .modal-footer span {
	font-size: 20px;
	margin-left: 15px;
}
img {
	cursor: pointer;
}
</style>
<div class="container-fluid chatbox"></div>

<div class="container-fluid chatbox_textbox">
	<div class="toolbox">
		<button class="btn btn-default" style="position:relative; height:35px; overflow:hidden; width:60px;">
			<span class="glyphicon glyphicon-picture"></span><input type="file" name="uploadfile" multiple="multiple" style="height:100%; width:100%; position:absolute; top:0; left:0; opacity:0; cursor:pointer; padding-left:60px; margin-right:-60px;">
		</button>
		<button class="btn btn-default" id="ticket_btn" style="height:35px; width:60px;">Ticket</button>
		<button class="btn btn-danger" id="close_btn" style="height:35px; width:60px;">Close</button>
	</div>
	<div class="textbox" id="textbox" contenteditable="true"></div>
	<button class="btn btn-primary pull-right" id="send_btn" style="margin-right:5px;">Send</button>
</div>

<div class="modal fade" id="picModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<img src="" id="modal_pic" width="100%" />
			</div>
		</div>
	</div>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {
	$('.textbox').focus();

	$('.textbox').on('keyup', function(e) {
		var keyCode = e.charCode || e.keyCode;
		if (keyCode == 13 && !e.ctrlKey) {
			var msg = $('.textbox').html();
			msg = msg.replace('<div><br></div>', '');
			msg = msg.replace('<div><br></div>', '');
			// msg = msg.replace(/<br>/g, '\n');
			// while (msg.substr(msg.length - 2, 2) == 0) {
			// 	msg = msg.substr(0, msg.length - 2);
			// }
			// lines = msg.split('\n');

			if (msg.match(/<img src="(.+)">/)) {
				sendPic(msg.match(/<img src="(.+)">/)[1]);
			} else if (msg.match(/<img src="(.+)" style/)) {
				sendPic(msg.match(/<img src="(.+)" style/)[1]);
			} else {
				sendText(msg);
			}
			$('.textbox').text('');
			$('.textbox').focus();
		}
		// else if (keyCode == 13 && e.ctrlKey) {
		// 	$('.textbox').append('<br><br>');
		// 		// focus range
		// 		if (window.getSelection) {
		// 			textbox.focus();
		// 			var range = window.getSelection();
		// 			range.selectAllChildren(textbox);
		// 			range.collapseToEnd();
		// 		}
		// }
	});

	$('.textbox').on('paste', function(e) {
		var items = (event.clipboardData || event.originalEvent.clipboardData).items;
		if (items.length > 0 && items[0].kind === 'file') {
			var reader = new FileReader();
			reader.readAsDataURL(items[0].getAsFile());
			reader.onload = function(e) {
				var base64 = e.target.result;
				// create img
				var textbox = document.getElementById('textbox');
				var img = document.createElement('img');
				img.src = base64;
				img.style.height = '80px';
				textbox.appendChild(img);
				// focus range
				if (window.getSelection) {
					textbox.focus();
					var range = window.getSelection();
					range.selectAllChildren(textbox);
					range.collapseToEnd();
				}
			};
		}
	});

	$('#send_btn').on('click', function() {
		var msg = $('.textbox').html().substr(0, $('.textbox').html().length);
		if (msg.match(/<img src="(.+)">/)) {
			sendPic(msg.match(/<img src="(.+)">/)[1]);
		} else if (msg.match(/<img src="(.+)" style/)) {
			sendPic(msg.match(/<img src="(.+)" style/)[1]);
		} else {
			sendText(msg);
		}
		$('.textbox').html('');
		$('.textbox').focus();
	});

	$('#close_btn').on('click', function() {
		var msg_id = sessionStorage.getItem('msg_id');
		var url = '<?=$this->createUrl("chat/close")?>';
		if (msg_id) {
			url = url.replace('.app', '/id/' + msg_id);
		}
		if (confirm('确认关闭')) {
			$.ajax({
				type: 'POST',
				url: url,
				success: function(r) {
					r = JSON.parse(r);
					if (r.success) {
						$('.chatbox').empty();
						msg_id = '';
					}
				}
			});
		}
	});

	$('#ticket_btn').on('click', function() {
		var msg_id = sessionStorage.getItem('msg_id');
		var url = '<?=$this->createUrl("chat/ticket")?>';
		if (msg_id) {
			url = url.replace('.app', '/id/' + msg_id);
		}
		$.ajax({
			type: 'POST',
			url: url,
			success: function(r) {
				r = JSON.parse(r);
				$('#modal').html(r.data);
				$('#ticketModal').modal('show');
			}
		});
	});

	function sendText(msg) {
		var msg_id = sessionStorage.getItem('msg_id');
		var time = new Date();
		time = ((String(time.getHours()).length == 2) ? time.getHours() : '0' + time.getHours()) + ':' + ((String(time.getMinutes()).length == 2) ? time.getMinutes() : '0' + time.getMinutes());
		$('.chatbox').append('<div class="row"><span class="sender2 pull-right">' + '<?=User::model()->findByPk(Yii::app()->user->id)->getName()?>' + '&nbsp;&nbsp;&nbsp;' + time + '</span></div><div class="row"><span class="msg2 label pull-right">' + msg + '</span></div>');
		$(".chatbox").animate({ scrollTop: $('.chatbox').prop("scrollHeight")}, 1000);

		var url = '<?=$this->createUrl("chat/addmsg", array("type" => "text"))?>';
		if (msg_id) {
			url = url.replace('.app', '/id/' + msg_id);
		}
		$.ajax({
			type: 'POST',
			url: url,
			data: {'text': msg},
			success: function(r) {
				if (r) {
					r = JSON.parse(r);
				}
			}
		});
	}

	$('input:file').on('change', function() {
		var files = $(this).prop('files');

		for (var index in files) {
			if (!isNaN(index)) {
				var reader = new FileReader();
				reader.readAsDataURL(files[index]);
				reader.onload = function(e) {
					base64 = this.result;
					sendPic(base64);
				}
			}
		}

		$(this).val('');
	});

	function sendPic(base64) {
		var type = base64.match(/data:image\/(\w+);base64,/);
		if (type) {
			var time = new Date();
			time = ((String(time.getHours()).length == 2) ? time.getHours() : '0' + time.getHours()) + ':' + ((String(time.getMinutes()).length == 2) ? time.getMinutes() : '0' + time.getMinutes());
			$('.chatbox').append('<div class="row"><span class="sender2 pull-right">' + '<?=User::model()->findByPk(Yii::app()->user->id)->getName()?>' + '&nbsp;&nbsp;&nbsp;' + time + '</span></div><div class="row"><span class="msg2 label pull-right" style="width:20%">' + '<img src="' + base64 + '" width="100%" />' + '</span></div>');
			$(".chatbox").animate({ scrollTop: $('.chatbox').prop("scrollHeight")}, 1000);

			$('img').off('click').on('click', function() {
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

			var msg_id = sessionStorage.getItem('msg_id');
			var url = '<?=$this->createUrl("chat/addmsg", array("type" => "pic"))?>';
			if (msg_id) {
				url = url.replace('.app', '/id/' + msg_id);
			}
			$.ajax({
				type: 'POST',
				url: url,
				data: {'pic': base64},
				success: function(r) {
					if (r) {
						r = JSON.parse(r);
					}
				}
			});
		} else {
			alert('暂只接受图片上传');
		}
	}
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>