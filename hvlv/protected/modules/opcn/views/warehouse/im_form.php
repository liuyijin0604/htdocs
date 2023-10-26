<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'shipment-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' =>[
		'data-bit' => '1',
	]
));
?>

	<div class="row">
        <div class="col col-md-6 col-sm-12">
            <h3><?=$this->t('Shipper');?></h3>

            <div class="row">
                <div class="col col-sm-6 col-xs-12">
                    <div class="form-group">
                        <?php echo $form->labelEx($model->cnor, 'name');
                        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                            'name' => 'cnor_name_ac',
                            'sourceUrl' => $this->createUrl('shipment/cnorSuggest', empty($model->cnor->id)? array() :array('id' => $model->cnor->id)),
                            'value' => empty($model->cnor->name)? '' : $model->cnor->name,
                            'options' => array(
                                'showAnim' => 'fold',
                                'minLength' => 2,
                                'delay' => 200,
                                'autoFocus' => true,
                                'select' => 'js:function(evt, ui){ $(this).val(ui.item["value"]); $(this).trigger("ac_after_select", ui); return false; }',
                                'change' => 'js:function(evt, ui){ return false; }',
                            ),
                            'htmlOptions' => array(
                                'size' => '15',
                                'name' => 'Cnor[name]',
                                'class' => 'form-control',
                            ),
                        ));
                        ?>
                    </div>
                </div>
                <div class="col col-sm-6 col-xs-12">
                    <div class="form-group">
                        <?php echo $form->labelEx($model->cnor, 'tel'),
                        CHtml::textField('Cnor[tel]', $model->cnor->tel, array('size'=>15, 'class' => 'form-control')); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col col-sm-4 col-sx-6">
                    <div class="form-group">
                        <?php echo $form->labelEx($model->cnor, 'state');
                        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                            'name' => 'cnor_state_ac',
                            'source' => AppHelper::cnProvince(),
                            'value' => empty($model->cnor->state)? '' : $model->cnor->state,
                            'options' => array(
                                'showAnim' => 'fold',
                                'autoFocus' => true,
                                'minLength' => 0,
                                'delay' => 0,
                                'select' => 'js:function(evt, ui){ $(this).trigger("ac_after_select", ui); return false;}',
                                'response' => 'js:function(evt, ui){ if(ui.content.length == 0){ $(this).data("sid", 0); }; return false; }',
                                'change' => 'js:function(evt, ui){ if($(this).data("sid") == 0) $(this).val(""); return false; }',
                            ),
                            'htmlOptions' => array(
                                'size' => '15',
                                'name' => 'Cnor[state]',
                                'class' => 'form-control',
                            ),
                        ));
                        ?>
                    </div>
                </div>
                <div class="col col-sm-4 col-sx-6">
                    <div class="form-group">
                        <?php echo $form->labelEx($model->cnor, 'city');
                        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                            'name' => 'cnor_city_ac',
                            'sourceUrl' => array('shipment/cnCitySuggest'),
                            'value' => empty($model->cnor->city)? '' : $model->cnor->city,
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
                                'name' => 'Cnor[city]',
                                'class' => 'form-control',
                            ),
                        ));
                        ?>
                    </div>
                </div>

                <div class="col col-sm-4 col-sx-6">
                    <div class="form-group">
                        <?php echo $form->labelEx($model->cnor, 'suburb');
                        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                            'name' => 'cnor_suburb_ac',
                            'sourceUrl' => array('shipment/cnSuburbSuggest'),
                            'value' => empty($model->cnor->suburb)? '' : $model->cnor->suburb,
                            'options' => array(
                                'showAnim' => 'fold',
                                'minLength' => 0,
                                'delay' => 100,
                                'select' => 'js:function(evt, ui){ $(this).trigger("ac_after_select", ui); return false; }',
                            ),
                            'htmlOptions' => array(
                                'size' => '15',
                                'name' => 'Cnor[suburb]',
                                'class' => 'form-control',
                            ),
                        )); ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model->cnor, 'address'),
                CHtml::textField('Cnor[address]', $model->cnor->address, array('size'=>40, 'class' => 'form-control')); ?>
            </div>

            <div class="row">
                <div class="col col-sm-6 col-sx-12">
                    <div class="form-group">
                        <?php echo $form->labelEx($model->cnor, 'postcode'),
                        CHtml::textField('Cnor[postcode]', $model->cnor->postcode, array('size'=>15, 'class' => 'form-control')); ?>
                    </div>
                </div>
                <div class="col col-sm-6 col-sx-12">
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
	<h3><?=$this->t('Consignee');?></h3>
	<div class="row">
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'name');
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'cnee_name_ac',
				'sourceUrl' => array('shipment/cneeSuggest'),
				'value' => ($model->cnee->name) ? $model->cnee->name : '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'autoFocus' => true,
						'select' => 'js:function(evt, ui){ $(this).trigger("ac_after_select", ui); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
					'name' => 'Cnee[name]',
					'class' => 'form-control',
				),
		));
		?>
	</div>
	</div>
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'tel');
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'cnee_tel_ac',
				'sourceUrl' => array('shipment/cneeSuggest'),
				'value' => ($model->cnee->tel) ? $model->cnee->tel : '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 4,
						'delay' => 200,
						'autoFocus' => true,
						'select' => 'js:function(evt, ui){ $(this).val(ui.item["value"]); $(this).trigger("ac_after_select", ui); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
					'name' => 'Cnee[tel]',
					'class' => 'form-control',
				),
		));
		?>
	</div>
	</div>
	</div>
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'address'),
		CHtml::textField('Cnee[address]', $model->cnee->address, array('size'=>40, 'class' => 'form-control'));?>
	</div>

	<div class="row">
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'suburb');
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'cnee_sub_ac',
				'sourceUrl' => array('shipment/auPcSuggest'),
				'value' => ($model->cnee->suburb) ? $model->cnee->suburb : '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'autoFocus' => true,
						'select' => 'js:function(evt, ui){ $(this).val(ui.item["value"]); $(this).trigger("ac_after_select", ui); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
					'name' => 'Cnee[suburb]',
					'class' => 'form-control',
				),
		));
		?>
	</div>
	</div>
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'state'),
		CHtml::dropDownList('Cnee[state]', $model->cnee->state, array('ACT' => 'ACT - Australia Capital Territory', 'NSW' => 'NSW - New South Wales', 'NT' => 'NT - Northern Territory', 'QLD' => 'QLD - Queensland', 'SA' => 'SA - South Australia', 'TAS' => 'TAS - Tasmania', 'VIC' => 'VIC - Victoria', 'WA' => 'WA - Western Australia'), array('empty' => $this->t('Select One'), 'class' => 'form-control')); ?>
	</div>
	</div>
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

	<div class="form-group">
        <h3><?=$this->t('Goods');?></h3>

        <div class="row">
            <div class="col col-md-2 col-sm-4 col-xs-6">
                <div class="form-group">
                    <?php echo $form->labelEx($model,'Weight');?>
                    <div class="input-group">
                        <?php echo $form->textField($model,'weight',array('size'=>10,'maxlength'=>10, 'class' => 'form-control', 'id' => 'ImParcel_weight')); ?>
                        <div class="input-group-addon">kg</div>
                    </div>
                </div>
            </div>
            <div class="col col-md-2 col-sm-4 col-xs-6">
                <div class="form-group">
                    <?php echo $form->labelEx($model,'Cubic Meter');?>
                    <div class="input-group">
                        <?php echo $form->textField($model,'cbm',array('size'=>10,'maxlength'=>10, 'class' => 'form-control', 'id' => 'ImParcel_cbm')); ?>
                        <div class="input-group-addon">M<sup>3</sup></div>
                    </div>
                </div>
            </div>

            <div class="col col-md-2 col-sm-4 col-xs-6">
                <div class="form-group">
                    <?php echo $form->labelEx($model,'Insurance'); ?>
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        <?php echo $form->textField($model,'insurance',array('size'=>6,'maxlength'=>10, 'class' => 'form-control')); ?>
                    </div>
                </div>
            </div>

            <div class="col col-md-2 col-sm-4 col-xs-6">
                <div class="form-group">
                    <?php echo $form->labelEx($model,'Cust.Ref'); ?>
                    <?php echo $form->textField($model, 'cref', array('size'=>10,'maxlength'=>20, 'class' => 'form-control')); ?>
                </div>
            </div>

        </div>

        <table id="items" class="table table-striped table-bordered">
	<thead>
	<tr><th width="60">#</th>
	<th><?=$this->t('Item Chinese Name');?> <span class="required">*</span></th>
	<th><?=$this->t('Item English Name');?> <span class="required">*</span></th>
        <th><?=$this->t('HS Code');?></th>
        <th><?=$this->t('Quantity');?><span class="required">*</span></th>
        <th><?=$this->t('Unit value');?><span class="required">*</span></th>
        <th><?=$this->t('Sub Total');?></th></tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
	<tr>
        <td><button class="moreitem btn btn-normal"><span class="glyphicon glyphicon-plus" style="color: #be3426; font-size: 1.2em; padding: 3px 10px;"></span></button></td>
        <th class="tright"><?=$this->t('Total');?>:</th><th id="tot_qty"></th><th id="tot_value"></th></tr>
	</tfoot>
	</table>
	</div>
	<div id="cost" style="text-align: right;"></div>
	<div class="form-group buttons">
		<button type="submit" class="btn btn-primary btn-lg"><?=$this->t($model->isNewRecord ? 'Create' : 'Save');?></button>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	var pitems = <?=json_encode(empty($model->eitems)? '' : $model->eitems);?> || {};
	var addItem = function(add){
		var tb = $('#items tbody');
		var id = $('tr', tb).length;
		var add = add || 1;
		while(add-- > 0){
            var items = '';
            items = '<tr class="'+(id%2==0? 'even' : 'odd');
            items = items + '"><td class="rid">'+(id+1)+'</td>';
            items = items + '<td><input type="text" class="item_name'+(pitems.g_zh && pitems.g_zh[id] === false? ' error' : '')+' form-control" name="items[g_zh]['+id+']" size="25" value="'+(pitems.g_zh && pitems.g_zh[id] ? pitems.g_zh[id] : '') + '" /> </td>';
            items = items + '<td><input type="hidden" class="item_pid" name="items[pid]['+id+']" value="'+(pitems.pid && pitems.pid[id]? pitems.pid[id] : 0) + '" />';
            items = items + '<input type="text" class="item_name'+(pitems.g && pitems.g[id] === false? ' error' : '')+' form-control" name="items[g]['+id+']" size="25" value="'+(pitems.g && pitems.g[id]? pitems.g[id] : '') +  '" /></td>';
            items = items + '<td><input type="text" class="item_hscode'+(pitems.hs && pitems.hs[id] === false? ' error' : '')+' form-control" name="items[hs]['+id+']" size="20" value="'+(pitems.hs && pitems.hs[id]? pitems.hs[id]: '') + '" /></td>';
            items = items + '<td><input type="text" class="item_qty'+(pitems.q && pitems.q[id] === false? ' error' : '')+' form-control" name="items[q]['+id+']" size="3" value="'+(pitems.q && pitems.q[id]? pitems.q[id]: '') + '" /></td>';
            items = items + '<td><input type="text" class="item_value'+(pitems.v && pitems.v[id] === false? ' error' : '')+' form-control" name="items[v]['+id+']" size="10" value="'+(pitems.v && pitems.v[id]? pitems.v[id]: '') + '" /></td>';
            var subTotal = 0;
            if ( pitems.q && pitems.q[id] && pitems.v && pitems.v[id]) subTotal =  pitems.q[id] * pitems.v[id];

            items = items + '<td class="item_tot">'+ subTotal.toFixed(2) +'</td>'
            items = items + '</tr>';
			tb.append(items);
			id++;
		}
		calcTot();
	};
	var calcTot = function(){
		var tqty = 0;
        var tvalue = 0.0;

		$('#items tbody tr').each(function(){
            var itemValue =  Number($('.item_value', this).val()) || 0;
            var itemQty = Number($('.item_qty', this).val()) || 0;
			tqty += itemQty;
            var subTotal =  itemValue * itemQty;
            $('.item_tot', this).html(  subTotal.toFixed(2) );
            tvalue += subTotal;
		});
		
		$('#tot_qty').text(tqty);
        $('#tot_value').text(tvalue.toFixed(2));
	};
	
	//required fields
	$('#cnee_name_ac, #ExParcel_weight, #cnor_name_ac, #cnee_tel_ac, #cnor_name_ac, #Cnor_tel, #Cnor_address, #cnor_state_ac, #cnor_city_ac, #Cnor_postcode').off('change').on('change', function(){
		if($(this).val() == '' || $(this).val() == 0){
			$(this).parents('.form-group').addClass('has-warning').removeClass('has-success');
		}else{
			$(this).parents('.form-group').removeClass('has-warning').addClass('has-success');
		}
	}).trigger('change');

	$('#items').off('change', '.item_qty,.item_value').on('change', '.item_qty,.item_value', calcTot);

	$('#items').off('keydown', 'input[type=text]').on('keydown', 'input[type=text]', function(evt){
			if(evt.keyCode == 13){
				if(Number($(this).parents('tr').find('.rid').text()) == $('#items tbody tr').length) addItem(1);
				return false;
			}
	});
	$('.moreitem').off('click').on('click', function(e){
        e.preventDefault();
		addItem(1);
	});

	addItem(pitems.g? pitems.g.length : 1);

	//ac baseurl
	$('#cnee_city_ac, #cnee_suburb_ac').on('autocompletecreate', function(){
		//alert('ac');
		$(this).data('src', $(this).autocomplete('option', 'source'));
	});

	var cnor_state = $('#cnor_state_ac');

	//cnor name/mobile ac
	$('#cnee_name_ac, #cnee_tel_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		var mfs = ['address', 'state', 'postcode', 'country', 'email'];
		for(var i=0; i < mfs.length; i++) $('#Cnor_'+mfs[i]).val(ui.item[mfs[i]]);
		$('#cnee_name_ac').val(ui.item.name);
		$('#cnee_tel_ac').val(ui.item.tel);
		$('#cnee_sub_ac').val(ui.item.suburb);
	});

	//cnor suburb ac
	$('#cnee_sub_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		$("#Cnee_postcode").val(ui.item.pc);
		$('#Cnee_state').val(ui.item.st);
	});

	//cnee name ac
	$('#cnor_name_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		var mfs = ['tel', 'address', 'postcode', 'country', 'email'];
		for(var i=0; i < mfs.length; i++) $('#Cnor_'+mfs[i]).val(ui.item[mfs[i]]);
		cnor_state.val(ui.item.state);
		$('#cnor_city_ac').val(ui.item.city);
		$("#cnor_suburb_ac").val(ui.item.suburb);
		$("#Cnor_address").focus();
		$('#notifc').notify({message: {text: "Consignor atuofill"}}).show();
	});

	//cnee state
	cnor_state.off('ac_after_select').on('ac_after_select', function(evt, ui){
		$(this).val(ui.item.value).data('sid', ui.item.id);
		if(ui.item.ocid){
			$('#cnor_city_ac').val(ui.item.value).data('cid', ui.item.ocid).focus();
			$('#Cnor_postcode').val(ui.item.oczip);
		}
	}).on('focus',function(){
		$(this).autocomplete('search', $(this).val());
	});

	var cnorSid = function(){
		var v = cnor_state.val();
		if(v != ''){
			var s = cnor_state.autocomplete('option', 'source');
			for(i in s){
				if(s[i].value == v) cnor_state.data('sid', s[i].id);
			}
		}
	};

	//cnee city
	$('#cnor_city_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		$(this).val(ui.item.value).data('cid', ui.item.id);
		if(ui.item.aname){
			$('#cnor_suburb_ac').val(ui.item.aname);
		}
		if(ui.item.zip){
			$('#Cnor_postcode').val(ui.item.zip);
		}
	}).on('focus', function(){
		cnorSid();
		if(!$(this).data('src')) $(this).data('src', $(this).autocomplete('option', 'source'));
		$(this).autocomplete({source : $(this).data('src')+'?sid='+cnor_state.data('sid')}).autocomplete('search', $(this).val());
	}).on( "autocompletesearch", function(e, u){
		if(cnor_state.val() == '') return false;
	});

	//cnee suburb
	$('#cnor_suburb_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
		$(this).val(ui.item.value);
		$('#Cnor_postcode').val(ui.item.zip);
	}).on('focus', function(){
		if(!$(this).data('src')) $(this).data('src', $(this).autocomplete('option', 'source'));
		$(this).autocomplete({source : $(this).data('src')+'?cid='+$('#cnor_city_ac').data('cid')}).autocomplete('search', $(this).val());
	}).on( "autocompletesearch", function(e, u){
		if($('#cnor_city_ac').val() == '') return false;
	});

	//product ac
	$('#items').off('focus', '.item_name').on('focus', '.item_name', function(){
		var t = $(this);
		var tp = $('select.typsel', t.parents('tr'));
		if(!t.data('ac_inited')){
			t.autocomplete({
				showAnim:'fold',
				minLength:1,
				delay:500,
				autoFocus:true,
				source : posApp.baseUrl+'shipment/prodSuggest',
				response: function(e, u){
					if(u.content.length == 1){
						var itm = u.content[0];
						$(this).parent().find('input.item_pid').val(itm.pid).data({'p': itm.price, 'r1': itm.r1, 'r2': itm.r2});
						$(this).val(itm.label);
						$(this).parents('td').next().find('.item_qty').focus();
					}
				},
				select:function(e, u){
					$(this).parent().find('input.item_pid').val(u.item.pid).data({'p': u.item.price, 'r1': u.item.r1, 'r2': u.item.r2});
					$('#cost').trigger('upCost');
				}
			}).on( "autocompletesearch", function(e, u){
				if(tp.val() == '') return false;
			});
			t.data({'ac_inited': true, 'src': posApp.baseUrl+'shipment/prodSuggest'});
		}
		$(this).autocomplete({source : $(this).data('src')+'?t='+tp.val()});
	});

	$('body').on('change', '#ImParcel_weight, .item_qty', function(){
		$('#cost').trigger('upCost');
	});

	//tabindex control
	$('#ImParcel_weight').focus();

	$('#items input').off('keydown').on('keydown', function(evt){
		if(evt.keyCode == 9 && !evt.shiftKey){
			var j = false;
			switch($(this).attr('id')){
				case 'ExParcel_hbn':
					j = '#ExParcel_weight';
				break;
				case 'ExParcel_weight':
					j = '#cnor_name_ac';
				break;
				case 'cnee_tel_ac':
					j = '#cnor_name_ac';
				break;
				case 'Cnor_address':
					j = '#items .typsel:first-of-type';
				break;
			}
			if(j){
				$(j).focus();
				return false;
			}
		}
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>