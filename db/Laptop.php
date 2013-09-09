<?php
require_once("History.php");
require_once("constants.php");

class Laptop
{	
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
		echo mysqli_error();
		return $laptop;
	}

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
				$output .= ($laptops?$laptops[$row['laptop']['id']]['assetTag']:"This computer")." was assigned to ".$row['student']."<br>";
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
	
	public static function getByProperty($property, $value)
	{
		global $mysql;
		$value = real_escape_string($value);
		$result = $mysql->query("SELECT id FROM laptops WHERE `".$property."` = '".$value."'");
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return new Laptop(mysqli_result($result, 0, "id"));
	}
	
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
	

	public function __construct($id)
	{
		$this->id = $id;
	}
	
	public function getID()
	{
		return $this->id;
	}
	
	public function getProperty($property)
	{
		global $mysql;
		$property = real_escape_string($property);
		
		$result = $mysql->query("SELECT `".$property."` FROM `laptops` WHERE `id` = ".$this->getID());
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_result($result, 0, $property);
	}
	
	public function getProperties()
	{
		global $mysql;
		$result = $mysql->query("SELECT * FROM `laptops` WHERE `id` = ".$this->getID());
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_fetch_array($result);
	}
	
	public function setProperty($property, $value)
	{
		global $mysql;
		$property = real_escape_string($property);
		$value = real_escape_string($value);
		
		return $mysql->query("UPDATE laptops SET `".$property."` = '".$value."' WHERE `id` = ".$this->getID());
	}
	
	public function getOwner()
	{
		global $mysql;
		$result = $mysql->query("SELECT `sid` FROM `students` WHERE `laptop` = ".$this->getID());
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return new Student(mysqli_result($result, 0, "sid"));
	}
	
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