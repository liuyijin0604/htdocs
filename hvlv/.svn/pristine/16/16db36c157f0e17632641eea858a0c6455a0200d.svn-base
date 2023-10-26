
<h1> Test Send by Fastway </h1>
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'testfw-import-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('import/Ajaxtestfw'),
    ));
    ?>


    <div class="row" style="margin-top: 20px;">
        <label for="postw-batch">Consignments List - <small>.xlsx File</small>(<a href="/ims/PCA_Express_Import_Template.xlsx" target="_blank">Get template file</a>)</label> <br>
        <input type="file" name="testfw_file" id="testfw_file" />
    </div>

    <input id="testfw_btn" type="submit" value="Send By Fastway" />

    <?php $this->endWidget(); ?>
</div>
<br>
<br>
<h1> Check Parcel </h1>
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'testfw-check-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('import/AjaxFastwayCheck'),
    ));
    ?>

<div class="row">
    <div class="rowcol">
        <?php echo CHtml::label('Suburb','forfwcheck'); ?>
        <?php echo CHtml::textField('suburb','',array('size'=>50,'maxlength'=>255)); ?>
    </div>

    <div class="rowcol">
        <?php echo CHtml::label('State','forfwcheck'); ?>
        <?php echo CHtml::dropDownList('state', 0, array('ACT' => 'ACT - Australia Capital Territory', 'NSW' => 'NSW - New South Wales', 'NT' => 'NT - Northern Territory', 'QLD' => 'QLD - Queensland', 'SA' => 'SA - South Australia', 'TAS' => 'TAS - Tasmania', 'VIC' => 'VIC - Victoria', 'WA' => 'WA - Western Australia'), array('empty' => $this->t('Select One'), 'style' => 'width:100px')); ?>
    </div>

    <div class="rowcol">
        <?php echo CHtml::label('Postcode','forfwcheck'); ?>
        <?php echo CHtml::textField('postcode','',array('size'=>50,'maxlength'=>255)); ?>
    </div>

    <div class="rowcol">
        <?php echo CHtml::label('Weight','forfwcheck'); ?>
        <?php echo CHtml::textField('weight','',array('size'=>50,'maxlength'=>255)); ?>
    </div>
</div>
<br/>
    <input id="testfw_check_btn" type="submit" value="Check" />

    <?php $this->endWidget(); ?>
</div>

<br/><br/>
<div id="testfw_result" style="margin: 10px 0; border: 1px solid;padding:20px;">
</div>


<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');

        $('form#testfw-check-form', panel).data('custom_success', function(r){
            $('#testfw_result', panel).empty();
            $('#testfw_result', panel).prepend($('<p>'+r.msg+'</p>').css('color', r.color).fadeIn());
            $('input[type="submit"]',panel).prop('disabled',false);
            return true;
        })

    });


</script>
