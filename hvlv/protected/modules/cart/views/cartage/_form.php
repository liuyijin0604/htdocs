<?php if(Yii::app()->user->grp<=40):?>
<div class="form" >
    <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'task-form',
	'enableAjaxValidation'=>false,
)); ?>
    <!--<pre id='err_message' style="border: none;background-color: white; color:red;"></pre>-->
    <?php
    $org = Org::model()->findByPk(106);
    if (empty($model->from_addr)) {
      $model->from_addr = $org->address.(!empty($org->suburb)?', ':'').$org->suburb.(!empty($org->state)?', ':'').$org->state.(!empty($org->postcode)?', ':'').$org->postcode;
    }
    if (empty($model->from_contact)) {
      $model->from_contact = $org->phone;
    }
    ?>
    <div class="row">
    <div class="col col-sm-6">
        <legend>From:</legend>
        <span> <label> Org Name<span class="required">*</span></label> <pre id='err_forg' style="border: none;background-color: transparent; color:red; display: inline"></pre>
                <?php echo  CHtml::hiddenField('fid',106,['id'=>'fid']);?>
              <?php
                $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                    'name' => 'from_org_name',
                    'value' => $org->name,
                    'source' =>$this->createUrl('cartage/suggest_agent'),
                    'options' => array(
                        'minLength' => '1',
                        'select'=>"js:function(e,u) {
                            $('#fid').val(u.item.id);
                            $('#fad').val(u.item.addr);
                            $('#fcontact').val(u.item.contact);
                
                  }",
                    ),
                    'htmlOptions' => array(
                         'class'=>'form-control',

                    ),
                ));
                ?></span>
  <div class="form-group">
    <label for="fad">Address<span class="required">*</span></label><pre id='err_fadd' class='err_msg' style="border: none;background-color: transparent; color:red; display: inline"></pre>
    <input type="text" class="form-control" id="fad" name='fad' placeholder="......." value="<?=$model->from_addr?>">
  </div>
  <div class="form-group">
    <label for="fcontact">Contact</label>
    <input type="text" class="form-control" id="fcontact" name='fcontact' placeholder="0430" value="<?=$model->from_contact?>">
  </div>
 
    </div>
    
     <div class="col col-sm-6">
         <legend>To:</legend>
              <span> 
                  <label> Org Name<span class="required">*</span></label><pre id='err_torg' class='err_msg' style="border: none;background-color: transparent; color:red; display: inline"></pre>
                  <?php echo  CHtml::hiddenField('tid',$model->to_id,['id'=>'tid']);?>
                  <?php
                $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                    'name' => 'tid_value',
                    'value' => @$model->to_org->name,
                    'source' =>$this->createUrl('cartage/suggest_agent'),
                    'options' => array(
                        'minLength' => '1',
                        'select'=>"js:function(e,u) {
                            $('#tid').val(u.item.id);
                            $('#tad').val(u.item.addr);
                            $('#tcontact').val(u.item.contact);
                
                  }",
                    ),
                    'htmlOptions' => array(
                         'class'=>'form-control',

                    ),
                ));
                ?></span>
     <div class="form-group">
         <label for="fad">Address<span class="required">*</span></label><pre id='err_taddr' class="err_msg" style="border: none;background-color: transparent; color:red; display: inline"></pre>
    <input type="text" class="form-control" id="tad" name="tad" placeholder="......." value="<?=$model->to_addr?>">
  </div>
  <div class="form-group">
    <label for="fcontact">Contact</label>
    <input type="text" class="form-control"  id="tcontact" name="tcontact" placeholder="0430" value="<?=$model->to_contact?>">
  </div>
         
    </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
    
       <legend>Goods Information</legend>
          <div class="row">
              
        <div class="col col-sm-5">  
            <div class="form-group">

                <label for="reference">Reference<span class="required">*</span></label><pre id='err_ref' class="err_msg" style="border: none;background-color: transparent; color:red; display: inline"></pre>
                <input type="text"  class="form-control"  id="reference" name="reference" value="<?=$model->ref?>">
            </div>
       
        <div class="form-group ">

            <label for="cbm">Dimension</label><pre id='err_cbm' class="err_msg" style="border: none;background-color: transparent; color:red; display: inline"></pre>
            <input type="text" class="form-control " id="cbm" name="cbm" placeholder="M3" value="<?=@$model->mdata['cbm']?>" />

        </div>
       </div>

    <div class="col col-sm-5">  

        <div class="form-group ">
		<?php echo $form->labelEx($model,'plt'); ?><pre id='err_plt' class="err_msg" style="border: none;background-color: transparent; color:red; display: inline"></pre>
		<?php echo $form->textField($model,'plt',array('class'=>'form-control')); ?>
		<?php echo $form->error($model,'plt'); ?>
        </div>
       
            <div class="form-group">

                <label for="weight">Weight</label><pre id='err_weight' class="err_msg" style="border: none;background-color: transparent; color:red; display: inline"></pre>
                <input type="text"  class="form-control"  id="weight" name="weight" placeholder=".kg" value="<?=@$model->mdata['weight']?>" />
            </div>
       

    </div>
          </div>
         <div class="row">
         <div class="col col-sm-5">  
         <div class="form-group ">
		<?php echo CHtml::label('Types','types'); ?><span class="required">*</span><pre id='err_type' class="err_msg" style="border: none;background-color: transparent; color:red; display: inline"></pre>
                <?php echo CHtml::hiddenField('types', 'W2T'); ?>
                <?php echo CHtml::dropDownList('type','W2T',array('C2W'=>'Customer to Warehouse','W2C'=>'Warehouse to Customer','W2T'=>'Warehouse to Terminal','T2W'=>'Terminal to Warehouse ','C2C'=>'Customer to Customer'),array('prompt'=>$this->t('All'),
                   'class'=>'form-control', 'disabled' => 'disabled'));
		 ?>
          </div>
         </div>
       
             <!-- <div class="col col-sm-5">  
                     <div class="form-group ">
                         <?php echo CHtml::label('Assign to Org', 'org'); ?><span class="required">*</span><pre id='err_aorg' class="err_msg" style="border: none;background-color: transparent; color:red; display: inline"></pre>
                         <span> 
                             <?php echo CHtml::hiddenField('org_id', '', ['id' => 'org_id']); ?>
                             <?php
                             $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                                 'name' => 'org_value',
                                 'source' => $this->createUrl('cartage/suggest_agent'),
                                 'options' => array(
                                     'minLength' => '1',
                                     'select' => "js:function(e,u) {
                            $('#org_id').val(u.item.id);
                               }",
                                 ),
                                 'htmlOptions' => array(
                                     'class' => 'form-control',
                                 ),
                             ));
                             ?></span>
                      </div>
                 </div> -->
             </div>
        </div>
        <div class="col-sm-6">
            <legend>Other Information</legend>
<!--         <div class="form-group ">
          <label for="rego">Rego</label>
            <input type="text" class="form-control" id="rego" name="rego">

        </div>-->
        <div class="row">
          <div class="col col-sm-5">
       <div class="form-group">
      <label for="awb">AWB</label>
      <input class="form-control"  id="awb" name='awb' value="<?=@$model->mdata['awb']?>" />
    </div> 
    </div> 
    <div class="col col-sm-5">
       <div class="form-group">
      <label for="flight">Flight No.</label>
      <input class="form-control"  id="flight" name='flight' value="<?=@$model->mdata['flight']?>" />
    </div>
  </div>
</div>
     <div class="form-group">
    <label for="exampleTextarea">Notes</label>
    <textarea class="form-control"  rows="6" id="notes" name='notes' rows="3"><?=@$model->mdata['notes']['op']?></textarea>
  </div>    
        </div>
    </div>
 
 
   <fieldset class="form-group">
    <legend>Schedule Date<span class="required">*</span><pre id='err_scd' class="err_msg" style="border: none;background-color: transparent; color:red; display: inline"></pre></legend>
    <div class="row">
        <div class="col-sm-3">
   <div class="input-group">
   <?php echo CHtml::textField('scd_date',$model->scd_time,array('size' => 25, 'class' => 'datetime_input form-control')); ?>
   
     <span class="input-group-addon" id="sizing-addon1"><span class="glyphicon glyphicon-time"></span></span>
       
  </div>
            
    </div>
    </div>
   </fieldset><!--
  -->
  <div class="form-group">
		<?php echo CHtml::submitButton('Submit',$htmlOptions=['class'=>'btn btn-primary']); ?>
	</div>
  <?php $this->endWidget(); ?>
</div>
<?php endif;?>

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
  var url=document.URL;
  $('form#task-form').on('submit',function(){
    $(".err_msg").text(''); 
  });
$('form#task-form').on('success', function(e, r){
            url = url.replace('create','update/id/' + r.id);
                if(!r.done){ 
                 var msg=r.msg.split('<br \/>');
                  for( m of msg){
                      if(!m) continue;
                      switch(m){
                          case 'Type cannot be blank.': $("#err_type").text('types not be blank');break;
                          case 'From Addr cannot be blank.': $("#err_fadd").text('Address can not be blank');break;
                          case 'From cannot be blank.':$("#err_forg").text('org must be valid'); break;
                          case 'To cannot be blank.':$("#err_torg").text('org must be valid');break;
                          case 'To Addr cannot be blank.':$("#err_taddr").text('Address not be blank');break;
                          case 'Plt cannot be blank.':$("#err_plt").text('Plt not be blank');break;
                          case 'Cbm cannot be blank.':$("#err_cbm").text('cb not be blank');break;
                          case 'Kg cannot be blank.':$("#err_weight").text('Weight not be blank');break;
                          case 'Scd Time cannot be blank.':$("#err_scd").text('Schedule time not be blank');break;
                          case 'Org cannot be blank.':$("#err_aorg").text('org must valid'); break;
                          case 'Reference cannot be blank.': $("#err_ref").text('need reference'); break;   
                      }
                    }
                  }
                         if(r.done) window.location.href=url;
        });
        
        
//        $('#datetimepicker1').datetimepicker();
       
    });


</script>
<?php $this->registerJS(ob_get_clean()); ?>