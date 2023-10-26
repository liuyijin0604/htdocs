
<h1> Tell Me Cost </h1>
<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'tellme-cost-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('import/AjaxTmc'),
    ));
    ?>


    <div class="row" style="margin-top: 20px;">
        <label for="postw-batch">Postcode & Weight List - <small>.xlsx File</small>(<a href="/ims/postcode_w_check.xlsx" target="_blank">Get template file</a>)</label> <br>
        <input type="file" name="tmc_file" id="tmc_file" />
    </div>
    <br>
    Weight Ranges : <input type="text" name="wrange" value="0-0.5-1-3-5-10-20-30-50-100-200"><br>
    Separated by '-' (ex. 0-0.5-1-3 means 0 - 0.5kg, 0.5 - 1kg, 1 - 3kg , more 3kg)
    <br/>
    <br>
    Fixed Charge By Piece : <input type="number" name="fixed_rate" value="0">
    <br/>
    <br>
    Profit Ratio : <input type="number" name="profit_rate" value="0">%
    <br/>

    <div class="row" style="margin-top: 20px;">
        <?php
         $defSelected = array();
        foreach ( $orgrates as $rate  ) {
            $defSelected[] = $rate['id'];
        }
        $rateList = CHtml::listData($orgrates,'id','name');
        echo CHtml::checkBoxList('selected_rate',$defSelected,$rateList,array(
            'template'=>'{input}{label}',
            'separator'=>'',
            'labelOptions'=>array(
                'style'=> 'padding-right:12px;min-width: 60px;float: left;'),
            'style'=>'float:left;',) );
        ?>

    </div>


    <!--
    <div style="margin-top: 20px;"></div>
    Profit Rate : <input type="number" max="100" min="0" name="profit_rate" value="0">%
    <br/>
-->
    <input id="tmc-op-type" type="hidden" name="op" value="show">
    <p style="margin-top:20px;"><input id="tmc_btn" type="submit" value="Submit" /> &nbsp;&nbsp; <input id="tmc_export_btn" type="submit" value="Export as Excel" /></p>




    <?php $this->endWidget(); ?>
</div>
<div id="tmc_result" style="margin: 10px 0; border: 1px solid;padding:20px;">
</div>



<script type="text/javascript">


    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');

        $('#tmc_export_btn',panel).click(function(e){
            e.preventDefault();
            $('#tmc-op-type',panel).val('export');
            $('form#tellme-cost-form', panel).submit();
        });

        $('#tmc_btn',panel).click(function(e){
            e.preventDefault();
            $('#tmc-op-type',panel).val('show');
            $('form#tellme-cost-form', panel).submit();

        });

        $('form#tellme-cost-form', panel).data('custom_success', function(r){
            $('#tmc_result', panel).empty().prepend($('<p>'+r.msg+'</p>').fadeIn());
            $('#tmc_btn', panel).attr('disabled', false);
            $('#tmc_export_btn', panel).attr('disabled', false);
            return true;
        });

    });
</script>
