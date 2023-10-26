/**
 * Webcam
**/

(function(e){var t;function a(){var e=Error.apply(this,arguments);e.name=this.name="FlashError";this.stack=e.stack;this.message=e.message}function i(){var e=Error.apply(this,arguments);e.name=this.name="WebcamError";this.stack=e.stack;this.message=e.message}IntermediateInheritor=function(){};IntermediateInheritor.prototype=Error.prototype;a.prototype=new IntermediateInheritor;i.prototype=new IntermediateInheritor;var Webcam={version:"1.0.22",protocol:location.protocol.match(/https/i)?"https":"http",loaded:false,live:false,userMedia:true,iOS:/iPad|iPhone|iPod/.test(navigator.userAgent)&&!e.MSStream,params:{width:0,height:0,dest_width:0,dest_height:0,image_format:"jpeg",jpeg_quality:90,enable_flash:true,force_flash:false,flip_horiz:false,fps:30,upload_name:"webcam",constraints:null,swfURL:"",flashNotDetectedText:"ERROR: No Adobe Flash Player detected.  Webcam.js relies on Flash for browsers that do not support getUserMedia (like yours).",noInterfaceFoundText:"No supported webcam interface found.",unfreeze_snap:true,iosPlaceholderText:"Click here to open camera.",user_callback:null,user_canvas:null},errors:{FlashError:a,WebcamError:i},hooks:{},init:function(){var t=this;this.mediaDevices=navigator.mediaDevices&&navigator.mediaDevices.getUserMedia?navigator.mediaDevices:navigator.mozGetUserMedia||navigator.webkitGetUserMedia?{getUserMedia:function(e){return new Promise(function(t,a){(navigator.mozGetUserMedia||navigator.webkitGetUserMedia).call(navigator,e,t,a)})}}:null;e.URL=e.URL||e.webkitURL||e.mozURL||e.msURL;this.userMedia=this.userMedia&&!!this.mediaDevices&&!!e.URL;if(navigator.userAgent.match(/Firefox\D+(\d+)/)){if(parseInt(RegExp.$1,10)<21)this.userMedia=null}if(this.userMedia){e.addEventListener("beforeunload",function(e){t.reset()})}},exifOrientation:function(e){var t=new DataView(e);if(t.getUint8(0)!=255||t.getUint8(1)!=216){console.log("Not a valid JPEG file");return 0}var a=2;var i=null;while(a<e.byteLength){if(t.getUint8(a)!=255){console.log("Not a valid marker at offset "+a+", found: "+t.getUint8(a));return 0}i=t.getUint8(a+1);if(i==225){a+=4;var s="";for(n=0;n<4;n++){s+=String.fromCharCode(t.getUint8(a+n))}if(s!="Exif"){console.log("Not valid EXIF data found");return 0}a+=6;var r=null;if(t.getUint16(a)==18761){r=false}else if(t.getUint16(a)==19789){r=true}else{console.log("Not valid TIFF data! (no 0x4949 or 0x4D4D)");return 0}if(t.getUint16(a+2,!r)!=42){console.log("Not valid TIFF data! (no 0x002A)");return 0}var o=t.getUint32(a+4,!r);if(o<8){console.log("Not valid TIFF data! (First offset less than 8)",t.getUint32(a+4,!r));return 0}var h=a+o;var l=t.getUint16(h,!r);for(var c=0;c<l;c++){var d=h+c*12+2;if(t.getUint16(d,!r)==274){var f=t.getUint16(d+2,!r);var m=t.getUint32(d+4,!r);if(f!=3&&m!=1){console.log("Invalid EXIF orientation value type ("+f+") or count ("+m+")");return 0}var p=t.getUint16(d+8,!r);if(p<1||p>8){console.log("Invalid EXIF orientation value ("+p+")");return 0}return p}}}else{a+=2+t.getUint16(a+2)}}return 0},fixOrientation:function(e,t,a){var i=new Image;i.addEventListener("load",function(e){var s=document.createElement("canvas");var r=s.getContext("2d");if(t<5){s.width=i.width;s.height=i.height}else{s.width=i.height;s.height=i.width}switch(t){case 2:r.transform(-1,0,0,1,i.width,0);break;case 3:r.transform(-1,0,0,-1,i.width,i.height);break;case 4:r.transform(1,0,0,-1,0,i.height);break;case 5:r.transform(0,1,1,0,0,0);break;case 6:r.transform(0,1,-1,0,i.height,0);break;case 7:r.transform(0,-1,-1,0,i.height,i.width);break;case 8:r.transform(0,-1,1,0,0,i.width);break}r.drawImage(i,0,0);a.src=s.toDataURL()},false);i.src=e},attach:function(a){if(typeof a=="string"){a=document.getElementById(a)||document.querySelector(a)}if(!a){return this.dispatch("error",new i("Could not locate DOM element to attach to."))}this.container=a;a.innerHTML="";var s=document.createElement("div");a.appendChild(s);this.peg=s;if(!this.params.width)this.params.width=a.offsetWidth;if(!this.params.height)this.params.height=a.offsetHeight;if(!this.params.width||!this.params.height){return this.dispatch("error",new i("No width and/or height for webcam.  Please call set() first, or attach to a visible element."))}if(!this.params.dest_width)this.params.dest_width=this.params.width;if(!this.params.dest_height)this.params.dest_height=this.params.height;this.userMedia=t===undefined?this.userMedia:t;if(this.params.force_flash){t=this.userMedia;this.userMedia=null}if(typeof this.params.fps!=="number")this.params.fps=30;var r=this.params.width/this.params.dest_width;var o=this.params.height/this.params.dest_height;if(this.userMedia){var n=document.createElement("video");n.setAttribute("autoplay","autoplay");n.style.width=""+this.params.dest_width+"px";n.style.height=""+this.params.dest_height+"px";if(r!=1||o!=1){a.style.overflow="hidden";n.style.webkitTransformOrigin="0px 0px";n.style.mozTransformOrigin="0px 0px";n.style.msTransformOrigin="0px 0px";n.style.oTransformOrigin="0px 0px";n.style.transformOrigin="0px 0px";n.style.webkitTransform="scaleX("+r+") scaleY("+o+")";n.style.mozTransform="scaleX("+r+") scaleY("+o+")";n.style.msTransform="scaleX("+r+") scaleY("+o+")";n.style.oTransform="scaleX("+r+") scaleY("+o+")";n.style.transform="scaleX("+r+") scaleY("+o+")"}a.appendChild(n);this.video=n;var h=this;this.mediaDevices.getUserMedia({audio:false,video:this.params.constraints||{mandatory:{minWidth:this.params.dest_width,minHeight:this.params.dest_height}}}).then(function(t){n.onloadedmetadata=function(e){h.stream=t;h.loaded=true;h.live=true;h.dispatch("load");h.dispatch("live");h.flip()};n.src=e.URL.createObjectURL(t)||t}).catch(function(e){if(h.params.enable_flash&&h.detectFlash()){setTimeout(function(){h.params.force_flash=1;h.attach(a)},1)}else{h.dispatch("error",e)}})}else if(this.iOS){var l=document.createElement("div");l.id=this.container.id+"-ios_div";l.className="webcamjs-ios-placeholder";l.style.width=""+this.params.width+"px";l.style.height=""+this.params.height+"px";l.style.textAlign="center";l.style.display="table-cell";l.style.verticalAlign="middle";l.style.backgroundRepeat="no-repeat";l.style.backgroundSize="contain";l.style.backgroundPosition="center";var c=document.createElement("span");c.className="webcamjs-ios-text";c.innerHTML=this.params.iosPlaceholderText;l.appendChild(c);var d=document.createElement("img");d.id=this.container.id+"-ios_img";d.style.width=""+this.params.dest_width+"px";d.style.height=""+this.params.dest_height+"px";d.style.display="none";l.appendChild(d);var f=document.createElement("input");f.id=this.container.id+"-ios_input";f.setAttribute("type","file");f.setAttribute("accept","image/*");f.setAttribute("capture","camera");var h=this;var m=this.params;f.addEventListener("change",function(e){if(e.target.files.length>0&&e.target.files[0].type.indexOf("image/")==0){var t=URL.createObjectURL(e.target.files[0]);var a=new Image;a.addEventListener("load",function(e){var t=document.createElement("canvas");t.width=m.dest_width;t.height=m.dest_height;var i=t.getContext("2d");ratio=Math.min(a.width/m.dest_width,a.height/m.dest_height);var s=m.dest_width*ratio;var r=m.dest_height*ratio;var o=(a.width-s)/2;var n=(a.height-r)/2;i.drawImage(a,o,n,s,r,0,0,m.dest_width,m.dest_height);var h=t.toDataURL();d.src=h;l.style.backgroundImage="url('"+h+"')"},false);var i=new FileReader;i.addEventListener("load",function(e){var i=h.exifOrientation(e.target.result);if(i>1){h.fixOrientation(t,i,a)}else{a.src=t}},false);var s=new XMLHttpRequest;s.open("GET",t,true);s.responseType="blob";s.onload=function(e){if(this.status==200||this.status===0){i.readAsArrayBuffer(this.response)}};s.send()}},false);f.style.display="none";a.appendChild(f);l.addEventListener("click",function(e){if(m.user_callback){h.snap(m.user_callback,m.user_canvas)}else{f.style.display="block";f.focus();f.click();f.style.display="none"}},false);a.appendChild(l);this.loaded=true;this.live=true}else if(this.params.enable_flash&&this.detectFlash()){e.Webcam=Webcam;var l=document.createElement("div");l.innerHTML=this.getSWFHTML();a.appendChild(l)}else{this.dispatch("error",new i(this.params.noInterfaceFoundText))}if(this.params.crop_width&&this.params.crop_height){var p=Math.floor(this.params.crop_width*r);var u=Math.floor(this.params.crop_height*o);a.style.width=""+p+"px";a.style.height=""+u+"px";a.style.overflow="hidden";a.scrollLeft=Math.floor(this.params.width/2-p/2);a.scrollTop=Math.floor(this.params.height/2-u/2)}else{a.style.width=""+this.params.width+"px";a.style.height=""+this.params.height+"px"}},reset:function(){if(this.preview_active)this.unfreeze();this.unflip();if(this.userMedia){if(this.stream){if(this.stream.getVideoTracks){var e=this.stream.getVideoTracks();if(e&&e[0]&&e[0].stop)e[0].stop()}else if(this.stream.stop){this.stream.stop()}}delete this.stream;delete this.video}if(this.userMedia!==true&&this.loaded&&!this.iOS){var t=this.getMovie();if(t&&t._releaseCamera)t._releaseCamera()}if(this.container){this.container.innerHTML="";delete this.container}this.loaded=false;this.live=false},set:function(){if(arguments.length==1){for(var e in arguments[0]){this.params[e]=arguments[0][e]}}else{this.params[arguments[0]]=arguments[1]}},on:function(e,t){e=e.replace(/^on/i,"").toLowerCase();if(!this.hooks[e])this.hooks[e]=[];this.hooks[e].push(t)},off:function(e,t){e=e.replace(/^on/i,"").toLowerCase();if(this.hooks[e]){if(t){var a=this.hooks[e].indexOf(t);if(a>-1)this.hooks[e].splice(a,1)}else{this.hooks[e]=[]}}},dispatch:function(){var t=arguments[0].replace(/^on/i,"").toLowerCase();var s=Array.prototype.slice.call(arguments,1);if(this.hooks[t]&&this.hooks[t].length){for(var r=0,o=this.hooks[t].length;r<o;r++){var n=this.hooks[t][r];if(typeof n=="function"){n.apply(this,s)}else if(typeof n=="object"&&n.length==2){n[0][n[1]].apply(n[0],s)}else if(e[n]){e[n].apply(e,s)}}return true}else if(t=="error"){if(s[0]instanceof a||s[0]instanceof i){message=s[0].message}else{message="Could not access webcam: "+s[0].name+": "+s[0].message+" "+s[0].toString()}alert("Webcam.js Error: "+message)}return false},setSWFLocation:function(e){this.set("swfURL",e)},detectFlash:function(){var t="Shockwave Flash",a="ShockwaveFlash.ShockwaveFlash",i="application/x-shockwave-flash",s=e,r=navigator,o=false;if(typeof r.plugins!=="undefined"&&typeof r.plugins[t]==="object"){var n=r.plugins[t].description;if(n&&(typeof r.mimeTypes!=="undefined"&&r.mimeTypes[i]&&r.mimeTypes[i].enabledPlugin)){o=true}}else if(typeof s.ActiveXObject!=="undefined"){try{var h=new ActiveXObject(a);if(h){var l=h.GetVariable("$version");if(l)o=true}}catch(e){}}return o},getSWFHTML:function(){var t="",i=this.params.swfURL;if(location.protocol.match(/file/)){this.dispatch("error",new a("Flash does not work from local disk.  Please run from a web server."));return'<h3 style="color:red">ERROR: the Webcam.js Flash fallback does not work from local disk.  Please run it from a web server.</h3>'}if(!this.detectFlash()){this.dispatch("error",new a("Adobe Flash Player not found.  Please install from get.adobe.com/flashplayer and try again."));return'<h3 style="color:red">'+this.params.flashNotDetectedText+"</h3>"}if(!i){var s="";var r=document.getElementsByTagName("script");for(var o=0,n=r.length;o<n;o++){var h=r[o].getAttribute("src");if(h&&h.match(/\/webcam(\.min)?\.js/)){s=h.replace(/\/webcam(\.min)?\.js.*$/,"");o=n}}if(s)i=s+"/webcam.swf";else i="webcam.swf"}if(e.localStorage&&!localStorage.getItem("visited")){this.params.new_user=1;localStorage.setItem("visited",1)}var l="";for(var c in this.params){if(l)l+="&";l+=c+"="+escape(this.params[c])}t+='<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" type="application/x-shockwave-flash" codebase="'+this.protocol+'://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="'+this.params.width+'" height="'+this.params.height+'" id="webcam_movie_obj" align="middle"><param name="wmode" value="opaque" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="false" /><param name="movie" value="'+i+'" /><param name="loop" value="false" /><param name="menu" value="false" /><param name="quality" value="best" /><param name="bgcolor" value="#ffffff" /><param name="flashvars" value="'+l+'"/><embed id="webcam_movie_embed" src="'+i+'" wmode="opaque" loop="false" menu="false" quality="best" bgcolor="#ffffff" width="'+this.params.width+'" height="'+this.params.height+'" name="webcam_movie_embed" align="middle" allowScriptAccess="always" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="'+l+'"></embed></object>';return t},getMovie:function(){if(!this.loaded)return this.dispatch("error",new a("Flash Movie is not loaded yet"));var e=document.getElementById("webcam_movie_obj");if(!e||!e._snap)e=document.getElementById("webcam_movie_embed");if(!e)this.dispatch("error",new a("Cannot locate Flash movie in DOM"));return e},freeze:function(){var e=this;var t=this.params;if(this.preview_active)this.unfreeze();var a=this.params.width/this.params.dest_width;var i=this.params.height/this.params.dest_height;this.unflip();var s=t.crop_width||t.dest_width;var r=t.crop_height||t.dest_height;var o=document.createElement("canvas");o.width=s;o.height=r;var n=o.getContext("2d");this.preview_canvas=o;this.preview_context=n;if(a!=1||i!=1){o.style.webkitTransformOrigin="0px 0px";o.style.mozTransformOrigin="0px 0px";o.style.msTransformOrigin="0px 0px";o.style.oTransformOrigin="0px 0px";o.style.transformOrigin="0px 0px";o.style.webkitTransform="scaleX("+a+") scaleY("+i+")";o.style.mozTransform="scaleX("+a+") scaleY("+i+")";o.style.msTransform="scaleX("+a+") scaleY("+i+")";o.style.oTransform="scaleX("+a+") scaleY("+i+")";o.style.transform="scaleX("+a+") scaleY("+i+")"}this.snap(function(){o.style.position="relative";o.style.left=""+e.container.scrollLeft+"px";o.style.top=""+e.container.scrollTop+"px";e.container.insertBefore(o,e.peg);e.container.style.overflow="hidden";e.preview_active=true},o)},unfreeze:function(){if(this.preview_active){this.container.removeChild(this.preview_canvas);delete this.preview_context;delete this.preview_canvas;this.preview_active=false;this.flip()}},flip:function(){if(this.params.flip_horiz){var e=this.container.style;e.webkitTransform="scaleX(-1)";e.mozTransform="scaleX(-1)";e.msTransform="scaleX(-1)";e.oTransform="scaleX(-1)";e.transform="scaleX(-1)";e.filter="FlipH";e.msFilter="FlipH"}},unflip:function(){if(this.params.flip_horiz){var e=this.container.style;e.webkitTransform="scaleX(1)";e.mozTransform="scaleX(1)";e.msTransform="scaleX(1)";e.oTransform="scaleX(1)";e.transform="scaleX(1)";e.filter="";e.msFilter=""}},savePreview:function(e,t){var a=this.params;var i=this.preview_canvas;var s=this.preview_context;if(t){var r=t.getContext("2d");r.drawImage(i,0,0)}e(t?null:i.toDataURL("image/"+a.image_format,a.jpeg_quality/100),i,s);if(this.params.unfreeze_snap)this.unfreeze()},snap:function(e,t){if(!e)e=this.params.user_callback;if(!t)t=this.params.user_canvas;var a=this;var s=this.params;if(!this.loaded)return this.dispatch("error",new i("Webcam is not loaded yet"));if(!e)return this.dispatch("error",new i("Please provide a callback function or canvas to snap()"));if(this.preview_active){this.savePreview(e,t);return null}var r=document.createElement("canvas");r.width=this.params.dest_width;r.height=this.params.dest_height;var o=r.getContext("2d");if(this.params.flip_horiz){o.translate(s.dest_width,0);o.scale(-1,1)}var n=function(){if(this.src&&this.width&&this.height){o.drawImage(this,0,0,s.dest_width,s.dest_height)}if(s.crop_width&&s.crop_height){var a=document.createElement("canvas");a.width=s.crop_width;a.height=s.crop_height;var i=a.getContext("2d");i.drawImage(r,Math.floor(s.dest_width/2-s.crop_width/2),Math.floor(s.dest_height/2-s.crop_height/2),s.crop_width,s.crop_height,0,0,s.crop_width,s.crop_height);o=i;r=a}if(t){var n=t.getContext("2d");n.drawImage(r,0,0)}e(t?null:r.toDataURL("image/"+s.image_format,s.jpeg_quality/100),r,o)};if(this.userMedia){o.drawImage(this.video,0,0,this.params.dest_width,this.params.dest_height);n()}else if(this.iOS){var h=document.getElementById(this.container.id+"-ios_div");var l=document.getElementById(this.container.id+"-ios_img");var c=document.getElementById(this.container.id+"-ios_input");iFunc=function(e){n.call(l);l.removeEventListener("load",iFunc);h.style.backgroundImage="none";l.removeAttribute("src");c.value=null};if(!c.value){l.addEventListener("load",iFunc);c.style.display="block";c.focus();c.click();c.style.display="none"}else{iFunc(null)}}else{var d=this.getMovie()._snap();var l=new Image;l.onload=n;l.src="data:image/"+this.params.image_format+";base64,"+d}return null},configure:function(e){if(!e)e="camera";this.getMovie()._configure(e)},flashNotify:function(e,t){switch(e){case"flashLoadComplete":this.loaded=true;this.dispatch("load");break;case"cameraLive":this.live=true;this.dispatch("live");break;case"error":this.dispatch("error",new a(t));break;default:break}},b64ToUint6:function(e){return e>64&&e<91?e-65:e>96&&e<123?e-71:e>47&&e<58?e+4:e===43?62:e===47?63:0},base64DecToArr:function(e,t){var a=e.replace(/[^A-Za-z0-9\+\/]/g,""),i=a.length,s=t?Math.ceil((i*3+1>>2)/t)*t:i*3+1>>2,r=new Uint8Array(s);for(var o,n,h=0,l=0,c=0;c<i;c++){n=c&3;h|=this.b64ToUint6(a.charCodeAt(c))<<18-6*n;if(n===3||i-c===1){for(o=0;o<3&&l<s;o++,l++){r[l]=h>>>(16>>>o&24)&255}h=0}}return r},upload:function(e,t,a){var i=this.params.upload_name||"webcam";var s="";if(e.match(/^data\:image\/(\w+)/))s=RegExp.$1;else throw"Cannot locate image format in Data URI";var r=e.replace(/^data\:image\/\w+\;base64\,/,"");var o=new XMLHttpRequest;o.open("POST",t,true);if(o.upload&&o.upload.addEventListener){o.upload.addEventListener("progress",function(e){if(e.lengthComputable){var t=e.loaded/e.total;Webcam.dispatch("uploadProgress",t,e)}},false)}var n=this;o.onload=function(){if(a)a.apply(n,[o.status,o.responseText,o.statusText]);Webcam.dispatch("uploadComplete",o.status,o.responseText,o.statusText)};var h=new Blob([this.base64DecToArr(r)],{type:"image/"+s});var l=new FormData;l.append(i,h,i+"."+s.replace(/e/,""));o.send(l)}};Webcam.init();if(typeof define==="function"&&define.amd){define(function(){return Webcam})}else if(typeof module==="object"&&module.exports){module.exports=Webcam}else{e.Webcam=Webcam}})(window);

var camApp = {
	storage: Rhaboo.persistent('wma_localStorage'),
	count: 0,
	upload: false,
	taskid: '',
	url: '',
	mode: '',
	init: function() {
		$(window).data({'barcode_buffer': ''}).on('keypress', function(e) {
			if (e.target.id == 'input' || e.target.id == '') {
				$('#input').focus();
				if (e.which == 13) {
					var barcode = $(window).data('barcode_buffer');
					$(window).data({'barcode_buffer': ''});
					if (barcode) {
						$('#input').val(barcode);
						$('#submit').click();
					}
					return;
				} else {
					var buffer = $(window).data('barcode_buffer') + e.key;
					$(window).data('barcode_buffer', buffer);
				}
			}
		});
	},
	queuePhoto: function(id, data) {
		if (this.storage.photos == undefined || Object.keys(this.storage.photos).length == 0) {
			this.storage.write('photos', {});
		}
		this.storage.photos.write(id, data);
	},
	uploadCallback: function(msg) {
		if (msg.result) {
			for (var i in camApp.storage.photos) {
				camApp.storage.photos.erase(i);
				break;
			}
			if (Object.keys(camApp.storage.photos).length == 0) {
				camApp.upload = false;
			}
		}
	}
};

$(function() {
	camApp.init();
	window.setInterval(function() {
		if (camApp.storage.photos == undefined || Object.keys(camApp.storage.photos).length == 0) {
			return;
		} else {
			if (camApp.upload) {
				for (var i in camApp.storage.photos) {
					var input = 'taskid=' + camApp.taskid + '&data=' + encodeURIComponent(camApp.storage.photos[i]);
					pack.ajax(camApp.url, input, camApp.uploadCallback);
					break;
				}
			}
		}
	}, 3000);
});

/**
 * pack
**/

var pack = {
	products: [],
	parcels: [],
	taskid: '',
	stockid: '',
	weightstatus: true,
	labelstatus: 'pca',
	markstatus: '',
	ws: null,
	finishlock: false,
	dimstatus: false,

	detailUrl: '',
	completeUrl: '',
	printUrl: '',
	checkUrl: '',
	boxUrl: '',
	singleItemUrl: '',
	dimUrl: '',
	holdTaskUrl: '',

	orgid: '',
	prodid: '',

	boxid: '',
	lasttaskid: '',
	lastboxid: '',
	lastfile: '',
	lsfile: '',

	success_sound: '',
	alarm_sound: '',

	defaultmsg: '请扫描拣货完成生成的二维码 或 分货箱条码',
	init: function() {
		pack.products = [];
		pack.parcels = [];
		pack.taskid = '';
		pack.stockid = '';
		pack.weightstatus = true;
		pack.labelstatus = 'pca';
		pack.markstatus = '';
		pack.finishlock = false;

		pack.orgid = [];
		pack.prodid = [];

		pack.boxid = '';
		pack.lasttaskid = '';
		pack.lastboxid = '';
		pack.lastfile = '';
		pack.lsfile = '';

		$('#input').val('');
		$('#taskdetail').html('');
		$('#msgdiv').html('');
		$('#input').focus();

		$('#taskid').text('未扫描');
		$('#taskid').removeClass('text-success');
		$('#taskid').addClass('text-danger');
		$('#label').text('');
		$('#mark').text('');

		pack.dimstatus = false;
		$('#dim').hide();

		pack.success_sound = document.createElement('audio');
		pack.success_sound.setAttribute('src', '../../sounds/success.wav');
		pack.alarm_sound = document.createElement('audio');
		pack.alarm_sound.setAttribute('src', '../../sounds/alarm.wav');

		pack.showInfo('msgdiv', 'success', pack.defaultmsg);
	},
	partInit: function() {
		pack.products = [];
		pack.parcels = [];
		pack.taskid = '';
		pack.stockid = '';
		pack.weightstatus = true;
		pack.labelstatus = 'pca';
		pack.markstatus = '';
		pack.finishlock = false;

		pack.orgid = [];
		pack.prodid = [];

		pack.boxid = '';
		// pack.lasttaskid = '';
		// pack.lastboxid = '';
		pack.lastfile = '';
		pack.lsfile = '';

		$('#input').val('');
		$('#taskdetail').html('');
		$('#msgdiv').html('');
		$('#input').focus();

		$('#taskid').text('未扫描');
		$('#taskid').removeClass('text-success');
		$('#taskid').addClass('text-danger');
		$('#label').text('');
		$('#mark').text('');

		pack.dimstatus = false;
		$('#dim').hide();

		var msg = pack.defaultmsg;
		if (pack.lasttaskid) {
			msg += '<br />上一个订单为: ' + pack.lasttaskid.toUpperCase();
			if (pack.lastboxid) {
				msg += ', 箱号为: ' + pack.lastboxid.toUpperCase();
			}
			if (pack.lastfile) {
				msg += ', 重打印面单请  <a id="reprint_label" style="cursor: pointer; text-decoration: none;" onclick="pack.reprintLabel()">点击</a>';
			}
		}

		pack.showInfo('msgdiv', 'success', msg);
		$('#search_task_bar').show();
	},
	show: function() {
		console.log(pack.products);
		console.log(pack.taskid);
	},
	ajax: function(url, input, callback) {
		$.ajax({
			type: 'post',
			url: url,
			dataType: 'json',
			data: input,
			success: function(msg) {
				pack.ajaxCallback(msg, callback);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				if(url.indexOf('count')<0) {
					alert('出错啦', textStatus + ': ' + errorThrown);
				}
			}
		});
	},
	ajaxCallback: function(msg, callback) {
		callback(msg);
	},
	load: function(url, div, callback) {
		$('#' + div).load(url, callback);
	},
	showInfo: function(div, type, msg) {
		if (type === 'success') {
			$('#' + div).html('<div class="alert alert-success" style="text-align:center; font-size:32px; margin-top:2px"><span class="glyphicon glyphicon-thumbs-up"></span> ' + msg + '</div>');
		} else if (type === 'fail') {
			$('#' + div).html('<div class="alert alert-danger" style="text-align:center; font-size:32px; margin-top:2px"><span class="glyphicon glyphicon-warning-sign"></span> ' + msg + '</div>');
		}
	},
	scanTask: function() {
		var type = $('#type').val();
		var input = $('#input').val().toLowerCase();
		var prefix = input.substr(0, 1);

		if (pack.weightstatus === false) {
			$('#input').val('');
			$('#input').focus();
			pack.showInfo('msgdiv', 'fail', '请打开称重工具，并重新打开此页面 <a href="https://os.pcaex.com/Weigh.zip" target="_blank">(点击下载)</a>');
			pack.alarm_sound.play();
			return;
		}

		if (type === 'check') {
			if (input === '**photo') {

			} else if (input === '**cancel') {
				pack.partInit();
			} else if (pack.products.length) {
				pack.scanProduct(input);
			} else if (prefix === 't') {
				pack.taskid = input;
				var url = pack.detailUrl.replace('.app', '/taskid/' + encodeURIComponent(input));
				pack.load(url, 'taskdetail', pack.scanTaskCallback);
			}
		} else if (type === 'whole' || type === 'pack') {
			if (input === '**photo') {

			} else if (input === '**cancel') {
				pack.partInit();
			} else if (input === '**material') {
				var material = $('#material').text();
				if (material == '是') {
					$('#material').text('否');
					$('#material').removeClass('text-success');
					$('#material').addClass('text-danger');
				} else {
					$('#material').text('是');
					$('#material').removeClass('text-danger');
					$('#material').addClass('text-success');
				}
			} else if (input === '**weight') {
				var taskid = pack.taskid;
				var material = $('#material').text();
				var weight = $('#weight').val();
				var height = $('#weight').val();
				var width = $('#width').val();
				var depth = $('#depth').val();
				$('#input').val('');
				$('#input').focus();
				if (taskid === '') {
					pack.showInfo('msgdiv', 'fail', '打包失败，请扫描拣货完成二维码 或者 客户面单');
					pack.alarm_sound.play();
				} else if (weight < 0.01) {
					pack.showInfo('msgdiv', 'fail', '打包失败，请将包裹放置在电子秤上');
					pack.alarm_sound.play();
				} else if (pack.dimstatus && (height == 0 || width == 0 || depth == 0)) {
					pack.showInfo('msgdiv', 'fail', '打包失败，请填写三边信息');
					pack.alarm_sound.play();
				} else {
					pack.packComplete();
				}
			} else if (input === '**finish') {
				var taskid = pack.taskid;
				$('#input').val('');
				$('#input').focus();
				if (pack.finishlock == true) {
					pack.showInfo('msgdiv', 'fail', '请稍等，由于网络延迟，后台正在处理面单');
					pack.alarm_sound.play();
					return;
				} else {
					pack.finishlock = true;
				}
				if (taskid === '') {
					pack.showInfo('msgdiv', 'fail', '打包失败，请扫描拣货完成二维码 或者 客户面单');
					pack.alarm_sound.play();
				} else {
					if (pack.labelstatus == 'null') {
						pack.printInvoice();
					} else if (pack.labelstatus == 'pca') {
						pack.printInvoice();
					} else if (pack.labelstatus == 'other') {
						pack.printInvoice();
					}
				}
			} else if (input.match(/^(B|b)[0-9]{5}$/)) {
				// box id
				pack.partInit();
				// var status = input.split(' ')[1];
				// if (status === 'complete') {
				pack.boxid = input.split(' ')[0];
				var url = pack.boxUrl.replace('.app', '/boxid/' + encodeURIComponent(pack.boxid));
				pack.ajax(url, '', pack.boxCallback);
				// }
			} else if (prefix === 't' && (input.match(/complete/) || input.match(/reprint/))) {
				// task id
				pack.partInit();
				var status = input.split(' ')[1];
				if (status === 'complete') {
					pack.taskid = input.split(' ')[0];
					var url = pack.detailUrl.replace('.app', '/taskid/' + encodeURIComponent(pack.taskid));
					pack.load(url, 'taskdetail', pack.scanTaskCallback);
				} else if (status === 'reprint') {
					pack.taskid = input.split(' ')[0];
					var url = pack.detailUrl.replace('.app', '/taskid/' + encodeURIComponent(pack.taskid) + '/type/reprint');
					pack.ajax(url, '', pack.printInvoiceCallback);
				}
			} else {
				pack.partInit();
				var singleProdBarcode = input;
				var url = pack.singleItemUrl.replace('.app', '/barcode/' + encodeURIComponent(singleProdBarcode));
				pack.ajax(url, '', pack.singleItemCallback);
			}
			// else if (prefix === 's') {
			// 	input = input.match(/S(\d+)Q(\d+)/);
			// 	if (pack.taskid === '') {
			// 		pack.showInfo('msgdiv', 'fail', pack.defaultmsg);
			// 		pack.alarm_sound.play();
			// 	} else {
			// 		var stockid = input[1];

			// 		pack.checkProduct(stockid);
			// 	}
			// }
			// else {
			// 	var taskid = pack.taskid;
			// 	if (taskid === '') {
			// 		pack.taskid = input;
			// 		var material = $('#material').text();
			// 		var weight = $('#weight').val();
			// 		var url = pack.detailUrl.replace('.app', '/taskid/' + encodeURIComponent(input) + '/material/' + encodeURIComponent(material) + '/weight/' + encodeURIComponent(weight));
			// 		pack.load(url, 'taskdetail', pack.scanTaskCallback);
			// 	} else if (weight < 0.01) {
			// 		pack.showInfo('msgdiv', 'fail', '打包失败，请将包裹放置在电子秤上');
			// 		pack.alarm_sound.play();
			// 	} else {
			// 		if (pack.labelstatus == 'pca' || pack.labelstatus == 'null') {
			// 			pack.showInfo('msgdiv', 'fail', '请扫描完成码');
			// 			pack.alarm_sound.play();
			// 		} else if (pack.labelstatus == 'other') {
			// 			pack.printInvoice(input);
			// 		}
			// 	}
			// }
			// else if (pack.products.length) {
			//     pack.scanProduct(pack.completeUrl, input);
			// }
		}
	},
	scanTaskCallback: function() {
		var type = $('#type').val();
		if (type === 'whole') {
			if (pack.products.length) {
				$('#taskid').text(pack.taskid.toUpperCase());
				$('#taskid').removeClass('text-danger');
				$('#taskid').addClass('text-success');
				pack.showInfo('msgdiv', 'success', '请确认是否使用耗材并扫描称重码');
				pack.success_sound.play();

				if (pack.lsfile) {
					pack.ws.send(pack.lsfile);
				}

				$('#search_task_bar').hide();
			} else {
				pack.taskid = '';
				pack.stockid = '';
				pack.alarm_sound.play();

				$('#search_task_bar').show();
			}
		}
	},
	scanProduct: function(barcode) {
		var scan_quantity = 1;

		if (pack.products.hasOwnProperty(barcode)) {
			pack.products[barcode]['scanquantity'] += scan_quantity;
			if (pack.products[barcode]['scanquantity'] <= pack.products[barcode]['quantity']) {
				pack.scanSuccess(barcode);
			} else {
				pack.products[barcode]['scanquantity'] -= scan_quantity;
				pack.showInfo('msgdiv', 'fail', '配货失败，此商品配货数超出了总商品数');
				pack.alarm_sound.play();
			}
		} else {
			pack.showInfo('msgdiv', 'fail', '配货失败，此商品不在此订单中');
			pack.alarm_sound.play();
		}
	},
	enterNumber: function(barcode) {
		var scan_quantity = parseInt($('#scan_' + barcode).find('input').val()) - pack.products[barcode]['scanquantity'];
		pack.products[barcode]['scanquantity'] += scan_quantity;

		if (pack.products[barcode]['scanquantity'] <= pack.products[barcode]['quantity']) {
			pack.scanSuccess(barcode);
		} else {
			pack.products[barcode]['scanquantity'] -= scan_quantity;
			pack.showInfo('msgdiv', 'fail', '配货失败，此商品配货数超出了总商品数');
			pack.alarm_sound.play();
		}
	},
	scanSuccess: function(barcode) {
		var type = $('#type').val();

		if (type === 'check') {
			var quanitythtml = '<input type=\'text\' id=\"' + barcode + '\" style=\'width: 100px; height: 30px\' onchange=\'order.enterNumber(\"' + pack.completeUrl + '\", ' + barcode + ');\' value=\'' + pack.products[barcode]['scanquantity'] + '\' />';
			$('#scan_' + barcode).html(quanitythtml);
			if (pack.products[barcode]['scanquantity'] == pack.products[barcode]['quantity']) {
				$('#tr_' + barcode).css("color", "#429842");
				$('#icon_' + barcode).html("<span class=\"glyphicon glyphicon-ok\"> </span>");
			}

			var complete = true;
			var taskid = pack.taskid;

			for (var barcode in pack.products) {
				if (pack.products[barcode]['quantity'] == pack.products[barcode]['scanquantity']) {
					continue;
				} else {
					complete = false;
					break;
				}
			}

			if (complete) {
				pack.scanComplete();
			} else {
				var quanitythtml = '<input type=\'text\' id=\"' + barcode + '\" style=\'width: 100px; height: 30px\' onchange=\'pack.enterNumber(\"' + pack.completeUrl + '\", ' + barcode + ');\' value=\'' + pack.products[barcode]['scanquantity'] + '\' />';
				$('#scan_' + barcode).html(quanitythtml);
				pack.showInfo('msgdiv', 'success', '扫码成功，请继续');
				pack.success_sound.play();
			}
		} else {
			$('#scan_' + barcode).html(pack.products[barcode]['scanquantity']);
			pack.showInfo('msgdiv', 'success', '扫码成功，请继续');
			pack.success_sound.play();
		}
	},
	scanComplete: function() {
		var input = 'taskid=' + pack.taskid;
		pack.ajax(pack.completeUrl, input, pack.scanCompleteCallback);
	},
	scanCompleteCallback: function(msg) {
		if (msg.result) {
			pack.showInfo('msgdiv', 'success', msg.text);
			pack.success_sound.play();
		} else {
			pack.showInfo('msgdiv', 'fail', msg.text);
			pack.alarm_sound.play();
		}
	},
	packComplete: function() {
		// Webcam.snap(function (data_uri) {
		// 	camApp.queuePhoto(camApp.count++, data_uri);
		// 	camApp.upload = true;
		// });

		var material = $('#material').text();
		var weight = $('#weight').val();
		var height = $('#height').val();
		var width = $('#width').val();
		var depth = $('#depth').val();

		var input = 'taskid=' + pack.taskid + '&material=' + material + '&weight=' + weight + '&height=' + height + '&width=' + width + '&depth=' + depth;

		pack.ajax(pack.completeUrl, input, pack.packCompleteCallback);
	},
	packCompleteCallback: function(msg) {
		if (msg.result) {
			var parcel = msg.parcel;
			pack.parcels[parcel.id] = {'id':parcel.id, 'taskid':parcel.taskid, 'date':parcel.date, 'weight':parcel.weight, 'material':parcel.material, 'height':parcel.height, 'width':parcel.width, 'depth':parcel.depth};
			pack.parcels.length ++;

			$('#parcellist').append('<tr class=\"success\"><td>' +
				parcel.id.slice(-1) + '</td><td>' +
				parcel.weight + ' kg</td><td>' + parcel.depth + ' cm</td><td> X </td><td>' + parcel.width + ' cm</td><td> X </td><td>' + parcel.height + ' cm</td><td>' + parcel.material + '</td>' +
				'<td><span class="glyphicon glyphicon-trash delete" id="' + parcel.id + '"></span></td></tr>');

			pack.showInfo('msgdiv', 'success', '添加包裹成功');
			$("#collapseParcel").animate({ scrollTop: $('#collapseParcel').prop("scrollHeight")}, 1000);
			pack.success_sound.play();

			$('#weight').val('');
			$('#width').val('');
			$('#depth').val('');
		} else {
			pack.showInfo('msgdiv', 'fail', '添加包裹失败');
			pack.alarm_sound.play();
		}
	},
	printInvoice: function(input) {
		if (pack.labelstatus == 'null') {
			url = pack.printUrl.replace('.app', '/taskid/' + encodeURIComponent(pack.taskid) + '/label/null');
			pack.ajax(url, '', pack.printInvoiceCallback);
		} else if (pack.labelstatus == 'pca') {
			url = pack.printUrl.replace('.app', '/taskid/' + encodeURIComponent(pack.taskid) + '/label/pca');
			if (pack.stockid) {
				url += '/stockid/' + pack.stockid;
			}
			if (pack.parcels.length) {
				pack.ajax(url, '', pack.printInvoiceCallback);
			} else {
				pack.showInfo('msgdiv', 'fail', '请先添加包裹');
				pack.alarm_sound.play();
			}
		} else if (pack.labelstatus == 'other') {
			url = pack.printUrl.replace('.app', '/taskid/' + encodeURIComponent(pack.taskid) + '/label/other');
			if (pack.parcels.length) {
				pack.ajax(url, '', pack.printInvoiceCallback);
			} else {
				pack.showInfo('msgdiv', 'fail', '请先添加包裹');
				pack.alarm_sound.play();
			}
		}
	},
	printInvoiceCallback: function(msg) {
		if (msg.result) {
			pack.showInfo('msgdiv', 'success', msg.msg);
			if (msg.file) {
				pack.lasttaskid = pack.taskid;
				pack.lastboxid = pack.boxid;
				pack.lastfile = msg.file;
				pack.ws.send(msg.file);
			}
			pack.success_sound.play();
			setTimeout(function() {
				pack.partInit();
			}, 1500);
		} else {
			if (msg.st) {
				$('#showDim').trigger('click');
				$('.modal-header').html('<div class="alert alert-danger" style="text-align:center; font-size:32px; margin-top:2px"><span class="glyphicon glyphicon-warning-sign"></span> ' + msg.msg + '</div>');
				body = '<div class="container"><div class="form"><form>';
				for (let parcel in pack.parcels) {
					body += '<div class="row">';
					body += '<div class="col-sm-3" style="margin-right: 4%"><div class="form-group">';
					body += '<h4>长 (cm)</h4><input type="text" name="length[]" class="form-control dim-length" required="required" />';
					body += '</div></div>';
					body += '<div class="col-sm-3" style="margin-right: 4%"><div class="form-group">';
					body += '<h4>宽 (cm)</h4><input type="text" name="width[]" class="form-control dim-width" required="required" />';
					body += '</div></div>';
					body += '<div class="col-sm-3" style="margin-right: 4%"><div class="form-group">';
					body += '<h4>高 (cm)</h4><input type="text" name="height[]" class="form-control dim-height" required="required" />';
					body += '</div></div>';
					body += '</div>';
				}
				body += '<button type="button" class="btn btn-primary" onclick="pack.dim()">打印</button>';
				body += '</form>';
				body += '</div></div>';

				$('.modal-body').html(body);
			} else {
				pack.finishlock = false;
				pack.showInfo('msgdiv', 'fail', msg.msg);
				pack.alarm_sound.play();
			}
		}
	},
	dim: function() {
		url = pack.dimUrl.replace('.app', '/taskid/' + encodeURIComponent(pack.taskid));
		length = [];
		width = [];
		height = [];
		$('input.dim-length').each(function() {
			length.push($(this).val());
		});
		$('input.dim-width').each(function() {
			width.push($(this).val());
		});
		$('input.dim-height').each(function() {
			height.push($(this).val());
		});
		$('#modalClose').trigger('click');
		pack.ajax(url, { 'length': length, 'width': width, 'height': height }, pack.printInvoiceCallback);
	},
	reprintLabel: function() {
		if (pack.lastfile) {
			pack.ws.send(pack.lastfile);
			pack.success_sound.play();
		} else {
			pack.showInfo('msgdiv', 'fail', '文件丢失');
			pack.alarm_sound.play();
		}
	},
	autocomplete: function(obj, type, url) {
		$(obj).autocomplete({
			minLength: 2,
			source: function (request, response) {
				$.ajax({
					type: 'get',
					url: url,
					dataType: 'json',
					data: {
						term: request.term,
						org: $('#org').val()
					},
					success: function (data) {
						if (data.result === true) {
							response($.map(data.items, function(item) {
								return {
									label: item.name + (type === 'product' && item.expiry ? ' (Exp: ' + item.expiry + ')' : ''),
									value: item.name + (type === 'product' && item.expiry ? ' (Exp: ' + item.expiry + ')' : ''),
									other: item
								}
							}));
						} else {
							if (type === 'org') {
								$('#org').val('');
							} else if (type === 'product') {
								$('#product').val('');
							}
							alert('系统内不存在');
						}
					}
				});
			},
			select: function (event, ui) {
				if (type === 'org') {
					$('#org').val(ui.item.other.name);
					pack.orgid = ui.item.other.id;
				} else if (type === 'product') {
					$('#product').val(ui.item.other.name + ' (Exp: ' + ui.item.other.expiry + ')');
					pack.prodid = ui.item.other.id;
				}
				return false;
			}
		});
	},
	generateBarcode: function(url) {
		var box = $('#box').val();
		var quantity = $('#quantity').val();

		url = url.replace('.app', '/prodid/' + encodeURIComponent(pack.prodid) + '/box/' + encodeURIComponent(box) + '/quantity/' + encodeURIComponent(quantity));

		window.open(url);
	},
	checkProduct: function(stockid) {
		var material = $('#material').text();
		var weight = $('#weight').val();

		var input = "taskid=" + pack.taskid + "&stockid=" + stockid + "&material=" + material + "&weight=" + weight;

		pack.ajax(pack.checkUrl, input, pack.checkProductCallback);
	},
	checkProductCallback: function(msg) {
		if (msg.result) {
			var parcel = msg.parcel;
			pack.parcels[parcel.id] = {'id':parcel.id, 'taskid':parcel.taskid, 'date':parcel.date, 'weight':parcel.weight, 'material':parcel.material};
			pack.parcels.length ++;

			$('#parcellist').append('<tr class=\"success\"><td>' + parcel.id + '</td><td>' + parcel.date + '</td><td>' + parcel.weight + ' kg</td><td>' + parcel.material + '</td>');

			pack.showInfo('msgdiv', 'success', '添加包裹成功');
			$("#collapseParcel").animate({ scrollTop: $('#collapseParcel').prop("scrollHeight")}, 1000);
			pack.success_sound.play();
		} else {
			pack.showInfo('msgdiv', 'fail', '添加包裹失败');
			pack.alarm_sound.play();
		}
	},
	transfer: function() {
		var input = 'shipment=' + $('#input').val();
		pack.ajax(pack.completeUrl, input, pack.transferCallback);
	},
	transferCallback: function(msg) {
		if (msg.result) {
			pack.showInfo('msgdiv', 'success', msg.msg);
			if (msg.file) {
				pack.ws.send(msg.file);
			}
			pack.success_sound.play();
			setTimeout(function() {
				pack.partInit();
			}, 3000);
		} else {
			pack.showInfo('msgdiv', 'fail', msg.msg);
			pack.alarm_sound.play();
		}
	},
	boxCallback: function(msg) {
		pack.taskid = msg.taskid;
		var url = pack.detailUrl.replace('.app', '/taskid/' + encodeURIComponent(pack.taskid));
		pack.load(url, 'taskdetail', pack.scanTaskCallback);
	},
	singleItemCallback: function(msg) {
		pack.taskid = msg.taskid;
		if (msg.stockid) {
			pack.stockid = msg.stockid;
		} else {
			pack.stockid = '';
		}
		var url = pack.detailUrl.replace('.app', '/taskid/' + encodeURIComponent(pack.taskid));
		pack.load(url, 'taskdetail', pack.scanTaskCallback);
	},
	holdTask: function() {
		if (confirm('确认要hold订单 ' + pack.taskid + '?')) {
			url = pack.holdTaskUrl.replace('.app', '/taskid/' + encodeURIComponent(pack.taskid));
			pack.ajax(url, {}, pack.holdTaskCallback);
		}
	},
	holdTaskCallback: function(msg) {
		if (msg.result) {
			pack.showInfo('msgdiv', 'success', msg.msg);
			if (msg.file) {
				pack.lasttaskid = pack.taskid;
				pack.lastboxid = pack.boxid;
				pack.lastfile = msg.file;
				pack.ws.send(msg.file);
			}
			pack.success_sound.play();
			setTimeout(function() {
				pack.partInit();
			}, 1500);
		}
	}
};