<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'edi-attach-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('EdiJob/AjaxSaveAttach'),
    ));
    ?>
    <div class="row">
        <label for="cwcattach"> Attached some files - <small>.xlsx,.jpg,.pdf,.png File</small></label><br>
        <input type="file" name="edi_attach" id="edi_attach" />
    </div>
    <?php echo $form->hiddenField($model,'id'); ?>

    <br>

    <p><input id="edi_attach_btn" type="submit" value="Submit" /></p>
    <?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
    $(function(){

        var win = $('#jqmw_<?=$_GET["tabid"];?>');

        $('form#edi-attach-form', win).on('success', function(e, r){
            win.data('opener').trigger('onOpen');
            win.jqmHide();
        });

    });
</script>