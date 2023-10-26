<h3>Org Pricing Note</h3>
<div style="position:absolute; right: 520px;top: 25px">
    <a class="jqm_link"  href="<?=$this->createUrl("org/editNote").'?orgId='.$model->id?>" title="Add Note" ><span class="icon"></span>Add Note</a>
</div>
</br>
</br>
</br>
<?php 
$note = new GeneralNote();
$note->model = 'Org';
$note->fid = $model->id;	
	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
	'id'=>$_GET["tabid"].'_org_note_grid',
	'cssFile' => false,
	'dataProvider'=>$note->search(true, 30),
	'filter'=>$note,
	'columns'=>[		
		array('name' => 'created'),
		array('name'=>'subject'),
		['class'=>'oButtonColumn',
			'template'=>'{detail}',
			'buttons'=>[
				'detail' => [
					'url'=>' Yii::app()->createURL("org/editNote")."?id=".$data->id',
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'jqm_link grid_view_btn', 'label'=>$this->t('Details'), 'title' => '$data->id'],
				]
			],
		]
	],
]);

?>