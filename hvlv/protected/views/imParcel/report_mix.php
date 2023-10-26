<div class="pane">
<h3>Mixed Mild Cost Compare</h3>
<br/>
<div class="form">    
    <div>
        <input type="hidden" name='tabid' value="<?=$_GET['tabid']?>"/>
    </div>
       <div class="row">
           <?php echo CHtml::label('Start Date','start_date');?>
            <?php echo CHtml::textField('start_date',date('Y-m-d'),array('class'=>'date_input','id'=>'start_date'.$_GET['tabid']));?>
       </div>
       <div class="row">
           <?php echo CHtml::label('End Date','end_date');?>
           <?php echo CHtml::textField('end_date',date('Y-m-d'),array('class'=>'date_input','id'=>'end_date'.$_GET['tabid']));?>
       </div>
       <div class="row buttons">
            <?php echo CHtml::submitButton('Search',array('id'=>'submit_button')); ?>
	</div>
</div>
<div id="mixed-mild-cost-report-view">
<?php $this->renderPartial('_search_mix',
	['dataProvider'=>$dataProvider]
); ?>
</div>
</div>
<script>
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
         $('#submit_button',panel).on("click",function(){
         var data = {};
        data['start_date'] = $("#start_date<?=$_GET['tabid']?>",panel).val();
        data['end_date'] = $("#end_date<?=$_GET['tabid']?>",panel).val();
        data['partial']=11;
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("imParcel/mixedMildsReport",array('tabid'=>$_GET['tabid'])) ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#mixed-mild-cost-report-view').html(resp);
            },
           complete:function(jqXHR, status ){   
            }
        });   
//            
    });
   });
 
</script>
        
