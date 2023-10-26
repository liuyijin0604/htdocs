<?php
$fr = new FileRepo('search');
$fr->unsetAttributes();
$fr->status = 20;
$fr->type = 84;
$fr->fid = $model->mainTask->id;
$data = $fr->search()->getData();
?>
<div class="container">
	<div class="table-responsive">
		<table id="files" class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Name</th>
					<th>Size</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($data)) {
					foreach ($data as $i => $file) {
						echo '<tr class="' . ($i % 2 == 0 ? 'even' : 'odd') . '"><td>' . '<a href="' . Yii::app()->baseUrl . '/filerepo/' . $file->hash . '/' . $file->name . '" target="_blank">' . $file->name . '</a>' . '</td><td>' . $file->formatSize() . '</td><td>' . $file->date . '</td></tr>';
					}
				} ?>
			</tbody>
		</table>
	</div>
	<div class="form">
		<?php $form = $this->beginWidget('CActiveForm', array(
			'id' => 'wms-task211-file-form',
			'action' => $this->createUrl('task/upLoadFile', array('id' => $model->id)),
			'enableAjaxValidation' => false,
			'htmlOptions' => array(
				'enctype' => 'multipart/form-data'
			)
		));

		?>
		<div class="form-group">
			<?php
			echo '<br />', CHtml::label($this->t('Upload Files (hold CTRL to select multiple files)'), 'uploader');
			//$pphash = FileRepo::uploadHash($model, 84);

			?>
			<input type='file' name='files[]' multiple /></input>
			<!-- <input type="file" name="pictures" id="pictures"  multiple="multiple" ></input> -->
		</div>

		</br>
		<div class="form-group">
			<?php if ($model->mainTask->status < 30 || $model->mainTask->status == 40) { ?>
				<?php echo CHtml::submitButton($this->t('Save'), array('class' => 'btn btn-primary','onclick' => 'window.location.reload()')); ?>
			<?php } ?>
		</div>
	</div>
	<?php $this->endWidget(); ?>
	<br />
</div><!-- form -->