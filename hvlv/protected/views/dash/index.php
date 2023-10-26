<div id="dashboard">
<?php
foreach($cols as $col){
	echo '<div class="column">';
	foreach($col as $name => $wid){
		$this->render('_widget', array('name' => $name, 'wid' => $wid));
	}
	echo '</div>';
}
?>
</div>

<script type="text/javascript">
$(function() {
	var tab = $('#<?=$_GET["tabid"];?>');
	var pane = tab.data('panel');
	var saveDash = function(){
		var wids = [];
		$('.column', pane).each(function(i){
			$('.portlet', this).each(function(){
				var id = $(this).attr('id').substring(4);
				var hide = $(".portlet-header .collapse-tog", this).hasClass('ui-icon-triangle-1-s')? 1 : 0;
				wids.push({name: id, col: i+1, collapse: hide});
			});
		});
		$.post('dash/save', {data: wids});
	};
	
	$(".column", pane).sortable({
		connectWith: '.column',
		handle: '.portlet-header',
		stop: function(){
			saveDash();
			dashSize();
		}
	});
	$(".portlet", pane).addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
	.hover(function(){$('.portlet-header', this).addClass('ui-state-active');},
	function(){$('.portlet-header', this).removeClass('ui-state-active');})
	.find(".portlet-header")
			.addClass("ui-widget-header ui-corner-top")
			.prepend('<span class="ui-icon collapse-tog ui-icon-triangle-1-n"></span> <span class="ui-icon ui-icon-refresh"></span>')
			.end();
	$(".portlet-header .collapse-tog", pane).click(function() {
		$(this).parents(".portlet:first").find(".portlet-content").slideToggle("fast");
		$(this).toggleClass("ui-icon-triangle-1-s");
		if(!$(this).hasClass("ui-icon-triangle-1-s")) $(this).next('.ui-icon-refresh').trigger('click');
		saveDash();
		return false;	
	});
	
	$(".portlet.collapsed .portlet-content").hide();
	$(".portlet.collapsed .collapse-tog").addClass("ui-icon-triangle-1-s");
	
	$(".portlet-header .ui-icon-refresh", pane).click(function() {
		var wid = $(this).parents(".portlet:first");
		wid.find('.portlet-content').load('dash/widget/'+wid.attr('id').substring(4));
		return false;	
	});
	$(".column .portlet-header", pane).disableSelection();
	
	//auto resize
	var dashSize = function(){
		var h = $('#content').height() - 20;
		$('.column', pane).each(function(){
			var pph = Math.floor(h/$('.portlet', this).length - 45);

		});
	};
	$(window).resize(dashSize);
	dashSize();
	
	//reload every entry
	tab.off('onOpen').on('onOpen', function(){
		tab.trigger('load');
	});
});
</script>