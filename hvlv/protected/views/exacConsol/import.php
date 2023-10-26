<style type="text/css">
.xlscol{
	width: 17%;
	float: left;
	margin: 0 10px 10px 0;
	border: 1px solid #999;
	min-height: 45px;
	padding: 5px;
}
.xlscol.assigned{
	border-color: #333;
	background: #9f9;
}
.mfld{
	border: 1px solid #333;
	padding: 5px;
	float: left;
	margin: 0 10px 10px 0;
	background: #eee;
	line-height: 20px;
}
.mfld.mand{
	background: #ffc;
}
</style>
<div class="form" style="min-height: 420px">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'import-form',
	'enableAjaxValidation'=>false,
	'action' => $this->createUrl('exacConsol/import', array('id' => $model->id)),
));
?>
	<div class="row s1">
		<label for="manifest">Upload Manifest - <small>.csv/.xls/.xlsx File</small></label>
		<input type="file" name="manifest" id="manifest" />
	</div>

	<div class="row s2">
	</div>

	<div id="result"></div>

	<div class="row buttons">
		<input id="maps" type="hidden" name="map" />
		<?php echo CHtml::submitButton($this->t('Submit')); ?> 
	</div>
<?php $this->endWidget(); ?>
</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	$('form#import-form', win).on('submit', function(){
		if($(".mfld", win).length > 0){
			if($(".mfld.mand", win).not('.used').length > 0){
				myApp.alert('Please make sure all mandatory fields are allocated!');
				return false;
			}
			var d = [];
			$(".mfld", win).each(function(){
				if($(this).data('t')) d.push($(this).data('fid')+':'+$(this).data('t').data('col'));
			});
			$('input#maps').val(d.join(','));
		}
	}).data({custom_success: function(r){
		if(r.err){
			$('#result', win).html('<h3>Errors:</h3><p class="red" style="font-weight:bold;">'+r.err+'</p>');
		}else{
			if(r.cols){
				$('form#import-form', win).resetForm();
				var h = '<p>Shipments: <b>'+r.rc+'</b></p><br />';
				for(var i in r.cols){
					h += '<div class="xlscol" data-col="'+i+'">'+i+':'+r.cols[i]+'</div>';
				}
				var cs = <?php echo json_encode($this->afsMaps());?>;
				h += '<div style="clear:both;height:15px;"></div><div class="pool" style="float:left">';
				for(i in cs){
					h += '<div class="mfld'+(cs[i][1]==1? ' mand' : '')+'" data-fid="'+i+'">'+cs[i][0]+'</div>';
				}
				h += '</div>';
				$('.s2', win).html(h);
				$('.s1', win).hide();
				$(".mfld", win).draggable({ revert: 'invalid' });
				$(".xlscol", win).droppable({
					accept: ".mfld",
					drop: function(evt, ui){
						ui.draggable.position( { of: $(this), my: 'right bottom', at: 'right bottom' } );
						ui.draggable.addClass('used');
						if(ui.draggable.data('t')) ui.draggable.data('t').removeClass('assigned');
						ui.draggable.data('t', $(this));
						$(this).addClass('assigned');
					}
				});
				$(".pool", win).droppable({
					accept: ".mfld",
					drop: function(evt, ui){
						ui.draggable.removeClass('used');
						if(ui.draggable.data('t')){
							ui.draggable.data('t').removeClass('assigned');
							ui.draggable.data('t', null);
						}
					}
				});
				var hmap = <?php echo empty($model->owner->extra['mtmap'])? '{}' : json_encode($model->owner->extra['mtmap']); ?>;
				var t;
				for(i in hmap){
					$('.mfld', win).each(function(){
						if($(this).data('fid') == i){
							t = $(this);
							return false;
						}
					});
					$('.xlscol', win).each(function(){
						if($(this).data('col') == hmap[i]){
							t.position( { of: $(this), my: 'right bottom', at: 'right bottom' } );
							t.addClass('used');
							t.data('t', $(this));
							$(this).addClass('assigned');
							return false;
						}
					});
				}
			}else{
				win.data('opener').trigger('update-exafs-grid');
				win.jqmHide();
			}
		}
		$('input[type=submit]', win).attr('disabled', false);
		return true;
	}});
});
</script>