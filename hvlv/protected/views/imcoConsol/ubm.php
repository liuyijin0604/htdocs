                                <?php  $typeName="Air";  
                                $isAir=true; 
                                if($model->service == 20){
                                    $typeName="Sea";
                                    $isAir=false;
                                } 
                                                             ?>
<h2><?=$typeName?> Underbond Movement</h2>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'ubm-form',
	'enableAjaxValidation'=>false,
        'action'=>$this->createUrl('imcoConsol/ubm',array('id'=>$model->id)),
)); ?>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'awb',array('label'=>$isAir?"awb":"Ocean Bill")); ?>
		<?php echo $form->textField($model,'awb',array('size'=>15,'maxlength'=>50, 'class' => 'required')); ?>
	</div>

	<div class="row rowcol">
        <?php echo $form->labelEx($model,'etd'); ?>
        <?php echo $form->textField($model,'etd', array('size'=>15,'class' => 'required date_input', 'id' => $_GET["tabid"].'_jqm_etd')); ?>
    </div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'eta'); ?>
		<?php echo $form->textField($model,'eta', array('size'=>15,'class' => 'required date_input', 'id' => $_GET["tabid"].'_jqm_eta')); ?>
	</div>


	<div class="row rowcol rowleft">
		<?php echo CHtml::label('HAWB No.', 'hwb'); ?>
		<?php echo CHtml::textField('mdata[house_bill]', @$model->mdata['house_bill'], ['size'=>20,'maxlength'=>50]); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'airline',array('label'=>$isAir?"airline":"Vessel")); ?>
		<?php echo $form->textField($model,'airline',array('size'=>15,'maxlength'=>50, 'class' => 'required')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'flight',array('label'=>$isAir?"flight":"Voyage Number")); ?>
		<?php echo $form->textField($model,'flight',array('size'=>15,'maxlength'=>50, 'class' => 'required')); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('Origin Est. ID','ubm_origin');?>
		<?php echo CHtml::dropdownList('mdata[ubm_origin]', empty($model->mdata['ubm_origin'])?$model->getDeportOrigin():$model->mdata['ubm_origin'], $model->getDeportOriginList(),array('empty' => 'Select One'),array('size'=>15, 'class' => 'required')); ?>
		<?php
			echo "</br>";
			echo CHtml::textField('mdata[ubm_origin_other]', @$model->mdata['ubm_origin_other'], array('size'=>15,"style"=>"display:".(empty($model->mdata['ubm_origin_other'])?'none':'block')));
		?>
	</div>
         <div class="row rowcol">
		<?php echo CHtml::label('Discharge Est. ID','ubm_discharge');?>
		<?php
		//if(empty($model->mdata['house_bill'])||!$isAir){
			echo CHtml::textField('mdata[ubm_discharge]', @$model->mdata['ubm_discharge'], array('size'=>15,));
		//}
		?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('Destination Est. ID','ubm_dest');?>
		<?php echo CHtml::textField('mdata[ubm_dest]', @$model->mdata['ubm_dest'], array('size'=>15, 'class' => 'required')); ?>
	</div>

	<div class="row buttons">
		<?php
		$ssb = true;
        $onschedule = false;
		if(!empty($model->mdata['ubmsent'])){
			switch($model->mdata['ubmsent']){
				case 10:
					echo '<b style="color:#700">UNDERBOND OVERRIDE</b>';
					$ssb = false;
				break;
				case 9:
					echo '<b style="color:#070">UNDERBOND APPROVED</b>';
					$ssb = false;
				break;
				case 7:
					echo '<b style="color:#700">UNDERBOND REJECTED</b>';
				break;
                case 2:
                    echo '<b style="color:#221cb4">ON SCHEDULE</b>';
                    $onschedule = true;
                    break;
				default:
					if($model->findUbmSent())
					{
						echo '<b style="color:#070">UNDERBOND APPROVED</b>';
						$ssb = false;
					}elseif($model->findUbmSentOverride())
					{
						echo '<b style="color:#700">UNDERBOND OVERRIDE</b>';
						$ssb = false;
					}else
					{
						echo '<b style="color:#070">ALREADY SENT</b>';
					}
				break;
			}
		}
        if ( $onschedule ) {
            echo '&nbsp;&nbsp;' . CHtml::submitButton($this->t('Delete'),array('name'=>'delete'));
            echo '&nbsp;&nbsp;' .CHtml::submitButton($this->t('Force Send UBM'),array('name'=>'force_ubm'));
        } else {
            if ($ssb) echo '&nbsp;&nbsp;' . CHtml::submitButton($this->t('Send UBM'));
        }
         echo '&nbsp;&nbsp;' . CHtml::submitButton($this->t('Save'));
        if(Acl::hasAccess('B:imcoconsol/approveUbm'))
        {
        	 echo '&nbsp;&nbsp;' .CHtml::button($this->t('Approve UBM'),array('class'=>'approve_ubm'));
        }


		?>
	</div>

    <div class="row rowcol">
    <?php if ( !empty($model->mdata['ubmsent']) && $model->mdata['ubmsent'] == 7 && isset($model->mdata['ubm_reject_reason']) && !empty($model->mdata['ubm_reject_reason']) )
        echo '<textarea rows="8" cols="50">' . $model->mdata['ubm_reject_reason'] . '</textarea>';
    ?>
   </div>
<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	$('form#ubm-form', win).on('success', function(e, r){
		win.data('opener').trigger('reload_tab');
		win.jqmHide();
	});

	$('.approve_ubm',win).on('click',function(){
		if( confirm('Are you sure to approve ubm?')){
				$.get('<?=$this->createUrl("imcoConsol/approveUbm")."?id=".$model->id?>',function(r){
					r = JSON.parse(r);
						if(r.done==true){
								myApp.notice('Done', 5000);
						 }else{
								myApp.alert(r, false);   
					 }
			 });
		
		}
	});

	$('#mdata_ubm_origin',win).on('change',function(){

		if($(this).val()=='other')
		{
			$('#mdata_ubm_origin_other',win).show();
		}else
		{
			$('#mdata_ubm_origin_other',win).hide();
		}
	});

});
</script>