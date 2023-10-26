<?php

?>

<h2>Imports Mail Records</h2>
<a class="export_search" target="_blank"  href="<?php echo Yii::app()->createUrl('importsMail/exportFeedbackDetails').'?op_id='.$model->op_id ?>">Export Current Search</a>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id' => $_GET["tabid"] . '_sub_imports_mail_record_grid',
	'dataProvider' => $model->getMailRatingByOp($model->op_id),
	'filter' => $model,
	'columns' => array(
		array(
			'name' => 'no', 
			'header' => 'No',
			'htmlOptions' => array(
				'style' => 'width: 150px',
			),
		),
		array(
			'name' => 'ticket',
			'header' => 'Ticket',
			'htmlOptions' => array(
				'style' => 'width: 150px',
			),
		),
		array(
			'name' => 'from_email', 
			'header' => 'From email',
			'htmlOptions' => array('style' => 'width: 150px'),
		),
        array(
			'name' => 'to_email', 
			'header' => 'To email',
			'htmlOptions' => array('style' => 'width: 150px'),
		),
        array(
			'name' => 'op_id', 
			'header' => 'Op Id',
			'htmlOptions' => array('style' => 'width: 150px'),
		),
        array(
			'name' => 'date', 
			'header' => 'Reply Time',
			'htmlOptions' => array('style' => 'width: 150px'),
		),
		array(
            'type' => 'raw',
            'header' => 'Rating',
            'value' => function($data) {
                return $data->mdata['rating'];
            },
			'htmlOptions' => array(
				'style' => 'width: 120px; text-align: center;',
			),
        ),
		[
			'class'=>'oButtonColumn',
			'template'=>'{emails}',
			'buttons'=>[
				'emails' => [
                    'imageUrl'=>false,
                    'options' => ['class' => 'tab_link grid_view_btn','title'=>'$data->ticket', 'label' => 'Log', 'data-win-class' => 'L'],
                    'visible' => 'empty($data->ticket)?false:true',
                    'url' => 'Yii::app()->createUrl("customerService/getEmailRelatedEmails")."?ticket=".$data->ticket',
                    'label' => 'Related Emails',
                ],
			],
		]
	),
)); 
?>