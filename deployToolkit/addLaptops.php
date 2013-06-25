<?php
require_once("APIlib.php");

$api = new InventoryAPI("http://127.0.0.1/LaptopInventory/admin/api.php", "qegethgewfgwrthyawrhtea5yt34345t");

$hostPrefix = "hslin";
$building = "High School";

while(true)
{
	$assetTag = getField("Asset Tag");
	$serial = getField("Serial");
	$wMAC = getField("Wireless MAC");
	$eMAC = getField("Ethernet MAC");
	$hostname = $hostPrefix.$assetTag;
	$confirm = confirm("Add ".$hostname." with the above information?");
	if ( $confirm )
	{
		if ( isSuccess($api->createLaptop($hostname, $assetTag, $serial, $wMAC, $eMAC, $building)) )
			echo "-> Added ".$hostname."\n";
	}
}

?>