<?php
$skipAuth = true;
$requiresAdmin = false;
require("../include.php");

$data = mysql_query("SELECT * FROM history");
mysql_query("ALTER TABLE history ADD subtype int(11) AFTER type");
mysql_query("ALTER TABLE history CHANGE data body TEXT");

$d = false;
while ( $d = mysql_fetch_assoc($data) )
{
	if ( !empty($d) )
	{
		$data = unserialize($d['body']);
		$id = $d['id'];
		
		if ( array_key_exists("type", $data) && array_key_exists("notes", $data) )
		{
			$data['notes'] = mysql_real_escape_string($data['notes']);
			mysql_query("UPDATE history SET `subtype` = ".$data['type']." WHERE `id` = ".$id);
			mysql_query("UPDATE history SET `body` = '".$data['notes']."' WHERE `id` = ".$id);
		}
		else if ( array_key_exists("verb", $data) )
		{
			$data['verb'] = mysql_real_escape_string($data['verb']);
			mysql_query("UPDATE history SET `body` = '".$data['verb']."' WHERE `id` = ".$id);
		}
		else if ( array_key_exists("body", $data) )
		{
			$data['body'] = mysql_real_escape_string($data['body']);
			mysql_query("UPDATE history SET `body` = '".$data['body']."' WHERE `id` = ".$id);
		}
	}
}
?>