<?php
if($model->status == 20) echo '<p><a class="ajax_link" href="'.$this->createUrl('exacConsol/esm', array('id' => $model->id, 'act'=>'send')).'">Send ESM</a></p>';

$m = new Edimsg('search');
if(isset($_GET['Edimsg'])){
	$m->unsetAttributes();
	$m->attributes=$_GET['Edimsg'];
}
$m->fid = $model->id;
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'edimsg-grid-'.$_GET['tabid'],
	'cssFile' => false,
	'dataProvider'=>$m->search(),
	'filter'=>$m,
	'columns'=>array(
		'dt',
		array(
            'name'=>'status',
            'value'=>'$data->getStatus()',
			'filter'=>CHtml::dropDownList('Edimsg[status]', $m->status, $this->t(Edimsg::$states), array('prompt'=>$this->t('All'))),
        ),
        ['name' => 'type', 'value' => '$data->getType()', ],
		'mid',
		['name' => 'sender', 'value' => '$data->getSender()', ],
		['name' => 'receiver', 'value' => '$data->getRecvr()', ],
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view}',
			'buttons'=>array(
				'view' => array(
					'imageUrl'=>false,
					'url' => 'Yii::app()->createUrl("edimsg/view", array("id" => $data->id))',
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	
	$('a.ajax_link', tab.data('panel')).on('success', function(e, r){
		if(r.done === true){
			tab.load();
			myApp.notice(r.msg, 5000);
		}else{
			myApp.alert(r.msg, false);
		}
	});

});
</script>