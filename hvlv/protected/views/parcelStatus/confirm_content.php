<p>Please Confirm AQIS Status For Parcel: <?=$model->hbn?></p>
<div class="form">
<?php 
	$id = $model->id;
	$url = $this->createUrl('parcelStatus/confirmAqis');
	if(isset($ids) && $ids != "")
	{
		$id = $ids;
	}
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'parcel-status-form',
	'enableAjaxValidation'=>false,
	'action'=> $url."?id=".$id)
	);
?>

	
	<div class="row buttons">
		<?php

			if(empty($model->process->mdata['customer_confirm']))
			{
				echo CHtml::button('Confirm Inspection',array('class'=>'confirm_inspection'));
				echo "&nbsp;&nbsp;&nbsp;&nbsp;";
				echo CHtml::button('Confirm Disposal',array('class'=>'confirm_disposal'));
			}else
			{
				echo "Confirmed: ".@$model->process->mdata['customer_confirm']." at ".@$model->process->mdata['customer_confirm_date'];
			}
		?>
			
	</div>

<?php
$this->endWidget();
?>

<script type="text/javascript">
	$('.confirm_inspection').click(function(){
		var qs={status:<?=$model->process->status?>,action:"confirm inspection",};
		if(confirm('Are you sure to confirm inspection?')){
			goNextAqisStatus(qs)
		}
	});

	$('.confirm_disposal').click(function(){
		var qs={status:<?=$model->process->status?>,action:"confirm disposal",};
		if(confirm('Are you sure to confirm disposal?')){
			goNextAqisStatus(qs)
		}
	});


	function goNextAqisStatus(qs)
	{
		$.post('<?=$this->createUrl("parcelStatus/confirmAqis", ["hbn"=>$model->hbn,"ref"=>$model->ref,"h"=>$_GET['h']]);?>', $.param(qs), function(r){
				r= JSON.parse(r);
				if(r.done){
					alert("success");
				}else{
					alert("failure");
				}
				location.reload();
			});
	}
</script>