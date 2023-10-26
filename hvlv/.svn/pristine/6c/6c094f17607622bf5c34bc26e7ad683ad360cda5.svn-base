<style type="text/css">
.chklst ul{
	display: none;
}
.chklst li.open {
	font-weight: bold;
}
.chklst li.open ul{
	font-weight: normal;
}
.chklst li.open ul{
	display: block;
}
</style>
<h1><?=$this->t('Generate Export Parcel Invoices');?></h1>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'expinv-form',
	'enableAjaxValidation' => false,
)); ?>

	<div class="row">
	<?php echo CHtml::label('Depot:','dt'); ?>
	<?php echo CHtml::dropDownList('wid', '', Org::dptList(), array('empty' => 'Select One', 'class' => 'required')); ?>
	</div>
	<div id="chkres">
	</div>
	<input id="gen_btn" type="submit" value="Generate" style="display:none;margin:40px 0 20px 0" />
<?php $this->endWidget(); ?>
</div><!-- form -->
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('select#wid', panel).on('change', function(){
		$('#gen_btn', panel).hide();
		$('#chkres', panel).html('<span class="loading">&nbsp; &nbsp; &nbsp;</span> Checking...').load('invoice/exparinv?check=1&wid='+$(this).val(), function(){
			$('#gen_btn', panel).show();
		});
	});

	$('form#expinv-form', panel).on('success', function(e, r){
		$('#chkres', panel).html(r.res);
		$('#gen_btn', panel).hide();
		return true;
	});

	$(panel).on('click', '.chklst>li>.blocking', function(){
		$(this).parent().toggleClass('open');
	});
});
</script>