(function($){
$.fn.jqEditableGrid = function(o){
	o = $.extend({
		version: 0.9,
		formUrl: '',
		afterInit: '',
		afterSave: ''
	}, o || {});
	var grid = this.addClass('editableGrid');
	
	var init = function(){
		$('tbody input[type=text]', grid).each(function(){
			if($(this).attr('type') != 'hidden')
				$(this).after('<span>'+$(this).val()+'</span>').hide();
		});
		$('tbody select', grid).each(function(){
			if($(this).attr('type') != 'hidden')
				$(this).after('<span>'+$('option:selected',this).text()+'</span>').hide();
		});
		$('thead input.grid_selection', grid).click(function(){
			var id = $(this).attr('id');
			$('tbody input.'+id, grid).each(function(){
				$(this).attr('checked', !$(this).attr('checked'));
			});
		});
		$('tbody a.cancel_btn, tbody a.save_btn', grid).hide();
		$('tbody tr', grid).data('editting', false).dblclick(function(){
			if($(this).data('editting') == false && $(this).find('.save_btn').length > 0) editRow($(this));
		});
		$('a.edit_btn', grid).click(function(){
			editRow($(this).parents('tr'));
			return false;
		});
		$('a.cancel_btn', grid).click(function(){
			cancelEdit($(this).parents('tr'), false);
			return false;
		});
		grid.off('click', 'a.save_btn').on('click', 'a.save_btn', function(){
			saveRow($(this).parents('tr'));
			return false;
		});
		//ENTER Key
		grid.off('keypress', 'input, select').on('keypress', 'input, select', function(evt){
			if(evt.keyCode == 13){
				saveRow($(this).parents('tr'));
				return false;
			}
		});
		
		if(typeof o.afterInit == 'function')  o.afterInit(grid);
	};
	
	var editRow = function(r){
		r.data('editting', true);
		r.find('input, select').each(function(){
			$(this).show().next('span, select').hide();
		});
		r.find('a.edit_btn').hide();
		r.find('a.cancel_btn, a.save_btn').show();
	};
	
	var cancelEdit = function(r, s){
		r.find('input[type=text]').each(function(){
			if(s){
				$(this).hide().next('span').text($(this).val()).show();
			}else{
				var v = $(this).hide().next('span').show().text();
				$(this).val(v);
			}
		});
		r.find('select').each(function(){
			var t = $('option:selected',this).text();
			if(s){
				$(this).hide().next('span').text(t).show();
			}else{
				var v = $(this).hide().next('span').show().text();
				$('option', this).attr('selected', false).each(function(){
					if($(this).text() == v) $(this).attr('selected', true);
				});
			}
		});
		r.find('a.edit_btn').show();
		r.find('a.cancel_btn, a.save_btn').hide();
		r.data('editting', false);
	};
	
	var saveRow = function(r){
		var data = {};
		r.find('input, select').removeClass('error').each(function(){
			$(this).trigger('validate');
            if ( $(this).is(':checkbox') ) {
                data[$(this).attr('name')] = $(this).prop('checked') ? 1 : 0;
            } else {
                data[$(this).attr('name')] = $(this).val();
            }
		});
		if(r.find('input.error, select.error').length == 0)
		$.post(o.formUrl, data, function(j){
			var saved = true;
			if(typeof o.afterSave == 'function') saved = o.afterSave(j, grid);
			if(saved){
				if(r.parent().get(0).tagName.toLowerCase() == 'tfoot'){//new record
					$('#'+grid.attr('id')).yiiGridView('update');
				}else{
					cancelEdit(r, true);
				}
			}
		}, 'json');
	}
	
	init();
	
	return this;
}})(jQuery);