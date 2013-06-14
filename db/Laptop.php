<?php
require_once("History.php");
require_once("constants.php");

class Laptop
{	
	public static function create($hostname, $serial, $assetTag, $wirelessMAC, $ethernetMAC, $building)
	{
		$hostname = mysql_real_escape_string($hostname);
		$serial = mysql_real_escape_string($serial);
		if ( ($assetTag = intval($assetTag)) == 0 )
			return false;
		$wirelessMAC = mysql_real_escape_string($wirelessMAC);
		$ethernetMAC = mysql_real_escape_string($ethernetMAC);
		
		if ( empty($hostname) || empty($assetTag) )
			return false;
		
		$result = mysql_query("INSERT INTO laptops (hostname, serial, assetTag, wirelessMAC, ethernetMAC, building) VALUES('".$hostname."', '".$serial."', ".$assetTag.", '".$wirelessMAC."', '".$ethernetMAC."', '".$building."')");
		$laptop = new Laptop(mysql_insert_id()); 
		if ( $result )
			addHistoryItem($laptop, -1, HISTORYEVENT_CREATION, array());
		echo mysql_error();
		return $laptop;
	}
	
	public static function searchField($property, $query, $dupCheck = array())
	{
		$query = mysql_real_escape_string($query);
		$result = mysql_query("SELECT * FROM laptops WHERE ".$property." LIKE '%".$query."%'");
		if ( !$result || mysql_num_rows($result) == 0 )
			return array();
		$output = array();
		
		while ( $d = mysql_fetch_array($result) )
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
		$value = mysql_real_escape_string($value);
		$result = mysql_query("SELECT id FROM laptops WHERE `".$property."` = '".$value."'");
		if ( !$result || mysql_num_rows($result) == 0 )
			return false;
		return new Laptop(mysql_result($result, 0, "id"));
	}
	
	public static function getAll()
	{
		$output = array();
		
		$result = mysql_query("SELECT id FROM `laptops`");
		while ( $d = mysql_fetch_array($result) )
		{
			if ( !empty($d) )
				$output[] = new Laptop($d['id']);
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
		$property = mysql_real_escape_string($property);
		
		$result = mysql_query("SELECT `".$property."` FROM `laptops` WHERE `id` = ".$this->getID());
		if ( !$result || mysql_num_rows($result) == 0 )
			return false;
		return mysql_result($result, 0, $property);
	}
	
	public function getProperties()
	{
		$result = mysql_query("SELECT * FROM `laptops` WHERE `id` = ".$this->getID());
		if ( !$result || mysql_num_rows($result) == 0 )
			return false;
		return mysql_fetch_array($result);
	}
	
	public function setProperty($property, $value)
	{
		$property = mysql_real_escape_string($property);
		$value = mysql_real_escape_string($value);
		
		return mysql_query("UPDATE laptops SET `".$property."` = '".$value."' WHERE `id` = ".$this->getID());
	}
	
	public function getOwner()
	{
		$result = mysql_query("SELECT `id` FROM `students` WHERE `laptop` = ".$this->getID());
		if ( !$result || mysql_num_rows($result) == 0 )
			return false;
		return new Student(mysql_result($result, 0, "id"));
	}
	
	public function getHistory()
	{
		$result = mysql_query("SELECT * FROM history WHERE `laptop` = ".$this->getID());
		$output = array();
		$sortPivot = array();
		while ( $d = mysql_fetch_array($result, MYSQL_ASSOC) )
		{
			if ( !empty($d) )
			{
				$d['data'] = unserialize($d['data']);
				$output[] = $d;
				$sortPivot[] = $d['timestamp'];
			}
		}
		array_multisort($sortPivot, SORT_DESC, $output);
		return $output;
	}
	
	public function remove()
	{
		$owner = $this->getOwner();
		if ( $owner )
			$owner->clearLaptop();
				
		mysql_query("DELETE FROM history WHERE `laptop` = ".$this->getID());
		return mysql_query("DELETE FROM laptops WHERE `id` = ".$this->getID());
	}
}
?>