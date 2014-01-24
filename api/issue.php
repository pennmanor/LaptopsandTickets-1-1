<?php
include_once("ApiInclude.php");
$session = new UserSession();
$key = $_POST["key"];
$data = $_POST["data"];
$properties = Array();
$limited = Array();

$output = Array(API_SUCCESS => 1, API_STATUS => -1, API_INFO => "Functioning normally.", API_RESULT => "");
try{
	if($key)
		$request = new Api($key);
	else if(!$session->isHelper())
		throw new Exception("Incorrect correct permissions.");
	if(!$data)
		throw new Exception("No data provided.");
	$decodedData = json_decode($data, true);
	$by = $decodedData[API_DATA_BY];
	$for = $decodedData[API_DATA_FOR];
	$issues = Array();
	$allHistory = Laptop::getAllHistory();

	switch($decodedData[API_DATA_ACTION]){
		case API_ACTION_ALL:
		foreach ( $allHistory as $event )
		{
			if ( $event['type'] == HISTORYEVENT_SERVICE )
			{
				$issues[] = $event;
			}
		}
		$output[API_INFO] = "Getting all issues.";
		break;
		case API_ACTION_GET:
		if(!$by || !$for)
			throw new Exception("Cannot get laptops, No \"by\" and/or \"for\" data provided.");
		throw new Exception("Getting Issues is not supported.");
		break;
		case API_ACTION_SEARCH:
		if(!$by)
			throw new Exception("Cannot search laptops, No \"by\" data provided.");
		if($by == "all") {
			foreach ( $allHistory as $event ) {
				if(strpos($event['body'], $for) !== false) {
					$issues[] = $event;
				}
			}
			
		}
		else {
			foreach ( $allHistory as $event ) {
				if($event['type'] = $by && strpos($event['body'], $for) !== false) {
					$issues[] = $event;
				}
			}
		}
		$output[API_INFO] = "Searching \"".$by."\" for \"".$for."\"";
		break;
		default:
		throw new Exception("Invalid action. This may have happened because you failed to provide the correct information. ");
		
	}
	if(is_array($issues)){
		foreach($issues as $issue){
			$laptop = Laptop::getByProperty(PROPERTY_ID, $issue['laptop']);
			$properties[] = Array("issue" => $issue, "laptop" =>$laptop->getProperties());
		}
		$output[API_RESULT] = $properties;
	}
}
catch(Exception $e){
	$output[API_SUCCESS] = 0;
	$output[API_INFO] = $e->getMessage();
}
echo json_encode($output);
?>
