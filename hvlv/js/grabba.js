var Grabba = {
	version : '2.5.2',
	
	//**********************************************************
	//*                                                        *
	//* GRABBA CONNECTED/DISCONNECTED EVENT                    *
	//* - Capture event when Grabba is connected/disconnected. *
	//*                                                        *
	//**********************************************************
	connectedEvent: function(model,serial,batteryLevel){ //ADD YOUR OWN CODE HERE
		$('body').trigger('gb_connect',[model,serial,batteryLevel]);
		return 'YES';
	},
	
	disconnectedEvent: function(){ //ADD YOUR OWN CODE HERE
		$('body').trigger('gb_disconnect');
		return 'YES';
	},
	
	//**********************************************************
	//*                                                        *
	//* INTERNATION EXECUTION COMMAND                          *
	//* - Triggers the Browser application command through     *
	//*   javascript                                           *
	//* - DO NOT MODIFY THIS PART OF SCRIPT                    *
	//*                                                        *
	//**********************************************************
	executeCommand: function(command){
		//document.location is bad, this method is much better
		//http://blog.techno-barje.fr/post/2010/10/06/UIWebView-secrets-part3-How-to-properly-call-ObjectiveC-from-Javascript
		// ***** DO NOT MODIFY *****
		var iframe = document.createElement("IFRAME");
		iframe.setAttribute("src", command);
		document.documentElement.appendChild(iframe);
		iframe.parentNode.removeChild(iframe);
		iframe = null;
	},
	
	callbacksCount : 1,
	callbacks : {},
	
	/* Automatically called by native layer when a result is available */
	resultForCallback : function resultForCallback(callbackId, resultArray){
		// ***** DO NOT MODIFY *****
		try {
			var callback = Grabba.callbacks[callbackId];
			if (!callback) return;
			
			callback.apply(null,resultArray);
		} catch(e) {alert(e)}
	},
	
	// Use this in javascript to request native objective-c code
	// functionName : string
	// args : array of arguments
	// callback : function with n-arguments that is going to be called when the native code returned
	call : function call(functionName, args, callback){
		// ***** DO NOT MODIFY *****
		var hasCallback = callback && typeof callback == "function";
		var callbackId = hasCallback ? Grabba.callbacksCount++ : 0;
		if (hasCallback) Grabba.callbacks[callbackId] = callback;
		var iframe = document.createElement("IFRAME");
		iframe.setAttribute("src", functionName + ":" + callbackId+ ":" + args);
		document.documentElement.appendChild(iframe);
		iframe.parentNode.removeChild(iframe);
		iframe = null;
	},
	
	//**********************************************************
	//*                                                        *
	//* GRABBA ERROR CODES                                     *
	//* - Script might returns the following error code in the *
	//*   functions                                            *
	//* - DO NOT MODIFY THIS PART OF SCRIPT                    *
	//*                                                        *
	//**********************************************************
	kGRGrabbaErrorNotConnected : -2,
	kGRGrabbaErrorFunctionNotSupported : -3,
	kGRGrabbaErrorIO : -4,
	kGRGrabbaErrorBusy : -5,
	kGRGrabbaErrorTimeout : -14,
	kGRGrabbaErrorProxcardNoCardInField: -16,
	
	showError : function(errorCode, errorMessage){
		//ADD YOUR OWN CODE HERE
		if(errorCode==Grabba.kGRGrabbaErrorNotConnected){
			$('#grabbaData').val('Not Connected');
		}else if(errorCode==Grabba.kGRGrabbaErrorFunctionNotSupported){
			$('#grabbaData').val('Not Supported');
		}else if(errorCode==Grabba.kGRGrabbaErrorIO){
			$('#grabbaData').val('IO Error');
		}else if(errorCode==Grabba.kGRGrabbaErrorBusy){
			$('#grabbaData').val('Busy');
		}else if(errorCode==Grabba.kGRGrabbaErrorTimeout){
			$('#grabbaData').val('Timeout');
		}else{
			$('#grabbaData').val('Err: ' + errorCode);
		}
		return 'YES';
	}
};

var GrabbaBarcode = {
	//**********************************************************
	//*                                                        *
	//* BARCODE SCRIPT                                         *
	//* - DO NOT MODIFY THIS PART OF SCRIPT                    *
	//*                                                        *
	//**********************************************************
	trigger: function(){
		// ***** DO NOT MODIFY *****
		//This tells the Grabba Browser application to trigger the barcode scan.
		Grabba.executeCommand('grabba://barcode.trigger/');
		return 'YES';
	},
	
	//***********************************************************
	//*                                                         *
	//* BARCODE DELEGATE SCRIPT                                 *
	//* - These are called from the Grabba Browser app when     * 
	//*   events happen                                         *
	//* - You have 2 options for receiving barcode data.        *
	//*   1. raw barcode data                                   *
	//*   2. formatted barcode data based on the barcode        *
	//*      formatting preferences.                            *
	//*                                                         *
	//*                                                         *
	//* DELEGATE DESCRIPTION                                    *
	//* - barcodeDidTrigger: called when "barcode trigger"      *
	//*   command is just called.                               *
	//* - barcodeDidReceiveData: called when barcode is         *
	//*   successfully scanned.  Raw barcode data will be       *
	//*   returned.                                             *
	//*   Data is an array of bytes,  eg, [100,232,123,12]      *
	//* - barcodeDidReceiveFormattedData: called when barcode   *
	//*   is successfully scanned.  Data is a string,           *
	//*   eg "D4 C3 34 B2"                                      *
	//* - barcodeDidFailTriggerWithError: called when hardware  *
	//*   returns an error                                      *
	//*                                                         *
	//***********************************************************
	barcodeDidTrigger: function(){ //ADD YOUR OWN CODE HERE
		return 'YES';
	},
	
	barcodeDidReceiveData: function(barcode,symbologyType){ //ADD YOUR OWN CODE HERE
		return 'YES';
	},
	
	barcodeDidReceiveFormattedData: function(barcode,symbologyType){ //ADD YOUR OWN CODE HERE
		$('#gb_bc').val(barcode).trigger('gotBC');
		return 'YES';
	},	
	
	barcodeDidFailTriggerWithError: function(errorCode, errorMessage){
		return Grabba.showError(errorCode, errorMessage);
	}
};


var addLog = function(l, e){
	$('#log').prepend('<p'+(e? ' class="err"' : '')+'>'+l+'</p>');
	$('#log p').each(function(i){
		if(i > 200) $(this).remove();
	});
};
$(function(){
	$.ajaxSetup({timeout: 8000});
	$(document).ajaxError(function(e,x,s){
		var msg = 'Connection error, please try again.';
		if($('body').data('init')) addLog(msg, true);
		else alert(msg);
	});

	$('#log').on('click', 'a.undo', function(){
		if((new Date().getTime()) > $(this).data('exp')){
			alert('Sorry resume expired.');
			$(this).remove();
			return false;
		}
		if(window.confirm('Undo?')){
			var that = $(this);
			$.get($(this).attr('href'), function(r){
				$('#main form').trigger('undo', [r]);
				addLog(r.msg);
				that.remove();
			}, 'json');
		}
		return false;
	});

	$('body').on('gb_connect', function(evt,md,sn,bl){
		var b = $(this).data({model: md, serial: sn, init: false});
		$.get('auth?md='+md+'&sn='+sn, function(r){
			if(r){
				$('#olay').fadeOut();
				b.data('init', true);
			}else{
				alert('Unknown Scanner!');
			}
		}, 'json');
	}).on('gb_disconnect', function(){
		$(this).data({model: null, serial: null, init: false});
		$('#olay').fadeIn();
	});

	$('nav').on('click', 'a:not(.hi)', function(){
		$('#main form').trigger('exit');
		if($('#main form').data('no-exit')) return false;
		$('#main').load($(this).attr('href')+'?md='+$('body').data('model')+'&sn='+$('body').data('serial'), function(r){
			$('#main form').ajaxForm({
				success:function(d,s,x,f){
					if(d.nf == 1){
						addLog(d.bc+', not found!', true);
						d.va = 'not_found';
					}
					f.trigger('success', [d]);
					if(d.va){
						$('#sound').attr('src', '../site/voice/'+d.va+'.mp3');
						$('#sound').trigger('sound');
					}
				},
				dataType: 'json'
			});
		});
		$('nav a').removeClass('hi');
		$(this).addClass('hi');
		$('nav a').not('.hi').hide();
		return false;
	}).on('click', 'a.hi', function(){
		$('nav a').not('.hi').toggle();
		return false;
	});

	$('#sound').on('sound', function(){
		if($('button.sound-ctrl').hasClass('off')) return;
		this.play();
	});

	$('button.sound-ctrl').click(function(){
		$(this).toggleClass('off');
		$('#sound').attr('src', '../site/voice/voice_on.mp3');
		$('#sound').trigger('sound');
	});
	
	$('#main').on('gotBC', '#gb_bc', function(){
		$(this).parents('form').submit();
	});

	//#auth
	var patt = /#(.+)\|(.+)$/g;
	r = patt.exec(window.location.href);
	if(r && r.length == 3) $('body').trigger('gb_connect',[r[1], r[2]]);
});

//minimal ac
(function($){
$.fn.autoComp = function(options) {
	var defaults = {minLength: 2, delay: 400}, o = $.extend(defaults, options), to, that = $(this);
	var rl = $('<ul class="ac_list"></ul>').hide();
	$(this).after(rl);
	$(this).on('keyup', function(){
		var v = $(this).val();
		if(v.length >= o.minLength){
			clearTimeout(to);
			to = setTimeout(function(){
				$.get(o.url+'&term='+v, function(r){
					rl.empty();
					for(var i in r){
						l = r[i].label || r[i].value
						rl.append('<li data-val="'+r[i].value+'">'+l+'</li>');
					}
					if($('li', rl).length > 0) rl.slideDown();
				}, 'json');
			}, o.delay);
		}
	}).on('blur', function(){
		rl.slideUp();
	}).on('focus', function(){
		if($('li', rl).length > 0) rl.slideDown();
	});
	rl.on('click', 'li', function(){
		that.val($(this).data('val'));
		rl.hide();
	});
	return this;
};
})(jQuery);