<?php
require_once("constants.php");

class Student
{	
	public static function searchField($property, $query, $dupCheck = array())
	{
		$query = mysql_real_escape_string($query);
		$result = mysql_query("SELECT * FROM students WHERE ".$property." LIKE '%".$query."%'");
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
					if ( $row['assetTag'] == $d['assetTag'] )
						$dup = true;
				}
				if ( !$dup )
					$output[] = $d;
			}
		}
		return $output;
	}
	
	public static function search($query)
	{
		$output = array();
		$output = array_merge(Laptop::searchField(PROPERTY_ID, $query, $output), $output);
		$output = array_merge(Laptop::searchField(PROPERTY_NAME, $query, $output), $output);
		return $output;
	}
	
	public static function getAll()
	{
		$output = array();
		
		$result = mysql_query("SELECT `id` FROM `students`");
		while ( $d = mysql_fetch_array($result) )
		{
			if ( !empty($d) )
				$output[] = new Student($d['id']);
		}
		return $output;
	}
	
	public static function getByProperty($property, $value)
	{
		$value = mysql_real_escape_string($value);
		$result = mysql_query("SELECT id FROM students WHERE `".$property."` = '".$value."'");
		if ( !$result || mysql_num_rows($result) == 0 )
			return false;
		return new Student(mysql_result($result, 0, "id"));
	}


	public function __construct($id)
	{
		$this->id = $id;
	}
	
	public function getID()
	{
		return $this->id;
	}
	
	public function getName()
	{
		$result =  mysql_query("SELECT name FROM `students` WHERE `id` = ".$this->getID());
		if ( !$result || mysql_num_rows($result) == 0 )
			return false;
		return mysql_result($result, 0, "name");
	}
	
	public function setName($name)
	{
		$this->name = $name;
		$name = mysql_real_escape_string($name);
		return mysql_query("UPDATE students SET `name` = '".$name."' WHERE `id` = ".$this->getID());
	}
	
	public function getLaptop()
	{
		$laptop = mysql_query("SELECT * FROM `students` WHERE `id` = ".$this->getID());
		if ( !$laptop || mysql_num_rows($laptop) == 0 )
			return false;
		
		$laptop = mysql_result($laptop, 0, "laptop");
		if ( $laptop == 0 )
			return false;
		
		return new Laptop($laptop);
	}
	
	public function clearLaptop()
	{
		$laptop = false;
		if ( !($laptop = $this->getLaptop()) )
			return false;
		
		addHistoryItem($laptop, $this, HISTORYEVENT_ASSIGNMENT);
		return mysql_query("UPDATE students SET `laptop` = 0 WHERE `id` = ".$this->getID());
	}
	
	public function setLaptop($laptop)
	{
		$this->clearLaptop();
		
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
			return mysql_query("UPDATE students SET `laptop` = ".$this->laptop->getID()." WHERE `id` = ".$this->getID());
		}
		return false;
	}
	
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
}
?>