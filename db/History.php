<?php

function getLaptopsByIssueType($issueType)
{
	$output = array();
	$result = mysql_query("SELECT * FROM `history` WHERE `action` = 4");
	
	while ( $d = mysql_fetch_array($result) )
	{
		$data = unserialize($d['data']);
		if ( $data['type'] == $issueType )
		{
			// Prevent duplicates (ex: one laptop with two keyboard issues logged)
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

function addHistoryItem($laptop, $student, $action, $data = array(), $tOffset = 0)
{
	if ( @get_class($student) == "Student" )
		$student = $student->getID();
	if ( @get_class($laptop) == "Laptop" )
		$laptop = $laptop->getID();
	if ( ($laptop = intval($laptop)) == 0 )
		return false;
	$student = mysql_real_escape_string($student);
	if ( ($action = intval($action)) == 0 )
		return false;
	$data = mysql_real_escape_string(serialize($data));
	return mysql_query("INSERT INTO history (laptop, student,action,data,timestamp, ticket) VALUES(".$laptop.", '".$student."', ".$action.", '".$data."', ".(time()+$tOffset).", 0)");
}

function addTicketHistoryItem($laptop, $ticket, $student, $action, $data = array(), $tOffset = 0)
{
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
	$student = mysql_real_escape_string($student);
	if ( ($action = intval($action)) == 0 )
		return false;

	$data = mysql_real_escape_string(serialize($data));
	return mysql_query("INSERT INTO history (laptop, ticket, student,action,data,timestamp) VALUES(".$laptop.", ".$ticket.", '".$student."', ".$action.", '".$data."', ".(time()+$tOffset).")");
}


?>