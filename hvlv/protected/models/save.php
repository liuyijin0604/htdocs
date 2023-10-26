<?php

// save.php

// Assuming you have already connected to the database

// Retrieve the form data
$awb = $_POST['awb'];
$ctoStart = $_POST['ctoStart'];
$ctoFinish = $_POST['ctoFinish'];
$clientStart = $_POST['clientStart'];
$clientFinish = $_POST['clientFinish'];

// Find the corresponding bwtrunk record
$bwtrunk = Bwtrunk::model()->findByAttributes(array('MAWB_number' => $awb));

// Update the values
$bwtrunk->CTO_start = $ctoStart;
$bwtrunk->CTO_finish = $ctoFinish;
$bwtrunk->Client_start = $clientStart;
$bwtrunk->Client_finish = $clientFinish;

// Save the changes
$bwtrunk->save();

// Redirect back to the page or perform any other necessary actions
header('Location: index.php');
exit();
?>
