<?php
echo "<h3>Are you sure finish task ".$_GET['id']."?</h3><hr>";
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
    'htmlOptions'=>['target'=>'err_result'],
    'action'=>$this->createUrl('cartage/addNotes')
      ));
?>
<div class="form-group">
    <label for="exampleTextarea">Notes</label>
    <textarea class="form-control"  rows="4" id="notes" name='notes' rows="3"></textarea>
    <!--<input type="text" class="ui-datepicker-year"/>-->
</div>
<input type="hidden" value="<?=$_GET['id']?>" name='id'>
<input type="hidden" value="<?=Yii::app()->user->id?>" name="user">
 

<div class="form-group">
<?php echo CHtml::submitButton('Submit', $htmlOptions = ['class' => 'btn btn-primary ','id'=>'my_btn']); ?>
</div>
 <?php $this->endWidget(); ?>
<script>
    
    $(function(){
      $('form#edit-notes-form').on('success',function(){
          $('#modal_close').trigger('click');
          $("take-list-grid").yiiGridView.update("take-list-grid");
      
   
        });
    });
 
 </script>