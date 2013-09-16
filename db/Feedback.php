<?php
class Feedback
{
	public static function create($sid, $liked, $dislike)
	{
		global $mysql;
		$sid = $mysql->escape_string($sid);
		$liked = $mysql->escape_string($liked);
		$dislike = $mysql->escape_string($dislike);
		
		$result = $mysql->query("INSERT INTO `feedback` (`sid`, `like`, `dislike`) VALUES('".$sid."', '".$liked."', '".$dislike."')");
		return new Feedback($mysql->insert_id);
	}
	
	public static function reset()
	{
		global $mysql;
		return $mysql->query("DELETE FROM `feedback`");
	}
	
	public static function getAll()
	{
		global $mysql;
		$output = array();
		$result = $mysql->query("SELECT id FROM `feedback`");
		while ( $d = $result->fetch_array() )
			$output[] = new Feedback($d['id']);
		return $output;
	}
	
	public function __construct($id)
	{
		$this->id = $id;
	}
	
	public function getID()
	{
		return $this->id;
	}
	
	public function getProperties()
	{
		global $mysql;
		$result = $mysql->query("SELECT * FROM `feedback` WHERE `id` = ".$this->getID());
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_fetch_array($result);
	}
	
	public function getProperty($property)
	{
		global $mysql;
		$property = real_escape_string($property);
		
		$result = $mysql->query("SELECT `".$property."` FROM `feedback` WHERE `id` = ".$this->getID());
		if ( !$result || mysqli_num_rows($result) == 0 )
			return false;
		return mysqli_result($result, 0, $property);
	}

	
	public function setProperty($key, $value)
	{
		global $mysql;
		$property = real_escape_string($property);
		$value = real_escape_string($value);
		
		return $mysql->query("UPDATE feedback SET `".$property."` = '".$value."' WHERE `id` = ".$this->getID());
	}
}
?>