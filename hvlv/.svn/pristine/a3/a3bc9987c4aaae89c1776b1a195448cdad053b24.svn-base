<h1><?=$this->t('Billing waiting for posting');?></h1>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'billing-streamline-post-form',
	'action' => Yii::app()->createUrl('billing/post'),
	'enableAjaxValidation' => false,
)); ?>
<div class="form">
	<div class="row">
		<?php
			echo CHtml::label('Selected Total', 'total');
			echo '<span id="total" style="color: red">0.00</span>';
		?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Post'); ?>
	</div>

	<?php
		$model = new Billing;
		$model->unsetAttributes();
		$model->attributes = @$_GET['Billing'];
		$model->status = 2;
		$ec = new CDbCriteria;
		$ec->addCondition('t.org_id != ' . Org::ORGID_COURIER_AUPOST . ' OR (t.billing_cref NOT REGEXP "^C\\\d{8}" AND t.billing_cref NOT REGEXP "^DW\\\d{8}" AND t.billing_cref NOT REGEXP "^3PL\\\d{8}" AND t.billing_cref NOT REGEXP "^AP\\\d{8}")');
		$this->widget('application.extensions.editablegrid.CEditableGridView', array(
			'id' => 'billing-streamline-post-grid',
			'selectableRows' => 2,
			'cssFile' => false,
			'dataProvider' => $model->search(true, 30, 't.date DESC', $ec),
			'formUrl' => $this->createUrl('billing/billingGrid', array('fid' => $model->id)),
			'filter' => $model,
			'summaryText' => '',
			'showQuickBar' => false,
			'afterSave' => 'function(r){
				if(r.done == true){
					myApp.notice(r.msg, 5000);
				}else{
					myApp.alert(r.msg, false);
				}
				return r.done;
			}',
			'columns' => array(
				array(
					'id' => 'selectedItems',
					'class' => 'CCheckBoxColumn',
				),
				// array( 'header' => 'No','type' => 'raw', 'name' => 'billing_ref', 'value' => '$data->getNo()'),
				array('name' => 'billing_cref', 'type' => 'raw', 'value' => '"<a href=\"" . Yii::app()->createUrl("billing/lineFix", array("id" => $data->id)) . "\" class=\"jqm_link\" data-win-class=\"XXL\">" . $data->billing_cref . "</a>"'),
				array('name' => 'date', 'value' => '$data->date', 'class' => 'CEditableColumn'),
				array('name' => 'due', 'value' => '$data->due', 'class' => 'CEditableColumn'),
				array('name' => 'client', 'value' => ' ( empty($data->org_id) || empty($data->cust) ) ? "" : $data->cust->shortName(5)', 'type' => 'autocomplete', 'class' => 'CEditableColumn', 'acOptions' => array('source' => 'org/supplierSuggest')),
				array('name' => 'currency', 'value' => '$data->getCurrency()', 'filter' => CHtml::dropDownList('Billing[currency]', $model->currency, $this->t(Invoice::$currencies), array('prompt' => $this->t('All'))),),
				array('name' => 'dpt_id', 'value' => '$data->getDptName()', 'filter' => CHtml::dropDownList('Billing[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), array('prompt' => $this->t('All'))),),
				array('name' => 'dpmt', 'value' => '$data->getDpmt()', 'filter' => CHtml::dropDownList('Billing[dpmt]', $model->dpmt, Invoice::$dpmts, array('prompt' => $this->t('All'))),),
				array('header' => 'Total (Excl. GST)', 'value' => '$data->total - $data->gst'),
				array('header' => 'GST', 'name' => 'gst'),
				array('name' => 'total', 'value' => '$data->total'),
				array(
					'class' => 'CEditableButtonColumn',
					'template' => '{edit} {cancel} {save}',
				)
			),
		));
	?>

</div>
<?php $this->endWidget(); ?>

<script>
	$(function() {
		$('#billing-streamline-post-form').on('success', function() {
			$('#billing-streamline-post-grid').yiiGridView('update');
		});

		var form = $('#billing-streamline-post-form');

		$(form).on('click', 'tr', function(e) {
			var selectedMe = $(this).find('.select-on-check').prop('checked');

			if (e.target.className != 'select-on-check') {
				selectedMe = !selectedMe;
			}

			var gstValue = parseFloat($(this).find('td:nth-child(8)').html());
			if (isNaN(gstValue)) gstValue = 0;

			var total = parseFloat($('#total', form).html().replace(',', ''));
			if (isNaN(total)) total = 0;
			if (selectedMe) {
				total += gstValue;
			} else {
				total -= gstValue;
			}

			if (total < 0) total = 0;
			total = myApp.formatNumber(total, 2, '.', ',');

			$('#total', form).html(total);
		});

		$(form).on('click', '.select-on-check-all', function() {
			var selectedMe = $(this).prop('checked');
			var total = 0;
			if (selectedMe) {
				$('.select-on-check', form).each(function() {
					var item = $(this).parent().parent();

					var gstValue = parseFloat(item.find('td:nth-child(8)').html());
					if (isNaN(gstValue)) gstValue = 0;

					total += gstValue;
				});
			}

			if (total < 0) total = 0;
			total = myApp.formatNumber(total, 2, '.', ',');

			$('#total', form).html(total);
		});
	});
</script>