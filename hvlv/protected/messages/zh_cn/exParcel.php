<?php
$g = include(dirname(__FILE__).DIRECTORY_SEPARATOR.'shipment.php');
return array_merge($g,array(
	'Create Export Shipment' => '新建出口运单',
	'HS Code' => 'HS 编码',
	'Item Name' => '品名',
	'Unit' => '单位',
	'Brand' => '品牌',
	'Model' => '规格',
	'Baby Formula' => '婴儿奶粉',
	'Milk Powder' => '成人奶粉',
	'Warnings' => '问题',
	'Check Image' => '检查面单',
	'Chinese ID Report' => '身份证报表',
	'Duplicate Report' => '重复件报表',
	'Refunded' => '已理赔',
	'Discarded' => '已销毁',
	'Check' => '需核实',
));