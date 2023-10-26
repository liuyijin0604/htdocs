<div class="form">

<?php 
$url = $this->createUrl('zwStorage/updateZWStorage');
if(!empty($model->id))
{
	$url = $url."?id=".$model->id;
}
$form=$this->beginWidget('CActiveForm', array(
	'id'=>@$_GET['tabid'].'zws-form',
	'enableAjaxValidation'=>false,
)); ?>
		<div style="margin-left: 2em">
			<?php if(isset($model->status)):?>
				<div class="row">
					<div class="col-12-left" style="padding-right: 5px;">
						Status
					</div>
					<div class="col-12-left" style="padding-right: 5px;">
						<?php echo CHtml::dropDownList('ImportZwStorage[status]', $model->status, $this->t(ImportZwStorage::$states));?>
					</div>
				</div>
			<?php endif;?>
			<div class="row">
				<div class="col-12-left" style="padding-right: 5px;">
					Store Code
				</div>
				<div class="col-12" style="padding-right: 5px;">
					<?php echo $form->dropDownList($model,'store_code', ImportZwStorage::$storeCode, array('prompt'=>'Select','class'=>'form-control',"style"=>"margin-top:1.1em;")); ?>
				</div>
			</div>
			<div  class="row">
				<div  class="col-12-left" style="padding-right: 5px;">
					<?php echo $form->labelEx($model,'org_id'); ?>
				</div>
				<div class="col-12" style="padding-right: 5px;">
					<?php echo $form->hiddenField($model,'org_id');
						$acname = empty($_GET["tabid"])? 'agent_ac' : $_GET["tabid"].'_agent_ac';
						$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
							'name' => $acname,
							'sourceUrl' => array('org/agentSuggest'),
							'value' => empty($model->org->name) ? '' : $model->org->name,
							'options' => array(
									'showAnim' => 'fold',
									'minLength' => 2,
									'delay' => 200,
									'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
									'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val(""); return false; }',
							),
							'htmlOptions' => array(
								'class' => 'required',
								'size' => '50',
							),
					));
					?>

					<?php echo $form->error($model,'org_id'); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-12-left" style="padding-right: 5px;">
					Service
				</div>
				<div class="col-12" style="padding-right: 5px;">
					<?php echo CHtml::dropDownList('ImportZwStorage[service]',@$model->mdata["service"],ImportZwStorage::$transportType); ?>
				</div>
				<div class="col-12" style="padding-right: 5px;">
					<?php echo CHtml::checkbox('ImportZwStorage[notPush]',isset($model->mdata["notPush"])?$model->mdata["notPush"]:1),'Do not push'; ?>
				</div>
			</div>
			<div class="row">
				<div class="col-12-left" style="padding-right: 5px;">
					Putcode
				</div>
				<div class="col-12" style="padding-right: 5px;">
					<?php echo $form->textField($model,'putcode',['class'=>'form-control',"style"=>"margin-top:1.1em;"])?>
				</div>
			</div>
			<div class="row">
				<div class="col-12-left" style="padding-right: 5px;">
					Volume
				</div>
				<div class="col-12" style="padding-right: 5px;">
					<?php echo $form->numberField($model,'volume',['class'=>'form-control',"style"=>"margin-top:1.1em;",'step'=>'0.001'])?>
				</div>
			</div>
			<div class="row">
				<div class="col-12-left" style="padding-right: 5px;">
					Weight
				</div>
				<div class="col-12" style="padding-right: 5px;">
					<?php echo $form->numberField($model,'weight',['class'=>'form-control',"style"=>"margin-top:1.1em;",'step'=>'0.001']) ?>
				</div>
			</div>
			<div class="row">
				<div class="col-12-left" style="padding-right: 5px;">
					Packages
				</div>
				<div class="col-12" style="padding-right: 5px;">
					<?php echo $form->numberField($model,'goods_count',['class'=>'form-control',"style"=>"margin-top:1.1em;",'step'=>'0.001']); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-12-left" style="padding-right: 5px;">
					Note
				</div>
				<div class="col-12" style="padding-right: 5px;">
					<?php echo $form->textarea($model,'remark',['class'=>'form-control',"style"=>"margin-top:1.1em;",'rows'=>'5']); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-12-left" style="padding-right: 5px;">
					HBNS(split by ,;)
				</div>
				<div class="col-12" style="padding-right: 5px;">
					<?php echo CHtml::textarea('ImportZwStorage[hbns]',@$model->mdata['hbns'],['class'=>'form-control',"style"=>"margin-top:1.1em;",'rows'=>'5']); ?>
				</div>
			</div>
		</div>

		<table id="items" class="table table-striped table-bordered">

		<thead>
		<tr><th width="60">#</th>
		<th><?=$this->t('Item Chinese Name');?> <span class="required">*</span></th>
		<th><?=$this->t('Item English Name');?> <span class="required">*</span></th>
	        <th><?=$this->t('HS Code');?></th>
	        <th style="width:2em;"><?=$this->t('SKU');?></th>
	        <th><?=$this->t('Quantity');?><span class="required">*</span></th>
	        <th><?=$this->t('Unit value');?><span class="required">*</span></th>
	        <th><?=$this->t('Sub Total');?></th></tr>
		</thead>
		<tbody>
		</tbody>
		<tfoot>
		<tr>
	        <td><button class="moreitem btn btn-normal"><span class="glyphicon glyphicon-plus" style="color: #be3426; font-size: 1.2em; padding: 3px 10px;">+</span></button></td>
	        <th class="tright"><?=$this->t('Total');?>:</th><th id="tot_qty"></th><th id="tot_value"></th></tr>
		</tfoot>

		</table>

		<div class="row rowcol"  style = "width:100%;">
		<h3>Actual Package Data of Courier</h3>
		<table id="packages" style = "border: 1px solid #000;width:100%;">
		<thead>
		<tr><th style="width:10em;">Actual Weight</th><th style="width:10em;">Length</th><th style="width:10em;">Width</th><th style="width:10em;">Height</th></tr>
		</thead>
		<tbody>
			<?php 
			if(!empty($model->mdata['aochenRealData']))
			{
				foreach($model->mdata['aochenRealData'] as $key => $pack)
				{
					$num = $key+1;
					echo "<tr><td align='center'>".@$pack['actual']."kg</td><td align='center'>".@$pack['length']."CM</td><td align='center'>".@$pack['width']."CM</td><td align='center'>".@$pack['height']."CM</td></tr>";
				}
			}

			?>
			
		</tbody>
		</table>
		</div>

		<div class="row">
			<div class="col-6" style="padding-left: 5px;">
				<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save'), array('class' => 'save_btn btn btn-primary btn-block')); ?>
			</div>
		</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
	$(function(){
		var win = $('#jqmw_<?=$_GET["tabid"];?>');
		var tab = $('#<?=$_GET["tabid"];?>');
		var panel = $('#<?=$_GET["tabid"];?>').data('panel');

		tab.unbind('reload_zw_storage_records').bind('reload_zw_storage_records', function(){
				$('#<?=$_GET["tabid"];?>_zw_storage_records', tab.data('panel')).yiiGridView('update');
				return false;
			});

		$('.save_btn',win).on('click',function(){
				save();
				return false;
			});

          function save()
			{
					var form = new FormData(document.getElementById("<?=@$_GET['tabid']?>zws-form"));
					 $.ajax({
					            url: '<?=$url?>',
					            type: "post",
					            data: form,
					            processData: false,
					            contentType: false,
					            success: function(r) {
					                if(r=='done')
					                 {
										myApp.notice('Done', 5000);
									 }else
									 {
										myApp.alert(r, false);   
						             }
						             tab.trigger('reload_zw_storage_records');
						         },
					            error: function(e) {
					                console.log(e);
					            }
					        });	
			}

			var pitems = <?=json_encode(empty($model->eitems)? '' : $model->eitems);?> || {};
			var addItem = function(add){
				var tb = $('#items tbody',win);
				var id = $('tr', tb).length;
				var add = add || 1;
				while(add-- > 0){
		            var items = '';
		            items = '<tr class="'+(id%2==0? 'even' : 'odd');
		            items = items + '"><td class="rid">'+'<div class="less btn btn-normal"><span class="glyphicon glyphicon-minus" style="color: #be3426; font-size: 0.8em; padding: 1px 2px;">-</span></div></td>';
		            items = items + '<td><input type="text" class="item_name'+(pitems.g_zh && pitems.g_zh[id] === false? ' error' : '')+' form-control" name="items[g_zh]['+id+']" size="25" value="'+(pitems.g_zh && pitems.g_zh[id] ? pitems.g_zh[id] : '') + '" /> </td>';
		            items = items + '<td><input type="hidden" class="item_pid" name="items[pid]['+id+']" value="'+(pitems.pid && pitems.pid[id]? pitems.pid[id] : 0) + '" />';
		            items = items + '<input type="text" class="item_name'+(pitems.g && pitems.g[id] === false? ' error' : '')+' form-control" name="items[g]['+id+']" size="25" value="'+(pitems.g && pitems.g[id]? pitems.g[id] : '') +  '" /></td>';
		            items = items + '<td><input type="text" class="item_hscode'+(pitems.hs && pitems.hs[id] === false? ' error' : '')+' form-control" name="items[hs]['+id+']" size="15" value="'+(pitems.hs && pitems.hs[id]? pitems.hs[id]: '') + '" /></td>';
		            items = items + '<td><input type="text" class="item_sku'+(pitems.sku && pitems.sku[id] === false? ' error' : '')+' form-control" name="items[sku]['+id+']" size="15" value="'+(pitems.sku && pitems.sku[id]? pitems.sku[id]: '') + '" /></td>';
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

				$('.moreitem',win).off('click').on('click', function(e){
		               e.preventDefault();
				addItem(1);
				});

				$('.less',win).off('click').on('click', function(e){
		            e.preventDefault();
		            $(this).parent().parent().remove();
		            calcTot();
				});
			};

			var calcTot = function(){
				var tqty = 0;
		        var tvalue = 0.0;

				$('#items tbody tr',win).each(function(){
		            var itemValue =  Number($('.item_value', this).val()) || 0;
		            var itemQty = Number($('.item_qty', this).val()) || 0;
					tqty += itemQty;
		            var subTotal =  itemValue * itemQty;
		            $('.item_tot', this).html(  subTotal.toFixed(2) );
		            tvalue += subTotal;
				});
				
				$('#tot_qty',win).text(tqty);
		        $('#tot_value',win).text(tvalue.toFixed(2));
			};

        	$('#items',win).off('change', '.item_qty,.item_value').on('change', '.item_qty,.item_value', calcTot);

			$('#items',win).off('keydown', 'input[type=text]').on('keydown', 'input[type=text]', function(evt){
					if(evt.keyCode == 13){
						if(Number($(this).parents('tr').find('.rid').text()) == $('#items tbody tr').length) addItem(1);
						return false;
					}
			});
			$('.moreitem',win).off('click').on('click', function(e){
		               e.preventDefault();
				addItem(1);
			});
		    

			addItem(pitems.g? pitems.g.length : 1);
        
    });

</script>