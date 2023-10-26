<div class="row">
	<div class="col">
	<?php 
	$columns=[];
	$columns1=[];
	foreach ($attributes as $key=>$value)
	{
			if($value=='id')
			{
				continue;
			}
			$column = [];
			if($value=="hbns"||$value=="hbnsOnly"||$value=="refs"||$value=="amazonPos"||$value=="amazonShipmentIds")
			{
				$columns[]=['name'=>$value,'header'=>$key,'type'=>'raw','htmlOptions'=>["style"=>"WORD-WRAP: break-word" ],'value'=>'"<div style=\"width:350px;\">".$data["'.$value.'"]."</div>"']; 
			}else
			{	
				$columns[]=['name'=>$value,'header'=>$key]; 
			}
	}

	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'fbareport-grid',
		'htmlOptions'=>array('style'=>'width: 70%'),
		'cssFile' => false,
		'dataProvider'=>$dataProvider,
		'filter'=>$filter,
		'columns'=>$columns,
	)); 

	?>
	</div>

</div>






