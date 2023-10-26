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
                    echo CHtml::label('Shipments','recs');
                    $m = new ImParcel('search');
                    if(isset($_GET['ImParcel'])) $m->attributes=$_GET['ImParcel'];
                    $m->type = 10;
                    $m->status = 25;
                    $m->odpt_id = ( !isset($o->extra['warehouse']) || ( isset($o->extra['warehouse']) && sizeof($o->extra['warehouse']) > 1 ) ) ? (empty($_GET['ImcoConsol']['dpt_id'])? -1 : $_GET['ImcoConsol']['dpt_id']) : $o->extra['warehouse'][0];
                    $this->widget('zii.widgets.grid.CGridView', array(
                    'id'=>'con-man-grid',
                    'cssFile' => false,
                    'dataProvider' => $m->search(100),
                    'filter' => $m,
                    'summaryText' => false,
                    'columns'=>array(
                        array('header' => '<input type="checkbox" id="chkbox_all" name="recs_all" checked="checked" />', 'type' => 'raw', 'value' => '"<input class=\"chkbox\" type=\"checkbox\" name=\"recs[]\" value=\"".$data->id."\" checked />"', 'htmlOptions' => array('align' => 'center')),
                        array('header' => 'HBN', 'name' => 'hbn'),
                        array('header' => 'Client','name' => 'agent_id' ,'type'=>'raw','value' => '$data->agent->name'),
                        array('header' => 'Goods', 'name' => 'goods'),
                        array('header' => 'Packages', 'name' => 'pkg'),
                        array('header' => 'Value', 'name' => 'dvalue'),
                        array('header' => 'Weight', 'name' => 'weight'),
                        array('header' => 'State', 'name' => 'state'),
                        array('header' => 'Post Code', 'name' => 'postcode'),
                        array('header' => 'Created', 'name' => 'created'),
                    ),
                )); ?>

    </div>


    <div class="form-group">
	<div class="buttons">
		<?php echo CHtml::submitButton('Create'); ?>
	</div>
    </div>
<?php $this->endWidget(); ?>

</div><!-- form -->

<?php ob_start(); ?>

<script type="text/javascript">
$(function(){

    $('#consol-form').on('click','input#chkbox_all',function(r){
        $('input.chkbox').prop('checked', $(this).prop('checked'));
    });

	$('#con-man-grid').yiiGridView('update');
	
	$('#ImConsol_dpt_id').off('change').on('change', function(){
		$('#con-man-grid').yiiGridView('update', {
			data: { 'ImcoConsol[dpt_id]': $(this).val() }
		});
	});
});
</script>

<?php $this->registerJS(ob_get_clean(),8); ?>