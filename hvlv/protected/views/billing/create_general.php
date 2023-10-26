<?php if ( $model->isNewRecord ) : ?>
    <h1><?=$this->t('Create General Cost');?></h1>
<?php else : ?>
    <h1><?=$this->t('Update General Cost');?></h1>
<?php endif; ?>
<?php echo $this->renderPartial('_general_form', array('model'=>$model)); ?>

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	$('#general-cost-grid .add_btn').on('click', function(){
		$('#general-cost-grid .items tbody td.empty').parent().remove();
		var r = $(this).parents('tr').clone();
		$('.add_btn', r).remove();
		$('input', r).each(function(){
			var n = $(this).attr('name');
			$(this).attr('name', n+'[]');
		});
        $('select', r).each(function(){
            var n = $(this).attr('name');
            $(this).attr('name', n+'[]');
        });
		$('#general-cost-grid .items tbody').append(r);
		$(this).parents('tr').find('input').val('');
		return false;
	});
});
</script>