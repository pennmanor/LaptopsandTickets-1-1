<?php
require_once("History.php");
require_once("constants.php");

class Ticket
{
	public static function create($creator, $title, $body)
	{
		if ( @get_class($creator) == "Student" )
			$creator = $creator->getID();
		if ( ($creator = intval($creator)) == 0 )
			return false;
		$title = mysql_real_escape_string($title);
		$body = mysql_real_escape_string($body);
		if ( !mysql_query("INSERT INTO `tickets` (student, title, body, state, timestamp) VALUES(".$creator.", '".$title."', '".$body."', ".TICKETSTATE_OPEN.", ".time().")") )
			return false;
		$ticket = new Ticket(mysql_insert_id());
		addTicketHistoryItem(-1, $ticket, $creator, HISTORYEVENT_TICKET_STATECHANGE, array("verb" => "created"), 1);
		$ticket->addReply($creator, $body);
		return $ticket;
		
	}
	
	public static function getByProperty($property, $value)
	{
		$value = mysql_real_escape_string($value);
		$result = mysql_query("SELECT id FROM tickets WHERE `".$property."` = '".$value."'");
		if ( !$result || mysql_num_rows($result) == 0 )
			return false;
		return new Ticket(mysql_result($result, 0, "id"));
	}
	
	public static function getAllByProperty($property, $value)
	{
		$value = mysql_real_escape_string($value);
		$result = mysql_query("SELECT id FROM tickets WHERE `".$property."` = '".$value."'");
		
		$output = array();
		while ( $d = mysql_fetch_array($result) )
		{
			if ( !empty($d) )
				$output[] = new Ticket($d['id']);
		}

		return $output;
	}
	
	public static function getAll()
	{
		$result = mysql_query("SELECT id FROM tickets");
		$out = array();
		while ( $d = mysql_fetch_array($result) )
		{
			if ( !empty($d) )
				$out[$d['id']] = new Ticket($d['id']);
		}
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
				$output .= "<strong>".$row['data']['body']." on ".date("M d, Y", $row['timestamp'])." at ".date("g:i A", $row['timestamp'])."</strong><br>";
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
				$output .= nl_fix($row['data']['body']);
				$output .= "</div>";
			}
		}
		return $output;
	}
	
	
	public static function searchField($property, $query, $dupCheck = array())
	{
		$query = mysql_real_escape_string($query);
		$result = mysql_query("SELECT * FROM tickets WHERE ".$property." LIKE '%".$query."%'");
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
		
		if ( ($person = intval($person)) == 0 )
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
		$result = mysql_query("SELECT * FROM history WHERE `ticket` = ".$this->getID());
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
		array_multisort($output, $sortBy, $sortPivot);
		return $output;
	}

	
	public function addReply($author, $body)
	{
		if ( @get_class($author) == "Student" )
			$author = $author->getID();
		if ( ($author = intval($author)) == 0 )
			return false;
		
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
		$property = mysql_real_escape_string($property);
		
		$result = mysql_query("SELECT `".$property."` FROM `tickets` WHERE `id` = ".$this->getID());
		if ( !$result || mysql_num_rows($result) == 0 )
			return false;
		return mysql_result($result, 0, $property);
	}
	
	public function getProperties()
	{
		$result = mysql_query("SELECT * FROM `tickets` WHERE `id` = ".$this->getID());
		if ( !$result || mysql_num_rows($result) == 0 )
			return false;
		return mysql_fetch_array($result);
	}
	
	public function setProperty($property, $value)
	{
		$property = mysql_real_escape_string($property);
		$value = mysql_real_escape_string($value);
		return mysql_query("UPDATE tickets SET `".$property."` = '".$value."' WHERE `id` = ".$this->getID());
	}
	
	public function getStateLabel()
	{
		$mostRecentEntry = $this->getHistory();
		$mostRecentEntry = $mostRecentEntry[0];
		if ( $this->getProperty(PROPERTY_STATE) == TICKETSTATE_CLOSED )
			return "<span class=\"label label-inverse\">Closed</span>";
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
		else if ( $mostRecentEntry['action'] == HISTORYEVENT_TICKET_STATECHANGE || $mostRecentEntry['action'] == HISTORYEVENT_TICKET_INFO )
		{
			return "<span class=\"label label-important\">New Ticket</span>";
		}
		return "";
	}

}
?>