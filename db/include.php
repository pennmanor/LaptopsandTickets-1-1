<?php
$mysql = new mysqli($databaseInfo['host'], $databaseInfo['username'], $databaseInfo['password']);
if ( !$mysql )
	die("Database connection failed.\n");
$mysql->select_db($databaseInfo['database']);

require_once(dirname(__FILE__)."/constants.php");
require_once(dirname(__FILE__)."/UserSession.php");
require_once(dirname(__FILE__)."/History.php");
require_once(dirname(__FILE__)."/Laptop.php");
require_once(dirname(__FILE__)."/Ticket.php");
require_once(dirname(__FILE__)."/Student.php");

function nl_fix($string)
{
	$string = str_replace(array("\\r\\n", "\\r", "\\n"), "<br />", $string);
	$string = str_replace(array("\r\\n", "\r", "\n"), "<br />", $string);
	return $string;
}

function htmlspecialcharsArray(&$arr)
{
	foreach ($arr as $k => $v)
	{
		$arr[$k] = htmlspecialchars($v);
	}
}

function array_subset($input, $start, $end)
{
	$inputMaxIndex = count($input);
	if ( $inputMaxIndex == 0 )
		return $input;
	
	if ( $start == $inputMaxIndex )
		return array($input[$inputMaxIndex]);
	
	if ( $end > $inputMaxIndex )
		$end = $inputMaxIndex;
	
	$output = array();
	for ( $i = $start; $i < $end; $i++ )
		$output[] = $input[$i];
	return $output;
}


function mysqli_result($result, $row, $field)
{
	$result->data_seek($row);
	$a = $result->fetch_array();
	return $a[$field];
}

function real_escape_string($str)
{
	global $mysql;
	return mysqli_real_escape_string($mysql, $str);
}

?>
