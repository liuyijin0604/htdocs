<style type="text/css">
	.weightDiffReport
	{
		width:100%;
		height: 100%;
		border: 1px solid #000;
	}
	.weightDiffReport th td
	{
		font-size: 2em;
	}

	.weightDiffReport tr td
	{
		height:50px;
		border: 1px solid #000;
	}

</style>
<?php echo CHtml::button('select All consol',array('id'=>$_GET['tabid'].'select_all_consol'));?>
<?php echo CHtml::button('select all ref',array('id'=>$_GET['tabid'].'select_all_ref'));?>
<table class="weightDiffReport">
	<thead>
		<th>
			Consol No
		</th>
		<?php if($title!='RTS'):?>
		<th>
			To Id
		</th>
		<?php endif;?>
		<th>
		</th>
		<th>
			Amount
		</th>
	</thead>
<?php
if($title=='RTS')
{
	foreach ($report as $key => $re) 
	{
		?>
			<tr>
					<td>
						<?=CHtml::checkbox('consolIds',$re['consol_id'],["value"=>$re['consol_id']])?>
						<?=$re['consol_no']?>
					</td>
					<td>
						<table class="weightDiffReport">
							<thead>
								<th>
									Select No
								</th>
								<th>
									To Id
								</th>
								<th>
									Description
								</th>
								<th>
									<?=$title?>Invoice
								</th>
							</thead>
						<?php
							foreach ($re['lines'] as $key => $line) 
							{
								?>
								<tr>
									<td><?=CHtml::checkbox('refs',$line['ref'],["value"=>$line['ref']])?><?=$line['ref']?></td><td><?=$line['to_id']?></td><td><?=$line['description']?></td><td><?=$line['amount']?></td>
								</tr>
							<?php
							}
							?>
						</table>
					</td>
					<td>
						<?=$re['amount']?>
					</td>

			</tr>

		<?php
	}
}else
{


foreach ($report as $key => $re) 
{
	?>
		<tr>
			<td>
				<?=CHtml::checkbox('consolIds',$re['consol_id'],["value"=>$re['consol_id']])?>
				<?=$re['consol_no']?>
			</td>
			<td>
				<?=$re['to_id']?>
			</td>
			<td>
				<table class="weightDiffReport">
					<thead>
						<th>
							Select No
						</th>
						<th>
							Description
						</th>
						<th>
							<?=$title?>Invoice
						</th>
					</thead>
				<?php
					foreach ($re['lines'] as $key => $line) 
					{
						?>
						<tr>
							<td><?=CHtml::checkbox('refs',$line['ref'],["value"=>$line['ref']])?><?=$line['ref']?></td><td><?=$line['description']?></td><td><?=$line['amount']?></td>
						</tr>
					<?php
					}
					?>
				</table>
			</td>
			<td>
				<?=$re['amount']?>
			</td>

		</tr>
<?php
}
?>
</table>

 <?php echo CHtml::button('Generate '.$title.' Invoice',array('id'=>$_GET['tabid'].'weight_diff_invoice'));?>

 <script type="text/javascript">
 	$(function(){
 	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	var ccheck =0;
	var rcheck =0;
    $('#<?=$_GET['tabid'];?>weight_diff_invoice').on('click',function()
	{
		var consolIds = [];
		var refIds = [];

		var consolStr="";
		var refStr="";

		$.each($("input[name='consolIds']:checkbox:checked"),function(){
	        consolIds.push($(this).val());
	    });

	    $.each($("input[name='refs']:checkbox:checked"),function(){
	        refIds.push($(this).val());
	    });

	    for(var i=0;i<consolIds.length;i++){
	    	consolStr+=consolIds[i]+","
	    }

	    for(var i=0;i<refIds.length;i++){
	    	refStr+=refIds[i]+","
	    }

	    if( confirm('Are you sure to Generate <?=$title?> Invoice?'))
	    {
	        $.get('<?=$this->createUrl($url)."?parentId=".$model->id?>'+'&&consolIds='+consolStr+"&&refs="+refStr,function(r){
	          r = JSON.parse(r);
	            if(r.done==true){
	                 myApp.alert("success");  
	             }else{
	                myApp.alert(r, false);   
	           }
	       });
	    
	    }
	});

	$('#<?=$_GET['tabid'];?>select_all_consol').on('click',function()
	{
		if(ccheck==0)
		{
			$("input[name='consolIds']").click();
			ccheck = 1;
	    }else
	    {
	    	$("input[name='consolIds']").removeAttr("checked");
	    	ccheck = 0;
	    }
	});

	$('#<?=$_GET['tabid'];?>select_all_ref').on('click',function()
	{
		if(rcheck==0)
		{
			$("input[name='refs']").click();
			rcheck = 1;
	    }else
	    {
	    	$("input[name='refs']").removeAttr("checked");
	    	rcheck = 0;
	    }
	});


	});
 </script>