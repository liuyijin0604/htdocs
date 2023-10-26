<div class="pane">
       <h3>Amazon Search</h3>
       <br>
    <div class="form">
    <div class="row">
        <?php echo CHtml::label('Amazon Booking Ref','amazon_booking_ref');?>
        <?php echo CHtml::textField('amazon_booking_ref','',array('id'=>"amazon_booking_ref".$_GET['tabid']));?>      
    </div>
        <div class="row">
        <?php echo CHtml::label('Amazon Booking Time','amazon_booking_time');?>
        <?php echo CHtml::textField('amazon_booking_time','',array('id'=>"amazon_booking_time".$_GET['tabid'],'class'=>'date_input'));?>      
    </div>
    <div class="button">
        <?php echo CHtml::submitButton('Search',array('id'=>'submit_button'));?>
    </div>
    </div>
       </div>
     <div id="shipment_amazon_booking_view">
      <?php $this->render('amazon_part_search',array('dataProvider'=>$dataProvider))?>
     </div>
<script>
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
         $('#submit_button',panel).on("click",function(){
         var data = {};
        data['amazon_booking_ref'] = $("#amazon_booking_ref<?=$_GET['tabid']?>",panel).val();
        data['amazon_booking_time'] = $("#amazon_booking_time<?=$_GET['tabid']?>",panel).val();
        data['partial']=11;
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("dmawbConsol/amazonSearch",array('tabid'=>$_GET['tabid'])) ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#shipment_amazon_booking_view').html(resp);
            },
        });   
//            
    });
   });
 
</script>

