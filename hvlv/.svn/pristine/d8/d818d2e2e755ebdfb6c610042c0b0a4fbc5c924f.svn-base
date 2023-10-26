<div id="inco_consol_import-tabs" style="min-height: 350px">
  <ul>
    <li><a href="#jqmw_<?= $_GET["tabid"]; ?>_tab1">ImcoConsol Air Shipment</a></li>
    <li><a href="#jqmw_<?= $_GET["tabid"]; ?>_tab2">ImcoConsol Sea Shipment</a></li>
  </ul>
  <div class="q_f" id="jqmw_<?= $_GET["tabid"]; ?>_tab1" style="padding: 5px;">
    <div class="form">
      <?php
      $form = $this->beginWidget('CActiveForm', array(
        'id' => 'inco-import-inward-form',
        'htmlOptions' => ['class' => 'inco-import-form'],
        'enableAjaxValidation' => false,
        'action' => $this->createUrl('imcoConsol/importImcoConsol'),
      ));
      ?>
      <div class="row">
        <label for="container_excel">Excel - <small>.csv/.xls/.xlsx File</small> (<a href="../template/pre_alert_air.xlsx" target="_blank">Air Template file</a>)</label>
        <br />
        <input type="file" name="container_excel" id="container_excel" />
      </div>
      <br>

      <p><input id="wmstask_import_btn" type="submit" value="Submit" /></p>
      <?php $this->endWidget(); ?>
    </div>
  </div>

  <div class="q_f" id="jqmw_<?= $_GET["tabid"]; ?>_tab2" style="padding: 5px;">
    <div class="form">
      <?php
      $form = $this->beginWidget('CActiveForm', array(
        'id' => 'inco-import-inward-form',
        'htmlOptions' => ['class' => 'inco-import-form'],
        'enableAjaxValidation' => false,
        'action' => $this->createUrl('imcoConsol/importImcoConsol'),
      ));
      ?>
      <div class="row">
        <label for="inward_excel">Excel - <small>.csv/.xls/.xlsx File</small> (<a href="../template/pre_alert_sea.xlsx" target="_blank">Sea Template file</a>)</label>
        <br />
        <input type="file" name="inward_excel" id="inward_excel" />
      </div>
      <br>

      <p><input id="wmstask_import_btn" type="submit" value="Submit" /></p>
      <?php $this->endWidget(); ?>
    </div>
  </div>

  


</div>
<script type="text/javascript">
  $(function() {
    var win = $('#jqmw_<?= $_GET["tabid"]; ?>');
    $('form.inco-import-form', win).on('success', function(e, r) {

    });
    $('#inco_consol_import-tabs', win).tabs();
  });
</script>