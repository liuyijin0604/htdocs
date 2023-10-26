(function($){
$.fn.jqDynTabs = function( p ){
p = $.extend({
	lowTabStyle: 'lowTab',
	hiTabStyle: 'hiTab',
	rememberClosed: 5,
	hometab: null
}, p || {});

if (p.tabcontrol === null && p.tabcontrol.length === 0 && p.tabcontent === null && p.tabcontent.length === 0){
  return;
} else {
	this.tabContainer = p.tabcontrol;
	this.panelContainer = p.tabcontent;
}
this.Name = 'jqDynTabs' + Math.round(100*Math.random()); 
this.tabnumber = 0;
this.curHiTab = null;
this.openTabs = {};
this.closedTabs = [];
var that = this;

this.saveCookie = function(){
	var stabs = [];
	that.openTabs = [];
	this.tabContainer.find('>li').each(function(){
		var t = $(this).data();
		if(t.hometab) return;
		stabs.push({url: t.surl, title: t.title, bg: $(this).hasClass(p.lowTabStyle)});
		that.openTabs[t.surl] = $(this);
	});
	if (typeof(Storage) !== "undefined") {
		localStorage.setItem('dynTabs', JSON.stringify(stabs));
	} else {
	 	$.cookie('dynTabs', JSON.stringify(stabs));
	}
};

this.resetCookie = function(){
	localStorage.setItem('dynTabs', null);
	$.cookie('dynTabs', null);
};

this.CreateTab = function(t){//title, closable, url, content, hometab
	t.surl = t.url;
	var ptab = this.openTabs[t.surl] || false;
	
	if(ptab){
		if(t.bg) ptab.trigger('blink');
		else ptab.trigger('click');
		return ptab;
	}
	
	if(t.url.indexOf('?') == -1) t.url += '?';
	t.url += "&tabid=" + this.Name + 'Tab' + this.tabnumber;
	if (t.closable === null || typeof t.closable != "boolean") t.closable = true;
	
	var panel = $('<div id="'+this.Name+'Panel'+this.tabnumber+'" style="height: 100%;display: none;"></div>');
	var tab = ptab || $('<li id="'+this.Name+'Tab'+this.tabnumber+'" class="'+p.lowTabStyle+'"></li>').data(t);
	
	//on events
	if(typeof t.beforeLoad == 'function') panel.on('beforeLoad', t.beforeLoad);
	if(typeof t.afterLoad == 'function') panel.on('afterLoad', t.afterLoad);
	if(typeof t.onOpen == 'function') tab.on('onOpen', t.onOpen);
	if(typeof t.onClose == 'function') tab.on('onClose', t.onClose);
	if(ptab) return ptab;

	this.panelContainer.append(panel);
	
	tab.on('load', function(){
		var t = tab.data();
		if(t.url){
			panel.trigger('beforeLoad');
			$.ajax({
				url: t.url,
				dataType: "html",
				success: function(req){
					panel.html(req);
					if(p.cookie) that.saveCookie();
					panel.trigger('afterLoad');
				},
				error: function(req, errortype){ alert(req||errortype); }
			});
		} else if(t.content) $(panel).html(t.content);
	}).trigger('load');

	tab.data('panel', panel);
	if(t.closable){
		tab.html('<div class="tabClose"></div><div class="tabName" title="'+t.title+'">' + t.title + '</div>');
		$(".tabClose", tab).click(function(){tab.trigger('close');});
		this.tabContainer.append(tab);
	} else {
		tab.html('<div class="tabName noclose" title="'+t.title+'">' + t.title + '</div>');
		this.tabContainer.prepend(tab);
	}

	tab.on('click', function(){ that.TabOpen(tab); }).on('close', function(){
		var t = tab.data();
		if(typeof t.beforeClose == "function" && t.beforeClose() === false) return;
		that.TabClose(tab);
	}).on('doClose', function(){ that.TabClose(tab);}).on('blink', function(){
		var i = 0;
		var blink = function(d){
			setTimeout(function(){
			if(d&1){
				tab.addClass('hover');
			}else{
				tab.removeClass('hover');
			}}, 200*d);
		};

		while(i++ < 8){
			blink(i);
		}
	});

	this.tabnumber++;
	this.trigger('afterCreate');
	if(!t.bg || (t.hometab && this.curHiTab === null)) tab.trigger('click');
	return tab;
};

this.TabOpen = function(tab){
	if(tab === null || this.curHiTab == tab) return;
	if(this.curHiTab !== null){
		this.curHiTab.addClass(p.lowTabStyle).removeClass(p.hiTabStyle);
		this.curHiTab.data('panel').hide();
	}
	this.curHiTab = tab;
	this.curHiTab.removeClass(p.lowTabStyle).addClass(p.hiTabStyle);
	this.curHiTab.data('panel').show();
	this.curHiTab.trigger('onOpen');
	this.trigger('afterOpen');
	if(p.cookie) this.saveCookie();
};

this.TabClose = function(tab){
	if (tab === null) return;
	if (tab == this.curHiTab){
		if(tab.next().length === 0)
			tab.prev().trigger('click');
		else
			tab.next().trigger('click');
	}
	tab.hide();
	that.AddClose(tab);
	tab.trigger('onClose');
	tab.data('panel').remove();
	tab.remove();
	if(p.cookie) this.saveCookie();
};

this.AddClose = function(tab){
	that.closedTabs.push({url: tab.data('surl'), title: tab.data('title')});
	if(that.closedTabs.length > p.rememberClosed)
		that.closedTabs.shift();
};

if(p.cookie){ //open tabs in cookie
	var savedTabs = (typeof(Storage) !== "undefined")? localStorage.dynTabs : $.cookie('dynTabs');
	var stabs = savedTabs == null? [] : ($.parseJSON(savedTabs) || []);
	for(var i in stabs){
		this.CreateTab({
			title: stabs[i].title,
			url: stabs[i].url,
			bg: stabs[i].bg
		});
	}
}

if(p.hometab !== null){ //home tab
	this.CreateTab({
		title: p.hometab.title,
		closable: false,
		url: p.hometab.url,
		bg: true,
		hometab: true
	});
}

this.tabContainer.mousedown(function(evt){
	if(evt.which == 2){
		if(evt.target == this){
			t = that.closedTabs.pop();
			if(t){
				that.CreateTab({
					title: t.title,
					url: t.url
				});
			}
		}else{
			$(evt.target).not('.noclose').parents('li').trigger('close');
		}
		return false;
	}
});

return this;
};
})(jQuery);
