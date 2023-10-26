<h1>Scan for Returned Shipment</h1>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'import-rts-scan-form',
	'enableAjaxValidation'=>false,
));
?>
<p>Barcode: <input id="scan" type="text" size="30" name="barcode" /></p>
<?php $this->endWidget(); ?>
<div id="result" style="display:none;margin: 20px; border: 1px solid;padding:30px 40px; font-weight: bold; font-size: 42px;">
</div>
<audio id="sound" src=""></audio>
</div>

<a class="export_search" target="_blank" href="<?=$this->createUrl('rts/export');?>"><div style="background-position:-48px -688px" class="icon"></div> Export Current Search</a> &nbsp; 
<br><br>
<h1>Received Shipments</h1>
<?php echo $this->renderPartial('list', array('model'=>$model,'dataProvider'=>$dataProvider)); ?>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('input#scan', panel).focus();
	$('form#import-rts-scan-form', panel).data('custom_success', function(r){
	//	$('#sound', panel).attr('src', 'site/voice/'+r.sound+'.mp3');
	//	$('#sound', panel)[0].play();
		$('#result', panel).text(r.msg).css('color', r.color).fadeIn(100, function(){
//			alert(r.msg);
		});

        // refresh returned item list
        $('#im-rts-parcel-grid', panel).yiiGridView('update');

		return true;
	}).on('submit', function(){
		$('input#scan', panel).focus();
	});
	$('input#scan', panel).on('focus', function(){
		$(this).select();
	});
	
	$('input#scan', panel).focus();
	$('a.export_search', panel).on('mousedown', function(){
		var q = $('.filters input, .filters select', panel).serialize()+'&'+$('.search-form form', panel).serialize();
		$(this).attr('href', $(this).attr('href') + '?' + q);
	});

});
</script>