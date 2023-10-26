(function($){
$.oriteApp = function(conf){
this.config = $.extend({
	baseURL: '',
	hometab: null,
	autoLock: false,
	timeout: 1200
}, conf || {});

var app = this;
app.session = {};
app.tabs = null;
app.himenu = null;
app.appmsgs = [];
app.assets = [];

this.alert = function(m, e, cb){
	var aml = app.appmsgs.length;
	var msg = $('#appmsg').clone().attr('id','appmsg'+aml).prependTo($('body'));
	app.appmsgs[aml] = msg;
	if(e){ msg.find('.appmsgwin').addClass('appmsgwinErr');	}
	msg.find('.msgContent').html(m);
	msg.jqm({modal: true, toTop: true}).jqmShow();
	msg.find('.msgOk').on('click', function(){
		msg.jqm().jqmHide();
		msg.remove();
		app.appmsgs.splice(aml,1);
		if(typeof cb == 'function'){ cb(); }
	});
	return msg;
};

this.formatNumber =  function formatNumber(m,c, d, t, p){
        var n = parseFloat(m), c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "." : d, t = t == undefined ? "," : t, s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
        return (p||'') + s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t)
            + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

this.message = function(m, cb){
	var aml = app.appmsgs.length;
	var msg = $('#appmsg').clone().attr('id','appmsg'+aml).prependTo($('body'));
	app.appmsgs[aml] = msg;
	msg.find('.appmsgwin').addClass('appmsgwinSucc');
	msg.find('.msgContent').html(m);
	msg.jqm({modal: true, toTop: true}).jqmShow();
	msg.find('.msgOk').on('click', function(){
		msg.jqm().jqmHide();
		msg.remove();
		app.appmsgs.splice(aml,1);
		if(typeof cb == 'function'){ cb(); }
	});
	return msg;
};

this.notice = function(msg, timeout, t) {
	t = t || 'body';
	timeout = timeout || 5000;
	$('<div class="app-notice">'+msg+'</div>').appendTo(t).fadeIn().delay(timeout).fadeOut(500,function(){$(this).remove();});
};

this.confirm = function(m, cby, cbn){
	var aml = app.appmsgs.length;
	var msg = $('#appmsg').clone().attr('id','appmsg'+aml).prependTo($('body'));
	app.appmsgs[aml] = msg;
	msg.find('.appmsgwin').addClass('appmsgwinConfirm');
	msg.find('.msgContent').html(m);
	msg.jqm({modal: true, toTop: true}).jqmShow();
	msg.on('close', function(){
		msg.jqm().jqmHide();
		msg.remove();
		app.appmsgs.splice(aml,1);
	});
	msg.find('.msgYes').on('click', function(){
		if(typeof cby == 'function') cby();
		msg.trigger('close');
	});
	msg.find('.msgNo').on('click', function(){
		if(typeof cbn == 'function') cbn();
		msg.trigger('close');
	});
	return msg;
};

this.genPassword = function(l){
	var h='abcdefghigklmnopqrstuvwxyz0123456789ABCDEFGHIGKLMNOPQRSTUVWXYZ';
	var pwd='';
	while(pwd.length<l){
		pwd+=h.charAt(getrand(h.length));
	}
	return pwd;
};

this.auth = function(){
	$.ajax({
		url: app.config.baseURL + 'site/auth.app',
		dataType: 'json',
		success: function(r){
			app.session = r;
			if(r.valid){
				app.load();
				if(r.rpc == 1){
					app.message('You are required to change password.');
					app.tabs.CreateTab({
							title: 'User Profile',
							url: 'user/profile',
							bg: false
						});
				}
			}else{
				app.showLogin();
			}
		},
		error: function(r, e){alert(e);}
	});
};

this.init = function(){
	//prevent middle/right click
	$(document).on('click auxclick', function(evt){
		if(evt.which == 2 && $(evt.target).attr('target') != '_blank'){
			evt.preventDefault();
			return false;
		}
	});

	//prep login form
	$("#loginform").validate({
			errorPlacement: function(err, el) { return; },
			submitHandler: function(f) {
				$(f).ajaxSubmit({
					url: $(f).attr('action'),
					resetForm: true,
					dataType: 'json',
					success: function(r){
						app.session = r;
						if(r.valid){
							app.load();
							$('#login').jqm().jqmHide();
						}else{
							$('#login').jqm().jqmHide();
							app.alert('Login failed, please try again!', false, function(){app.showLogin();});

						}
					}
				});
			}
	});

	//prep captcha
	$('#lf_capcha').on('reload', function(){
		$(this).attr('src', app.config.baseURL + 'site/captcha/' + Math.round(100*Math.random()));
	}).on('click', function(){ $(this).trigger('reload'); });
	$('#lf_pwd').focus(function(){
		$('#lf_capcha').trigger('reload');
	});

	//window resize
	$(window).resize(function(){
		var sc = $('body').data('scale');
		var wh = $(window).height() / sc;
		var ww = $(window).width() / sc;
		
		$('body').width(ww).height(wh);
		$('#main').height(wh - 62);
		$('#rightpanel').width(ww - ($('#leftpanel').css('display') == 'none'? 12 : 217));
		$('#content').height(wh - 66 - $('#tabs').outerHeight());
		$('.jqmw_cont').each(function(){
			$(this).height($(this).parent().height());
		});
	});

	//authenticate
	app.auth();
};

//messages
$.oMessage = {
	opt: {cid: 'oMessage-container',
		onShow: function(evt,id){
			$.oMessage.chkAjax();
			$.oMessage.opt.sjax = true;
			$.get(app.config.baseURL + 'message/status/'+id+'?status=1', function(){
				$.oMessage.opt.sjax = false;
			});
		},
		onHide: function(evt,id){
			// $.oMessage.chkAjax();
			// $.oMessage.opt.sjax = true;
			// $.get(app.config.baseURL + 'message/status/'+id+'?status=2', function(){
			// 	$.oMessage.opt.sjax = false;
			// });
		},
		url: app.config.baseURL + 'message/load',
		noticeUrl: app.config.baseURL + 'message/notice',
		type: 'notify',
		msg: '',
		id: 0,
		from: '',
		ajax: false,
		sajx: false
	},
	add: function(o) {
		var opt = $.extend($.oMessage.opt, o||{}),
			ctn = $('#' + opt.cid),
			msg = $('<div/>').data('id', opt.id).addClass(opt.type).html(opt.msg).hide().on('oMessageShow', opt.onShow).on('oMessageHide', opt.onHide),
			cbtn = $('<div class="close-button"/>');
		if(ctn.length === 0 ){
			ctn = $('<div/>').attr('id', opt.cid);
			$('body').append(ctn);
		}
		if(opt.from !== ''){
			msg.append('<p class="from">'+opt.from+'</p>');
		}
		msg.append(cbtn);
		ctn.prepend(msg);
		msg.fadeIn(300).trigger('oMessageShow', [opt.id]);
		cbtn.on('click', function(){
			msg.fadeOut(800, function(){ msg.trigger('oMessageHide', [opt.id]).remove(); });
		});
		setTimeout(function(){
			cbtn.click();
		},5000);
		return msg;
	},
	update: function(id,o) {
		$(id).html(o);
		return "";
	},
	chkAjax: function(){
		if($.oMessage.opt.ajax) $.oMessage.opt.ajax.abort();
		return !$.oMessage.opt.sjax;
	},
	load: function(){
		if($.oMessage.chkAjax()){
			$.oMessage.opt.ajax = $.post($.oMessage.opt.url, {'ids': $.oMessage.serialize()}, function(r){
				for(var i in r){
					$.oMessage.add(r[i]);
				}
			}, 'json');
		}
	},
	getNotice: function(){
		if($.oMessage.chkAjax()){
			// $.oMessage.opt.ajax = $.post($.oMessage.opt.url, {'ids': $.oMessage.serialize()}, function(r){
			// 	for(var i in r){
			// 		$.oMessage.add(r[i]);
			// 	}
			// }, 'json');
			$.ajax({
					    url: $.oMessage.opt.noticeUrl,
					    type: "post",
					    data: {'ids': $.oMessage.serialize()},
					    processData: false,
					    contentType: false,
					    success: function(r) {
					        var valueList = r.split(",");
							if(valueList[0]>0)
							{
								data = {id:1,msg:'You have '+valueList[0]+' new message.',from:1,'type':'notify'};
								$.oMessage.add(data);
							}
							$.oMessage.update('#messageNumber',valueList[0]);
							$.oMessage.update('#importsEmailNumber',valueList[1]);
							$.oMessage.update('#wmsEmailNumber',valueList[2]);
							$.oMessage.update('#taskNumber',valueList[3]);
					     },
					    error: function(e) {
					        console.log(e);
					    }
			});	
		}
	},
	serialize: function(){
		var o = [];
		$('#'+$.oMessage.opt.cid+'>div').each(function(){
			o.push($(this).data('id'));
		});
		return o;
	}
};

this.load = function(){
	//splitter click
	$('#splitter').on('click', function(){
		$('#leftpanel').toggle();
		$(this).toggleClass('splitRight');
		$(window).trigger('resize');
	});

	/*load menu*/
	$.ajax({
		url: app.config.baseURL + 'app/menu.app',
		dataType: 'html',
		success: function(req) {
			$('#menu').html(req);
			$('#menu ul.menusection').prepend('<div class="pin"></div>').not('.pinned').hide();
			$('#menu>ul>li').on('click', function(){
				var ul = $('>ul', this);
				if(ul.hasClass('pinned')) return ;
				if(ul.hasClass('open')){
					ul.stop().delay(200).slideUp();
					ul.removeClass('open');
				}else{
					ul.stop().slideDown();
					ul.addClass('open');
					$(this).siblings().find('ul.open').not('.pinned').stop().slideUp().removeClass('open');
				}
			});

			//menu options
			$(document).on('click', '#menu a.menuLink', function(evt, bg){
				if(evt.which == 2){
					evt.preventDefault();
					bg = true;
				}
				var a = this;
				var l = $(this).attr('href');
				this.tab = app.tabs.CreateTab({
					title: $(this).attr('title'),
					url: l,
					onClose: function(){
						a.tab = null;
						$(a).removeClass('hi');
					},
					onOpen: function(){
						app.menuHi($(a));
					},
					bg: bg || false
				});
				return false;
			});
			$('#menu li ul div.pin').on('click', function(){
				$(this).parent().toggleClass('pinned');
			});
		},
		error: function(req, errortype){ alert(errortype); }
	});

	//init tabs
	app.tabs = $("#rightpanel").on('afterCreate', function(){
		$(window).trigger('resize');
	}).on('afterLoad', function(e){
		app.ajaxifyForm(e.target);
	}).jqDynTabs({tabcontrol: $("#tabs"), tabcontent: $("#content"), position: "top", hometab: app.config.hometab, cookie: true});

	$("#tabs").sortable();
	
	if($("#titleforce").val()!="")
	{
		app.tabs.CreateTab({
			title: $("#titleforce").val(),
			url:  $("#menuforce").val(),
			bg: false
		});
	}

	//close all tabs
	$('#close_tabs').on('click', function(e){
		app.confirm('Are you sure to close all tabs?', function(){
			$('#tabs>li:first').trigger('click');
			$('#tabs .tabClose').trigger('click');
		});
	});
	
	//put username
	$('#username').html(app.session.name);

	//bind logout
	$('#logout').on('click', function(){
		app.logout();
		return false;
	});
	
	//jqm link
	$(document).on('click', 'a.jqm_link', function(){
		var p = {modal: true, tabid: app.tabs.curHiTab.attr('id'), url: $(this).attr('href')};
		if($(this).attr('id') == 'help'){
			p.param = { view:  app.tabs.curHiTab.data('url').substring(0,app.tabs.curHiTab.data('url').indexOf('?'))};
		}
		if($(this).data('win-class')) p.className = $(this).data('win-class');
		app.popupWindow(p);
		return false;
	});

	$(document).on('click', 'a.jqm2_link', function(){
		var p = {modal: true, tabid: app.tabs.curHiTab.attr('id'), url: $(this).attr('href')};
		if($(this).attr('id') == 'help'){
			p.param = { view:  app.tabs.curHiTab.data('url').substring(0,app.tabs.curHiTab.data('url').indexOf('?'))};
		}
		if($(this).data('win-class')) p.className = $(this).data('win-class');
		app.popupWindow2(p);
		return false;
	});
	
	//tab link
	$(document).on('click', 'a.tab_link', function(evt, bg){
		if(evt.which == 2){
			evt.preventDefault();
			bg = true;
		}
		var l = $(this).attr('href');
		app.tabs.CreateTab({
			title: $(this).attr('title'),
			url: l,
			bg: bg || false
		});
		return false;
	});

	$(document).on('mouseup', 'a.menuLink, a.tab_link, a.jqm_link', function(evt){
		if(evt.which == 2){
			evt.preventDefault();
			$(this).trigger('click', true);
			return false;
		}
	});
	
	//ajax link
	$(document).on('click', 'a.ajax_link', function(){
		var me = $(this);
		$.get(me.attr('href'), function(r){
			me.trigger('success', [r]);
		}, 'json');
		return false;
	});

	//finish loading
	$(window).load(function(){$('#sysloading').fadeOut(2000);});
	$(window).trigger('load');

	//session check
	$.oMessage.getNotice();
	$(window).everyTime(3e5, app.sessionchk);
	$(window).everyTime(6e4, $.oMessage.getNotice);

	$('#lf_capcha').everyTime(6e4, function(){
		if(app.session.valid) return;
		$('#lf_capcha').trigger('click');
	});
	
	//grid-view ENTER key for IE
	$(document).on('keypress', '.grid-view .filters input', function(e){
		var code = (e.keyCode ? e.keyCode : e.which);
		if(code == 13) $(this).blur();
	});
	
	$(document).on('keydown',function(e){
		var code = (e.keyCode ? e.keyCode : e.which);
		if (code == 27){
			$('.jqmWindow').jqmHide();
			if(app.appmsgs.length > 0){
				msg = app.appmsgs.pop();
				msg.find('.msgOk').trigger('click');
			}
		}
	});

	//language switcher
	$('select#lang-switch').change(function(){
		window.location = '?lang='+$(this).val();
	});
};

this.sessionchk = function(){
	$.get(app.config.baseURL + 'site/checkSession',
		function(r){
			if(parseInt(r) > app.config.timeout){
				$(window).focus();
				var seswar = app.confirm('Your session is expiring in <strong id="counter">60</strong> seconds!<br />Keep working?',
					function(){
						$.get(app.config.baseURL + 'app/live');
						seswar.stopTime();
					}
				);
				seswar.everyTime(1000,function(t){
					$("#counter",seswar).html((60 - t) + "");
					if(t >=60){
						seswar.stopTime();
						seswar.trigger('close');
						app.logout();
					}
				});
			}
		});
};

this.showLogin = function(){
	$('#login').jqm({modal: true, onShow: function(m){
		$('#lf_capcha').trigger('click');
		m.w.fadeIn(1000);
	}}).jqmShow();
};

this.logout = function(){
	$.ajax({
		url: app.config.baseURL + 'site/logout',
		success: function(r){
			app.tabs.resetCookie();
			$(window).stopTime();
			window.location.href = window.location.href;
		},
		error: function(r, e){ alert(e); }
	});
};

this.menuHi = function(c){
	if(app.himenu !== null){
		app.himenu.removeClass('hi');
	}
	c.addClass('hi');
	app.himenu = c;
};

this.getSelectedRow = function(table, msg) {
	var s = table.tablesorterSelectedRows();
	if (s.length === 0) {
		if (msg) app.alert(msg, false, null);
		return false;
	}
	return s[0];
};

this.formatUrl = function(url, param) {
	if (typeof param != 'object') return url;
	var arr = [];
	for(var p in param) arr.push(p+'='+escape(param[p]));
	url += (url.indexOf('?') == -1)? '?' : '&';
	return url + arr.join('&');
};

this.tabOpenSelected = function(conf, area) {
	var table = $(conf.tableID, area);
	var row = app.getSelectedRow(table, conf.message);
	if (conf.url.indexOf('?') == -1) { conf.url += '?'; }
	var l = conf.url+'&title='+tdval+'&'+conf.idname+'='+ id;
	if (! row) return;
	var id = row.attr('rid');
	if (app.tabs.openTabs[l]) {
		$(app.tabs.openTabs[l]).trigger('click');
	} else {
		var tdval = conf.title || row.children('td')[conf.tdIDX].innerHTML;
		uet = app.tabs.CreateTab({
			title: tdval,
			url: l
		});
	}
};

this.popupWindow = function(args) {
	var wsize = args.className || '';
	var jqmWindow = $('<div id="jqmw_'+args.tabid+'" class="jqmWindow '+wsize+'"><div class="popCancel"></div><div class="jqmw_cont"></div></div>').appendTo('body').data('opener', app.tabs.curHiTab);
	args.param = $.extend({ tabid: args.tabid }, args.param || {});
	jqmWindow.jqm({
		modal: args.modal,
		ajax: app.formatUrl(args.url, args.param),
		target: '.jqmw_cont',
		onLoad: function(h) {
			$(window).trigger('resize');
			$('.popOK', jqmWindow).on('click', function(){
				if (args.clickOK) args.clickOK(jqmWindow);
				jqmWindow.jqmHide();
				return false;
			});
			$('.popCancel', jqmWindow).on('click', function(){
				jqmWindow.jqmHide();
				return false;
			});
			app.ajaxifyForm(jqmWindow);
		},
		onHide: function(h) {
			h.w.trigger('close').remove();
			h.o.remove();
		}
	}).jqmShow();
};

this.popupWindow2 = function(args) {
	var wsize = args.className || '';
	var jqmWindow = $('<div id="jqmw2_'+args.tabid+'" class="jqmWindow '+wsize+'"><div class="popCancel"></div><div class="jqmw_cont"></div></div>').appendTo('body').data('opener', app.tabs.curHiTab);
	args.param = $.extend({ tabid: args.tabid }, args.param || {});
	jqmWindow.jqm({
		modal: args.modal,
		ajax: app.formatUrl(args.url, args.param),
		target: '.jqmw_cont',
		onLoad: function(h) {
			$(window).trigger('resize');
			$('.popOK', jqmWindow).on('click', function(){
				if (args.clickOK) args.clickOK(jqmWindow);
				jqmWindow.jqmHide();
				return false;
			});
			$('.popCancel', jqmWindow).on('click', function(){
				jqmWindow.jqmHide();
				return false;
			});
			app.ajaxifyForm(jqmWindow);
		},
		onHide: function(h) {
			h.w.trigger('close').remove();
			h.o.remove();
		}
	}).jqmShow();
};

this.zoom = function(io){
	var az = [0.6,0.8,1,1.2,1.4];
	var zi = $('body').data('zi') + io;
	zi = zi<0? 0 : zi;
	zi = zi>4? 4 : zi;
	$('body').css({'transform-origin': '0 0', 'transform' : 'scale('+az[zi]+')'}).data({'zi': zi, 'scale': az[zi]});
	$(window).trigger('resize');
};

$('body').data({'zi': 2, 'scale' : 1});
$('#zoom_in').on('click', function(){ app.zoom(1); return false; });
$('#zoom_out').on('click', function(){ app.zoom(-1); return false; });

this.ajaxifyForm = function(container){//ajax form
	$("label.required", container).each(function(){
		$(this).next('input,select,textarea').addClass('required');
	});
	$("form", container).not('.ifrm-form').each(function(){
		$(this).ajaxForm({
			success: function(r,s,x,f) {
				if(typeof f.data('custom_success') == 'function') return f.data('custom_success')(r);
				if(r.done === true){
					if(f.data('reset')) f.resetForm();
					app.notice(r.msg, 5000);
					f.trigger('success', [r]);
				}else{
					app.alert(r.msg, false);
					f.trigger('error', [r]);
				}
				$('input[type=submit]', f).attr('disabled', false);
			},
			dataType: 'json',
			beforeSerialize: function(f, o){
				f.trigger('beforeSerialize');
				try{ //ckeditor hack
					for(var instanceName in CKEDITOR.instances)
						CKEDITOR.instances[instanceName].updateElement();
				}catch(e){}
				return true;
			},
			beforeSubmit: function(a,f,o){
				if(typeof f.data('beforeSubmit') == 'function'){
					var r = f.data('beforeSubmit')(f);
					if(r === false) return false;
				}
				var is_valid = f.valid();
				if(is_valid){
					if(!f.data('no_btn_lock')) $('input[type=submit]', f).attr('disabled', true);
					return true;
				}else{
					return false;
				}
			}
		}).validate({
			errorPlacement: function(err, el) { return; }
		});
	});
};

//date/time pickers
$('body').on('focus', 'input.date_input', function(){
	if($(this).data('init') === 1) return;
	$(this).datepicker({dateFormat: 'yy-mm-dd'}).data('init', 1);
}).on('focus', 'input.datetime_input', function(){
	if($(this).data('init') === 1) return;
	$(this).datetimepicker({
		dateFormat: 'yy-mm-dd',
		timeFormat: 'HH:mm:ss',
		stepMinute: 5
	}).data('init', 1);
}).on('focus', 'input.time_input', function() {
	if ($(this).data('init') === 1) return;
	$(this).timepicker({
		timeFormat: 'HH:mm:ss',
	});
});

//ajaxCache
$.ajaxSetup({
	cache: true,
	beforeSend: function(xhr, o) {
		if(o.dataType == 'script' && $.inArray(o.url, app.assets) > -1) return false;
		app.assets.push(o.url);
	}
});

$(this.init);
$(window).on('beforeunload', function(){
	return 'Are you sure you want to leave?';
});
return this;
};
})(jQuery);
