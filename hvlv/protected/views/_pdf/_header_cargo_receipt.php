
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<?php
	if(!isset($model))
	{
		$model = $models[0];
	}
	?>
	<td width="200" valign="top"><img src="<?=Yii::app()->request->hostInfo.Yii::app()->baseUrl.'/images/'.(Yii::app()->name == 'PEP'? 'PEP_logo.png' : $model->agent_id == 1474&&$cargoType ==CargoProcess::NORMAL_CARGO?'austway_logo.png':'pcae_logo.svg');?>" alt="TLA" width="398" height="<?=$model->agent_id == 1474?'117':'87'?>" /></td>
	<td align="right" valign="top"><p style="font-size:39px;color:#14487E;font-weight:bold;">
	<?php if($model->agent_id == 1474&&$cargoType ==CargoProcess::NORMAL_CARGO):?>
		Austway Logistics Pty Ltd</p>
		  <p><strong>ABN: 69 63 392 1098</strong><br />
		  	Email:<a href="mailto:AustwayLogistics@gmail.com">AustwayLogistics@gmail.com</a>
	<?php else:?>
		<?php if(Yii::app()->name == 'PEP'): ?>
			PCA Express Parcel</p>
		  <p><strong>ABN: 81 255 611 743</strong><br />
		  	53 Christian Road, Punchbowl NSW 2196<br />
			   <?php
			  if((isset($inv) && in_array($inv->type, [10,30,31,33,34,35,36,37,38,39,40,41,42,45,90,102]))||isset($type)&&($type=='Sea'||$type=='Air')):
			  ?>
			  Phone: +61 2 9925 7111<br />
			  <?php else:?>
			  Phone: +61 2 9925 7100<br />
			  <?php endif; ?>
		  E-Mail: <a href="mailto:accounts@ep.pcaex.com">accounts@ep.pcaex.com</a>
		  <?php else: ?>
			Top Logistics</p>
		  <p><strong>ABN: 37 169 904 312</strong><br />
		  <?php
		  if( isset($inv) && in_array($inv->type, [20, 40]) && $inv->dpt_id == 218):
		  ?>
		  18 Grimes Court, Derrimut, VIC 3030<br />
		  Phone: +61 3 8366 1450<br />
		  <?php 
		  elseif( isset($inv) && in_array($inv->type, [20, 40]) && $inv->dpt_id == 530):
		  ?>
		  7-9 Eileen St, Underwood, QLD 4109<br />
		  Phone: +61 7 3088 6676 <br />
		  <?php else: ?>
		  6C The Crescent, Kingsgrove, NSW 2208<br />
		  		   <?php
			  if((isset($inv) && in_array($inv->type, [10,30,31,33,34,35,36,37,38,39,40,41,42,45,90,102]))||isset($type)&&($type=='Sea'||$type=='Air')):
			  ?>
			  Phone: +61 2 9925 7111<br />
			  <?php else:?>
			  Phone: +61 2 9925 7100<br />
			  <?php endif; ?>
		  <?php endif; ?>
		  E-Mail: 
		  	    <?php if(isset($type)&&($type=='Sea'||$type=='Air')):?>
		  	    	<a href="mailto:imports@toplogistics.com.au">imports@toplogistics.com.au</a>
		  	    <?php else: ?>
		  			<a href="mailto:finance@toplogistics.com.au">finance@toplogistics.com.au</a>
		 		<?php endif; ?>
		  <?php endif; ?>
	  <?php endif; ?>
	</p></td>
</tr>
</table>