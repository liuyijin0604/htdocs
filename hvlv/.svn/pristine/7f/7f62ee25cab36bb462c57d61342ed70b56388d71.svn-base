$(document).ready(function(e){
init();
auto();
submit();
});

function init() {
	showHeaders();
	var a = {'good_cost':'Goods Cost','hs_code':'Hs code'}
	for (var n in a) {
			$('.httpparameter:first').clone(true).appendTo("#allparameters").find("label").text(a[n]);
			$('.httpparameter:last').find("input").attr('id',n);
			$('.the-buttom').attr('id','thebuttom');
			$('#good_cost').attr('type','number');
	}
}

function showHeaders() {
	$("#allparameters").show();
}
function removeit(){
	$("#outputpre").empty();
	$("#headerpre").empty();
	$("#statuspre").text("0");
	$("#statuspre").removeClass("alert-success");
	$("#statuspre").removeClass("alert-error");
	$("#statuspre").removeClass("alert-warning");
}

function submit(){
	$("#thebuttom").click(function (e) {
		removeit();
		e.preventDefault();
		if($("#calculatoroption").val()=="Duty"){
			data = createUrlData();
			data['currency']=$("#currency :selected").val();
			data['duty']='1';
			$.ajax({
				url: "hs.php",
				type: "POST",
				data: data,
				complete: function (jqXHR) {
					if(jqXHR.responseText){
						let response=JSON.parse(jqXHR.responseText);
						if(response.msg=='success'){
						 $("#statuspre").text(response.msg);
						 $("#statuspre").addClass("alert-success");
						 let text='HS Code: '+ $("#hs_code").val()+", with value of "+response.good_value+' in Australian Dollars\nDuty: '+response.duty+'(AUS)'+ ', rate:'+ response.rate;
						 $("#outputpre").text(text);
						 
						}else{
						 $("#statuspre").text(response.msg);
						 $("#statuspre").addClass("alert-error");
						}
					}	
				}
			});
		}
		if($("#calculatoroption").val()=="GST"){
			data = createUrlData();
			data['currency']=$("#currency :selected").val();
			data['gst']=1;
			data['delivery']=$("#deliveryOption :selected").val();
			$.ajax({
				url: "hs.php",
				type: "POST",
				data: data,
				complete: function (jqXHR) {
					if(jqXHR.responseText){
						let response=JSON.parse(jqXHR.responseText);
						if(response.msg=='success'){
						 $("#statuspre").text(response.msg);
						 $("#statuspre").addClass("alert-success");
						 let text='HS Code: '+ $("#hs_code").val()+", with value of "+response.good_value+' in Australian Dollars\nGST: '+response.gst+'(AUS)';
						 $("#outputpre").text(text);
						 
						}else{
						 $("#statuspre").text(response.msg);
						 $("#statuspre").addClass("alert-error");
						}
					}	
				}
			});
		}
	});
}


$("#submitajax").click(function (e) {
	e.preventDefault();
		postWithAjax({
			data: createUrlData()
		});
});

function auto(){
$("#hs_code").autocomplete({
	source: function (request, response) {
	  $.ajax({
		url: "hs.php", 
		type: "POST",
		data: {
		  "term": request.term,
		},
		success: function (data) {
		  response(JSON.parse(data));
		}
	  });
	},
	minLength: 3,
	delay: 200,
  });
}


function checkForAuth() {
	return $("#paramform").find("input[type=password]").length > 0;
}

//fetch the data from the form
function createUrlData() {
	var mydata = {};
	var parameters = $("#allparameters").find(".realinputvalue");
	for (i = 0; i < parameters.length; i++) {
		name = $(".realinputvalue").eq(i + 1).attr('id');
		if (name == undefined || name == "undefined") {
			continue;
		}
		value = $(parameters).eq(i).val();
		mydata[name] = value
	}
	return (mydata);
}


// help to calculate the sign 


function httpZeroError() {
	$("#errordiv").append('<div class="alert alert-error"> <a class="close" data-dismiss="alert">&times;</a> <strong>Oh no!</strong> Javascript returned an HTTP 0 error. One common reason this might happen is that you requested a cross-domain resource from a server that did not include the appropriate CORS headers in the response. Better open up your Firebug...</div>');
}


// change the input feilds numbers according to the change of selected ApI website.

$("#calculatoroption").change(function (e) {
	val = $(this).val();
	if (val == "GST") {
		showHeaders();
		number = $("#allparameters").find(".realinputvalue").length;
		for (i = 0; i < number; i++) {
			$('.httpparameter:last').remove();
		}

		var a = {'good_cost':'Goods Cost','hs_code':'Hs code'}
		for (var n in a) {
				$('.httpparameter:first').clone(true).appendTo("#allparameters").find("label").text(a[n]);
				$('.httpparameter:last').find("input").attr('id',n);
				$('.the-buttom').attr('id','thebuttom');
				$('#good_cost').attr('type','number');
		}
		$('.delivery:first').clone(true).appendTo("#allparameters");
		var a = {'good_weight':'Chargeable weight(Kg)','good_cbm':"Chargeable cbm(M3)",'delivery_rate':'Air/Sea Freight rate(Per Kg USD)'}
		for (var n in a) {
				$('.httpparameter:first').clone(true).appendTo("#allparameters").find("label").text(a[n]);
				$('.httpparameter:last').find("input").attr('id',n);
				$('.the-buttom').attr('id','thebuttom');
			}
		$('#good_cbm').attr('type','number');
		$('#good_weight').attr('type','number');
		$('#delivery_rate').attr('type','number');
	}
	if (val == "Duty") {
		showHeaders();
		number = $("#allparameters").find(".realinputvalue").length;
		$('.delivery:last').remove();
		for (i = 0; i < number; i++) {
			$('.httpparameter:last').remove();
		}

		var a = {'good_cost':'Goods Cost','hs_code':'Hs code'}
		for (var n in a) {
				$('.httpparameter:first').clone(true).appendTo("#allparameters").find("label").text(a[n]);
				$('.httpparameter:last').find("input").attr('id',n);
				$('.the-buttom').attr('id','thebuttom');
				$('#good_weight').attr('type','number');
				$('#delivery_rate').attr('type','number');
		}
	}
	removeit();
	auto();
});

	$("#deliveryOption").change(function (e) {
		val = $(this).val();
		if (val == "air") {
			showHeaders();
			number = $("#allparameters").find(".realinputvalue").length;
			for (i = 0; i < number-2; i++) {
				$('.httpparameter:last').remove();
			}
	
			var a = {'good_weight':'Chargeable weight(Kg)','good_cbm':"Chargeable cbm(M3)",'delivery_rate':'Air/Sea Freight rate(Per Kg USD)'}
			for (var n in a) {
					$('.httpparameter:first').clone(true).appendTo("#allparameters").find("label").text(a[n]);
					$('.httpparameter:last').find("input").attr('id',n);
					$('.the-buttom').attr('id','thebuttom');
					$('#good_weight').attr('type','number');
					$('#delivery_rate').attr('type','number');
			}
		}
		if (val == "sea") {
			showHeaders();
			number = $("#allparameters").find(".realinputvalue").length;
			for (i = 0; i < number-2; i++) {
				$('.httpparameter:last').remove();
			}
			var a = {'good_weight':'Chargeable weight(Kg)','good_cbm':"Chargeable cbm(M3)",'delivery_rate':'Air/Sea Freight rate(Per Cubic Meter USD)'}
			for (var n in a) {
					$('.httpparameter:first').clone(true).appendTo("#allparameters").find("label").text(a[n]);
					$('.httpparameter:last').find("input").attr('id',n);
					$('.the-buttom').attr('id','thebuttom');
					$('#good_weight').attr('type','number');
					$('#delivery_rate').attr('type','number');
			}
		}
});