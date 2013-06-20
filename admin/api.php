<?php
$skipAuth = true;
require_once("../include.php");

if ( $_GET['apiKey'] == $apiKey )
{
	switch ($_GET['action'])
	{
		case "assign":;
			$student = new Student($_GET['student']);
			$laptop  = Laptop::getByProperty(PROPERTY_ASSETTAG, $_GET['laptop']);
			if ( $student->setLaptop($laptop->getID()) )
				echo "OK\n";
			else
				echo "Error!\n";
			
			break;
	}
}
?>