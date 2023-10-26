<form id="pub_form" name="pub_form" method="post" action="pub?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>">
<div class="row" id="s1">
<div class="col c9"><input type="text" class="field" name="agt" id="agtac" placeholder="Agent" /></div>
<div class="col c3"><input type="submit" class="btn" value="Start" /></div>
</div>
<div class="row" id="s2" style="display:none">
<div class="col c9"><input type="text" id="gb_bc" name="bc" class="field" placeholder="Barcode" /></div>
<div class="col c3"><input type="submit" class="btn" value="Enter" /></div>
</div>
<input type="hidden" id="mid" name="mid" />
<div id="scanning" style="display:none;">
	<div class="row">
	<div class="col c9" style="font-size:2em;">Total: <span id="count" data-sent="0">0</span></div>
	<div class="col c3"><input type="button" id="mfbtn" class="btn" value="More" /></div>
	</div>

	<div id="mfin" style="display: none;">
	<div class="row boxes">
	<div class="col c4"><input type="text" name="box[s1]" class="field" placeholder="Size 1" /></div>
	<div class="col c4"><input type="text" name="box[s2]" class="field" placeholder="Size 2" /></div>
	<div class="col c4"><input type="text" name="box[s3]" class="field" placeholder="Size 3" /></div>
	<div class="col c4"><input type="text" name="box[s4]" class="field" placeholder="Size 4" /></div>
	<div class="col c4"><input type="text" name="box[s6]" class="field" placeholder="Size 6" /></div>
	<div class="col c4"><input type="text" name="box[tape]" class="field" placeholder="Tape" /></div>
	</div>
	<div style="clear:both;background: #ddd; width:300px; height: 120px; margin: 0 auto;">
<div style="position:absolute; font-size: 32px; line-height:120px; width: 300px; text-align: center; color: #bbb; z-index:1;">Sign Here</div>
<canvas width="300" height="120" style="position: relative; z-index:2;"></canvas>
</div>
	<input type="button" class="btn" id="clear_btn" value="Clear Signature" /> &nbsp; <input type="button" class="btn" id="fin_btn" value="Finish" />
	</div>
</div>
</form>
<!--iframe id="prntifm" name="prntifm" style="border:0; width:0; height: 0;"></iframe-->
<script type="text/javascript">
var rpbs = Rhaboo.persistent("mypubs");
var pbr;
$(function(){
	$('#pub_form').on('success', function(e, d){
		if(d.nf == 1) return;
		if(d.mi){
			$('#mid').val(d.mi).data('ref', d.ref);
			addLog('Start Pickup #'+d.ref);
			$('#scanning').fadeIn();
			$('#gb_bc').val('');
			$('#count').text('0/0');
			$('#s1').fadeOut();
			$('#s2').fadeIn();
			sigPad.clear();
			return;
		}
	}).on('exit', function(){
		if($('#mid').val() != '') $('#fin_btn').trigger('click');
	}).on('undo', function(e, r){
		rpbs[r.mid].bcs.erase(r.bc);
		$('#count').data('sent', r.tt).text(r.tt + '/' + (Object.keys(rpbs[r.mid].bcs).length - 1));
	}).on('submit', function(){
		var mid = $('#mid').val();
		if(mid > 0){
			var bc = $('#gb_bc').val();
			var ts = (new Date()).getTime() + 1728e5;
			if(!rpbs[mid]) rpbs.write(mid, {bcs: {}, cts: ts, sta: 1});

			if(!rpbs[mid].bcs[bc]){
				rpbs[mid].write('cts', ts);
				rpbs[mid].bcs.write(bc, 1);
				addLog('<span id="l_'+mid+'_'+bc+'">PUB: '+bc+'</span>');
			}else{
				addLog($('<span>PUB: '+bc+', already scanned </span>').append($('#l_'+mid+'_'+bc+' a').clone()).html());
				$('#sound').attr('src', '../site/voice/beep_warn.mp3').trigger('sound');
			}

			$('#count').text($('#count').data('sent') + '/' + (Object.keys(rpbs[mid].bcs).length - 1));

			return false;
		}
	});

	$('#agtac').autoComp({ url: "agtSuggest?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>"});

	$('#fin_btn').click(function(){
		if(sigPad.isEmpty()){
			alert("Please provide signature.");
			$('#tab-fin').trigger('click');
			$('#pub_form').data('no-exit', true);
		}else{
			var boxes = {};
			$('.boxes input').each(function(){
				var k = $(this).attr('name').replace(/box\[([^\]]+)\]/, '$1');
				boxes[k] = $(this).val();
			});
			var mid = $('#mid').val();
			addLog('Pickup #'+$('#mid').data('ref')+' finished, <a class="pub_resume" data-exp="'+((new Date()).getTime() + 18e5)+'" href="#" data-ref="'+$('#mid').data('ref')+'" data-mi="'+mid+'" data-tt="'+$('#count').text()+'">resume</a>');
			$('#pub_form').resetForm();
			$('#scanning, #s2').fadeOut();
			$('#s1').fadeIn();
			$('#pub_form').data('no-exit', false);
			if(!rpbs[mid]) rpbs.write(mid, {bcs: {}, cts: (new Date()).getTime() + 1728e5, sta: 1});
			rpbs[mid].write('boxes', boxes);
			rpbs[mid].write('sig', sigPad.toDataURL());
			rpbs[mid].write('sta', 2);
			sigPad.clear();
			$('#mid').val(0);
			$('#mfbtn').trigger('click');
		}
	});

	$('#log').off('click', 'a.pub_resume').on('click', 'a.pub_resume', function(){
		var t = $(this);
		if((new Date().getTime()) > t.data('exp')){
			alert('Sorry resume expired.');
			t.remove();
			return false;
		}
		if(window.confirm('Add more consignments to Pickup #'+t.data('ref')+'?')){
			var mid = t.data('mi');
			$('#mid').val(mid).data('ref', t.data('ref'));
			$('#count').text(t.data('tt'));
			$('#scanning').fadeIn();
			$('.boxes input').each(function(){
				var k = $(this).attr('name').replace(/box\[([^\]]+)\]/, '$1');
				$(this).val(rpbs[mid].boxes[k]);
			});
			rpbs[mid].write('sta', 1);
		}
		return false;
	});
	$('#clear_btn').on("click", function(e){ sigPad.clear(); });
	$('#mfbtn').on('click', function(){
		$('#mfin').toggle();
		if($(this).val() == 'More'){
			$(this).val('Less');
		}else{
			$(this).val('More');
		}
	});
	var sigPad = new SignaturePad(document.querySelector("canvas"));
});

//pba runner
clearInterval(pbr);
pbr = setInterval(function(){
for(var mid in rpbs){
	if(!rpbs.hasOwnProperty(mid)) continue;

	if((rpbs[mid].cts - (new Date()).getTime()) < 0 && rpbs[mid].sta == 5){
		rpbs.erase(mid);
		continue;
	}
	bcs = [];
	for(var i in rpbs[mid].bcs){
		if(!rpbs[mid].bcs.hasOwnProperty(i)) continue;
		if(rpbs[mid].bcs[i] == 1) bcs.push(i);
	}

	if(bcs.length > 0){
		$.ajax({ type: "POST", url: $('#pub_form').attr('action'), data: {mid: mid, bcs: bcs}, success: function(r){
			for(var i in r.bcs){
				if(r.bcs[i][0] == 2){
					rpbs[r.mid].bcs.write(i, 2);
					$('#l_'+r.mid+'_'+i).append(' <a class="undo" data-exp="'+((new Date()).getTime() + 18e5)+'" href="undoPub?md=<?=$_GET["md"];?>&sn=<?=$_GET["sn"];?>&bc='+i+'&tid='+r.bcs[i][1]+'">undo</a>');
				}else if(r.bcs[i][0] == 9){
					$('#l_'+r.mid+'_'+i).remove();
					addLog('PUB: Warning Duplicate Consignment '+i);
					rpbs[r.mid].bcs.erase(i);
				}
			}
			$('#count').data('sent', r.tt).text(r.tt + '/' + (Object.keys(rpbs[r.mid].bcs).length - 1));
		}, dataType: 'json', global: false });
	}
	if(rpbs[mid].sta == 2){
		var bxs = $.extend({}, rpbs[mid].boxes);
		delete bxs._rhaboo;
		$.ajax({ type: "POST", url: $('#pub_form').attr('action'), data: {mid: mid, box: bxs, sig: rpbs[mid].sig } , success: function(r){
			rpbs[r.mid].write('sta', 5);
			rpbs[r.mid].erase('sig');
		}, dataType: 'json', global: false });
	}
}}, 10e3);
</script>
