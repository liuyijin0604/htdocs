<?php if(empty($model)){
	echo '<h2>Customer Validate </h2>';
	echo '<h3>Dear Customer,</h3>';
	echo '<p>The tracking number you provided is : '.$caref.'</p>';
}
?>

<div class="form" style="margin-left:15px;">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'courier-list-form',
		'enableAjaxValidation' => false,
		'action' => $this->createUrl('booking/customerView',array('caref' => $caref)),
		'htmlOptions' => ['target' => '_self'],
	)); ?>


	<?php 

	if(!empty($model)){
		
		$strSummary = '<p>Contact Us:</p><p>Top Logistics Australia</p><p>Email: cartage@toplogistics.com.au</p><br/><br/>';
			if(isset($model->mdata['customer_confirmed_summary'])){
				$strSummary = $model->mdata['customer_confirmed_summary'];
			}
			if(isset($model->mdata['customer_confirmed']['surcharge_invoice_download'])){
				$strUrl = $model->mdata['customer_confirmed']['surcharge_invoice_download'];
				$strReplace = '<br/><br/><h4>Invoice</h4><a onclick ="window.open(\''.$strUrl.'\');">download</a><br/><br/><h4>Other special notes:';
				$strSummary = str_replace("<br/><br/><h4>Other special notes:",$strReplace,$model->mdata['customer_confirmed_summary']);
			}
			if(isset($model->mdata['customer_confirmed']['catgo_process_job'])){
				$strDriverInfo = "<p>Driver's contact number : 12345678</p>";
				$objJob = CargoProcessJob::model()->find('job_no=:job_no',[':job_no'=>$model->mdata['customer_confirmed']['catgo_process_job']]);
				$strDate = $objJob->funcParseDeliveryTime($objJob->created,$objJob->mdata['time'],'');
				$strReplaceDate = '<br/><br/><h4>Delivery Time</h4>'.$strDate.'<br/><br/>'.$strDriverInfo;
				$strSummary = str_replace('<div id="divDeliveryTime"></div>',$strReplaceDate,$strSummary);
			}?>
			<?=$strSummary?>
	<?php
	}else{

		if(!empty($message)){
			echo '<div class="row">', CHtml::label($message,'',array('style'=>"color:red")), '</div></br>';
		}else{
			echo '<div class="row"><p><a style="color:red;">* </a><a>Please input the postcode of this shipping</a></p></div>';
		}
	?>
		<?php 
			if(empty($inputpostcode)){
				echo '<div class="row">', CHtml::textField('postcode'), '</div></br>';
				echo '<div class="row buttons">';
				echo CHtml::submitButton($this->t('Submit')); 
				echo '</br></br>';
				echo '</div>';
			}	
		?>
	<?php
		}
	?>
	<?php $this->endWidget(); ?>
</div>