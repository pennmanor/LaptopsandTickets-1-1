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
		$query = "SELECT `data` FROM `History` WHERE `student` = \"".$studentId."\" AND `action` = \"".HISTORYEVENT_SIGNIN."\" ORDER BY `timestamp` ASC LIMIT 1";
		$result = $this->mysqli->query($query);
		if(!$result){
			throw new Exception($studentId." failed to construct because mysql query failed: ".$this->mysqli->error);
			return false;
		}
		$row = mysqli_fetch_assoc($result);
		$data = unserialize($row["data"]);
		$this->status = $data["status"];
		$this->studentId = $studentId;
	}

	public function getStudentId(){
		return $this->studentId;
	}

	public function getStatus(){
		return $this->status;
	}

	public function signin(){
		addHistoryItem(-1, HELPER_SIGNIN, HISTORYEVENT_SIGNIN);
	}

	public function signout(){
		addHistoryItem(-1, HELPER_SIGNOUT, HISTORYEVENT_SIGNIN);
	}

	public static function exists($studentId){
		global $helpers;
		return array_key_exists($studentId, $helpers);
	}

	public static function getLoggedIn(){
		global $mysqli;
		$output = Array();
		$query = "SELECT `ticket`, `student` FROM `History` WHERE `action` = \"".HISTORYEVENT_SIGNIN."\" `ticket` = \"".HELPER_SIGNIN."\" GROUP BY `student` ORDER BY `timestamp`";
		$result = $mysqli->query($query);
		while($row = mysqli_fetch_array($result)){
			$data = unserialize($row["action"]);
			if($data == HELPER_SIGNIN){
				$output[] = $row["student"];
			}
		}
	}
}
?>
