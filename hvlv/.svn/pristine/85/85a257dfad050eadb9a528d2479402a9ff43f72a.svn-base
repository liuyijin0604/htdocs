<div class="container">
<div class="form">
<?php
$_GET['tabid']=112321231;
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'wms-task-form',
	'action' => $model->isNewRecord ? $this->createUrl('task/createTask', array('type' => $_GET['type'], 'id' => $_GET['id'],'isSpecial'=>$model->mdata['isSpecial'])) : $this->createUrl('task/updateTask', array('id' => $model->id)),
	'enableAjaxValidation'=>false,
  'htmlOptions' => array(
    'enctype' => 'multipart/form-data')
)); ?>
    <div class="row">
  <?php $oids = User::getOrgIds();
  if (sizeof($oids) > 1 && $model->isNewRecord) { ?>
    <?php if (Yii::app()->session['org_id'] == Yii::app()->user->org) { ?>
      <div class="col col-md-4 col-sm-8">
        <div class="form-group">
          <?php echo CHtml::label('Belong To', 'belong'); ?>
          <?php
          $orgs = [];
          foreach ($oids as $oid) {
            $org = Org::model()->findByPk($oid);
            $orgs[$oid] = $org->name;
          }
          echo Chtml::dropDownList('org_id', Yii::app()->user->org, $orgs, ['name' => 'org_id', 'class' => 'form-control', 'disabled' => 'disabled']);
          ?>
        </div>
      </div>
    <?php } else { ?>
      <div class="col col-md-4 col-sm-8">
        <div class="form-group">
          <?php echo CHtml::label('Belong To', 'belong'); ?>
          <?php
          $orgs = [];
          foreach ($oids as $oid) {
            $org = Org::model()->findByPk($oid);
            $orgs[$oid] = $org->name;
          }
          echo Chtml::dropDownList('org_id', Yii::app()->session['org_id'], $orgs, ['name' => 'org_id', 'class' => 'form-control', 'disabled' => 'disabled']);
          ?>
        </div>
      </div>
    <?php } ?>
  <?php } ?>
	<div class="col col-md-2 col-sm-4">
          <div class="form-group">
	<?php echo $form->labelEx($model,'ref'); ?>
        <?php echo $form->textField($model,'ref',['size' => 25,'class'=>'form-control']); ?>
	</div>
        </div>
        <div class="col col-md-2 col-sm-4">
          <div class="form-group">
						<?php echo $form->labelEx($model, 'dpt_id'); ?>
						<?php if ($model->isNewRecord) {
							echo $form->dropDownList($model, 'dpt_id', Org::dptList3PL(), ['empty' => 'Select One', 'disabled' => 'disabled', 'class' => 'form-control']);
						} else {
							echo $form->dropDownList($model, 'dpt_id', Org::dptList3PL(), ['empty' => 'Select One', 'disabled' => 'disabled', 'class' => 'form-control']);
						} ?>
					</div>
				</div>

        <div class="col col-md-2 col-sm-4">
          <div class="form-group">
	        <?php echo $form->labelEx($model,'status'); ?>
	        <?php
					if (Pcaw::ifOldVersion()) {
						if ($model->status >= 30 && $model->status != 40) {
							echo $form->dropDownList($model, 'status', array('10' => 'New', '20' => 'Confirmed', '30' => 'Processing', '99' => 'Completed', '100' => 'Cancelled'), array('empty' => $this->t('Select One'),'class'=>'form-control','disabled'=>($model->status >= 20)));
						} else if ($model->status <= 20 || $model->status == 40) {
							echo $form->dropDownList($model, 'status', array('10' => !empty($model->mdata['errs']) ? 'Pending' : 'New', '20' => 'Confirmed', '40' => 'Hold', '100' => 'Cancel'), array('empty' => $this->t('Select One'),'class'=>'form-control'));
						}
					} else {
						if ($model->status >= 30 && $model->status != 40) {
							echo $form->dropDownList($model, 'status', WmsTask::$states_client_bio, array('empty' => $this->t('Select One'),'class'=>'form-control','disabled'=>($model->status >= 20)));
						} else if ($model->status <= 20 || $model->status == 40) {
							echo $form->dropDownList($model, 'status', array('10' => 'Backorder', '20' => 'Order received', '40' => 'Hold', '100' => 'Cancel'), array('empty' => $this->t('Select One'),'class'=>'form-control'));
						}
					}
          ?>
          </div>
        </div>
      </div>
     <div class="row">
          <div class="col col-md-2 col-sm-4">
          <div class="form-group">
	   	<?php echo $form->labelEx($model,'schd_time'); ?>
                <?php echo $form->textField($model,'schd_time',['class' => 'datetime_input form-control', 'id' => $_GET['tabid'].'_dt_schd','value' => $model->isNewRecord ? date("Y-m-d H:i:s") : $model->schd_time]); ?>
          </div>
        </div>
        <div class="col col-md-2 col-sm-4">
          <div class="form-group">
     <?php echo $form->labelEx($model,'due_time', array('label' => 'Due Time / EDT')); ?>
           <?php echo $form->textField($model,'due_time',['class' => 'datetime_input form-control', 'id' => $_GET['tabid'].'_dt_due']); ?>
          </div>
        </div>
         <div class="col col-md-2 col-sm-4">
          <div class="form-group">
	 	<?php echo $form->labelEx($model,'start_time'); ?>
                <?php echo $form->textField($model,'start_time',['class' => 'form-control','disabled'=>'true']); ?>
          </div>
        </div>
         <div class="col col-md-2 col-sm-4">
          <div class="form-group">
	  <?php echo $form->labelEx($model,'compl_time'); ?>
          <?php echo $form->textField($model,'compl_time',['class' => 'form-control','disabled'=>'true']); ?>
          </div>
        </div>
      </div>
  <?php if ($model->type == 1030) { ?>
    <div class="row">
      <div class="col col-md-2 col-sm-4">
        <div class="form-group">
          <?php echo $form->labelEx($model, 'mdata[ctn_no]', array('label' => 'Container No.')); ?>
          <?php echo $form->textField($model, 'mdata[ctn_no]', ['class' => 'form-control']); ?>
        </div>
      </div>
      <div class="col col-md-2 col-sm-4">
        <div class="form-group">
          <?php echo $form->labelEx($model, 'mdata[ctn_size]', array('label' => 'Container Size')); ?>
          <?php echo $form->dropDownList($model, 'mdata[ctn_size]', array(
            '22G0' => '22G0 - 20\' general container',
            '42G0' => '42G0 - 40\' general container',
            '25G0' => '25G0 - 20\' general high cube container',
            '45G0' => '45G0 - 40\' general high cube container',
            '22R0' => '22R0 - 20\' integral reefer container',
            '25R1' => '25R1 - 20\' integral high cube reefer container',
            '42RO' => '42RO - 40\' integral reefer container',
            '45R1' => '45R1 - 40\' integral reefer container high cube',
            '22U0' => '22U0 - 20\' open top container',
            '42U1' => '42U1 - 40\' open top container',), array('empty' => $this->t('Select One'), 'class' => 'form-control')); ?> 
        </div>
      </div>
      <div class="col col-md-2 col-sm-4">
        <div class="form-group">
          <?php echo $form->labelEx($model, 'mdata[ctn_seal]', array('label' => 'Container Seal #')); ?>
          <?php echo $form->textField($model, 'mdata[ctn_seal]', ['class' => 'form-control']); ?>
        </div>
      </div>
    </div>
  <?php } ?>
    <?php
switch($model->type){
  case 1030: //Container Unload
	case 1010: //Pallets / Cartons / Units In
		echo $this->renderPartial('_form_extra_10', array('model'=>$model),true);
	break;
  case 3010:
  case 3020:
	case 3030: //Pickup / Delivery
		if($model->type == 2030) echo $this->renderPartial('_form_extra_2030', array('model'=>$model));
		echo $this->renderPartial('_form_extra_30', array('model'=>$model),true);
	break;

}
?>
<?php if($model->mdata['isSpecial']) {?>
  <div class="form-group">
  <label>Instruction</label>
  <?php echo CHtml::textArea('meta[note]', @$model->mdata['note'], ['cols' => 40, 'rows' => 3, 'class' => 'form-control']); ?>
  </div>
<?php }else{?>
  <?php echo CHtml::hiddenField('meta[note]'); }?>
  <?php echo CHtml::hiddenField('isSpecial'); ?>
 <div class="form-group">
		<?php echo CHtml::hiddenField('meta_items'); ?>
    <?php if ($model->status < 30) { ?>
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save'),array('class'=>'btn btn-primary ajax-link','id'=>'task-btn')); ?>
    <?php } ?>
    </div>
        

<?php $this->endWidget(); ?>

</div><!-- form -->
</div>
<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
  
	var t = $('#items', panel);
	
	for(var i = 0; i < 3; i++) t.trigger('addLine');

	$('#wms-task-form').on({
                'submit':function(e,r){
                      $('#task-btn').prop('disabled',true);
                },
		'success': function(e, r){
      if (window.location.href.indexOf('createTask')) {
                       var url = window.location.href.replace('task/createTask','task/updateTask/id/'+r.id);
                       pcawApp.toPage(url);
      }
    //                   $('#task-btn').prop('disabled',false);
    // $('tbody input', t).prop('disabled', false);
		},
		'error': function(e, r){
			$('#notifc').notify({message: {html: r.msg}, type: 'danger'}).show();
                      $('#task-btn').prop('disabled',false);
    $('tbody input', t).prop('disabled', false);
							// f.trigger('error', [r]);
		}
	}).on('beforeSerialize', function(){
		var ia = [];
		$('tbody tr', t).each(function(){
			var o = {};
			$('input', this).each(function(){
				o[$(this).attr('name')] = $(this).val();
			});
			ia.push(o);
		});
                // console.log(ia);
		$('#meta_items').val(JSON.stringify(ia));
		$('tbody input', t).prop('disabled', true);
              
		return true;
	});
	var item_data = <?=json_encode($model->getItemsArray());?>;
	var e = item_data.length - $('tbody tr', t).length;
	if(e > 0) for(var c = 0; c < e; c++) t.trigger('addLine');
	$('tbody tr', t).each(function(i){
		for(p in item_data[i]){
			$('input.in_'+p, this).val(item_data[i][p]);
		}
	});
	t.trigger('calcTot');
});
</script>