<h2>Import Tasks</h2>
<hr>
<div class="container">
<div id="wms_pickup_import-tabs" style="min-height: 250px">
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'wms-import-inward-form',
        'htmlOptions' => ['class'=>'wms-import-form'],
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('task/ImportProduct', array('id'=>$model->id)),
    ));
    ?>
    <?php 
    if ($_GET['type']==1010) {
      $template='wms_inward_template.xlsx';
      $name='inward_excel';
    } else if ($_GET['type']==1030) {
      $template='wms_container_template.xlsx';
      $name='container_excel';
    } else if($_GET['type']==3030){
      $template='wms_stockout_template.xlsx';
      $name='pickup_excel';
    }
    else if($_GET['type']==WmsTask::TYPE_Split_Delivery){
      $template='wms_split_delivery_template.xlsx';
      $name='split_delivery_excel';
    }
    ?>
<div class="form-group">
       <label for="excel">Excel - <small>.csv/.xls/.xlsx File</small> (<a href="../../../../../../template/<?=$template?>" target="_blank">Tempalte file</a>)</label>
       <br/>
       <input type="file" name="<?=$name?>" id="<?=$name?>" />
    </div>

    <?php echo $form->hiddenField($model,'id'); ?>

    <br>
    <div class="form-group">
        <p><input id="wmstask_import_btn"  class="btn btn-primary" type="submit" value="Submit" /></p>
    </div>
    <?php $this->endWidget(); ?>
</div>


<div id="wmstask_import_result" style="margin: 10px 0; border: 1px solid;padding:20px; font-weight: bold; font-size: 20px; width: 550px">
</div>
</div>

</div>
<script type="text/javascript">
$(function(){
	$('form.wms-import-form').on('submit', function() {
		$('#wmstask_import_btn').prop('disabled', 'disabled');
	});

	$('form.wms-import-form').on('success', function(e, r) {
		$('#wmstask_import_btn').removeProp('disabled');
	}).on('error', function(e, r) {
		$('#wmstask_import_btn').removeProp('disabled');
	});
});
</script>