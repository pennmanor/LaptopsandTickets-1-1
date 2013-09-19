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
	else if(!$session->isAuthenticated())
		throw new Exception("No key provided.");
	if(!$data)
		throw new Exception("No data provided.");
	$decodedData = json_decode($data, true);
	$by = $decodedData[API_DATA_BY];
	$for = $decodedData[API_DATA_FOR];
	$limits = $decodedData[API_LIMIT] ? $decodedData[API_LIMIT]:Array();
	if(!$session->isHelper()){
		if(!in_array(API_LIMIT_MY, $limits))
			$limits = array_merge(array(API_LIMIT_MY),$limits);
	}
	
	switch($decodedData[API_DATA_ACTION]){
		case API_ACTION_ALL:
		$tickets = Ticket::getAll();
		$output[API_INFO] = API_ACTION_ALL;
		break;
		case API_ACTION_GET:
		if(!$by || !$for)
			throw new Exception("Cannot get tickets, No \"by\" and/or \"for\" data provided.");
		$tickets = Ticket::getAllByProperty($by, $for);
		$output[API_INFO] = API_ACTION_GET;
		break;
		case API_ACTION_SEARCH:
		if(!$by)
			throw new Exception("Cannot search tickets, No \"by\" and/or \"for\" data provided.");
		$tickets = Ticket::searchField($by, $for);
		$output[API_INFO] = API_ACTION_SEARCH;
		break;
		default:
		throw new Exception("Invalid action \"".$decodedData[API_DATA_ACTION]."\".");
	}
	if(is_array($limits)){
		$output[API_INFO] = $output[API_INFO]." Limits: ";
		foreach($limits as $limit){
			$output[API_INFO] = $output[API_INFO].$limit.", ";
			foreach($tickets as $ticket){
				switch($limit){
					case API_LIMIT_MY:
					if($ticket->getProperty(PROPERTY_STUDENT) == $session->getID())
						$limited[] = $ticket;
					break;
					case API_LIMIT_OPEN:
					if($ticket->getProperty(PROPERTY_STATE) == TICKETSTATE_OPEN)
						$limited[] = $ticket;
					break;
					case API_LIMIT_CLOSED:
					if($ticket->getProperty(PROPERTY_STATE) == TICKETSTATE_CLOSED)
						$limited[] = $ticket;
					break;
					case API_LIMIT_ASSIGNED:
					if($ticket->getProperty(PROPERTY_HELPER) != NULL)
						$limited[] = $ticket;
					break;
					case API_LIMIT_UNASSIGNED:
					if($ticket->getProperty(PROPERTY_HELPER) == NULL)
						$limited[] = $ticket;
					break;
					case API_LIMIT_HELPER:
					if($ticket->getProperty(PROPERTY_HELPER) == $session->getID())
						$limited[] = $ticket;
					break;
					default:
					throw new Exception("Invalid limit \"".$limit."\".");
				}
			}
			$tickets = $limited;
			$limited = Array();
		}
	}
	if(is_array($tickets)){
		foreach($tickets as $ticket){
			$properties[] = $ticket->getProperties();
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
