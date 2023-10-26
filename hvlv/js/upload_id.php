<?php
$ttl = 5184000;
header('Expires: '.gmdate('D, d M Y H:i:s', time() + $ttl) . ' GMT');
header('Pragma: cache');
header('Cache-Control: max-age='.$ttl);
header('Cache-Control: private',false);
header('Content-Type: text/javascript');
?>
/**
* Chinese ID uploder API
*
* Variables:
* lang - Define interface language 'en' or 'zh', [default='en']
* container_id - Define the container DOM ID, [default='pca_id_uploader']
* exlib - Exclude any JS library, by default jquery,jquery-ui,plupload are loaded
* nocss - Exclude stylesheet if defined
*
* Copyright: PCA Express
* Version: 1.0
*/
<?php
$lang = (isset($_GET['lang']) && $_GET['lang'] == 'zh')? 'zh' : 'en';
$jsQ = ['jquery' => 'https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', 
		'jquery-ui' => 'https://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js', 
		'plupload' => 'plupload/plupload.full.min.js'];
if($lang == 'zh') $jsQ[] = 'plupload/i18n/zh_cn.js';
if(isset($_GET['exlib'])){
	$exlib = explode(',', $_GET['exlib']);
	foreach($exlib as $el){
		if(isset($jsQ[$el])) unset($jsQ[$el]);
	}
}

function t($s){
	global $lang;
	if($lang == 'en') return $s;
	$sp = [
		'Full Name' => '姓名',
		'ID Number' => '身份证号码',
		'Mobile' => '手机号码',
		'Connote#' => '运单号',
		'Upload' => '上传',
		'Select Files' => '选择文件',
		'Swap Sides' => '正反对调',
		'Front' => '正面',
		'Back' => '反面',
		'Drag and drop files here or click Select Files button to add files.<br />Please upload both side of the ID card in separate files.' => '拖拽要上传的文件到这里, 或点击选择文件。<br />身份证正反面需要有两个分别的文件。',
		'Your browser does not have Flash, Silverlight or HTML5 support.' => '对不起，您的浏览系不支持文件上传。',
		'Please select both front and back of the ID card to continue.' => '请选择身份证的正面和反面文件。',
		'ID name invalid, please check.' => '请正确填写中文姓名。',
		'For better mathing, please enter either your mobile number or the connote#, or both.' => '为了确保更好的对应，请至少输入手机号和运单号中的一项。',
		'Uploaded successfully, thanks!' => '上传成功，谢谢！',
		'Uploading, please do not navigate away or close this page.' => '正在上传请稍候，不要离开或关闭页面。',
		'Upload failed, please try again.' => '上传失败，请重试',
		'ID number invalid, please check.' => '身份证号码格式错误，请确认。',
		'Mobile number invalid, acceptable format: 13123456789, 010-12345678' => '电话号码格式错误，有效格式如：13123456789, 010-12345678',
	];
	return $sp[$s];
};

//load from js cache
$pkg = '../assets/'.md5(implode(',',$jsQ)).'.js';
if(is_file($pkg) && filectime($pkg) < time() - $ttl) unlink($pkg);

if(!is_file($pkg)){
	foreach($jsQ as $js){
		file_put_contents($pkg, file_get_contents($js)."\n", FILE_APPEND);
	}
}
readfile($pkg);

$ctnr_id = empty($_GET['container_id'])? 'pca_id_uploader' : $_GET['container_id'];

if(empty($_GET['nocss'])):
?>
//add style
jQuery('head').append('<style type="text/css">#<?=$ctnr_id;?> .msg .warn{ color: #c00; font-weight: bold; } #<?=$ctnr_id;?> .msg .note{ color: #0c0; font-weight: bold; } #<?=$ctnr_id;?> .form-row{ padding-bottom: 10px;} #<?=$ctnr_id;?> .form-row label{ width: 100px; display: inline-block; } #<?=$ctnr_id;?> .form-row input{ width: 200px; } #<?=$ctnr_id;?> .form-row input.error{ background-color: #fdd; } #<?=$ctnr_id;?> .filelist{ padding: 10px; border: 2px solid #ddd; margin-bottom: 10px; float:left; min-width: 300px; max-width:100%; box-sizing: border-box;} #<?=$ctnr_id;?> .filelist.droptarget{ background-color: #dfd; } #<?=$ctnr_id;?> .filelist .ddhint{ padding: 50px 30px; font-size: 1.2em;} #<?=$ctnr_id;?> .swap{ display: none; clear:both;}#<?=$ctnr_id;?> .qfile{ width: 200px; float: left; position: relative; margin: 5px;} #<?=$ctnr_id;?> .qfile .side{ text-align: center; font-weight: bold; font-size: 1.2em; }#<?=$ctnr_id;?> .qfile .del{ position: absolute; right: -5px; top: -5px; display: none; border-radius: 10px; width: 20px; height: 20px; line-height: 18px; text-align: center; font-weight: bold; font-size: 18px; cursor: pointer; background: #ccc; color: #c00; transition: 0.5s; } #<?=$ctnr_id;?> .qfile:hover .del{ display: block;} #<?=$ctnr_id;?> .qfile:hover .del:hover{ background: #c00; color: #fff;} #<?=$ctnr_id;?> .qfile .detail{ font-size: 0.8em;} #<?=$ctnr_id;?> .qfile .progress{ height: 10px; width: 0%; background: #BC3426; } #<?=$ctnr_id;?> .buttons{ clear:both; } #<?=$ctnr_id;?> .uploading { position: relative; clear:both; font-size: 1.4em; font-weight: bold; color: #BC3426; line-height: 32px; background: #fff; z-index: 99; padding: 15px 5px; margin-bottom: -60px; display: none; }</style>');
<?php endif; ?>

//id uploader
jQuery(function(){
	var $ = jQuery;
	var ctnr = $('#<?=$ctnr_id;?>');
	ctnr.html('<div class="msg"></div><div class="fields" style="display:none;"><div class="form-row"><label><?=t("Full Name");?>:</label> <input type="text" class="id_name required" name="id_name" placeholder="中文全名" /></div><div class="form-row"><label><?=t("ID Number");?>:</label> <input type="text" class="id_no required" name="id_no" /></div><div class="form-row"><label><?=t("Mobile");?>:</label> <input type="text" class="id_mobile" name="id_mobile" /></div><div class="form-row"><label><?=t("Connote#");?>:</label> <input type="text" class="id_connote" name="id_connote" /></div></div><div class="filelist"><?=t("Your browser does not have Flash, Silverlight or HTML5 support.");?></div><div class="uploading"><img src="https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif" width="24" /><?=t("Uploading, please do not navigate away or close this page.");?></div><div class="buttons" style="display:relative;"><button class="pickfiles" type="button"><?=t("Select Files");?></button> <button class="swap" type="button"><?=t("Swap Sides");?></button> <button class="upload" type="submit"><?=t("Upload");?></button></div>').trigger('onReady');

	var uploader = new plupload.Uploader({
		runtimes : 'html5,flash,silverlight,html4',
		browse_button : $('.pickfiles', ctnr).get(0),
		url : 'https://os.pcaex.com/client/uploadID',
		flash_swf_url : 'https://www.pcaexpress.com.au/client/js/plupload/Moxie.swf',
		silverlight_xap_url : 'https://www.pcaexpress.com.au/client/js/plupload/Moxie.xap',
		filters : {
			max_file_size : '5mb',
			mime_types: [{title : "Image files", extensions : "jpg,jpeg,gif,png"}]
		},
		resize: {'width': 600, 'height': 600, 'quality': 80},
		drop_element: $('.filelist', ctnr).get(0),
		unique_names : true,
		prevent_duplicates: true,
		max_files: 2,
		init: {
			PostInit: function() {
				$('.fields, .buttons', ctnr).show();
				$('.filelist', ctnr).html('<div class="ddhint"><?=t("Drag and drop files here or click Select Files button to add files.<br />Please upload both side of the ID card in separate files.");?></div>');
				if(uploader.runtime === 'html5') {
					$('.filelist', ctnr).on('dragover', function() {
						$('.filelist', ctnr).addClass('droptarget');
					});

					$('.filelist', ctnr).on('dragleave', function(e) {
						$(this).removeClass('droptarget');
					});
				}
				if($.fn.sortable) $(".filelist", ctnr).sortable({containment: "parent", distance: 5, tolerance: "pointer", stop: function(evt, ui){
					$(".filelist").trigger('setSides');
				}});
				$('.swap', ctnr).on('click', function(){
					$($(".filelist .qfile").get(0)).appendTo($('.filelist', ctnr));
					$(".filelist").trigger('setSides');
				});

				$('.filelist', ctnr).on('setSides', function(){
					$(".filelist .qfile", ctnr).each(function(i){
						$('.side', this).text(i==0? '<?=t("Front");?>' : '<?=t("Back");?>');
					});
				});

				$('.buttons .upload', ctnr).on('click', function(){
					var v = true;
					var msg = [];
					var mbl = $('.id_mobile', ctnr).val();
					uploader.settings.multipart_params = {};
					
					if(!(/^[\u4e00-\u9fa5· \.]+$/.test($('.id_name', ctnr).val()))){
						msg.push('<span class="warn"><?=t("ID name invalid, please check.");?></span>');
						v = false;
						$('.id_name', ctnr).addClass('error');
					}else{
						$('.id_name', ctnr).removeClass('error');
					}
					var checkIDCard = function(idcode){
						var weight_factor = [7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2];
						var check_code = ['1', '0', 'X' , '9', '8', '7', '6', '5', '4', '3', '2'];
						var num = 0;
						for(var i = 0; i < 17; i++){
							num += idcode[i] * weight_factor[i];
						}

						return idcode[17].toUpperCase() === check_code[num%11] && /^[1-9]\d{5}(19|20)[0-9]{2}(0[1-9]|1[0|1|2])(0[1-9]|[12][\d]|3[01])\d{3}([\dXx])$/.test(idcode);
					}

					if(!checkIDCard($('.id_no', ctnr).val())){
						msg.push('<span class="warn"><?=t("ID number invalid, please check.");?></span>');
						v = false;
						$('.id_no', ctnr).addClass('error');
					}else{
						$('.id_no', ctnr).removeClass('error');
					}

					if(mbl == '' && $('.id_connote', ctnr).val() == ''){
						msg.push('<span class="warn"><?=t("For better mathing, please enter either your mobile number or the connote#, or both.");?></span>');
						v = false;
					}

					if(mbl != ''){
						var regMobile = /^1[3|4|5|6|7|8|9][0-9]{9}$/;
						var regPhone = /^(([0\+]\d{2,3}[- ]{1})?(0\d{2,3})[- ]{1})?(\d{7,8})$/;
						if(!regMobile.test(mbl) && !regPhone.test(mbl)){
							msg.push('<span class="warn"><?=t("Mobile number invalid, acceptable format: 13123456789, 010-12345678");?></span>');
							v = false;
							$('.id_mobile', ctnr).addClass('error');
						}else{
							$('.id_mobile', ctnr).removeClass('error');
						}
					}

					if($(".filelist .qfile", ctnr).length < 2){
						v = false;
						msg.push('<span class="warn"><?=t("Please select both front and back of the ID card to continue.");?></span>');
					}else{
						var idno = $('.fields input.id_no').val();
						$(".filelist .qfile", ctnr).each(function(i){
							for(var k in uploader.files){
								if(uploader.files[k].id == $(this).data('fid')){
									m = /\.(.+)$/.exec(uploader.files[k].name);
									if(i === 0){
										uploader.files[k].name = idno+'_1'+m[0];
									}else{
										uploader.files[k].name = idno+'_2'+m[0];
									}
								}
							}
						});
					}

					$('.fields input', ctnr).each(function(){
						uploader.settings.multipart_params[$(this).attr('name')] = $(this).val();
					});

					if(v){
						uploader.start();
						$('.msg', ctnr).empty();
						$('html, body').animate({ scrollTop: Math.round($('.filelist').offset().top) }, 1e3);
						$('.uploading', ctnr).fadeIn();
					}else{
						$('.msg', ctnr).html(msg.join('<br />'));
					}
				});
			},

			FilesAdded: function(up, files) {
				$('.ddhint', ctnr).hide();
				if(up.files.length >= up.settings.max_files){
					up.files.splice(up.settings.max_files);
					$(up.settings.browse_button).attr('disabled', true);
					$('.swap', ctnr).fadeIn();
				}
				$('.qfile', ctnr).remove();
				plupload.each(up.files, function(file) {
					var elm = $('<div class="qfile" data-fid="' + file.id + '"><div class="side"></div><div class="del">&times;</div><div class="preview"></div><div class="detail">' + file.name + ' (' + plupload.formatSize(file.size) + ')</div><div class="progress"></div></div>');
					$('.filelist', ctnr).append(elm);
					var img = new moxie.image.Image();
					img.onload = function() {
						this.embed($('.preview', elm).get(0), {
							width: 175,
							height: 130,
							crop: true
						});
					};
					img.onembedded = function() { this.destroy(); };
					img.onerror = function() { this.destroy(); };
					img.load(file.getSource());
					$('.del', elm).on('click', function(){
						uploader.removeFile(file.id);
						elm.remove();
						return false;
					});
				});
				$(".filelist").trigger('setSides');
			},

			FilesRemoved: function(up, files) {
				if(up.files.length < up.settings.max_files){
					$(up.settings.browse_button).attr('disabled', false);
					$('.swap', ctnr).fadeOut();
					if(up.files.length == 0){
						$('.ddhint', ctnr).fadeIn();
					}
				}
			},

			BeforeUpload: function(up, file){
				uploader.settings.multipart_params['name'] = file.name;
			},

			UploadProgress: function(up, file) {
				$('.qfile[data-fid='+file.id+'] .progress', ctnr).css('width', file.percent + "%");
			},

			FileUploaded: function(up, file, info) {
				if(info.response != 'DONE') file.status = 4;
			},

			UploadComplete: function(up, files) {
				done = true;
				for(i in files){
					if(files[i].status != 5){
						$('.msg', ctnr).append('<span class="warn">'+files[i].name+' <?=t("Upload failed, please try again.");?></span><br />');
						done = false;
					}
				}
				$('.fields input', ctnr).val('');
				uploader.splice();
				uploader.refresh();
				$(up.settings.browse_button).attr('disabled', false);
				$('.swap', ctnr).fadeOut();
				$(".filelist .qfile", ctnr).remove();
				$('.ddhint', ctnr).fadeIn();
				$('html, body').animate({ scrollTop: 0 }, 1e3);
				$('.uploading', ctnr).fadeOut();
				if(done) $('.msg', ctnr).html('<span class="note"><?=t("Uploaded successfully, thanks!");?></span>');
			},

			Error: function(up, err) {
				window.alert("Error #" + err.code + ": " + err.message);
			}
		}
	});

	uploader.init();
});
