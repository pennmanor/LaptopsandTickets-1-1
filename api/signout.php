<?php
include_once("ApiInclude.php");
$key = $_POST["key"];
$data = $_POST["data"];

$output = Array(API_STATUS => "true", API_INFO => "Functioning normally.");
try{
	if(!$key || !$data)
		throw new Exception("No key and/or data provided.");
	$request = new Api($key);
	$decodedData = json_decode($data, true);
	if(!Student::exists($decodedData[API_DATA_ID]))
		throw new Exception("Student does not exist in database");
	$student = new Student($decodedData[API_DATA_ID]);
	switch($student->getStatus()){
		case LOGGED_OUT:
		$output[API_INFO] = $student->getStudentId()." is already logged out.";
		break;
		case LOGGED_IN:
		$output[API_INFO] = "Logging ".$student->getStudentId()." out.";
		$student->signout();
		$logger = new Log($request->getId(), $request->getName());
		$logger->signout($student->getStudentId());
		break;
	}
}
catch(Exception $e){
	$output[API_STATUS] = false;
	$output[API_INFO] = $e->getMessage();
}
echo json_encode($output);
?>
