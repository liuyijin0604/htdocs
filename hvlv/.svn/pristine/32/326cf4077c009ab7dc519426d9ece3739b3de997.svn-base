
  <img width="40%" src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl.'/images/inspection_header.png'?>" ></img>
  <img width="570"  height="27"  src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl.'/images/request-for-inspection-form.png'?>" ></img>
  <div class="form">
<?php 
  $id = $model->id;
  $url = $this->createUrl('cargoProcess/update');
  if(isset($ids) && $ids != "")
  {
    $id = $ids;
  }
  $form=$this->beginWidget('CActiveForm', array(
  'id'=>'edit_request_inspection_form',
  'enableAjaxValidation'=>false,
  'action'=> $url."?id=".$id)
  );
?>
<table>
  <tr>
    <th>Consignment details</th>
    <th><input name="type" type="hidden" value ="<?=$type?>"></th>
  </tr>
  <tr>
    <td>Quarantine entry number/s:</td>
    <td><input name="mdata[insf][quarantine_entry_number]" type="text" value ="<?=@$model->process->mdata['insf']['quarantine_entry_number']?>"></td>
  </tr>
  <tr>
    <td>Airway bill (if nil quarantine entry number):</td>
    <td><input name="mdata[insf][airway_bill]" type="text" value ="<?=@$model->process->mdata['insf']['airway_bill']?>"></td>
  </tr>
  <tr>
    <th>Booking agent</th>
    <th></th>
  </tr>
  <tr>
    <td>Contact name:</td>
    <td><input type="text" name="mdata[insf][contact_name_1]" value="<?=empty($model->process->mdata['insf']['contact_name_1'])?$setting["agent"][0]:$model->process->mdata['insf']['contact_name_1']?>"></td>
  </tr>
  <tr>
    <td>Phone number:</td>
    <td><input type="text" name="mdata[insf][phone_number_1]" value="<?=empty($model->process->mdata['insf']['phone_number_1'])?$setting["agent"][1]:$model->process->mdata['insf']['phone_number_1']?>"></td>
  </tr>
  <tr>
    <td>Email:</td>
    <td><input type="text" name="mdata[insf][email]"  value="<?=empty($model->process->mdata['insf']['email'])?$setting["agent"][2]:$model->process->mdata['insf']['email']?>"></td>
  </tr>
  <tr>
    <th>Location</th>
    <th></th>
  </tr>
  <tr>
    <td>Change of location:</td>
    <td>☐Change *charges may apply</td>
  </tr>
  <tr>
    <td>Approved Arrangement (Name & AA number):</td>
    <td><input  name="mdata[insf][approved_arrangement]" type="text" value="<?=empty($model->process->mdata['insf']['approved_arrangement'])?$setting["location"][0]:$model->process->mdata['insf']['approved_arrangement']?>"></td>
  </tr>
  <tr>
    <td>Address of premise:</td>
    <td><input  name="mdata[insf][address_premise]" type="text" value="<?=empty($model->process->mdata['insf']['address_premise'])?$setting["location"][1]:$model->process->mdata['insf']['address_premise']?>"></td>
  </tr>
  <tr>
    <td>Opening hours:</td>
    <td><input  name="mdata[insf][opening_hours]" type="text" value="<?=empty($model->process->mdata['insf']['opening_hours'])?$setting["location"][2]:$model->process->mdata['insf']['opening_hours']?>"></td>
  </tr>
  <tr>
    <td>Contact name:</td>
    <td><input  name="mdata[insf][contact_name_2]" type="text" value="<?=empty($model->process->mdata['insf']['contact_name_2'])?$setting["location"][3]:$model->process->mdata['insf']['contact_name_2']?>"></td>
  </tr>
  <tr>
    <td>Phone number:</td>
    <td><input  name="mdata[insf][phone_number_2]" type="text" value="<?=empty($model->process->mdata['insf']['phone_number_2'])?$setting["location"][4]:$model->process->mdata['insf']['phone_number_2']?>"></td>
  </tr>
  <tr>
    <td>Inspection Type:</td>
    <td><input  name="mdata[insf][inspection_type]" type="text" value="<?=empty($model->process->mdata['insf']['inspection_type'])?"":$model->process->mdata['insf']['inspection_type']?>"></td>
  </tr>
  <!-- <tr>
    <th>Additional information</th>
    <th></th>
  </tr>
  <tr>
    <td>Consignment Type (if applicable):</td>
    <td>☐Flat Rack ☐Open top container ☐Isotank</td>
  </tr>
  <tr>
    <td>Hazardous goods?</td>
    <td>☐Yes ☒No</td>
  </tr>
  <tr>
    <th>Inspection type</th>
    <th></th>
  </tr>
  <tr>
    <td>Type of Inspection (Unpack, IFIP, Dual, CCV, Produce, etc):</td>
    <td><input type="text"></td>
  </tr>
  <tr>
    <td>Number of officers:</td>
    <td><input type="text"></td>
  </tr>
  <tr>
    <td>Inspection duration requested:</td>
    <td><input type="text" value="1 15 Minutes"></td>
  </tr>
  <tr>
    <th>Booking request</th>
    <th></th>
  </tr>
  <tr>
    <td>READY NOW:</td>
    <td>☒Yes ☐No</td>
  </tr>
  <tr>
    <td>Date goods are available from:</td>
    <td><input type="text" placeholder="Click here to enter a date"></td>
  </tr>
  <tr>
    <td>Requested date for inspection:</td>
    <td><input type="text" placeholder="Click here to enter a date"></td>
  </tr>
  <tr>
    <td>Requested time for inspection:</td>
    <td><p>☒Anytime ☐AM ☐PM</p>
      <p>We will endeavour to allocate inspections as requested, however, where this is not possible, you will be allocated the next available appointment.</p></td>
  </tr>
  <tr>
    <th colspan="2">Comments</th>
  </tr>
  <tr>
    <td colspan="2">Eg: overtime request, quantity of goods, etc...</td>
  </tr> -->
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
        if( confirm('Are you sure to generate inspection form?')){
          $(this).val("Generating.........");
          $(this).attr("disabled",true);
          var form = new FormData(document.getElementById("edit_request_inspection_form"));
           $.ajax({
                      url: '<?=$this->createUrl("customProcess/generateInspectionRequestForm",['id'=>$model->id])?>',
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