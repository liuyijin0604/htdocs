<h1>Orgs in consol</h1>


<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol-orgs-form',
	'enableAjaxValidation'=>false,
));
$index = 1;
?>

    <?php foreach ( $model->getOrgsInfo() as $org ) : ?>
<div class="row">
    <div class="row rowcol" style="font-weight:bold; color:blue;">
        <?php echo  CHtml::label( $index++ . '. '. $org['name'] . '( ' . $org['weight']  .'Kg )','orgname');?>
    </div> <br/>
	<div class="row rowcol">
		<?php echo CHtml::label('AWB Weight','awb_wt');?>
		<?php echo CHtml::textField('mdata[org_'.$org['id'].'][awb_wt]', @$model->mdata['org_'.$org['id']]['awb_wt'], array('size'=>5)); ?>Kg
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('Chargable Wt.','cgb_wt');?>
		<?php echo CHtml::textField('mdata[org_'.$org['id'].'][cgb_wt]', @$model->mdata['org_'.$org['id']]['cgb_wt'], array('size'=>5)); ?>Kg
	</div>
</div>
<?php endforeach; ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	
	//bind reload_tab
	tab.off('reload_tab').on('reload_tab', function(){
		var t = $('.ui-tabs', panel);
		t.tabs('load', t.tabs('option','active'));
	});

});
</script>