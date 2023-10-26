<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol_form',
	'enableAjaxValidation'=>false,
				'action'=> $this->createUrl('warehouseProcess/editConsolWhNote',array('id'=>$model->id)),
));
echo "<h1>".$model->MAWB_number." Warehouse Note</h2>";
echo CHtml::textArea('wh_note', @$model->getConsolWHNote(), array('rows' => 2, 'cols' => 60,'class'=>'form-control','id'=>'wh_note'));
echo CHtml::submitButton('submit',["id"=>"submitWHNote"]);
?>


</div>

<?php $this->endWidget();?>


<script type="text/javascript">
	$("#submitWHNote").on("click",function(){
		$.ajax({
			'url': '<?= $this->createUrl('warehouseProcess/editConsolWhNote',array('id'=>$model->id))?>',
			'type': 'POST',
			'data': { 'wh_note': $('#wh_note').val() },
			success: function(r) {
				r = JSON.parse(r);
				console.log(r);
				if (r['done'] == true) {
					$('#notifc').notify({message: {html: "Upload Success"}}).show();
					$('#modal_close').click();
					$('#bwtrunk-grid').yiiGridView('update');
				} else {
					$('#notifc').notify({message: {html: r['msg']},type: 'danger'}).show();
					$('#modal_close').click();
				}
			}
		});
		return false;
	});

</script>