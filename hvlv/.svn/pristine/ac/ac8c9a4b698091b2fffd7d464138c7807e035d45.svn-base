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
        <div class="col col-md-2 col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'ref'); ?>
		<?php echo $form->textField($model, 'ref', array('size'=>10,'maxlength'=>20, 'class' => 'form-control')); ?>
	</div>
	</div>
            
	<div class="col col-md-2 col-sm-4 col-xs-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'cref'); ?>
		<?php echo $form->textField($model, 'cref', array('size'=>10,'maxlength'=>20, 'class' => 'form-control')); ?>
	</div>
	</div>
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
		<?php echo $form->labelEx($model,'cbm');?>
		<div class="input-group">
			<?php echo $form->textField($model,'cbm',array('size'=>10,'maxlength'=>10, 'class' => 'form-control', 'id' => 'ExParcel_weight')); ?>
                    <div class="input-group-addon">M<sup>3</sup></div>
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

	
	<?php if(SellRate::hasExRate($org->id, 'V0')): ?>
	<div class="col col-md-2 col-sm-4 col-xs-6">
	<div class="form-group">
		<label>Service</label>
		<label>
		<?php echo CHtml::checkbox('serivce_pv', !empty($model->mdata['pv'])), $this->t(' Premium Express'); ?> 
		</label>
	</div>
	</div>
	<?php endif; ?>
	</div>

	<div class="row">
	<div class="col col-md-6 col-sm-12">
	<h3><?=$this->t('Shipper');?></h3>
	<div class="row">
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'name');
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'cnor_name_ac',
				'sourceUrl' => array('shipment/cnorSuggest'),
				'value' => ($model->cnor->name) ? $model->cnor->name : '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'autoFocus' => true,
						'select' => 'js:function(evt, ui){ $(this).trigger("ac_after_select", ui); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
					'name' => 'Cnor[name]',
					'class' => 'form-control',
				),
		));
		?>
	</div>
	</div>
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnor, 'tel');
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => 'cnor_tel_ac',
				'sourceUrl' => array('shipment/cnorSuggest'),
				'value' => ($model->cnor->tel) ? $model->cnor->tel : '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 4,
						'delay' => 200,
						'autoFocus' => true,
						'select' => 'js:function(evt, ui){ $(this).val(ui.item["value"]); $(this).trigger("ac_after_select", ui); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
					'name' => 'Cnor[tel]',
					'class' => 'form-control',
				),
		));
		?>
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
				'name' => 'cnor_sub_ac',
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
	<div class="col col-sm-4 col-xs-6">
		<h3><?=$this->t('Consignee');?></h3>
	</div>
	</div>

	<div class="pasting-pane" style="display:none; position: absolute; opacity: 0.9; z-index:9; width: 100%; padding-right:25px;">
		<textarea id="addr_paste" class="form-control" style="width:100%; min-height: 280px; font-size: 1.5em; font-weight: bold; color: #14487E; box-shadow: 5px 5px 5px #999;" placeholder="<?=$this->t('Paste address here');?>"></textarea>
	</div>

	<div class="row">
	<div class="col col-sm-6 col-xs-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'name');
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => 'cnee_name_ac',
					'sourceUrl' => $this->createUrl('shipment/cneeSuggest', empty($model->cnee->id)? array() :array('id' => $model->cnee->id)),
					'value' => empty($model->cnee->name)? '' : $model->cnee->name,
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
						'name' => 'Cnee[name]',
						'class' => 'form-control',
					),
			));
		?>
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
	<div class="col col-sm-4 col-sx-6">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'state');
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => 'cnee_state_ac',
					'source' => AppHelper::cnProvince(),
					'value' => empty($model->cnee->state)? '' : $model->cnee->state,
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
						'name' => 'Cnee[state]',
						'class' => 'form-control',
					),
			));
		?>
	</div>
	</div>
	<div class="col col-sm-4 col-sx-6">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'city');
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => 'cnee_city_ac',
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

	<div class="col col-sm-4 col-sx-6">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'suburb');
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'name' => 'cnee_suburb_ac',
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
	<div class="col col-sm-6 col-sx-12">
	<div class="form-group">
		<?php echo $form->labelEx($model->cnee, 'postcode'),
		CHtml::textField('Cnee[postcode]', $model->cnee->postcode, array('size'=>15, 'class' => 'form-control')); ?>
	</div>
	</div>
	<div class="col col-sm-6 col-sx-12">
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
	<table id="items" class="table table-striped table-bordered">
	<thead>
	<tr><th width="80">#</th><th><?=$this->t('Item Name');?> <span class="required">*</span></th><th><?=$this->t('Qty');?>*</th></th><th><?=$this->t('Unit value');?>*</th></tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
	</tfoot>
	</table>
	</div>

	
	<div id="cost" style="text-align: right;"></div>
<!--	<div class="form-group buttons">

		<button type="submit" class="btn btn-primary btn-lg"><?=$this->t('Save');?></button> &nbsp;
		<a class="btn btn-info" href="<?=$this->createUrl('shipment/create', ['copy' => $model->id]);?>"><?=$this->t('Copy Shipment');?></a>
	</div>-->

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	var pitems = <?=json_encode(empty($model->eitems)? '' : $model->eitems);?> || {type:[], g:[], q:[]};
	var ecr = <?=SellRate::hasExRate($org->id, 'EC')? 'true' : 'false';?>;
	var addItem = function(add){
		var tb = $('#items tbody');
		var id = $('tr', tb).length;
		var add = add || 1;
		while(add-- > 0){
			tb.append('<tr class="'+(id%2==0? 'even' : 'odd')+'"><td class="rid">'+(id+1)+'</td><td><input type="hidden" class="item_pid" name="items[pid]['+id+']" value="'+(pitems.pid && pitems.pid[id]? pitems.pid[id] : 0)+'" /><input type="text" class="item_name'+(pitems.g && pitems.g[id] === false? ' error' : '')+' form-control" name="items[g]['+id+']" size="25" value="'+(pitems.g && pitems.g[id]? pitems.g[id] : '')+'" /></td><td><input type="text" class="item_qty'+(pitems.q && pitems.q[id] === false? ' error' : '')+' form-control" name="items[q]['+id+']" size="3" value="'+(pitems.q && pitems.q[id]? pitems.q[id]: '')+'" /></td><td><input type="text" class="item_value'+(pitems.v && pitems.v[id] === false? ' error' : '')+' form-control" name="items[v]['+id+']" size="3" value="'+(pitems.v && pitems.v[id]? pitems.v[id]: '')+'" /></td></tr>');
			id++;
		}
	};
       addItem(pitems.g? pitems.g.length : 1);
	
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>