<?php 
$columns=[];
if(empty($attributes)){
	$columns=array(
		array('name'=>'amount','value'=>''),
	);
}else{
	foreach ($attributes as $key=>$value){
		if($value=='no'){
			 $columns[]=array('name'=>'no','header'=>$key,'type' => 'raw','value'=>'"<a href=\"".Yii::app()->createURL(($data["type"]==15)?"imcoConsol/update":(($data["type"]==80)?"elmsConsol/update":"dmawbConsol/update" ), array("id" =>$data["id"]))."\" class=\"tab_link\" title=\"".$data["no"]."\">".$data["no"]."</a>"');
		}else{
		   $columns[]=['name'=>$value,'header'=>$key]; 
		}
	}
}
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'im-wd-sum-report-grid',
	'htmlOptions'=>array('style'=>'width: 70%'),
	'cssFile' => false,
	'dataProvider'=>$model,
	'filter'=>$filter,
	'columns'=>$columns,
)); ?>
