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
require_once("History.php");
require_once("constants.php");

/**
 * Class for accessing Laptop data
 * @author Andrew
 */
class Laptop
{	
	/**
	 * Creates a Laptop
	 * @param $hostname The laptop's hostname
	 * @param $serial The laptop's serial
	 * @param $assetTag The laptop's asset tag
	 * @param $wirelessMAC The laptop's wireless MAC address
	 * @param $ethernetMAC The laptop's ethernet MAC address
	 * @param $building The laptop's building ID
	 * @return A new Laptop object on success, false otherwise
	 */
	public static function create($hostname, $serial, $assetTag, $wirelessMAC, $ethernetMAC, $building)
	{
		global $mysql;
		if ( Laptop::getByProperty(PROPERTY_ASSETTAG, $assetTag) )
			return false;
		$hostname = real_escape_string($hostname);
		$serial = real_escape_string($serial);
		if ( ($assetTag = intval($assetTag)) == 0 )
			return false;
		$wirelessMAC = real_escape_string($wirelessMAC);
		$ethernetMAC = real_escape_string($ethernetMAC);

		if ( empty($hostname) || empty($assetTag) )
			return false;
		
		$result = $mysql->query("INSERT INTO laptops (hostname, serial, assetTag, wirelessMAC, ethernetMAC, building) VALUES('".$hostname."', '".$serial."', ".$assetTag.", '".$wirelessMAC."', '".$ethernetMAC."', '".$building."')");
		$laptop = new Laptop($mysql->insert_id); 
		if ( $result )
			addHistoryItem($laptop, -1, HISTORYEVENT_CREATION, array());
		return $laptop;
	}

	/**
	 * Utility function for converting the history array returned by Laptop's getHistory() to viewable HTML
	 * @param $history The history array returned by Laptop's getHistory()
	 * @param $laptops An array from Laptop::getAll() for looking up the asset tag of computers referenced in history events. Defaults to false. If not specified, the laptop will be refered to as "This computer"
	 * @return A string containing the HTML representation of $history
	 */
	public static function getHTMLForHistory($history, $laptops = false)
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
				$output .= ($laptops?$laptops[$row['laptop']['id']]['assetTag']:"This computer")." was assigned to <a href=\"../students/student.php?sid=".$row['student']."\">".$row['student']."</a><br>";
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
	
	/**
	 * Search for Laptop objects where $query is in the value of $property. It does not look for an exact match.
	 * @param $property The property to search in
	 * @param $query The query string to look for in $property
	 * @param $dupCheck Do not return any matching items already in this array. Useful when searching multiple properties. Defaults to an empty array.
	 * @return The matched Laptop objects that do not already exist in the $dupCheck array
	 */
	public static function searchField($property, $query, $dupCheck = array())
	{
		global $mysql;
		$query = real_escape_string($query);
		$result = $mysql->query("SELECT * FROM laptops WHERE ".$property." LIKE '%".$query."%'");
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
					if ( $row->getProperty(PROPERTY_ASSETTAG) == $d['assetTag'] )
						$dup = true;
				}
				if ( !$dup )
					$output[] = new Laptop($d['id']);
			}
		}
		return $output;
	}
	
	/**
	 * Wraper for searchField() that searches by id, hostname, serial, asset tag, wireless MAC, wired MAC, and building
	 * @param $query The query to search for
	 * @return An array of the Laptop objects that match $query
	 */
	public static function search($query)
	{
		$output = array();
		$output = array_merge(Laptop::searchField(PROPERTY_ID, $query, $output), $output);
		$output = array_merge(Laptop::searchField(PROPERTY_HOSTNAME, $query, $output), $output);
		$output = array_merge(Laptop::searchField(PROPERTY_SERIAL, $query, $output), $output);
		$output = array_merge(Laptop::searchField(PROPERTY_ASSETTAG, $query, $output), $output);
		$output = array_merge(Laptop::searchField(PROPERTY_WMAC, $query, $output), $output);
		$output = array_merge(Laptop::searchField(PROPERTY_EMAC, $query, $output), $output);
		$output = array_merge(Laptop::searchField(PROPERTY_BUILDING, $query, $output), $output);
		return $output;
	}
	
	/**
	 * Find all Laptop objects in the database where the value of $property matches $value
	 * @param $property The property to look at
	 * @param $value The value to look for
	 * @return A Laptop object for the found object, false otherwise. If multiple Laptop match, the first one will be returned.
	 */
	public static function getByProperty($property, $value)
	{
		global $mysql;
		$value = real_escape_string($value);
		$result = $mysql->query("SELECT id FROM laptops WHERE `".$property."` = '".$value."'");
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return new Laptop(mysqli_result($result, 0, "id"));
	}
	
	/**
	 * Find all Laptops in the database
	 * @return An array of all Laptop objects in the database
	 */
	public static function getAll()
	{
		global $mysql;
		$output = array();
		
		$result = $mysql->query("SELECT id FROM `laptops`");
		while ( $d = mysqli_fetch_array($result) )
		{
			if ( !empty($d) )
				$output[] = new Laptop($d['id']);
		}
		return $output;
	}
	/**
	 * Get the entire History database
	 * @return An array containing every entry in the database
	 */
	public static function getAllHistory()
	{
		global $mysql;
		$output = array();
		
		$result = $mysql->query("SELECT * FROM `history`");
		while ( $d = mysqli_fetch_array($result) )
		{
			if ( !empty($d) )
			{
				$d['data'] = unserialize($d['data']);
				$output[] = $d;
			}
		}
		return $output;
	}
	
	/**
	 * Create a new Laptop object
	 * @param $id The ID of the Laptop that this object represents
	 */
	public function __construct($id)
	{
		$this->id = $id;
	}
	
	/**
	 * Get the ID for the Laptop that this object represents
	 * @return The Laptop's ID
	 */
	public function getID()
	{
		return $this->id;
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
		
		$result = $mysql->query("SELECT `".$property."` FROM `laptops` WHERE `id` = ".$this->getID());
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_result($result, 0, $property);
	}
	
	/**
	 * Get all the properties associated with this object
	 * @return An array of properties with the key being the property name
	 */
	public function getProperties()
	{
		global $mysql;
		$result = $mysql->query("SELECT * FROM `laptops` WHERE `id` = ".$this->getID());
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_fetch_array($result);
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
		
		return $mysql->query("UPDATE laptops SET `".$property."` = '".$value."' WHERE `id` = ".$this->getID());
	}
	
	/**
	 * Get the Student object of this laptop's owner
	 * @return A Student object for the owner, false on failure or if unassigned
	 */
	public function getOwner()
	{
		global $mysql;
		$result = $mysql->query("SELECT `sid` FROM `students` WHERE `laptop` = ".$this->getID());
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return new Student(mysqli_result($result, 0, "sid"));
	}
	
	/**
	 * Get the history associated with this Laptop
	 * @param $sortBy The order, by timestamp, to sort the array. SORT_DESC or SORT_ASC. SORT_DESC is default.
	 * @return An array containing an array of the history events associated with this Laptop.
	 */
	public function getHistory($sortBy = SORT_DESC)
	{
		global $mysql;
		$result = $mysql->query("SELECT * FROM history WHERE `laptop` = ".$this->getID());
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
	 * Removes this Laptop
	 * @return true on success, false on failure
	 */
	public function remove()
	{
		global $mysql;
		$owner = $this->getOwner();
		if ( $owner )
			$owner->clearLaptop();
				
		$mysql->query("DELETE FROM history WHERE `laptop` = ".$this->getID());
		return $mysql->query("DELETE FROM laptops WHERE `id` = ".$this->getID());
	}
}
?>