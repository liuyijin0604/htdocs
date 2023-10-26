<?php
$this->widget('zii.widgets.CBreadcrumbs', [
	'links' => [
		'Manifests'=>['manifest/list'],
		'Create Manifest'
	],
]);
?>
<style>    
#warning{
	display:none;
	margin: 10px 0;
	border: 1px solid;
	padding:15px 20px;
	font-size: 14px;
	background: #fe0;
}
 </style>
<h3>Create Consol For Manifest</h3>
<div class="container">
	<div id="warning"></div>
<?php $form=$this->beginWidget('CActiveForm', [
			'id'=>'customer-consol-form',
			'enableAjaxValidation'=>false,
		]);
?>
<div class="row">
	<div class="col-md-5 form-group">
		<label>Consignment numbers:</label>
		<textarea class="form-control" name="hbns" rows="10" cols="40"></textarea>
	</div>
</div>
<div class="row ">
	<?php echo CHtml::submitButton('Create', ['class'=>'btn btn-primary']); ?>
</div>
<?php $this->endWidget(); ?>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('#customer-consol-form').on('success', function(e,r){
		posApp.toPage('<?=$this->createUrl('manifest/list')?>', false);
	}).on('error',function(e,r){
		var msg = '<div>' + r.msg + '</div>';
		var elm = $(msg);
		$('#warning').html('');
		$('#warning').prepend(elm.fadeIn());
		$('#warning').show();
	});
});
</script>
<?php $this->registerJS(ob_get_clean(), 2); ?>