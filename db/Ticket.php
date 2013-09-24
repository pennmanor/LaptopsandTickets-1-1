<?php
require_once("History.php");
require_once("constants.php");

/**
 * Class for accessing Ticket data
 * @author Andrew
 */
class Ticket
{
	/**
	 * Crates a ticket for $creator with $title and $body, along with inserting the necessary history data.
	 * This ticket is unassigned until assignHelper() is called on the returned instance
	 * @param $creator The student ID or student object of the ticket creator
	 * @param $title The title of the ticket
	 * @param $body The body of the ticket, also the first message in the ticket history
	 * @return A new Ticket object with the provided information. False if an error occurs.
	 */
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
	
	/** 
	 * Find the ticket object that has it's $property value equal $value
	 * @param $property The property to look at
	 * @param $value The value to look for
	 * @return A Ticket object that has $property's value equal $value. If multiple objects are matched, the first one will be returned. If not found, false is returned.
	*/
	public static function getByProperty($property, $value)
	{
		global $mysql;
		$value = real_escape_string($value);
		$result = $mysql->query("SELECT id FROM tickets WHERE `".$property."` = '".$value."'");
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return new Ticket(mysqli_result($result, 0, "id"));
	}
	
	/** 
	 * Find the ticket objects that have their $property value equal $value
	 * @param $property The property to look at
	 * @param $value The value to look for
	 * @return An array containing the Ticket objects where the value of $property equals $value. An empty array is returned if none match.
	*/
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
	
	/**
	 * Find all tickets in the database
	 * @param $sortyBy Sort the list ascending or decending by timestamp (SORT_DESC/SORT_ASC) SORT_DESC is default.
	 * @return The array of all Ticket objects in the database
	 */
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
	
	/**
	 * Utility function for converting the history array returned by Ticket's getHistory() to viewable HTML
	 * @param $history The history array returned by Ticket's getHistory()
	 * @return A string containing the HTML representation of $history
	 */
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
	
	/**
	 * Search for Ticket objects where $query is in the value of $property. It does not look for an exact match.
	 * @param $property The property to search in
	 * @param $query The query string to look for in $property
	 * @param $dupCheck Do not return any matching items already in this array. Useful when searching multiple properties. Defaults to an empty array.
	 * @return The matched Ticket objects that do not already exist in the $dupCheck array
	 */
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
	
	/**
	 * Wraper for searchField() that searches by creator, assigned helper, title, and body.
	 * @param $query The query to search for
	 * @return An array of the Ticket objects that match $query
	 */
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


	/**
	 * Constructs a new Ticket object
	 * Creating a ticket object does not cache database data inside the object.
	 * @param The database ID of the row this Ticket object will reference.
	 */
	public function __construct($id)
	{
		$this->id = $id;
	}
	
	/**
	 * Get the ID of the row in the Tickets database that this object references
	 * @return The ID used by this object
	 */
	public function getID()
	{
		return $this->id;
	}
	
	/**
	 * Assign a helper to the ticket
	 * In addition to setting the helper, this function creates a history event for the action.
	 * @return true on success, false on failure
	 */
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
	
	/**
	 * Get the Student object of the helper
	 * @return The Student object of the helper assigned to this ticket. false if there is no helper assigned
	 */
	public function getHelper()
	{
		$helper = $this->getProperty("helper");
		if ( !$helper )
			return false;
		return new Student($helper);
	}
	
	/**
	 * Get the student who created this ticket.
	 * @return The Student object of the student who created this ticket. false on failure
	 */
	public function getStudent()	
	{
		$student = $this->getProperty("student");
		if ( !$student )
			return false;
		return new Student($student);
	}
	
	/**
	 * Get the history associated with this ticket
	 * @param $sortBy The order, by timestamp, to sort the array. SORT_DESC or SORT_ASC. SORT_DESC is default.
	 * @return An array containing an array of the history events associated with this ticket.
	 */
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

	/**
	 * Add a reply to this ticket
	 * @param $author The author of the reply. Can be a Student object or an ID
	 * @param $body The body of the reply
	 * @return true on success, false on failure
	 */
	public function addReply($author, $body)
	{
		if ( @get_class($author) == "Student" )
			$author = $author->getID();
		
		return addTicketHistoryItem(-1, $this, $author, HISTORYEVENT_TICKET_REPLY, array("body"=>$body));
	}
	
	/**
	 * Close the ticket
	 * This function also creates a history event for the change
	 * @return true on success, false on failure
	 */
	public function close()
	{
		$result = $this->setProperty(PROPERTY_STATE, TICKETSTATE_CLOSED);
		if ( $result )
			addTicketHistoryItem(-1, $this, -1, HISTORYEVENT_TICKET_STATECHANGE, array("verb"=>"closed"));
		return $result;
	}
	
	/**
	 * Reopens the ticket
	 * This function also creates a history event for the change
	 * @return true on success, false on failure
	 */
	public function reopen()
	{
		$result = $this->setProperty(PROPERTY_STATE, TICKETSTATE_OPEN);
		if ( $result )
			addTicketHistoryItem(-1, $this, -1, HISTORYEVENT_TICKET_STATECHANGE, array("verb" => "reopened") );
		return $result;
	}
	
	/**
	 * Get the value of $property for this ticket
	 * @property $property The property to return the value of. This should be a constant from constants.php, but can also be a string with the column name.
	 * @return The value of $property, false on failure
	 */
	public function getProperty($property)
	{
		global $mysql;
		$property = real_escape_string($property);
		
		$result = $mysql->query("SELECT `".$property."` FROM `tickets` WHERE `id` = ".$this->getID());
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_result($result, 0, $property);
	}
	
	/**
	 * Get all properties for this ticket as an array. The key of each value is the column name, which matches a constant in constants.php
	 * @return An array of all properties for this Ticket
	 */
	public function getProperties()
	{
		global $mysql;
		$result = $mysql->query("SELECT * FROM `tickets` WHERE `id` = ".$this->getID());
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_fetch_array($result);
	}
	
	/**
	 * Set a property for this ticket
	 * @property $property The property to set
	 * @property $value The value to set property to
	 * @return mysqli_result object on success, false on failure
	 */
	public function setProperty($property, $value)
	{
		global $mysql;
		$property = real_escape_string($property);
		$value = real_escape_string($value);
		return $mysql->query("UPDATE tickets SET `".$property."` = '".$value."' WHERE `id` = ".$this->getID());
	}
	
	/**
	 * Get the state label for this ticket. (New ticket, helper replied, closed, etc)
	 * TODO: Move the HTML generation out of this function
	 * @return The label as HTML for this ticket
	 */
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