<?php
/* @var $this ImportsMailController */
/* @var $model ImportsMail */
/* @var $form CActiveForm */
?>
<div class="pane">
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'imports-mail-form',
	'enableAjaxValidation'=>true,
)); ?>
       <div class="row"><b>Received Time:</b><?=$model->create_time?></div>
       <hr/>
	<div class="row">
		<?php echo $form->labelEx($model,'from_email'); ?>
		<?php echo $form->textField($model,'from_email',array('size'=>60,'maxlength'=>200));?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'from_name'); ?>
		<?php echo $form->textField($model,'from_name',array('size'=>60,'maxlength'=>200)); ?>
	</div>
    <?php 
    if(!empty($model->mdata['cc'])){
        $cc='';
        foreach($model->mdata['cc'] as $c){
            $cc.=$c['address'].";";
        }
        if(!empty($cc)){
            echo '<div class="row">';
            echo CHtml::label('CC','cc_mail');
            echo CHtml::textField('cc',$cc,array('size'=>60,'maxlength'=>500));
        }
       }
    ?>
	<div class="row">
		<?php echo $form->labelEx($model,'to_email'); ?>
		<?php echo $form->textField($model,'to_email',array('size'=>60,'maxlength'=>500)); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'to_name'); ?>
		<?php echo $form->textField($model,'to_name',array('size'=>60,'maxlength'=>60)); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'subject'); ?>
		<?php echo $form->textField($model,'subject',array('size'=>60 )); ?>
	</div>

  <?php $this->endWidget(); ?>

</div>
</div>
  <div style="width: 90%;">
      <h3>Attachment</h3>
  <div>
      <div style="width: 50%; margin: 0;padding: 0;">
  <?php

$fr = new FileRepo('search');
$fr->unsetAttributes();
$fr->non_status=[0];
if(empty($_GET['FileRepo'])){
	
	$fr->theTypes = [28];
}else{
	$fr->attributes=$_GET['FileRepo'];
	if(empty($fr->theTypes)) $fr->theTypes = [28];
}
$fr->fid = $model->id;
$mf = Acl::hasAccess('B:org/manageFile');

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET['tabid'].'_excofile-grid',
	'cssFile' => false,
	'summaryText'=>'',
	'dataProvider'=> $fr->search(),
	'filter'=>$fr,
	'columns'=>array(
		array('name' => 'name', 'type' => 'raw', 'value' => '"<a href=\"".$data->getUrl()."\" target=\"_blank\">".$data->name."</a>"'),
		array(
			'name'=>'size',
			'value'=>'$data->formatSize()',
			'filter' => false,
		),
		'date',
	),
));
?>
      </div>
  </div>
      <div id="accordion<?=$_GET['tabid']?>" style="max-width:1000px;" >
  <h3>Content</h3>
  <iframe src="<?=$this->createUrl('salesfunnelCustomerLogin/body', ['id' => $model->id]);?>" width="100%" border="0" height="500"></iframe>
  </div>
</div>
  </div>