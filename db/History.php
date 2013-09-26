<?php

/**
 * Get all of the Laptop issues of a type
 * @param $issueType The Laptop Service issue type
 * @return An array of Laptops that have a service issue with the provided type.
 */
function getLaptopsByIssueType($issueType)
{
	global $mysql;
	$output = array();
	$result = $mysql->query("SELECT * FROM `history` WHERE `action` = 4");
	
	while ( $d = mysqli_fetch_array($result) )
	{
		$data = unserialize($d['data']);
		if ( $data['type'] == $issueType )
		{
			// Prevent duplicates (ex: one laptop with two keyboard issues logged )
			$duplicate = false;
			foreach ( $output as $laptop )
			{
				if ( $d['laptop'] == $laptop->getID() )
					$duplicate = true;
			}
			
			if ( !$duplicate )
				$output[] = new Laptop($d['laptop']);
		}
	}
	
	return $output;
}

/** 
 * Add a history item to the database
 * @param $laptop The laptop context. 0 if none
 * @param $student The student context. -1 if none
 * @param $action The action type
 * @param $data The data as an array to be associated with this entry. Defaults to empty array
 * @param $tOffset The time offset for this event. Useful when adding multiple items that need to be in a certain order. Defaults to zero.
 * @return true on success, false on failure
 */
function addHistoryItem($laptop, $student, $action, $data = array(), $tOffset = 0)
{
	global $mysql;
	if ( @get_class($student) == "Student" )
		$student = $student->getID();
	if ( @get_class($laptop) == "Laptop" )
		$laptop = $laptop->getID();
	if ( ($laptop = intval($laptop)) == 0 )
		return false;
	$student = real_escape_string($student);
	if ( ($action = intval($action)) == 0 )
		return false;
	$data = real_escape_string(serialize($data));
	return $mysql->query("INSERT INTO history (laptop, student,action,data,timestamp, ticket) VALUES(".$laptop.", '".$student."', ".$action.", '".$data."', ".(time()+$tOffset).", 0)");
}

/** 
 * Add a history item to the database. This function is different from addHistoryItem() because it adds ticket context.
 * @param $laptop The laptop context. 0 if none
 * @param $ticket The ticket context. 0 if none
 * @param $student The student context. -1 if none
 * @param $action The action type
 * @param $data The data as an array to be associated with this entry. Defaults to empty array
 * @param $tOffset The time offset for this event. Useful when adding multiple items that need to be in a certain order. Defaults to zero.
 * @return true on success, false on failure
 * TODO: Update calls of addHistoryItem to pass ticket context and rename this to addHistoryItem
 */

function addTicketHistoryItem($laptop, $ticket, $student, $action, $data = array(), $tOffset = 0)
{
	global $mysql;
	if ( @get_class($student) == "Student" )
		$student = $student->getID();
	if ( @get_class($laptop) == "Laptop" )
		$laptop = $laptop->getID();
	if ( @get_class($ticket) == "Ticket" )
		$ticket = $ticket->getID();
	if ( ($laptop = intval($laptop)) == 0 )
		return false;
	if ( ($ticket = intval($ticket)) == 0 )
		return false;
	$student = real_escape_string($student);
	if ( ($action = intval($action)) == 0 )
		return false;

	$data = real_escape_string(serialize($data));
	return $mysql->query("INSERT INTO history (laptop, ticket, student,action,data,timestamp) VALUES(".$laptop.", ".$ticket.", '".$student."', ".$action.", '".$data."', ".(time()+$tOffset).")");
}


?>