<?php
function addHistoryItem($laptop, $student, $action, $data = array())
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
	return mysql_query("INSERT INTO history (laptop, student,action,data,timestamp) VALUES(".$laptop.", ".$student.", ".$action.", '".$data."', ".time().")");
}

function getHTMLForHistory($history, $laptops = false)
{
	global $issueTypes;
	$output = "";
	foreach ($history as $row)
	{
		if ( $row['action'] == ACTION_CREATE )
		{
			$output .= "<div class=\"alert action-info\"><strong>Created</strong><br>";
			$output .= ($laptops?$laptops[$row['laptop']['id']]['assetTag']:"This computer")." was added to the database.<br>";
			$output .= "<small>Recorded on ".date("M d, Y", $row['timestamp'])." at ".date("g:i A", $row['timestamp'])."</small>";
			$output .= "</div>";
		}
		else if ( $row['action'] == ACTION_UNASSIGN )
		{
			$output .= "<div class=\"alert\"><strong>Returned</strong><br>";
			$output .= ($laptops?$laptops[$row['laptop']['id']]['assetTag']:"This computer")." was returned.<br>";
			$output .= "<small>Recorded on ".date("M d, Y", $row['timestamp'])." at ".date("g:i A", $row['timestamp'])."</small>";
			$output .= "</div>";
		}
		else if ( $row['action'] == ACTION_ASSIGN )
		{
			$output .= "<div class=\"alert alert-success\"><strong>Assigned</strong><br>";
			$output .= ($laptops?$laptops[$row['laptop']['id']]['assetTag']:"This computer")." was assigned to ".$row['student']."<br>";
			$output .= "<small>Recorded on ".date("M d, Y", $row['timestamp'])." at ".date("g:i A", $row['timestamp'])."</small>";
			$output .= "</div>";
		}
		else if ( $row['action'] == HISTORYEVENT_SERVICE )
		{
			$output .= "<div class=\"alert alert-info\"><strong>Service - ".$issueTypes[$row['data']['type']]."</strong><br>";
			$output .= $row['data']['notes']."<br>";
			$output .= "<small>Recorded on ".date("M d, Y", $row['timestamp'])." at ".date("g:i A", $row['timestamp'])."</small>";
			$output .= "</div>";
		}
	}
	return $output;
}

?>