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
require_once(dirname(__FILE__)."/Helper.php");
require_once(dirname(__FILE__)."/Feedback.php");
require_once(dirname(__FILE__)."/Api.php");

/**
 * Convert new lines to HTML line breaks.
 * @param $string The string to be processed
 * @return The processed string
 */
function nl_fix($string)
{
	$string = str_replace(array("\\r\\n", "\\r", "\\n"), "<br />", $string);
	$string = str_replace(array("\r\n", "\r", "\n"), "<br />", $string);
	return $string;
}

/**
 * Preforms htmlspecialchars() on every element in an array
 * @param &$arr Reference to an array to be processed
 * @return Nothing, referenced array is directly updated
 */
function htmlspecialcharsArray(&$arr)
{
	foreach ($arr as $k => $v)
	{
		$arr[$k] = htmlspecialchars($v);
	}
}

/**
 * Return part of an array
 * @param $input The input array
 * @param $start The array index to start at
 * @param $end The array index to stop at
 * @return An array with the data from $input's $start to $end
 */
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

/**
 * Wrapper function to allow replacement of mysql_result with mysqli_result
 * @param $result A mysqli_result object
 * @param $row The index of the row
 * @param $field The field to return
 * @return The $field in $row key in $result
 */
function mysqli_result($result, $row, $field)
{
	$result->data_seek($row);
	$a = $result->fetch_array();
	return $a[$field];
}

/**
 * Wrapper function to allow easy replacement of mysql_real_escape_string, since mysqli_real_escape_string requires a connection object passed to it
 * @param $str The string to escape
 * @return The escasped string
 */
function real_escape_string($str)
{
	global $mysql;
	return mysqli_real_escape_string($mysql, $str);
}

?>
