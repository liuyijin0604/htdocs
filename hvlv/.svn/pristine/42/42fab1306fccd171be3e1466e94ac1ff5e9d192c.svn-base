<h3><?=empty($title)?$this->t('Cargo Receipt Page'):$this->t($title);?></h3>
<div class="form">


    <div class="row">
        <?php echo CHtml::dropDownList('couriers',0,$courierList,["id"=>"couriers"]);?>
    </div>

    <div class="row">
        Cargo Receipt Address:<?php echo CHtml::textArea('address',"",["id"=>"address","rows"=>5,"cols"=>30]);?>
    </div>

    <br/>

    <div class="row buttons">
       <a id="export_sea" style = "float:right;" target="_blank" href=""><div class="icon"></div>Export</a> 
    </div>


</div><!-- form -->
<script type="text/javascript">
    $(function(){
        var win = $('#jqmw_<?=$_GET["tabid"];?>');
        var panel = win.data('panel');

        var win = $('#jqmw_<?=$_GET["tabid"];?>');

        $('#export_sea', win).on('mousedown', function(e, r){
            var courier = "";
            courier+="&&courier="+$("#couriers",panel).val();
            var address = $("#address",panel).val();
            $(this).attr('href', '<?=$this->createUrl(empty($link)?'imcoConsol/exportCargoReceiptNew':$link);?>'+'?id=<?=$id?>'+courier+'&&address='+address);
            return true;
        });

    });
</script>
