<?php
/* @var $this ErpProducts */

$this->breadcrumbs=array(
    'Erp Products'=>array('/erpProductRoute'),
    'Reports',
);
?>
<h1><?php echo $this->t('Consume Goods Reporting'); ?></h1>


<div class="form">

    <div id="wh-report-data">
        <?php $this->renderPartial('_wh_reports',array('model' => $model)); ?>
    </div>


    <div id="driver-report-data">
        <?php $this->renderPartial('_driver_reports',array('model' => $model)); ?>
    </div>



    <div id="agent-report-data">
        <?php $this->renderPartial('_agent_reports',array('model' => $model)); ?>
    </div>


</div>


<script type="text/javascript">

    function whsend(){
        var data = $('#erp-whreport-form').serialize();

        $.ajax({
            type : 'POST',
            url : '<?php echo Yii::app()->createAbsoluteUrl("ErpProductRoute/ajaxWhReport") ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#wh-report-data').html(resp);
            }
        });
    }

    function driversend(){
        var data = $('#erp-driverreport-form').serialize();

        $.ajax({
            type : 'POST',
            url : '<?php echo Yii::app()->createAbsoluteUrl("ErpProductRoute/ajaxDriverReport") ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#driver-report-data').html(resp);
            }
        });
    }

    function agentsend(){
        var data = $('#erp-agentreport-form').serialize();

        $.ajax({
            type : 'POST',
            url : '<?php echo Yii::app()->createAbsoluteUrl("ErpProductRoute/ajaxAgentReport") ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#agent-report-data').html(resp);
            }
        });
    }



</script>


