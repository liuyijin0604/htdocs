<h1>Bank Overview</h1>

<div class="bank-summary" style="margin:0;">
	<div class="balance">
		<div class="statement-balance">
			<label>Statement Balance: </label><span data-automationid="statementBalance" id="statementBalance"><?php echo number_format($balance, 2); ?></span>(AUD)
			&nbsp;&nbsp;<label>Reconciled Amount: </label><span data-automationid="reconciledAmount" id="reconciledAmount"><?php echo number_format($reconciled, 2); ?></span>(AUD)
			&nbsp;&nbsp;<label>Unreconciled Amount: </label><span data-automationid="unreconciledAmount" id="unreconciledAmount"><?php echo number_format($unreconciled, 2); ?></span>(AUD)
			&nbsp;&nbsp;<label>Opening Amount: <?php echo number_format($opening['amount'], 2); ?>(AUD) as <?=$opening['date']?></label>
		</div>
	</div>
</div>


<div style="text-align:right">
	<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-96px -768px" class="icon"></div>Options</a> &nbsp;
</div>

<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('bank/importStatement');?>" target="_blank">Import a statement</a></li>
		<li><a class="jqm_link" href="<?=$this->createUrl('bank/syncXero');?>" title="Sync With Xero">Sync With Xero</a></li>
	</ul>
</div>


<div id="pcae-banks-tabs">
	<ul>
		<?php
		$tabs = array(
		  //  array('reconcile', $this->t('Reconcile')),
			array('unrec_transactions', $this->t('Unreconciled Bank Transactions')),
			array('rec_transactions', $this->t('Reconciled')),
			array('all_transactions', $this->t('All')),
			// array('transactions', $this->t('Accounting Transactions')),
		);
		foreach($tabs as $tab){
			if(Acl::hasAccess($this->CaName.'/'.$tab[0]) && $tab[1]){
				$href = strpos($tab[0], '/') === false? $this->createUrl('bank/overview',array( 'tab'=>$tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
				echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
			}
		}
		?>
	</ul>
</div>
<script type="text/javascript">
	$(function(){
		var tab = $('#<?=$_GET["tabid"];?>');
		$('#pcae-banks-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
			myApp.ajaxifyForm(this);
		}});
	});
</script>
