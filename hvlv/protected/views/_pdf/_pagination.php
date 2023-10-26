<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
var pagination = function(){
	var ph = $('body').data('height') || 1560;
	var tpl = $('header, table.chart, footer').addClass('tpl');
	var fpgoh = 0;
	$('.first_page_only').each(function(){
		fpgoh += $(this).height();
	});
	var lpgoh = 0;
	$('.last_page_only').each(function(){
		lpgoh += $(this).height();
	});
	$('.first_page_only, .last_page_only').hide();
	var hh = $('header').height();
	var fh = $('footer').height();
	var th = 0;
	var pph = ph - hh - fh;
	$('table.chart').each(function(){
		th += $(this).height();
		var mb = Number($(this).css('margin-bottom').replace('px',''));
		if(mb > 0) th += mb;
	});
	var tfoot = $('table.chart tfoot');
	if(th > pph - fpgoh - lpgoh){//multiple page
		var thh = $('table.chart thead').height();
		var pages = [];
		pages[0] = $('table.chart').clone();
		pages[0].find('tbody').empty();
		pages[0].find('tfoot').remove();
		var pch = thh+fpgoh;
		var pc = 0;
		var rcount = $('table.chart>tbody tr').length;
		$('table.chart>tbody tr').each(function(i){
			if(pch + $(this).height() >= pph || (i+2 == rcount && pch + $(this).height() + lpgoh >= pph)){
				pc++;
				pch = thh;
				pages[pc] = $('table.chart').clone();
				pages[pc].find('tbody').empty();
				pages[pc].find('tfoot').remove();
			}
			pch += $(this).height();
			$('tbody', pages[pc]).append($(this).clone());
		});
		$(pages).each(function(i){
			var hdr = $('header.tpl').clone().removeClass('tpl');
			var ftr = $('footer.tpl').clone().removeClass('tpl');
			$('<div class="page" id="page-'+i+'"></div>').append(hdr).append(this).append(ftr).appendTo('body');
		});
		$('#page-0 .first_page_only').show();
		$('#page-'+pc+' .last_page_only').show();
		$('#page-'+pc+' footer').css('page-break-after', 'auto');
		tpl.remove();
		$(pages[pc]).append(tfoot);
		$('.plc1', pages[pc]).css('height', pph - lpgoh - pages[pc].height());
	}else{
		$('.plc1').css('height', pph - th - fpgoh - lpgoh);
		$('.first_page_only, .last_page_only').show();
		$('footer').css('page-break-after', 'auto');
	}
	//$('footer').append('<p>'+pc+'</p>');
};
$(function(){
	pagination();
});
</script>