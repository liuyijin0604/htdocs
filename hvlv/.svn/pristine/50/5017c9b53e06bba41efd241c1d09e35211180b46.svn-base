<?php
echo "<h3>Are you Sure to take the task ".$_GET['id']."?</h3><hr>";
echo '<h4>Goods Information:</h4>';
echo '<b>Reference:</b>'.$model->ref.'<br><b>Pallet Number:</b>'.$model->plt.'&nbsp &nbsp &nbsp <b>Cubic Meters(M<sup>3</sup>): </b>'.$model->cbm.'&nbsp &nbsp &nbsp <b>Weight(Kg): </b>'.$model->kg.'<br>';
      if(!empty($model->mdata)&&is_array($model->mdata)){
                 foreach ($model->mdata as $key=>$value){
                     if($key=='notes'){
                       echo '<b>Op Notes:</b>';
                     if(is_array($value)){
                         echo  (empty($value['op'])?'':$value['op']).'<br>';
                     }
                   }
                 }
             }
 echo '<hr>';
?>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'edit-notes-form',
    'enableAjaxValidation' => false,
    'action'=>$this->createUrl('cartage/take')
      ));
?>
 <div class="form-group ">
         <label for="rego">Rego</label>
         <input type="text" class="form-control" id="rego" name="rego">

        </div>
<div class="form-group">
    <label for="exampleTextarea">Your Notes</label>
    <textarea class="form-control"  rows="2" id="notes" name='notes' rows="3"></textarea>
</div>
<input type="hidden" value="<?=$_GET['id']?>" name='id'>

<div class="form-group">
<?php echo CHtml::submitButton('take', $htmlOptions = ['class' => 'btn btn-primary ','id'=>'my_btn']); ?>
</div>
 <?php $this->endWidget(); ?>
<script>
    
    $(function(){
      $('form#edit-notes-form').on('success',function(){
          $('#modal_close').trigger('click');
          $("task-list-grid").yiiGridView.update("task-list-grid");
     
        });
    });