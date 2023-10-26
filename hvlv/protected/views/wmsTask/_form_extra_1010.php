	<div class="row rowcol rowleft">
		<?php echo CHtml::checkbox('mdata[pi_cargo]', !empty($model->mdata['pi_cargo'])), $this->t(' <b>大货专用</b>'); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::checkbox('mdata[simple_in]', !empty($model->mdata['simple_in'])), $this->t(' <b>简单入库</b>'); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::checkbox('mdata[single_item]', !empty($model->mdata['single_item'])), $this->t(' <b>单件入库</b>'); ?>
	</div>

	<div class="row rowcol rowleft" id="div_cargo" style="border:1px solid grey; padding:10px;">
		<div class="row rowcol rowleft">
			<?php echo CHtml::checkbox('mdata[pi_stockin]', !empty($model->mdata['pi_stockin'])), $this->t(' <b>入库</b>'); ?>
		</div>
		<div class="row rowcol">
			<?php echo CHtml::checkbox('mdata[pi_photo]', !empty($model->mdata['pi_photo'])), $this->t(' <b>拍照</b>'); ?>
		</div>
		<div class="row rowcol">
			<?php echo CHtml::checkbox('mdata[pi_weight]', !empty($model->mdata['pi_weight'])), $this->t(' <b>提供重量</b>'); ?>
		</div>
		<div class="row rowcol">
			<?php echo CHtml::checkbox('mdata[pi_count]', !empty($model->mdata['pi_count'])), $this->t(' <b>需点数</b>'); ?>
		</div>
		<div class="row rowcol">
			<?php echo CHtml::checkbox('mdata[pi_batch]', !empty($model->mdata['pi_batch'])), $this->t(' <b>检查batch no</b>'); ?>
		</div>
		<div class="row rowcol">
			<?php echo CHtml::checkbox('mdata[pi_expiry]', !empty($model->mdata['pi_expiry'])), $this->t(' <b>检查有效期</b>'); ?>
		</div>
		<div class="row rowcol">
			<?php echo CHtml::checkbox('mdata[pi_split]', !empty($model->mdata['pi_split'])), $this->t(' <b>分仓(请上传相关文件)</b>'); ?>
		</div>
		<div class="row rowcol">
			<?php echo CHtml::checkbox('mdata[pi_airsea]', !empty($model->mdata['pi_airsea'])), $this->t(' <b>托盘标记空/海运</b>'); ?>
		</div>
		<div class="row rowcol rowleft">
			<?php echo CHtml::label('ETA', 'mdata[pi_eta]'); ?>
			<?php echo CHtml::textField('mdata[pi_eta]', @$model->mdata['pi_eta'], array('class' => 'datetime_input')); ?>
		</div>
		<div class="row rowcol">
			<?php echo CHtml::label('ETD', 'mdata[pi_etd]'); ?>
			<?php echo CHtml::textField('mdata[pi_etd]', @$model->mdata['pi_etd'], array('class' => 'datetime_input')); ?>
		</div>
		<div class="row rowcol">
			<?php echo CHtml::label('预期到达板数', 'mdata[pi_expect]'); ?>
			<?php echo CHtml::textField('mdata[pi_expect]', @$model->mdata['pi_expect']); ?>
		</div>
		<div class="row rowcol">
			<?php echo CHtml::label('Other requirement', 'mdata[pi_other]'); ?>
			<?php echo CHtml::textField('mdata[pi_other]', @$model->mdata['pi_other']); ?>
		</div>
		<br>
		<br>
		<hr>
		<br>
		<div class="row rowcol rowleft">
			<div class="row rowcol rowleft" style="width:200px;">
				<?php echo CHtml::textField('mdata[pi_count_number]', @$model->mdata['pi_count_number'], array('size' => 10, 'disabled' => 'disabled')), $this->t(' <b>数量</b>'); ?>
			</div>
			<div class="row rowcol" style="width:240px;">
				<?php echo CHtml::textField('mdata[pi_expiry_number]', @$model->mdata['pi_expiry_number'], array('size' => 10, 'disabled' => 'disabled')), $this->t(' <b>效期</b>'); ?>
			</div>
			<div class="row rowcol" style="width:240px;">
				<?php echo CHtml::textField('mdata[pi_batch_number]', @$model->mdata['pi_batch_number'], array('size' => 10, 'disabled' => 'disabled')), $this->t(' <b>Batch No.</b>'); ?>
			</div>
			<div class="row rowcol rowleft" style="width:200px;">
				<?php echo CHtml::textField('mdata[pi_wood]', @$model->mdata['pi_wood'], array('size' => 10, 'disabled' => 'disabled')), $this->t(' <b>木板数</b>'); ?>
			</div>
			<div class="row rowcol" style="width:240px;">
				<?php echo CHtml::textField('mdata[pi_ippc]', @$model->mdata['pi_ippc'], array('size' => 10, 'disabled' => 'disabled')), $this->t(' <b>IPPC熏蒸木板数</b>'); ?>
			</div>
			<div class="row rowcol" style="width:240px;">
				<?php echo CHtml::checkbox('mdata[pi_ippc_clear]', !empty($model->mdata['pi_ippc_clear']), array('disabled' => 'disabled')), $this->t(' <b>熏蒸标是否清晰</b>'); ?>
			</div>
			<div class="row rowcol rowleft" style="width:200px;">
				<?php echo CHtml::textField('mdata[pi_plastic]', @$model->mdata['pi_plastic'], array('size' => 10, 'disabled' => 'disabled')), $this->t(' <b>塑料板数</b>'); ?>
			</div>
			<div class="row rowcol" style="width:240px;">
				<?php echo CHtml::textField('mdata[pi_chep]', @$model->mdata['pi_chep'], array('size' => 10, 'disabled' => 'disabled')), $this->t(' <b>chep板数</b>'); ?>
			</div>
			<div class="row rowcol" style="width:240px;">
				<?php echo CHtml::textField('mdata[pi_loscam]', @$model->mdata['pi_loscam'], array('size' => 10, 'disabled' => 'disabled')), $this->t(' <b>loscam板数</b>'); ?>
			</div>
			<div class="row rowcol rowleft" style="width:200px;">
				<?php echo CHtml::textField('mdata[pi_weight_number]', @$model->mdata['pi_weight_number'], array('size' => 10, 'disabled' => 'disabled')), $this->t(' <b>总重量</b>'); ?>
			</div>
			<div class="row rowcol" style="width:240px;">
				<?php echo CHtml::textField('mdata[pi_volumn_number]', @$model->mdata['pi_volumn_number'], array('size' => 10, 'disabled' => 'disabled')), $this->t(' <b>总容量</b>'); ?>
			</div>
			<div class="row rowcol rowleft">
				<table>
					<thead>
						<tr>
							<th>PLT no</th>
							<th>Length</th>
							<th>Width</th>
							<th>Height</th>
							<th>Volumn</th>
							<th>Weight</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;
						while (!empty($model->mdata['length_'.$i]) || !empty($model->mdata['width_'.$i]) || !empty($model->mdata['height_'.$i]) || !empty($model->mdata['weight_'.$i])) {
							echo '<tr><td>' . $i . '</td><td>' . (!empty($model->mdata['length_'.$i]) ? $model->mdata['length_'.$i] : 0) . '</td><td>' . (!empty($model->mdata['width_'.$i]) ? $model->mdata['width_'.$i] : 0) . '</td><td>' . (!empty($model->mdata['height_'.$i]) ? $model->mdata['height_'.$i] : 0) . '</td><td>' . (!empty($model->mdata['volumn_'.$i]) ? $model->mdata['volumn_'.$i] : 0) . '</td><td>' . (!empty($model->mdata['weight_'.$i]) ? $model->mdata['weight_'.$i] : 0) . '</td></tr>';
							$i ++;
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="row rowcol">
			<?php echo CHtml::label('最新Note', 'lastest_note'); ?>
			<?php echo CHtml::textArea('latest_note', !empty(Log::getLast1($model, 6)) ? Log::getLast1($model, 6)->getUser() . ' (' . Log::getLast1($model, 6)->time . ') : ' . Log::getLast1($model, 6)->getExtra() : '', array('style' => 'width:400px; height:60px;')); ?>
		</div>
		<br>
	</div>

	<div class="row rowcol rowleft" id="div_simple" style="border: 1px solid grey; padding: 10px">
		<div class="row rowcol rowleft">
			<?php echo CHtml::label('ETA', 'mdata[si_eta]'); ?>
			<?php echo CHtml::textField('mdata[si_eta]', @$model->mdata['si_eta'], array('class' => 'datetime_input')); ?>
		</div>
		<div class="row rowcol">
			<?php echo CHtml::label('预期到达板数', 'mdata[si_expect]'); ?>
			<?php echo CHtml::textField('mdata[si_expect]', @$model->mdata['si_expect']); ?>
		</div>
	</div>