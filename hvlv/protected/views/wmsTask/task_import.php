<div id="wms_pickup_import-tabs" style="min-height: 350px">
  <ul>
  <li><a href="#jqmw_<?=$_GET["tabid"];?>_tab1">Container Unload</a></li>
  <li><a href="#jqmw_<?=$_GET["tabid"];?>_tab2">Pallet In</a></li>
  <li><a href="#jqmw_<?=$_GET["tabid"];?>_tab3">Pick Carton / Unit</a></li>
  <li><a href="#jqmw_<?=$_GET["tabid"];?>_tab4">Quick Pick</a></li>
  </ul>
<div class="q_f" id="jqmw_<?=$_GET["tabid"];?>_tab1" style="padding: 5px;">
    <div class="form">
        <?php
        $form=$this->beginWidget('CActiveForm', array(
            'id'=>'wms-import-inward-form',
            'htmlOptions' => ['class'=>'wms-import-form'],
            'enableAjaxValidation'=>false,
            'action' => $this->createUrl('wmsTask/ImportProduct'),
        ));
        ?>
        <div class="row">
           <label for="container_excel">Excel - <small>.csv/.xls/.xlsx File</small> (<a href="../template/wms_container_template.xlsx" target="_blank">Template file</a>)</label>
           <br/>
           <input type="file" name="container_excel" id="container_excel" />
        </div>

        <?php echo $form->hiddenField($model,'id'); ?>

        <br>

        <p><input id="wmstask_import_btn" type="submit" value="Submit" /></p>
        <?php $this->endWidget(); ?>
    </div>
</div>

<div class="q_f" id="jqmw_<?=$_GET["tabid"];?>_tab2" style="padding: 5px;">
    <div class="form">
        <?php
        $form=$this->beginWidget('CActiveForm', array(
            'id'=>'wms-import-inward-form',
            'htmlOptions' => ['class'=>'wms-import-form'],
            'enableAjaxValidation'=>false,
            'action' => $this->createUrl('wmsTask/ImportProduct'),
        ));
        ?>
        <div class="row">
           <label for="inward_excel">Excel - <small>.csv/.xls/.xlsx File</small> (<a href="../template/wms_inward_template.xlsx" target="_blank">Template file</a>)</label>
           <br/>
           <input type="file" name="inward_excel" id="inward_excel" />
        </div>

        <?php echo $form->hiddenField($model,'id'); ?>

        <br>

        <p><input id="wmstask_import_btn" type="submit" value="Submit" /></p>
        <?php $this->endWidget(); ?>
    </div>
</div>

<div class="q_f" id="jqmw_<?=$_GET["tabid"];?>_tab3" style="padding: 5px;">
  <div class="form">
    <?php
      $form=$this->beginWidget('CActiveForm', array(
          'id'=>'wms-import-pickup-form',
          'htmlOptions' => ['class'=>'wms-import-form'],
          'enableAjaxValidation'=>false,
          'action' => $this->createUrl('wmsTask/ImportProduct'),
      ));
      ?>
      <div class="row">
         <label for="pickup_excel">Excel - <small>.csv/.xls/.xlsx File</small> (<a href="../template/wms_stockout_template.xlsx" target="_blank">Template file</a>)</label>
         <br/>
         <input type="file" name="pickup_excel" id="inward_excel" />
      </div>

      <?php echo $form->hiddenField($model,'id'); ?>

      <br>

      <p><input id="wmstask_import_btn" type="submit" value="Submit" /></p>
      <?php $this->endWidget(); ?>       
              
  </div>
</div>

<div class="q_f" id="jqmw_<?=$_GET["tabid"];?>_tab4" style="padding: 5px;">
  <div class="form">
    <?php
      $form = $this->beginWidget('CActiveForm', array(
        'id' => 'wms-import-quickpick-form',
        'htmlOptions' => ['class' => 'wms-import-form'],
        'enableAjaxValidation' => false,
        'action' => $this->createUrl('wmsTask/ImportProduct'),
      ));
    ?>
    <div class="row">
      <label for="quickpick_excel">Excel - <small>.csv/.xls/.xlsx File</small> (<a href="../template/wms_quickpick_template.xlsx" target="_blank">Template file</a>)</label>
      <br />
      <input type="file" name="quickpick_excel" id="inward_excel" />
    </div>

    <?php echo $form->hiddenField($model, 'id'); ?>
    <br />
    <p><input name="wmstask_import_btn" type="submit" value="Submit" /></p>
    <?php $this->endWidget(); ?>
  </div>
</div>

</div>
<script type="text/javascript">
    $(function(){
     var win = $('#jqmw_<?=$_GET["tabid"];?>');
        $('form.wms-import-form', win).on('success', function(e, r){
	
	});
        $('#wms_pickup_import-tabs', win).tabs();
    });
</script>