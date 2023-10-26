<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
?>
<center>
<h1><?=ucfirst(Yii::app()->session['scan_warehouse'])?> Warehouse</h1>
</center>
<?php
$tts = ['resort' => 'Resorting', 'checkin' => 'CheckIn', 'change' => 'Change Label'];
echo '<h1>Scan for '.(isset($tts[$op])? $tts[$op] : 'Check Status').' (加速版)</h1>';
?>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', [
	'id'=>'scan-form',
	'enableAjaxValidation'=>false,
]);
?>
<div style="float:right;"><!--label id="unpacking_label">Unpacking: <input type="checkbox" id="unpacking" name="unpacking" value="1" /></label> &nbsp;
	<label id = "auto_print_label">Auto Print: <input type="checkbox" id="auto_print" name="auto_print" value="1" /></label--> &nbsp; <select id="voice" name="sound"><option value="">Default Sound</option><option value="1">中文女声</option><option value="2">中文男声</option></select></div>
<div style="font-size: 1.5em;">Barcode: <input style="width: 95%" id="scan" type="text" size="30" name="barcode" autocomplete="off" /></div>
<?php $this->endWidget(); ?>
<div id="result" style="background-color: white;margin-top:10px;">
	<table id="items" class="table table-striped table-bordered" style="font-size: 1.5em;">
		<tbody>
			<tr><td class="status"></td></tr>
			<tr><td class="area"></td></tr>
			<tr><td class="msg"></td></tr>
			<tr><td class="gatepass"></td></tr>
			<tr><td class="console"></td></tr>
			<tr><td class="hold"></td></tr>
		</tbody>
	</table>
</div>
<?php
echo CHtml::button('Print PDF Label', ['class' => 'print_btn_pdf','style'=> 'display:none;margin-left:20px;font-size:xx-large;']);
?>
<input type="hidden" name="scanned-id" value="" id="scaned_shipment_id">
<div id="print-result" style="margin-left: 20px;margin-top:10px;font-size: 1.5em;"></div>
</div>
<iframe id="pdf_label" style="display: none;" name="pdf_label" src="" ></iframe>
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	var cache = {}, nrmap = {}, ttls = {};
	var lstore = window.localStorage;
	var bulk_sending = false;
	var states = <?=json_encode(ImParcel::$states);?>;
	var debug = window.location.href.indexOf('#debug') > 0;
	if(debug) console.log('Debug enabled');

	$('input#scan').focus();
	$('#result tbody tr').hide();

	var updateNrMap = function(){
		nrmap = {};
		for(i in cache){
			if(i != cache[i].r) nrmap[cache[i].r] = i;
		}
	};
	var registerTTL = function(l){
		var ts = Math.floor(Date.now() / 1000) + 600;
		ttls[ts] = Object.keys(l);
		if(debug) console.log('ttl register', ttls[ts]);
	};
	var expireTTL = function(t){
		for(i in ttls[t]){
			if(debug) console.log('delete cache ', ttls[t][i]);
			if(cache[ttls[t][i]]){
				if(nrmap[cache[ttls[t][i]].r]) delete nrmap[cache[ttls[t][i]].r];
				delete cache[ttls[t][i]];
			}
		}
		if(debug) console.log('expired '+ttls[t].length+' records '+t);
		delete ttls[t];
	};

	var parseBarcode = function(barcode){
		barcode = barcode.trim().toUpperCase();
		if(barcode == '') return false;
		var m = ref = sn = null;
		function preg_match(p, s){
			m = s.match(p);
			return m !== null;
		};

		function decodeTNT(c){
			var p = '';
			for (i = 0; i < c.length; i = i + 2) {
				p += String.fromCharCode(Number(c.substr(i,2)) + 55);
			}
			return p;
		};
		if (preg_match(/^T\d{6}(AWNL|AWUJ)\d{10}/, barcode)) { //Toll
			ref = barcode.substr(7, 10);
			sn = barcode.substr(17, 3);
		} else if (preg_match(/^(7RFZ|4XHZ)\d{8}EXP\d{5}/, barcode)) {
			//Startrack
			ref = barcode.substr(0, 12);
			sn = barcode.substr(15, 5);
		} else if (preg_match(/^99700160([A-Z]{3})(\d{7})(\d{11})/, barcode)) { //eparcel legacy
			ref = m[1].m[2];
			sn = m[3].substr(0, 2);
		} else if (preg_match(/^019931265099999891(((\w{3,5})\d{7})(\d{11}))(\d{23}|\d{33})?$/, barcode)) {
			//eparcel
			// barcode = m[1];
			ref = [m[2], m[1]];
			sn = m[4].substr(0, 2);
		} else if (preg_match(/^610412\d{18}0\d{4}0$/, barcode)) { // TNT
			prefix = decodeTNT(barcode.substr(6, 6));
			ref = prefix+barcode.substr(12, 9);
			sn = barcode.substr(21, 3);
			// barcode = ref+barcode.substr(21, 3);
		} else if (preg_match(/^64\d{4}(\d{13})(\d{3})$/, barcode)) { // shippit (Toll)
			ref = m[1];
			sn = m[2];
			// barcode = ref+'-'+sn;
		} else if (preg_match(/^(CP[A-Z]{5}\d{7})(\d{3})$/, barcode)){ //couriers please
			ref = m[1];
			sn = m[2];
		} else if (preg_match(/^(CP[A-Z]{3}\d{10})(\d{2})$/, barcode)){ //couriers please 2
			ref = m[1];
			sn = m[2];
		}else if (preg_match(/^(SCAU\d{8})(\d{3})(\d{3})$/, barcode)){ //saicheng
			ref = m[1];
			sn = m[2];
		} else if (preg_match(/^((?:ZV|PB|DQ)\d{5,6}|UDW\d{7})(\d{3})(\d{7})$/, barcode)){ //hunter express
			ref = m[1];
			sn = m[2];
		} else if (preg_match(/^(246831\d{7})(\d{3})(\d{4})$/, barcode)){ //DFE
			ref = m[1];
			sn = m[2];
		}  else {
			if(preg_match(/^(.+)-(\d+)$/, barcode)) {
				ref = m[1];
				sn = m[2];
			} else {
				ref = barcode;
			}
		}
		sn = sn == null? 0 : sn.replace(/^0+/, '');
		if(!Array.isArray(ref)) ref = [ref];
		return [barcode, ref, sn];
	};
	
/* //test case
	var r = {"ECN2983001881":{"id":"12358478","r":"33G775076869","s":"58"},"33G77507687001000935002":{"id":"12387824","r":"33G77507687001000935002","s":"55"},"DKC000007777":{"id":"915731","r":"ECN1478023905","s":"60"}};
	$.extend(cache, r);
	console.log(cache);
	updateNrMap();
	registerTTL(r);
	// var pb = parseBarcode('01993126509999989133G77507687001000935002420315392488188678008200404155515');
	var pb = parseBarcode('610412132012000007777001021700');
	console.log(pb);
	var hbn = pb[1][0];
	for(k in pb[1]){
		if(cache[pb[1][k]]){
			console.log('00', cache[pb[1][k]]);
			hbn = pb[1][k];
			break;
		}
		if(nrmap[pb[1][k]]){
			console.log('11', nrmap[pb[1][k]]);
			hbn = nrmap[pb[1][k]];
			break;
		}
	}
	console.log('final', hbn);
	bc = cache[hbn];
	console.log(bc);
*/

	$('form#scan-form').on('success', function(e,r){
		$('input#scan-code').val($('input#scan').val());
		$('input#scan').val('').focus();
		$('#print-result').html('');
		var audio=new Audio();
		audio.src='https://os.toplogistics.com.au/site/voice/' + (r.sounds.join('-')) + '.mp3';
		audio.play();
		$('#result tbody tr').hide();
		$(['status', 'area', 'msg', 'area', 'amazon', 'gatepass', 'hold']).each(function(i){
			if(r[this]) $('#result td.'+this).html(r[this]).removeClass('red green blue').addClass(r.color).parent().show();
		});

		if ( r.found == 1 ) {
			$('#scaned_shipment_id').val(r.id).data('sn', r.sn);
		}

		/*if (r.print == 1 ) {
			$('.print_btn_pdf').show();
			if($('input#auto_print').prop('checked')) $('input.print_btn_pdf').trigger('click');
		} else {
			$('.print_btn_pdf').hide();
		}*/

		if(r.cache){
			$.extend(cache, r.cache);
			updateNrMap();
			registerTTL(r.cache);
		}
		return true;
	}).on('submit', function(){
		$('input#scan').focus();
	}).on('before-submit', function(){
		var pb = parseBarcode($('#scan').val());
		if(debug) console.log(pb);
		var hbn = pb[1][0];
		for(k in pb[1]){
			if(cache[pb[1][k]]){
				hbn = pb[1][k];
				break;
			}
			if(nrmap[pb[1][k]]){
				hbn = nrmap[pb[1][k]];
				break;
			}
		}

		if(cache[hbn]){
			bc = cache[hbn];
			if(debug) console.log(bc);
			//map sound
			var sounds = [];
			var voice = $('#voice').val() == ''? '' : '_'+$('#voice').val();
			var color = '';
			switch(Number(bc.s)){
				case 55:
				case 57:
				case 58:
				case 59:
					sounds.push('speed_held'+voice);
					color = 'red';
				break;
				case 60:
					sounds.push('speed_cleared'+voice);
					color = 'green';
				break;
			}

			if(sounds.length > 0){
				var audio=new Audio();
				audio.src='/site/voice/' + (sounds.join('-')) + '.mp3';
				audio.play();
				if(debug) console.log('sound ', sounds);
			}

			$('#result tbody tr').hide();
			$('#result td.status').html(states[bc.s].toUpperCase()+', '+bc.r+(pb[2] == null? '' : ','+pb[2])).removeClass('red green blue').addClass(color).parent().show();

			//prefetch expiring cache
			var ts = Math.floor(Date.now() / 1000);
			for(t in ttls){
				if(t < ts + 10 && ttls[t][hbn] !== null){
					if(debug) console.log('refreshing '+bc.id);
					$.get('<?=Yii::app()->createAbsoluteUrl("whscan/shipment/cacheData");?>?sid='+bc.id, function(r){
						expireTTL(t);
						$.extend(cache, r);
						updateNrMap();
						registerTTL(r);
					}, 'json');
					break;
				}
			}
			lstore.setItem('_ssbc.'+bc.id+'.'+pb[2], pb[0]);
			$(this).data('invalid', true);
		}else{
			$(this).data('invalid', false);
		}
	});

	$('input#scan').on('focus', function(){
		$(this).select();
	});

	$('.print_btn_pdf').click(function(e){
		var pdfFrame = window.frames["pdf_label"];
		$("#pdf_label").attr("src","<?php echo Yii::app()->createAbsoluteUrl("whscan/shipment/connote"); ?>"+"?id="+$('#scaned_shipment_id').val()+'&sn='+$('#scaned_shipment_id').data('sn'));
		$("#pdf_label").load(function(){
			pdfFrame.focus();
			pdfFrame.print();
		});
	});

	var sendScans = function(){
		if(bulk_sending) return;
		var ds = [];
		for(i in lstore){
			if(i.match(/^_ssbc\.\d+/) === null) continue;
			ds.push(lstore[i]);
		}
		if(ds.length == 0) return;
		if(debug) console.log('pushing '+ds.length+' records');
		bulk_sending = true;
		$.post('<?=Yii::app()->createAbsoluteUrl("whscan/shipment/batchScan");?>?op=<?=$op;?>', {bcs: JSON.stringify(ds)}, function(r){
			for(i in r){
				lstore.removeItem('_ssbc.'+r[i]);
			}
			if(debug) console.log('success '+r.length, r);
		}, 'json').always(function() {
			bulk_sending = false;
			if(debug) console.log('reset sending flag');
  		});
	};

	var cacheIntv = setInterval(function(){
		sendScans();

		//expire cache
		var ts = Math.floor(Date.now() / 1000);
		for(t in ttls){
			if(t < ts) expireTTL(t);
		}
	}, 6e4); //60s interval

	$(window).on('unload', sendScans);

	if(navigator.userAgent.match(/Android|iPhone|iPad|iPod|SymbianOS|Windows Phone/) !== null || (window.screen.width < 500 && window.screen.height < 800)){ //Mobile
		$("#auto_print_label").hide();
	}

});
</script>
<?php $this->registerJS(ob_get_clean()); ?>