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
class Api{
	private $id;
	private $key;
	private $name;
	
	/**
	 * Creates a new Api object.
	 * Information about this Api key is accessed and cached in the constructor
	 * @param $key The API key
	 */
	function __construct($key){
		global $mysql;
		if(!is_a($mysql, "mysqli")){
			throw new Exception("API Object Failed to construct because no \$mymsql");
		}
		$query = "SELECT `id`, `key`, `name` FROM `apikeys` WHERE `key` = \"".$mysql->real_escape_string($key)."\"";
		$result = $mysql->query($query);
		if( !$result || $result->num_rows == 0) {
			throw new Exception("Invalid key");
		}
		$row = mysqli_fetch_assoc($result);
		$this->id = $row["id"];
		$this->key = $row["key"];
		$this->name = $row["name"];
		
	}

	/**
	 * Get the database ID of the row this object describes
	 * @return This object's DB ID
	 */
	public function getID(){
		return $this->id;
	}

	/**
	 * Get the API key that this object describes
	 * @return This object's API key
	 */
	public function getKey(){
		return $this->key;
	}

	/**
	 * Get the friendly name of the API key that this object describes
	 * @return This object's friendly name
	 */
	public function getName(){
		return $this->name;
	}
}
?>
