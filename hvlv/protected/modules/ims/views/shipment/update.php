<?php
if(!empty($_GET['man_id'])){
    $link_url=array('manifest/manage','id'=>$_GET['man_id']);
    $title='Manifest-'.$_GET['man_id'];
}else{
    $title='Shipment';
    $link_url=array('shipment/manage');
}
$this->widget('zii.widgets.CBreadcrumbs', array(
	'links' => array(
		$title =>$link_url,
		'Update  - '.$model->hbn,
	),
));
?>

<h1>Update Shipment - <?=$model->hbn;?></h1>
<?php echo $this->renderPartial($type.'_form', array('model'=>$model, 'org' => $org)); ?>
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('#shipment-form').on('success', function(e,r){
		    <?php if(!empty($_GET['man_id'])):?>
		   posApp.toPage('<?=$this->createUrl('manifest/manage',array('id'=>$_GET['man_id']))?>', false);
            <?php else:?>
                  posApp.toPage('<?=$this->createUrl('shipment/manage')?>', false);
            <?php endif;?>
	});
});
</script>
<?php $this->registerJS(ob_get_clean(),2); ?>