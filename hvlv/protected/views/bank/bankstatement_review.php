<h1>The Statement Lines </h1>
<br/>
<div class="bank-statement-detail">

	<div style="display: -webkit-inline-box;">
		<div style="width: 188px;"></div>
		<div style="width: 96px; text-align: right; padding-right: 8px;">Spent</div>
		<div style="width: 96px; text-align: right; padding-right: 8px;" >Received</div>
	</div>

	<div class="statement no-shadow">
		<div class="info">
			<div class="details"><span style="float: left"><span style="color:#000000;font-weight: bold;">Date:</span> <?php echo $model->created; ?></span><span style="float: left;"><?php  echo $model->desc; ?></span></div>
			<div class="amount"> <?php echo number_format($model->debits,2);?></div>
			<div class="amount received" ><?php echo number_format($model->credits,2);?></div>
		</div>
	</div>

</div>

<br/>
<h1>Has been reconciled with the following invoices...</h1>
<div id="all-payments" class="grid-view" style="width: 60%;">
	<table class="items">
		<thead>
		<tr>
			<th>Date</th>
			<th>Customer</th>
			<th>Invoice no.</th>
			<th>Amount</th>
			<th></th>
		</tr>
		</thead>
		<tbody id="body-payments">
		<?php
		foreach ( $details as $payment ) {
			foreach ( $payment as $r ) {
				echo '<tr>';
				echo '<td>'. $r['date'] .'</td>';
				echo '<td>'. $r['cust'] .'</td>';
				echo '<td>'. $r['no'] .'</td>';
				if ( $r['spent'] > 0 ) {
					echo '<td>' . $r['spent'] . '</td>';
				} else if ( $r['received'] > 0 )  {
					echo '<td>' . $r['received'] . '</td>';
				}
				echo '<td>' . $r['view'] .'</td>';
				echo '</tr>';
			}
		}
		?>
		</tbody>
	</table>
</div>


<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id' => 'statements-undo-reconcile-form',
		'action' => $this->createUrl('bank/undoreconcile',['id' => $model->id]),
		'enableAjaxValidation'=>false,
	)); ?>

	<div class="row">
		<?php echo CHtml::submitButton($this->t('Undo Reconcile'), array('class' => 'undo-reconcile-btn')); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>

<script type="text/javascript">
	var mytab;

	$(function(){
		mytab = '#jqmw_<?=$_GET["tabid"];?>';
		var panel = $(mytab).data('panel');

		$('.undo-reconcile-btn',panel).click(function(e){
			if ( !confirm('Are you sure undo the reconciled statment ?') ) {
				e.preventDefault();
			}
		});

		$('#statements-undo-reconcile-form', panel).on('success', function(e, r) {
			setTimeout(function() {
				$('.popCancel', panel).trigger('click');
				$('#bank-rec-transactions-grid').yiiGridView('update');
				$('#bank-all-transactions-grid').yiiGridView('update');

				r.balance = r.balance ? r.balance : 0;
				r.reconciled = r.reconciled ? r.reconciled : 0;
				r.unreconciled = r.unreconciled ? r.unreconciled : 0;
				$('#statementBalance').text(r.balance.toFixed(2));
				$('#reconciledAmount').text(r.reconciled.toFixed(2));
				$('#unreconciledAmount').text(r.unreconciled.toFixed(2));
			}, 1000);
		});
	});

</script>