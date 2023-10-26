<?php
// $this->widget('zii.widgets.CBreadcrumbs', array(
//         'homeLink'=>CHtml::link('Home', array('site/index')),
// 	'links' => array(
//            'Customer Service',
// 	),
// ));
?>
<h1>Customer Service</h1>
<div class="form">
        <br>
        <div class="form-group">
                <?php echo CHtml::label("Tracking Number: ".$model->Shipment->ref,"Tracking Number: ".$model->Shipment->ref); ?>
        </div>
        <br>
        <div class="form-group">
            <?php echo CHtml::label("Ticket Number: ".$model->ShipmentQuestionSubmit->ticket,"Ticket Number: ".$model->ShipmentQuestionSubmit->ticket); ?>
        </div>
        <br>
        <div class="form-group">
                <?php echo CHtml::label(Yii::t('shipmentquestion','question'),'faq'); ?><span class="required">*</span>
                <?php echo CHtml::dropDownList('ShipmentQuestionSubmit[faq]',@$model->ShipmentQuestionSubmit->faq,$faqList,["class"=>"form-control","style"=>"width:250px;"]); ?>
        </div>
        <br>
        <div class="form-group">
                <?php echo CHtml::label(Yii::t('shipmentquestion','customer note'),'ShipmentQuestionSubmit[c_note]'); ?>
                 <?php echo CHtml::textArea('ShipmentQuestionSubmit[c_note]',@$model->ShipmentQuestionSubmit->c_note,array('cols'=>60, 'rows' => 3,"class"=>"form-control","disabled"=>"disabled")); ?>
        </div>
        <br>
        <div class="form-group">
                <?php echo CHtml::label(Yii::t('shipmentquestion','Items Detail(Brand, Name, Weight, Color, Size, Quantity)'),'Items Detail(Brand, Name, Weight, Color, Size, Numbers)'); ?>
                 <?php echo CHtml::textArea('ShipmentQuestion[mdata][items]',@$model->mdata['items'],array('cols'=>60, 'rows' => 3,"class"=>"form-control","disabled"=>"disabled")); ?>
        </div>

        <br>
        <div class="form-group">
                <?php echo CHtml::label(Yii::t('shipmentquestion','response'),'ShipmentQuestionSubmit[s_note]'); ?>
                 <?php echo CHtml::textArea('ShipmentQuestionSubmit[s_note]',@$model->s_note,array('cols'=>60, 'rows' => 3,"class"=>"form-control","disabled"=>"disabled")); ?>
        </div>
</div>

<br>
<br>
<br>