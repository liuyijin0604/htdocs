<h1><?=$this->t('Billing Stream Line');?> </h1>

<div style="right: 20px; position: absolute; z-index: 9999">
<!-- <a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-48px -688px" class="icon"></div> Export</a>
<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="<?=$this->createUrl('billing/export', array('t' => 'pcae_in_tla'))?>" class="jqm_link">PCAE in TLA</a></li>
		<li><a href="<?=$this->createUrl('billing/export', array('t' => 'tla_in_pcae'))?>" class="jqm_link">TLA in PCAE</a></li>
	</ul>
</div> -->
<a href="<?=$this->createUrl('billing/export', array('t' => Yii::app()->name == 'TLA' ? 'pcae_in_tla' : 'tla_in_pcae'))?>" class="jqm_link"><div style="background-position:-48px -688px" class="icon"></div> Invoice Report</a>
</div>

<div id="billing-stream-line-tabs">
	<ul>
		<?php
		$tabs = array(
			array('dispute', $this->t('Dispute Tasks'),true,'billing/disputeTask'),
			array('accentry', $this->t('Entry')),
			array('importneedcheck', $this->t('Need Check'), true),
			// array('check', $this->t('New Check')),
			array('post', $this->t('Post')),
			array('arrange', $this->t('Arrange Payment')),
			array('approve_payment_to_pay', $this->t('Approve Payment To Pay')),
			array('pay', $this->t('Pay')),
			array('billingall', $this->t('All')),
		);
		foreach ($tabs as $tab) {
			if (Acl::hasAccess($this->CaName . '/' . $tab[0]) && $tab[1]) {
				if(!empty($tab[3]))
				{
					$href = strpos($tab[0], '/') === false ? $this->createUrl('billing/disputeTask', array('tab' => $tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
						echo '<li><a href="' . $href . '">' . $tab[1] . '</a></li>';
					continue;
				}
				
				if($tab[0]=="approve_payment_to_pay")
				{
					if(Acl::hasAccess("D:Billing/streamline/approve_payment_to_pay"))
					{
						$href = strpos($tab[0], '/') === false ? $this->createUrl('billing/accUpdate', array('tab' => $tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
						echo '<li><a href="' . $href . '">' . $tab[1] . '</a></li>';
					}
				}else
				{
					$href = strpos($tab[0], '/') === false ? $this->createUrl('billing/accUpdate', array('tab' => $tab[0], "tabid" => $_GET["tabid"])) : $tab[0];
						echo '<li><a href="' . $href . '">' . $tab[1] . '</a></li>';
				}
			}
		}
		?>
	</ul>
</div>
<script type="text/javascript">
	$(function(){
		var tab = $('#<?=$_GET["tabid"];?>');
		$('#billing-stream-line-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab']) ? 0 : $_GET['actab']; ?>, load: function(event,ui){
			myApp.ajaxifyForm(this);
		}});
	});
</script>
