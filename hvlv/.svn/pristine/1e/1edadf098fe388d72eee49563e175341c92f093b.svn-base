<h2>Import Courier Report</h2>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'im-courier-report-form',
	'enableAjaxValidation' => false,
	'action' => $this->createUrl('report/implReport'),
)); ?>

	<div class="row rowcol rowleft">
	<?php echo CHtml::label('Depot:','dt'); ?>
	<?php echo CHtml::dropDownList('wid', '', Org::dptList(), array('empty' => 'All')); ?>
	</div>
    <div class="row rowcol">
	<?php echo CHtml::label('Type:','type'); ?>
	<?php echo CHtml::dropDownList('type', 1, Array(1=>'Time Report',2=>'RTS Report',3=>'Manifest Report')); ?>
	</div>

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

    <div class="row rowcol">
    <?php echo CHtml::label('Courier:','courier'); ?>
    <?php echo CHtml::dropDownList('courier', 1,Org::getAllCouriers(),['prompt'=>'All']); ?>
    </div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('From Date:','fd'); ?>
		<?php echo CHtml::textField('datefrom', empty($_GET['datefrom'])? date('Y-m-d', strtotime('-7 day')) : $_GET['datefrom'], array('size' => 12, 'id' => 'fd_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>


	<div class="row rowcol">
		<?php echo CHtml::label('To Date:','td'); ?>
		<?php echo CHtml::textField('dateto', empty($_GET['dateto'])? date('Y-m-d') : $_GET['dateto'], array('size' => 12, 'id' => 'td_'.$_GET["tabid"],'class' => 'date_input')); ?>
        <?php echo CHtml::hiddenField('openStateRowKeys_manifested','');?>
        <?php echo CHtml::hiddenField('openStateRowKeys_delivered','');?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report'),["id"=>'icr_report_submit']); ?>
        <?php echo CHtml::submitButton($this->t('Export Details')); ?> <?php echo CHtml::checkbox('exportState',0); ?> exportState
	</div>

<?php $this->endWidget(); ?>

<div id="im-courier-report-data" style="margin-top: 20px;">
   
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

        var data = $('#im-courier-report-form',panel).serialize();

        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("report/ajaxImCourierReport") ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#im-courier-report-data',panel).html(resp);
            }
        });

    });

    $('input[name="yt1"]',panel).click(function(e){
        e.preventDefault();
        e.stopPropagation();

        var data = $('#im-courier-report-form',panel).serialize();
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("report/ajaxExportImCourierReport") ;?>',
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
