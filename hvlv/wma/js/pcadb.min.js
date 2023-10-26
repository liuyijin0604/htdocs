//jQuery Form Plugin 3.51.0 https://github.com/malsup/form
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery"],e):e("undefined"!=typeof jQuery?jQuery:window.Zepto)}(function(e){"use strict";function t(t){var r=t.data;t.isDefaultPrevented()||(t.preventDefault(),e(t.target).ajaxSubmit(r))}function r(t){var r=t.target,a=e(r);if(!a.is("[type=submit],[type=image]")){var n=a.closest("[type=submit]");if(0===n.length)return;r=n[0]}var i=this;if(i.clk=r,"image"==r.type)if(void 0!==t.offsetX)i.clk_x=t.offsetX,i.clk_y=t.offsetY;else if("function"==typeof e.fn.offset){var o=a.offset();i.clk_x=t.pageX-o.left,i.clk_y=t.pageY-o.top}else i.clk_x=t.pageX-r.offsetLeft,i.clk_y=t.pageY-r.offsetTop;setTimeout(function(){i.clk=i.clk_x=i.clk_y=null},100)}function a(){if(e.fn.ajaxSubmit.debug){var t="[jquery.form] "+Array.prototype.join.call(arguments,"");window.console&&window.console.log?window.console.log(t):window.opera&&window.opera.postError&&window.opera.postError(t)}}var n={};n.fileapi=void 0!==e("<input type='file'/>").get(0).files,n.formdata=void 0!==window.FormData;var i=!!e.fn.prop;e.fn.attr2=function(){if(!i)return this.attr.apply(this,arguments);var e=this.prop.apply(this,arguments);return e&&e.jquery||"string"==typeof e?e:this.attr.apply(this,arguments)},e.fn.ajaxSubmit=function(t){function r(r){var a,n,i=e.param(r,t.traditional).split("&"),o=i.length,s=[];for(a=0;o>a;a++)i[a]=i[a].replace(/\+/g," "),n=i[a].split("="),s.push([decodeURIComponent(n[0]),decodeURIComponent(n[1])]);return s}function o(a){for(var n=new FormData,i=0;i<a.length;i++)n.append(a[i].name,a[i].value);if(t.extraData){var o=r(t.extraData);for(i=0;i<o.length;i++)o[i]&&n.append(o[i][0],o[i][1])}t.data=null;var s=e.extend(!0,{},e.ajaxSettings,t,{contentType:!1,processData:!1,cache:!1,type:u||"POST"});t.uploadProgress&&(s.xhr=function(){var r=e.ajaxSettings.xhr();return r.upload&&r.upload.addEventListener("progress",function(e){var r=0,a=e.loaded||e.position,n=e.total;e.lengthComputable&&(r=Math.ceil(a/n*100)),t.uploadProgress(e,a,n,r)},!1),r}),s.data=null;var c=s.beforeSend;return s.beforeSend=function(e,r){r.data=t.formData?t.formData:n,c&&c.call(this,e,r)},e.ajax(s)}function s(r){function n(e){var t=null;try{e.contentWindow&&(t=e.contentWindow.document)}catch(r){a("cannot get iframe.contentWindow document: "+r)}if(t)return t;try{t=e.contentDocument?e.contentDocument:e.document}catch(r){a("cannot get iframe.contentDocument: "+r),t=e.document}return t}function o(){function t(){try{var e=n(g).readyState;a("state = "+e),e&&"uninitialized"==e.toLowerCase()&&setTimeout(t,50)}catch(r){a("Server abort: ",r," (",r.name,")"),s(k),j&&clearTimeout(j),j=void 0}}var r=f.attr2("target"),i=f.attr2("action"),o="multipart/form-data",c=f.attr("enctype")||f.attr("encoding")||o;w.setAttribute("target",p),(!u||/post/i.test(u))&&w.setAttribute("method","POST"),i!=m.url&&w.setAttribute("action",m.url),m.skipEncodingOverride||u&&!/post/i.test(u)||f.attr({encoding:"multipart/form-data",enctype:"multipart/form-data"}),m.timeout&&(j=setTimeout(function(){T=!0,s(D)},m.timeout));var l=[];try{if(m.extraData)for(var d in m.extraData)m.extraData.hasOwnProperty(d)&&l.push(e.isPlainObject(m.extraData[d])&&m.extraData[d].hasOwnProperty("name")&&m.extraData[d].hasOwnProperty("value")?e('<input type="hidden" name="'+m.extraData[d].name+'">').val(m.extraData[d].value).appendTo(w)[0]:e('<input type="hidden" name="'+d+'">').val(m.extraData[d]).appendTo(w)[0]);m.iframeTarget||v.appendTo("body"),g.attachEvent?g.attachEvent("onload",s):g.addEventListener("load",s,!1),setTimeout(t,15);try{w.submit()}catch(h){var x=document.createElement("form").submit;x.apply(w)}}finally{w.setAttribute("action",i),w.setAttribute("enctype",c),r?w.setAttribute("target",r):f.removeAttr("target"),e(l).remove()}}function s(t){if(!x.aborted&&!F){if(M=n(g),M||(a("cannot access response document"),t=k),t===D&&x)return x.abort("timeout"),void S.reject(x,"timeout");if(t==k&&x)return x.abort("server abort"),void S.reject(x,"error","server abort");if(M&&M.location.href!=m.iframeSrc||T){g.detachEvent?g.detachEvent("onload",s):g.removeEventListener("load",s,!1);var r,i="success";try{if(T)throw"timeout";var o="xml"==m.dataType||M.XMLDocument||e.isXMLDoc(M);if(a("isXml="+o),!o&&window.opera&&(null===M.body||!M.body.innerHTML)&&--O)return a("requeing onLoad callback, DOM not available"),void setTimeout(s,250);var u=M.body?M.body:M.documentElement;x.responseText=u?u.innerHTML:null,x.responseXML=M.XMLDocument?M.XMLDocument:M,o&&(m.dataType="xml"),x.getResponseHeader=function(e){var t={"content-type":m.dataType};return t[e.toLowerCase()]},u&&(x.status=Number(u.getAttribute("status"))||x.status,x.statusText=u.getAttribute("statusText")||x.statusText);var c=(m.dataType||"").toLowerCase(),l=/(json|script|text)/.test(c);if(l||m.textarea){var f=M.getElementsByTagName("textarea")[0];if(f)x.responseText=f.value,x.status=Number(f.getAttribute("status"))||x.status,x.statusText=f.getAttribute("statusText")||x.statusText;else if(l){var p=M.getElementsByTagName("pre")[0],h=M.getElementsByTagName("body")[0];p?x.responseText=p.textContent?p.textContent:p.innerText:h&&(x.responseText=h.textContent?h.textContent:h.innerText)}}else"xml"==c&&!x.responseXML&&x.responseText&&(x.responseXML=X(x.responseText));try{E=_(x,c,m)}catch(y){i="parsererror",x.error=r=y||i}}catch(y){a("error caught: ",y),i="error",x.error=r=y||i}x.aborted&&(a("upload aborted"),i=null),x.status&&(i=x.status>=200&&x.status<300||304===x.status?"success":"error"),"success"===i?(m.success&&m.success.call(m.context,E,"success",x),S.resolve(x.responseText,"success",x),d&&e.event.trigger("ajaxSuccess",[x,m])):i&&(void 0===r&&(r=x.statusText),m.error&&m.error.call(m.context,x,i,r),S.reject(x,"error",r),d&&e.event.trigger("ajaxError",[x,m,r])),d&&e.event.trigger("ajaxComplete",[x,m]),d&&!--e.active&&e.event.trigger("ajaxStop"),m.complete&&m.complete.call(m.context,x,i),F=!0,m.timeout&&clearTimeout(j),setTimeout(function(){m.iframeTarget?v.attr("src",m.iframeSrc):v.remove(),x.responseXML=null},100)}}}var c,l,m,d,p,v,g,x,y,b,T,j,w=f[0],S=e.Deferred();if(S.abort=function(e){x.abort(e)},r)for(l=0;l<h.length;l++)c=e(h[l]),i?c.prop("disabled",!1):c.removeAttr("disabled");if(m=e.extend(!0,{},e.ajaxSettings,t),m.context=m.context||m,p="jqFormIO"+(new Date).getTime(),m.iframeTarget?(v=e(m.iframeTarget),b=v.attr2("name"),b?p=b:v.attr2("name",p)):(v=e('<iframe name="'+p+'" src="'+m.iframeSrc+'" />'),v.css({position:"absolute",top:"-1000px",left:"-1000px"})),g=v[0],x={aborted:0,responseText:null,responseXML:null,status:0,statusText:"n/a",getAllResponseHeaders:function(){},getResponseHeader:function(){},setRequestHeader:function(){},abort:function(t){var r="timeout"===t?"timeout":"aborted";a("aborting upload... "+r),this.aborted=1;try{g.contentWindow.document.execCommand&&g.contentWindow.document.execCommand("Stop")}catch(n){}v.attr("src",m.iframeSrc),x.error=r,m.error&&m.error.call(m.context,x,r,t),d&&e.event.trigger("ajaxError",[x,m,r]),m.complete&&m.complete.call(m.context,x,r)}},d=m.global,d&&0===e.active++&&e.event.trigger("ajaxStart"),d&&e.event.trigger("ajaxSend",[x,m]),m.beforeSend&&m.beforeSend.call(m.context,x,m)===!1)return m.global&&e.active--,S.reject(),S;if(x.aborted)return S.reject(),S;y=w.clk,y&&(b=y.name,b&&!y.disabled&&(m.extraData=m.extraData||{},m.extraData[b]=y.value,"image"==y.type&&(m.extraData[b+".x"]=w.clk_x,m.extraData[b+".y"]=w.clk_y)));var D=1,k=2,A=e("meta[name=csrf-token]").attr("content"),L=e("meta[name=csrf-param]").attr("content");L&&A&&(m.extraData=m.extraData||{},m.extraData[L]=A),m.forceSync?o():setTimeout(o,10);var E,M,F,O=50,X=e.parseXML||function(e,t){return window.ActiveXObject?(t=new ActiveXObject("Microsoft.XMLDOM"),t.async="false",t.loadXML(e)):t=(new DOMParser).parseFromString(e,"text/xml"),t&&t.documentElement&&"parsererror"!=t.documentElement.nodeName?t:null},C=e.parseJSON||function(e){return window.eval("("+e+")")},_=function(t,r,a){var n=t.getResponseHeader("content-type")||"",i="xml"===r||!r&&n.indexOf("xml")>=0,o=i?t.responseXML:t.responseText;return i&&"parsererror"===o.documentElement.nodeName&&e.error&&e.error("parsererror"),a&&a.dataFilter&&(o=a.dataFilter(o,r)),"string"==typeof o&&("json"===r||!r&&n.indexOf("json")>=0?o=C(o):("script"===r||!r&&n.indexOf("javascript")>=0)&&e.globalEval(o)),o};return S}if(!this.length)return a("ajaxSubmit: skipping submit process - no element selected"),this;var u,c,l,f=this;"function"==typeof t?t={success:t}:void 0===t&&(t={}),u=t.type||this.attr2("method"),c=t.url||this.attr2("action"),l="string"==typeof c?e.trim(c):"",l=l||window.location.href||"",l&&(l=(l.match(/^([^#]+)/)||[])[1]),t=e.extend(!0,{url:l,success:e.ajaxSettings.success,type:u||e.ajaxSettings.type,iframeSrc:/^https/i.test(window.location.href||"")?"javascript:false":"about:blank"},t);var m={};if(this.trigger("form-pre-serialize",[this,t,m]),m.veto)return a("ajaxSubmit: submit vetoed via form-pre-serialize trigger"),this;if(t.beforeSerialize&&t.beforeSerialize(this,t)===!1)return a("ajaxSubmit: submit aborted via beforeSerialize callback"),this;var d=t.traditional;void 0===d&&(d=e.ajaxSettings.traditional);var p,h=[],v=this.formToArray(t.semantic,h);if(t.data&&(t.extraData=t.data,p=e.param(t.data,d)),t.beforeSubmit&&t.beforeSubmit(v,this,t)===!1)return a("ajaxSubmit: submit aborted via beforeSubmit callback"),this;if(this.trigger("form-submit-validate",[v,this,t,m]),m.veto)return a("ajaxSubmit: submit vetoed via form-submit-validate trigger"),this;var g=e.param(v,d);p&&(g=g?g+"&"+p:p),"GET"==t.type.toUpperCase()?(t.url+=(t.url.indexOf("?")>=0?"&":"?")+g,t.data=null):t.data=g;var x=[];if(t.resetForm&&x.push(function(){f.resetForm()}),t.clearForm&&x.push(function(){f.clearForm(t.includeHidden)}),!t.dataType&&t.target){var y=t.success||function(){};x.push(function(r){var a=t.replaceTarget?"replaceWith":"html";e(t.target)[a](r).each(y,arguments)})}else t.success&&x.push(t.success);if(t.success=function(e,r,a){for(var n=t.context||this,i=0,o=x.length;o>i;i++)x[i].apply(n,[e,r,a||f,f])},t.error){var b=t.error;t.error=function(e,r,a){var n=t.context||this;b.apply(n,[e,r,a,f])}}if(t.complete){var T=t.complete;t.complete=function(e,r){var a=t.context||this;T.apply(a,[e,r,f])}}var j=e("input[type=file]:enabled",this).filter(function(){return""!==e(this).val()}),w=j.length>0,S="multipart/form-data",D=f.attr("enctype")==S||f.attr("encoding")==S,k=n.fileapi&&n.formdata;a("fileAPI :"+k);var A,L=(w||D)&&!k;t.iframe!==!1&&(t.iframe||L)?t.closeKeepAlive?e.get(t.closeKeepAlive,function(){A=s(v)}):A=s(v):A=(w||D)&&k?o(v):e.ajax(t),f.removeData("jqxhr").data("jqxhr",A);for(var E=0;E<h.length;E++)h[E]=null;return this.trigger("form-submit-notify",[this,t]),this},e.fn.ajaxForm=function(n){if(n=n||{},n.delegation=n.delegation&&e.isFunction(e.fn.on),!n.delegation&&0===this.length){var i={s:this.selector,c:this.context};return!e.isReady&&i.s?(a("DOM not ready, queuing ajaxForm"),e(function(){e(i.s,i.c).ajaxForm(n)}),this):(a("terminating; zero elements found by selector"+(e.isReady?"":" (DOM not ready)")),this)}return n.delegation?(e(document).off("submit.form-plugin",this.selector,t).off("click.form-plugin",this.selector,r).on("submit.form-plugin",this.selector,n,t).on("click.form-plugin",this.selector,n,r),this):this.ajaxFormUnbind().bind("submit.form-plugin",n,t).bind("click.form-plugin",n,r)},e.fn.ajaxFormUnbind=function(){return this.unbind("submit.form-plugin click.form-plugin")},e.fn.formToArray=function(t,r){var a=[];if(0===this.length)return a;var i,o=this[0],s=this.attr("id"),u=t?o.getElementsByTagName("*"):o.elements;if(u&&!/MSIE [678]/.test(navigator.userAgent)&&(u=e(u).get()),s&&(i=e(':input[form="'+s+'"]').get(),i.length&&(u=(u||[]).concat(i))),!u||!u.length)return a;var c,l,f,m,d,p,h;for(c=0,p=u.length;p>c;c++)if(d=u[c],f=d.name,f&&!d.disabled)if(t&&o.clk&&"image"==d.type)o.clk==d&&(a.push({name:f,value:e(d).val(),type:d.type}),a.push({name:f+".x",value:o.clk_x},{name:f+".y",value:o.clk_y}));else if(m=e.fieldValue(d,!0),m&&m.constructor==Array)for(r&&r.push(d),l=0,h=m.length;h>l;l++)a.push({name:f,value:m[l]});else if(n.fileapi&&"file"==d.type){r&&r.push(d);var v=d.files;if(v.length)for(l=0;l<v.length;l++)a.push({name:f,value:v[l],type:d.type});else a.push({name:f,value:"",type:d.type})}else null!==m&&"undefined"!=typeof m&&(r&&r.push(d),a.push({name:f,value:m,type:d.type,required:d.required}));if(!t&&o.clk){var g=e(o.clk),x=g[0];f=x.name,f&&!x.disabled&&"image"==x.type&&(a.push({name:f,value:g.val()}),a.push({name:f+".x",value:o.clk_x},{name:f+".y",value:o.clk_y}))}return a},e.fn.formSerialize=function(t){return e.param(this.formToArray(t))},e.fn.fieldSerialize=function(t){var r=[];return this.each(function(){var a=this.name;if(a){var n=e.fieldValue(this,t);if(n&&n.constructor==Array)for(var i=0,o=n.length;o>i;i++)r.push({name:a,value:n[i]});else null!==n&&"undefined"!=typeof n&&r.push({name:this.name,value:n})}}),e.param(r)},e.fn.fieldValue=function(t){for(var r=[],a=0,n=this.length;n>a;a++){var i=this[a],o=e.fieldValue(i,t);null===o||"undefined"==typeof o||o.constructor==Array&&!o.length||(o.constructor==Array?e.merge(r,o):r.push(o))}return r},e.fieldValue=function(t,r){var a=t.name,n=t.type,i=t.tagName.toLowerCase();if(void 0===r&&(r=!0),r&&(!a||t.disabled||"reset"==n||"button"==n||("checkbox"==n||"radio"==n)&&!t.checked||("submit"==n||"image"==n)&&t.form&&t.form.clk!=t||"select"==i&&-1==t.selectedIndex))return null;if("select"==i){var o=t.selectedIndex;if(0>o)return null;for(var s=[],u=t.options,c="select-one"==n,l=c?o+1:u.length,f=c?o:0;l>f;f++){var m=u[f];if(m.selected){var d=m.value;if(d||(d=m.attributes&&m.attributes.value&&!m.attributes.value.specified?m.text:m.value),c)return d;s.push(d)}}return s}return e(t).val()},e.fn.clearForm=function(t){return this.each(function(){e("input,select,textarea",this).clearFields(t)})},e.fn.clearFields=e.fn.clearInputs=function(t){var r=/^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i;return this.each(function(){var a=this.type,n=this.tagName.toLowerCase();r.test(a)||"textarea"==n?this.value="":"checkbox"==a||"radio"==a?this.checked=!1:"select"==n?this.selectedIndex=-1:"file"==a?/MSIE/.test(navigator.userAgent)?e(this).replaceWith(e(this).clone(!0)):e(this).val(""):t&&(t===!0&&/hidden/.test(a)||"string"==typeof t&&e(this).is(t))&&(this.value="")})},e.fn.resetForm=function(){return this.each(function(){("function"==typeof this.reset||"object"==typeof this.reset&&!this.reset.nodeType)&&this.reset()})},e.fn.enable=function(e){return void 0===e&&(e=!0),this.each(function(){this.disabled=!e})},e.fn.selected=function(t){return void 0===t&&(t=!0),this.each(function(){var r=this.type;if("checkbox"==r||"radio"==r)this.checked=t;else if("option"==this.tagName.toLowerCase()){var a=e(this).parent("select");t&&a[0]&&"select-one"==a[0].type&&a.find("option").selected(!1),this.selected=t}})},e.fn.ajaxSubmit.debug=!1});

//doubletap
!function(a){a.event.special.doubletap={bindType:"touchend",delegateType:"touchend",handle:function(a){var b=a.handleObj,c=jQuery.data(a.target),d=(new Date).getTime(),e=c.lastTouch?d-c.lastTouch:0,f=null==f?300:f;e<f&&e>30?(c.lastTouch=null,a.type=b.origType,["clientX","clientY","pageX","pageY"].forEach(function(b){a[b]=a.originalEvent.changedTouches[0][b]}),b.handler.apply(this,arguments)):c.lastTouch=d}}}(jQuery);

/**
 * bootstrap-notify.js v1.0
 **/
 !function(t){var s=function(s,o){if(this.$element=t(s),this.$note=t('<div class="alert"></div>'),this.options=t.extend(!0,{},t.fn.notify.defaults,o),this.options.transition?"fade"==this.options.transition?this.$note.addClass("in").addClass(this.options.transition):this.$note.addClass(this.options.transition):this.$note.addClass("fade").addClass("in"),this.$note.addClass(this.options.type?"alert-"+this.options.type:"alert-success"),this.options.message||""===this.$element.data("message")?"object"==typeof this.options.message?this.options.message.html?this.$note.html(this.options.message.html):this.options.message.text&&this.$note.text(this.options.message.text):this.$note.html(this.options.message):this.$note.html(this.$element.data("message")),this.options.closable){var i=t('<a class="close pull-right" href="#">&times;</a>');t(i).on("click",t.proxy(e,this)),this.$note.prepend(i)}return this},e=function(){return this.options.onClose(),t(this.$note).remove(),this.options.onClosed(),!1};s.prototype.show=function(){this.options.fadeOut.enabled&&this.$note.delay(this.options.fadeOut.delay||5e3).fadeOut("slow",t.proxy(e,this)),this.$element.append(this.$note)},s.prototype.hide=function(){this.options.fadeOut.enabled?this.$note.delay(this.options.fadeOut.delay||5e3).fadeOut("slow",t.proxy(e,this)):e.call(this)},t.fn.notify=function(t){return new s(this,t)},t.fn.notify.defaults={type:"success",closable:!0,transition:"fade",fadeOut:{enabled:!0,delay:3e3},message:null,onClose:function(){},onClosed:function(){}}}(window.jQuery);

//rhaboo
!function a(b,c,d){function e(g,h){if(!c[g]){if(!b[g]){var i="function"==typeof require&&require;if(!h&&i)return i(g,!0);if(f)return f(g,!0);var j=new Error("Cannot find module '"+g+"'");throw j.code="MODULE_NOT_FOUND",j}var k=c[g]={exports:{}};b[g][0].call(k.exports,function(a){var c=b[g][1][a];return e(c?c:a)},k,k.exports,a,b,c,d)}return c[g].exports}for(var f="function"==typeof require&&require,g=0;g<d.length;g++)e(d[g]);return e}({1:[function(a,b){"use strict";function c(a,b){var c=a.split(b);return 1==c.length&&void 0==c[0]&&delete c[0],c}var d=function(a){return null===a?"null":typeof a},e=function(a){return a},f=function(a){return function(){return a}},g=function(a){return function(b){return a===b}},h=function(a){return function(b){return b.map(a)}},i=function(a){return function(b){return void 0!==a[1]?[a[0],a[1](b)]:[a[0]]}},j=function(a){return function(b){return function(c){for(var d=[],e=0;e<b.length&&e<c.length;e++)d[e]=b[e](a)(c[e]);return d}}},k=function(){return function(a){return a.toString()}},l=function(a){return function(b){return a?b.toString():Number(b)}},m=function(a){return function(b){return a?b?"t":"f":"t"===b}},n=function(a){return function(){return a?"":null}},o=function(a){return function(){return a?"":void 0}},p=function(a){return function(b){return function(c){return function(d){return c?b(c)(a(c)(d)):a(c)(b(c)(d))}}}},q=function(a){return function(b){for(var c=[],d=0,d=0;d<a.length&&b.length;b=b.substring(a[d++]))c.push(b.substring(0,a[d]));return c.push(b),c}},r=function(a){return function(b){return function(c){return function(d){return c?j(c)(b)(d).join(""):j(c)(b)(q(a)(d))}}}},s=function(a){return function(b){return function(d){return function(e){return d?j(d)(b)(e).join(a):j(d)(b)(c(e,a))}}}},t=function(a,b){return function(c){return function(d){return function(e){return d?c(d)(e).replace(RegExp(a,"g"),a+b):c(d)(e.replace(RegExp(a+b,"g"),a))}}}},u=function(a,b){return function(c){return function(d){return function(e){return s(d?a:RegExp(a+"(?!"+b+")","g"))(h(t(a,b))(c))(d)(e)}}}},v=u(";",":");b.exports={typeOf:d,id:e,konst:f,eq:g,map:h,runSnd:i,string_pp:k,number_pp:l,boolean_pp:m,null_pp:n,undefined_pp:o,thru:j,chop:q,fixedWidth:r,sepBy:s,escape:t,sepByEsc:u,tuple:v,pipe:p}},{}],2:[function(a,b){var c=a("./core"),d=d||{pop:Array.prototype.pop,push:Array.prototype.push,shift:Array.prototype.shift,unshift:Array.prototype.unshift,splice:Array.prototype.splice,reverse:Array.prototype.reverse,sort:Array.prototype.sort,fill:Array.prototype.fill},e=function(a){return function(){var b,e,f=void 0;this._rhaboo&&(e=this._rhaboo.storage,f=this._rhaboo.slotnum,b=this._rhaboo.refs,c.release(this,!0));var g=d[a].apply(this,arguments);return void 0!==f&&c.addRef(this,e,f,b),g}};Array.prototype.push=function(){var a=this.length,b=d.push.apply(this,arguments),e=this.length;if(void 0!==this._rhaboo&&e>a){for(var f=a;e>f;f++)c.storeProp(this,f);c.updateSlot(this)}return b},Array.prototype.pop=function(){var a=this.length;void 0!==this._rhaboo&&a>0&&c.forgetProp(this,a-1);var b=d.pop.apply(this,arguments);return void 0!==this._rhaboo&&a>0&&c.updateSlot(this),b},Object.defineProperty(Array.prototype,"write",{value:function(a,b){Object.prototype.write.call(this,a,b),c.updateSlot(this)}}),Array.prototype.shift=e("shift"),Array.prototype.unshift=e("unshift"),Array.prototype.splice=e("splice"),Array.prototype.reverse=e("reverse"),Array.prototype.sort=e("sort"),Array.prototype.fill=e("fill"),b.exports={persistent:c.persistent,perishable:c.perishable,algorithm:"sand"}},{"./core":3}],3:[function(a,b){(function(c){"use strict";function d(a){return f(localStorage,a)}function e(a){return f(sessionStorage,a)}function f(a,b){var c=a.getItem(s+b);if(c){var d=A(a)(!1)(c);return t={},d[0]}var e={};Object.defineProperty(e,"_rhaboo",{value:{storage:a},writable:!0,configurable:!0,enumerable:!1});var f=n(e);return a.setItem(s+b,y(a)(!0)(f)),f}function g(a){return function(b){if(void 0!==t[b])return n(t[b]);var c=a.getItem(s+b);if(void 0===c||null===c)return void 0;var d=z(!1)(c);return Object.defineProperty(d[0],"_rhaboo",{value:{storage:a,slotnum:b,refs:1,kids:{}},writable:!0,configurable:!0,enumerable:!1}),t[b]=d[0],d.length>1?h(d[0],d[1][0],d[1][1]):d[0]}}function h(a,b,c){var d=a._rhaboo.storage.getItem(s+c);if(void 0===d||null===d)return a;var e=A(a._rhaboo.storage)(!1)(d);return a[b]=e[0],i(a,b,c),e.length>1?h(a,e[1][0],e[1][1]):a}function i(a,b,c){var d=void 0!==a._rhaboo.prev?a._rhaboo.kids[a._rhaboo.prev]:a._rhaboo;a._rhaboo.kids[b]={slotnum:c,prev:a._rhaboo.prev},d.next=a._rhaboo.prev=b}function j(a,b){var c=a._rhaboo.kids[b];(a._rhaboo.kids[c.prev]||a._rhaboo).next=c.next,(a._rhaboo.kids[c.next]||a._rhaboo).prev=c.prev,delete a._rhaboo.kids[b]}function k(a,b){var c=[];c.push(void 0!==b?a[b]:a);var d=void 0!==b?a._rhaboo.kids[b]:a._rhaboo;void 0!==d.next&&c.push([d.next,a._rhaboo.kids[d.next].slotnum]);var e=(void 0!==b?A(a._rhaboo.storage):z)(!0)(c);try{a._rhaboo.storage.setItem(s+d.slotnum,e)}catch(f){throw l(f)&&a._rhaboo.storage.removeItem(s+d.slotnum,e),console.log("Local storage quota exceeded by rhaboo"),f}}function l(a){var b=!1;if(a)if(a.code)switch(a.code){case 22:b=!0;break;case 1014:"NS_ERROR_DOM_QUOTA_REACHED"===a.name&&(b=!0)}else-2147024882===a.number&&(b=!0);return b}function m(a,b){if(void 0===a._rhaboo.kids[b]){var c=r(a._rhaboo.storage);i(a,b,c),k(a,a._rhaboo.kids[b].prev)}}function n(a,b,c,d){if(void 0!==a._rhaboo&&void 0!==a._rhaboo.slotnum)a._rhaboo.refs++;else{void 0===a._rhaboo&&Object.defineProperty(a,"_rhaboo",{value:{},writable:!0,configurable:!0,enumerable:!1}),void 0!==b&&(a._rhaboo.storage=b),a._rhaboo.slotnum=void 0!==c?c:r(a._rhaboo.storage),a._rhaboo.refs=void 0!==d?d:1,a._rhaboo.kids={},k(a);for(var e in a)a.hasOwnProperty(e)&&"_rhaboo"!==e&&o(a,e)}return a}function o(a,b){m(a,b),"object"===u.typeOf(a[b])&&(void 0===a[b]._rhaboo&&Object.defineProperty(a[b],"_rhaboo",{value:{storage:a._rhaboo.storage},writable:!0,configurable:!0,enumerable:!1}),n(a[b])),k(a,b)}function p(a,b){var c,d;if(a._rhaboo.refs--,b||0===a._rhaboo.refs){for(d=void 0,c=a._rhaboo;c;c=a._rhaboo.kids[d=c.next])a._rhaboo.storage.removeItem(s+c.slotnum),void 0!==d&&"object"==u.typeOf(a[d])&&p(a[d]);delete a._rhaboo}}function q(a,b){var c=a._rhaboo.kids[b];if(void 0!==c){var d=c.prev;a._rhaboo.storage.removeItem(s+c.slotnum),"object"==u.typeOf(a[b])&&p(a[b]),j(a,b),k(a,d)}}function r(a){var b=a===localStorage?0:1,c=C[b];return C[b]++,a.setItem(B,C[b]),c}void 0===Function.prototype.name&&void 0!==Object.defineProperty&&Object.defineProperty(Function.prototype,"name",{get:function(){var a=/function\s([^(]{1,})\(/,b=a.exec(this.toString());return b&&b.length>1?b[1].trim():""},set:function(){}});var s="_rhaboo_",t={},u=a("parunpar"),v=u.sepByEsc("=",":"),w=v([u.string_pp,u.number_pp]),x=u.pipe(function(a){return function(b){return a?"Date"===b.constructor.name?[b.constructor.name,b.toString()]:void 0!==b.length?[b.constructor.name,b.length.toString()]:[b.constructor.name]:new c[b[0]]("Date"==b[0]?b[1]:b[1]?Number(b[1]):void 0)}})(v([u.string_pp,u.string_pp])),y=function(a){return u.pipe(function(b){return function(c){return b?u.runSnd({string:["$",u.id],number:["#",String],"boolean":["?",function(a){return a?"t":"f"}],"null":["~"],undefined:["_"],object:["&",function(a){return a._rhaboo.slotnum}]}[u.typeOf(c)])(c):{$:u.id,"#":Number,"?":u.eq("t"),"~":u.konst(null),_:u.konst(void 0),"&":g(a)}[c[0]](c[1])}})(u.fixedWidth([1])([u.string_pp,u.string_pp]))},z=u.tuple([x,w]),A=function(a){return u.tuple([y(a),w])};Object.defineProperty(Object.prototype,"write",{value:function(a,b){return m(this,a),"object"===u.typeOf(this[a])&&p(this[a]),this[a]=b,"object"===u.typeOf(b)&&(void 0===b._rhaboo&&Object.defineProperty(b,"_rhaboo",{value:{storage:this._rhaboo.storage},writable:!0,configurable:!0,enumerable:!1}),n(b)),k(this,a),this}}),Object.defineProperty(Object.prototype,"erase",{value:function(a){if(!this.hasOwnProperty(a))return this;"object"===u.typeOf(this[a])&&p(this[a]);var b=this._rhaboo.kids[a];this._rhaboo.storage.removeItem(s+b.slotnum);var c=b.prev;return j(this,a),k(this,c),delete this[a],this}});for(var B="_RHABOO_NEXT_SLOT",C=[0,0],D=0;2>D;D++)C[D]=localStorage.getItem(B)||0,C[D]=Number(C[D]);b.exports={persistent:d,perishable:e,addRef:n,release:p,storeProp:o,forgetProp:q,updateSlot:k}}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{parunpar:1}],4:[function(a){(function(b){b.Rhaboo=a("./arr")}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{"./arr":2}]},{},[4]);

// WebcamJS v1.0.25 - http://github.com/jhuckaby/webcamjs - MIT Licensed
(function(e){var t;function a(){var e=Error.apply(this,arguments);e.name=this.name="FlashError";this.stack=e.stack;this.message=e.message}function i(){var e=Error.apply(this,arguments);e.name=this.name="WebcamError";this.stack=e.stack;this.message=e.message}var s=function(){};s.prototype=Error.prototype;a.prototype=new s;i.prototype=new s;var Webcam={version:"1.0.25",protocol:location.protocol.match(/https/i)?"https":"http",loaded:false,live:false,userMedia:true,iOS:/iPad|iPhone|iPod/.test(navigator.userAgent)&&!e.MSStream,params:{width:0,height:0,dest_width:0,dest_height:0,image_format:"jpeg",jpeg_quality:90,enable_flash:true,force_flash:false,flip_horiz:false,fps:30,upload_name:"webcam",constraints:null,swfURL:"",flashNotDetectedText:"ERROR: No Adobe Flash Player detected.  Webcam.js relies on Flash for browsers that do not support getUserMedia (like yours).",noInterfaceFoundText:"No supported webcam interface found.",unfreeze_snap:true,iosPlaceholderText:"Click here to open camera.",user_callback:null,user_canvas:null},errors:{FlashError:a,WebcamError:i},hooks:{},init:function(){var t=this;this.mediaDevices=navigator.mediaDevices&&navigator.mediaDevices.getUserMedia?navigator.mediaDevices:navigator.mozGetUserMedia||navigator.webkitGetUserMedia?{getUserMedia:function(e){return new Promise(function(t,a){(navigator.mozGetUserMedia||navigator.webkitGetUserMedia).call(navigator,e,t,a)})}}:null;e.URL=e.URL||e.webkitURL||e.mozURL||e.msURL;this.userMedia=this.userMedia&&!!this.mediaDevices&&!!e.URL;if(this.iOS){this.userMedia=null}if(navigator.userAgent.match(/Firefox\D+(\d+)/)){if(parseInt(RegExp.$1,10)<21)this.userMedia=null}if(this.userMedia){e.addEventListener("beforeunload",function(e){t.reset()})}},exifOrientation:function(e){var t=new DataView(e);if(t.getUint8(0)!=255||t.getUint8(1)!=216){console.log("Not a valid JPEG file");return 0}var a=2;var i=null;while(a<e.byteLength){if(t.getUint8(a)!=255){console.log("Not a valid marker at offset "+a+", found: "+t.getUint8(a));return 0}i=t.getUint8(a+1);if(i==225){a+=4;var s="";for(n=0;n<4;n++){s+=String.fromCharCode(t.getUint8(a+n))}if(s!="Exif"){console.log("Not valid EXIF data found");return 0}a+=6;var r=null;if(t.getUint16(a)==18761){r=false}else if(t.getUint16(a)==19789){r=true}else{console.log("Not valid TIFF data! (no 0x4949 or 0x4D4D)");return 0}if(t.getUint16(a+2,!r)!=42){console.log("Not valid TIFF data! (no 0x002A)");return 0}var o=t.getUint32(a+4,!r);if(o<8){console.log("Not valid TIFF data! (First offset less than 8)",t.getUint32(a+4,!r));return 0}var l=a+o;var h=t.getUint16(l,!r);for(var c=0;c<h;c++){var d=l+c*12+2;if(t.getUint16(d,!r)==274){var f=t.getUint16(d+2,!r);var m=t.getUint32(d+4,!r);if(f!=3&&m!=1){console.log("Invalid EXIF orientation value type ("+f+") or count ("+m+")");return 0}var p=t.getUint16(d+8,!r);if(p<1||p>8){console.log("Invalid EXIF orientation value ("+p+")");return 0}return p}}}else{a+=2+t.getUint16(a+2)}}return 0},fixOrientation:function(e,t,a){var i=new Image;i.addEventListener("load",function(e){var s=document.createElement("canvas");var r=s.getContext("2d");if(t<5){s.width=i.width;s.height=i.height}else{s.width=i.height;s.height=i.width}switch(t){case 2:r.transform(-1,0,0,1,i.width,0);break;case 3:r.transform(-1,0,0,-1,i.width,i.height);break;case 4:r.transform(1,0,0,-1,0,i.height);break;case 5:r.transform(0,1,1,0,0,0);break;case 6:r.transform(0,1,-1,0,i.height,0);break;case 7:r.transform(0,-1,-1,0,i.height,i.width);break;case 8:r.transform(0,-1,1,0,0,i.width);break}r.drawImage(i,0,0);a.src=s.toDataURL()},false);i.src=e},attach:function(a){if(typeof a=="string"){a=document.getElementById(a)||document.querySelector(a)}if(!a){return this.dispatch("error",new i("Could not locate DOM element to attach to."))}this.container=a;a.innerHTML="";var s=document.createElement("div");a.appendChild(s);this.peg=s;if(!this.params.width)this.params.width=a.offsetWidth;if(!this.params.height)this.params.height=a.offsetHeight;if(!this.params.width||!this.params.height){return this.dispatch("error",new i("No width and/or height for webcam.  Please call set() first, or attach to a visible element."))}if(!this.params.dest_width)this.params.dest_width=this.params.width;if(!this.params.dest_height)this.params.dest_height=this.params.height;this.userMedia=t===undefined?this.userMedia:t;if(this.params.force_flash){t=this.userMedia;this.userMedia=null}if(typeof this.params.fps!=="number")this.params.fps=30;var r=this.params.width/this.params.dest_width;var o=this.params.height/this.params.dest_height;if(this.userMedia){var n=document.createElement("video");n.setAttribute("autoplay","autoplay");n.style.width=""+this.params.dest_width+"px";n.style.height=""+this.params.dest_height+"px";if(r!=1||o!=1){a.style.overflow="hidden";n.style.webkitTransformOrigin="0px 0px";n.style.mozTransformOrigin="0px 0px";n.style.msTransformOrigin="0px 0px";n.style.oTransformOrigin="0px 0px";n.style.transformOrigin="0px 0px";n.style.webkitTransform="scaleX("+r+") scaleY("+o+")";n.style.mozTransform="scaleX("+r+") scaleY("+o+")";n.style.msTransform="scaleX("+r+") scaleY("+o+")";n.style.oTransform="scaleX("+r+") scaleY("+o+")";n.style.transform="scaleX("+r+") scaleY("+o+")"}a.appendChild(n);this.video=n;var l=this;this.mediaDevices.getUserMedia({audio:false,video:this.params.constraints||{mandatory:{minWidth:this.params.dest_width,minHeight:this.params.dest_height}}}).then(function(t){n.onloadedmetadata=function(e){l.stream=t;l.loaded=true;l.live=true;l.dispatch("load");l.dispatch("live");l.flip()};if("srcObject"in n){n.srcObject=t}else{n.src=e.URL.createObjectURL(t)}}).catch(function(e){if(l.params.enable_flash&&l.detectFlash()){setTimeout(function(){l.params.force_flash=1;l.attach(a)},1)}else{l.dispatch("error",e)}})}else if(this.iOS){var h=document.createElement("div");h.id=this.container.id+"-ios_div";h.className="webcamjs-ios-placeholder";h.style.width=""+this.params.width+"px";h.style.height=""+this.params.height+"px";h.style.textAlign="center";h.style.display="table-cell";h.style.verticalAlign="middle";h.style.backgroundRepeat="no-repeat";h.style.backgroundSize="contain";h.style.backgroundPosition="center";var c=document.createElement("span");c.className="webcamjs-ios-text";c.innerHTML=this.params.iosPlaceholderText;h.appendChild(c);var d=document.createElement("img");d.id=this.container.id+"-ios_img";d.style.width=""+this.params.dest_width+"px";d.style.height=""+this.params.dest_height+"px";d.style.display="none";h.appendChild(d);var f=document.createElement("input");f.id=this.container.id+"-ios_input";f.setAttribute("type","file");f.setAttribute("accept","image/*");f.setAttribute("capture","camera");var l=this;var m=this.params;f.addEventListener("change",function(e){if(e.target.files.length>0&&e.target.files[0].type.indexOf("image/")==0){var t=URL.createObjectURL(e.target.files[0]);var a=new Image;a.addEventListener("load",function(e){var t=document.createElement("canvas");t.width=m.dest_width;t.height=m.dest_height;var i=t.getContext("2d");ratio=Math.min(a.width/m.dest_width,a.height/m.dest_height);var s=m.dest_width*ratio;var r=m.dest_height*ratio;var o=(a.width-s)/2;var n=(a.height-r)/2;i.drawImage(a,o,n,s,r,0,0,m.dest_width,m.dest_height);var l=t.toDataURL();d.src=l;h.style.backgroundImage="url('"+l+"')"},false);var i=new FileReader;i.addEventListener("load",function(e){var i=l.exifOrientation(e.target.result);if(i>1){l.fixOrientation(t,i,a)}else{a.src=t}},false);var s=new XMLHttpRequest;s.open("GET",t,true);s.responseType="blob";s.onload=function(e){if(this.status==200||this.status===0){i.readAsArrayBuffer(this.response)}};s.send()}},false);f.style.display="none";a.appendChild(f);h.addEventListener("click",function(e){if(m.user_callback){l.snap(m.user_callback,m.user_canvas)}else{f.style.display="block";f.focus();f.click();f.style.display="none"}},false);a.appendChild(h);this.loaded=true;this.live=true}else if(this.params.enable_flash&&this.detectFlash()){e.Webcam=Webcam;var h=document.createElement("div");h.innerHTML=this.getSWFHTML();a.appendChild(h)}else{this.dispatch("error",new i(this.params.noInterfaceFoundText))}if(this.params.crop_width&&this.params.crop_height){var p=Math.floor(this.params.crop_width*r);var u=Math.floor(this.params.crop_height*o);a.style.width=""+p+"px";a.style.height=""+u+"px";a.style.overflow="hidden";a.scrollLeft=Math.floor(this.params.width/2-p/2);a.scrollTop=Math.floor(this.params.height/2-u/2)}else{a.style.width=""+this.params.width+"px";a.style.height=""+this.params.height+"px"}},reset:function(){if(this.preview_active)this.unfreeze();this.unflip();if(this.userMedia){if(this.stream){if(this.stream.getVideoTracks){var e=this.stream.getVideoTracks();if(e&&e[0]&&e[0].stop)e[0].stop()}else if(this.stream.stop){this.stream.stop()}}delete this.stream;delete this.video}if(this.userMedia!==true&&this.loaded&&!this.iOS){var t=this.getMovie();if(t&&t._releaseCamera)t._releaseCamera()}if(this.container){this.container.innerHTML="";delete this.container}this.loaded=false;this.live=false},set:function(){if(arguments.length==1){for(var e in arguments[0]){this.params[e]=arguments[0][e]}}else{this.params[arguments[0]]=arguments[1]}},on:function(e,t){e=e.replace(/^on/i,"").toLowerCase();if(!this.hooks[e])this.hooks[e]=[];this.hooks[e].push(t)},off:function(e,t){e=e.replace(/^on/i,"").toLowerCase();if(this.hooks[e]){if(t){var a=this.hooks[e].indexOf(t);if(a>-1)this.hooks[e].splice(a,1)}else{this.hooks[e]=[]}}},dispatch:function(){var t=arguments[0].replace(/^on/i,"").toLowerCase();var s=Array.prototype.slice.call(arguments,1);if(this.hooks[t]&&this.hooks[t].length){for(var r=0,o=this.hooks[t].length;r<o;r++){var n=this.hooks[t][r];if(typeof n=="function"){n.apply(this,s)}else if(typeof n=="object"&&n.length==2){n[0][n[1]].apply(n[0],s)}else if(e[n]){e[n].apply(e,s)}}return true}else if(t=="error"){var l;if(s[0]instanceof a||s[0]instanceof i){l=s[0].message}else{l="Could not access webcam: "+s[0].name+": "+s[0].message+" "+s[0].toString()}alert("Webcam.js Error: "+l)}return false},setSWFLocation:function(e){this.set("swfURL",e)},detectFlash:function(){var t="Shockwave Flash",a="ShockwaveFlash.ShockwaveFlash",i="application/x-shockwave-flash",s=e,r=navigator,o=false;if(typeof r.plugins!=="undefined"&&typeof r.plugins[t]==="object"){var n=r.plugins[t].description;if(n&&(typeof r.mimeTypes!=="undefined"&&r.mimeTypes[i]&&r.mimeTypes[i].enabledPlugin)){o=true}}else if(typeof s.ActiveXObject!=="undefined"){try{var l=new ActiveXObject(a);if(l){var h=l.GetVariable("$version");if(h)o=true}}catch(e){}}return o},getSWFHTML:function(){var t="",i=this.params.swfURL;if(location.protocol.match(/file/)){this.dispatch("error",new a("Flash does not work from local disk.  Please run from a web server."));return'<h3 style="color:red">ERROR: the Webcam.js Flash fallback does not work from local disk.  Please run it from a web server.</h3>'}if(!this.detectFlash()){this.dispatch("error",new a("Adobe Flash Player not found.  Please install from get.adobe.com/flashplayer and try again."));return'<h3 style="color:red">'+this.params.flashNotDetectedText+"</h3>"}if(!i){var s="";var r=document.getElementsByTagName("script");for(var o=0,n=r.length;o<n;o++){var l=r[o].getAttribute("src");if(l&&l.match(/\/webcam(\.min)?\.js/)){s=l.replace(/\/webcam(\.min)?\.js.*$/,"");o=n}}if(s)i=s+"/webcam.swf";else i="webcam.swf"}if(e.localStorage&&!localStorage.getItem("visited")){this.params.new_user=1;localStorage.setItem("visited",1)}var h="";for(var c in this.params){if(h)h+="&";h+=c+"="+escape(this.params[c])}t+='<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" type="application/x-shockwave-flash" codebase="'+this.protocol+'://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="'+this.params.width+'" height="'+this.params.height+'" id="webcam_movie_obj" align="middle"><param name="wmode" value="opaque" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="false" /><param name="movie" value="'+i+'" /><param name="loop" value="false" /><param name="menu" value="false" /><param name="quality" value="best" /><param name="bgcolor" value="#ffffff" /><param name="flashvars" value="'+h+'"/><embed id="webcam_movie_embed" src="'+i+'" wmode="opaque" loop="false" menu="false" quality="best" bgcolor="#ffffff" width="'+this.params.width+'" height="'+this.params.height+'" name="webcam_movie_embed" align="middle" allowScriptAccess="always" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="'+h+'"></embed></object>';return t},getMovie:function(){if(!this.loaded)return this.dispatch("error",new a("Flash Movie is not loaded yet"));var e=document.getElementById("webcam_movie_obj");if(!e||!e._snap)e=document.getElementById("webcam_movie_embed");if(!e)this.dispatch("error",new a("Cannot locate Flash movie in DOM"));return e},freeze:function(){var e=this;var t=this.params;if(this.preview_active)this.unfreeze();var a=this.params.width/this.params.dest_width;var i=this.params.height/this.params.dest_height;this.unflip();var s=t.crop_width||t.dest_width;var r=t.crop_height||t.dest_height;var o=document.createElement("canvas");o.width=s;o.height=r;var n=o.getContext("2d");this.preview_canvas=o;this.preview_context=n;if(a!=1||i!=1){o.style.webkitTransformOrigin="0px 0px";o.style.mozTransformOrigin="0px 0px";o.style.msTransformOrigin="0px 0px";o.style.oTransformOrigin="0px 0px";o.style.transformOrigin="0px 0px";o.style.webkitTransform="scaleX("+a+") scaleY("+i+")";o.style.mozTransform="scaleX("+a+") scaleY("+i+")";o.style.msTransform="scaleX("+a+") scaleY("+i+")";o.style.oTransform="scaleX("+a+") scaleY("+i+")";o.style.transform="scaleX("+a+") scaleY("+i+")"}this.snap(function(){o.style.position="relative";o.style.left=""+e.container.scrollLeft+"px";o.style.top=""+e.container.scrollTop+"px";e.container.insertBefore(o,e.peg);e.container.style.overflow="hidden";e.preview_active=true},o)},unfreeze:function(){if(this.preview_active){this.container.removeChild(this.preview_canvas);delete this.preview_context;delete this.preview_canvas;this.preview_active=false;this.flip()}},flip:function(){if(this.params.flip_horiz){var e=this.container.style;e.webkitTransform="scaleX(-1)";e.mozTransform="scaleX(-1)";e.msTransform="scaleX(-1)";e.oTransform="scaleX(-1)";e.transform="scaleX(-1)";e.filter="FlipH";e.msFilter="FlipH"}},unflip:function(){if(this.params.flip_horiz){var e=this.container.style;e.webkitTransform="scaleX(1)";e.mozTransform="scaleX(1)";e.msTransform="scaleX(1)";e.oTransform="scaleX(1)";e.transform="scaleX(1)";e.filter="";e.msFilter=""}},savePreview:function(e,t){var a=this.params;var i=this.preview_canvas;var s=this.preview_context;if(t){var r=t.getContext("2d");r.drawImage(i,0,0)}e(t?null:i.toDataURL("image/"+a.image_format,a.jpeg_quality/100),i,s);if(this.params.unfreeze_snap)this.unfreeze()},snap:function(e,t){if(!e)e=this.params.user_callback;if(!t)t=this.params.user_canvas;var a=this;var s=this.params;if(!this.loaded)return this.dispatch("error",new i("Webcam is not loaded yet"));if(!e)return this.dispatch("error",new i("Please provide a callback function or canvas to snap()"));if(this.preview_active){this.savePreview(e,t);return null}var r=document.createElement("canvas");r.width=this.params.dest_width;r.height=this.params.dest_height;var o=r.getContext("2d");if(this.params.flip_horiz){o.translate(s.dest_width,0);o.scale(-1,1)}var n=function(){if(this.src&&this.width&&this.height){o.drawImage(this,0,0,s.dest_width,s.dest_height)}if(s.crop_width&&s.crop_height){var a=document.createElement("canvas");a.width=s.crop_width;a.height=s.crop_height;var i=a.getContext("2d");i.drawImage(r,Math.floor(s.dest_width/2-s.crop_width/2),Math.floor(s.dest_height/2-s.crop_height/2),s.crop_width,s.crop_height,0,0,s.crop_width,s.crop_height);o=i;r=a}if(t){var n=t.getContext("2d");n.drawImage(r,0,0)}e(t?null:r.toDataURL("image/"+s.image_format,s.jpeg_quality/100),r,o)};if(this.userMedia){o.drawImage(this.video,0,0,this.params.dest_width,this.params.dest_height);n()}else if(this.iOS){var l=document.getElementById(this.container.id+"-ios_div");var h=document.getElementById(this.container.id+"-ios_img");var c=document.getElementById(this.container.id+"-ios_input");iFunc=function(e){n.call(h);h.removeEventListener("load",iFunc);l.style.backgroundImage="none";h.removeAttribute("src");c.value=null};if(!c.value){h.addEventListener("load",iFunc);c.style.display="block";c.focus();c.click();c.style.display="none"}else{iFunc(null)}}else{var d=this.getMovie()._snap();var h=new Image;h.onload=n;h.src="data:image/"+this.params.image_format+";base64,"+d}return null},configure:function(e){if(!e)e="camera";this.getMovie()._configure(e)},flashNotify:function(e,t){switch(e){case"flashLoadComplete":this.loaded=true;this.dispatch("load");break;case"cameraLive":this.live=true;this.dispatch("live");break;case"error":this.dispatch("error",new a(t));break;default:break}},b64ToUint6:function(e){return e>64&&e<91?e-65:e>96&&e<123?e-71:e>47&&e<58?e+4:e===43?62:e===47?63:0},base64DecToArr:function(e,t){var a=e.replace(/[^A-Za-z0-9\+\/]/g,""),i=a.length,s=t?Math.ceil((i*3+1>>2)/t)*t:i*3+1>>2,r=new Uint8Array(s);for(var o,n,l=0,h=0,c=0;c<i;c++){n=c&3;l|=this.b64ToUint6(a.charCodeAt(c))<<18-6*n;if(n===3||i-c===1){for(o=0;o<3&&h<s;o++,h++){r[h]=l>>>(16>>>o&24)&255}l=0}}return r},upload:function(e,t,a){var i=this.params.upload_name||"webcam";var s="";if(e.match(/^data\:image\/(\w+)/))s=RegExp.$1;else throw"Cannot locate image format in Data URI";var r=e.replace(/^data\:image\/\w+\;base64\,/,"");var o=new XMLHttpRequest;o.open("POST",t,true);if(o.upload&&o.upload.addEventListener){o.upload.addEventListener("progress",function(e){if(e.lengthComputable){var t=e.loaded/e.total;Webcam.dispatch("uploadProgress",t,e)}},false)}var n=this;o.onload=function(){if(a)a.apply(n,[o.status,o.responseText,o.statusText]);Webcam.dispatch("uploadComplete",o.status,o.responseText,o.statusText)};var l=new Blob([this.base64DecToArr(r)],{type:"image/"+s});var h=new FormData;h.append(i,l,i+"."+s.replace(/e/,""));o.send(h)}};Webcam.init();if(typeof define==="function"&&define.amd){define(function(){return Webcam})}else if(typeof module==="object"&&module.exports){module.exports=Webcam}else{e.Webcam=Webcam}})(window);

/**
 * WMA
**/

var pcadbApp = {
	auth: false,
	user: '',
	baseurl:'',
	pleaseWait: 'Please Wait',
	isScolling: false,
	storage: Rhaboo.persistent("pcadb_localStorage"),
	init: function(){
		var t = this;
		$('body').on('submit', 'form', function(evt){
			var f = $(this);
			if(!f.data('ajaxf')){
				f.ajaxForm({
					success: function(r,s,x,f) {
						//bit 1 no reset, 2 custom-success, 4 no lock submit button
						var bit = Number(f.data('bit')) || 0;
						if((bit & 2) > 0){
							f.trigger('success', [r]);
							return false;
						}
						f.trigger('before-success', [r]);
						if(r.done === true){
							$('#notifc').notify({message: {html: r.msg}, closable: false}).show();
							f.trigger('success', [r]);
							javascript:window.history.back();
							//if((bit & 1) === 0) f.resetForm();
						}else{
							$('#notifc').notify({message: {html: r.msg}, closable: false, type: 'danger'}).show();
							f.trigger('error', [r]);
						}
						t.btnLoading($('button[type=submit]', f), true);
					},
					beforeSubmit: function(a,f,o){
						f.data('invalid', false);
						f.trigger('before-submit');
						$('input.required, select.required, textarea.required', f).each(function(){
							var v = $(this).val();
							if(v == ''){
								$(this).addClass('error');
								if(!f.data('invalid')) $(this).focus();
								f.data('invalid', true);
							}else{
								$(this).removeClass('error');
							}
						});
						if(f.data('invalid') === true){
							$('#notifc').notify({message: {html: 'Please fill in all required fields'}, closable: false, type: 'danger'}).show();
							return false;
						}else{
							var bit = Number(f.data('bit')) || 0;
							if((bit & 4) === 0) t.btnLoading($('button[type=submit]', f));
							return true;
						}
					},
					dataType: 'json',
				});
				f.data('ajaxf', true);
				f.submit();
				return false;
			}
		}).on('touchend', 'a.search-form-tog', function(e){
			$('div.search-form').slideToggle();
			return false;
		}).on('touchend', 'a.confirm_link', function(e){
			return window.confirm('Are you sure?');
		}).on('touchend', 'a.modal_link', function(){
			window.clearInterval(window.interval);
			if(t.isScrolling) return false;
			var m = $('#ajax-modal').data('url', $(this).attr('href')).trigger('loadModal');
			$('h1.title', m).text($(this).attr('title'));
			return false;
		}).on('touchend', '.modal .icon-close', function(e){
			$(this).parents('.modal').removeClass('active');
			$('.lazy-load').data({'more-url':'wma/site/index/?WmsTask_page=1&q='+$('#task_search').val()}).empty().trigger('lazyLoad');
		}).on('touchend', '#lf_capcha', function(e){
			$(this).attr('src', $(this).data('url')+'?'+Math.random());
		}).on('touchend', '.popover a', function(){
			if($(this).data('ignore')) return true;
			$(this).parents('.popover').hide();
			$('body .backdrop').remove();
		}).on('success', '.search-form form', function(e, r){
			$('div.search-result').html($(r.data).data({'lazy-loading': false, 'more-url': r.url}));
			t.btnLoading($('button[type=submit]', this), true);
			$(window).trigger('load');
		}).on('touchend', '.table-view-cell', function(e){
			if(t.isScrolling) return false;
			if($(e.target).parents('a').length > 0) return;
			$('.more', this).slideToggle();
		}).on('lazyLoad', '#main-content .table-view.lazy-load', function(){
			var t = $(this), url = t.data('more-url') || '';
			if(url == ''){
				t.removeClass('lazy-loading');
				return false;
			}
			$.getJSON(url, function(r){
				t.data({'more-url': r.url}).append($(r.data).html()).removeClass('lazy-loading');
			});
		}).on('touchend', '.tabs a.control-item', function(){
			$(this).siblings().each(function(){
				$('#'+$(this).data('pane')+'.tab-pane').removeClass('active').trigger('hiding');
			});
			$('#'+$(this).data('pane')+'.tab-pane').addClass('active').trigger('showing');
		}).on('touchstart focus', 'input.txtdate', function(){
			$(this).attr('type', 'date').trigger('click');
		}).on('blur', 'input.txtdate', function(){
			$(this).attr('type', 'search');
		}).on('focus', 'input, textarea, select', function(){
			$(this).parents('form').find('input.barcode.hi').removeClass('hi');
		}).on('focus', 'input.barcode', function(){
			if(!$(this).hasClass('keyboard')){
				$(window).data('current_barcode_input', $(this));
				$('input.barcode.hi').removeClass('hi');
				$(this).addClass('hi').blur();
				return false;
			}
			$(this).removeClass('keyboard hi');
		}).on('doubletap', 'input.barcode', function(){
			$(this).addClass('keyboard').focus();
			$(window).data('current_barcode_input', null);
			return false;
		});

		$('#ajax-modal').on('loadModal', function(){
			var m = $(this);
			$('.modal-content', m).load(m.data('url'), function(){
				m.addClass('active');
				$('form', m).on('before-success', function(){
					if ($(this).attr('id') == 'sign-form') return;
					m.removeClass('active');
				});
				$('input', m).attr('autocomplete', 'off');
			});
		});

		var bc_sto = null;

		$(window).data({'current_barcode_input': null, 'barcode_buffer': ''}).on('load push', function(){
			if($('.table-view.lazy-load').is(':empty')) $('.table-view.lazy-load').trigger('lazyLoad');
			$('input').attr('autocomplete', 'off');
			var scroll_sto = null;
			$('body #main-content').off('scroll').on('scroll', function(e){
				var t = $('#main-content .table-view.lazy-load');
				var oset = $('.table-view-cell:last-child', t).offset();
				// if(!t.hasClass('lazy-loading') && oset.top < $(window).height()){
				if(!t.hasClass('lazy-loading')){
					clearTimeout(scroll_sto);
					scroll_sto =setTimeout(function(){ t.addClass('lazy-loading').trigger('lazyLoad'); }, 200);
				}
			});
		}).on('touchstart', function(){
			t.isScrolling = false;
		}).on('touchmove', function(){
			t.isScrolling = true;
		}).on('keypress', function(e){
			clearTimeout(bc_sto);
			bc_sto = setTimeout(function(){
				$(window).data('barcode_buffer', '');
			}, 100);
			if(e.which == 13){
				if($(window).data('current_barcode_input')) $(window).trigger('enter_barcode');
				return;
			}else{
				buffer = $(window).data('barcode_buffer')+e.key;
				$(window).data('barcode_buffer', buffer);
			}
		}).on('enter_barcode', function(){
			$(window).data('current_barcode_input').val($(window).data('barcode_buffer')).trigger('afterBarcode');
			$(window).data('barcode_buffer', '');
		});


		setInterval(function(){
			$.get($('body').data('baseurl')+'/site/checkSession');
		}, 6e4);
	},
	btnLoading: function(b,d){
		if(d === true){
			b.attr('disabled', false).html(b.data('oc'));
		}else{
			b.data('oc', b.html());
			b.attr('disabled', true).html('<span class="icon icon-refresh icon-refresh-animate"></span> '+this.pleaseWait);
		}
	},
	ajax : function(url,async){
		let data = "";
		$.ajax({
			url: this.baseurl + url,
			dataType: 'json',
			type: "post",
			data: [],
			async: async,
			processData: false,
			contentType: false,
			success: function(r){
				if(r.code)
				{
					data = r.data;
				}else
				{
					$('#notifc').notify({message: {html: r.msg}, closable: false, type: 'danger'}).show();
							f.trigger('error', [r]);
				}
			},
			error: function(r, e){alert(e);}
		});
		return data;
	},
	showLogin: function(){
		$('#login-modal').addClass('active');
		$('#lf_capcha').trigger('touchend');
	},
	uuid: function(){
		if(this.storage.uid == undefined||this.storage.uid.uid==""|| Object.keys(this.storage.uid).length == 0)
		{
			let uid = this.ajax('generateUUid.html',false);
			this.storage.write('uid',{});
			this.storage.uid.write('uid',uid);
		}
		return this.storage.uid.uid;
	},
	selectTime: function(selectTime){
		if(((this.storage.selectTime == undefined||this.storage.selectTime.selectTime==''|| Object.keys(this.storage.selectTime).length == 0)&&selectTime!=null)||selectTime=='')
		{
			this.storage.write('selectTime',{});
			this.storage.selectTime.write('selectTime',selectTime);
			console.log(this.storage.selectTime.selectTime);
			return this.storage.selectTime.selectTime;

		}else if(this.storage.selectTime == undefined)
		{
			return '';
		}else
		{
			return this.storage.selectTime.selectTime;
		}
	},
	queuePhoto: function(id, data, note, type = 'task'){
		if(this.storage.photos == undefined || Object.keys(this.storage.photos).length == 0) {
			this.storage.write('photos', {});
		}
		if (this.storage.notes == undefined || Object.keys(this.storage.notes).length == 0) {
			this.storage.write('notes', {});
		}
		if (this.storage.types == undefined || Object.keys(this.storage.types).length == 0) {
			this.storage.write('types', {});
		}
		var uuid = this.uuid();
		this.storage.photos.write(uuid, [id, data]);
		this.storage.notes.write(uuid, [id, note]);
		this.storage.types.write(uuid, [id, type]);
	},
	initLink:function()
	{
		$('nav a').click(function(){
			var btn = this;  
			var event = document.createEvent('Events');
			event.initEvent('touchend', true, true); 
			btn.dispatchEvent(event); 
		});

		$('header a').click(function(){
			var btn = this;  
			var event = document.createEvent('Events');
			event.initEvent('touchend', true, true); 
			btn.dispatchEvent(event); 
		});
	}
};
$(function(){
	pcadbApp.init();
	window.setInterval(function(){
		if(pcadbApp.storage.photos == undefined || Object.keys(pcadbApp.storage.photos).length == 0) return;
		for(var i in pcadbApp.storage.photos){
			$.post($('body').data('baseurl')+'/job/uploadPhoto', {'id': pcadbApp.storage.photos[i][0], 'data':pcadbApp.storage.photos[i][1], 'note':pcadbApp.storage.notes[i][1], 'type':wmaApp.storage.types[i][1]}, function(r){
				if(r == 'DONE') {
					pcadbApp.storage.photos.erase(i);
					pcadbApp.storage.notes.erase(i);
					pcadbApp.storage.types.erase(i);
				}
			});
		}
	}, 1e4);

	$('#popover a').click(function(){
		var btn = this;  
		var event = document.createEvent('Events');
		event.initEvent('touchend', true, true); 
		btn.dispatchEvent(event); 
	});
});