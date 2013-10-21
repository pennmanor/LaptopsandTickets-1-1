<?php
/*
  Copyright 2013 Penn Manor School District, Andrew Lobos, and Benjamin Thomas

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/
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
	echo "Hostname,AssetTag,Serial,EthernetMAC,WirelessMAC,OwnerID,OwnerName,OwnerGrade,Building\n";
	foreach ( $laptops as $laptop )
	{
		$owner = $laptop->getOwner();
		$ownerID = "";	
		$ownerGrade = "";
		$ownerName = "";
		if ( $owner )	
		{
			$ownerName = $owner->getProperty(PROPERTY_NAME);
			$ownerGrade = $owner->getProperty(PROPERTY_GRADE);	
			$ownerID = $owner->getID();
		}
		$data = $laptop->getProperties();
		echo $data['hostname'].",".$data['assetTag'].",".$data['serial'].",".$data['ethernetMAC'].",".$data['wirelessMAC'].",".$ownerID.",".$ownerName.",".$ownerGrade.",".$data['building']."\n";
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
else if ( array_key_exists("feedback", $_GET) )
{
	$feedback = Feedback::getAll();
	setFilename("feedback.csv");
	echo "Student ID,Liked,Dislike\n";
	foreach ($feedback as $entry)
	{
		$data = $entry->getProperties();
		$data[PROPERTY_LIKE] = str_replace("\n", "", $data[PROPERTY_LIKE]);
		$data[PROPERTY_LIKE] = str_replace("\r", "", $data[PROPERTY_LIKE]);
		$data[PROPERTY_LIKE] = str_replace('"', '""', $data[PROPERTY_LIKE]);
		
		$data[PROPERTY_DISLIKE] = str_replace("\n", "", $data[PROPERTY_DISLIKE]);
		$data[PROPERTY_DISLIKE] = str_replace("\r", "", $data[PROPERTY_DISLIKE]);
		$data[PROPERTY_DISLIKE] = str_replace('"', '""', $data[PROPERTY_DISLIKE]);
		
		echo $data[PROPERTY_SID].",\"".$data[PROPERTY_LIKE]."\",\"".$data[PROPERTY_DISLIKE]."\"\n";
	}
	die();
}


echo "Invalid report URL";
?>