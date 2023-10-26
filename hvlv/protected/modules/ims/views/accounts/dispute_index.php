<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'homeLink'=>CHtml::link('Home', array('site/index')),
    'links' => array(
        'Dispute Service',
    ),
));
?>
<ul class="nav nav-tabs" id="myTab<?=@$_GET['tabid']?>">
   <?php if(!isset($singleCheck)):?>
  <li><a href="#submitDisputCase<?=@$_GET['tabid']?>" data-toggle="tab">提交Dispute Case</a></li>
<?php endif;?>
  <li><a href="#disputeHistory<?=@$_GET['tabid']?>" data-toggle="tab">Dispute Case历史记录</a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane" id="submitDisputCase<?=@$_GET['tabid']?>">
    
<h1>Dispute Service</h1>
<div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'ds_qs_form',
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'post',
    )); ?>
        <div class="form-group">
                <?php  
                  echo $form->textArea($model,'comment',array('cols'=>60, 'rows' => 5,"class"=>"form-control")); 
                ?>
        </div>
        <br>
        <div class="form-group">
                <?php echo CHtml::label(Yii::t('shipmentquestion','Upload Template'),'Upload Template'); ?>
                <a href="../TLA_dispute_template.xlsx" target="_blank">get Template</a>
                 <?php
              $this->widget('CMultiFileUpload', array(
                 'model'=>$model,
                 'attribute'=>'files',
                 'accept'=>'xls|xlsx',
                 'htmlOptions'=>["accept"=>"xls,xlsx"],
                 'options'=>array(
                 ),
                 'denied'=>'File is not allowed',
                 'max'=>1, // max 10 files
              ));
            ?>
        </div>
         <br>

        <div class="form-group">
                 <?php echo CHtml::submitButton(Yii::t('shipmentquestion','submit'),array("class"=>"form-control update","style"=>"width:250px;","id"=>"submit_dispute")); ?>
                 <!--<?php echo CHtml::button(Yii::t('shipmentquestion','reset submit'),array("class"=>"form-control","style"=>"width:250px;","id"=>"refresh_dispute")); ?>-->
        </div>


    <?php $this->endWidget(); ?>
</div>



  </div>
  <div class="tab-pane" id="disputeHistory<?=@$_GET['tabid']?>">
      

    <br>
    <br>
    <br>
     <?php $this->widget('application.extensions.booster.TbExtendedGridView',array(
        'fixedHeader'=>true,
        'id'=>'qs_grid_view',
        'filter'=>$disputeTask,
        'type'=>'striped bordered',
        'headerOffset'=>40,
        'responsiveTable'=>true,
        'dataProvider'=>$disputeTask->search(),
        'template' => "{summary}\n{items}\n{pager}",
        'columns'=>array(
                array('name' => 'task_no'),
                array('name' => 'invNo','header' => Yii::t('shipmentquestion','Invoice No'),'value'=>'$data->getLinkObj()->inv_no'),
                array('name' => 'ref','header' => Yii::t('shipmentquestion','Ref'),'value'=>'$data->getLinkObj()->ref'),
                array('header' => Yii::t('shipmentquestion','Invoice Amount'),'value'=>'$data->getLinkObj()->invoice_amount'),
                array('header' => Yii::t('shipmentquestion','Customer Amount'),'value'=>'$data->getLinkObj()->customer_amount'),
                array('header' => Yii::t('shipmentquestion','Diff'),'value'=>'$data->getLinkObj()->diff'),
                 array('header' => Yii::t('shipmentquestion','Comment'),'value'=>'$data->getLinkObj()->comment'),
                array('header' => Yii::t('shipmentquestion','Dispute Type'),'value'=>'$data->getLinkObj()->dispute_type'),
                 array('header' => Yii::t('shipmentquestion','TLA op comment'),'value'=>'$data->getLinkObj()->tla_op_comment'),
                  array('header' => Yii::t('shipmentquestion','Credit Note No.'),'value'=>'$data->getLinkObj()->credit_note_no'),
                   array('header' => Yii::t('shipmentquestion','Credit Note Amount'),'value'=>'$data->getLinkObj()->credit_note_amount'),
                   array('header' => Yii::t('shipmentquestion','Handle Status'),'value'=>'$data->getLinkObj()->getHandleStatus()'),
            ),
        )
        
    );
    ?>


  </div>
</div>


<script type="text/javascript">
$(function(){
  $('#<?='ds_qs_form'.@$_GET['tabid']?>').on('success',function(){
    $("#qs_grid_view<?=@$_GET['tabid']?>").yiiGridView("update");
  });

     jQuery('#qs_grid_view<?=@$_GET['tabid']?>').yiiGridView({'ajaxUpdate':['qs_grid_view<?=@$_GET['tabid']?>'],'ajaxVar':'ajax','pagerClass':'no\x2Dclass','loadingClass':'grid\x2Dview\x2Dloading','filterClass':'filters','tableClass':'items\x20table\x20table\x2Dstriped\x20table\x2Dbordered','selectableRows':1,'enableHistory':false,'updateSelector':'\x7Bpage\x7D,\x20\x7Bsort\x7D','filterSelector':'\x7Bfilter\x7D','pageVar':'ShipmentQuestion_page','afterAjaxUpdate':function() {
            jQuery('.popover').remove();
            jQuery('[data-toggle=popover]').popover();
            jQuery('.tooltip').remove();
            jQuery('[data-toggle=tooltip]').tooltip();
        },'selectionChanged':function(id) {
                $("#"+id+" input[type=checkbox]").change();
            }});

    $('#myTab<?=@$_GET['tabid']?> a:first').tab('show');

    $('#myTab<?=@$_GET['tabid']?> a').click(function (e) {
      e.preventDefault();
      $(this).tab('show');
    })

    $('#ds_qs_form').on('submit',function(){
        $('#submit_dispute').attr('disabled','disabled');
    });

    $('#refresh_dispute').on('click',function(){
        $('#submit_dispute').removeAttr('disabled');
    });


});


</script>