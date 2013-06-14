<?php
mysql_connect($databaseInfo['host'], $databaseInfo['username'], $databaseInfo['password']);
mysql_select_db($databaseInfo['database']);

require_once(dirname(__FILE__)."/constants.php");
require_once(dirname(__FILE__)."/History.php");
require_once(dirname(__FILE__)."/Laptop.php");
require_once(dirname(__FILE__)."/Student.php");

?>
