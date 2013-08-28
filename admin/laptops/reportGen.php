<?php
$requiresAdmin = true;
require_once("../../include.php");

header('Cache-Control: must-revalidate, post-check=0, pre-check=0'); 
header('Content-Type: application/octet-stream'); 

function getStringForAction($action)
{
	switch ( $action )
	{
		case ACTION_CREATE:
			return "Create";
		case ACTION_UNASSIGN:
			return "Unassign";
		case ACTION_ASSIGN:
			return "Assign";
		default:
			return "Invalid Action ID";
	}
}

function setFilename($filename)
{
	header("Content-Disposition: attachment; filename=\"".$filename."\"");  
}


if ( array_key_exists("getHistoryCSV", $_GET) )
{
	$laptop = Laptop::getByProperty(PROPERTY_ID, $_GET['getHistoryCSV']);
	$history = $laptop->getHistory();
	setFilename($laptop->getProperty(PROPERTY_ASSETTAG)."History.csv");
	echo "Action,Student,Date\n";
	foreach ( $history as $row )
	{
		echo getStringForAction($row['action']).",".$row['student'].",".date("M d Y", $row['timestamp'])." ".date("g:i A", $row['timestamp'])."\n";
	}
	die();
}
else if ( array_key_exists("fullListCSV", $_GET) )
{
	$laptops = Laptop::getAll();
	setFilename("laptops.csv");
	echo "Hostname,AssetTag,Serial,EthernetMAC,WirelessMAC,Owner,OwnerGrade,Building\n";
	foreach ( $laptops as $laptop )
	{
		$owner = $laptop->getOwner();	
		$ownerGrade = "";
		if ( $owner )	
		{
			$ownerGrade = $owner->getProperty(PROPERTY_GRADE);	
			$owner = $owner->getID();
		}
		$data = $laptop->getProperties();
		echo $data['hostname'].",".$data['assetTag'].",".$data['serial'].",".$data['ethernetMAC'].",".$data['wirelessMAC'].",".$owner.",".$ownerGrade.",".$data['building']."\n";
	}
	die();
}
else if ( array_key_exists("fullListDHCP", $_GET) )
{
	$laptops = Laptop::getAll();
	setFilename("laptopsDHCP.conf");
	foreach ( $laptops as $laptop )
	{
		$properties = $laptop->getProperties();
		echo "#".$properties['hostname']."\n";
		if ( !empty($properties['wirelessMAC']) )
		{
			echo "host ".$properties['hostname']."w {\n";
			echo "	hardware ethernet ".$properties['wirelessMAC'].";\n";
			echo "}\n";
		}
		
		echo "\n";
		
		if ( !empty($properties['ethernetMAC']) )
		{
			echo "host ".$properties['hostname']."e {\n";
			echo "	hardware ethernet ".$properties['ethernetMAC'].";\n";
			echo "}\n";
		}
		echo "\n\n\n";
	}
	die();
}

else if ( array_key_exists("searchListCSV", $_GET) )
{
	$laptops = Laptop::search($_GET['searchListCSV']);
	setFilename("laptops.csv");
	echo "Hostname,AssetTag,Serial,EthernetMAC,WirelessMAC,Owner\n";
	foreach ( $laptops as $laptop )
	{
		$properties = $laptop->getProperties();
		$owner = $laptop->getOwner();	
		if ( $owner )	
			$owner = $owner->getID();
		echo $properties['hostname'].",".$properties['assetTag'].",".$properties['serial'].",".$properties['ethernetMAC'].",".$properties['wirelessMAC'].",".$owner."\n";
	}
	die();
}
else if ( array_key_exists("searchListDHCP", $_GET) )
{
	$laptops = Laptop::search($_GET['searchListDHCP']);
	setFilename("laptopsDHCP.conf");
	foreach ( $laptops as $laptop )
	{
		$properties = $laptop->getProperties();
		echo "#".$properties['hostname']."\n";
		if ( !empty($properties['wirelessMAC']) )
		{
			echo "host ".$properties['hostname']."w {\n";
			echo "	hardware ethernet ".$properties['wirelessMAC'].";\n";
			echo "}\n";
		}
		
		echo "\n";
		
		if ( !empty($properties['ethernetMAC']) )
		{
			echo "host ".$properties['hostname']."e {\n";
			echo "	hardware ethernet ".$properties['ethernetMAC'].";\n";
			echo "}\n";
		}
		echo "\n\n\n";
	}
	die();
}


echo "Invalid report URL";
?>