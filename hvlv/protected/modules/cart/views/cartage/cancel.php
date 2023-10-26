<?php

echo "<h2>Are you Sure to Cancel the task?</h2>";

?>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'edit-notes-form',
    'enableAjaxValidation' => false,
    'action'=>$this->createUrl('cartage/cancel')
      ));
?>
<div class="form-group">
    <label for="exampleTextarea">Cancel Notes</label>
    <textarea class="form-control"  rows="2" id="notes" name='notes' rows="3"></textarea>
</div>
<input type="hidden" value="<?=$_GET['id']?>" name='id'>

<div class="form-group">
<?php echo CHtml::submitButton('Confirm', $htmlOptions = ['class' => 'btn btn-primary ','id'=>'my_btn']); ?>
</div>
 <?php $this->endWidget(); ?>
<script>
    
    $(function(){
      $('form#edit-notes-form').on('success',function(){
          $('#modal_close').trigger('click');
          $("task-list-grid").yiiGridView.update("task-list-grid");
     
        });
    });