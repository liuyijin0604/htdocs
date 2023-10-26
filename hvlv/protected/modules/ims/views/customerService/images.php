<h1>详情: Ticket <?=$model->ShipmentQuestionSubmit->ticket?></h1>
<h4>转单号: <?=empty($model->Shipment)?"":$model->Shipment->ref?></h4>
<h4>邮箱地址: <?=$model->ShipmentQuestionSubmit->email?></h4>
<h4>备注:</h4>
<?= CHtml::textarea('note',$model->ShipmentQuestionSubmit->c_note,['class'=>'form-control','rows'=>8])?>
<br>
<?php
$fr = new FileRepo('search');
$fr->unsetAttributes();
if (empty($_GET['FileRepo'])) {
    $fr->theTypes = [133];
} else {
    $fr->attributes=$_GET['FileRepo'];
    if (empty($fr->theTypes)) {
        $fr->theTypes = [133];
    }
}
$fr->fid = $model->id;
$mf = Acl::hasAccess('C:CustomerService/getImages');

$this->widget('zii.widgets.grid.CGridView', [
    'id'=>'_excofile-grid',
    'cssFile' => false,
    'summaryText'=>'',
    'dataProvider'=> $fr->search(),
    'filter'=>$fr,
    'columns'=>[
        ['name' => 'name', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->baseUrl."/filerepo/".$data->hash."/".$data->name."\" target=\"_blank\">".$data->name."</a>"'],
        [
            'name'=>'size',
            'value'=>'$data->formatSize()',
            'filter' => false,
        ],
        'date'
    ],
]);
?>

<div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'images_form',
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'post',
    )); ?>
        <div class="form-group">
                <?php echo CHtml::hiddenField('ShipmentQuestion[id]',$model->id)?>
        </div>
        <br>
    
        <div class="form-group">
                <?php echo CHtml::label(Yii::t('shipmentquestion','Upload Pictures'),'Upload Pictures'); ?>
                 <?php
              $this->widget('CMultiFileUpload', array(
                 'model'=>$model->ShipmentQuestionSubmit,
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

<script type="text/javascript">
    $('#images_form').on('success',function(){
        $('#_excofile-grid').yiiGridView('update');
    });

</script>
