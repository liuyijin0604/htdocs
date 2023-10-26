
<h1> EDI Job Import </h1>
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'edi-job-import-form',
        'enableAjaxValidation'=>false,
    //    'action' => $this->createUrl('invoice/AjaxEdiJobImport'),
    ));
    ?>


    <div class="row" style="margin-top: 20px;">
        <label for="postw-batch">EDI Job AR invoice <small>.xlsx File</small></label> <br>
        <input type="file" name="edijob_ar_file" id="edijob_ar_file" />
    </div>

    <div class="row" style="margin-top: 20px;">
        <label for="postw-batch">EDI Job AP invoice <small>.xlsx File</small></label> <br>
        <input type="file" name="edijob_ap_file" id="edijob_ap_file" />
    </div>

    <p style="margin-top:20px;"><input id="tmc_btn" type="submit" value="Submit" /></p>

    <?php $this->endWidget(); ?>
</div>
<div id="tmc_result" style="margin: 10px 0; border: 1px solid;padding:20px;">
</div>



<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
    });
</script>
