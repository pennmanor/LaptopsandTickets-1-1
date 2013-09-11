<?php
class Helper extends Student{
	
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

	public static function exists($studentId){
		global $helpers;
		return in_array(strval($studentId), $helpers);
	}
}
?>
