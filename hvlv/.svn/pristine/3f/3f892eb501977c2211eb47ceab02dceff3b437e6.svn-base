<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
		'homeLink'=>CHtml::link('主页', array('site/index')),
		'links' => array(
					 '我要理赔',
		),
));
?>
<br>
<div class="panel panel-danger">
	<div class="panel-heading">我要理赔</div>
	<div class="panel-body">
		<form role="form" action="<?=$this->createUrl('order/compensation')?>" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<label><span class="required">*</span> 运单号</label>
				<input type="text" name="trackno" class="form-control" placeholder="请输入您的运单号" id="trackno" required />
			</div>
			<div class="form-group">
				<label><span class="required">*</span> 发件人</label>
				<input type="text" name="sender" class="form-control" placeholder="请输入对应的发件人" id="sender" required />
			</div>
			<div class="row form-group">
				<div class="col-md-6 col-sm-12 col-xs-12">
					<label><span class="required">*</span> 电话/手机</label>
					<input type="text" name="tel" class="form-control" placeholder="请输入您的电话或手机，便于客服联系您" id="tel" required />
				</div>
				<div class="col-md-6 col-sm-12 col-xs-12">
					<label><span class="required">*</span> 电子邮件</label>
					<input type="text" name="email" class="form-control" placeholder="请输入您的电子邮件，便于客服联系您" id="email" required />
				</div>
			</div>
			<div class="form-group">
				<label><span class="required">*</span> 理赔原因</label>
			</div>
			<div class="form-group" style="margin-top: -20px;">
				<label class="radio-inline"><h5><input type="radio" name="reason" id="miss" value="丢失" checked />丢失</h5></label>
				<label class="radio-inline"><h5><input type="radio" name="reason" id="damage_outer" value="外箱破损" />外箱破损</h5></label>
				<label class="radio-inline"><h5><input type="radio" name="reason" id="damage_inner" value="内件破损" />内件破损</h5></label>
				<label class="radio-inline"><h5><input type="radio" name="reason" id="other" value="其他原因" />其他原因</h5></label>
				<textarea class="form-control" id="otherreason" readonly></textarea>
			</div>
			<div class="form-group" style="margin-bottom: 0;">
				<label><span class="required">*</span> 附件上传<span class="text-danger"> - 限大小200KB</span></label>
			</div>
			<div class="row">
				<div class="form-group col-md-6 col-sm-6 col-xs-12">
					<label><span class="required">*</span> 破损商品图片，包裹图片（需保留面单），内部填充物图片<span class="text-danger"> - 限上传5张</span></label>
					<div class="row" id="uploadfile-1"></div>
					<div class="btn btn-default" style="position:relative; height:35px; overflow:hidden; width:150px; margin-top:20px;">
						<input type="file" name="uploadfile-1" multiple="multiple" style="height:100%; width:100%; position:absolute; opacity:0; cursor:pointer; padding-left:150px; margin-right:-150px;">选择文件
					</div>
				</div>
				<div class="form-group col-md-6 col-sm-6 col-xs-12">
					<label><span class="required">*</span> 购买凭证（网站交易截图或购物小票）<span class="text-danger"> - 限上传1张</span></label>
					<div class="row" id="uploadfile-2"></div>
					<div class="btn btn-default" style="position:relative; height:35px; overflow:hidden; width:150px; margin-top:20px;">
						<input type="file" name="uploadfile-2" multiple="multiple" style="height:100%; width:100%; position:absolute; opacity:0; cursor:pointer; padding-left:150px; margin-right:-150px;">选择文件
					</div>
				</div>
			</div>
			<div class="form-group" style="margin-top: 0;">
				<button type="submit" class="btn btn-danger ajax-link">申诉</button>
				<a class="btn btn-default" href="<?=$this->createUrl('site/index')?>">返回</a>
			</div>
		</form>
	</div>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
var filenum = 0;
$(function() {
	if (typeof(Storage) !== "undefined") {
		if (sessionStorage.getItem('trackno')) {
			$('#trackno').val(sessionStorage.getItem('trackno'));
		}
		if (sessionStorage.getItem('sender')) {
			$('#sender').val(sessionStorage.getItem('sender'));
		}
		if (sessionStorage.getItem('tel')) {
			$('#tel').val(sessionStorage.getItem('tel'));
		}
		if (sessionStorage.getItem('email')) {
			$('#email').val(sessionStorage.getItem('email'));
		}
	}

	$('input:radio').on('click', function() {
		if ($('input:radio:checked').val() == 'other') {
			$('#otherreason').removeAttr('readonly');
			$('#otherreason').attr('required','required');
			$('#otherreason').attr('placeholder','请填写相关描述');
		} else {
			$('#otherreason').attr('readonly','readonly');
			$('#otherreason').removeAttr('required');
			$('#otherreason').removeAttr('placeholder');
		}
	});

	$('input:file').on('change', function() {
		var name = $(this).attr('name');
		var files = $(this).prop('files');
		var num = $('#' + name).children().length;

		for (var index in files) {
			if (!isNaN(index) && index <= 4 - num) {
				if (files[index].size > 1024 * 200) {
					alert(files[index].name + '大小超过限制');
				} else {
					var reader = new FileReader();
					reader.readAsDataURL(files[index]);
					reader.onload = function(e) {
						if (name == 'uploadfile-1' && $('#uploadfile-1').children().length < 5) {
							$('#' + name).append('<div class="col-md-6 col-sm-6 col-xs-12"><img src="' + this.result + '"  style="width:100%;" /><input type="hidden" name="file[' + (filenum++) + ']" value="' + this.result + '" /></div>');
							$('img').css({'height':$('img').width()*0.5625, 'margin-bottom':$('img').width()*0.1});
						} else if (name == 'uploadfile-2' && $('#uploadfile-2').children().length < 1) {
							$('#' + name).append('<div class="col-md-6 col-sm-6 col-xs-12"><img src="' + this.result + '"  style="width:100%;" /><input type="hidden" name="file[' + 5 + ']" value="' + this.result + '" /></div>');
							$('img').css({'height':$('img').width()*0.5625, 'margin-bottom':$('img').width()*0.1});
						}
					}
				}
			}
		}
	});

	$('form').on('submit', function() {
		if ($('#uploadfile-1').children().length < 1 || $('#uploadfile-1').children().length > 5 && $('#uploadfile-2').children().length != 1) {
			alert('请上传图片');
			return false;
		}

		sessionStorage.setItem('trackno', $('#trackno').val());
		sessionStorage.setItem('sender', $('#sender').val());
		sessionStorage.setItem('tel', $('#tel').val());
		sessionStorage.setItem('email', $('#email').val());
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>