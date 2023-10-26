<h1><?=$this->t('My Shipments');?></h1>
<table class="table table-striped">
<thead><tr><th><?=$this->t('Connote#');?></th><th><?=$this->t('Status');?></th><th>&nbsp;</th></tr></thead>
<tbody id="slist">
</tbody>
</table>
<!-- Tracking Modal -->
<div class="modal fade" id="modal-tracking" tabindex="-1" role="dialog" aria-labelledby="modal-tracking-label" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-body">
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?=$this->t('Close');?></button>
	  </div>
	</div>
  </div>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	var rpms = Rhaboo.persistent("myshipments");
	if(rpms.hasOwnProperty('Connotes')){
		var q = {};
		for(var i = 0; i < rpms.Connotes.length; i++){
			c = rpms.Connotes[i];
			$('#slist').append('<tr id="s'+c.id+'"><td>'+c.hbn+'</td><td class="st">&nbsp;</td><td><a href="<?=Yii::app()->createUrl("pos/client/tracking");?>?c='+c.hbn+'" class="tracking-modal-link"><span class="glyphicon glyphicon-search"></span></a></td></tr>');
			q[c.id] = c.hbn;
		}
		$.post('<?=$this->createUrl("client/status");?>', {s: q}, function(r){
			for(var i in r){
				$('#s'+i+' .st').text(r[i]);
			}
		}, 'json');
	}

	$('body').off('click', 'a.tracking-modal-link').on('click', 'a.tracking-modal-link', function(e){
		$('#modal-tracking').modal();
		$('#modal-tracking .modal-body').load($(this).attr('href'));
		e.preventDefault();
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>