<ul>
<?php
$menu = array(
	'Exports' => array(
		'items' => array(
			//array('excoConsol/create', 'New Consol.'),
			array('excoConsol/createplus', 'New Consol.+'),
			array('excoConsol/list', 'Manage Consol.'),
			//array('exParcel/create', 'New Shipment'),
			array('exParcel/list', 'Manage Shipment'),
			array('pickupList/list', 'Receipt List'),
			array('exChannel/list', 'Manage Channels'),
			array('exParcel/lsx', '流水线'),
		),
		'pinned' => true,
	),
	'Manifest' => array(
		'items' => array(
			array('manifest/upload', 'Upload Manifest'),
			array('manifest/list', 'Manage Manifest'),
		),
	),
	'Accounting' => array(
		'items' => array(
			array('invoice/list', 'Invoices'),
			array('payment/list', 'Receipt'),
			array('consolWeightCheck/list', 'Consol. Cost Check'),
		),
	),
	'Organisation' => array(
		'items' => array(
			//array('org/create', 'New Organisation'),
			array('org/list', 'Manage Organisations'),
			array('orgContact/list', 'Org. Contacts'),
		),
	),
	'Reports' => array(
		'items' => array(
			// array('report/shipments', 'Shipments Report'),
			// array('report/consol', 'Consol Report'),
			array('report/account', 'Account Report'),
			// array('report/chart', 'Charts'),
			array('report/holdAgent', 'Hold Agent Report'),
			// array('import/report', 'Import Client Report'),
			// array('report/plReport', 'P & L Report'),
			// array('report/implReport', 'Import P&L Report'),
			// array('shipmentScan/index', 'WHScan report'),
			// array('customProcess/report','Customs Process Report'),
			// array('shipmentErrRecord/index','输单错误报告'),
			// array('imParcel/mixedMildsReport','Mixed eParcel cost Report'),
			// array('imParcel/dailyEparcelSend','Daily Delivery To Melbourne Report'),
			// array('report/cbReport','Courier cubic Report'),
			array('report/rebate', 'Org Rebate Report')
		),
	),
	'Reference' => array(
		'items' => array(
			array('cnID/list', 'Manage CnID'),
			array('exprod/list', 'Manage Products'),
		),
	),
	'CRM' => array(
		'items' => array(
			  array('exCrm/crmManage','Export CRM Manage'),
			  array('exCrm/ticketList','Export Tickets'),
			  array('exCrm/report','Export CRM Report'),
			  array('crmmsg/site/index', 'CRM MSG', true, true),
			  array('crmmsg/massmsg/list', 'CRM MASS MSG')
			 ) ,
	),

	'Consumables' => array(
		'items' => array(
			// array('Cgoods/vendorList','Manage Vendors'),
			// array('Cgoods/goodsList','Manage Goods'),
			array('Cgoods/orders', 'Orders'),
			array('Cgoods/jobs', 'Batched Orders'),
			array('Cgoods/products', 'Manage Products'),
			array('Cgoods/stocks', 'Manage Stocks'),
			array('Cgoods/settings', 'Manage Settings'),
			array('Cgoods/reports', 'Reports'),
		),
	),

	'System' => [
		'items' => [
			['user/profile', 'My Info', true],
			['user/list', 'Manage User'],
		],
	],
);

foreach($menu as $h=>$sm){
	$l = '';
	foreach($sm['items'] as $m){
		if(!empty($m['items'])){
			$l .= '<li><a href="javascript:void(0)">'.$m[0].' &raquo;</a><ul class="ext_menu">';
			foreach($m['items'] as $m2){
							if(!empty($m2['items'])){
								$l .= '<li><a href="javascript:void(0)">'.$m2[0].' &raquo;</a><ul class="ext_menu">';
							   foreach($m2['items'] as $m3){
								   if(!Acl::hasAccess('C:'.$m3[0]) && empty($m3[2])) continue;
				$m3[1] = $this->t($m3[1]);
				$l .= '<li><a href="'.$this->createUrl($m3[0]).'" ' . (!empty($m3[3]) ? 'target="_blank"' : 'class="menuLink"') . ' title="'.$m3[1].'">'.$m3[1].'</a></li>';
							   } 
							   $l .= '</ul></li>';
			   continue;
							}
				if(!Acl::hasAccess('C:'.$m2[0]) && empty($m2[2])) continue;
				$m2[1] = $this->t($m2[1]);
				$l .= '<li><a href="'.$this->createUrl($m2[0]).'" ' . (!empty($m2[3]) ? 'target="_blank"' : 'class="menuLink"') . ' title="'.$m2[1].'">'.$m2[1].'</a></li>';
			}
			$l .= '</ul></li>';
			continue;
		}
		if(!Acl::hasAccess('C:'.$m[0]) && empty($m[2])) continue;
		$m[1] = $this->t($m[1]);
		$l .= '<li><a href="'.$this->createUrl($m[0]).'" ' . (!empty($m[3]) ? 'target="_blank"' : 'class="menuLink"') . ' title="'.$m[1].'"' . (!empty($m[3]) ? ' target="_blank"' : '') . '>'.$m[1].'</a></li>';
	}
	if(!empty($l))	echo '<li><h2>',$this->t($h),'</h2><ul class="',(isset($sm['pinned']) && $sm['pinned']? 'menusection pinned' : 'menusection'),'">', $l , '</ul></li>';
}
?>
</ul>