<?php
require_once("constants.php");

class Student
{	
	public static function create($sid, $name, $grade)
	{
		if ( Student::getByProperty(PROPERTY_SID, $sid) )
			return false;
		$sid = mysql_real_escape_string($sid);
		$name = mysql_real_escape_string($name);
		if ( ($grade = intval($grade)) == 0 )
			return false;
		$result = mysql_query("INSERT INTO students (sid, name, grade) VALUES('".$sid."', '".$name."', ".$grade.")");
		echo mysql_error();
		if ( !$result )
			return false;
		return new Student($sid);
	}
	
	public static function remove($sid)
	{
		$sid = mysql_real_escape_string($sid);
		return mysql_query("DELETE FROM `students` WHERE `sid` = '".$sid."'");
	}
	
	public static function nuke($sid)
	{
		$sid = mysql_real_escape_string($sid);
		
		$studentTicketRemoval = mysql_query("DELETE FROM `tickets` WHERE `student` = '".$sid."'");
		$helperTicketRemoval = mysql_query("DELETE FROM `tickets` WHERE `helper` = '".$sid."'");
		$historyRemoval = mysql_query("DELETE FROM `history` WHERE `student` = '".$sid."'");
		$studentRemoval = mysql_query("DELETE FROM `students` WHERE `sid` = '".$sid."'");
		
		return $studentTicketRemoval && $helperTicketRemoval && $historyRemoval && $studentRemoval;
	}
	
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
		
		$result = mysql_query("SELECT `sid` FROM `students`");
		while ( $d = mysql_fetch_array($result) )
		{
			if ( !empty($d) )
				$output[] = new Student($d['sid']);
		}
		return $output;
	}
	
	public static function getByProperty($property, $value)
	{
		if ( $property == PROPERTY_ID )
			$property = PROPERTY_SID;
		
		$value = mysql_real_escape_string($value);
		$result = mysql_query("SELECT sid FROM students WHERE `".$property."` = '".$value."'");
		if ( !$result || mysql_num_rows($result) == 0 )
			return false;
		return new Student(mysql_result($result, 0, "sid"));
	}


	public function __construct($id)
	{
		$this->id = mysql_real_escape_string($id);
	}
	
	public function getID()
	{
		return $this->id;
	}
	
	public function getName()
	{
		$result =  mysql_query("SELECT name FROM `students` WHERE `sid` = '".$this->getID()."'");
		if ( !$result || mysql_num_rows($result) == 0 )
			return false;
		return mysql_result($result, 0, "name");
	}
	
	public function setName($name)
	{
		$this->name = $name;
		$name = mysql_real_escape_string($name);
		return mysql_query("UPDATE students SET `name` = '".$name."' WHERE `sid` = '".$this->getID()."'");
	}
	
	public function getLaptop()
	{
		$laptop = mysql_query("SELECT * FROM `students` WHERE `sid` = '".$this->getID()."'");
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
		return mysql_query("UPDATE students SET `laptop` = 0 WHERE `sid` = '".$this->getID()."'");
	}
	
	public function setLaptop($laptop)
	{
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
			return mysql_query("UPDATE students SET `laptop` = ".$this->laptop->getID()." WHERE `sid` = '".$this->getID()."'");
		}
		return false;
	}
	
	public function getProperty($property)
	{
		$property = mysql_real_escape_string($property);
		
		$result = mysql_query("SELECT `".$property."` FROM `students` WHERE `sid` = ".$this->getID());
		if ( !$result || mysql_num_rows($result) == 0 )
			return false;
		return mysql_result($result, 0, $property);
	}
	
	public function getProperties()
	{
		$result = mysql_query("SELECT * FROM `students` WHERE `sid` = ".$this->getID());
		if ( !$result || mysql_num_rows($result) == 0 )
			return false;
		return mysql_fetch_array($result);
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