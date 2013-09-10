<?php
class Helper extends Student{
	
	public function isSignedIn(){
		global $mysql;
		$query = "SELECT `action` FROM `History` WHERE `student` = \"".$this->getID()."\" AND `action` = \"".HISTORYEVENT_SIGNIN."\" OR `student` = \"".$this->getID()."\" AND `action` = \"".HISTORYEVENT_SIGNOUT."\" ORDER BY `timestamp` DESC LIMIT 1";
		$result = $mysql->query($query);
		if(!$result)
			return false;
		$row = mysqli_fetch_assoc($result);
		
		return $row["action"];
	}

	public function signin(){
		addHistoryItem(-1, $this->getID(), HISTORYEVENT_SIGNIN);
	}

	public function signout(){
		addHistoryItem(-1, $this->getID(), HISTORYEVENT_SIGNOUT);
	}

	public static function exists($studentId){
		global $helpers;
		return in_array(strval($studentId), $helpers);
	}
}
?>
