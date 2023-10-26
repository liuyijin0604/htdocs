<h3>Operation-Cargo-Job-<?= $model->job_name ?></h3>
<h3><?= $model->status == 15 ? "Consol" : "Direct Consol" ?>
	<div class="form">
		<div class="row">
			<div class="col" style="margin-right: 25px">
				<div class="row rowcol-left">
					<?php echo CHtml::label('status', 'status'); ?>
					<?php echo CHtml::dropDownList('cargo_process_job_status', @$model->status, CargoProcessJob::$processTypes, array('prompt' => 'Select', 'disabled' => 'disabled')); ?>

					<div style="background-position:-240px -416px" class="icon"></div>
					<?php echo CHtml::button('Update', array('class' => 'updateStatus')); ?>
				</div>

			</div>
		</div>
		<br>
		<div class="form">
			<?php
			$id = $model->id;
			$url = $this->createUrl('topCourierService/updateJob');
			$form = $this->beginWidget(
				'CActiveForm',
				array(
					'id' => 'cargo-process-job-acr_form',
					'enableAjaxValidation' => false,
					'action' => $url . "?id=" . $id
				)
			);
			?>
			<?php if ($model->status == CargoProcessJob::STATE_NEW) : ?>
				<div class="row buttons">
					<?php echo CHtml::button('Confirm Job', array('class' => 'collectInfo_done')); ?>
				</div>
			<?php endif; ?>

			<?php if ($model->status == CargoProcessJob::STATE_WAITING_WAREHOUSE_PREPARATION) : ?>
				<div class="row buttons">
					<?php echo CHtml::button('Generate Pickup List', array('class' => 'gate_pass')); ?>
				</div>

				<div class="row buttons">
					<?php echo CHtml::button('Warehouse Preparation Done', array('class' => 'job_warehouse_preparation_done')); ?>
				</div>
			<?php endif; ?>

			<?php
			if (!empty($model->gate_pass_id)) {
				echo CHtml::link('Check Pickup List', $this->createUrl("topCourierService/printGatePass") . "?id=" . $model->gate_pass_id, ["target" => "_blank"]);
			}
			?>
			<div class="row buttons">
				<?php if ($model->status >= CargoProcessJob::STATE_WAITING_FOR_DELIVERY && $model->type == CargoProcessJob::FBA_JOB) : ?>
					<div class="row buttons">
						<?php
						$fr = new FileRepo('search');
						$fr->unsetAttributes();
						$fr->theTypes = [150, 151];
						$fr->fid = $model->id;
						$mf = Acl::hasAccess('B:org/manageFile');
						$this->widget('zii.widgets.grid.CGridView', [
							'id' => $_GET['tabid'] . '_excofile-grid',
							'cssFile' => false,
							'summaryText' => '',
							'dataProvider' => $fr->search(),
							'filter' => $fr,
							'columns' => [
								['name' => 'name', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->baseUrl."/filerepo/".$data->hash."/".$data->name."\" target=\"_blank\">".$data->name."</a>"'],
								[
									'name' => 'size',
									'value' => '$data->formatSize()',
									'filter' => false,
								],
								'date',
								'type',
								[
									'name' => 'status', 'type' => 'raw', 'value' => '$data->getStatus()',
									'filter' => CHtml::dropDownList('FileRepo[status]', $fr->status, FileRepo::$states, ['prompt' => $this->t('All')])
								],
								[
									'class' => 'oButtonColumn',
									'template' => '{update}',
									'buttons' => [
										'update' => [
											'url' => 'Yii::app()->createUrl("imParcel/fileUpdate",array("id"=>$data->id))',
											'imageUrl' => false,
											'visible' => 'true',
											'options' => ['class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Update'), 'title' => '$data->name'],
										],
									],
								],
							],
						]);
						?>
					</div>

					<?php

					$pphash = "";
					echo '<br /><h3>', CHtml::label($this->t('Upload POD Files'), '</h3>uploader');
					$uploadType = FileRepo::CARGOPROCESSPOD;
					$pphash = FileRepo::uploadHash($model, $uploadType);

					$this->widget('application.extensions.plupload.PluploadWidget', [
						'config' => [
							'url' => $this->createUrl('topCourierService/uploadPODCargoJob/') . '?hash=' . $pphash,
							'max_file_size' => Yii::app()->params['maxFileSize'],
							'unique_names' => true,
							'file_list_height' => 60,
							'visible_header' => false,
							'filters' => [
								['title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg,png,txt'],
							],
							//'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
							'language' => Yii::app()->language,
							'max_file_number' => 2,
							'autostart' => false,
							'jquery_ui' => false,
							'reset_after_upload' => true,
						],
						'callbacks' => [
							'FileUploaded' => 'function(up,file,response){alert("Upload POD File Success");$("#' . $_GET["tabid"] . '").trigger("reload_excofile_grid");$("#' . $_GET["tabid"] . '").trigger("reload_excofile_grid_file");}',
						],
						'id' => $_GET['tabid'] . '_excofile_uploader_1',
					]);

					$pphash = "";
					echo '<br /><h3>', CHtml::label($this->t('Upload Signature Files'), '</h3>uploader');
					$uploadType = FileRepo::CARGOPROCESSFILETYPE;
					$pphash = FileRepo::uploadHash($model, $uploadType);

					$this->widget('application.extensions.plupload.PluploadWidget', [
						'config' => [
							'url' => $this->createUrl('topCourierService/uploadSignatureFileForCargoJob/') . '?hash=' . $pphash,
							'max_file_size' => Yii::app()->params['maxFileSize'],
							'unique_names' => true,
							'file_list_height' => 60,
							'visible_header' => false,
							'filters' => [
								['title' => Yii::t('app', 'JPG, PDF, Word, Excel files'), 'extensions' => '	pdf,doc,docx,xls,xlsx,jpg,png,txt'],
							],
							//'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
							'language' => Yii::app()->language,
							'max_file_number' => 2,
							'autostart' => false,
							'jquery_ui' => false,
							'reset_after_upload' => true,
						],
						'callbacks' => [
							'FileUploaded' => 'function(up,file,response){
							alert("Upload POD File Success");
							$("#' . $_GET["tabid"] . '	").trigger("reload_excofile_grid");
							$("#' . $_GET["tabid"] . '	").trigger("reload_excofile_grid_file");
						}',
						],
						'id' => $_GET['tabid'] . '_excofile_uploader_2',
					]);
					?>
			</div>
		<?php endif; ?>


		<?php if ($model->status == CargoProcessJob::STATE_WAITING_FOR_DELIVERY) : ?>
			<div class="row buttons">
				<?php echo CHtml::button('Job Process Done', array('class' => 'job_process_done')); ?>
			</div>
		<?php endif; ?>

		<?php if ($model->dpt_id == 106 || $model->dpt_id == 218) { ?>
			<div class="row buttons">
				<?php echo CHtml::button('Post to Tony', array('class' => 'post_tony')); ?>
			</div>
		<?php } ?>

		<?php if ($model->type == CargoProcessJob::FBA_JOB || $model->type == CargoProcessJob::B2B_JOB) : ?>
			<div class="row">
				<?php echo CHtml::label('Total Job Pallet No', 'Job Pallet No'); ?>
				<?php echo CHtml::numberField('total_job_pallet_no', !empty($model->mdata['total_job_pallet_no']) ? $model->mdata['total_job_pallet_no'] : $model->getTotalPltNo()); ?>
			</div>
			<div class="row">
				<?php echo CHtml::label('Cost For C-job', 'Cost For C-job'); ?>
				<?php echo CHtml::numberField('total_job_input_cost', !empty($model->mdata['total_job_input_cost']) ? $model->mdata['total_job_input_cost'] : "0"); ?>
			</div>
		<?php endif; ?>
		<?php if ($model->type == CargoProcessJob::INTERSTATE_JOB) : ?>
			<!-- <div class="row">
			<?php //echo CHtml::label('Third Part Label No.', 'Third Part Courier Label No.'); 
			?>
			<?php //echo CHtml::textField('third_part_label_no', !empty($model->mdata['third_part_label_no'])?$model->mdata['third_part_label_no']:"");
			?>
	</div> -->
			<h3>Interstate Cost</h3>
			<div class="row">
				<?php echo CHtml::label('Interstate Job Cost', 'interstate_job_cost'); ?>
				<?php echo CHtml::numberField('interstate_job_cost', !empty($model->mdata['interstate_cost']) ? $model->mdata['interstate_cost'] : 0); ?>
			</div>
			<h3>Interstate Pallets Detail</h3>
			<div class="row">
				<?php echo CHtml::label('P1', 'P1'); 
				?>
				<?php echo CHtml::numberField('num_P1', !empty($model->mdata['num_P1'])?$model->mdata['num_P1']:"0",['class'=>'numPallet']);
				?>
				<?php echo CHtml::label('P2', 'P2'); 
				?>
				<?php echo CHtml::numberField('num_P2', !empty($model->mdata['num_P2'])?$model->mdata['num_P2']:"0",['class'=>'numPallet']);
				?>
				<?php echo CHtml::label('14P', '14P'); 
				?>
				<?php echo CHtml::numberField('num_14P', !empty($model->mdata['num_14P'])?$model->mdata['num_14P']:"0",['class'=>'numPallet']);
				?>
				<?php echo CHtml::label('SEMI', 'SEMI'); 
				?>
				<?php echo CHtml::numberField('num_SEMI', !empty($model->mdata['num_SEMI'])?$model->mdata['num_SEMI']:"0",['class'=>'numPallet']);
				?>
				<?php echo CHtml::label('BW', 'BW'); 
				?>
				<?php echo CHtml::numberField('num_BW', !empty($model->mdata['num_BW'])?$model->mdata['num_B-W']:"0",['class'=>'numPallet']);
				?>
			<h3 id="totalsum"></h3>
			<br />
			</div>
		<?php endif; ?>
		<?php echo CHtml::submitButton('Save', array('class' => 'update')); ?>


		<div class="row buttons">
			<div class="row">
				<?php echo CHtml::label('Description', 'Description'); ?>
				<?php echo CHtml::textArea('description', @$model->description, array('rows' => 2, 'cols' => 60)); ?>
			</div>
		</div>

		<script type="text/javascript">
			$(function() {
				var win = $('#jqmw_<?= $_GET["tabid"]; ?>');
				var tab = $('#<?= $_GET["tabid"]; ?>');
				var panel = $('#<?= $_GET["tabid"]; ?>').data('panel');

				tab.unbind('reload_cargo_process_job_grid').bind('reload_cargo_process_job_grid', function() {
					$('#<?= $_GET["tabid"]; ?>_cargo_process_job_grid', tab.data('panel')).yiiGridView('update');
					return false;
				});

				tab.unbind('reload_excofile_grid').bind('reload_excofile_grid', function() {
					$('#<?= $_GET["tabid"]; ?>_excofile-grid', win).yiiGridView('update');
					return false;
				});
				tab.data('panel').off('change', 'select.pfile_status').on('change', 'select.pfile_status', function() {
					$.post('files/status', {
						'id': $(this).data('id'),
						'status': $(this).val()
					});
				});
				win.unbind('reload_cargo_invoice_grid').bind('reload_cargo_invoice_grid', function() {
					$('#cargo_invoice_grid_<?= $_GET['tabid'] ?>', win).yiiGridView('update');
				});

				$('.collectInfo_done', win).on('click', function() {
					if (confirm('Are you sure to confirm this Job？')) {
						$.get('<?= $this->createUrl("topCourierService/confirmJob") . "?id=" . $id ?>', function(r) {
							if (r == 'done') {
								save();
							} else {
								myApp.alert(r, false);
							}
							tab.trigger('reload_cargo_process_job_grid');
						});

					}
				});

				$('.send_outturn', win).on('click', function() {
					if (confirm('Are you sure to send outturn Done')) {
						$.get('<?= $this->createUrl("cargoProcess/collectInfoDone") . "?id=" . $id . "&outturn=1" ?>', function(r) {
							if (r == 'done') {
								save();
							} else {
								myApp.alert(r, false);
							}
							tab.trigger('reload_cargo_process_job_grid');
						});

					}
				});

				$('.send_do', win).on('click', function() {
					if (confirm('Are you sure to send outturn Done')) {
						$.get('<?= $this->createUrl("cargoProcess/sendDo") . "?id=" . $id ?>', function(r) {
							if (r == 'done') {
								save();
							} else {
								myApp.alert(r, false);
							}
							tab.trigger('reload_cargo_process_job_grid');
						});

					}
				});

				$('.post_tony', win).on('click', function() {
					if (confirm('Are you sure to post C-Job to Tony?')) {
						$.get('<?= $this->createUrl("topCourierService/sendToTony") . "?id=" . $id ?>', function(r) {
							if (r == 'done') {
								save();
							} else {
								myApp.alert(r, false);
							}
							tab.trigger('reload_cargo_process_job_grid');
						});

					}
				});

				$('.send_sms_email', win).on('click', function() {
					if (confirm('Are you sure to send sms and email?')) {
						$.get('<?= $this->createUrl("cargoProcess/sendNormalCargoSmsEmail") . "?id=" . $id ?>', function(r) {
							if (r == 'done') {
								save();
							} else {
								myApp.alert(r, false);
							}
							tab.trigger('reload_cargo_process_job_grid');
						});

					}
				});


				$('.add_preparetion_tracking', win).on('click', function() {
					if (confirm('Are you sure to add preparetion tracking?')) {
						$.get('<?= $this->createUrl("cargoProcess/addPreparetionTracking") . "?id=" . $id ?>', function(r) {
							if (r == 'done') {
								save();
							} else {
								myApp.alert(r, false);
							}
							tab.trigger('reload_cargo_process_job_grid');
						});

					}
				});

				$('#cargo_process_job_status', win).next().on('dblclick', function() {
					if (window.confirm('Are you sure to override status?')) {
						$(this).prev().attr('disabled', false);
					}
				});

				$('.assign_driver_done', win).on('click', function() {
					if (confirm('Are you sure to New Done')) {
						$.get('<?= $this->createUrl("topCourierService/jobNewDone") . "?id=" . $id ?>', function(r) {
							if (r == 'done') {
								save();
							} else {
								myApp.alert(r, false);
							}
							tab.trigger('reload_cargo_process_job_grid');
						});

					}
				});

				$('.gate_pass', win).on('click', function() {
					if (confirm('Are you sure to Generate Gate Pass?')) {
						$.get('<?= $this->createUrl("gatepass/generateCargoProcessGatePassByJob") . "?id=" . $id ?>', function(r) {
							r = JSON.parse(r);
							if (r.done == true) {
								window.open("<?= $this->createUrl("topCourierService/printGatePass") ?>" + "?id=" + r.id, "_blank");
							} else {
								myApp.alert(r, false);
							}
						});

					}
				});

				$('.job_warehouse_preparation_done', win).on('click', function() {
					if (confirm('Are you sure to finish warehouse preparetion for this job and all related cargos?')) {
						$.get('<?= $this->createUrl("topCourierService/jobWarehousePreparationDone") . "?id=" . $id ?>', function(r) {
							if (r == 'done') {
								save();
								tab.trigger('reload_cargo_process_job_grid');
							} else {
								myApp.alert(r, false);
							}
						});

					}
				});

				$('.job_process_done', win).on('click', function() {
					if (confirm('Are you sure to job process done?')) {
						$.get('<?= $this->createUrl("topCourierService/jobProcessDone") . "?id=" . $id ?>', function(r) {
							if (r == 'done') {
								save();
								tab.trigger('reload_cargo_process_job_grid');
							} else {
								myApp.alert(r, false);
							}
						});

					}
				});

				$('.gate_pass_prepare', win).on('click', function() {
					if (confirm('Are you sure to notice the warehouse to prepare?')) {
						$.get('<?= $this->createUrl("topCourierService/noticeWarehouseToPrepareByJob") . "?id=" . $id ?>', function(r) {
							if (r == "done") {
								myApp.notice('Done', 5000);
							} else {
								myApp.alert(r, false);
							}
						});
					}
				});

				$('.paperwork_done', win).on('click', function() {
					if (confirm('Are you sure to paperwork done?')) {
						$.get('<?= $this->createUrl("cargoProcess/paperworkDone") . "?id=" . $id ?>', function(r) {
							if (r == 'done') {
								myApp.notice('Done', 5000);
							} else {
								myApp.alert(r, false);
							}
							tab.trigger('reload_cargo_process_job_grid');
						});

					}
				});

				$('.generate_cargo_receipt', win).on('click', function() {
					if (confirm('Are you sure to generate cargo receipt')) {
						window.open("<?= $this->createUrl("topCourierService/generateCargoReceiptByJob") . "?id=" . $id ?>", "_blank");
					};
				});


				$('.update', win).on('click', function() {
					if (confirm('Are you sure to save?')) {
						var listData = new FormData();
						listData.append('numpallet', $('#total_job_pallet_no', win).val());
						listData.append('costforCjob', $('#total_job_input_cost', win).val());
						listData.append('num_P1', $('#num_P1', win).val());
						listData.append('num_P2', $('#num_P2', win).val());
						listData.append('num_14P', $('#num_14P', win).val());
						listData.append('num_SEMI', $('#num_SEMI', win).val());
						listData.append('num_BW', $('#num_BW', win).val());
						listData.append('interstate_cost', $('#interstate_job_cost', win).val());
						//listData.append('thirdpartlabel', $('#third_part_label_no',win).val());
						listData.append('id', <?= $id ?>);
						htmlobj = $.ajax({
							url: '<?= $this->createUrl("topCourierService/updateJobPallet") ?>',
							type: "post",
							data: listData,
							async: false,
							contentType: false,
							processData: false,
						});
						obj = JSON.parse(htmlobj.responseText);
						//alert(obj.isSuccess);
						if (obj.isSuccess) {
							myApp.notice('Successful', 5000);
						} else {
							myApp.notice('Error', 5000);
						}
					}
					return false;
				});

				function save() {
					myApp.notice('Done', 5000);
					return;
					var form = new FormData(document.getElementById("cargo-process-job-acr_form"));
					$.ajax({
						url: '<?= $url . "?id=" . $id ?>',
						type: "post",
						data: form,
						processData: false,
						contentType: false,
						success: function(r) {
							if (r == 'done') {
								myApp.notice('Done', 5000);
							} else {
								myApp.alert(r, false);
							}
							tab.trigger('reload_cargo_process_job_grid');
						},
						error: function(e) {
							console.log(e);
						}
					});
				}

				$('.updateStatus', win).on('click', function() {
					if (confirm('Are you sure to update status?')) {
						var form = new FormData(document.getElementById("cargo-process-job-acr_form"));
						$.ajax({
							url: '<?= $this->createUrl("topCourierService/updateJobStatus") . "?id=" . $id ?>' + '&&status=' + $('#cargo_process_job_status', win).val(),
							type: "post",
							data: form,
							processData: false,
							contentType: false,
							success: function(r) {
								if (r == 'done') {
									myApp.notice('Done', 5000);
								} else {
									myApp.alert(r, false);
								}
								tab.trigger('reload_cargo_process_job_grid');
							},
							error: function(e) {
								console.log(e);
							}
						});
					}
					return false;
				});

				$('.numPallet',win).change(function() {
					var sum = 0;
					var num_P1 = Number($('#num_P1', win).val());
					var num_P2 = Number($('#num_P2', win).val());
					var num_14P = Number($('#num_14P', win).val());
					var num_SEMI = Number($('#num_SEMI', win).val());
					var num_BW = Number($('#num_BW', win).val());
					sum = num_P1*1+num_P2*1+num_14P*1+num_SEMI*1+num_BW*1;
					//alert(sum);
					$('#totalsum', win).html("Total: "+sum+" Pallets");
				});

			});
		</script>


		<?php $this->endWidget(); ?>