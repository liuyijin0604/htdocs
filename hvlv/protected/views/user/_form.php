<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
));
$notme =  $model->id != Yii::app()->user->id;
?>
<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

<?php 
	if(isset($type)&&$type=="driverUser"&&!Acl::hasAccess('C:user/F:SelectUserType'))
	{
		echo $form->hiddenField($model,'type');
	}0
?>

<?php if(Acl::hasAccess('C:user/F:SelectUserType') ): ?>
	<div class="row">
		<div class="row rowcol rowleft">
			<?php echo $form->labelEx($model,'type'); ?>
			<?php echo $form->dropDownList($model, 'type', Yii::app()->name == 'PEP'? $this->t([35 => 'PEP Export', 80 => 'Export Agent Manager', 82 => 'Export Agent Operator', 100 => 'Driver', 120 => 'Data Entry',]) : $this->t(User::$types),array('empty' => $this->t('Select One'))); ?>
		</div>
		<div class="row rowcol">
			<?php echo $form->labelEx($model, 'dpt_id'); ?>
			<?php echo $form->dropDownList($model, 'dpt_id', Org::dptList(), ['empty' => 'Select One']); ?>
		</div>
		<div class="row rowcol">
			<?php echo $form->labelEx($model, 'group'); ?>
			<?php echo CHtml::dropDownList('User[group]',@$model->userWarehouseGroup->group, UserWarehouseGroup::$groupList, ['empty' => 'Select One']); ?>
		</div>
	</div>

	<div class="row">
	</div>

	<div class="row">
		<div class="row">
			<?php echo $form->labelEx($model,'occupation'); ?>
			<?php
				foreach (User::$occupationList as $key2 => $occupations) {
					echo '<div class="row rowcol">';
					$occupation = [];
					foreach ($occupations as $key => $value) {
						if($key>0 && ($key&$model->occupation)>0)
						{
							$occupation[] = $key;
						}
					}
					echo CHtml::checkBoxList('occupation',$occupation,Yii::app()->name == 'PEP'? $this->t([]) : $this->t($occupations),array(
											'template'=>'{input}{label}</br>',
											'separator'=>'',
											'labelOptions'=>array(
													'style'=> 'padding-right:12px;min-width: 60px;float: left;'),
											'style'=>'float:left;',) );
					echo '</div>';
				}
				

			?>
		</div>
	</div>
	<div class="row">
	</div>

        <div class="row">
            <?php 
             $selected=[];
             foreach (User::$dpmts as $k=>$d){
                 if(($model->dpmt&$k)>0){
                     $selected[]=$k;
                 }
             }
             echo CHtml::label('Select Department','fordept'); 
             echo CHtml::checkBoxList('theDpmts',$selected,$this->t(User::$dpmts),array( 'template'=>'{input}{label}',
                    'separator'=>'','labelOptions'=>array('style'=> 'padding-right:12px;min-width: 60px;float: left;'), 'style'=>'float:left;'));
            ?>
    
        </div>

<?php endif;?>

<?php if(Acl::hasAccess('C:user/F:SetUserOrg') && $notme): ?>
	<div class="row">
		<?php echo $form->labelEx($model,'org_id'); ?>
		<?php 
			echo $form->hiddenField($model,'org_id');
			$acname = empty($_GET["tabid"])? 'user_org_ac' : $_GET["tabid"].'_org_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('org/userSuggest'),
				'value' => ($model->org) ? $model->org->name : '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val(""); return false; }',
				),
				'htmlOptions' => array(
					'class' => 'required',
					'size' => '50',
				),
		));
		?>

	</div>
<?php endif; ?>
	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->dropDownList($model, 'title', $this->t(User::$titles), array('class'=>'user')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'fname'); ?>
		<?php echo $form->textField($model,'fname',array('size'=>40,'maxlength'=>40,'class'=>'user')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'lname'); ?>
		<?php echo $form->textField($model,'lname',array('size'=>40,'maxlength'=>40,'class'=>'user')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'user'); ?>
		<?php echo $model->isNewRecord? 'AUTO ASSIGN' : $model->user;?>
		<?php echo $form->hiddenField($model,'user',array('class'=>'subuser')); ?>
	</div>
	

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>50,'maxlength'=>255,'class'=>'email user')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('size'=>32,'maxlength'=>32,'minlength'=>6,'value'=>'', 'id'=>'User_password_'.$_GET["tabid"])); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'pwd_conf'); ?>
		<?php echo CHtml::passwordField('pwd_conf','',array('size'=>32,'maxlength'=>32,'minlength'=>6)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone',array('size'=>40,'maxlength'=>255,'class'=>'user')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'fax'); ?>
		<?php echo $form->textField($model,'fax',array('size'=>40,'maxlength'=>255,'class'=>'user')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'mobile'); ?>
		<?php echo $form->textField($model,'mobile',array('size'=>40,'maxlength'=>255,'class'=>'user')); ?>
	</div>

<?php if(Acl::hasAccess('C:user/update') && $notme): ?>
	<div class="row">
		<?php echo $form->labelEx($model,'active'); ?>
		<?php echo $form->radioButtonList($model,'active', $this->t(array(1=>'Yes', 0=>'No')), array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp')); ?>
	</div>
       

	<div class="row">
		<?php echo CHtml::label('Require Password Change at Next Login','rpc');?>
		<?php echo CHtml::checkbox('extra[rpc]', !empty($model->extra['rpc'])); ?>
	</div>
<?php endif; ?>
<?php if($model->type == 100): ?>
	<div class="row rowcol">
		<?php echo CHtml::label('Zone','pickup_zone');?>
		<?php echo CHtml::dropDownList('extra[pickup_zone]', @$model->extra['pickup_zone'], oList::kvp('ex_zone'), array('empty' => $this->t('Select One'))); ?>
	</div>
<?php endif; ?>
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
		<input type="button" name="reset_google_code" id="reset_google_code" value="reset google code" style="float:right;margin-right:30px;">
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('form#user-form', panel).validate({
		rules: {pwd_conf: {
				minlength: 6,
				equalTo: "#User_password_<?=$_GET["tabid"];?>"
			}
		}
	});
	$('#User_type', panel).change(function(){
		var org = $('#User_org_id', panel);
		if(!org.data('ov')) org.data('ov', org.val() || 1);
		if($(this).val() >= 40){
			org.parent().show();
			org.val(org.data('ov')).next().addClass('required');
		}else{
			org.val(1).next().removeClass('required');
		}
		if($(this).val() == 70){
			$('.user', panel).each(function(){
				if($(this).hasClass('required')){
					$(this).data('was-required', 1);
					$(this).removeClass('required');
				}
			}).parent().hide();
			$('.subuser', panel).parent().show();
		}else{
			$('.user', panel).each(function(){
				if($(this).data('was-required') == 1){
					$(this).addClass('required');
				}
			}).parent().show();
			$('.subuser', panel).parent().hide();
		}
	}).trigger('change');

	$("#reset_google_code").on("click", function(e){
	    e.preventDefault();
	    <?php $url2=$this->createUrl('user/resetGoogleAuthernticator', ['id' => $model->id]);?>
	    $.ajax({
		            url: '<?=$url2?>',
		            type: "post",
		            success: function(r) {
		            	var response = JSON.parse(r);
		            	console.log(response);
		                if(response.done)
		                 {
		                 	myApp.notice('Done', 5000);
						}else
						 {
							myApp.alert(response.msg, false);
			    	   }
							             
					},
		            error: function(e) {
		                console.log(e);
		            }
		        });
		return false;
	});
});
</script>