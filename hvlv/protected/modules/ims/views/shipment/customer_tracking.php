<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'homeLink'=>CHtml::link('Home', array('site/index')),
    'links' => array(
        'Customer Serivce',
    ),
));
?>

<style>
	span.reason{
		margin-right: 1.0em;
		color: blue; 
		
	}
</style>

<ul class="nav nav-tabs" id="myTab<?=@$_GET['tabid']?>">
  <li><a href="#delivery<?=@$_GET['tabid']?>" data-toggle="tab"><?=$this->t('Shipment Tracking');?></a></li>
  <li><a href="#customs_status<?=@$_GET['tabid']?>" data-toggle="tab"><?=$this->t('Customs Clearance Tracking');?></a></li>
  <li><a href="#chat<?=@$_GET['tabid']?>" data-toggle="tab"><?=$this->t('Data Trail');?></a></li>
</ul>

<div class="tab-content">
<?php
foreach ($cs as $c) {
				$p = Shipment::model()->find('(hbn = :c or ref = :c) and status !=100', [':c' => $c]);

				if (empty($p)) {
					echo '<h2>'.$this->t('Consignment not found').': '.$c.'</h2>';
					continue;
				}
				$o = $p->trackingInfo(0,true,false);
				if (empty($o)) {
					echo '<h2>'.$c.' '.$this->t('has no tracking info').'</h2>';
					continue;
				}
				echo '<h2>'.$this->t('Consignment').': '.$c.'</h2>';
				echo '<div class="gd-summary">
		<div class="row">
		<div class="col-md-4 col-sm-6 col-xs-12">
		  <label class="lbl">'.'Status'.': </label>
		  <span>'.$o->status.'</span>
		</div>
		<div class="col-md-4 col-sm-6 col-xs-12">
		  <label class="lbl">'.$this->t('Departure Depot').': </label>
		  <span>'.$o->odpt.'</span>
		</div>
		<div class="col-md-4 col-sm-6 col-xs-12">
		  <label class="lbl">'.$this->t('Departure Date').': </label>
		  <span>'.$o->odate.'</span>
		</div>
	  </div>
	  <div class="row">
		<div class="col-md-4 col-sm-6 col-xs-12">
		  <label class="lbl">'.$this->t('Service').': </label>
		  <span>'.$this->t('Express').'</span>
		</div>
		<div class="col-md-4 col-sm-6 col-xs-12">
		  <label class="lbl">'.$this->t('Delivery Depot').': </label>
		  <span>'.$o->ddpt.'</span>
		</div>
		<div class="col-md-4 col-sm-6 col-xs-12">
		  <label class="lbl">'.$this->t('Estimated Dispatch Date').': </label>
		  <span>'.$o->eta.'</span>
		</div>
	  </div>
	</div>';
}

?>
<div class="tab-pane active" id="delivery<?=@$_GET['tabid']?>">
<?php
// $held = ["id"=>-1,"type"=>"Held","detail"=>"xxx","time"=>"2000-01-04 08:00:00"];
$arr =[];
foreach ($cs as $c) {
				$p = Shipment::model()->find('(hbn = :c or ref = :c) and status !=100', [':c' => $c]);

				// if (empty($p)) {
				// 	echo '<h2>'.$this->t('Consignment not found').': '.$c.'</h2>';
				// 	continue;
				// }
				$o = $p->trackingInfo(0,true,false);
				if (empty($o)) {
					echo '<h2>'.$c.' '.$this->t('has no tracking info').'</h2>';
					continue;
				}
	// 			echo '<h2>'.$this->t('Consignment').': '.$c.'</h2>';
	// 			echo '<div class="gd-summary">
	// 	<div class="row">
	// 	<div class="col-md-4 col-sm-6 col-xs-12">
	// 	  <label class="lbl">'.$this->t('Status').': </label>
	// 	  <span>'.$o->status.'</span>
	// 	</div>
	// 	<div class="col-md-4 col-sm-6 col-xs-12">
	// 	  <label class="lbl">'.$this->t('Departure Depot').': </label>
	// 	  <span>'.$o->odpt.'</span>
	// 	</div>
	// 	<div class="col-md-4 col-sm-6 col-xs-12">
	// 	  <label class="lbl">'.$this->t('Departure Date').': </label>
	// 	  <span>'.$o->odate.'</span>
	// 	</div>
	//   </div>
	//   <div class="row">
	// 	<div class="col-md-4 col-sm-6 col-xs-12">
	// 	  <label class="lbl">'.$this->t('Service').': </label>
	// 	  <span>'.$this->t('Express').'</span>
	// 	</div>
	// 	<div class="col-md-4 col-sm-6 col-xs-12">
	// 	  <label class="lbl">'.$this->t('Delivery Depot').': </label>
	// 	  <span>'.$o->ddpt.'</span>
	// 	</div>
	// 	<div class="col-md-4 col-sm-6 col-xs-12">
	// 	  <label class="lbl">'.$this->t('Estimated Dispatch Date').': </label>
	// 	  <span>'.$o->eta.'</span>
	// 	</div>
	//   </div>
	// </div>';
	  
				echo '<table cellspacing="0" cellpadding="0" border="0" id="lvTrackingGrid" class="gvTable" style="width:100%">
	<thead>
	<tr class="thead">
	  <th style="width: 20%" align="left">'.$this->t('Date &amp; Time').' </th>
	  <th align="left">'.$this->t('Description').' </th>
	  <th style="width: 10%" align="left">'.'Depot'.' </th>
	</tr>
	</thead>
	<tbody>
	';
				foreach ($o->tracks as $key => $t) {
					$tm = isset($msgmap[$t[0]])? $msgmap[$t[0]]: $t[0];
					$m = empty($t[3])? [] : json_decode($t[3], true);
					if($t[3]=="Held"||$t[3]=="Cleared")
					{
						if ($t[3]=="Held") {
							$arr[] = ["id"=>$key,"status"=>$t[3],"event"=>"Start Custom Clearance","time"=>@$t[2],"detail"=>nl2br($tm)];
						}
						elseif ($t[3]=="Held") {
							$arr[] = ["id"=>$key,"status"=>$t[3],"event"=>"Complete Custom Clearance","time"=>@$t[2],"detail"=>nl2br($tm)];
						}
						
					}
					$tags = str_replace(';O T H E R', '',nl2br($tm));
					echo '<tr>
	  <td>'.$t[2].'</td>
	  <td>'.(empty($m['signature'])? '' : '<img src="data:image/png;base64,'.$m['signature'].'" width="150" /><br />').$tags.'</td>
	  <td>'.$t[1].'</td>
	</tr>';
				}
	
				echo '</tbody></table>';
				if ($p->status == 80) {
					echo 'EMS: <a href="http://www.kuaidi100.com/all/ems.shtml?mscomnu='.$p->ref.'" target="_blank">'.$p->ref.'</a>';
				} else {
					foreach ($p->trans as $ts) {
						echo $ts->infoLink(true).'<br />';
					}
				}
			}

?>
</div>

<div class="tab-pane" id="customs_status<?=@$_GET['tabid']?>">
<br />

<?php 
$model = Shipment::model()->find('(hbn = :c or ref = :c) and status !=100', [':c' => $cs[0]]);
if($model->status==55){
	$held_reason='<h3 style="color: #be3426">Held Reason</h3>';
	if(($model->bwf&4)>0){
		$held_reason.="<span class='reason'>High Value</span>";
	}
	if(($model->bwf&8)>0){
		$held_reason.="<span class='reason'>Sac</span>";
	}
	if(($model->bwf&16)>0){
		$held_reason.="<span class='reason'>Border</span>";
	}
	if(($model->bwf&32)>0){
		$held_reason.="<span class='reason'>AQIS</span>";
	}
	if(($model->bwf&512)>0){
		$held_reason.="<span class='reason'>EMPP</span>";
	}
	
	echo $held_reason;

}?>


<?php if(isset($model->process)&&($model->process->status<24)&&$model->status<=60):?>
<h3 style="color: #be3426">Customs Process</h3>
<h4> Shipment on custom process stage:<b><?= $model->getProcessStatus();?></b></h4>
<?php
if($model->process->status<=6||$model->process->status==10||$model->process->status==12){
	if(($model->bwf&4)>0){ 
		$link=$model->getTheHashUrl(2);
	}else if(($model->bwf&512)>0){
		$link=$model->getTheHashUrl(5);
	}else if(($model->bwf&32)>0){
		$link=$model->getTheHashUrl(6);
	}
	
	if ($model->process->status == 12) 
	{
		$link=$model->getTheHashUrl(3);
	}
	if(!empty($link))
	{
	echo "<a href='$link' style='color:blue;text-decoration: underline;' target='_blank'>Upload Document Link</a>";
	}
}

if(!empty($model->mdata['custom_note'])){
	echo '<h4 style="color: #be3426">Custom Note</h4>','<h4>'.$model->mdata['custom_note'].'</h3>';
	
}


?>
		
<hr/>
		
<?php endif;?>

<?php
echo '<h4 style="color: #be3426">Process Log</h4>';

$lid = (!empty($model->process->id)?$model->process->id:-1);
$logs=Log::model()->findAll(['condition' => 'lid = :lid and model="ShipmentProcess"', 'params' => [':lid' => $lid], 'order' => 'time ASC']);
foreach ($logs as $key => $log) {
	if(!empty($logs[$key+1])&&$logs[$key]->getType()==$logs[$key+1]->getType()){
		if($logs[$key]->getExtra()==$logs[$key+1]->getExtra()){
			unset($logs[$key]);
		}else
		{
			$arr[]= ["id"=>@$log->id,"status"=>@$log->getType(),"event"=>"Update","detail"=>@$log->getExtra(),"time"=>date('d/m/Y H:i:s', strtotime(@$log->time))];
		}
	}else
	{
		$arr[]= ["id"=>@$log->id,"status"=>@$log->getType(),"event"=>"Update","detail"=>@$log->getExtra(),"time"=>date('d/m/Y H:i:s', strtotime(@$log->time))];
	}
}


$filtersForm=new FiltersForm;
$filteredData=$filtersForm->filter($arr);
$dataprovider=new CArrayDataProvider($filteredData);
$dataprovider->pagination=['pageSize' =>20,];
$sort=new CSort();
$sort->defaultOrder = 'time DESC';
$dataprovider->sort=$sort;

// $processlog=$model->process;
// $log = new Log;
// $log->model = "ShipmentProcess";
// $log->lid = (!empty($model->process->id)?$model->process->id:-1);

$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'id'=>'_log-grid',
	'cssFile' => false,
	'summaryText'=>'',
	'dataProvider'=> $dataprovider,
	// 'dataProvider'=> $log->search(8),
	'columns'=>array(
		'time',
		// array(
  //           'name'=>'user_id',
  //           'value'=>'$data->getUser()',
  //       ),
		array(
            'name'=>'status'
        ),
        array(
            'name'=>'event'
        ),
		array(
            'name'=>'detail'
        ),
),
));

?>

</div>

<div class="tab-pane" id="chat<?=@$_GET['tabid']?>">

<?php
$emailIds = [0];
$shipmentQuestionTks = [];
$emailModel = new ImportsMail('search');
$emailModel->unsetAttributes();
if(!empty($_GET['ImportsMail']))
{
	$emailModel->setAttributes($_GET['ImportsMail']);
}
$shipmentQuestions = ShipmentQuestion::model()->findAll(['condition' => 'shipment_id = :shipment_id', 'params' => [':shipment_id' => $model->id]]);
if(!empty($shipmentQuestions)){
	foreach ($shipmentQuestions as $key => $shipmentQuestion) {
		$shipmentQuestionTks[] = $shipmentQuestion->ShipmentQuestionSubmit->ticket;
	}
	$relatedEmails = ImportsMail::model()->findAll(' `ticket` in ("'.join('","',$shipmentQuestionTks).'") or `subject` like "%'.$model->ref.'%" or `plain_body` like "%'.$model->ref.'%" or `subject` like "%'.$model->hbn.'%" or `plain_body` like "%'.$model->hbn.'%"');
	// $relatedEmails = ImportsMail::model()->findAll(' `ticket` in ("'.join('","',$shipmentQuestionTks).'")');
	$ids = array_column($relatedEmails, "id");
	$emailIds = array_merge($ids,$emailIds);
}

// $model->emailIds = $emailIds;
$sort1=new CSort();
$sort1->defaultOrder = array('date' => CSort::SORT_ASC);
$emailModel->emailIds = $emailIds;
$emailDataProvider = $emailModel->search(true, 30, false, false);
$emailDataProvider->sort = $sort1;

?>
<h1>Emails For Shipment <?=@$model->hbn?></h1>
<br>
<?php 

$shipperEmails = [];
$shipperEmails = array_merge($shipperEmails, explode(';', @$model->agent->email));
$shipperEmails = array_merge($shipperEmails, explode(';', @$model->agent->extra['outturn_email']));
$shipperEmails = array_merge($shipperEmails, explode(';', @$model->agent->extra['client_billing_email']));
// print_r($shipperEmails);

$this->widget('zii.widgets.grid.CGridView', [
    'id'=>'imports-email-total-grid-list1',
    'cssFile' => false,
    'dataProvider'=>$emailDataProvider,
    'filter'=>$emailModel,
    'columns'=>[
        'no',
        ['name'=>'status', 'value'=>'$data->getStatus()','filter'=>CHtml::dropDownList('ImportsMail[status]', $model->status, ImportsMail::$states, ['prompt'=>'All'])],
        'from_email',
        ['name'=>'to_email','type'=>'raw','value'=>function ($data) {
            return CHtml::tag('div', ['title'=>$data->to_email], substr($data->to_email, 0, 25));
        }],
        ['name'=>'subject','type'=>'raw','value'=>function ($data) {
            return CHtml::tag('div', ['title'=>$data->subject], mb_substr($data->subject, 0, 60));
        }],
        ['name'=>'plain_body','type'=>'raw','value'=>function ($data) use ($shipperEmails){
        	if(!empty(array_intersect(explode(';',$data->to_email), $shipperEmails)) || !empty(array_intersect(explode(';',$data->from_email), $shipperEmails)))
        	{
        		return CHtml::tag('div', ['title'=>strip_tags($data->plain_body)], mb_substr(strip_tags($data->plain_body), 0, 25));
        	}
        }],
        'create_time',
        ['name'=>'date', 'header'=>'Reply Time'],
       
    ]]);
        ?>
</div>

</div>

<script type="text/javascript">
$(function(){

    $('#myTab<?=@$_GET['tabid']?> a:first').tab('show');

    $('#myTab<?=@$_GET['tabid']?> a').click(function (e) {
      e.preventDefault();
      $(this).tab('show');
    });

});


</script>