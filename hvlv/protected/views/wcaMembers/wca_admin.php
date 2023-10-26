<h1>WCA Members Management</h1>
<div style="position:absolute; right: 20px">
    <a class="jqm_link" href="<?=Yii::app()->createAbsoluteUrl('wcaMembers/pretpl')?>"><span class="icon" style=" background-position:-32px 0;"></span>create Template</a>
    <a class="jqm_link" href="<?=Yii::app()->createAbsoluteUrl('wcaMembers/importWCA')?>"><span class="icon" style=" background-position:-16px 0;"></span>Import Email</a>
    <a class="jqm_link" href="<?=Yii::app()->createAbsoluteUrl('wcaMembers/createWCA',array('type'=>30))?>"><span class="icon"></span>WCA Email</a>
</div>

<div class="pane" style="width: 70%;">
<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET["tabid"].'_wca-contact-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'name',
		array(
			'name'=>'email',
			'type' => 'raw',
			'value' => 'CHtml::link($data->email, "mailto:".$data->email)',
		),
            'phone',
            array(
			'class'=>'oButtonColumn',
                         'template'=>"{update}",
                         'buttons'=>array(
                             'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn','label'=>$this->t('Update'), 'title' => '$data->name'),
				),
                         )
		),
            ),
	));
?>
</div>