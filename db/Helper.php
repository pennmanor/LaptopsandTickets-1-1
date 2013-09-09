<?php
class Helper{
	private $mymsqli;
	private $studentId;
	private $name;
	private $status;

	function __construct($studentId){
		global $helpers;
		if(!is_numeric($studentId))
			throw new Exception("Id is not number. ");
		global $mysqli;
		if(!is_a($mysqli, "mysqli"))
			throw new Exception($studentId."Helper Object Failed to construct because no \$mymsqli");
		$this->mysqli = $mysqli;
		$this->studentId = $studentId;
		$query = "SELECT `action` FROM `History` WHERE `student` = \"".$studentId."\" AND `action` = \"".HISTORYEVENT_SIGNIN."\" OR `student` = \"".$studentId."\" AND `action` = \"".HISTORYEVENT_SIGNOUT."\" ORDER BY `timestamp` DESC LIMIT 1";
		$result = $this->mysqli->query($query);
		if(!$result){
			throw new Exception($studentId." failed to construct because mysql query failed: ".$this->mysqli->error);
			return false;
		}
		$row = mysqli_fetch_assoc($result);
		switch($row["action"]){
			case HISTORYEVENT_SIGNIN:
			$this->status = HELPER_SIGNIN;
			break;
			default:
			case HISTORYEVENT_SIGNOUT:
			$this->status = HELPER_SIGNOUT;
			break;
		}
		$this->studentId = $studentId;
	}

	public function getStudentId(){
		return $this->studentId;
	}

	public function getStatus(){
		return $this->status;
	}

	public function signin(){
		addHistoryItem(-1, $this->studentId, HISTORYEVENT_SIGNIN);
	}

	public function signout(){
		addHistoryItem(-1, $this->studentId, HISTORYEVENT_SIGNOUT);
	}

	public static function exists($studentId){
		global $helpers;
		return in_array(strval($studentId), $helpers);
	}
}
?>
