<?php
require_once("../config.php");
require_once("../db/include.php");

$mysql->query("use olddb");
$result = $mysql->query("SELECT * FROM history");
$mysql->query("ALTER TABLE history CHANGE action type int(11)");
$mysql->query("ALTER TABLE history ADD subtype int(11) AFTER type");
$mysql->query("ALTER TABLE history CHANGE data body TEXT");

$d = false;
while ( $d = $result->fetch_assoc() )
{
	if ( !empty($d) )
	{
		$data = unserialize($d['body']);
		$id = $d['id'];
		
		if ( array_key_exists("type", $data) && array_key_exists("notes", $data) )
		{
			$data['notes'] = mysql_real_escape_string($data['notes']);
			$mysql->query("UPDATE history SET `subtype` = ".$data['type']." WHERE `id` = ".$id);
			$mysql->query("UPDATE history SET `body` = '".$data['notes']."' WHERE `id` = ".$id);
		}
		else if ( array_key_exists("verb", $data) )
		{
			$data['verb'] = mysql_real_escape_string($data['verb']);
			$mysql->query("UPDATE history SET `body` = '".$data['verb']."' WHERE `id` = ".$id);
		}
		else if ( array_key_exists("body", $data) )
		{
			$data['body'] = mysql_real_escape_string($data['body']);
			$mysql->query("UPDATE history SET `body` = '".$data['body']."' WHERE `id` = ".$id);
		}
	}
}
?>