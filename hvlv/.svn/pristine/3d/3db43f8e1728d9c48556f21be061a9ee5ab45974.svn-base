<div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalLabel">New Ticket</h4>
			</div>
			<div class="modal-body">
				<div class="container" style="width:100%;">
					<form id="ticket_form">
						<div class="row">
							<div class="col-xs-12">
								<?php echo CHtml::label('Type', 'type'); ?>
								<br>
								<?php echo Chtml::radioButtonList('ExCrm[type]', (!empty($crm) ? $crm->type : '10'), ExCrm::$types_cn, array('separator' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;')); ?>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-xs-12">
								<?php echo CHtml::label('Source', 'source'); ?>
								<br>
								<?php echo CHtml::radioButtonList('ExCrm[source]', (!empty($crm) ? $crm->source : '11'), ExCrm::$sources, array('separator' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;')); ?>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-xs-12">
								<?php echo CHtml::label('运单号 <span class="required">*</span> (多个单号用;或者,或者断行分隔)', 'connote'); ?>
								<br>
								<?php
								$shipments = '';
								if (!empty($crm->shipments)) {
									foreach ($crm->shipments as $shipment) {
										$shipments .= $shipment->hbn . ', ';
									}
									$shipments = substr($shipments, 0, strlen($shipments) - 2);
								}?>
								<?php echo CHtml::textArea('connote_no', $shipments, array('style' => 'width:700px; height:150px; resize:none;', 'required' => 'required')); ?>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-6 col-xs-12">
								<?php echo CHtml::label('电子邮箱 <span class="required">*</span>', 'email'); ?>
								<?php echo CHtml::textField('ExCrm[email]', (!empty($crm) ? $crm->email : ''), array('style' => 'width:246px;', 'required' => 'required')); ?>
							</div>
							<div class="col-md-6 col-xs-12">
								<?php echo CHtml::label('手机号码 <span class="required">*</span>', 'telephone'); ?>
								<?php echo CHtml::textField('ExCrm[telephone]', (!empty($crm) ? $crm->telephone : ''), array('style' => 'width:246px;', 'required' => 'required')); ?>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-xs-12">
								<?php echo CHtml::label('Note', 'note'); ?>
								<br>
								<?php echo CHtml::textArea('notes', (!empty($crm->notes) ? $crm->notes[count($crm->notes)-1]->note : ''), array('style' => 'width:700px; height:100px; resize:none;')); ?>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="modal-footer">
			<?php if (!empty($crm)) { ?>
				<button type="button" class="btn btn-primary pull-right" id="update_btn">Update</button>
			<?php } else { ?>
				<button type="button" class="btn btn-primary pull-right" id="create_btn">Create</button>
			<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {
	$('#create_btn').on('click', function() {
		ticket('create', '<?=Yii::app()->createUrl("ExCrm/create")?>');
	});

	<?php if (!empty($crm)) { ?>
	$('#update_btn').on('click', function() {
		ticket('update', '<?=Yii::app()->createUrl("ExCrm/update", array("id" => $crm->id))?>');
	});
	<?php } ?>

	function ticket(type, url) {
		var formData = $('#ticket_form').serialize();
		$.ajax({
			type: 'POST',
			url: url,
			data: formData + '&ppupload=',
			success: function(r) {
				if (r) {
					r = JSON.parse(r);
					if (r.done) {
						$('#ticketModal .modal-footer').append('<span class="text-success pull-left">' + r.msg + '</span>');
						if (type == 'create') {
							var ticket_no = r.msg.match(/单号：(\w+)/);
							var ticket_id = ticket_no[1];
							var status = '创建';
						}
						<?php if (!empty($crm)) { ?>
						if (type == 'update') {
							var ticket_id = '<?=$crm->no?>';
							var status = '更新';
						}
						<?php } ?>

						setTimeout(function() {
							$('#ticketModal .close').click();
							var time = new Date();
							time = ((String(time.getHours()).length == 2) ? time.getHours() : '0' + time.getHours()) + ':' + ((String(time.getMinutes()).length == 2) ? time.getMinutes() : '0' + time.getMinutes());
							$('.chatbox').append('<div class="row msg3"><span>' + '<?=User::model()->findByPk(Yii::app()->user->id)->getName()?>' + '&nbsp;&nbsp;&nbsp;' + time + '&nbsp;&nbsp;&nbsp;' + status + '&nbsp;Ticket: ' + ticket_id + '</span></div>');
							$(".chatbox").animate({ scrollTop: $('.chatbox').prop("scrollHeight")}, 1000);
							$('.textbox').val('');
							$('.textbox').focus();
						}, 1500);

						$.ajax({
							type: 'POST',
							url: '<?=$this->createUrl("chat/addmsg", array("id" => $crm_msg->id, "type" => "ticket"))?>',
							data: { ticket_no: ticket_id, status: status },
							success: function(r) {
								if (r) {
									r = JSON.parse(r);
								}
							}
						});
					} else {
						$('#ticketModal .modal-footer').append('<span class="text-danger pull-left">' + r.msg + '</span>');
					}
				}
			}
		});
	}

	$('#ticketModal').on('show.bs.modal', function(e) {
		var margin_left = $('#ticketModal .modal-dialog').css('margin-left');
		$('#ticketModal .modal-dialog').css('width', 800).css('margin-left', margin_left - 400);

		$(this).css('display', 'block');
		var modalHeight = $(window).height() / 2 - $('#ticketModal .modal-dialog').height() / 2;
		$(this).find('.modal-dialog').css({
			'margin-top': modalHeight
		});
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>