<?php
class Helper extends Student{
	
	/**
	 * Check if this Helper is at the helpdesk
	 * @return true if the helper is at the helpdesk, false otherwise
	 */
	public function isSignedIn(){
		global $mysql;
		$query = "SELECT `action` FROM `history` WHERE `student` = \"".$this->getID()."\" AND `action` = \"".HISTORYEVENT_SIGNIN."\" OR `student` = \"".$this->getID()."\" AND `action` = \"".HISTORYEVENT_SIGNOUT."\" ORDER BY `timestamp` DESC LIMIT 1";
		$result = $mysql->query($query);
		if(!$result)
			return false;
		$row = mysqli_fetch_assoc($result);
		
		return $row["action"];
	}

	public function signin($id, $name){
		addHistoryItem(-1, $this->getID(), HISTORYEVENT_SIGNIN, array("id" => $id, "name" => $name));
	}

	public function signout($id, $name){
		addHistoryItem(-1, $this->getID(), HISTORYEVENT_SIGNOUT, array("id" => $id, "name" => $name));
	}
	
	/**
	 * Check if a student ID is a helper ID
	 * @param $studentId The student ID to check
	 * @return true if the student is a helper, false otherwise
	 */
	public static function exists($studentId){
		global $helpers;
		return in_array(strval($studentId), $helpers);
	}
}
?>
