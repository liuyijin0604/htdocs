<h1>Fetch IDs</h1>
<pre id="output">Loading...</pre>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$(document).everyTime(5000, 'fetch_id_et', function(){
		if(panel.is(':visible')){
			$.get('<?=$this->createUrl("cnID/fetch");?>?out=1', function(r){
				$('#output', panel).html('hello');
				$('#output', panel).html(r.out + (r.status > 0? '...' :''));
				if(r.status == 0) $(document).stopTime('fetch_id_et');
			}, 'json');
		}
	});
	tab.on('close', function(){
		$(document).stopTime('fetch_id_et');
	});
});
</script>