<?php
include_once("ApiInclude.php");
$session = new UserSession();
$key = $_POST["key"];
$data = $_POST["data"];
$properties = Array();

$output = Array(API_SUCCESS => 1, API_STATUS => -1, API_INFO => "Functioning normally.", API_RESULT => "");
try{
	if($key)
		$request = new Api($key);
	else if(!$session->isAuthenticated())
		throw new Exception("No key provided.");
	if(!$data)
		throw new Exception("No data provided.");
	
	$decodedData = json_decode($data, true);
	$by = $decodedData[API_DATA_BY];
	$for = $decodedData[API_DATA_FOR];
	switch($decodedData[API_DATA_ACTION]){
		case API_ACTION_ALL:
		$tickets = Ticket::getAll();
		break;
		case API_ACTION_GET:
		if(!$by || !$for)
			throw new Exception("Cannot get tickets, No \"by\" and/or \"for\" data provided.");
		$tickets = Ticket::getAllByProperty($by, $for);
		break;
		case API_ACTION_SEARCH:
		if(!$by || !$for)
			throw new Exception("Cannot search tickets, No \"by\" and/or \"for\" data provided.");
		$tickets = Ticket::searchField($by, $for);
		break;
		default:
		throw new Exception("Invalid action.");
	}
	foreach($tickets as $ticket){
		if($session->isHelper() || $request)
			$properties[] = $ticket->getProperties();
		else
			if($ticket->getProperty("student") == $session->getID())
				$properties[] = $ticket->getProperties();
	}
	$output[API_RESULT] = $properties;
}
catch(Exception $e){
	$output[API_SUCCESS] = 0;
	$output[API_INFO] = $e->getMessage();
}
echo json_encode($output);
?>
