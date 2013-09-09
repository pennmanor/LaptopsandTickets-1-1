<?php
class Api{
	private $id;
	private $key;
	private $name;
	
	function __construct($key){
		global $mysql;
		if(!is_a($mysql, "mysqli")){
			throw new Exception($studentId."API Object Failed to construct because no \$mymsql");
		}
		$query = "SELECT `id`, `key`, `name` FROM `apikeys` WHERE `key` = \"".$key."\"";
		$result = $mysql->query($query);
		if(!$result){
			throw new Exception($studentId." Invalid key");
		}
		$row = mysqli_fetch_assoc($result);
		$this->id = $row["id"];
		$this->key = $row["key"];
		$this->name = $row["name"];
	}

	public function getId(){
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
