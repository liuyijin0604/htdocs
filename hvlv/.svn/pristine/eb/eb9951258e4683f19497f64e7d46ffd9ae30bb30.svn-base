<div id="bluk_submit">

<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'homeLink'=>CHtml::link('Home', array('site/index')),
    'links' => array(
        'Customer Serivce',
    ),
));
?>
<ul class="nav nav-tabs" id="myTab<?=@$_GET['tabid']?>">
  <li><a href="#submit<?=@$_GET['tabid']?>" data-toggle="tab">多票查询以及意见反馈</a></li>
</ul>

<div class="tab-content">

  <div class="tab-pane active" id="submit<?=@$_GET['tabid']?>">
    
<h1>Customer Service</h1>
<h4>Shipments: <?=implode(',',array_column($shipments,"hbn"))?></h4>
<div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'cs_qs_form'.@$_GET['tabid'],
        'action'=>Yii::app()->createUrl('ims/customerService/uploadBlukNote'),
        'method'=>'post',
    )); ?>
        <div class="form-group">
                <?php if(!isset($singleCheck))// echo CHtml::label(Yii::t('shipmentquestion','Multiple reference numbers'),'ShipmentQuestionSubmit[mhbns]'),'<span class="required">*</span>'; ?>
                <?php 
                if(!isset($singleCheck))
                {
                    // echo CHtml::textArea('ShipmentQuestionSubmit[mhbns]','',array('cols'=>60, 'rows' => 5,"class"=>"form-control")); 
                    // echo "<p><small>Up to 200 numbers. Divided by ,; space or enter</small></p>";
                }else
                {
                    echo CHtml::textArea('ShipmentQuestionSubmit[shipmentIds]',$shipmentIds,array('cols'=>60, 'rows' => 5,"class"=>"form-control","style"=>"display:none")); 
                }
                ?>
        </div>
        <br>
        <div class="form-group">
                <?php echo CHtml::label(Yii::t('shipmentquestion','Email'),'Email'); ?><span class="required">*</span>
                <?php echo CHtml::textField('ShipmentQuestionSubmit[email]','',["class"=>"form-control","style"=>"width:250px;"]); ?>
        </div>
        <br>
        <div class="form-group">
                <?php echo CHtml::label(Yii::t('shipmentquestion','question'),'faq'); ?><span class="required">*</span>
                <?php echo CHtml::dropDownList('faq','',$faqList,["class"=>"form-control","style"=>"width:250px;"]); ?>
        </div>
        <br>
        <div class="form-group">
                <?php echo CHtml::label(Yii::t('shipmentquestion','note'),'ShipmentQuestionSubmit[c_note]'); ?><span class="required">*</span>
                 <?php echo CHtml::textArea('ShipmentQuestionSubmit[c_note]','',array('cols'=>60, 'rows' => 3,"class"=>"form-control")); ?>
        </div>
        <br>
        <div class="form-group">
                <?php echo CHtml::label(Yii::t('shipmentquestion','Upload Pictures'),'Upload Pictures'); ?>
                 <?php
                if(!isset($singleCheck))
                {
                    echo "<p><small>name the file as referenceNumber-1.jpg to match the reference numbers</small></p>";
                }
                ?>
                 <?php
              $this->widget('CMultiFileUpload', array(
                 'model'=>$shipmentQuestionSubmit,
                 'attribute'=>'photos',
                 'accept'=>'jpg|gif|png',
                 'htmlOptions'=>["accept"=>"image/gif, image/jpeg"],
                 'options'=>array(
                 ),
                 'denied'=>'File is not allowed',
                 'max'=>10, // max 10 files
              ));
            ?>
        </div>
         <br>

        <div class="form-group">
                 <?php echo CHtml::submitButton(Yii::t('shipmentquestion','submit'),array("class"=>"form-control update","style"=>"width:250px;")); ?>
        </div>


    <?php $this->endWidget(); ?>
</div>



  </div>

</div>


<script type="text/javascript">
$(function(){
  $('#<?='cs_qs_form'.@$_GET['tabid']?>').on('success',function(){
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

});


</script>

</div>