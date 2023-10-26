<div id="org_import_rate-tabs" style="min-height: 350px">
  <ul>
    <li><a href="#jqmw_<?=$_GET["tabid"];?>_tab1">Import Rate</a></li>
  </ul>
  <div class="q_f" id="jqmw_<?=$_GET["tabid"];?>_tab1" style="padding: 5px;">
    <div class="form">
        <?php
        $form=$this->beginWidget('CActiveForm', array(
            'id'=>'org-import-rate-form',
            'htmlOptions' => ['class'=>'org-import-rate-form'],
            'enableAjaxValidation'=>false,
            'action' => $this->createUrl('Org/ImportRate'),
        ));
        ?>
        <div class="row">
           <label for="rate_excel">Excel - <small>.csv/.xls/.xlsx File</small> (<a href="../template/rate_template.xlsx" target="_blank">Tempalte file</a>)</label>
           <br/>
           <input type="file" name="rate_excel" id="rate_excel" />
        </div>

        <?php echo $form->hiddenField($model,'id'); ?>

        <br>
        <p><input id="org_import_rate_btn" type="submit" value="Submit" /></p>
        <?php $this->endWidget(); ?>
    </div>

    <div id="org_import_rate_result" style="margin: 10px 0; border: 1px solid;padding:20px; font-weight: bold; font-size: 20px;">
    </div>
  </div>
</div>
<script type="text/javascript">
  $(function(){
    var win = $('#jqmw_<?=$_GET["tabid"];?>');
    $('form.org-import-rate-form', win).on('success', function(e, r){
      $('.popCancel').trigger('click');
      $('#org-wms-rate-grid').yiiGridView('update');
    });
    $('#org_import_rate-tabs', win).tabs();
  });
</script>