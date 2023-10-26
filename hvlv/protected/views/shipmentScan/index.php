<div class="pane">
<h1>Shipment Scans</h1>
<div class="form" id="date-form">
    <div>
        <input type="hidden" name='tabid' value="<?=$_GET['tabid']?>"/>
    </div>
       <div class="row">
           <?php echo CHtml::label('start_date','start_date');?>
            <?php echo CHtml::textField('start_date',date('Y-m-d'),array('class'=>'date_input','id'=>'start_date'.$_GET['tabid']));?>
       </div>
       <div class="row">
           <?php echo CHtml::label('end_date','end_date');?>
           <?php echo CHtml::textField('end_date',date('Y-m-d', strtotime('+1 day')),array('class'=>'date_input','id'=>'end_date'.$_GET['tabid']));?>
       </div>
       <div class="row">
           <div class="row rowcol buttons">
                <?php echo CHtml::submitButton('Search',array('id'=>'submit_button')); ?> 
    	     </div>
           <div class="row rowcol buttons">
                <?php echo CHtml::submitButton('Export',array('id'=>'export_button')); ?> 
           </div>
           <style>
              .uploading { position: relative; clear:both; width: 150px; font-size: 1.4em; font-weight: bold; color: #BC3426; line-height: 32px; z-index: 99; padding: 15px 5px; margin-bottom: -50px; display: none; }
            </style>
            <div class="uploading"><img src="https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif" width="24" /> Loading</div>
            </div>
    </div>

    </div>
    <br>
    <div id="report-view">
    
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
        $('.uploading', panel).fadeIn();
        $.ajax({
                type : 'GET',
                url : '<?php echo Yii::app()->createAbsoluteUrl("shipmentScan/index",array('tabid'=>$_GET['tabid'])) ;?>',
                data: data,
                dataType: 'html',
                success:function(resp){
                    $('#report-view').html(resp);
                    $('.uploading', panel).fadeOut();
                },
                complete:function(jqXHR, status ){
                
                }
            });            
        });

        $('#export_button',panel).on("click",function(){
        var data = {};
        data['start_date'] = $("#start_date<?=$_GET['tabid']?>",panel).val();
        data['end_date'] = $("#end_date<?=$_GET['tabid']?>",panel).val();
        $('.uploading', panel).fadeIn();
        $.ajax({
                type : 'GET',
                url : '<?php echo Yii::app()->createAbsoluteUrl("shipmentScan/export",array('tabid'=>$_GET['tabid'])) ;?>',
                data: data,
                dataType: 'html',
                success:function(resp){
                   window.open(resp,'_blank');
                   $('.uploading', panel).fadeOut();
                },
                complete:function(jqXHR, status ){
                     
                }
            });            
        });
       
   }); 
</script>
        
