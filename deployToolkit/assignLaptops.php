<?php
require_once("APIlib.php");

$api = new InventoryAPI("http://127.0.0.1/LaptopInventory/admin/api.php", "qegethgewfgwrthyawrhtea5yt34345t");

while ( true )
{
	$studentID = intval(getField("Student ID"));
	$laptopTag = intval(getField("Asset Tag"));
	
	if ( confirm("Confirm assignment of ".$laptopTag." to ".$studentID."?") ) 
	{
		if ( isSuccess($api->assignLaptop($laptopTag, $studentID)) )
			echo "-> Assigned ".$laptopTag." to ".$studentID."\n";
	}
	else
		echo "Request stopped.\n";
}
?>