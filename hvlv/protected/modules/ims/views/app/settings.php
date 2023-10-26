<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'links' => array(
		'Settings',
	),
));
?>
<h2>Account Details</h2>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('data-bit' => 1),
));
?>

	<div class="row">
	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->dropDownList($model, 'title', $this->t(User::$titles), array('class'=>'form-control')); ?>
	</div>
	</div>

	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($model,'fname'); ?>
		<?php echo $form->textField($model,'fname',array('size'=>40,'maxlength'=>40,'class'=>'form-control')); ?>
	</div>
	</div>

	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($model,'lname'); ?>
		<?php echo $form->textField($model,'lname',array('size'=>40,'maxlength'=>40,'class'=>'form-control')); ?>
	</div>
	</div>
	</div>

	<div class="form-group">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>50,'maxlength'=>255,'class'=>'email form-control')); ?>
	</div>

	<div class="row">
	<div class="col col-sm-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('size'=>32,'maxlength'=>32,'minlength'=>6,'value'=>'','class'=>'email form-control')); ?>
	</div>
	</div>
	
	<div class="col col-sm-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'pwd_conf'); ?>
		<?php echo CHtml::passwordField('pwd_conf','',array('size'=>32,'maxlength'=>32,'minlength'=>6,'class'=>'email form-control')); ?>
	</div>
	</div>
	</div>

	<div class="row">
	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone',array('size'=>40,'maxlength'=>255,'class'=>'form-control')); ?>
	</div>
	</div>
	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($model,'fax'); ?>
		<?php echo $form->textField($model,'fax',array('size'=>40,'maxlength'=>255,'class'=>'form-control')); ?>
	</div>
	</div>
	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($model,'mobile'); ?>
		<?php echo $form->textField($model,'mobile',array('size'=>40,'maxlength'=>255,'class'=>'form-control')); ?>
	</div>
	</div>
	</div>
	<div class="form-group buttons">
		<button class="btn btn-primary btn-lg" id="user_btn" type="submit"><?=$this->t('Save');?></button>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php
if ( !empty($model->org->contacts) ) {
    $orgContact = $model->org->contacts[0];
} else {
    $orgContact = new OrgContact();
    $orgContact->status = 1;
}
?>
    <div class="form">
        <h2>Shipper Address</h2>
        <?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'shipper-form',
            'enableAjaxValidation'=>false,
            'htmlOptions' => array('data-bit' => 1),
        ));
        ?>

        <div class="row">
            <div class="col col-sm-6 col-xs-12">
                <div class="form-group">
                    <?php echo $form->labelEx($orgContact, 'name'),
                    CHtml::textField('OrgContact[name]', $orgContact->name, array('size'=>15, 'class' => 'form-control')); ?>
                </div>
            </div>
            <div class="col col-sm-6 col-xs-12">
                <div class="form-group">
                    <?php echo $form->labelEx($orgContact, 'phone'),
                    CHtml::textField('OrgContact[phone]', $orgContact->phone, array('size'=>15, 'class' => 'form-control')); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col col-sm-4 col-sx-6">
                <div class="form-group">
                    <?php echo $form->labelEx($orgContact, 'state');
                    $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                        'name' => 'cnor_state_ac',
                        'source' => AppHelper::cnProvince(),
                        'value' => empty($orgContact->state)? '' : $orgContact->state,
                        'options' => array(
                            'showAnim' => 'fold',
                            'autoFocus' => true,
                            'minLength' => 0,
                            'delay' => 0,
                            'select' => 'js:function(evt, ui){ $(this).trigger("ac_after_select", ui); return false;}',
                            'response' => 'js:function(evt, ui){ if(ui.content.length == 0){ $(this).data("sid", 0); }; return false; }',
                            'change' => 'js:function(evt, ui){ if($(this).data("sid") == 0) $(this).val(""); return false; }',
                        ),
                        'htmlOptions' => array(
                            'size' => '15',
                            'name' => 'OrgContact[state]',
                            'class' => 'form-control',
                        ),
                    ));
                    ?>
                </div>
            </div>
            <div class="col col-sm-4 col-sx-6">
                <div class="form-group">
                    <?php echo $form->labelEx($orgContact, 'city');
                    $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                        'name' => 'cnor_city_ac',
                        'sourceUrl' => array('shipment/cnCitySuggest'),
                        'value' => empty($orgContact->city)? '' : $orgContact->city,
                        'options' => array(
                            'showAnim' => 'fold',
                            'minLength' => 0,
                            'delay' => 100,
                            'autoFocus' => true,
                            'select' => 'js:function(evt, ui){ $(this).trigger("ac_after_select", ui); return false;}',
                            'response' => 'js:function(evt, ui){ if(ui.content.length == 0){ $(this).data("cid", 0); }; return false; }',
                            'change' => 'js:function(evt, ui){ if($(this).data("cid") == 0) $(this).val(""); return false; }',
                        ),
                        'htmlOptions' => array(
                            'size' => '15',
                            'name' => 'OrgContact[city]',
                            'class' => 'form-control',
                        ),
                    ));
                    ?>
                </div>
            </div>

            <div class="col col-sm-4 col-sx-6">
                <div class="form-group">
                    <?php echo $form->labelEx($orgContact, 'suburb');
                    $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                        'name' => 'cnor_suburb_ac',
                        'sourceUrl' => array('shipment/cnSuburbSuggest'),
                        'value' => empty($orgContact->suburb)? '' : $orgContact->suburb,
                        'options' => array(
                            'showAnim' => 'fold',
                            'minLength' => 0,
                            'delay' => 100,
                            'select' => 'js:function(evt, ui){ $(this).trigger("ac_after_select", ui); return false; }',
                        ),
                        'htmlOptions' => array(
                            'size' => '15',
                            'name' => 'OrgContact[suburb]',
                            'class' => 'form-control',
                        ),
                    )); ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->labelEx($orgContact, 'address'),
            CHtml::textField('OrgContact[address]', $orgContact->address, array('size'=>40, 'class' => 'form-control')); ?>
        </div>

        <div class="row">
            <div class="col col-sm-6 col-sx-12">
                <div class="form-group">
                    <?php echo $form->labelEx($orgContact, 'postcode'),
                    CHtml::textField('OrgContact[postcode]', $orgContact->postcode, array('size'=>15, 'class' => 'form-control')); ?>
                </div>
            </div>
            <div class="col col-sm-6 col-sx-12">
                <div class="form-group">
                    <?php echo $form->labelEx($orgContact, 'country'),
                    CHtml::textField('OrgContact[country]', empty($orgContact->country) ? 'PR CHINA' : $orgContact->country, array('size'=>15, 'class' => 'form-control')); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->labelEx($orgContact, 'email'),
            CHtml::textField('OrgContact[email]', $orgContact->email, array('size'=>30, 'class' => 'form-control')); ?>
        </div>

        <div class="form-group">
            <?php echo $form->labelEx($orgContact,'status'); ?>
            <?php echo $form->radioButtonList($orgContact,'status', $this->t(array(1=>'Active', 0=>'Inactive')), array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp')); ?>
        </div>


        <div class="form-group buttons">
            <button class="btn btn-primary btn-lg" id="user_btn" type="submit"><?=$this->t('Save');?></button>
        </div>

        <?php $this->endWidget(); ?>

    </div><!-- form -->

<div class="form">
<h2>Preference</h2>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'pref-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('data-bit' => 1),
));
?>

    <div class="checkbox">
        <label>
            <?php echo CHtml::checkbox('extra[tracking]', !empty($model->org->extra['tracking'])), $this->t('Always tracking shipment.'); ?>
        </label>
    </div>

	<div class="form-group">
		<label>Page Size</label>
		<div class="input-group" style="max-width:150px">
		<?php echo CHtml::dropDownList('extra[pager_size]', empty($model->org->extra['pager_size'])? 20 : $model->org->extra['pager_size'], [20 => 20, 30 => 30, 50 => 50, 100 => 100], array('class' => 'form-control')); ?>
    	</div>
	</div>

	<div class="form-group buttons">
		<?php echo CHtml::hiddenField('extra[pos]', 1); ?> 
		<button class="btn btn-primary btn-lg" id="user_btn" type="submit"><?=$this->t('Save');?></button>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->


<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('#pwd_conf').on('change', function(){
		if($(this).val() != $('#User_password').val()){
			$(this).parent().addClass('has-error');
			$('#user_btn').attr('disabled', true);
		}else{
			$(this).parent().removeClass('has-error');
			$('#user_btn').attr('disabled', false);
		}
	});


    var cnor_state = $('#cnor_state_ac');

    //cnor state
    cnor_state.off('ac_after_select').on('ac_after_select', function(evt, ui){
        $(this).val(ui.item.value).data('sid', ui.item.id);
        if(ui.item.ocid){
            $('#cnor_city_ac').val(ui.item.value).data('cid', ui.item.ocid).focus();
            $('#Cnor_postcode').val(ui.item.oczip);
        }
    }).on('focus',function(){
        $(this).autocomplete('search', $(this).val());
    });

    var cnorSid = function(){
        var v = cnor_state.val();
        if(v != ''){
            var s = cnor_state.autocomplete('option', 'source');
            for(i in s){
                if(s[i].value == v) cnor_state.data('sid', s[i].id);
            }
        }
    };

    //cnor city
    $('#cnor_city_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
        $(this).val(ui.item.value).data('cid', ui.item.id);
        if(ui.item.aname){
            $('#cnor_suburb_ac').val(ui.item.aname);
        }
        if(ui.item.zip){
            $('#Cnor_postcode').val(ui.item.zip);
        }
    }).on('focus', function(){
        cnorSid();
        if(!$(this).data('src')) $(this).data('src', $(this).autocomplete('option', 'source'));
        $(this).autocomplete({source : $(this).data('src')+'?sid='+cnor_state.data('sid')}).autocomplete('search', $(this).val());
    }).on( "autocompletesearch", function(e, u){
        if(cnor_state.val() == '') return false;
    });

    //cnor suburb
    $('#cnor_suburb_ac').off('ac_after_select').on('ac_after_select', function(evt, ui){
        $(this).val(ui.item.value);
        $('#Cnor_postcode').val(ui.item.zip);
    }).on('focus', function(){
        if(!$(this).data('src')) $(this).data('src', $(this).autocomplete('option', 'source'));
        $(this).autocomplete({source : $(this).data('src')+'?cid='+$('#cnor_city_ac').data('cid')}).autocomplete('search', $(this).val());
    }).on( "autocompletesearch", function(e, u){
        if($('#cnor_city_ac').val() == '') return false;
    });

});
</script>
<?php $this->registerJS(ob_get_clean()); ?>