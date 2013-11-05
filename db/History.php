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
		if ( $d['subtype'] == $issueType )
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
 * @param $body A string to be associated with this 
 * @param $tOffset The time offset for this event. Useful when adding multiple items that need to be in a certain order. Defaults to zero.
 * @return true on success, false on failure
 */
function addHistoryItem($laptop, $student, $action, $body ="", $subtype = 0, $tOffset = 0)
{
	global $mysql;
	if ( @get_class($student) == "Student" )
		$student = $student->getID();
	if ( @get_class($laptop) == "Laptop" )
		$laptop = $laptop->getID();
	if ( ($laptop = intval($laptop)) == 0 )
		return false;
	if ( ($subtype = intval($subtype)) == 0 && !is_numeric($subtype) )
		return false;

	$student = real_escape_string($student);
	$body = real_escape_string($body);
	if ( ($action = intval($action)) == 0 )
		return false;
	return $mysql->query("INSERT INTO history (laptop, student,type,body,subtype,timestamp, ticket) VALUES(".$laptop.", '".$student."', ".$action.", '".$body."', ".$subtype.", ".(time()+$tOffset).", 0)");
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
	return $mysql->query("INSERT INTO history (laptop, ticket, student,type,data,timestamp) VALUES(".$laptop.", ".$ticket.", '".$student."', ".$action.", '".$data."', ".(time()+$tOffset).")");
}


?>