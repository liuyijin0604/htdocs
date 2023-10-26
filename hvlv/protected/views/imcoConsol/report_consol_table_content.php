<?php
$dicPort2State = [
	'AUSYD' => 'NSW',
	'AUMEL' => 'VIC',
	'AUBNE' => 'QLD',
	'AUPER' => 'WA',
	'AUADL' => 'SA',
	'AUFRE'=>'WA',
	// 'AUOOL'=>'AUOOL',
	// 'EGCAI'=>'EGCAI',
	// 'KRPUS'=>'KRPUS',
	// 'NZAKL'=>'NZAKL',
	// 'THBKK'=>'THBKK',
	// 'USEWR'=>'USEWR',
	// 'USSFO'=>'USSFO',
];
foreach ($listRecord as $i => $objRecord) {
	echo '<tr class="' . ($i % 2 == 0 ? 'odd' : 'even') . '">';
	echo '<td>' . $objRecord->customer . '</td>';
	echo '<td>' . $objRecord->no . '</td>';
	echo '<td>' . Imcoconsol::$states[$objRecord->status] . '</td>';
	echo '<td>' . $dicPort2State[$objRecord->pod] . '</td>';
	echo '<td>' . Imcoconsol::$services[$objRecord->service] . '</td>';
	echo '<td>' . $objRecord->awb . '</td>';
	echo '<td>' . $objRecord->airline . '</td>';
	echo '<td>' . date("m-d", strtotime($objRecord->etd)) . '</td>';
	echo '<td>' . date("m-d", strtotime($objRecord->eta)) . '</td>';
	echo '<td>' . date("m-d", strtotime($objRecord->discharge)) . '</td>';
	

	$numDayPickup = $objRecord->days_pickup;
	$numDayUnpack = $objRecord->days_unpack;

	$strColorPickup = 'black';
	$strColorUnpack = 'black';
	if($objRecord->service == ImcoConsol::AIRCONSOL){
		if ($numDayPickup > 2) {
			$strColorPickup = 'red';
		} else if ($numDayPickup > 1) {
			$strColorPickup = 'orange';
		} else if ($numDayPickup > 0) {
			$strColorPickup = '#BBBB00';
		}
		if ($numDayUnpack > 3) {
			$strColorUnpack = 'red';
		} else if ($numDayUnpack > 2) {
			$strColorUnpack = 'orange';
		} else if ($numDayUnpack > 1) {
			$strColorUnpack = '#BBBB00';
		}
	}

	if ($objRecord->service == ImcoConsol::SEACONSOL) {
		if ($numDayPickup > 5) {
			$strColorPickup = 'red';
		} else if ($numDayPickup > 4) {
			$strColorPickup = 'orange';
		} else if ($numDayPickup > 3) {
			$strColorPickup = '#BBBB00';
		}
		if ($numDayUnpack > 6) {
			$strColorUnpack = 'red';
		} else if ($numDayUnpack > 5) {
			$strColorUnpack = 'orange';
		} else if ($numDayUnpack > 4) {
			$strColorUnpack = '#BBBB00';
		}
	}

	if (!empty($objRecord->pickup)) {
		echo '<td>' . date("m-d", strtotime($objRecord->pickup)) . ' <span style="color:' . $strColorPickup . '">(' . $numDayPickup . 'D)</span></td>';
	} else {
		echo '<td></td>';
	}

	if (!empty($objRecord->unpack)) {
		echo '<td>' . date("m-d", strtotime($objRecord->unpack)) . ' <span style="color:' . $strColorUnpack . '">(' . $numDayUnpack . 'D)</span></td>';
	} else {
		echo '<td></td>';
	}

	echo '</tr>';
}