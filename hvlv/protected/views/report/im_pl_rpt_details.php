<div class="row">
	<?php 
	$columns=[];
	$columns1=[];
	if(empty($attributes))
	{
		$columns=array(
			'eta',
			'owner',
			'shipments',
			'weight',
			'revenue',
			'cost',
			'gp',
			array('name'=>'profit_rate','value'=>'$data["profit_rate"]."%"'),
		);
	}else{
		foreach ($attributes as $key=>$value){
			if($value=='no'){
				 $columns[]=array('name'=>'no','header'=>$key,'type' => 'raw','value'=>'"<a href=\"".Yii::app()->createURL(($data["type"]==15)?"imcoConsol/update":(($data["type"]==80)?"elmsConsol/update":"dmawbConsol/update" ), array("id" =>@$data["consol_id"]))."\" class=\"tab_link\" title=\"".$data["no"]."\">".$data["no"]."</a>"');
			}else if($value=='profit_rate'){
				 $columns[]=['name'=>$value,'header'=>$key,'value'=>'$data["profit_rate"]."%"']; 
			}else{
			   $columns[]=['name'=>$value,'header'=>$key,'type'=>'raw']; 
			}
		}
	}
	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'im-pl-by-cost-detail-grid',
		'cssFile' => false,
		'dataProvider'=>$model,
		'filter'=>$filter,
		'columns'=>$columns,
	)); 

	?>
</div>






