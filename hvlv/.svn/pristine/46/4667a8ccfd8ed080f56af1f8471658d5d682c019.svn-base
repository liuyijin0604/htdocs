<?php
if(empty($model)):
?>
<p>The label requested not found, please check with your RMA details.</p>
<?php
else:
?>
<h3>Review and Print your Label <span style="color:#14487E;font-size:1.2em"><?=$model->ref;?></span></h3>
<div style="border: 1px solid #14487E; padding: 5px 15px;">
<div class="row">
	<div class="col-md-6">
		<h4 style="text-decoration: underline;">From</h4>
		<p><?=$model->cnor->name;?><br /><?=$model->cnor->address;?><br /><?=$model->cnor->suburb;?>, <?=$model->cnor->city;?>, <?=$model->cnor->state;?> <?=$model->cnor->postcode;?></p>
	</div>
	<div class="col-md-6">
		<h4 style="text-decoration: underline;">To</h4>
		<p><?=!empty($model->agent->extra['return_label_show_name']) ? ($model->agent_id == 3384 ? 'Creative Labs Pte Ltd C/O' : $model->agent->name) . '<br>' : ''?>Top Logistics - 3PL<br /><?=$model->cnee->address;?><br /><?=$model->cnee->suburb;?>, <?=$model->cnee->state;?> <?=$model->cnee->postcode;?></p>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<h4 style="text-decoration: underline;">Consignment Detail</h4>
		<div class="row">
			<div class="col-md-3 col-xs-6">
				<p>Service: <br />
					Weight: <br />
					Value: 
				</p>
			</div>
			<div class="col-md-3 col-xs-6">
				<p>eParcel Return <br />
					<?=$model->weight;?><br />
					$0.00
				</p>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<h4 style="text-decoration: underline;">Items</h4>
		<?=implode(', ', $model->eitems['g']);?>
	</div>
</div>
</div><br />
<p align="right" style="margin-bottom: 3em;"><a href="/client/return/print/<?=$_GET['h'];?>/<?=$_GET['c'];?>.pdf" target="_blank" class="btn-primary btn-lg">Print Label</a></p>
<?php
endif;
?>
