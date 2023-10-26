<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
    'links' => array(
        'Create Consol',
    ),
));
?>

<h1><?=$this->t('Create Consol');?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol-form',
	'enableAjaxValidation'=>false,
));
$model->pol = 'CNSHA';
$model->pod = 'AUSYD';
$o = Org::model()->findByPk(Yii::app()->user->org);
?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>
	<?php echo $form->errorSummary($model);?>

<div class="row">
    <div class="col col-sm-4">
    <div class="form-group">
	<?php if( !isset($o->extra['warehouse']) || (isset($o->extra['warehouse']) && sizeof($o->extra['warehouse']) > 1 ) ): ?>

		<?php echo $form->labelEx($model,'dpt_id'); ?>
		<?php echo $form->dropDownList($model, 'dpt_id', $o->getWarehouseList(), array('empty' => 'Select One')); ?>
		<?php echo $form->error($model,'dpt_id'); ?>

	<?php else:
	$model->dpt_id = $o->extra['warehouse'][0];
	echo $form->hiddenField($model, 'dpt_id');
	endif; ?>
    </div>
    </div>
    </div>

    <div class="row">
        <div class="col col-sm-3">
            <div class="form-group">
            <?php echo $form->labelEx($model,'awb'); ?>
            <?php echo $form->textField($model,'awb',array('size'=>15,'maxlength'=>50)); ?>
            <?php echo $form->error($model,'awb'); ?>
                </div>
        </div>

        <div class="col col-sm-3">
            <div class="form-group">
            <?php echo $form->labelEx($model,'airline'); ?>
            <?php echo $form->textField($model,'airline',array('size'=>15,'maxlength'=>50)); ?>
            <?php echo $form->error($model,'airline'); ?>
                </div>
        </div>

        <div class="col col-sm-3">
            <div class="form-group">
            <?php echo $form->labelEx($model,'flight'); ?>
            <?php echo $form->textField($model,'flight',array('size'=>15,'maxlength'=>50)); ?>
            <?php echo $form->error($model,'flight'); ?>
                </div>
        </div>


    </div>

    <div class="row">
        <div class="col col-sm-8">
            <div class="row">
	<div class="col col-sm-3">
        <div class="form-group">
		<?php echo $form->labelEx($model,'pol'); ?>
		<?php echo $form->dropDownList($model,'pol', AppHelper::setting2List('pols')); ?>
		<?php echo $form->error($model,'pol'); ?>
            </div>
	</div>

	<div class="col col-sm-3">
        <div class="form-group">
		<?php echo $form->labelEx($model,'pod'); ?>
		<?php echo $form->dropDownList($model,'pod', AppHelper::setting2List('pods')); ?>
		<?php echo $form->error($model,'pod'); ?>
            </div>
	</div>

	<div class="col col-sm-3">
        <div class="form-group">
		<?php echo $form->labelEx($model,'etd'); ?>
		<?php echo $form->textField($model,'etd', array('size' => 12, 'id' => 'etd','class' => 'date_input')); ?>
		<?php echo $form->error($model,'etd'); ?>
            </div>
	</div>

	<div class="col col-sm-3">
        <div class="form-group">
		<?php echo $form->labelEx($model,'eta'); ?>
		<?php echo $form->textField($model,'eta', array('size' => 12, 'id' => 'eta','class' => 'date_input')); ?>
		<?php echo $form->error($model,'eta'); ?>
            </div>
	</div>
            </div>
	</div>
    </div>


 <div class="form-group">
            <?php
            echo CHtml::label('Paid Manifests','recs');
            $m = new Manifest('search');
            if(isset($_GET['Manifest'])) $m->attributes=$_GET['Manifest'];
            $m->type = 10;  // Import lodgement
            $m->status = 12; // Console waiting has been paid or client credit is enough
            $m->consol_id = 0; // only for not consoled
            $m->dpt_id = ( !isset($o->extra['warehouse']) || ( isset($o->extra['warehouse']) && sizeof($o->extra['warehouse']) > 1 ) ) ? (empty($_GET['ImcoConsol']['dpt_id'])? -1 : $_GET['ImcoConsol']['dpt_id']) : $o->extra['warehouse'][0];

            // check for paid manifest only
            $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'con-man-grid',
            'cssFile' => false,
            'dataProvider' => $m->search(false,30,false),
            'filter' => $m,
            'summaryText' => false,
            'columns'=>array(
                array('header' => '<input type="checkbox" id="chkbox_all" name="recs_all" checked="checked" />', 'type' => 'raw', 'value' => '"<input class=\"chkbox\" type=\"checkbox\" name=\"recs[]\" value=\"".$data->id."\" checked />"', 'htmlOptions' => array('align' => 'center')),
                array('header' => 'ID', 'name' => 'id'),
                array('header' => 'Client','name' => 'fwd_id' ,'type'=>'raw','value' => 'empty($data->owner)? "" : $data->owner->name'),
                array('header' => 'Status','name' => 'status', 'type' => 'raw', 'value' => '$data->getStatus()',
                    'filter'=>CHtml::dropDownList(get_class($m).'[status]', $m->status, $this->t($m->statusList()), array('prompt' => $this->t('All'), 'class' => 'form-control'))),
                array('header' => 'Packages', 'name' => 'allshipments','type' => 'raw','value' => '$data->totPacks()'),
                array('header' => 'Weight', 'name' => 'totalWeight','type' => 'raw','value' => '$data->totWeight()'),
                array('header' => 'CBM', 'name' => 'totalCBM','type' => 'raw','value' => '$data->totCBM()'),
                array('header' => 'Value', 'name' => 'total'),
                array('header' => 'Created', 'name' => 'created'),
            ),
        )); ?>

    </div>


    <div class="form-group">
       <b> Total Weight : </b> <input type="number" name="total_weight_show" id="total_weight_show" disabled value="0.0">kg <br> <br>
       <b> Total CBM : </b> <input type="number" name="total_cbm_show" id="total_cbm_show" disabled value="0.0">M<sup>3</sup><br>
        <br>
	<div class="buttons">
		<?php echo CHtml::submitButton('Create'); ?>
	</div>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php ob_start(); ?>

<script type="text/javascript">

    function showTotalInfo(){
        var totalWeight = 0.0;
        var totalCBM = 0.0;
        $('.items').find('tr').each(function(e){
           if ( $(this).find('input.chkbox').prop('checked') ) {
               totalWeight += parseFloat( $(this).find('td:nth-child(6)').html());
               totalCBM += parseFloat( $(this).find('td:nth-child(7)').html())
           }
        });

        $('#total_weight_show').val(totalWeight);
        $('#total_cbm_show').val(totalCBM);
    }

$(function(){

    $('#consol-form').on('click','input#chkbox_all',function(r){
        $('input.chkbox').prop('checked', $(this).prop('checked'));
        showTotalInfo();
    });

    $('#consol-form').on('click','input.chkbox',function(){
        showTotalInfo();
    });

	$('#con-man-grid').yiiGridView('update');
	
	$('#ImcoConsol_dpt_id').off('change').on('change', function(){
		$('#con-man-grid').yiiGridView('update', {
			data: { 'ImcoConsol[dpt_id]': $(this).val() }
		});

        // show all weight and CBM
        setTimeout(function(){
            showTotalInfo();
        },6000);

	});


    // show all weight and CBM
    setTimeout(function(){
        showTotalInfo();
    },6000);


});
</script>

<?php $this->registerJS(ob_get_clean(),8); ?>