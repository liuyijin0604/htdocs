
<?php if(!empty($total)):?>
<span> Total : Revenue <span style="font-weight: bold;"> <?php echo $total['revenue']; ?></span>
	Cost <span style="font-weight: bold;"> <?php echo $total['cost']; ?> </span>
		GP <span style="font-weight: bold;"> <?php echo $total['gp']; ?></span>
	  Profit <span style="font-weight: bold;"> <?php echo $total['profit']; ?></span>  </span>
<?php endif;?>
<div>
	<div class="col">
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
			} else if (in_array($value, ['accrual', 'invoice_payable', 'dispute', 'confirm_payable', 'to_xero'])) {
				$columns[] = ['name' => $value, 'header' =>$key, 'type' => 'raw', 'footer' => '<div style="width: 100%; text-align: right">' . AppHelper::money_format('%i', $total[$key]) . '</div>']; 
			} else {
			   $columns[]=['name'=>$value,'header'=>$key,'type'=>'raw']; 
			}
		}
	}
	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'im-pl-sum-report-grid',
		'htmlOptions'=>array('style'=>'width:100%'),
		'cssFile' => false,
		'dataProvider'=>$model,
		'filter'=>$filter,
		'columns'=>$columns,
	)); 

	?>
	</div>

	<div class="col">
		<?php
			if(isset($attributes1))
			{
				foreach ($attributes1 as $key=>$value)
				{
					   $columns1[]=['name'=>$value,'header'=>$key]; 
				}
				$this->widget('zii.widgets.grid.CGridView', array(
				'id'=>'im-pl-sum-report-grid1',
				'htmlOptions'=>array('style'=>'width: 100%'),
				'cssFile' => false,
				'dataProvider'=>$model1,
				'filter'=>$filter1,
				'columns'=>$columns1,
				));
			}
		?>
	</div>

</div>

<?php
if (!empty($model2)) {
	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'im-pl-sum-report-grid',
		'htmlOptions'=>array('style'=>'width: 30%; position: absolute; right: 0'),
		'cssFile' => false,
		'dataProvider'=>$model2,
		'filter'=>$filter,
		'columns'=>array(
			'charge_code',
			'code',
			array('name' => 'total', 'type' => 'raw', 'value' => '$data["total"]'),
		),
	));
}
?>





