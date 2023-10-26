<?php

$filtersForm = new FiltersForm;
// $filtersForm1 = new FiltersForm1;
if (isset($_GET['FiltersForm'])) {
	$filtersForm->filters = $_GET['FiltersForm'];
}
// if (isset($_GET['FiltersForm1'])) {
// 	$filtersForm1->filters = $_GET['FiltersForm1'];
// }
$provide = [];
$exchange_rate = Currency::getExrate()[0];
$value = 1000 * $exchange_rate;
/**Calculate the number based on the status **/
$timeRange = [1 => [-1, 5], 2 => [5, 365]];
$sql = "SELECT * ,DATEDIFF(NOW(),date) as day_diff FROM `shipment_process` t WHERE status!=24";
$allLiveProcess = Yii::app()->db->createCommand($sql)->queryAll();
foreach ($allLiveProcess as $oneProcess) {
	foreach (ShipmentProcess::$processTypes as $typeValue => $statusRange) {
		if (in_array($oneProcess['status'], $statusRange)) {
			if ($oneProcess['day_diff'] < 3) {
				if (isset(${$typeValue . 'n1'})) {
					${$typeValue . 'n1'} += 1;
				} else {
					${$typeValue . 'n1'} = 1;
				}
			} elseif ($oneProcess['day_diff'] < 5) {
				if (isset(${$typeValue . 'n3'})) {
					${$typeValue . 'n3'} += 1;
				} else {
					${$typeValue . 'n3'} = 1;
				}
			} else {
				if (isset(${$typeValue . 'n5'})) {
					${$typeValue . 'n5'} += 1;
				} else {
					${$typeValue . 'n5'} = 1;
				}
			}
		}
	}
}
$anotherProvide = [];
$index = 1;
foreach (ShipmentProcess::$processTypesMap as $key => $value) {
	if (isset(${$key . 'n1'}) || isset(${$key . 'n3'}) || isset(${$key . 'n5'})) {
		$anotherProvide[] = [
		'id' => $index++, 
		'name' => $value, 'type' => $key, 
		'n1' => isset(${$key . 'n1'}) ? ${$key . 'n1'} : 0, 
		'n3' => isset(${$key . 'n3'}) ? ${$key . 'n3'} : 0, 
		'n5' => isset(${$key . 'n5'}) ? ${$key . 'n5'} : 0];
	}
}
// $filteredData1 = $filtersForm1->filter($anotherProvide);
// $dataprovider1 = new CArrayDataProvider($filteredData1);

//pre the top table data:
$returnStatics = ShipmentProcess::getStaticOnDayRange();
$provide = $returnStatics['provide'];

$filteredData = $filtersForm->filter($provide);
$dataprovider = new CArrayDataProvider($filteredData);
$dataprovider->pagination = ['pageSize' => 15];
$sort = new CSort();
$sort->attributes = [
	'number' => [
		'asc' => 'number ASC',
		'desc' => 'number DESC',
	],
	'owner' => [
		'asc' => 'owner ASC',
		'desc' => 'owner DESC',
	],
];
$sort->defaultOrder = "number DESC";
$dataprovider->sort = $sort;
$dataProvider= [$dataprovider, $filtersForm];
// $dataProvider1= [$dataprovider1, $filtersForm1];
?>
<table>
	<tr><td width="60%">
			<div style="width:100%" id="custom-client-view">
			<?php
			$this->widget('zii.widgets.grid.CGridView', [
				'id' => $_GET['tabid'].'custom-client-list-grid',
				'htmlOptions' => ['style' => 'width: 90%'],
				'afterAjaxUpdate'=>'function(r,s){$("#custom_summary").html($(s).find("#custom_summary").html());}',
				'cssFile' => false,
				'dataProvider' => $dataProvider[0],
				'filter' => $dataProvider[1],
				'columns' => [
				['name' => 'agent_id', 'headerHtmlOptions' => ['style' => 'display:none'], 'filterHtmlOptions' => ['style' => 'display:none'],
					'htmlOptions' => ['style' => 'display:none'], 'type' => 'raw'],
				['name' => 'owner', 'type' => 'raw'],
				'number',
				['name' => 'day1', 'header' => '0-3 Days', 'type' => 'raw'],
				['name' => 'day2', 'header' => '3-5 Days', 'type' => 'raw'],
				['name' => 'day3', 'header' => '5-7 Days', 'type' => 'raw', 'cssClassExpression' => '$data>0? "cloumn_red_1" : ""'],
				['name' => 'day4', 'header' => '7+ Days', 'type' => 'raw', 'cssClassExpression' => '$data>0? "cloumn_red_1" : ""'],
				],
			]);
			?>
		</div>
		</td>
<!-- 		<td width="40%" valign="top">
			<div style="width:100%;" id="custom-client-view-detail">
			<?php
			// $this->widget('zii.widgets.grid.CGridView', [
			// 	'id' => 'custom-client-list-grid-detail',
			// 	'htmlOptions' => ['style' => 'width: 90%'],
			// 	'afterAjaxUpdate'=>'function(r,s){$("#custom_summary").html($(s).find("#custom_summary").html());}',
			// 	'cssFile' => false,
			// 	'dataProvider' => $dataProvider1[0],
			// 	//                'filter' => $dataProvider1[1],
			// 	'columns' => [
			// 	['name' => 'type', 'headerHtmlOptions' => ['style' => 'display:none'], 'filterHtmlOptions' => ['style' => 'display:none'],
			// 		'htmlOptions' => ['style' => 'display:none'], 'type' => 'raw'],
			// 	['name' => 'name', 'header' => 'status'],
			// 	['name' => 'n1', 'header' => '0-3 Days', 'type' => 'raw'],
			// 	['name' => 'n3', 'header' => '3-5 Days', 'type' => 'raw'],
			// 	['name' => 'n5', 'header' => '5+ Days', 'type' => 'raw', 'cssClassExpression' => '$data["n5"]>0? "cloumn_red_1" : ""'],
			// 	],
			// ]);
			?>
		</div> 
		</td> -->
	</tr>
</table>
<br/>


		