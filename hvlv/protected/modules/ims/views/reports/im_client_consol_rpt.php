<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'homeLink' => CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
    'links' => array(
        'Reports',
    ),
));
?>
<br>
<style type="text/css">
    .row
    {
        width:20rem;
        display:inline-block;
        margin-right: 10rem;
        margin-left: 0.1rem;
    }
</style>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
    'id' => 'im-client-consol-all-report-form',
    'enableAjaxValidation' => false,
    'action' => $this->createUrl('report/imClientConsolReport'),
)); ?>

   <div class="row" >
            <?php echo CHtml::label('Consol No.:','Consol No.'); ?>
            <?php echo CHtml::textField('consol', "",['class'=>'form-control']); ?>
    </div>

    <div class="row">
        <?php echo CHtml::label('From Date:','fd'); ?>
        <?php echo CHtml::dateField('datefrom', empty($_GET['datefrom'])? date('Y-m-d', strtotime('-7 day')) : $_GET['datefrom'], array('size' => 12, 'id' => 'fd','class' => 'form-control')); ?>
    </div>


    <div class="row">
        <?php echo CHtml::label('To Date:','td'); ?>
        <?php echo CHtml::dateField('dateto', empty($_GET['dateto'])? date('Y-m-d') : $_GET['dateto'], array('size' => 12, 'id' => 'td','class' => 'form-control')); ?>
    </div>

    <div class="row">
        <?php echo CHtml::hiddenField('org_id', User::currentUserOrgId()); ?>
    </div>

    <div class="buttons" style="margin-top: 1rem;">
        <?php echo CHtml::submitButton($this->t('Report')); ?>
        <?php echo CHtml::submitButton($this->t('Export Details')); ?>
    </div>

<?php $this->endWidget(); ?>
<style>
    .uploading { position: relative; clear:both; width: 150px; font-size: 1.4em; font-weight: bold; color: #BC3426; line-height: 32px; z-index: 99; padding: 15px 5px; margin-bottom: -50px; display: none; }
</style>
  <div class="uploading"><img src="https://os.toplogistics.com.au/css/images/ajaxLoader.gif" width="24" /> Loading</div>
<div id="im-client-consol-sum-report-data" style="margin-top: 20px;">
    <?php
    $this->renderPartial('im_pl_rpt_partial',['model' => $model,'filter'=>$filter,'total' => 0]);
    ?>

</div>

</div><!-- form -->
 <iframe id="ifrm" name="ifrm" style="display:none"></iframe>

<script type="text/javascript">
$(function(){
   // $('#im-pl-all-report-form', panel).on('submit', function(){
    //    $('#im-pl-sum-report-grid', panel).yiiGridView('update', {data: $(this).serialize()});
 //       return false;
 //   });


    $('input[name="yt0"]').click(function(e){
        e.preventDefault();
        e.stopPropagation();

        var data = $('#im-client-consol-all-report-form').serialize();
        $('.uploading').fadeIn();
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createUrl("ims/reports/ajaxImClientConsolReport") ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#im-client-consol-sum-report-data').html(resp);
                $('.uploading').fadeOut();
            }
        });

    });

    $('input[name="yt1"]').click(function(e){
        e.preventDefault();
        e.stopPropagation();

        var data = $('#im-client-consol-all-report-form').serialize();
        $('.uploading').fadeIn();
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createUrl("ims/reports/ajaxExportImClientConsolReport") ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#ifrm').attr('src', resp);
                $('.uploading').fadeOut();
            }
        });
    });


    $('#fd').on('change', function(){
        var v = $(this).val();
        var ld = new Date(v.substr(0, 4), parseInt(v.substr(5,2)), 0).getDate();
        $('#td', panel).val(v.substr(0,8) + ld);
    });
});
</script>
