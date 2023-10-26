<h2>Bulk Warnings</h2>
<div class="form">
<?php 
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'cnid-bulkwarn-form',
	'enableAjaxValidation'=>false,
));

	echo '<div class="row">';
	echo CHtml::label('Warnings','');
	foreach(CnID::$bwfs as $b=>$w){
		if(in_array($b, [1, 2])) continue;
		echo '<label class="radio_label"><input type="checkbox" value="1" name="warn['.$b.']" /> '.$w.'</label> &nbsp;';
	}
	echo '<label class="radio_label"><input type="checkbox" value="1" name="exp" /> Expired</label>';
	echo '</div>';
?>
	<div class="row">
		<?php echo CHtml::label('ID Numbers','');; ?>
		<?php echo CHtml::textArea('d', '', array('rows'=>10, 'cols' => 40)); ?>
		<p><small>Up to 200 numbers.</small></p>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Save'), array('id'=>'save_btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	$('#cnid-bulkwarn-form', win).on('submit', function(){
		var a = $('#d', win).val().split(/[\s,;]+/);
		if(a.length > 200){
			alert("Maximum of 200 numbers allowed");
			return false;
		}
		return window.confirm('Are you sure?');
	}).on('success', function(){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});
});
</script>