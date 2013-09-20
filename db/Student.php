<?php
require_once("constants.php");
class Student
{	
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
	
	public static function remove($sid)
	{
		global $mysql;
		$sid = real_escape_string($sid);
		return $mysql->query("DELETE FROM `students` WHERE `sid` = '".$sid."'");
	}
	
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
					if ( $row['sid'] == $d['sid'] )
						$dup = true;
				}
				if ( !$dup )
					$output[] = new Student($d['sid']);
			}
		}
		return $output;
	}
	
	public static function search($query)
	{
		$output = array();
		$output = array_merge(Student::searchField(PROPERTY_ID, $query, $output), $output);
		$output = array_merge(Student::searchField(PROPERTY_NAME, $query, $output), $output);
		return $output;
	}
	
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


	public function __construct($id)
	{
		$this->id = $id;
	}
	
	public function getID()
	{
		return real_escape_string($this->id);
	}
	
	public function getName()
	{
		global $mysql;
		$result =  $mysql->query("SELECT name FROM `students` WHERE `sid` = '".$this->getID()."'");
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_result($result, 0, "name");
	}
	
	public function setName($name)
	{
		global $mysql;
		$this->name = $name;
		$name = real_escape_string($name);
		return $mysql->query("UPDATE students SET `name` = '".$name."' WHERE `sid` = '".$this->getID()."'");
	}
	
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
	
	public function clearLaptop()
	{
		global $mysql;
		$laptop = false;
		if ( !($laptop = $this->getLaptop()) )
			return false;
		
		addHistoryItem($laptop, $this, HISTORYEVENT_UNASSIGN);
		return $mysql->query("UPDATE students SET `laptop` = 0 WHERE `sid` = '".$this->getID()."'");
	}
	
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
	
	public function getProperty($property)
	{
		global $mysql;
		$property = real_escape_string($property);
		
		$result = $mysql->query("SELECT `".$property."` FROM `students` WHERE `sid` = '".$this->getID()."'");
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_result($result, 0, $property);
	}

	public function setProperty($property, $value)
	{
		global $mysql;
		$property = real_escape_string($property);
		$value = real_escape_string($value);
		
		return $mysql->query("UPDATE students SET `".$property."` = '".$value."' WHERE `sid` = ".$this->getID());
	}
	
	public function getProperties()
	{
		global $mysql;
		$result = $mysql->query("SELECT * FROM `students` WHERE `sid` = '".$this->getID()."'");
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_fetch_array($result);
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
		}
		return $output;
	}
}
?>
