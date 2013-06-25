<?php
function getField($name)
{
	echo $name.": ";
	return trim(fgets(STDIN));
}

function confirm($text)
{
	$d = strtolower(getField($text." (y/n)"));
	return $d == "y"|| $d == "yes";
}

function isSuccess($response)
{
	if ( trim($response) == "OK" )
		return true;
	else
		echo "Request failed with: ".$response."\n";
	return false;
}

class InventoryAPI
{
	public function __construct($url, $key)
	{
		$this->url = $url;
		$this->key = $key;
	}
	
	public function sendRequest($query)
	{
		return file_get_contents($this->url."?apiKey=".$this->key."&".$query);
	}
	
	
	public function assignLaptop($laptop, $to)
	{
		return $this->sendRequest("action=assign&student=".$to."&laptop=".$laptop);
	}
	
	public function createLaptop($hostname, $assetTag, $serial, $wMAC, $eMAC, $building)
	{
		return $this->sendRequest("action=addLaptop&hostname=".$hostname."&assetTag=".$assetTag."&serial=".$serial."&wirelessMAC=".$wMAC."&ethernetMAC=".$eMAC."&building=".$building);
	}
}
?>