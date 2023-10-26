<style>
	.operate_button {
		font: bold 11px Arial;
		text-decoration: none;
		background-color: #EEEEEE;
		color: #333333;
		padding: 2px 6px 2px 6px;
		border-top: 1px solid #CCCCCC;
		border-right: 1px solid #333333;
		border-bottom: 1px solid #333333;
		border-left: 1px solid #CCCCCC;
	}
	.statistics-grid-container {
		width: 400px;
	}
</style>
<?php
/* @var $this GoogleReviewController */
/* @var $model GoogleReview */

$this->breadcrumbs = array(
	'Google Reviews' => array('index'),
	'Manage',
);

$this->menu = array(
	array('label' => 'List GoogleReview', 'url' => array('index')),
	array('label' => 'Create GoogleReview', 'url' => array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#google-review-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Google Review History Records</h1>
<div class="statistics-grid-container">
<h2>Actural Google Review Data</h2>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id' => $_GET["tabid"] . '_sub_google_review_statistics_grid',
	'dataProvider' => $statistics->getGoogleReviewStatistics(),
	'filter' => $statistics,
	'columns' => array(
		array('type' => 'raw', 'name' => 'create_time', 'header'=>'Date', 'htmlOptions'=>array('style'=>'width: 200px; text-align: center;')),
		array(
			'type' => 'raw', 
			'header'=>'Total', 
			'value' => function($data){
				return $data->mdata['total'];
			},
			'htmlOptions' => array(
				'style' => 'text-align: center;'
			),
		),
		array(
			'type' => 'raw', 
			'header'=>'Rating', 
			'value' => function($data){
				return $data->mdata['rating'];
			},
			'htmlOptions' => array(
				'style' => 'text-align: center;'
			),
		),
	),
));
?>
</div>
<h2>History Records</h2>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id' => $_GET["tabid"] . '_sub_google_review_history_grid',
	'dataProvider' => $model->getHistoryRecords(),
	'filter' => $model,
	'columns' => array(
		array(
			'name' => 'hbn', 
			'type' => 'raw', 
			'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->shipment_id))."\" class=\"tab_link\" title=\"".$data->shipment->hbn."\">".$data->shipment->hbn."</a>"',
			'htmlOptions' => array(
				'style' => 'width: 150px',
			),
		),
		array(
			'name' => 'time_done',
			'header' => 'Delivery Date',
			'htmlOptions' => array(
				'style' => 'width: 150px',
			),
		),
		array(
			'name' => 'time_processed', 
			'header' => 'Google Review Date',
			'htmlOptions' => array('style' => 'width: 150px'),
		),
		array(
            'type' => 'raw',
            'header' => 'Status',
            'value' => function($data) {
                if ($data->status == GoogleReview::CALLED_NO_ANSWER) {
                    return 'Called No Answer';
                } elseif ($data->status == GoogleReview::REFUSED) {
                    return 'Refused';
                } elseif ($data->status == GoogleReview::EMAIL_SENT) {
                    return 'Email Sent';
                } else {
                    return 'Error';
                }
            },
			'htmlOptions' => array(
				'style' => 'width: 120px; text-align: center;',
			),
        ),
        'problem',
		'comment',
		array(
			'class' => 'CButtonColumn',
			'template' => '{Comment}',
			'buttons' => [
				'Comment' => [
					'url' => 'Yii::app()->createURL("googleReview/addComment")."?id=".$data->id',
					'imageUrl' => false,
					'visible' => 'true',
					'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Comment'],
				],
			]
		),
	),
)); ?>
