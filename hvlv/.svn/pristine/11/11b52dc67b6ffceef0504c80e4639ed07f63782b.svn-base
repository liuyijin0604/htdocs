<h1><?=$this->t('Batch Create X-Ray Job');?></h1>

<div class="form" style="min-height: 300px">

	<?php $form=$this->beginWidget('CActiveForm', [
		'id'=>'batch-xray-form',
		'enableAjaxValidation'=>false,
	]); ?>

	<div class="row">
		<?php echo CHtml::label('Customer', 'for_org_id'); ?>
		<?php echo CHtml::hiddenField('org_id');
		$acname1 = empty($_GET["tabid"])? 'org_ac' : $_GET["tabid"].'_org_ac';
		$this->widget('zii.widgets.jui.CJuiAutoComplete', [
			'name' => $acname1,
			'sourceUrl' => ['org/clientSuggest'],
			'options' => [
				'showAnim' => 'fold',
				'minLength' => 2,
				'delay' => 200,
				'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]).trigger("change"); return false; }',
				'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
			],
			'htmlOptions' => [
				'size' => '60',
				'class' => 'required',
			],
		]);
		?>
	</div>

	<div class="grid-view">
		<table class="items jobs">
			<thead>
			<tr>
				<th>MAWB</th>
				<th>POL</th>
				<th>POD</th>
				<th>Qty</th>
				<th>Flight</th>
				<th>ETD</th>
				<th>Weight</th>
			</tr>
			</thead>
			<tbody>
				<tr class="item"><td><input type="text" class="mawb" name="job[mawb][]" size="15" /></td><td><input type="text" class="pol" name="job[pol][]" size="10" /></td><td><input type="text" class="pod" name="job[pod][]" size="10" /></td><td><input type="text" class="qty" name="job[qty][]" size="5" /></td><td><input type="text" class="flight" name="job[flight][]" size="10" /></td><td><input type="text" class="etd date_input" name="job[etd][]" size="15" /></td><td><input type="text" class="weight" name="job[weight][]" size="10" /></td></tr>
			</tbody>
		</table>
	  </div>
	<div class="row"><label><input type="checkbox" name="inv" value="1" /> Create Invoice</label></div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

	<?php $this->endWidget(); ?>
</div><!-- form -->
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	
	$('#batch-xray-form .jobs', win).on('paste','tbody input[type=text]',function(e){
		if (window.clipboardData && window.clipboardData.getData){
			pastedText = window.clipboardData.getData('Text');
		} else if (e.originalEvent.clipboardData && e.originalEvent.clipboardData.getData) {
			pastedText = e.originalEvent.clipboardData.getData('text/plain');
		}

		var ppos = -1;
		var me = $(this);
		var rows = pastedText.trim().split(/[\n\r]+/);
		var tr = $('#batch-xray-form .jobs tbody tr:first-child').clone();
		if(rows.length > 0){
			$('#batch-xray-form .jobs tbody').empty();
			for(var i in rows){
				cells = rows[i].trim().split(/[\t]+/);
				if(cells[1] && cells[1].length == 3){
					cells[1] = 'AU'+cells[1];
				}
				if(cells[2] && cells[2].length == 3){
					switch(cells[2]){
						case 'TPE':
							cells[2] = 'TWTPE';
						break;
						case 'HKG':
							cells[2] = 'HKHKG';
						break;
						case 'ICN':
							cells[2] = 'KRICN';
						break;
						case 'SGN':
							cells[2] = 'VNSGN';
						break;
						case 'AKL':
							cells[2] = 'NZAKL';
						break;
						case 'DXB':
							cells[2] = 'AEDXB';
						break;
						case 'RGN':
							cells[2] = 'MMRGN';
						break;
						case 'NRT':
							cells[2] = 'JPNRT';
						break;
						case 'SIN':
							cells[2] = 'SGSIN';
						break;
						case 'KUL':
							cells[2] = 'MYKUL';
						break;
						case 'OPO':
							cells[2] = 'PTOPO';
						break;
						case 'BKK':
							cells[2] = 'THBKK';
						break;
						default:
							cells[2] = 'CN'+cells[2];
						break;
					}
				}

				$('input[type=text]', tr).each(function(i){
					$(this).val(cells[i] || '').prop('title', '').removeClass('error').trigger('change');
				});
				$('#batch-xray-form .jobs tbody').append(tr.clone());
			}
		}
		
		return false;
	});

	$('form#batch-xray-form', win).data('custom_success', function(r){
		$('#batch-xray-form .jobs tbody input.error').removeClass('error');
		if(r.error.length > 0){
			msg = 'Please fix errors and submit again';
			for(i in r.error){
				if(Array.isArray(r.error[i])){
					$('.'+r.error[i][1], $('#batch-xray-form .jobs tbody tr').get(r.error[i][0])).addClass('error').prop('title', r.error[i][2]);
				}else{
					msg += ' '+r.error[i];
				}
			}
			myApp.alert(msg);
		}else{
			myApp.notice('Jobs created successfully');
			win.data('opener').trigger('onOpen');
			win.jqmHide();
		}
		$('input[type="submit"]', win).prop('disabled',false);
		return true;
	});
});
</script>
