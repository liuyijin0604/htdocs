<style type="text/css">
#queue p.status_1 {
	font-style: italic;
}
#queue p.status_3{
	color: #0c0;
	font-weight: bold;
}
#queue p.status_9{
	color: #c00;
}
#queue p.status_1:after{
	content: ' - pending';
}
#queue p.status_2:after{
	content: ' - uploading';
}
#queue p.status_3:after{
	content: ' - done';
}
</style>
<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'links' => array(
		'Report' => array('reports/index'),
		'Pickup Scan',
	),
));
?>
<h2><?=$this->t('Pickup Scan');?></h2>
<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#tab1">Scanner</a></li>
  <li><a data-toggle="tab" href="#tab2">Batch Load</a></li>
</ul>
<div class="tab-content">
  <div id="tab1" class="tab-pane fade in active" style="padding: 10px;">
<div class="form">
<form id="scan_form" action="" method="POST">
	<div class="form-group">
		<div class="row">
		<div class="col col-md-4"><input class="form-control" type="text" name="bc" autocomplete="off" id="bc" placeholder="Barcode" /></div>
		<div class="col col-md-4"><button class="btn btn-primary" id="upload_btn" type="submit"><?=$this->t('Add');?></button></div>
		</div>
	</div>
</form>
</div>
  </div>
  <div id="tab2" class="tab-pane fade" style="padding: 10px;">
<div class="form">
<form id="batch_form" action="" method="POST">
	<div class="form-group">
		<p>one barcode per line</p>
		<textarea class="form-control" type="text" name="bcs" autocomplete="off" id="bcs" placeholder="Barcodes"></textarea>
	</div>
	<div class="form-group">
		<button class="btn btn-primary" id="batch_btn" type="submit"><?=$this->t('Add All');?></button>
	</div>
</form>
</div>
  </div>
</div>

<h3 id="summary"></h3>
<div id="queue"></div>
</div><!-- form -->

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	var bcs = [];
	
	var showSum = function(){
		$('#summary').html('Scanned: '+ bcs.length+' &nbsp; Uploaded: '+$('#queue .status_3').length);
	};

	var addBarcode = function(bc){
		if(bc == ''){
			$('#notifc').notify({message: {html: 'Please scan a barcode'}, type: 'danger'}).show();
		}else if($.inArray(bc, bcs) > -1){
			$('#notifc').notify({message: {html: 'Barcode already scaned'}, type: 'danger'}).show();
		}else{
			bcs.push(bc);
			$('#queue').prepend('<p class="status_1">'+bc+'</p>');
			showSum();
		}
	}

	$('#scan_form').on('submit', function(){
		var bc = $.trim($('#bc').val().toUpperCase());
		addBarcode(bc);
		$('#bc').focus();
		return false;
	});

	$('#batch_form').on('submit', function(){
		var bcs = $.trim($('#bcs').val().toUpperCase()).split(/[\n\r\t ,;]+/);
		for(var i in bcs){
			addBarcode(bcs[i]);
		}
		$('#bcs').focus();
		return false;
	});

	$('#bc').on('focus', function(){
		$(this).select();
	}).focus();

	window.setInterval(function(){
		var bs = $('#queue .status_1');
		if(bs.length > 0){
			bs.removeClass('status_1').addClass('status_2');
			var dd = [];
			bs.each(function(i){
				dd.push($(this).text());
			});
			$.post('<?=$this->createUrl("reports/scan");?>', {'bcs': dd.join(',')}, function(r){
				bs.removeClass('status_2').addClass('status_3');
				bs.each(function(i){
					t = $(this).text();
					if(r.err[t]){
						$(this).removeClass('status_3').addClass('status_9').text(t+' - error: '+r.err[t]);
					}
				});
				showSum();
			}, 'json').fail(function(r){
				bs.removeClass('status_2').addClass('status_1');
				showSum();
			});
		}
	}, 5e3);

	$(window).on('beforeunload', function(){
		if($('#queue .status_1').length > 0) return 'Please wait until all data finished uploading.';
	});
});
</script>
<?php $this->registerJS(ob_get_clean(),8); ?>
