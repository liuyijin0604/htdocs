<h2>Farmland Pallet Label Generator</h2>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'label-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' =>[
		'data-bit' => '1',
		'target' => '_blank',
		'data-ajaxf' => '1',
	]
));
?>
	<div class="row">
		<div class="col col-md-6">
	<div class="form-group">
		<label>Product</label>
		<select id="prod" class="form-control" name="prod"><option value="">Select Product...</option>
		<?php
		$reco = ['87' => 'CN', '01' => 'AU', '45' => 'HK'];
		$region = '';
		foreach($this->products as $p){
			$pr = substr($p[0],0,2);
			if($pr != $region){
				$region = $pr;
				echo '<option disabled value="" style="font-weight: bold;">'.$reco[$region].' Product</option>';
			}
			echo '<option value="'.$p[0].'">'.$p[0].' - '.$p[1].'</option>';
		}
		?></select>
	</div>
	</div>
	</div>
	<div class="row">
	<div class="col col-md-3 col-sm-6">
	<div class="form-group">
		<label>Batch Number</label>
		<input type="text" class="form-control" name="batch" />
	</div>
	</div>
	<div class="col col-md-3 col-sm-6">
	<div class="form-group">
		<label>Manufacture Date</label>
		<input id="mfd" type="text" class="form-control date_input" name="mfd" />
	</div>
	</div>
	<div class="col col-md-3 col-sm-6">
	<div class="form-group">
		<label>Best Before Date</label>
		<input id="bbd" type="text" class="form-control date_input" name="bbd" />
	</div>
	</div>
	</div>

	<div class="row">
	<div class="col col-md-3 col-sm-6">
	<div class="form-group">
		<label>Palllet # Range</label>
	<div class="input-group">
		<input type="number" class="form-control" min="1" name="pns" /><span class="input-group-addon"> - </span>
		<input type="number" class="form-control" min="1" name="pne" />
	</div>
	</div>
	</div>
	<div class="col col-md-3 col-sm-6">
	<div class="form-group">
		<label>Qty Per Pallet</label>
		<input id="qtypp" type="number" min="1" class="form-control" name="qty" />
	</div>
	</div>
	</div>

	<div class="form-group buttons">
		<button type="submit" class="btn btn-primary btn-lg"><?=$this->t('Create Label');?></button>
	</div>
<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var products = <?=json_encode($this->products);?>;
	$('select#prod').on('change', function(){
		var v = $(this).val();
		for(var i in products){
			if(products[i][0] == v){
				$('#qtypp').val(products[i][2]);
				break;
			}
		}
	});
	$('input#mfd').on('change', function(){
		var d = new Date($(this).val());
		d.setYear(d.getFullYear() + 2);
		d.setDate(d.getDate() - 1);

		var month = '' + (d.getMonth() + 1),
		day = '' + d.getDate(),
		year = d.getFullYear();

		if (month.length < 2) 
			month = '0' + month;
		if (day.length < 2) 
			day = '0' + day;

		$('input#bbd').val([year, month, day].join('-'));
	});
});
</script>
