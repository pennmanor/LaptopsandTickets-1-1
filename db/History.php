<?php
function addHistoryItem($laptop, $student, $action, $data = array(), $tOffset = 0)
{
	if ( @get_class($student) == "Student" )
		$student = $student->getID();
	if ( @get_class($laptop) == "Laptop" )
		$laptop = $laptop->getID();
	if ( ($laptop = intval($laptop)) == 0 )
		return false;
	if ( ($student = intval($student)) == 0 )
		return false;
	if ( ($action = intval($action)) == 0 )
		return false;
	$data = mysql_real_escape_string(serialize($data));
	return mysql_query("INSERT INTO history (laptop, student,action,data,timestamp, ticket) VALUES(".$laptop.", ".$student.", ".$action.", '".$data."', ".(time()+$tOffset).", 0)");
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
	if ( ($student = intval($student)) == 0 )
		return false;
	if ( ($action = intval($action)) == 0 )
		return false;

	$data = mysql_real_escape_string(serialize($data));
	return mysql_query("INSERT INTO history (laptop, ticket, student,action,data,timestamp) VALUES(".$laptop.", ".$ticket.", ".$student.", ".$action.", '".$data."', ".(time()+$tOffset).")");
}


?>