<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'cwc-attach-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('consolWeightCheck/AjaxSaveAttach'),
    ));
    ?>
    <div class="row">
        <label for="cwcattach"> Attached some files - <small>.xlsx,.jpg,.pdf,.png File</small></label><br>
        <input type="file" name="cwc_attach" id="cwc_attach" />
    </div>
    <?php echo $form->hiddenField($model,'id'); ?>

    <br>

    <p><input id="cwc_attach_btn" type="submit" value="Submit" /></p>
    <?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
    $(function(){

        var win = $('#jqmw_<?=$_GET["tabid"];?>');

        $('form#cwc-attach-form', win).on('success', function(e, r){
            win.data('opener').trigger('onOpen');
            win.jqmHide();
        });

    });
</script>