
<style type="text/css">
	p{
		font-size: 16px;
		color:black;
	}
</style>
<div class="content-padded">
	<h3>Please confirm details before delivery:</h3>
	<div class="form">

	<form method="post" enctype="multipart/form-data">
	 
		<div class="row">

			<div id="divSummary" class="col-12" style="padding-left: 5px;">
				</br>
				</br>
				<h3>Successfully Confirmed</h3>
				<br/>
				<?php
				$strSummary = '<p>Contact Us:</p><p>Top Logistics Australia</p><p>Email: cartage@toplogistics.com.au</p><br/><br/>';

				$modelCJob = CargoProcessJobRelations::model()->find('cargo_process_id=:cid and active = 1', [':cid' => $model->id])->job;
				if(!empty($modelCJob)){
					$strDate = $modelCJob->funcParseDeliveryTime($modelCJob->created,$modelCJob->mdata['time'],'');
					//$strReplaceDate = '<br/><br/><h4>Delivery Time</h4>'.$strDate.'<br/><br/>';
					$strSummary = '<h4>Delivery Time</h4>'.$strDate.'<br/><br/><p>Contact Us:</p><p>Top Logistics Australia</p><p>Email: cartage@toplogistics.com.au</p><br/><br/>';
				}
				
				
				if(isset($model->mdata['customer_confirmed_summary'])){
					$strSummary = $model->mdata['customer_confirmed_summary'];
				}
				if(isset($model->mdata['customer_confirmed']['surcharge_invoice_download'])){
					$strUrl = $model->mdata['customer_confirmed']['surcharge_invoice_download'];
					$strReplace = '<br/><br/><h4>Invoice</h4><a onclick ="window.open(\''.$strUrl.'\');">download</a><br/><br/><h4>Other special notes:';
					$strSummary = str_replace("<br/><br/><h4>Other special notes:",$strReplace,$model->mdata['customer_confirmed_summary']);
				}
				if(isset($model->mdata['customer_confirmed']['catgo_process_job'])){
					$objJob = CargoProcessJob::model()->find('job_no=:job_no',[':job_no'=>$model->mdata['customer_confirmed']['catgo_process_job']]);
					$strDate = $objJob->funcParseDeliveryTime($objJob->created,$objJob->mdata['time'],'');
					$strReplaceDate = '<br/><br/><h4>Delivery Time</h4>'.$strDate.'<br/><br/>';
					$strSummary = str_replace('<div id="divDeliveryTime"></div>',$strReplaceDate,$strSummary);
				}
				
				?>
                <?=$strSummary?>
			</div>
		</div>

	</form>

	</div>
</div>
