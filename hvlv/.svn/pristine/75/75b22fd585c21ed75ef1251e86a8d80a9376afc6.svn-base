<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'shipment-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' =>[
		'data-bit' => '0',
	]
));

if(empty($model->cnor)){
	$model->cnor = new Addr;
	$model->cnor->country = 'Australia';
}
if(empty($model->cnee)){
	$model->cnee = new Addr;
	$model->cnee->country = 'PR China';
}
?>
	<h2><?=$org->name;?></h2>
	<div class="row">
	<div class="col col-md-6 col-sm-12">
	<h3><?=$this->t('Shipper');?></h3>
	<div class="row">
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'name'),
		CHtml::textField('Cnor[name]', $model->cnor->name, array('size'=>20, 'class' => 'form-control'));?>
	</div>
	</div>
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'tel'),
		CHtml::textField('Cnor[tel]', $model->cnor->tel, array('size'=>20, 'class' => 'form-control'));?>
	</div>
	</div>
	</div>
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'address'),
		CHtml::textField('Cnor[address]', $model->cnor->address, array('size'=>40, 'class' => 'form-control'));?>
	</div>

	<div class="row">
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'suburb');
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'Cnor_sub_ac',
				'sourceUrl' => array('shipment/auPcSuggest'),
				'value' => ($model->cnor->suburb) ? $model->cnor->suburb : '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'autoFocus' => true,
						'select' => 'js:function(evt, ui){ $(this).val(ui.item["value"]); $(this).trigger("ac_after_select", ui); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
					'name' => 'Cnor[suburb]',
					'class' => 'form-control',
				),
		));
		?>
	</div>
	</div>
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'state'),
		CHtml::dropDownList('Cnor[state]', $model->cnor->state, array('ACT' => 'ACT - Australia Capital Territory', 'NSW' => 'NSW - New South Wales', 'NT' => 'NT - Northern Territory', 'QLD' => 'QLD - Queensland', 'SA' => 'SA - South Australia', 'TAS' => 'TAS - Tasmania', 'VIC' => 'VIC - Victoria', 'WA' => 'WA - Western Australia'), array('empty' => $this->t('Select One'), 'class' => 'form-control')); ?>
	</div>
	</div>
	</div>

	<div class="row">
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'postcode'),
		CHtml::textField('Cnor[postcode]', $model->cnor->postcode, array('size'=>15, 'class' => 'form-control')); ?>
	</div>
	</div>
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'country'),
		CHtml::textField('Cnor[country]', $model->cnor->country, array('size'=>15, 'class' => 'form-control')); ?>
	</div>
	</div>
	</div>
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'email'),
		CHtml::textField('Cnor[email]', $model->cnor->email, array('size'=>30, 'class' => 'form-control')); ?>
	</div>
	</div>
	<div class="col col-md-6 col-sm-12">
	<div class="row">
		<h3 class="pull-left" style="margin: 20px 15px 10px 15px;"><?=$this->t('Consignee');?></h3>
		<div class="dropdown" id="cnee_fill" style="display: none; margin-top: 15px;">
  <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
	<span class="glyphicon glyphicon-flash"></span> <?=$this->t('Quick Fill');?>
	<span class="caret"></span>
  </button>
  <ul class="dropdown-menu" id="cnees_list" aria-labelledby="dropdownMenu1"></ul>
</div>
	</div>

	<div class="row">
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'name'),
		CHtml::textField('Cnee[name]', $model->cnee->name, array('size'=>15, 'class' => 'form-control'));?>
	</div>
	</div>
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'tel'),
		CHtml::textField('Cnee[tel]', $model->cnee->tel, array('size'=>15, 'class' => 'form-control')); ?>
	</div>
	</div>
	</div>

	<div class="row">
	<div class="col col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'state');
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => 'Cnee_state_ac',
					'source' => 'js:function(q,r){ var t = q.term.replace(/\s+/, ""), d = '.json_encode(AppHelper::cnProvince()).', m = new RegExp($.ui.autocomplete.escapeRegex(t), "i"); r($.map(d, function(el){ if(m.test(el.label)){ return el; } })); }',
					'value' => empty($model->cnee->state)? '' : $model->cnee->state,
					'options' => array(
							'showAnim' => 'fold',
							'autoFocus' => true,
							'minLength' => 0,
							'delay' => 0,
							'select' => 'js:function(evt, ui){ $(this).blur().trigger("ac_after_select", ui); return false;}',
							'response' => 'js:function(evt, ui){ if(ui.content.length == 0){ $(this).data("sid", 0); }; return false; }',
							'change' => 'js:function(evt, ui){ if($(this).data("sid") == 0) $(this).val(""); return false; }',
					),
					'htmlOptions' => array(
						'size' => '15',
						'name' => 'Cnee[state]',
						'class' => 'form-control',
					),
			));
		?>
	</div>
	</div>
	<div class="col col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'city');
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => 'Cnee_city_ac',
					'sourceUrl' => array('shipment/cnCitySuggest'),
					'value' => empty($model->cnee->city)? '' : $model->cnee->city,
					'options' => array(
							'showAnim' => 'fold',
							'minLength' => 0,
							'delay' => 100,
							'autoFocus' => true,
							'select' => 'js:function(evt, ui){ $(this).trigger("ac_after_select", ui); return false;}',
							'response' => 'js:function(evt, ui){ if(ui.content.length == 0){ $(this).data("cid", 0); }; return false; }',
							'change' => 'js:function(evt, ui){ if($(this).data("cid") == 0) $(this).val(""); return false; }',
					),
					'htmlOptions' => array(
						'size' => '15',
						'name' => 'Cnee[city]',
						'class' => 'form-control',
					),
			));
		?>
	</div>
	</div>

	<div class="col col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'suburb');
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => 'Cnee_suburb_ac',
					'sourceUrl' => array('shipment/cnSuburbSuggest'),
					'value' => empty($model->cnee->suburb)? '' : $model->cnee->suburb,
					'options' => array(
							'showAnim' => 'fold',
							'minLength' => 0,
							'delay' => 100,
							'select' => 'js:function(evt, ui){ $(this).trigger("ac_after_select", ui); return false; }',
					),
					'htmlOptions' => array(
						'size' => '15',
						'name' => 'Cnee[suburb]',
						'class' => 'form-control',
					),
			)); ?>
	</div>
	</div>
	</div>
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'address'),
		CHtml::textField('Cnee[address]', $model->cnee->address, array('size'=>40, 'class' => 'form-control')); ?>
	</div>
	

	<div class="row">
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'postcode'),
		CHtml::textField('Cnee[postcode]', $model->cnee->postcode, array('size'=>15, 'class' => 'form-control')); ?>
	</div>
	</div>
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'country'),
		CHtml::textField('Cnee[country]', $model->cnee->country, array('size'=>15, 'class' => 'form-control')); ?>
	</div>
	</div>
	</div>
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'email'),
		CHtml::textField('Cnee[email]', $model->cnee->email, array('size'=>30, 'class' => 'form-control')); ?>
	</div>
	</div>
	</div>

	<div class="form-group table-responsive">
	<table id="items" class="table table-striped table-bordered" style="min-width:380px">
	<thead>
	<tr><th>#</th><th><?=$this->t('Type');?> <span class="required">*</span></th><th style="min-width:120px"><?=$this->t('Item Name');?> <span class="required">*</span></th><th style="min-width:60px"><?=$this->t('Qty');?>*</th><th style="min-width:70px"><?=$this->t('Value');?></th></tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
	<tr><td colspan="2"><button class="moreitem btn btn-normal"><span class="glyphicon glyphicon-plus" style="color: #be3426; font-size: 1.2em; padding: 3px 10px;"></span></button></td><th class="tright"><?=$this->t('Total');?>:</th><th id="tot_qty"></th><th id="tot_value">&nbsp;</th></tr>
	</tfoot>
	</table>
	</div>

	<div class="row">
	<div class="col col-md-2 col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'weight');?>
		<div class="input-group">
			<?php echo $form->textField($model,'weight',array('size'=>10,'maxlength'=>10, 'class' => 'form-control', 'id' => 'ExParcel_weight')); ?>
			<div class="input-group-addon">kg</div>
		</div>
	</div>
	</div>

	<div class="col col-md-2 col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'insurance'); ?>
		<div class="input-group">
			<div class="input-group-addon">$</div>
			<?php echo $form->textField($model,'insurance',array('size'=>6,'maxlength'=>10, 'class' => 'form-control')); ?>
		</div>
	</div>
	</div>

	</div>

	<div class="form-group">
		<label><?=$this->t('Captcha');?></label>
		<div class="row">
		<div class="col col-sm-3 col-xs-6">
			<input type="text" class="form-control input-lg" id="vvc" name="vvc" autocomplete="off" />
		</div>
		<div class="col col-sm-3 col-xs-6">
			<img style="cursor:pointer;" alt="CAPTCHA" title="Click to reload" id="lf_capcha" src="<?=$this->createUrl('site/captcha').'?'.time();?>" />
		</div>
		</div>
	</div>
	
	<div class="form-group buttons">
		<button type="submit" class="btn btn-primary btn-lg"><?=$this->t('Create');?></button>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php ob_start(); ?>
<script type="text/javascript">
//rhaboo
!function a(b,c,d){function e(g,h){if(!c[g]){if(!b[g]){var i="function"==typeof require&&require;if(!h&&i)return i(g,!0);if(f)return f(g,!0);var j=new Error("Cannot find module '"+g+"'");throw j.code="MODULE_NOT_FOUND",j}var k=c[g]={exports:{}};b[g][0].call(k.exports,function(a){var c=b[g][1][a];return e(c?c:a)},k,k.exports,a,b,c,d)}return c[g].exports}for(var f="function"==typeof require&&require,g=0;g<d.length;g++)e(d[g]);return e}({1:[function(a,b){"use strict";function c(a,b){var c=a.split(b);return 1==c.length&&void 0==c[0]&&delete c[0],c}var d=function(a){return null===a?"null":typeof a},e=function(a){return a},f=function(a){return function(){return a}},g=function(a){return function(b){return a===b}},h=function(a){return function(b){return b.map(a)}},i=function(a){return function(b){return void 0!==a[1]?[a[0],a[1](b)]:[a[0]]}},j=function(a){return function(b){return function(c){for(var d=[],e=0;e<b.length&&e<c.length;e++)d[e]=b[e](a)(c[e]);return d}}},k=function(){return function(a){return a.toString()}},l=function(a){return function(b){return a?b.toString():Number(b)}},m=function(a){return function(b){return a?b?"t":"f":"t"===b}},n=function(a){return function(){return a?"":null}},o=function(a){return function(){return a?"":void 0}},p=function(a){return function(b){return function(c){return function(d){return c?b(c)(a(c)(d)):a(c)(b(c)(d))}}}},q=function(a){return function(b){for(var c=[],d=0,d=0;d<a.length&&b.length;b=b.substring(a[d++]))c.push(b.substring(0,a[d]));return c.push(b),c}},r=function(a){return function(b){return function(c){return function(d){return c?j(c)(b)(d).join(""):j(c)(b)(q(a)(d))}}}},s=function(a){return function(b){return function(d){return function(e){return d?j(d)(b)(e).join(a):j(d)(b)(c(e,a))}}}},t=function(a,b){return function(c){return function(d){return function(e){return d?c(d)(e).replace(RegExp(a,"g"),a+b):c(d)(e.replace(RegExp(a+b,"g"),a))}}}},u=function(a,b){return function(c){return function(d){return function(e){return s(d?a:RegExp(a+"(?!"+b+")","g"))(h(t(a,b))(c))(d)(e)}}}},v=u(";",":");b.exports={typeOf:d,id:e,konst:f,eq:g,map:h,runSnd:i,string_pp:k,number_pp:l,boolean_pp:m,null_pp:n,undefined_pp:o,thru:j,chop:q,fixedWidth:r,sepBy:s,escape:t,sepByEsc:u,tuple:v,pipe:p}},{}],2:[function(a,b){var c=a("./core"),d=d||{pop:Array.prototype.pop,push:Array.prototype.push,shift:Array.prototype.shift,unshift:Array.prototype.unshift,splice:Array.prototype.splice,reverse:Array.prototype.reverse,sort:Array.prototype.sort,fill:Array.prototype.fill},e=function(a){return function(){var b,e,f=void 0;this._rhaboo&&(e=this._rhaboo.storage,f=this._rhaboo.slotnum,b=this._rhaboo.refs,c.release(this,!0));var g=d[a].apply(this,arguments);return void 0!==f&&c.addRef(this,e,f,b),g}};Array.prototype.push=function(){var a=this.length,b=d.push.apply(this,arguments),e=this.length;if(void 0!==this._rhaboo&&e>a){for(var f=a;e>f;f++)c.storeProp(this,f);c.updateSlot(this)}return b},Array.prototype.pop=function(){var a=this.length;void 0!==this._rhaboo&&a>0&&c.forgetProp(this,a-1);var b=d.pop.apply(this,arguments);return void 0!==this._rhaboo&&a>0&&c.updateSlot(this),b},Object.defineProperty(Array.prototype,"write",{value:function(a,b){Object.prototype.write.call(this,a,b),c.updateSlot(this)}}),Array.prototype.shift=e("shift"),Array.prototype.unshift=e("unshift"),Array.prototype.splice=e("splice"),Array.prototype.reverse=e("reverse"),Array.prototype.sort=e("sort"),Array.prototype.fill=e("fill"),b.exports={persistent:c.persistent,perishable:c.perishable,algorithm:"sand"}},{"./core":3}],3:[function(a,b){(function(c){"use strict";function d(a){return f(localStorage,a)}function e(a){return f(sessionStorage,a)}function f(a,b){var c=a.getItem(s+b);if(c){var d=A(a)(!1)(c);return t={},d[0]}var e={};Object.defineProperty(e,"_rhaboo",{value:{storage:a},writable:!0,configurable:!0,enumerable:!1});var f=n(e);return a.setItem(s+b,y(a)(!0)(f)),f}function g(a){return function(b){if(void 0!==t[b])return n(t[b]);var c=a.getItem(s+b);if(void 0===c||null===c)return void 0;var d=z(!1)(c);return Object.defineProperty(d[0],"_rhaboo",{value:{storage:a,slotnum:b,refs:1,kids:{}},writable:!0,configurable:!0,enumerable:!1}),t[b]=d[0],d.length>1?h(d[0],d[1][0],d[1][1]):d[0]}}function h(a,b,c){var d=a._rhaboo.storage.getItem(s+c);if(void 0===d||null===d)return a;var e=A(a._rhaboo.storage)(!1)(d);return a[b]=e[0],i(a,b,c),e.length>1?h(a,e[1][0],e[1][1]):a}function i(a,b,c){var d=void 0!==a._rhaboo.prev?a._rhaboo.kids[a._rhaboo.prev]:a._rhaboo;a._rhaboo.kids[b]={slotnum:c,prev:a._rhaboo.prev},d.next=a._rhaboo.prev=b}function j(a,b){var c=a._rhaboo.kids[b];(a._rhaboo.kids[c.prev]||a._rhaboo).next=c.next,(a._rhaboo.kids[c.next]||a._rhaboo).prev=c.prev,delete a._rhaboo.kids[b]}function k(a,b){var c=[];c.push(void 0!==b?a[b]:a);var d=void 0!==b?a._rhaboo.kids[b]:a._rhaboo;void 0!==d.next&&c.push([d.next,a._rhaboo.kids[d.next].slotnum]);var e=(void 0!==b?A(a._rhaboo.storage):z)(!0)(c);try{a._rhaboo.storage.setItem(s+d.slotnum,e)}catch(f){throw l(f)&&a._rhaboo.storage.removeItem(s+d.slotnum,e),console.log("Local storage quota exceeded by rhaboo"),f}}function l(a){var b=!1;if(a)if(a.code)switch(a.code){case 22:b=!0;break;case 1014:"NS_ERROR_DOM_QUOTA_REACHED"===a.name&&(b=!0)}else-2147024882===a.number&&(b=!0);return b}function m(a,b){if(void 0===a._rhaboo.kids[b]){var c=r(a._rhaboo.storage);i(a,b,c),k(a,a._rhaboo.kids[b].prev)}}function n(a,b,c,d){if(void 0!==a._rhaboo&&void 0!==a._rhaboo.slotnum)a._rhaboo.refs++;else{void 0===a._rhaboo&&Object.defineProperty(a,"_rhaboo",{value:{},writable:!0,configurable:!0,enumerable:!1}),void 0!==b&&(a._rhaboo.storage=b),a._rhaboo.slotnum=void 0!==c?c:r(a._rhaboo.storage),a._rhaboo.refs=void 0!==d?d:1,a._rhaboo.kids={},k(a);for(var e in a)a.hasOwnProperty(e)&&"_rhaboo"!==e&&o(a,e)}return a}function o(a,b){m(a,b),"object"===u.typeOf(a[b])&&(void 0===a[b]._rhaboo&&Object.defineProperty(a[b],"_rhaboo",{value:{storage:a._rhaboo.storage},writable:!0,configurable:!0,enumerable:!1}),n(a[b])),k(a,b)}function p(a,b){var c,d;if(a._rhaboo.refs--,b||0===a._rhaboo.refs){for(d=void 0,c=a._rhaboo;c;c=a._rhaboo.kids[d=c.next])a._rhaboo.storage.removeItem(s+c.slotnum),void 0!==d&&"object"==u.typeOf(a[d])&&p(a[d]);delete a._rhaboo}}function q(a,b){var c=a._rhaboo.kids[b];if(void 0!==c){var d=c.prev;a._rhaboo.storage.removeItem(s+c.slotnum),"object"==u.typeOf(a[b])&&p(a[b]),j(a,b),k(a,d)}}function r(a){var b=a===localStorage?0:1,c=C[b];return C[b]++,a.setItem(B,C[b]),c}void 0===Function.prototype.name&&void 0!==Object.defineProperty&&Object.defineProperty(Function.prototype,"name",{get:function(){var a=/function\s([^(]{1,})\(/,b=a.exec(this.toString());return b&&b.length>1?b[1].trim():""},set:function(){}});var s="_rhaboo_",t={},u=a("parunpar"),v=u.sepByEsc("=",":"),w=v([u.string_pp,u.number_pp]),x=u.pipe(function(a){return function(b){return a?"Date"===b.constructor.name?[b.constructor.name,b.toString()]:void 0!==b.length?[b.constructor.name,b.length.toString()]:[b.constructor.name]:new c[b[0]]("Date"==b[0]?b[1]:b[1]?Number(b[1]):void 0)}})(v([u.string_pp,u.string_pp])),y=function(a){return u.pipe(function(b){return function(c){return b?u.runSnd({string:["$",u.id],number:["#",String],"boolean":["?",function(a){return a?"t":"f"}],"null":["~"],undefined:["_"],object:["&",function(a){return a._rhaboo.slotnum}]}[u.typeOf(c)])(c):{$:u.id,"#":Number,"?":u.eq("t"),"~":u.konst(null),_:u.konst(void 0),"&":g(a)}[c[0]](c[1])}})(u.fixedWidth([1])([u.string_pp,u.string_pp]))},z=u.tuple([x,w]),A=function(a){return u.tuple([y(a),w])};Object.defineProperty(Object.prototype,"write",{value:function(a,b){return m(this,a),"object"===u.typeOf(this[a])&&p(this[a]),this[a]=b,"object"===u.typeOf(b)&&(void 0===b._rhaboo&&Object.defineProperty(b,"_rhaboo",{value:{storage:this._rhaboo.storage},writable:!0,configurable:!0,enumerable:!1}),n(b)),k(this,a),this}}),Object.defineProperty(Object.prototype,"erase",{value:function(a){if(!this.hasOwnProperty(a))return this;"object"===u.typeOf(this[a])&&p(this[a]);var b=this._rhaboo.kids[a];this._rhaboo.storage.removeItem(s+b.slotnum);var c=b.prev;return j(this,a),k(this,c),delete this[a],this}});for(var B="_RHABOO_NEXT_SLOT",C=[0,0],D=0;2>D;D++)C[D]=localStorage.getItem(B)||0,C[D]=Number(C[D]);b.exports={persistent:d,perishable:e,addRef:n,release:p,storeProp:o,forgetProp:q,updateSlot:k}}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{parunpar:1}],4:[function(a){(function(b){b.Rhaboo=a("./arr")}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{"./arr":2}]},{},[4]);

$(function(){
	var pitems = {};
	var addItem = function(add){
		var tb = $('#items tbody');
		var id = $('tr', tb).length;
		var add = add || 1;
		while(add-- > 0){
			tb.append('<tr class="'+(id%2==0? 'even' : 'odd')+'"><td class="rid">'+(id+1)+'</td><td><select class="typsel form-control" name="items[type]['+id+']" data-ov="'+(pitems.type && pitems.type[id]? pitems.type[id] : '')+'"><option value=""><?=$this->t("Select One");?></option><option value="B"><?=$this->t("Baby Formula");?></option><option value="M"><?=$this->t("Milk Powder");?></option><option value="O"><?=$this->t("Other");?></option></select></td><td><input type="text" class="item_name'+(pitems.g && pitems.g[id] === false? ' error' : '')+' form-control" name="items[g]['+id+']" size="25" value="'+(pitems.g && pitems.g[id]? pitems.g[id] : '')+'" /></td><td><input type="text" class="item_qty'+(pitems.q && pitems.q[id] === false? ' error' : '')+' form-control" name="items[q]['+id+']" size="3" value="'+(pitems.q && pitems.q[id]? pitems.q[id]: '')+'" /></td><td><input type="text" class="item_tv'+(pitems.v && pitems.v[id] === false? ' error' : '')+' form-control" name="items[v]['+id+']" size="6" value="'+(pitems.v && pitems.v[id]? pitems.v[id]: '')+'" /></td></tr>');
			id++;
		}
		$('select.typsel', tb).each(function(){
			if($(this).data('ov') != ''){
				$(this).val($(this).data('ov'));
			}
		});
		calcTot();
	};
	var calcTot = function(){
		var tqty = 0;

		$('#items tbody tr').each(function(){
			tqty += Number($('.item_qty', this).val()) || 0;
		});
		
		$('#tot_qty').text(tqty);
	};

	posApp.pleaseWait = '<?=$this->t("Please Wait");?>';
	
	//required fields
	$('#Cnee_name, #ExParcel_weight, #Cnor_name, #Cnor_tel, #Cnee_tel, #Cnee_address, #Cnee_state_ac, #Cnee_city_ac, #Cnee_postcode, #vvc').off('change').on('change', function(){
		if($(this).val() == '' || $(this).val() == 0){
			$(this).parents('.form-group').addClass('has-warning').removeClass('has-success');
		}else{
			$(this).parents('.form-group').removeClass('has-warning').addClass('has-success');
		}
	}).trigger('change');

	$('#items').off('change', '.item_qty,.item_wt,.item_tv,.item_tax').on('change', '.item_qty,.item_wt,.item_tv,.item_tax', calcTot);

	$('#items').off('keydown', 'input[type=text]').on('keydown', 'input[type=text]', function(evt){
			if(evt.keyCode == 13){
				if(Number($(this).parents('tr').find('.rid').text()) == $('#items tbody tr').length) addItem(1);
				$(this).parents('tr').next().find('.typsel').focus();
				return false;
			}
	});
	$('.moreitem').off('click').on('click', function(e){
		addItem(1);
		$(this).parents('tr').next().find('.typsel').focus();
		e.preventDefault();
	});

	addItem(pitems.g? pitems.g.length : 1);

	//ac baseurl
	$('#Cnee_city_ac, #Cnee_suburb_ac').on('autocompletecreate', function(){
		$(this).data('src', $(this).autocomplete('option', 'source'));
	});

	var cnee_state = $('#Cnee_state_ac');

	//cnor name/mobile ac

	//cnor suburb ac
	$('#Cnor_sub_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		$("#Cnor_postcode").val(ui.item.pc);
		$('#Cnor_state').val(ui.item.st);
	});

	//cnee name/mobile ac

	//cnee state
	cnee_state.off('ac_after_select').on('ac_after_select', function(evt, ui){
		$(this).val('').val(ui.item.value).data('sid', ui.item.id);
		if(ui.item.ocid){
			$('#Cnee_city_ac').val(ui.item.value).data('cid', ui.item.ocid).focus();
			$('#Cnee_postcode').val(ui.item.oczip);
		}
	}).on('focus',function(){
		$(this).autocomplete('search', $(this).val());
	});

	var cneeSid = function(){
		var v = cnee_state.val();
		if(v != ''){
			var s = cnee_state.autocomplete('option', 'source');
			for(i in s){
				if(s[i].value == v) cnee_state.data('sid', s[i].id);
			}
		}
	};

	//cnee city
	$('#Cnee_city_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		$(this).val(ui.item.value).data('cid', ui.item.id);
		if(ui.item.aname){
			$('#Cnee_suburb_ac').val(ui.item.aname);
		}
		if(ui.item.zip){
			$('#Cnee_postcode').val(ui.item.zip);
		}
	}).on('focus', function(){
		cneeSid();
		if(!$(this).data('src')) $(this).data('src', $(this).autocomplete('option', 'source'));
		$(this).autocomplete({source : $(this).data('src')+'?sid='+cnee_state.data('sid')}).autocomplete('search', $(this).val());
	}).on( "autocompletesearch", function(e, u){
		if(cnee_state.val() == '') return false;
	});

	//cnee suburb
	$('#Cnee_suburb_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		$(this).val(ui.item.value);
		$('#Cnee_postcode').val(ui.item.zip);
	}).on('focus', function(){
		if(!$(this).data('src')) $(this).data('src', $(this).autocomplete('option', 'source'));
		$(this).autocomplete({source : $(this).data('src')+'?cid='+$('#Cnee_city_ac').data('cid')}).autocomplete('search', $(this).val());
	}).on( "autocompletesearch", function(e, u){
		if($('#Cnee_city_ac').val() == '') return false;
	});

	//product ac
	$('#items').off('focus', '.item_name').on('focus', '.item_name', function(){
		var t = $(this);
		var tp = $('select.typsel', t.parents('tr'));
		if(!t.data('ac_inited')){
			t.autocomplete({
				'showAnim':'fold',
				'minLength':1,
				'delay':200,
				'autoFocus':true,
				'source' : posApp.baseUrl+'shipment/prodSuggest',
				'messages': {
					'noResults': '',
					'results': function() {}
				}
			}).on( "autocompletesearch", function(e, u){
				if(tp.val() == '') return false;
			});
			t.data({'ac_inited': true, 'src': posApp.baseUrl+'shipment/prodSuggest'});
		}
		$(this).autocomplete({source : $(this).data('src')+'?t='+tp.val()});
	});

	//tabindex control
	$('#Cnor_name').focus();

	$('form input, form select').off('keydown').on('keydown', function(evt){
		if(evt.keyCode == 9 && !evt.shiftKey){
			var j = false;
			switch($(this).attr('id')){
				case 'ExParcel_hbn':
					j = '#ExParcel_weight';
				break;
				break;
				case 'Cnor_tel':
					j = '#Cnee_name';
				break;
				case 'Cnee_address':
					j = '#items .typsel:first-of-type';
				break;
			}
			if(j){
				$(j).focus();
				return false;
			}
		}
	});
	
	var rpms = Rhaboo.persistent("myshipments");
	
	//preserve
	$('#shipment-form').on('success', function(evt, r){
		rpms.write('Cnor', {name: $('#Cnor_name').val(), tel: $('#Cnor_tel').val(), address: $('#Cnor_address').val(), 'sub_ac': $('#Cnor_sub_ac').val(), 'state': $('#Cnor_state').val(), 'postcode': $('#Cnor_postcode').val(), 'country': $('#Cnor_country').val(), 'email': $('#Cnor_email').val()});

		var cnees = rpms.Cnees || [];
		var ncnee = {name: $('#Cnee_name').val(), tel: $('#Cnee_tel').val(), address: $('#Cnee_address').val(), 'suburb_ac': $('#Cnee_suburb_ac').val(), 'state_ac': $('#Cnee_state_ac').val(), 'city_ac': $('#Cnee_city_ac').val(),'postcode': $('#Cnee_postcode').val(), 'country': $('#Cnee_country').val(), 'email': $('#Cnee_email').val()};

		for(var i = 0; i < cnees.length; i++){
			if(cnees[i].name == ncnee.name && cnees[i].tel == ncnee.tel){
				cnees.splice(i, 1);
				break;
			}
		}
		cnees.unshift(ncnee);
		if(cnees.length > 30) cnees.splice(30, cnees.length - 30);
		rpms.write('Cnees', cnees);

		var connotes = rpms.Connotes || [];
		connotes.unshift({id: r.id, hbn: r.hbn});
		if(connotes.length > 50) connotes.splice(50, connotes.length - 50);
		rpms.write('Connotes', connotes);

		posApp.toPage('<?=substr($this->createUrl("client/done"),0,-5);?>/' + r.id + '?c=' + r.hbn, false);
	});

	//read local data
	if(rpms.hasOwnProperty('Cnor')){
		for(var k in rpms.Cnor){
			$('#Cnor_'+k).val(rpms.Cnor[k]);
		}
	}

	//cnee selection
	if(rpms.hasOwnProperty('Cnees')){
		$('#cnee_fill').fadeIn();
		for(var i = 0; i < rpms.Cnees.length; i++){
			c = rpms.Cnees[i];
			$('#cnees_list').append('<li><a class="cnee_qf" href="#" data-idx="'+i+'">'+c.name+' '+c.tel+'</a></li>');
		}
		$('a.cnee_qf').on('click', function(e){
			for(var k in rpms.Cnees[$(this).data('idx')]){
				$('#Cnee_'+k).val(rpms.Cnees[$(this).data('idx')][k]);
			}
			e.preventDefault();
		});
	}
	
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>