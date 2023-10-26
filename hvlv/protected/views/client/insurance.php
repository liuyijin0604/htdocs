<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<meta name="language" content="en" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="../css/pos.css" />
	<!--[if lt IE 10]>
	<style type="text/css">
		.placeholder { color: #999; }
	</style>
	<![endif]-->
	<title>PCA Express - Insurance Purchase</title>
<style type="text/css">
.uk-container {
    background: #fff none repeat scroll 0 0;
    box-sizing: border-box;
    margin: 0 auto;
    max-width: 1200px;
    padding: 0 20px;
}
.tm-headerbar{
	margin: 25px 0 15px 0;
}
ol {
  list-style-type: none;
  counter-reset: item;
  margin: 0;
  padding: 0;
}

ol > li {
  display: table;
  counter-increment: item;
  margin-bottom: 0.6em;
}

ol > li:before {
  content: counters(item, ".") ". ";
  display: table-cell;
  padding-right: 0.6em;
}

ol.ol_alpha{
	list-style-type: upper-alpha;
}
ol.ol_alpha > li:before{
	content: counter(item, upper-alpha) ". ";
}

li ol > li {
  margin: 0;
}
</style>
</head>

<body>
<div class="uk-container" id="page" style="padding: 0 20px">
<div class="tm-headerbar">
<img width="240" alt="Logo" src="../images/PCAE_Logo.png" />
</div>
<div class="hredbar gradient fullspan" style="margin: 0 -20px"></div>
	<div id="mainmenu">
			</div><!-- mainmenu -->
			<!-- breadcrumbs -->
	<div id="content">
	<div style="padding: 20px 0">
	<h1 class="uk-article-title">保险购买</h1>
	<form method="post" action="">
	<table id="tbl_isr" class="table table-striped" style="width: 400px">
	<thead><tr><th>#</th><th width="200">运单号</th><th width="100">保额(AUD)</th><th width="100">保金(AUD)</th></tr></thead>
	<tbody>
	</tbody>
	<tfoot>
		<tr><td><button class="add btn btn-info"><b>+</b></button></td><td align="right">总额:</td><td class="t1"></td><td class="t2"></td></tr>
	</tfoot>
	</table>
	<h3>付款方式</h3>
	<label><input type="radio" name="pm" value="poli" checked /> POLi</label><br />
	<label><input type="radio" name="pm" value="paypal" /> Paypal/信用卡 (+2% 服务费)</label>
	<br />
	<input type="submit" name="submit" value="购买" class="btn btn-primary btn-lg" />
	</form>
	<br />
	<br />
	<div><h3>PCA Express 承运条款</h3>
<ol>
  <li>包装：寄件人清楚知悉国际物流路途遥远、环节繁多，确保所投寄的物品妥善并加固包装，已确保货物安全。遵守公司打包规则并且不会载有任何危险品、违禁品或者受限制物品。</li>
  <li>清关：寄件人需真实准确地填写收件人的个人信息和包裹内物件的详细信息，并提供所需清关证件（身份证）。</li>
  <li>保险：请寄件人尽量选择购买保险，保险费用按照货物实际价值的3%计费并在投寄货物24小时内支付，每票货物投保价值不超过AUD $500。</li>
  <li>货物遗失、损毁或延误的赔偿责任
  <ol>
    <li>赔偿
    <ol class="ol_alpha">
      <li>投保物品整票的丢失可根据投保金额进行赔偿，每票货物赔偿金最高不超于AUD $500。</li>
      <li>未投保物品的丢失，本公司一律不赔偿货物价值，只赔付运费。</li>
      <li>任何索赔须在PCA  Express 接受快件30天内或者签收7天内提交书面材料申请理赔，否则视为放弃索赔权。</li>
    </ol></li>
    <li>如发生以下事项，本公司不予赔偿： 
    <ol class="ol_alpha">
      <li>物品（包含投保物品）如发生内件丢失，破损，变形，变质。</li>
      <li>收件人信息提供不清晰、不完整或错误而引致延误或无法投递。</li>
      <li>由于寄件人申报内容不清晰、不真实，没有提供清关证明（身份证），导致清关延误或没收。</li>
      <li>海关查验导致的延迟。</li>
      <li>航班延误导致的延迟。</li>
      <li>因重大交通事故、自然灾害等不可抗力造成的损失、破环、延误。</li>
    </ol></li>
  </ol>
  </li>
  <li>本公司有权拒绝及放弃任何违反公司规定的物品，并保留在运输过程中对航线的选择。</li>
  <li>在可适用范围内华沙公约及其后续任何修正案中有关责任的规则适用于被承担条款中任何货品的国际运输。</li>
  <li>本公司保留对此运输条款的解释权和修改权。</li>
  <li>本公司有权对以上条款进行修改，更新将在生效日前发布于公司网站，恕不另行通知。</li>
</ol>
</div>
<div class="clear"></div>
</div><!-- page -->
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../js/bootstrap.min.js"></script>
<script type="text/javascript">
$(function(){
	var t = $('#tbl_isr');

	var addLine = function(){
		var c = $('tbody tr', t).length;
		if(c > 100) return;
		var a = '<tr><td class="sn">'+(c + 1)+'</td><td><input class="hbn" name="hbn[]" /></td><td><input class="ina" type="number" name="ina[]" size="5" min="20" max="500" style="width:80px" /></td><td class="insure"></td></tr>';
		$('tbody', t).append($(a));
		return false;
	};
	
	for(var i = 0; i < 5; i++) addLine();

	$('button.add').on('click', addLine);
	
	var calcTot = function(){
		var tt = 0;
		var ti = 0;
		$('tbody tr', t).each(function(){
			var s = $('input.ina', this).val();
			if(s == '' || s == 0){
				$('td.insure', this).html('');
			}else{
				var i = Math.round(s*3) / 100;
				if(i < 1.5) i = 1.5;
				$('td.insure', this).html(i);
				tt += Number(s);
				ti += Number(i);
			}
		});
		$('.t1', t).html(tt);
		$('.t2', t).html(ti);
	};
	$('input.ina', t).on('change', calcTot);
});
</script>
</body>
</html>
