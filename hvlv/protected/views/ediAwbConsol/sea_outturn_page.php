<h3><?=empty($title)?$this->t('sea_outturn'):$this->t($title);?></h3>
<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'sea-outturn',
        'enableAjaxValidation'=>false,
    )); ?>
    <?php if(empty($title)):?>
        <div class="row">
            <?php echo CHtml::label('Type','Type');?>
            <?php echo CHtml::dropDownList('type',1,["0"=>"without_coverpage","1"=>"with_coverpage"],["id"=>"type"]);?>
        </div>
    <?php endif; ?>
    <?php if(!empty($title)):?> 
        <div class="row">
            <?php echo CHtml::label('Type','Type');?>
            <?php echo CHtml::dropDownList('type',1,["1"=>"manifest","2"=>"scan"],["id"=>"type"]);?>
        </div>
    <?php endif; ?>
    <div class="row">
        <?php echo CHtml::checkboxList('couriers',0,$courierList,["id"=>"couriers"]);?>
    </div>
    <br/>

    <div class="row buttons">
       <a id="export_sea" style = "float:right;" target="_blank" href=""><div class="icon"></div>Export</a> 
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');

        var win = $('#jqmw_<?=$_GET["tabid"];?>');

        $('#export_sea', win).on('mousedown', function(e, r){
            var type = $('#type', win).val();
            var courier = "";
            $.each($('input:checkbox',win),function(){
                if(this.checked)
                {
                  courier+="&&courier[]="+$(this).val();
                }
            });

            $(this).attr('href', '<?=$this->createUrl(empty($link)?'imcoConsol/seaOutturnShipments':$link);?>'+'?id=<?=$id?>&&type='+type+courier);
            return true;
        });

    });
</script>
