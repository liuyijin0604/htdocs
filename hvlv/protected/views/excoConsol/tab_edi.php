<?php
if(empty($model->mdata['subc'])){
	if($model->status == 20) echo '<p><a class="ajax_link" href="'.$this->createUrl('excoConsol/edi', array('id' => $model->id, 'act'=>'send')).'">Send ESM</a></p>';
	//if($model->status == 40 && Acl::hasAccess($this->CaName.'/F:WithdrawESM')) echo '<p><a class="ajax_link" href="'.$this->createUrl('excoConsol/edi', array('id' => $model->id, 'act'=>'withdraw')).'">Withdraw ESM</a></p>';
}else{
	foreach($model->mdata['subc'] as $i => $s){
		$rs = Manifest::model()->findAll('id IN ('.$s['pids'].')');
		$ps = [];
		foreach($rs as $r){
			$ps[] = $r->ref;
		}
		echo '<h3>Sub Consol. '.($i+1).'</h3>';
		echo '<p>Pallets: '.implode(', ',$ps).'</p>';
		if(empty($s['ecn'])){
			echo '<p><a class="ajax_link" href="'.$this->createUrl('excoConsol/edi', array('id' => $model->id, 'act'=>'send', 'sub' => $i)).'">Send ESM</a></p>';
		}else{
			echo '<p>CRN: <b style="font-size:16px;color: #080;">'.$s['ecn'].'</b></p>';
		}
	}
}

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