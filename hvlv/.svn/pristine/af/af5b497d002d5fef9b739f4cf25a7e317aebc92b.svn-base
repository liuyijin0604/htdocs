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
	        $title => $link_url,
		'New',
	),
));
?>

<h1>New Shipment</h1>
<div style="position:absolute; top:150px; right: 800px;"  ><span id="result-output"></span></div>
<?php echo $this->renderPartial($type.'_form', array('model'=>$model, 'org'=>$org)); ?>

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('#shipment-form').on('success', function(e,r){
            <?php if(!empty($_GET['man_id'])):?>
		   posApp.toPage('<?=$this->createUrl('manifest/manage',array('id'=>$_GET['man_id']))?>', false);
            <?php else:?>
                  posApp.toPage('<?=$this->createUrl('shipment/manage')?>', false);
            <?php endif;?>
	}).on('error',function(e,r){
//            bootbox.confirm("Would you want to print label now?",function(result){});
                        var msg = '<div>' + r.msg + '</div>';
                        var elm = $(msg);
                        $('#warning').html('');
                        $('#warning').prepend(elm.fadeIn());
                        $('#warning').show();
        });
});
</script>
<?php $this->registerJS(ob_get_clean(),2); ?>