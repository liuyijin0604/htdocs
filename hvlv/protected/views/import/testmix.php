
<h1> Test Mix courier selection </h1>
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'testmix-import-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('import/Ajaxtestmix'),
        'htmlOptions' => [
            'class' => 'ifrm-form',
            'target' => $_GET["tabid"].'_ifrm',
            'enctype' => 'multipart/form-data',
        ]

    ));
    ?>


    <div class="row" style="margin-top: 20px;">
        <label for="postw-batch">Consignments List - <small>.xlsx File</small>(<a href="/ims/PCA_Express_Import_Template.xlsx" target="_blank">Get template file</a>)</label> <br>
        <input type="file" name="testmix_file" id="testmix_file" />
    </div>

    <div class="row">
        <?php echo CHtml::label('Charge Code','forchargecode'); ?>
        <?php echo CHtml::textField('chargecode',''); ?>
    </div>

    <input id="testfw_btn" type="submit" value="Test Mix Courier Selection" />

    <?php $this->endWidget(); ?>
</div>

<iframe name="<?=$_GET["tabid"];?>_ifrm" id="<?=$_GET["tabid"];?>_ifrm" src="" width="100%" height="400" border="0" style="border:1px #ccc solid; margin-top:10px;">
</iframe>
<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
    });
</script>
