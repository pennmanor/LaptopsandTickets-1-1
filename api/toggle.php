<?php
include_once("ApiInclude.php");
$key = $_POST["key"];
$data = $_POST["data"];

$output = Array(API_SUCCESS => 1, API_STATUS => -1, API_INFO => "Functioning normally.");
try{
	if(!$key || !$data)
		throw new Exception("No key and/or data provided.");
	$request = new Api($key);
	$decodedData = json_decode($data, true);
	if(!Helper::exists($decodedData[API_DATA_ID]))
		throw new Exception("Helper does not exist in system");
	$helper = new Helper($decodedData[API_DATA_ID]);
	switch($helper->IsSignedIn()){
		case HISTORYEVENT_SIGNIN:
		$output[API_STATUS] = HISTORYEVENT_SIGNOUT;
		$output[API_INFO] = "Logging ".$helper->getID()." out.";
		$helper->signout($request->getID(), $request->getName());
		break;
		default:
		case HISTORYEVENT_SIGNOUT:
		$output[API_STATUS] = HISTORYEVENT_SIGNIN;
		$output[API_INFO] = "Logging ".$helper->getID()." in.";
		$helper->signin($request->getID(), $request->getName());
		break;
	}
}
catch(Exception $e){
	$output[API_SUCCESS] = 0;
	$output[API_INFO] = $e->getMessage();
}
echo json_encode($output);
?>
