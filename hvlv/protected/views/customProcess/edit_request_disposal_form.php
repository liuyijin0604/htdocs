<style type="text/css">
  #edit_request_disposal_form table {
    font-family: Calibri;
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1em;
  }

  #edit_request_disposal_form th {
    font-size: 14px;
    color: #FFF;
    background-color: rgb(88, 88, 88);
  }

  #edit_request_disposal_form th,
  td {
    border: 1px solid black;
    padding: 0px;
    font-size: 1.2em;
  }

  #edit_request_disposal_form .left-column {
    width: 40%;
  }

  #edit_request_disposal_form .right-column {
    width: 60%;
  }
  #edit_request_disposal_form td input
  {
    width: 100%;
  }
</style>



  <img width="40%" src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl.'/images/inspection_header.png'?>" ></img>
  <table style="width:100%;background-color:rgb(88,88,88);color:#FFF;">
  <tr>
    <th>Request for permission to</th>
  </tr>
  <tr>
    <th>dispose of goods / conveyance</th>
  </tr>
</table>

  <div class="form">
<?php 
  $id = $model->id;
  $url = $this->createUrl('cargoProcess/update');
  if(isset($ids) && $ids != "")
  {
    $id = $ids;
  }
  $form=$this->beginWidget('CActiveForm', array(
  'id'=>'edit_request_disposal_form',
  'enableAjaxValidation'=>false,
  'action'=> $url."?id=".$id)
  );
?>
<table>
  <tr>
    <th>Description of goods and/or conveyance<input name="type" type="hidden" value ="<?=$type?>"></th>
    <th>Reference no. / Name</th>
  </tr>
  <tr>
    <td><input type="text" name="mdata[disf][description]" value="<?=empty($model->process->mdata['disf']['description']) ? '' : $model->process->mdata['disf']['description']?>"></td>
    <td><input type="text" name="mdata[disf][reference]" value="<?=empty($model->process->mdata['disf']['reference']) ? '' : $model->process->mdata['disf']['reference']?>"></td>
  </tr>
  <tr>
    <td colspan="2"></td>
  </tr>
</table>
<table>
  <tr>
    <th colspan="3">Intended method of disposal of the goods and/or conveyance</th>
  </tr>
  <tr>
    <td colspan="3">(detail how, when and where the goods and/or conveyance is to be disposed of)</td>
  </tr>
  <tr>
    <td  width="30%"><input type="text" name="mdata[disf][disposal_method_how]" value="<?=empty($model->process->mdata['disf']['disposal_method_how']) ? '' : $model->process->mdata['disf']['disposal_method_how']?>"  style="width:100%"></td>
    <td width="15%">disposal at</td>
    <td  width="55%">Where: <input type="text" name="mdata[disf][disposal_method_where]" value="<?=empty($model->process->mdata['disf']['disposal_method_where']) ? $setting['disLocation'][0] : $model->process->mdata['disf']['disposal_method_where']?>" style="width:100%"></td>
  </tr>
  <tr>
    <td colspan="3">Address: <input type="text" name="mdata[disf][disposal_method_address]" value="<?=empty($model->process->mdata['disf']['disposal_method_address']) ? $setting['disLocation'][1] : $model->process->mdata['disf']['disposal_method_address']?>"  style="width:100%"></td>
  </tr>
</table>
<table>
  <tr>
    <th colspan="3">Contact details of person requesting permission</th>
  </tr>
  <tr>
      <td>Street number and name: <input type="text" name="mdata[disf][street_number]" value="<?=empty($model->process->mdata['disf']['street_number']) ? 'Top Logistics Australia, 1/233 Milperra Rd' : $model->process->mdata['disf']['street_number']?>"
      value="Top Logistics Australia, 1/233 Milperra Rd"></td>
    <td colspan="2">Suburb/Town:<input type="text" name="mdata[disf][suburb]" value="<?=empty($model->process->mdata['disf']['suburb']) ? 'BANKSTOWN AERODROME' : $model->process->mdata['disf']['suburb']?>"></td>
  </tr>
</table>
<table style="margin-top:-1em;">
  <tr>
    <td style="border-top: 0px solid black;">State:<input type="text" name="mdata[disf][state]" value="<?=empty($model->process->mdata['disf']['state']) ? 'NSW' : $model->process->mdata['disf']['state']?>"></td>
    <td style="border-top: 0px solid black;">Postcode:<input type="text" name="mdata[disf][postcode]" value="<?=empty($model->process->mdata['disf']['postcode']) ? '2220' : $model->process->mdata['disf']['postcode']?>"></td>
    <td style="border-top: 0px solid black;">mobile:<input type="text" name="mdata[disf][mobile]" value="<?=empty($model->process->mdata['disf']['mobile']) ? '' : $model->process->mdata['disf']['mobile']?>"></td>
  </tr>
  <tr>
    <td>Landline: <input type="text" name="mdata[disf][landline]" value="<?=empty($model->process->mdata['disf']['landline']) ? '' : $model->process->mdata['disf']['landline']?>"></td>
    <td colspan="2">Email:<input type="text" name="mdata[disf][email]" value="<?=empty($model->process->mdata['disf']['email']) ? 'michelle@toplogistics.com.au' : $model->process->mdata['disf']['email']?>"></td>
  </tr>
</table>
<table>
  <tr>
    <th colspan="2">Person requesting permission</th>
  </tr>
  <tr>
    <td colspan="2">Signature: <input type="text" name="mdata[disf][signature]"  value="<?=empty($model->process->mdata['disf']['signature']) ? "Michelle Wang" : $model->process->mdata['disf']['signature']?>"></td>
  </tr>
  <tr>
    <td>Printed Name:<input type="text" name="mdata[disf][printed_name]"  value="<?=empty($model->process->mdata['disf']['printed_name']) ? "Michelle Wang" : $model->process->mdata['disf']['printed_name']?>"></td>
    <td><input type="text" name="mdata[disf][date]"  value="<?=empty($model->process->mdata['disf']['date']) ? date("d/m/Y") : $model->process->mdata['disf']['date']?>"></td>
  </tr>
</table>
</div>
<?php echo CHtml::button('Generate', array('class' => 'generate'));?>

<?php $this->endWidget();?>

<script type="text/javascript">
  $(function(){
      var win = $('#jqmw_<?=$_GET["tabid"];?>');
      var tab = $('#<?=$_GET["tabid"];?>');
      var panel = $('#<?=$_GET["tabid"];?>').data('panel');

      $('.generate',win).on('click',function(){
        if( confirm('Are you sure to generate disposal form?')){
          $(this).val("Generating.........");
          $(this).attr("disabled",true);
          var form = new FormData(document.getElementById("edit_request_disposal_form"));
           $.ajax({
                      url: '<?=$this->createUrl("customProcess/generateDisposalRequestForm",['id'=>$model->id])?>',
                      type: "post",
                      data: form,
                      processData: false,
                      contentType: false,
                      success: function(r) {
                        r=JSON.parse(r);
                        if(r.done)
                        {
                         myApp.notice('Done', 5000);
                        }else
                        {
                        myApp.alert(r, false);   
                        }
                        $('#<?=$_GET["tabid"];?>_excofile-grid').yiiGridView('update');
                        win.jqmHide();
                     },
                      error: function(e) {
                          console.log(e);
                      }
                  });     
        }
        return false;
      });




  });
</script>