<h1><?=$this->t('Create Export Consol');?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'exco-consol-form',
	'enableAjaxValidation'=>false,
));
$model->pol = 'AUSYD';
$model->pod = 'CNCAN';
?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>
	
	<div class="row rowcol">
		<?php echo $form->labelEx($model,'dpt_id'); ?>
		<?php echo $form->dropDownList($model,'dpt_id',Org::dptList(), array('empty' => 'Select One')); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'awb'); ?>
		<?php echo $form->textField($model,'awb',array('size'=>15,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'flight'); ?>
		<?php echo $form->textField($model,'flight',array('size'=>15,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'exrate'); ?>
		1AUD = <?php echo $form->textField($model,'exrate',array('size'=>10,'maxlength'=>15)); ?> <a href="http://www.customs.gov.au/site/page4277.asp" target="_blank"><div class="icon" style="background-position:-96px -384px"></div></a>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'pol'); ?>
		<?php echo $form->dropDownList($model,'pol', AppHelper::setting2List('pods')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'pod'); ?>
		<?php echo $form->dropDownList($model,'pod', AppHelper::setting2List('pols')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'poc'); ?>
		<?php echo $form->dropDownList($model,'poc',  ExChannel::getPocs(), array('empty' => 'Select One')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'etd'); ?>
		<?php echo $form->textField($model,'etd', array('size' => 12, 'id' => 'etd_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'eta'); ?>
		<?php echo $form->textField($model,'eta', array('size' => 12, 'id' => 'eta_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<!--div class="row">
		<label for="manifest">Manifest - <small>.csv/.xls/.xlsx File</small></label>
		<input type="file" name="manifest" id="manifest" />
	</div-->
	<div class="row">
	<?php
		echo CHtml::label('Shipments','recs');
		$m = new ExParcel('search');
		if(isset($_GET['ExParcel'])) $m->attributes=$_GET['ExParcel'];
		$m->odpt_id = empty($_GET['ExParcel']['odpt_id'])? -1 : $_GET['ExParcel']['odpt_id'];
		$m->status = 18;
		$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'excon-man-grid',
		'cssFile' => false,
		'dataProvider' => $m->createConsolSearch(),
		'afterAjaxUpdate' => 'js:function(id,data){ $("#'.$_GET["tabid"].'").trigger("totWeight"); }',
		'filter' => $m,
		'columns'=>array(
			array('header' => '<input type="checkbox" id="chkbox_all" name="recs_all" checked="checked" />', 'type' => 'raw', 'value' => '"<input class=\"chkbox\" type=\"checkbox\" name=\"recs[]\" value=\"".$data->id."\" checked />"', 'htmlOptions' => array('align' => 'center')),
			array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("exParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"'),
			array('name' => 'agent_name', 'value' => 'empty($data->agent)? "" : $data->agent->shortName(2)',),
			'tariff',
			'weight',
			array('name' => 'cnor_name', 'value' => 'empty($data->cnor)? "" : $data->cnor->name',),
			array('name' => 'cnee_name', 'value' => 'empty($data->cnee)? "" : $data->cnee->name',),
			array('header' => 'Holdings', 'type' => 'raw', 'value' => '$data->getDupStat()'),
		),
		));
	?>
	</div>

	<div class="row buttons">
		<div style="float:right" id="twt"></div>
		<?php echo CHtml::submitButton('Create'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<div id="result">
</div>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	tab.bind('onOpen', function(){
		$('#excon-man-grid', panel).yiiGridView('update');
	});
	
	$('form#exco-consol-form', panel).on('success', function(e, r){
		tab.trigger('load');
	});
	
	$('#ExcoConsol_dpt_id', tab.data('panel')).off('change').on('change', function(){
		var grid = $('#excon-man-grid', tab.data('panel'));
		if(grid.find('.summary').length == 0) grid.css('padding-top', '20px');
		grid.addClass('grid-view-loading').yiiGridView('update', {
			data: { 'ExParcel[odpt_id]': $(this).val() }
		});
	});

	var totWeight = function(){
		var t = 0;
		$('input.chkbox:checked', panel).each(function(){
			t += Number($($(this).parents('tr').find('td')[4]).text());
		});
		t = Math.round(t*100)/100;
		$('#twt', panel).html('Total weight: <b>'+t+'</b>kg');
	};

	tab.on('totWeight', totWeight);
	
	panel.off('click').on('click', 'input#chkbox_all', function(r){
		$('input.chkbox', panel).attr('checked', this.checked);
		totWeight();
	}).on('click', 'a.flow_link', function(){
		var that = $(this);
		var t = myApp.tabs.CreateTab({
			title: 'Manage Shipment',
			url: '<?=$this->createUrl("exParcel/list");?>',
			afterLoad: function(){
				p = $(this).data('panel');
				flow();
			}
		});
		var p = t.data('panel');
		var flow = function(){
			$('.search-form input, .search-form select, .search-form textarea, .grid-view .filters input, .grid-view .filters select').val('');
			$('#ExParcel_'+that.data('field'), p).val(that.data('val'));
			$('#ExParcel_status', p).val(18);
			$('.search-form form',p).trigger('submit');
		};
		flow();
		return false;
	}).on('click', 'input.chkbox', totWeight);
});
</script>