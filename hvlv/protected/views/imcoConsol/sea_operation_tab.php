<h3>Sea Process Operation</h3>
<br/>
<div class="pane">
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'sea-process-operation-form'.$_GET['tabid'],
	'enableClientValidation'=>true,
        'action'=> $this->createURL('ConsolProcess/seaOperation',array('id'=>$model->id)),
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
));
?>
<div class="row">
<label>To Do List:</label>
<table style="table-layout: fixed"  border="0" cellspacing="0" cellpadding="0" width="50%">
    <?php 
       $index=0;
       $result="";
       foreach (ConsolProcess::$seaStates as $key=>$value){
           if($index%4==0){
               $result.="<tr>";
            }
            $checked= (($model->process->status & $key) >0) ? true : false;
            $result.="<td width='25%'><input type='checkbox' id='".$key."_sea_status' name=seaStates[] value='$key' ".($checked?"checked":"")."/> ".$value."</td>";
            $index++;
             if($index%4==0){
               $result.="</tr>";
            }
       }
     echo $result;
    ?>
</table>
</div>
     <div class="row buttons">
         <?php echo CHtml::submitButton('Update'); ?>
    </div>
    </div>
<?php $this->endWidget(); ?>
</div>
<script>
    $(function(){
        var win = $('#jqmw_<?=$_GET["tabid"];?>');
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = $('#<?=$_GET["tabid"];?>').data('panel');         
    });
</script>