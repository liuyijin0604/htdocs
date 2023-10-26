
	<?php 
	foreach ($attributes as $key1 => $attribute) 
	{
		echo '<div class="row">';
		echo '<h3>'.$labels[$key1].'</h3>';
		echo '<div class="col">';
		$columns=[];
		if(!empty($attribute))
		{
			foreach ($attribute as $key=>$value){
				if($value=="name"&&$key1!="rts")
				{
				   $columns[]=['name'=>$value,'header'=>$key,'value'=>'$data["state"]=="TOTAL"?$data["name"]:""']; 
				}else
				{
				   $columns[]=['name'=>$value,'header'=>$key]; 
				}
			}
			if($key1!="rts")
			{
				$columns[]=	[
						'class'=>'oButtonColumn',
						'template'=>'{CheckState}&nbsp;',
						'buttons'=>[
							'CheckState' => [
								'url'=>' Yii::app()->createURL("#")',
								'imageUrl'=>false,
								'visible'=>'$data["state"]=="TOTAL"?true:false',
								'options' => ['class' => 'add_key_to_state_field grid_edit_btn', 'label'=>$this->t('Operation'),'value'=>'$data["name"]','title'=>$key1],
							]
						],
					];
			}
			$this->widget('zii.widgets.grid.CGridView', array(
				'id'=>'im-pl-sum-report-grid'.$key1,
				'htmlOptions'=>array('style'=>'width: 70%'),
				'cssFile' => false,
				'dataProvider'=>$dataProvider[$key1],
				'filter'=>$filter[$key1],
				'columns'=>$columns,
				'afterAjaxUpdate'=>'function(){initStateButton();}',
			)); 
		}
		echo '</div>';
		echo '</div>';

	}

	?>
	<script type="text/javascript">
		function initStateButton()
		{
			$('.add_key_to_state_field').on('click',function(e){
				openStateRowKey = $('#openStateRowKeys_'+$(this).attr('title')).val();
				if(openStateRowKey.indexOf($(this).attr('value')) == -1 )
				{
					openStateRowKey+=$(this).attr('value')+";";
				}else
				{
					openStateRowKey = openStateRowKey.replace($(this).attr('value')+";","");
				}
				$('#openStateRowKeys_'+$(this).attr('title')).val(openStateRowKey);
				$('#icr_report_submit').click();
			});
		}
		 initStateButton();

	</script>






