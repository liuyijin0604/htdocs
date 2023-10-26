<h1><?= $this->t($model->name . ' Oversize Fee'); ?></h1>


<div class="form" style="position:relative">

	<?php
	// get zone rate based on org id
	$zoneRate = new ZoneRate();
	?>

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'org-zone-rate-form',
		'enableAjaxValidation' => false,
	));
	?>
	<div id='tld_surcharge'>
		<div class="row">
			<div class="row rowcol rowcol-left divBorder">
				<div class="row" style="padding-left: 40px;">
					<div class="row rowcol rowcol-left" style="margin-top: 20px;">
						<?php echo CHtml::label('OS0', 'OS0') ?>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS0][kgdwf]', isset($model->mdata['truck_delivery']['OS0']['kgdwf']) ? $model->mdata['truck_delivery']['OS0']['kgdwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS0][kgdwt]', isset($model->mdata['truck_delivery']['OS0']['kgdwt']) ? $model->mdata['truck_delivery']['OS0']['kgdwt'] : 0, array('style' => 'width:30px')), 'KG Dead Weight' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS0][kgcwf]', isset($model->mdata['truck_delivery']['OS0']['kgcwf']) ? $model->mdata['truck_delivery']['OS0']['kgcwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS0][kgcwt]', isset($model->mdata['truck_delivery']['OS0']['kgcwt']) ? $model->mdata['truck_delivery']['OS0']['kgcwt'] : 0, array('style' => 'width:30px')), 'KG Cubic Weight' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS0][meterf]', isset($model->mdata['truck_delivery']['OS0']['meterf']) ? $model->mdata['truck_delivery']['OS0']['meterf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS0][metert]', isset($model->mdata['truck_delivery']['OS0']['metert']) ? $model->mdata['truck_delivery']['OS0']['metert'] : 0, array('style' => 'width:30px')), 'M Diagonal Length' ?>

						</p>

						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS0][length]', isset($model->mdata['truck_delivery']['OS0']['length']) ? $model->mdata['truck_delivery']['OS0']['length'] : 0, array('style' => 'width:30px')), 'M length' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS0][width]', isset($model->mdata['truck_delivery']['OS0']['width']) ? $model->mdata['truck_delivery']['OS0']['width'] : 0, array('style' => 'width:30px')), 'M width' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS0][height]', isset($model->mdata['truck_delivery']['OS0']['height']) ? $model->mdata['truck_delivery']['OS0']['height'] : 0, array('style' => 'width:30px')), 'M height' ?>

						</p>

						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS0][cbm]', isset($model->mdata['truck_delivery']['OS0']['cbm']) ? $model->mdata['truck_delivery']['OS0']['cbm'] : 0, array('style' => 'width:30px')), 'cbm' ?>
						</p>
						<p>_________________________________________</p>
						<p>
							<?php echo '$', CHtml::textField('mdata[truck_delivery][OS0][p_piece]', isset($model->mdata['truck_delivery']['OS0']['p_piece']) ? $model->mdata['truck_delivery']['OS0']['p_piece'] : 0, array('style' => 'width:30px')), '/piece or ', CHtml::textField('mdata[truck_delivery][OS0][p_shipment]', isset($model->mdata['truck_delivery']['OS0']['p_shipment']) ? $model->mdata['truck_delivery']['OS0']['p_shipment'] : 0, array('style' => 'width:30px')), '/shipment' ?>

						</p>

					</div>
					<div class="row rowcol rowcol-left" style="margin-top: 20px;">
						<?php echo CHtml::label('OS1', 'OS1') ?>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS1][kgdwf]', isset($model->mdata['truck_delivery']['OS1']['kgdwf']) ? $model->mdata['truck_delivery']['OS1']['kgdwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS1][kgdwt]', isset($model->mdata['truck_delivery']['OS1']['kgdwt']) ? $model->mdata['truck_delivery']['OS1']['kgdwt'] : 0, array('style' => 'width:30px')), 'KG Dead Weight' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS1][kgcwf]', isset($model->mdata['truck_delivery']['OS1']['kgcwf']) ? $model->mdata['truck_delivery']['OS1']['kgcwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS1][kgcwt]', isset($model->mdata['truck_delivery']['OS1']['kgcwt']) ? $model->mdata['truck_delivery']['OS1']['kgcwt'] : 0, array('style' => 'width:30px')), 'KG Cubic Weight' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS1][meterf]', isset($model->mdata['truck_delivery']['OS1']['meterf']) ? $model->mdata['truck_delivery']['OS1']['meterf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS1][metert]', isset($model->mdata['truck_delivery']['OS1']['metert']) ? $model->mdata['truck_delivery']['OS1']['metert'] : 0, array('style' => 'width:30px')), 'M Diagonal Length' ?>

						</p>

						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS1][length]', isset($model->mdata['truck_delivery']['OS1']['length']) ? $model->mdata['truck_delivery']['OS1']['length'] : 0, array('style' => 'width:30px')), 'M length' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS1][width]', isset($model->mdata['truck_delivery']['OS1']['width']) ? $model->mdata['truck_delivery']['OS1']['width'] : 0, array('style' => 'width:30px')), 'M width' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS1][height]', isset($model->mdata['truck_delivery']['OS1']['height']) ? $model->mdata['truck_delivery']['OS1']['height'] : 0, array('style' => 'width:30px')), 'M height' ?>

						</p>

						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS1][cbm]', isset($model->mdata['truck_delivery']['OS1']['cbm']) ? $model->mdata['truck_delivery']['OS1']['cbm'] : 0, array('style' => 'width:30px')), 'cbm' ?>
						</p>
						<p>_________________________________________</p>
						<p>
							<?php echo '$', CHtml::textField('mdata[truck_delivery][OS1][p_piece]', isset($model->mdata['truck_delivery']['OS1']['p_piece']) ? $model->mdata['truck_delivery']['OS1']['p_piece'] : 0, array('style' => 'width:30px')), '/piece or ', CHtml::textField('mdata[truck_delivery][OS1][p_shipment]', isset($model->mdata['truck_delivery']['OS1']['p_shipment']) ? $model->mdata['truck_delivery']['OS1']['p_shipment'] : 0, array('style' => 'width:30px')), '/shipment' ?>

						</p>

					</div>

					<div class="row rowcol rowcol-left" style="margin-top: 20px;">
						<?php echo CHtml::label('OS2', 'OS2') ?>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS2][kgdwf]', isset($model->mdata['truck_delivery']['OS2']['kgdwf']) ? $model->mdata['truck_delivery']['OS2']['kgdwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS2][kgdwt]', isset($model->mdata['truck_delivery']['OS2']['kgdwt']) ? $model->mdata['truck_delivery']['OS2']['kgdwt'] : 0, array('style' => 'width:30px')), 'KG Dead Weight' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS2][kgcwf]', isset($model->mdata['truck_delivery']['OS2']['kgcwf']) ? $model->mdata['truck_delivery']['OS2']['kgcwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS2][kgcwt]', isset($model->mdata['truck_delivery']['OS2']['kgcwt']) ? $model->mdata['truck_delivery']['OS2']['kgcwt'] : 0, array('style' => 'width:30px')), 'KG Cubic Weight' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS2][meterf]', isset($model->mdata['truck_delivery']['OS2']['meterf']) ? $model->mdata['truck_delivery']['OS2']['meterf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS2][metert]', isset($model->mdata['truck_delivery']['OS2']['metert']) ? $model->mdata['truck_delivery']['OS2']['metert'] : 0, array('style' => 'width:30px')), 'M Diagonal Length' ?>

						</p>

						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS2][length]', isset($model->mdata['truck_delivery']['OS2']['length']) ? $model->mdata['truck_delivery']['OS2']['length'] : 0, array('style' => 'width:30px')), 'M length' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS2][width]', isset($model->mdata['truck_delivery']['OS2']['width']) ? $model->mdata['truck_delivery']['OS2']['width'] : 0, array('style' => 'width:30px')), 'M width' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS2][height]', isset($model->mdata['truck_delivery']['OS2']['height']) ? $model->mdata['truck_delivery']['OS2']['height'] : 0, array('style' => 'width:30px')), 'M height' ?>

						</p>

						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS2][cbm]', isset($model->mdata['truck_delivery']['OS2']['cbm']) ? $model->mdata['truck_delivery']['OS2']['cbm'] : 0, array('style' => 'width:30px')), 'cbm' ?>
						</p>
						<p>_________________________________________</p>
						<p>
							<?php echo '$', CHtml::textField('mdata[truck_delivery][OS2][p_piece]', isset($model->mdata['truck_delivery']['OS2']['p_piece']) ? $model->mdata['truck_delivery']['OS2']['p_piece'] : 0, array('style' => 'width:30px')), '/piece or ', CHtml::textField('mdata[truck_delivery][OS2][p_shipment]', isset($model->mdata['truck_delivery']['OS2']['p_shipment']) ? $model->mdata['truck_delivery']['OS2']['p_shipment'] : 0, array('style' => 'width:30px')), '/shipment' ?>
						</p>
					</div>

					<div class="row rowcol rowcol-left" style="margin-top: 20px;">
						<?php echo CHtml::label('OS3', 'OS3') ?>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS3][kgdwf]', isset($model->mdata['truck_delivery']['OS3']['kgdwf']) ? $model->mdata['truck_delivery']['OS3']['kgdwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS3][kgdwt]', isset($model->mdata['truck_delivery']['OS3']['kgdwt']) ? $model->mdata['truck_delivery']['OS3']['kgdwt'] : 0, array('style' => 'width:30px')), 'KG Dead Weight' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS3][kgcwf]', isset($model->mdata['truck_delivery']['OS3']['kgcwf']) ? $model->mdata['truck_delivery']['OS3']['kgcwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS3][kgcwt]', isset($model->mdata['truck_delivery']['OS3']['kgcwt']) ? $model->mdata['truck_delivery']['OS3']['kgcwt'] : 0, array('style' => 'width:30px')), 'KG Cubic Weight' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS3][meterf]', isset($model->mdata['truck_delivery']['OS3']['meterf']) ? $model->mdata['truck_delivery']['OS3']['meterf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS3][metert]', isset($model->mdata['truck_delivery']['OS3']['metert']) ? $model->mdata['truck_delivery']['OS3']['metert'] : 0, array('style' => 'width:30px')), 'M Diagonal Length' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS3][length]', isset($model->mdata['truck_delivery']['OS3']['length']) ? $model->mdata['truck_delivery']['OS3']['length'] : 0, array('style' => 'width:30px')), 'M length' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS3][width]', isset($model->mdata['truck_delivery']['OS3']['width']) ? $model->mdata['truck_delivery']['OS3']['width'] : 0, array('style' => 'width:30px')), 'M width' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS3][height]', isset($model->mdata['truck_delivery']['OS3']['height']) ? $model->mdata['truck_delivery']['OS3']['height'] : 0, array('style' => 'width:30px')), 'M height' ?>

						</p>

						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS3][cbm]', isset($model->mdata['truck_delivery']['OS3']['cbm']) ? $model->mdata['truck_delivery']['OS3']['cbm'] : 0, array('style' => 'width:30px')), 'cbm' ?>
						</p>
						<p>_________________________________________</p>
						<p>
							<?php echo '$', CHtml::textField('mdata[truck_delivery][OS3][p_piece]', isset($model->mdata['truck_delivery']['OS3']['p_piece']) ? $model->mdata['truck_delivery']['OS3']['p_piece'] : 0, array('style' => 'width:30px')), '/piece or ', CHtml::textField('mdata[truck_delivery][OS3][p_shipment]', isset($model->mdata['truck_delivery']['OS3']['p_shipment']) ? $model->mdata['truck_delivery']['OS3']['p_shipment'] : 0, array('style' => 'width:30px')), '/shipment' ?>
						</p>
					</div>

					<div class="row rowcol rowcol-left" style="margin-top: 20px;">
						<?php echo CHtml::label('OS4', 'OS4') ?>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS4][kgdwf]', isset($model->mdata['truck_delivery']['OS4']['kgdwf']) ? $model->mdata['truck_delivery']['OS4']['kgdwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS4][kgdwt]', isset($model->mdata['truck_delivery']['OS4']['kgdwt']) ? $model->mdata['truck_delivery']['OS4']['kgdwt'] : 0, array('style' => 'width:30px')), 'KG Dead Weight' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS4][kgcwf]', isset($model->mdata['truck_delivery']['OS4']['kgcwf']) ? $model->mdata['truck_delivery']['OS4']['kgcwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS4][kgcwt]', isset($model->mdata['truck_delivery']['OS4']['kgcwt']) ? $model->mdata['truck_delivery']['OS4']['kgcwt'] : 0, array('style' => 'width:30px')), 'KG Cubic Weight' ?>
						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS4][meterf]', isset($model->mdata['truck_delivery']['OS4']['meterf']) ? $model->mdata['truck_delivery']['OS4']['meterf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS4][metert]', isset($model->mdata['truck_delivery']['OS4']['metert']) ? $model->mdata['truck_delivery']['OS4']['metert'] : 0, array('style' => 'width:30px')), 'M Diagonal Length' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS4][length]', isset($model->mdata['truck_delivery']['OS4']['length']) ? $model->mdata['truck_delivery']['OS4']['length'] : 0, array('style' => 'width:30px')), 'M length' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS4][width]', isset($model->mdata['truck_delivery']['OS4']['width']) ? $model->mdata['truck_delivery']['OS4']['width'] : 0, array('style' => 'width:30px')), 'M width' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS4][height]', isset($model->mdata['truck_delivery']['OS4']['height']) ? $model->mdata['truck_delivery']['OS4']['height'] : 0, array('style' => 'width:30px')), 'M height' ?>
						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS4][cbm]', isset($model->mdata['truck_delivery']['OS4']['cbm']) ? $model->mdata['truck_delivery']['OS4']['cbm'] : 0, array('style' => 'width:30px')), 'cbm' ?>
						</p>
						<p>_________________________________________</p>
						<p>
							<?php echo '$', CHtml::textField('mdata[truck_delivery][OS4][p_piece]', isset($model->mdata['truck_delivery']['OS4']['p_piece']) ? $model->mdata['truck_delivery']['OS4']['p_piece'] : 0, array('style' => 'width:30px')), '/piece or ', CHtml::textField('mdata[truck_delivery][OS4][p_shipment]', isset($model->mdata['truck_delivery']['OS4']['p_shipment']) ? $model->mdata['truck_delivery']['OS4']['p_shipment'] : 0, array('style' => 'width:30px')), '/shipment' ?>
						</p>
					</div>

					<div class="row rowcol rowcol-left" style="margin-top: 20px;">
						<?php echo CHtml::label('OS5', 'OS5') ?>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS5][kgdwf]', isset($model->mdata['truck_delivery']['OS5']['kgdwf']) ? $model->mdata['truck_delivery']['OS5']['kgdwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS5][kgdwt]', isset($model->mdata['truck_delivery']['OS5']['kgdwt']) ? $model->mdata['truck_delivery']['OS5']['kgdwt'] : 0, array('style' => 'width:30px')), 'KG Dead Weight' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS5][kgcwf]', isset($model->mdata['truck_delivery']['OS5']['kgcwf']) ? $model->mdata['truck_delivery']['OS5']['kgcwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS5][kgcwt]', isset($model->mdata['truck_delivery']['OS5']['kgcwt']) ? $model->mdata['truck_delivery']['OS5']['kgcwt'] : 0, array('style' => 'width:30px')), 'KG Cubic Weight' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS5][meterf]', isset($model->mdata['truck_delivery']['OS5']['meterf']) ? $model->mdata['truck_delivery']['OS5']['meterf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS5][metert]', isset($model->mdata['truck_delivery']['OS5']['metert']) ? $model->mdata['truck_delivery']['OS5']['metert'] : 0, array('style' => 'width:30px')), 'M Diagonal Length' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS5][length]', isset($model->mdata['truck_delivery']['OS5']['length']) ? $model->mdata['truck_delivery']['OS5']['length'] : 0, array('style' => 'width:30px')), 'M length' ?>
						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS5][width]', isset($model->mdata['truck_delivery']['OS5']['width']) ? $model->mdata['truck_delivery']['OS5']['width'] : 0, array('style' => 'width:30px')), 'M width' ?>
						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS5][height]', isset($model->mdata['truck_delivery']['OS5']['height']) ? $model->mdata['truck_delivery']['OS5']['height'] : 0, array('style' => 'width:30px')), 'M height' ?>
						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS5][cbm]', isset($model->mdata['truck_delivery']['OS5']['cbm']) ? $model->mdata['truck_delivery']['OS5']['cbm'] : 0, array('style' => 'width:30px')), 'cbm' ?>
						</p>
						<p>_________________________________________</p>
						<p>
							<?php echo '$', CHtml::textField('mdata[truck_delivery][OS5][p_piece]', isset($model->mdata['truck_delivery']['OS5']['p_piece']) ? $model->mdata['truck_delivery']['OS5']['p_piece'] : 0, array('style' => 'width:30px')), '/piece or ', CHtml::textField('mdata[truck_delivery][OS5][p_shipment]', isset($model->mdata['truck_delivery']['OS5']['p_shipment']) ? $model->mdata['truck_delivery']['OS5']['p_shipment'] : 0, array('style' => 'width:30px')), '/shipment' ?>
						</p>
					</div>

					<div class="row rowcol rowcol-left" style="margin-top: 20px;">
						<?php echo CHtml::label('OS6', 'OS6') ?>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS6][kgdwf]', isset($model->mdata['truck_delivery']['OS6']['kgdwf']) ? $model->mdata['truck_delivery']['OS6']['kgdwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS6][kgdwt]', isset($model->mdata['truck_delivery']['OS6']['kgdwt']) ? $model->mdata['truck_delivery']['OS6']['kgdwt'] : 0, array('style' => 'width:30px')), 'KG Dead Weight' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS6][kgcwf]', isset($model->mdata['truck_delivery']['OS6']['kgcwf']) ? $model->mdata['truck_delivery']['OS6']['kgcwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS6][kgcwt]', isset($model->mdata['truck_delivery']['OS6']['kgcwt']) ? $model->mdata['truck_delivery']['OS6']['kgcwt'] : 0, array('style' => 'width:30px')), 'KG Cubic Weight' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS6][meterf]', isset($model->mdata['truck_delivery']['OS6']['meterf']) ? $model->mdata['truck_delivery']['OS6']['meterf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS6][metert]', isset($model->mdata['truck_delivery']['OS6']['metert']) ? $model->mdata['truck_delivery']['OS6']['metert'] : 0, array('style' => 'width:30px')), 'M Diagonal Length' ?>

						</p>

						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS6][length]', isset($model->mdata['truck_delivery']['OS6']['length']) ? $model->mdata['truck_delivery']['OS6']['length'] : 0, array('style' => 'width:30px')), 'M length' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS6][width]', isset($model->mdata['truck_delivery']['OS6']['width']) ? $model->mdata['truck_delivery']['OS6']['width'] : 0, array('style' => 'width:30px')), 'M width' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS6][height]', isset($model->mdata['truck_delivery']['OS6']['height']) ? $model->mdata['truck_delivery']['OS6']['height'] : 0, array('style' => 'width:30px')), 'M height' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS6][cbm]', isset($model->mdata['truck_delivery']['OS6']['cbm']) ? $model->mdata['truck_delivery']['OS6']['cbm'] : 0, array('style' => 'width:30px')), 'cbm' ?>
						</p>
						<p>_________________________________________</p>
						<p>
							<?php echo '$', CHtml::textField('mdata[truck_delivery][OS6][p_piece]', isset($model->mdata['truck_delivery']['OS6']['p_piece']) ? $model->mdata['truck_delivery']['OS6']['p_piece'] : 0, array('style' => 'width:30px')), '/piece or ', CHtml::textField('mdata[truck_delivery][OS6][p_shipment]', isset($model->mdata['truck_delivery']['OS6']['p_shipment']) ? $model->mdata['truck_delivery']['OS6']['p_shipment'] : 0, array('style' => 'width:30px')), '/shipment' ?>

						</p>
					</div>
					<div class="row rowcol rowcol-left" style="margin-top: 20px;">
						<?php echo CHtml::label('OS7', 'OS7') ?>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS7][kgdwf]', isset($model->mdata['truck_delivery']['OS7']['kgdwf']) ? $model->mdata['truck_delivery']['OS7']['kgdwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS7][kgdwt]', isset($model->mdata['truck_delivery']['OS7']['kgdwt']) ? $model->mdata['truck_delivery']['OS7']['kgdwt'] : 0, array('style' => 'width:30px')), 'KG Dead Weight' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS7][kgcwf]', isset($model->mdata['truck_delivery']['OS7']['kgcwf']) ? $model->mdata['truck_delivery']['OS7']['kgcwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS7][kgcwt]', isset($model->mdata['truck_delivery']['OS7']['kgcwt']) ? $model->mdata['truck_delivery']['OS7']['kgcwt'] : 0, array('style' => 'width:30px')), 'KG Cubic Weight' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS7][meterf]', isset($model->mdata['truck_delivery']['OS7']['meterf']) ? $model->mdata['truck_delivery']['OS7']['meterf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OS7][metert]', isset($model->mdata['truck_delivery']['OS7']['metert']) ? $model->mdata['truck_delivery']['OS7']['metert'] : 0, array('style' => 'width:30px')), 'M Diagonal Length' ?>

						</p>

						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS7][length]', isset($model->mdata['truck_delivery']['OS7']['length']) ? $model->mdata['truck_delivery']['OS7']['length'] : 0, array('style' => 'width:30px')), 'M length' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS7][width]', isset($model->mdata['truck_delivery']['OS7']['width']) ? $model->mdata['truck_delivery']['OS7']['width'] : 0, array('style' => 'width:30px')), 'M width' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS7][height]', isset($model->mdata['truck_delivery']['OS7']['height']) ? $model->mdata['truck_delivery']['OS7']['height'] : 0, array('style' => 'width:30px')), 'M height' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OS7][cbm]', isset($model->mdata['truck_delivery']['OS7']['cbm']) ? $model->mdata['truck_delivery']['OS7']['cbm'] : 0, array('style' => 'width:30px')), 'cbm' ?>
						</p>
						<p>_________________________________________</p>
						<p>
							<?php echo '$', CHtml::textField('mdata[truck_delivery][OS7][p_piece]', isset($model->mdata['truck_delivery']['OS7']['p_piece']) ? $model->mdata['truck_delivery']['OS7']['p_piece'] : 0, array('style' => 'width:30px')), '/piece or ', CHtml::textField('mdata[truck_delivery][OS7][p_shipment]', isset($model->mdata['truck_delivery']['OS7']['p_shipment']) ? $model->mdata['truck_delivery']['OS7']['p_shipment'] : 0, array('style' => 'width:30px')), '/shipment' ?>

						</p>
					</div>

					<!-- <div class="row rowcol rowcol-left" style="margin-top: 20px;">
						<?php echo CHtml::label('OSC', 'OSC') ?>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OSC][kgdwf]', isset($model->mdata['truck_delivery']['OSC']['kgdwf']) ? $model->mdata['truck_delivery']['OSC']['kgdwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OSC][kgdwt]', isset($model->mdata['truck_delivery']['OSC']['kgdwt']) ? $model->mdata['truck_delivery']['OSC']['kgdwt'] : 0, array('style' => 'width:30px')), 'KG Dead Weight' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OSC][kgcwf]', isset($model->mdata['truck_delivery']['OSC']['kgcwf']) ? $model->mdata['truck_delivery']['OSC']['kgcwf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OSC][kgcwt]', isset($model->mdata['truck_delivery']['OSC']['kgcwt']) ? $model->mdata['truck_delivery']['OSC']['kgcwt'] : 0, array('style' => 'width:30px')), 'KG Cubic Weight' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OSC][meterf]', isset($model->mdata['truck_delivery']['OSC']['meterf']) ? $model->mdata['truck_delivery']['OSC']['meterf'] : 0, array('style' => 'width:30px')), '-', CHtml::textField('mdata[truck_delivery][OSC][metert]', isset($model->mdata['truck_delivery']['OSC']['metert']) ? $model->mdata['truck_delivery']['OSC']['metert'] : 0, array('style' => 'width:30px')), 'M Diagonal Length' ?>

						</p>

						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OSC][length]', isset($model->mdata['truck_delivery']['OSC']['length']) ? $model->mdata['truck_delivery']['OSC']['length'] : 0, array('style' => 'width:30px')), 'M length' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OSC][width]', isset($model->mdata['truck_delivery']['OSC']['width']) ? $model->mdata['truck_delivery']['OSC']['width'] : 0, array('style' => 'width:30px')), 'M width' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OSC][height]', isset($model->mdata['truck_delivery']['OSC']['height']) ? $model->mdata['truck_delivery']['OSC']['height'] : 0, array('style' => 'width:30px')), 'M height' ?>

						</p>
						<p>
							<?php echo CHtml::textField('mdata[truck_delivery][OSC][cbm]', isset($model->mdata['truck_delivery']['OSC']['cbm']) ? $model->mdata['truck_delivery']['OSC']['cbm'] : 0, array('style' => 'width:30px')), 'cbm' ?>
						</p>
						<p>_________________________________________</p>
						<p>
							<?php echo '$', CHtml::textField('mdata[truck_delivery][OSC][p_piece]', isset($model->mdata['truck_delivery']['OSC']['p_piece']) ? $model->mdata['truck_delivery']['OSC']['p_piece'] : 0, array('style' => 'width:30px')), '/piece or ', CHtml::textField('mdata[truck_delivery][OSC][p_shipment]', isset($model->mdata['truck_delivery']['OSC']['p_shipment']) ? $model->mdata['truck_delivery']['OSC']['p_shipment'] : 0, array('style' => 'width:30px')), '/shipment' ?>

						</p>
					</div> -->
				</div>
			</div>
		</div>

	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
	$(document).ready(function(e) {

		var win = $('#jqmw_<?= $_GET["tabid"]; ?>');

		// current weight ranges
		var weightRanges = <?php echo json_encode($zoneRate->getZoneRateWeightRangeByArray($model->id)); ?>;

		// set default zone cost rate
		var zoneCostRateData = <?php echo json_encode(ZoneMap::getOrgZoneMap($model->org_id, $model->zone_id)); ?>;

		var allZoneRateData = [];
		var allRowIndex = 0;
		var selectedRowIndex = 0;
		var importInProgress = false;

		initZoneRateData(zoneCostRateData);
		appendWerightRanges(weightRanges);

		function isImportInProgress() {
			return importInProgress;
		}

		function appendWerightRanges(rateData) {

			$('#zone-rate-weight-range-grid .items tbody td.empty', win).parent().remove();
			var wtpl = $('#zone-rate-weight-range-grid .items tfoot', win);
			var row = wtpl.find('tr').clone();
			for (var i in rateData) {
				allRowIndex++;
				var r = wtpl.find('tr').clone();
				$('.add_btn', r).replaceWith('<a href="javascript:;" data-index="' + allRowIndex + '"class="delete_btn" title="Remove">Remove</a>');
				$('input', r).each(function() {
					var n = $(this).attr('name');
					$(this).attr('name', n + '[]');
				});
				$('#zone-rate-weight-range-grid .items tbody', win).append(r);
				r.find('#zone-rate-weight-range-grid_weight_lo').val(rateData[i]['weight_lo']);
				r.find('#zone-rate-weight-range-grid_weight_hi').val(rateData[i]['weight_hi']);
				addOneZoneRateByData(allRowIndex, rateData[i]['data']);
			}
		}

		function addOneZoneRateByData(tag, rateData) {
			var bFound = false;
			for (var i in allZoneRateData) {
				var oneZone = allZoneRateData[i];
				if (oneZone['tag'] == tag) {
					bFound = true;
				}
			}
			if (!bFound) {
				var zoneData = {};
				zoneData['tag'] = tag;
				zoneData['lo'] = 0;
				zoneData['hi'] = 0;
				zoneData['data'] = rateData;
				allZoneRateData.push(zoneData);
			}
		}

		function addOneZoneRate(tag) {
			// check existing or not
			// if not existing just create a new one
			var bFound = false;
			for (var i in allZoneRateData) {
				var oneZone = allZoneRateData[i];
				if (oneZone['tag'] == tag) {
					bFound = true;
				}
			}
			if (!bFound) {
				var zoneData = {};
				zoneData['tag'] = tag;
				zoneData['lo'] = 0;
				zoneData['hi'] = 0;
				zoneData['data'] = JSON.parse(JSON.stringify(zoneCostRateData));
				allZoneRateData.push(zoneData);
			}
		}

		function updateLoHi(dataIndex, lo, hi) {
			// get related zone rate data
			for (var i in allZoneRateData) {
				var oneZone = allZoneRateData[i];
				if (oneZone['tag'] == dataIndex) {
					oneZone['lo'] = lo;
					oneZone['hi'] = hi;
					break;
				}
			}
		}

		function refreshZoneRate(tag) {
			// get related zone rate data
			var bFound = false;
			var oneZoneRate = [];
			for (var i in allZoneRateData) {
				var oneZone = allZoneRateData[i];
				if (oneZone['tag'] == tag) {
					bFound = true;
					oneZoneRate = oneZone['data'];
					break;
				}
			}
			if (bFound) {
				selectedRowIndex = tag;
				var zoneBody = $('#org-zone-rate-view tbody', win);
				zoneBody.empty();
				fillZoneRateData(oneZoneRate);
			}
		}

		function fillZoneRateData(rateData) {
			var zoneBody = $('#org-zone-rate-view tbody', win);
			var index = 0;
			for (var i in rateData) {
				var detail = rateData[i];
				var rowData = '<tr class="odd">';
				if (index++ % 2 == 0) {
					rowData = '<tr class="even">';
				}
				rowData += '<td class="show-details"><input type="hidden" name="id" value="">';
				rowData += '<a href="zoneMap/list?oid=<?= $model->org_id; ?>&zid=<?= $model->zone_id; ?>&z1=' + detail['code'] + '" class="tab_link" title="' + detail['code'] + '">' + detail['code'] + '</a></td>';
				rowData += '<td>' + detail['name'] + '</td>';
				rowData += '<td><input name="ppc[]" data-code="' + detail['code'] + '" type="text" value="' + detail['ppc'] + '"></td>';
				rowData += '<td><input name="pkg[]" data-code="' + detail['code'] + '"type="text" value="' + detail['pkg'] + '"></td>';
				rowData += '<td><input name="base[]" data-code="' + detail['code'] + '"type="text" value="' + detail['base'] + '"></td>';
				rowData += '<td><input name="minimum[]" data-code="' + detail['code'] + '"type="text" value="' + detail['minimum'] + '"></td>';
				rowData += '<td><input name="min_incl[]" data-code="' + detail['code'] + '"type="text" value="' + detail['min_incl'] + '"></td>';
				rowData += '<td><input name="nkg[]" data-code="' + detail['code'] + '"type="text" value="' + detail['nkg'] + '"></td></tr>';

				zoneBody.append(rowData);
			}
		}

		function initZoneRateData(rateData) {
			fillZoneRateData(rateData);
		}

		function validWeightFrom($fromWeight) {
			var lo = parseFloat($fromWeight.val());
			var hi = parseFloat($fromWeight.parent().parent().find('#zone-rate-weight-range-grid_weight_hi').val());
			if (!isNaN(lo) && !isNaN(hi)) {
				if (lo > hi) {
					alert("low weight must be less than high weight");
					return false;
				}
			}
			return true;
		}

		function validWeightTo($toWeight) {
			var hi = parseFloat($toWeight.val());
			var lo = parseFloat($toWeight.parent().parent().find('#zone-rate-weight-range-grid_weight_lo').val());
			if (!isNaN(lo) && !isNaN(hi)) {
				if (lo > hi) {
					alert("low weight must be less than high weight");
					return false;
				}
			}
			return true;
		}

		function refreshSelectedWeightRangeByDest(destRange) {
			if (typeof destRange != 'undefined') {
				var lo = destRange.find('#zone-rate-weight-range-grid_weight_lo').val();
				var hi = destRange.find('#zone-rate-weight-range-grid_weight_hi').val();
				var wrangeStr = '(' + lo + 'kg - ' + hi + 'kg)';
				$('#zone-rate-weight-span', win).html(wrangeStr);
			}
		}

		function updateRowItemData(value, code, key) {
			//console.log('update data for :' + value + ' ,' +  code  + ' , ' + key);
			// save related price by piece
			var bFound = false;
			var oneZoneRate = [];
			var rowIndex = 0;
			for (var i in allZoneRateData) {
				var oneZone = allZoneRateData[i];
				if (oneZone['tag'] == selectedRowIndex) {
					bFound = true;
					oneZoneRate = oneZone['data'];
					rowIndex = i;
					// console.log('related zone data found');
					break;
				}
			}
			if (bFound) {
				// get code related data item
				for (var j in oneZoneRate) {
					var oneData = oneZoneRate[j];
					if (oneData['code'] == code) {
						oneData[key] = value;
						break;
					}
				}
			}
		}


		$('form#org-zone-rate-form', win).on('success', function(e, r) {
			win.jqmHide();
		});


		$('#zone-rate-weight-range-grid .add_btn', win).on('click', function(e) {
			if (isImportInProgress()) return;
			e.preventDefault();
			allRowIndex++;
			$('#zone-rate-weight-range-grid .items tbody td.empty', win).parent().remove();
			var r = $(this).parents('tr').clone();
			$('.add_btn', r).replaceWith('<a href="javascript:;" data-index="' + allRowIndex + '"class="delete_btn" title="Remove">Remove</a>');
			$('input', r).each(function() {
				var n = $(this).attr('name');
				$(this).attr('name', n + '[]');
			});
			$('#zone-rate-weight-range-grid .items tbody', win).append(r);
			$(this).parents('tr').find('input').val('');

			addOneZoneRate(allRowIndex);

			return false;
		});

		$('#zone-rate-weight-range-grid', win).on('click', 'tr', function(e) {
			if (isImportInProgress()) return;
			// exclude the dummy row data
			if ($(this).parent().is("tfoot")) return;

			// get related zone data related tag
			var tag = $(this).find('.delete_btn').data('index');
			refreshZoneRate(tag);
			refreshSelectedWeightRangeByDest($(this));

		});

		$('#zone-rate-weight-range-grid', win).on('click', '.delete_btn', function(e) {
			if (isImportInProgress()) return;
			e.preventDefault();
			if (confirm('Are you sure remove the zone rate data?')) {
				$(this).parents('tr').remove();
				return false;
			}
			return true;
		});

		$('#zone-rate-weight-range-grid', win).on('change textInput input', 'input[name="ZoneRate[weight_lo][]"]', function(e) {
			if (isImportInProgress()) return;
			refreshSelectedWeightRangeByDest($(this).parent().parent());
			if (!validWeightFrom($(this))) $(this).focus();
		});

		$('#zone-rate-weight-range-grid', win).on('change textInput input', 'input[name="ZoneRate[weight_hi][]"]', function(e) {
			if (isImportInProgress()) return;
			refreshSelectedWeightRangeByDest($(this).parent().parent());
			if (!validWeightTo($(this))) $(this).focus();
		});

		$('#org-zone-rate-view', win).on('change textInput input', 'input[name="ppc[]"]', function(e) {
			if (isImportInProgress()) return;
			updateRowItemData($(this).val(), $(this).data('code'), 'ppc');
		});

		$('#org-zone-rate-view', win).on('change textInput input', 'input[name="pkg[]"]', function(e) {
			if (isImportInProgress()) return;
			updateRowItemData($(this).val(), $(this).data('code'), 'pkg');
		});

		$('#org-zone-rate-view', win).on('change textInput input', 'input[name="minimum[]"]', function(e) {
			if (isImportInProgress()) return;
			updateRowItemData($(this).val(), $(this).data('code'), 'minimum');
		});

		$('#org-zone-rate-view', win).on('change textInput input', 'input[name="min_incl[]"]', function(e) {
			if (isImportInProgress()) return;
			updateRowItemData($(this).val(), $(this).data('code'), 'min_incl');
		});

		$('#org-zone-rate-view', win).on('change textInput input', 'input[name="base[]"]', function(e) {
			if (isImportInProgress()) return;
			updateRowItemData($(this).val(), $(this).data('code'), 'base');
		});
		$('#org-zone-rate-view', win).on('change textInput input', 'input[name="nkg[]"]', function(e) {
			if (isImportInProgress()) return;
			updateRowItemData($(this).val(), $(this).data('code'), 'nkg');
		});

		$('#zone_cost_rate_import_btn', win).click(function(e) {
			if (isImportInProgress()) return;

			$('#import-inprogress-flag', win).addClass('grid-view-loading');
			e.preventDefault();
			e.stopPropagation();
			importInProgress = true;
			$('form#org-zone-cost-import-form', win).submit();
		});


		$('form#org-zone-cost-import-form', win).on('error', function(e, r) {
			importInProgress = false;
		});

		$('form#org-zone-cost-import-form', win).data('custom_success', function(r) {

			if (r.done === true) {
				// refresh current data
				allZoneRateData = [];
				allRowIndex = 0;
				selectedRowIndex = 0;
				$('#org-zone-rate-view tbody', win).empty();
				$('#zone-rate-weight-range-grid .items tbody', win).empty();
				initZoneRateData(r.zcostrate);
				appendWerightRanges(r.wranges);

				alert('Import successfully!');
			} else {
				alert(r.msg);
			}
			$('#import-inprogress-flag', win).removeClass('grid-view-loading');

			importInProgress = false;

			return true;
		});



		$('#org-zone-rate-view', win).on('paste', 'tbody input[type=text]', function(e) {
			if (window.clipboardData && window.clipboardData.getData) {
				pastedText = window.clipboardData.getData('Text');
			} else if (e.originalEvent.clipboardData && e.originalEvent.clipboardData.getData) {
				pastedText = e.originalEvent.clipboardData.getData('text/plain');
			}

			var ppos = -1;
			var me = $(this);
			var cells = pastedText.trim().split(/[\t\n\r]+/);
			$('#org-zone-rate-view tbody input[type=text]', win).each(function(i) {
				if ($(this).is(me)) ppos = 0;
				if (ppos >= cells.length) return false;
				if (ppos > -1) $(this).val(cells[ppos++]).trigger('change');
			});

			return false;
		});

		$('a.copyData', win).on('click', function() {
			var input = $('<textarea style="width:1px; height:1px;border:none;"></textarea>');
			var rs = [];
			$('#org-zone-rate-view tbody tr', win).each(function(i) {
				var c = [];
				$('input[type=text]', this).each(function() {
					c.push($(this).val());
				});
				rs.push(c.join("\t"));
			});
			win.append(input);
			input.val(rs.join("\n")).select();
			document.execCommand("copy");
			input.remove();
			window.alert('Copied');
		});
	});
</script>