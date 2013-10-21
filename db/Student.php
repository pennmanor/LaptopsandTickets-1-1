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
require_once("constants.php");

/**
 * Class for accessing Student data
 * @author Andrew
 */
class Student
{	
	/**
	 * Creates a Student
	 * @param $sid The student's ID
	 * @param $name The student's name
	 * @param $grade The student's grade
	 * @return A new student object on success, false otherwise
	 */
	public static function create($sid, $name, $grade)
	{
		global $mysql;
		if ( Student::getByProperty(PROPERTY_SID, $sid) )
			return false;
		$sid = real_escape_string($sid);
		$name = real_escape_string($name);
		if ( ($grade = intval($grade)) == 0 )
			return false;
		$result = $mysql->query("INSERT INTO students (sid, name, grade) VALUES('".$sid."', '".$name."', ".$grade.")");
		if ( !$result )
			return false;
		return new Student($sid);
	}
	
	/**
	 * Removes a student, preserving tickets and history
	 * @param $sid The student's ID
	 * @return true on success, false on failure
	 */
	public static function remove($sid)
	{
		global $mysql;
		$sid = real_escape_string($sid);
		return $mysql->query("DELETE FROM `students` WHERE `sid` = '".$sid."'");
	}
	
	/**
	 * Removes a student and all associated data (history and tickets)
	 * @param $sid The student's ID
	 * @return true on success, false on failure
	 */
	public static function nuke($sid)
	{
		global $mysql;
		$sid = real_escape_string($sid);
		
		$studentTicketRemoval = $mysql->query("DELETE FROM `tickets` WHERE `student` = '".$sid."'");
		$helperTicketRemoval = $mysql->query("DELETE FROM `tickets` WHERE `helper` = '".$sid."'");
		$historyRemoval = $mysql->query("DELETE FROM `history` WHERE `student` = '".$sid."'");
		$studentRemoval = $mysql->query("DELETE FROM `students` WHERE `sid` = '".$sid."'");
		
		return $studentTicketRemoval && $helperTicketRemoval && $historyRemoval && $studentRemoval;
	}
	
	/**
	 * Search for Student objects where $query is in the value of $property. It does not look for an exact match.
	 * @param $property The property to search in
	 * @param $query The query string to look for in $property
	 * @param $dupCheck Do not return any matching items already in this array. Useful when searching multiple properties. Defaults to an empty array.
	 * @return The matched Student objects that do not already exist in the $dupCheck array
	 */
	public static function searchField($property, $query, $dupCheck = array())
	{
		global $mysql;
		$query = real_escape_string($query);
		$result = $mysql->query("SELECT * FROM students WHERE ".$property." LIKE '%".$query."%'");
		if ( !$result || mysqli_num_rows($result) == 0 )
			return array();
		$output = array();
		
		while ( $d = mysqli_fetch_array($result) )
		{
			if ( !empty($d) )
			{
				$dup = false;
				foreach ( $dupCheck as $row )
				{
					if ( $row->getID() == $d['sid'] )
						$dup = true;
				}
				if ( !$dup )
					$output[] = new Student($d['sid']);
			}
		}
		return $output;
	}
	
	/**
	 * Wraper for searchField() that searches by id and name.
	 * @param $query The query to search for
	 * @return An array of the Student objects that match $query
	 */
	public static function search($query)
	{
		$output = array();
		$output = array_merge(Student::searchField(PROPERTY_SID, $query, $output), $output);
		$output = array_merge(Student::searchField(PROPERTY_NAME, $query, $output), $output);
		return $output;
	}
	
	/**
	 * Find all Students in the database
	 * @return An array of all Student objects in the database
	 */
	public static function getAll()
	{
		global $mysql;
		$output = array();
		
		$result = $mysql->query("SELECT `sid` FROM `students`");
		while ( $d = mysqli_fetch_array($result) )
		{
			if ( !empty($d) )
				$output[] = new Student($d['sid']);
		}
		return $output;
	}
	
	/**
	 * Find all Students in the database with a laptop assigned
	 * @return An array of all Student objects in the database with a laptop assigned
	 */
	public static function getAllWithLaptop()
	{
		global $mysql;
		$output = array();
		
		$result = $mysql->query("SELECT `sid` FROM `students` WHERE `laptop` <> 0");
		while ( $d = mysqli_fetch_array($result) )
		{
			if ( !empty($d) )
				$output[] = new Student($d['sid']);
		}
		return $output;
	}
	
	/**
	 * Find all Student objects in the database where the value of $property matches $value
	 * @param $property The property to look at
	 * @param $value The value to look for
	 * @return A Student object for the found object, false otherwise. If multiple Students match, the first one will be returned.
	 */
	public static function getByProperty($property, $value)
	{
		global $mysql;
		if ( $property == PROPERTY_ID )
			$property = PROPERTY_SID;
		
		$value = real_escape_string($value);
		$result = $mysql->query("SELECT sid FROM students WHERE `".$property."` = '".$value."'");
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return new Student(mysqli_result($result, 0, "sid"));
	}

	/**
	 * Create a new Student object
	 * @param $id The Student ID of the Student that this object represents
	 */
	public function __construct($id)
	{
		$this->id = $id;
	}
	
	/**
	 * Get the ID for the Student that this object represents
	 * @return The Student's ID
	 */
	public function getID()
	{
		return real_escape_string($this->id);
	}
	
	/**
	 * Get the name of the Student
	 * @return The name of the student, false on failure
	 */
	public function getName()
	{
		global $mysql;
		$result =  $mysql->query("SELECT name FROM `students` WHERE `sid` = '".$this->getID()."'");
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_result($result, 0, "name");
	}
	
	/**
	 * Set the name of the Student
	 * @return true on success, false otherwise
	 */
	public function setName($name)
	{
		global $mysql;
		$this->name = $name;
		$name = real_escape_string($name);
		return $mysql->query("UPDATE students SET `name` = '".$name."' WHERE `sid` = '".$this->getID()."'");
	}
	
	/**
	 * Get the Laptop object that this Student owns
	 * @return The Laptop object that this Student owns. If the student is not assigned a laptop, false is returned
	 */
	public function getLaptop()
	{
		global $mysql;
		$laptop = $mysql->query("SELECT * FROM `students` WHERE `sid` = '".$this->getID()."'");
		if ( !$laptop || mysqli_num_rows($laptop) == 0 )
			return false;
		
		$laptop = mysqli_result($laptop, 0, "laptop");
		if ( $laptop == 0 )
			return false;
		
		return new Laptop($laptop);
	}
	
	/** 
	 * Unassign the Student's Laptop
	 * @return true on success, false on failure or if the Student has no laptop assigned
	 */
	public function clearLaptop()
	{
		global $mysql;
		$laptop = false;
		if ( !($laptop = $this->getLaptop()) )
			return false;
		
		addHistoryItem($laptop, $this, HISTORYEVENT_UNASSIGN);
		return $mysql->query("UPDATE students SET `laptop` = 0 WHERE `sid` = '".$this->getID()."'");
	}
	
	/**
	 * Set the Student's Laptop
	 * @param $laptop The Laptop to assign to this Student. May be an ID or a Laptop object
	 * @return true on success, false on failure or if a laptop is already assigned
	 */
	public function setLaptop($laptop)
	{
		global $mysql;
		if ( $this->getLaptop() )
			return false;
		
		if ( @get_class($laptop) == "Laptop" )
			$this->laptop = $laptop;
		else
		{
			if ( ($laptop = intval($laptop)) == 0 )
				return false;
			$this->laptop = Laptop::getByProperty(PROPERTY_ID, $laptop);
		}
		
		if ( $this->laptop )
		{
			addHistoryItem($this->laptop, $this, HISTORYEVENT_ASSIGNMENT);
			return $mysql->query("UPDATE students SET `laptop` = ".$this->laptop->getID()." WHERE `sid` = '".$this->getID()."'");
		}
		return false;
	}
	
	/**
	 * Get the value of a property
	 * @param The property to get the value of
	 * @return true on success, false on failure
	 */
	public function getProperty($property)
	{
		global $mysql;
		$property = real_escape_string($property);
		
		$result = $mysql->query("SELECT `".$property."` FROM `students` WHERE `sid` = '".$this->getID()."'");
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_result($result, 0, $property);
	}

	/**
	 * Set the value of a property
	 * @param The property to get the value of
	 * @param The new value for the property
	 * @return true on success, false on failure
	 */
	public function setProperty($property, $value)
	{
		global $mysql;
		$property = real_escape_string($property);
		$value = real_escape_string($value);
		
		return $mysql->query("UPDATE students SET `".$property."` = '".$value."' WHERE `sid` = ".$this->getID());
	}
	
	/**
	 * Get all the properties associated with this object
	 * @return An array of properties with the key being the property name
	 */
	public function getProperties()
	{
		global $mysql;
		$result = $mysql->query("SELECT * FROM `students` WHERE `sid` = '".$this->getID()."'");
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_fetch_array($result);
	}
	
	/**
	 * Check if this Student is a Helper
	 * @return A boolean representing if this Student is a Helper
	 */
	public function isHelper()
	{
		global $helpers;
		foreach ($helpers as $helper)
		{
			if ( $helper == $this->getID() )
				return true;
		}
		return false;
	}
	
	/**
	 * Get the history associated with this Student
	 * @param $sortBy The order, by timestamp, to sort the array. SORT_DESC or SORT_ASC. SORT_DESC is default.
	 * @return An array containing an array of the history events associated with this Student.
	 */
	public function getHistory($sortBy = SORT_DESC)
	{
		global $mysql;
		$result = $mysql->query("SELECT * FROM history WHERE `student` = '".$this->getID()."'");
		$output = array();
		$sortPivot = array();
		while ( $d = mysqli_fetch_array($result, MYSQL_ASSOC) )
		{
			if ( !empty($d) )
			{
				$d['data'] = unserialize($d['data']);
				$output[] = $d;
				$sortPivot[] = $d['timestamp'];
			}
		}
		array_multisort($output, $sortBy, $sortPivot);
		return $output;
	}

	/**
	 * Utility function for converting the history array returned by Student's getHistory() to viewable HTML
	 * @param $history The history array returned by Student's getHistory()
	 * @return A string containing the HTML representation of $history
	 */
	public static function getHTMLForHistory($history)
	{
		global $issueTypes;
		$output = "";
		foreach ($history as $row)
		{
			$laptop = new Laptop($row['laptop']);
			if ( $row['action'] == ACTION_UNASSIGN )
			{
				$output .= "<div class=\"alert\"><strong>Returned</strong><br>";
				$output .= "<a href=\"../laptops/laptop.php?id=".$laptop->getID()."\">".$laptop->getProperty(PROPERTY_HOSTNAME)."</a> was returned.<br>";
				$output .= "<small>Recorded on ".date("M d, Y", $row['timestamp'])." at ".date("g:i A", $row['timestamp'])."</small>";
				$output .= "</div>";
			}
			else if ( $row['action'] == ACTION_ASSIGN )
			{
				$output .= "<div class=\"alert alert-success\"><strong>Assigned</strong><br>";
				$output .= "<a href=\"../laptops/laptop.php?id=".$laptop->getID()."\">".$laptop->getProperty(PROPERTY_HOSTNAME)."</a> was assigned to ".$row['student']."<br>";
				$output .= "<small>Recorded on ".date("M d, Y", $row['timestamp'])." at ".date("g:i A", $row['timestamp'])."</small>";
				$output .= "</div>";
			}
			else if ( $row['action'] == HISTORYEVENT_TICKET_STATECHANGE )
			{
				$output .= "<div class=\"alert alert-info\"><strong>Ticket Change</strong><br>";
				$output .= $row['student']." ".$row['data']['verb']." a <a href=\"../tickets/ticket.php?id=".$row['ticket']."\">ticket</a>.<br>";
				$output .= "<small>Recorded on ".date("M d, Y", $row['timestamp'])." at ".date("g:i A", $row['timestamp'])."</small>";
				$output .= "</div>";
			}
			else if ( $row['action'] == HISTORYEVENT_SERVICE )
			{
				$output .= "<div class=\"alert alert-info\"><strong>Service - ".$issueTypes[$row['data']['type']]."</strong><br>";
				$output .= stripcslashes(nl_fix($row['data']['notes']))."<br>";
				$output .= "<small>Recorded on ".date("M d, Y", $row['timestamp'])." at ".date("g:i A", $row['timestamp'])."</small>";
				$output .= "</div>";
			}
		}
		return $output;
	}
}
?>
