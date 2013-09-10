<?php
class Api{
	private $id;
	private $key;
	private $name;
	
	function __construct($key){
		global $mysql;
		if(!is_a($mysql, "mysqli")){
			throw new Exception("API Object Failed to construct because no \$mymsql");
		}
		echo $query = "SELECT `id`, `key`, `name` FROM `apikeys` WHERE `key` = \"".$mysql->real_escape_string($key)."\"";
		$result = $mysql->query($query);
		if(!$result || mysqli_affected_rows($mysql) == 0) {
			//throw new Exception("Invalid key");
		}
		$row = mysqli_fetch_assoc($result);
		$this->id = $row["id"];
		$this->key = $row["key"];
		$this->name = $row["name"];
		
	}

	public function getID(){
		return $this->id;
	}

	public function getKey(){
		return $this->key;
	}

	public function getName(){
		return $this->name;
	}
}
?>
