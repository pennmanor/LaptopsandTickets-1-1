<?php
/*
  Copyright 2013 Penn Manor School District, Andrew Lobos, and Benjamin Thomas

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/
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
		default:
		case HISTORYEVENT_SIGNOUT:
		$output[API_STATUS] = HISTORYEVENT_SIGNIN;
		$output[API_INFO] = "Logging ".$helper->getID()." in.";
		$helper->signin($request->getID(), $request->getName());
		break;
		case HISTORYEVENT_SIGNIN:
		$output[API_INFO] = $helper->getID()." is already logged in.";
		break;
	}
}
catch(Exception $e){
	$output[API_SUCCESS] = 0;
	$output[API_INFO] = $e->getMessage();
}
echo json_encode($output);
?>
