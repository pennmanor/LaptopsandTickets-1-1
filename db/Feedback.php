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
 * Class for accessing Feedback data
 * @author Andrew
 */
class Feedback
{
	/**
	 * Create a Feedback entry
	 * @param $sid The student ID that authored this entry
	 * @param $liked A string containing what the student liked
	 * @param $dislike A string containing what the student disliked
	 * @return A new Feedback object on success, false on failure
	 */
	public static function create($sid, $liked, $dislike)
	{
		global $mysql;
		$sid = $mysql->escape_string($sid);
		$liked = $mysql->escape_string($liked);
		$dislike = $mysql->escape_string($dislike);
		
		$result = $mysql->query("INSERT INTO `feedback` (`sid`, `like`, `dislike`) VALUES('".$sid."', '".$liked."', '".$dislike."')");
		return new Feedback($mysql->insert_id);
	}
	
	/**
	 * Remove all feedback entries
	 * @return true on success, false on failure
	 */
	public static function reset()
	{
		global $mysql;
		return $mysql->query("DELETE FROM `feedback`");
	}
	
	/**
	 * Get all Feedback objects in the database
	 * @return An array containing all Feedback entries in the database
	 */
	public static function getAll()
	{
		global $mysql;
		$output = array();
		$result = $mysql->query("SELECT id FROM `feedback`");
		while ( $d = $result->fetch_array() )
			$output[] = new Feedback($d['id']);
		return $output;
	}
	
	/**
	 * Create a new Feedback object to represent an entry in the database
	 * @param $id The id of the entry that this object will represent
	 */
	public function __construct($id)
	{
		$this->id = $id;
	}
	
	/**
	 * Get the ID of the entry that this object represents
	 * @return The ID that this entry represents
	 */
	public function getID()
	{
		return $this->id;
	}
	
	/** 
	 * Get all of the properties associated with this object
	 * @return An array containing all the entries associated with this object
	 */
	public function getProperties()
	{
		global $mysql;
		$result = $mysql->query("SELECT * FROM `feedback` WHERE `id` = ".$this->getID());
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_fetch_array($result);
	}
	
	/**
	 * Get the value of a property
	 * @param $property The property to get the value of
	 * @return The value of the requested property
	 */
	public function getProperty($property)
	{
		global $mysql;
		$property = real_escape_string($property);
		
		$result = $mysql->query("SELECT `".$property."` FROM `feedback` WHERE `id` = ".$this->getID());
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_result($result, 0, $property);
	}

	/**
	 * Set the value of a property
	 * @param $key The key to set the value of
	 * @param $value The new value for the property/key
	 * @return true on success, false on failure
	 */
	public function setProperty($key, $value)
	{
		global $mysql;
		$property = real_escape_string($property);
		$value = real_escape_string($value);
		
		return $mysql->query("UPDATE feedback SET `".$property."` = '".$value."' WHERE `id` = ".$this->getID());
	}
}
?>