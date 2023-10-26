<div class="pane">
<?php
$fr = new FileRepo('search');
$fr->unsetAttributes();
$fr->status = 20;
$fr->type = 92;
$fr->fid = $model->id;
$data = $fr->search()->getData();
?>

<br />
<div class="container">
	<div class="table-responsive">
		<table id="files" class="table table-striped table-bordered">
			<thead>
				<tr><th>Name</th><th>Size</th><th>Date</th></tr>
			</thead>
			<tbody>
				<?php
				$i = 0;
				foreach ($data as $file) {
					echo '<tr class="' . ($i++ % 2 == 0 ? 'even' : 'odd') . '"><td>' . '<a href="' . Yii::app()->baseUrl . '/filerepo/' . $file->hash . '/' . $file->name . '" target="_blank">' . $file->name . '</a>' . '</td><td>' . $file->formatSize() . '</td><td>' . $file->date . '</td><td class="button-columns"><a class="delete_btn" target="_blank" tilte="Delete" href="' . Yii::app()->createUrl('cart/cartage/delete', ['id' => $file->id]) . '">Delete</a></td></tr>';
				}
				if (empty($data)) {
					echo '<tr class="' . ($i++ % 2 == 0 ? 'even' : 'odd') . '"><td colspan="3">No files</td><td></td></tr>';
				}
				?>
			</tbody>
		</table>
	</div>
</div>

<?php
echo '<br />', CHtml::label($this->t('Upload Files'), 'uploader');
$pphash = FileRepo::uploadHash($model, 92);
$this->widget('application.extensions.plupload.PluploadWidget', array(
	'config' => array(
		'url' => $this->createUrl('cartage/upload', ['hash' => $pphash]),
		'max_file_size' => Yii::app()->params['maxFileSize'],
		'unique_names' => true,
		'file_list_height' => 300,
		'visible_header' => false,
		'filters' => array(
			array('title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg'),
		),
		'language' => Yii::app()->language,
		'max_file_number' => 15,
		'autostart' => true,
		'jquery_ui' => false,
		'reset_after_upload' => true,
	),
	'callbacks' => array(
		'FileUploaded' => 'function(up, file, response) { $("#prodphoto_uploader").trigger("reload"); }',
	),
	'id' => 'prodphoto_uploader',
));
?>
</div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {
	$('#prodphoto_uploader').on('reload', function() {
		$.get('<?=$this->createURL("cartage/uploadGrid", ["id" => $model->id]);?>', function(r) {
			r = JSON.parse(r);
			$('#files tbody').html(r['data']);
		});
	});

	$('#files').on('click', 'a.delete_btn', function() {
		$.get($(this).attr('href'), function() {
			$.get('<?=$this->createURL("cartage/uploadGrid", ["id" => $model->id]);?>', function(r) {
				r = JSON.parse(r);
				$('#files tbody').html(r['data']);
			});
		});
		return false;
	});
});
</script>
<?php $this->registerJS(ob_get_clean(),8); ?>