<h2>Export Held Shipments</h2>
<div class="form">
    <style>
     div.consol_input_held_shipments{
            display: none;
        }
    </style>
<?php 
$form=$this->beginWidget('CActiveForm',array(
    'id'=>'export_held_shipments'.$_GET['tabid'],
    'enableAjaxValidation'=>false,
    'htmlOptions'=>['target'=>'held_shipments_export_result','class'=>'ifrm-form','enctype'=>"multipart/form-data"],
     
));?>
<div class="row rowcol rowleft">
     <?php echo CHtml::label('Type','export_type'); ?>
     <?php echo CHtml::dropDownList('export_type','',array('1'=>'Today Held',2=>'Consol Held',3=>'All Held',4=>'Yesterday Held')); ?>
</div>
<div class="row rowcol">
     <?php echo CHtml::label('POD','export_pod'); ?>
     <?php echo CHtml::dropDownList('export_pod','',array('' => 'All', 'AUSYD'=>'Sydney', 'AUMEL'=>'Melbourne', 'AUBNE'=>'Brisbane'));?>
</div>
<div class="row rowcol">
     <?php echo CHtml::label('Format Type','export_format_type'); ?>
     <?php echo CHtml::dropDownList('export_format_type','',array('1'=>'Excel',2=>'pdf for label printer',3=>'pdf',4=>'Scan Check')); ?>
</div>
<div class="row rowright">
    <?php  echo "<a href=\"".Yii::app()->createUrl("imcoConsol/historyScanCheckList")."\" class=\"tab_link\" title=\"HistoryScanCheckList\"><h2>History Scan Check List</h2></a>";?>
</div>

<div class="row ">
    <div class="consol_input_held_shipments">
        <?php echo CHtml::label('Consol No.<span class="required">*</span>','held_shipments_consol_no'); ?>
        <?php echo CHtml::textField('held_shipments_consol_no','',array('size'=>100)); ?>
    </div>
</div>
<div class="button">
    <?php echo CHtml::submitButton('Export',["id"=>"export"])?>
</div>
<iframe name="held_shipments_export_result" id="held_shipments_export_result<?=$_GET['tabid']?>" style="border: 1px solid black; width:80%;height:700px;margin-top: 20px;"></iframe>

<div name="held_shipments_scan_check" id="held_shipments_scan_check<?=$_GET['tabid']?>" style="border: 1px solid black; width:80%;height:700px;margin-top: 20px;display: none;">
  <?php $this->renderPartial('export_held_shipments_scan_check',['dataProvider'=>$dataProvider,'filter'=>$filter])?>

</div>

<?php $this->endWidget();?>
</div>
<script>
    $(function(){
        var tab=$('#<?=$_GET['tabid']?>');
        var panel=tab.data('panel');
        $('#export_type',panel).on('change',function(){
           if($(this).val()==2){
               $('.consol_input_held_shipments').show();
           }else{
               $('.consol_input_held_shipments').hide();
           }
        });

        $('#export_format_type',panel).on('change',function(){
           if($(this).val()==4){
               $('#held_shipments_scan_check<?=$_GET['tabid']?>').show();
               $('#held_shipments_export_result<?=$_GET['tabid']?>').hide();
           }else{
                $('#held_shipments_export_result<?=$_GET['tabid']?>').show();
                $('#held_shipments_scan_check<?=$_GET['tabid']?>').hide();
           }
        });
        $('form#export_held_shipments<?=$_GET['tabid']?>',panel).on('submit',function(){
           $('#held_shipments_export_result<?=$_GET['tabid']?>',panel).contents().find('body').html('');
           if($('#export_type',panel).val()==2){
               if(!$('#held_shipments_consol_no',panel).val()){
                   alert('Please input the consol No.');
                   return false;
                }
           }
        });


       $('#export',panel).on('click',function(){
          var form = new FormData(document.getElementById('export_held_shipments<?=$_GET['tabid']?>'));
           $.ajax({
                      url: '<?=$this->createUrl("imcoConsol/exportHeldShipments")?>'+'?&tabid='+'<?=$_GET['tabid']?>',
                      type: "post",
                      data: form,
                      processData: false,
                      contentType: false,
                      success: function(r) {
                         if($('#export_format_type',panel).val()==4){
                            $('#held_shipments_scan_check<?=$_GET['tabid']?>',panel).html(r);
                            return false;
                         }
                      },
                      error: function(e) {
                          console.log(e);
                      }
                  });
        if($('#export_format_type',panel).val()==4){
                            return false;
        }     
        return true;
      });
 });
</script>