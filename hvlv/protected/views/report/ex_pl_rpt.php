<h2>Export P & L Report</h2>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'ex-pl-all-report-form',
	'enableAjaxValidation' => false,
	'action' => $this->createUrl('report/explReport'),
)); ?>

	<div class="row rowcol rowleft">
	<?php echo CHtml::label('Depot:','dt'); ?>
	<?php echo CHtml::dropDownList('wid', '', Org::dptList(), array('empty' => 'All')); ?>
	</div>
        <div class="row rowcol">
	<?php echo CHtml::label('Type:','type'); ?>
	<?php echo CHtml::dropDownList('type', 1, Array(1=>'By Consol',2=>'By Client',3=>'By Courier')); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('From Date:','fd'); ?>
		<?php echo CHtml::textField('datefrom', empty($_GET['datefrom'])? date('Y-m-d', strtotime('-7 day')) : $_GET['datefrom'], array('size' => 12, 'id' => 'fd_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>


	<div class="row rowcol">
		<?php echo CHtml::label('To Date:','td'); ?>
		<?php echo CHtml::textField('dateto', empty($_GET['dateto'])? date('Y-m-d') : $_GET['dateto'], array('size' => 12, 'id' => 'td_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?>
        <!-- <?php echo CHtml::submitButton($this->t('Export Details')); ?> -->
	</div>

<?php $this->endWidget(); ?>

    <div id="export_explsum_result" style="margin: 10px 0; border: 1px solid;padding:20px;display: none;">
    </div>

    <div id="ex-pl-sum-report-data" style="margin-top: 40px;">
        <?php
        $this->renderPartial('ex_pl_rpt_partial',['model' => $model,'filter'=>$filter,'total' => $total]);
        ?>

    </div>

</div><!-- form -->



<script type="text/javascript">
$(function(){
    var tab_id = '<?=$_GET["tabid"];?>';
    var tab = $('#'+tab_id);
    var panel = tab.data('panel');

   // $('#ex-pl-all-report-form', panel).on('submit', function(){
    //    $('#ex-pl-sum-report-grid', panel).yiiGridView('update', {data: $(this).serialize()});
 //       return false;
 //   });


    $('input[name="yt0"]',panel).click(function(e){
        e.preventDefault();
        e.stopPropagation();

        var data = $('#ex-pl-all-report-form',panel).serialize();

        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("report/ajaxExPlSumReport") ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#ex-pl-sum-report-data',panel).html(resp);
            }
        });

    });

    $('input[name="yt1"]',panel).click(function(e){
        e.preventDefault();
        e.stopPropagation();

        var data = $('#ex-pl-all-report-form',panel).serialize();
        $('#export_explsum_result',panel).html('');
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("report/ajaxExportExPlSumReport") ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#export_explsum_result',panel).show();
                $('#export_explsum_result',panel).html(resp);
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
