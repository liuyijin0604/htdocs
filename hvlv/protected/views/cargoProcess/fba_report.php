<h2>FBA Report</h2>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
    'id' => 'fba-report-form',
    'enableAjaxValidation' => false,
    'action' => $this->createUrl('topCourierService/fbaReport'),
)); ?>

    <div class="row rowcol rowleft">
        <?php echo CHtml::label('Depot:','dt'); ?>
        <?php echo CHtml::dropDownList('wid', 106, CargoProcess::getCargoProcesPodList()); ?>
    </div>

    <div class="row rowcol">
        <?php echo CHtml::label('Sub Warehouse:','dt'); ?>
        <?php echo CHtml::dropDownList('swid', 0,['0'=>'ALL','106-1'=>'BWU1','106-2'=>'BWU2','218-1'=>'MEL1','218-5'=>'MEL5']); ?>
    </div>
    <div class="row rowcol">
        <?php echo CHtml::label('Type:','type'); ?>
        <?php echo CHtml::dropDownList('type', 1, Array(1=>'Forecast',2=>'Waiting for dispatch',3=>'Daily',4=>'Weekly Report')); ?>
    </div>

    <div class="row rowcol">
        <?php echo CHtml::label('From Date:','td'); ?>
        <?php echo CHtml::textField('datefrom', empty($_GET['datefrom'])? date('Y-m-d') : $_GET['datefrom'], array('size' => 12, 'id' => 'fd_'.$_GET["tabid"],'class' => 'date_input')); ?>
    </div>

    <div class="row rowcol">
        <?php echo CHtml::label('To Date:','td'); ?>
        <?php echo CHtml::textField('dateto', empty($_GET['dateto'])? date('Y-m-d') : $_GET['dateto'], array('size' => 12, 'id' => 'td_'.$_GET["tabid"],'class' => 'date_input')); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($this->t('Report'),["id"=>'fba_report_submit']); ?>
        <?php echo CHtml::submitButton($this->t('Export Details')); ?>
    </div>

<?php $this->endWidget(); ?>

<div id="fba-report-data" style="margin-top: 20px;">
   
</div>

</div><!-- form -->
 <iframe id="ifrm" name="ifrm" style="display:none"></iframe>

<script type="text/javascript">
$(function(){
    var tab_id = '<?=$_GET["tabid"];?>';
    var tab = $('#'+tab_id);
    var panel = tab.data('panel');

   // $('#im-pl-all-report-form', panel).on('submit', function(){
    //    $('#im-pl-sum-report-grid', panel).yiiGridView('update', {data: $(this).serialize()});
 //       return false;
 //   });


    $('input[name="yt0"]',panel).click(function(e){
        e.preventDefault();
        e.stopPropagation();

        var data = $('#fba-report-form',panel).serialize();

        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("topCourierService/fbaReport") ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#fba-report-data',panel).html(resp);
            }
        });

    });

    $('input[name="yt1"]',panel).click(function(e){
        e.preventDefault();
        e.stopPropagation();

        var data = $('#fba-report-form',panel).serialize()+"&&export=1";
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("topCourierService/fbaReport") ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#ifrm', panel).attr('src', resp);
            }
        });
    });


    $('#fd_'+tab_id, panel).on('change', function(){
        var v = $(this).val();
        var ld = new Date(v.substr(0, 4), parseInt(v.substr(5,2)), 0).getDate();
        $('#td_'+tab_id, panel).val(v.substr(0,8) + ld);
    });
    
});
</script>
