<?php
require_once("History.php");
require_once("constants.php");

class Ticket
{
	public static function create($creator, $title, $body)
	{
		global $mysql;
		if ( @get_class($creator) == "Student" )
			$creator = $creator->getID();
		$creator = real_escape_string($creator);
		$title = real_escape_string($title);
		$body = real_escape_string($body);
		$q = $mysql->query("INSERT INTO `tickets` (student, title, body, state, timestamp) VALUES('".$creator."', '".$title."', '".$body."', ".TICKETSTATE_OPEN.", ".time().")");
		if ( !$q )
			return false;
		$ticket = new Ticket($mysql->insert_id);
		addTicketHistoryItem(-1, $ticket, $creator, HISTORYEVENT_TICKET_STATECHANGE, array("verb" => "created"), 1);
		$ticket->addReply($creator, $body);
		return $ticket;
		
	}
	
	public static function getByProperty($property, $value)
	{
		global $mysql;
		$value = real_escape_string($value);
		$result = $mysql->query("SELECT id FROM tickets WHERE `".$property."` = '".$value."'");
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return new Ticket(mysqli_result($result, 0, "id"));
	}
	
	public static function getAllByProperty($property, $value)
	{
		global $mysql;
		$value = real_escape_string($value);
		$result = $mysql->query("SELECT id FROM tickets WHERE `".$property."` = '".$value."'");
		
		$output = array();
		while ( $d = mysqli_fetch_array($result) )
		{
			if ( !empty($d) )
				$output[] = new Ticket($d['id']);
		}

		return $output;
	}
	
	public static function getAll($sortBy = SORT_DESC)
	{
		global $mysql;
		$result = $mysql->query("SELECT id FROM tickets");
		$out = array();
		$pivot = array();
		while ( $d = mysqli_fetch_array($result) )
		{
			if ( !empty($d) )
			{
				$out[] = new Ticket($d['id']);
				$pivot[] = $d['timestamp'];
			}
		}
		array_multisort($out, SORT_DESC, $pivot);
		return $out;
	}
	
	
	public static function getHTMLForHistory($history)
	{
		$output = "";
		foreach ($history as $row)
		{
			if ( $row['action'] == HISTORYEVENT_TICKET_STATECHANGE )
			{
				$output .= "<div class=\"alert\">";
				$output .= "<strong>Ticket ".$row['data']['verb']." on ".date("M d, Y", $row['timestamp'])." at ".date("g:i A", $row['timestamp'])."</strong><br>";
				$output .= "</div>";
			}
			else if ( $row['action'] == HISTORYEVENT_TICKET_INFO )
			{
				$output .= "<div class=\"alert\">";
				$output .= "<strong>".stripcslashes($row['data']['body'])." on ".date("M d, Y", $row['timestamp'])." at ".date("g:i A", $row['timestamp'])."</strong><br>";
				$output .= "</div>";
			}
			else if ( $row['action'] == HISTORYEVENT_TICKET_REPLY )
			{
				$author = new Student($row['student']);
				if ( $author->isHelper() )
					$output .= "<div class=\"alert alert-success\">";
				else
					$output .= "<div class=\"alert alert-info\">";
				$output .= "<strong>".$author->getName()."</strong> <small>".date("M d, Y", $row['timestamp'])." at ".date("g:i A", $row['timestamp'])."</small><br>";
				$output .= stripcslashes(nl_fix($row['data']['body']));
				$output .= "</div>";
			}
		}
		return $output;
	}
	
	
	public static function searchField($property, $query, $dupCheck = array())
	{
		global $mysql;
		$query = real_escape_string($query);
		$result = $mysql->query("SELECT * FROM tickets WHERE ".$property." LIKE '%".$query."%'");
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
					if ( $row->getProperty(PROPERTY_ID) == $d['id'] )
						$dup = true;
				}
				if ( !$dup )
					$output[] = new Ticket($d['id']);
			}
		}
		return $output;
	}
	
	public static function search($query)
	{
		$output = array();
		$output = array_merge(Ticket::searchField(PROPERTY_STUDENT, $query, $output), $output);
		$output = array_merge(Ticket::searchField(PROPERTY_HELPER, $query, $output), $output);
		$output = array_merge(Ticket::searchField(PROPERTY_TITLE, $query, $output), $output);
		$output = array_merge(Ticket::searchField(PROPERTY_BODY, $query, $output), $output);
		
		$pivot = array();
		foreach ( $output as $k => $v )
		{
			$pivot[$k] = $v->getProperty(PROPERTY_TIMESTAMP);
		}
		array_multisort($output, SORT_DESC, $pivot);
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
	
	public function assignHelper($person)
	{
		$obj = false;
		if ( @get_class($person) == "Student" )
		{
			$obj = $person;
			$person = $person->getID();
		}
		else
			$obj = new Student($person);
		
		if ( !Student::getByProperty(PROPERTY_SID, $person) )
			return false;
		
		$result = $this->setProperty("helper", $person);
		if ( $result )
			addTicketHistoryItem(-1, $this, -1, HISTORYEVENT_TICKET_INFO, array("body" => "Ticket assigned to ".$obj->getName()));
		return $result;
	}
	
	public function getHelper()
	{
		$helper = $this->getProperty("helper");
		if ( !$helper )
			return false;
		return new Student($helper);
	}
	
	public function getStudent()	
	{
		$student = $this->getProperty("student");
		if ( !$student )
			return false;
		return new Student($student);
	}
	
	public function getHistory($sortBy = SORT_DESC)
	{
		global $mysql;
		$result = $mysql->query("SELECT * FROM history WHERE `ticket` = ".$this->getID());
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

	
	public function addReply($author, $body)
	{
		if ( @get_class($author) == "Student" )
			$author = $author->getID();
		
		return addTicketHistoryItem(-1, $this, $author, HISTORYEVENT_TICKET_REPLY, array("body"=>$body));
	}
	
	public function close()
	{
		$result = $this->setProperty(PROPERTY_STATE, TICKETSTATE_CLOSED);
		if ( $result )
			addTicketHistoryItem(-1, $this, -1, HISTORYEVENT_TICKET_STATECHANGE, array("verb"=>"closed"));
		return $result;
	}
	
	public function reopen()
	{
		$result = $this->setProperty(PROPERTY_STATE, TICKETSTATE_OPEN);
		if ( $result )
			addTicketHistoryItem(-1, $this, -1, HISTORYEVENT_TICKET_STATECHANGE, array("verb" => "reopened") );
		return $result;
	}
	
	public function getProperty($property)
	{
		global $mysql;
		$property = real_escape_string($property);
		
		$result = $mysql->query("SELECT `".$property."` FROM `tickets` WHERE `id` = ".$this->getID());
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_result($result, 0, $property);
	}
	
	public function getProperties()
	{
		global $mysql;
		$result = $mysql->query("SELECT * FROM `tickets` WHERE `id` = ".$this->getID());
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_fetch_array($result);
	}
	
	public function setProperty($property, $value)
	{
		global $mysql;
		$property = real_escape_string($property);
		$value = real_escape_string($value);
		return $mysql->query("UPDATE tickets SET `".$property."` = '".$value."' WHERE `id` = ".$this->getID());
	}
	
	public function getStateLabel()
	{
		$mostRecentEntry = $this->getHistory();
		$mostRecentEntry = $mostRecentEntry[0];
		if ( $this->getProperty(PROPERTY_STATE) == TICKETSTATE_CLOSED )
			return "<span class=\"label label-inverse\">Closed</span>";
		else if ( !$this->getHelper() || $mostRecentEntry['action'] == HISTORYEVENT_TICKET_STATECHANGE || $mostRecentEntry['action'] == HISTORYEVENT_TICKET_INFO )
		{
			return "<span class=\"label label-important\">New Ticket</span>";
		}
		else if ( $mostRecentEntry['action'] == HISTORYEVENT_TICKET_REPLY )
		{
			$studentReply = new Student($mostRecentEntry['student']);
			if ( $studentReply->isHelper() )
			{
				return "<span class=\"label label-success\">Helper Replied</span>";
			}
			else
			{
				return "<span class=\"label label-warning\">Student Replied</span>";
			}
		}

		return "";
	}

}
?>