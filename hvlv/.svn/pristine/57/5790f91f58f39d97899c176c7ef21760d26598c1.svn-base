<div style="right: 20px; position: absolute;">

	<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-import-reconciliation-declare-dropdown"><div style="background-position:-48px -688px" class="icon"></div>Import Reconciliation Declare</a>
	<div id="<?=$_GET["tabid"];?>-import-reconciliation-declare-dropdown" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
		<ul class="dropdown-menu">
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/d2zReconciliationDeclare');?>" target="_blank">D2z Reconciliation Declare</a></li>
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/d2zReconciliationRTS');?>" target="_blank">D2z Reconciliation RTS</a></li>
		</ul>
	</div>

	<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-import-reconciliation-dropdown"><div style="background-position:-48px -688px" class="icon"></div>Import Reconciliation</a>
	<div id="<?=$_GET["tabid"];?>-import-reconciliation-dropdown" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
		<ul class="dropdown-menu">
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/reconciliationNew');?>" target="_blank">Fastway Old File</a></li>
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/fastwayReconciliationNew');?>" target="_blank">Fastway New File</a></li>
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/aupostReconciliationInvoice');?>" target="_blank">Aupost Invoice</a></li>
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/aupostReconciliation');?>" target="_blank">&nbsp;&nbsp;&nbsp;Aupost Manifest</a></li>
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/aupostReconciliationRTS');?>" target="_blank">&nbsp;&nbsp;&nbsp;Aupost RTS</a></li>
			<!-- <li><a class="jqm_link" href="<?=$this->createUrl('invoice/aupostReconciliationLetter');?>" target="_blank">Aupost Letter</a></li> -->
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/startrackReconciliation');?>" target="_blank">Startrack</a></li>
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/tntReconciliationNew');?>" target="_blank">TNT</a></li>
			<li class="divider">-------------<b>Not Use</b>-------------<br /></li>
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/hunterReconciliation');?>" target="_blank">HUNTER</a></li>
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/d2zReconciliation');?>" target="_blank">D2z</a></li>
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/ecofReconciliation');?>" target="_blank">ECOF</a></li>
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/globavendReconciliation');?>" target="_blank">Globavend</a></li>
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/ubiReconciliation');?>" target="_blank">UBI</a></li>
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/ubiReconciliationRTS');?>" target="_blank">UBI RTS</a></li>
		</ul>
	</div>
	<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-192px -80px" class="icon"></div> Actions</a>
	<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
		<ul class="dropdown-menu">
			<li><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('invoice/exportReconciliation', ['type' => 'search']);?>" target="_blank">Export Current Search</a></li>
			<?php if (isset(Yii::app()->user->grp) && Yii::app()->user->grp == 0) { ?>
				<li><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('invoice/exportAupostReconciliation', ['type' => 'search']);?>" target="_blank">Export Aupost</a></li>
			<?php } ?>
			<li><a class="jqm_link" href="<?=$this->createUrl('invoice/toolsInvoiceDiff');?>" target="_blank">Tools Invoice Diff</a></li>
		</ul>
	</div>

	<!-- <a class="tab_link" href="<?=$this->createUrl('invoice/portInvoiceReconciliation');?>" title="Port Invoice Reconciliation"><div class="icon" style="background-position:-16px 0"></div>Port Invoice Reconciliation</a> -->

</div>

<h1><?=$this->t('Reconciliations');?> </h1>

<div id="reconciliation-tabs">
	<ul>
		<?php
		$tabs = array(
			array('invoice', $this->t('Invoices'), true),
			array('aupost_mani', $this->t('Aupost Manifest')),
			array('aupost_rts', $this->t('Aupost RTS')),
		);
		foreach ($tabs as $tab) {
			if (Acl::hasAccess($this->CaName . '/' . $tab[0]) && $tab[1]) {
				$href = strpos($tab[0], '/') === false ? $this->createUrl('invoice/reconciliation', array('tab' => $tab[0], 'tabid' => $_GET['tabid'])) : $tab[0];
				echo '<li><a href="' . $href . '">' . $tab[1] . '</a></li>';
			}
		}
		?>
	</ul>
</div>
<script type="text/javascript">
	$(function() {
		var tab = $('#<?=$_GET["tabid"];?>');
		$('#reconciliation-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab']) ? 0 : $_GET['actab']; ?>, load: function(event,ui) {
			myApp.ajaxifyForm(this);
		}});

		var panel = tab.data('panel');
		$('a.export_search', panel).on('mousedown', function(){
			var q = $('.filters input, .filters select', panel).serialize();
			var href = $(this).data('baseurl') + '&' + q;
			href = href.replace('.app&', '?');
			$(this).attr('href', href);
		});
	});
</script>
