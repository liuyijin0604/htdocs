<h2>Import P & L Report (Management)</h2>
<div style="right: 20px; position: absolute; z-index: 9999">
<a href="<?=$this->createUrl('billing/export', array('t' => 'pcae_in_tla'))?>" class="jqm_link"><div style="background-position:-48px -688px" class="icon"></div> Invoice Report</a>
</div>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
    'id' => 'im-pl-all-report-form',
    'enableAjaxValidation' => false,
    'action' => $this->createUrl('report/implReport'),
)); ?>

    <div class="row rowcol rowleft">
    <?php echo CHtml::label('Depot:','dt'); ?>
    <?php echo CHtml::dropDownList('wid', '', Org::dptList(), array('empty' => 'All')); ?>
    </div>
    <div class="row rowcol">
    <?php echo CHtml::label('Type:','type'); ?>
    <?php echo CHtml::dropDownList('type', 1, Array(9=>'By Depot',1=>'By Consol',2=>'By Client',3=>'By Courier',4 =>'By Invoice',5 =>'By Zone',6 =>'By Weight', 7 => 'By Cost (Consol ETA)', 8 => 'By Cost (Invoice Date)')); ?>
    </div>

    <div id ="forZoneWeight" style="display: none">
        <div class="row">
        <?php echo CHtml::label('Client:','Client'); ?>
        <?php 
            echo CHtml::hiddenField('org_id');
            $acname = empty($_GET["tabid"])? 'user_org_ac' : $_GET["tabid"].'_org_ac';
            $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                'name' => $acname,
                'sourceUrl' => array('org/userSuggest'),
                'value' => '',
                'options' => array(
                        'showAnim' => 'fold',
                        'minLength' => 2,
                        'delay' => 200,
                        'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
                        'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val(""); return false; }',
                ),
                'htmlOptions' => array(
                    'class' => 'required',
                    'size' => '50',
                ),));
        ?>
        </div>
        <div id = "otherField" class="row" >
            <div  class="row rowcol">
            <?php echo CHtml::label('Courier:','courier'); ?>
            <?php echo CHtml::dropDownList('courier', 1,Org::getAllCouriers(),['prompt'=>'All']); ?>
            </div>

             <div class="row rowcol">
            <?php echo CHtml::label('Consol No.:','Consol No.'); ?>
            <?php echo CHtml::textField('consol', ""); ?>
            </div>
        </div>
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
        <?php echo CHtml::submitButton($this->t('Export Details')); ?>
    </div>

<?php $this->endWidget(); ?>
<style>
    .uploading { position: relative; clear:both; width: 150px; font-size: 1.4em; font-weight: bold; color: #BC3426; line-height: 32px; z-index: 99; padding: 15px 5px; margin-bottom: -50px; display: none; }
</style>
  <div class="uploading"><img src="https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif" width="24" /> Loading</div>
<div id="im-pl-sum-report-data" style="margin-top: 20px;">
    <?php
    $this->renderPartial('im_pl_rpt_partial',['model' => $model,'filter'=>$filter,'total' => $total]);
    ?>

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

        var data = $('#im-pl-all-report-form',panel).serialize();
        $('.uploading', panel).fadeIn();
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("report/ajaxImPlSumReport") ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#im-pl-sum-report-data',panel).html(resp);
                $('.uploading', panel).fadeOut();
            }
        });

    });

    $('input[name="yt1"]',panel).click(function(e){
        e.preventDefault();
        e.stopPropagation();

        var data = $('#im-pl-all-report-form',panel).serialize();
        $('.uploading', panel).fadeIn();
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("report/ajaxExportImPlSumReport") ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#ifrm', panel).attr('src', resp);
                $('.uploading', panel).fadeOut();
            }
        });
    });

    $('#type',panel).on('change',function(e){
        if($('#type',panel).val()==5||$('#type',panel).val()==6)
        {
            $('#forZoneWeight').show();
            $('#otherField').show();
        }else if($('#type',panel).val()==3)
        {
            $('#forZoneWeight').show();
            $('#otherField').hide();
        }else
        {
             $('#forZoneWeight').hide();
            $('#otherField').hide();
        }


    });

    $('#fd_'+tab_id, panel).on('change', function(){
        var v = $(this).val();
        var ld = new Date(v.substr(0, 4), parseInt(v.substr(5,2)), 0).getDate();
        $('#td_'+tab_id, panel).val(v.substr(0,8) + ld);
    });
});
</script>
