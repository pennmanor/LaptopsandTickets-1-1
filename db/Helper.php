<?php
/*
  Copyright 2013 Penn Manor School District, Andrew Lobos, and Benjamin Thomas

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/

/**
 * Helper class for accessing helper-specific calls
 * @author Ben
 */
class Helper extends Student{
	
	/**
	 * Check if this Helper is at the helpdesk
	 * @return true if the helper is at the helpdesk, false otherwise
	 */
	public function isSignedIn(){
		global $mysql;
		$query = "SELECT `type` FROM `history` WHERE `student` = \"".$this->getID()."\" AND `type` = \"".HISTORYEVENT_SIGNIN."\" OR `student` = \"".$this->getID()."\" AND `type` = \"".HISTORYEVENT_SIGNOUT."\" ORDER BY `timestamp` DESC LIMIT 1";
		$result = $mysql->query($query);
		if(!$result)
			return false;
		$row = mysqli_fetch_assoc($result);
		
		return $row["type"];
	}
	
	/**
	 * Signin this helper
	 * @param $id The ID of the API key being used
	 * @param $name The name of the API key being used
	 */
	public function signin(){
		addHistoryItem(-1, $this->getID(), HISTORYEVENT_SIGNIN);
	}

	/**
	 * Signout this helper
	 * @param $id The ID of the API key being used
	 * @param $name The name of the API key being used
	 */
	public function signout(){
		addHistoryItem(-1, $this->getID(), HISTORYEVENT_SIGNOUT);
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
