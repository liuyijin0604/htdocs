<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'links' => array(
		'Tools',
	),
));
?>
<h2><?=$this->t('Upload Shipments');?> &nbsp;
<button type="button" id="history_btn" data-toggle="modal" data-target="#modal-history" class="btn btn-default"><span class="glyphicon glyphicon-time"></span> History</button></h2>
<div class="form">
<?php
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'manifest-form',
	'action' => $this->createUrl('tools/manifest'),
	'enableAjaxValidation'=>false,
	'htmlOptions' =>[
		'data-bit' => '3',
	]
)); ?>
	<div class="form-group">
		<label for="manifest">Data File - <small>.csv/.xls/.xlsx File</small> (<a href="/PCA_Express_Export_sample.xlsx" target="_blank">Get template file</a>)</label>
		<input type="file" name="manifest" id="manifest" />
	</div>
	<div id="result"></div>
	<div class="form-group buttons">
		<button class="btn btn-primary btn-lg" id="upload_btn" type="submit"><?=$this->t('Upload');?></button>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<!-- History Modal -->
<div class="modal fade" id="modal-history" tabindex="-1" role="dialog" aria-labelledby="modal-history-label" aria-hidden="true">
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
<?php if(!Yii::app()->user->isGuest && Yii::app()->user->grp == 80): ?>
<a href="products.html" class="btn btn-default"><span class="glyphicon glyphicon-tag"></span> Products List</a> &nbsp; 
<?php endif; ?>
<a href="bulkID.html" class="btn btn-default"><span class="glyphicon glyphicon-cloud-upload"></span> Bulk ID Upload</a>
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('#manifest-form').on('success', function(e,r){
		var rdiv = $('#result');
		rdiv.empty();
		if(r.done == true){
			$('form#manifest-form').resetForm();
			$('#notifc').notify({message: {text: r.msg}}).show();
		}else if(r.vdt == true){
			if(!$.isEmptyObject(r.err)){
				p = '<p style="font-weight:bold; color: #c00">Error:<br />';
				for(i in r.err){
					p += 'Line '+i+': '+r.err[i]+'<br />';
				}
				rdiv.append(p+'Please fix errors and upload again.</p>');
			}else{
				rdiv.append('<input type="hidden" name="cfm" value="1" />');
				$('#upload_btn').text('Confirm'); }
			rdiv.append('<h4>Total Shipments: '+r.tp+' &nbsp; Total Weight: '+r.tw+'kg</h4>');
			if(!$.isEmptyObject(r.duty)){
				tbl = '<table class="items table table-striped"><thead><tr><th>Line#</th><th>HBN</th><th>Detail</th><th>Weight</th><th>Duty</th><th><label><input type="checkbox" class="cbta" /> Exclude</label></th></tr></thead><tbody>';				
				for(i in r.duty){
					tbl += '<tr class="'+(i%2==0? 'even' : 'odd')+'"><td>'+i+'</td><td>'+r.duty[i][0]+'</td><td>'+r.duty[i][1]+'</td><td>'+r.duty[i][2]+'</td><td>'+r.duty[i][3]+'</td><td><input type="checkbox" class="excb" name="exclude[]" value="'+i+'" /></td></tr>';
				}
				rdiv.append(tbl+'</tbody></table');
			}
		}else{
			rdiv.append('<h4>Errors:</h4><p class="red" style="font-weight:bold;">'+r.msg+'</p>');
		}
		if(r.warns && r.warns.length > 0){
			rdiv.append('<h4>Warns:</h4><p class="warn">'+r.warns.join('<br />')+'</p>');
		}
		posApp.btnLoading($('button[type=submit]', this), true);
	}).on('submit', function(){
		if($('#manifest').val() == ''){
			alert('Please select a file');
			return false;
		}
	});
	$('#history_btn').on('click', function(){
		$('#modal-history .modal-body').load('<?=$this->createUrl("tools/maniHistory");?>');
	});
	$('body').on('click', 'input.cbta', function(){
		$('input.excb').prop('checked', $(this).prop('checked'));
	});
});
</script>
<?php $this->registerJS(ob_get_clean(),8); ?>
