<?php
header('Content-Type: application/json');

require_once('db.php');

$sqlQuery = "SELECT N,P,K,temperature,humidity,ph,rainfall FROM data";
$sqlQuery1 = "SELECT distinct(label) FROM smart_aggriculture.data";
$con = mysqli_connect("localhost:3306","root","Naren@03561","smart_aggriculture");
$result = mysqli_query($con,$sqlQuery);
$label = mysqli_query($con,$sqlQuery1);
$data = array();
foreach ($label as $row) {
	$data[] = $row;
}

echo json_encode($data);
mysqli_close($con);
?>