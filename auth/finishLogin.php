<?php
$skipAuth = true;
require_once("../include.php");
require_once("authCommon.php");
$response = $openID->complete($openIDbaseURL.$openIDbasePath);

$identifier = $response->getDisplayIdentifier();

if ( $response->status == Auth_OpenID_SUCCESS )
{
	$axFetcher = new Auth_OpenID_AX_FetchResponse();
	$axList = $axFetcher->fromSuccessResponse($response);
	
	
	$email = $axList->data["http://axschema.org/contact/email"][0];
	$email = explode("@", $email);
	
	// Verify that the student exists in the students table
	if ( !$session->login($email[0]) )
	{
		echo "Your account is not in the system database. <a href='".$openIDlogoutURL."'>Logout</a>";
		die();
	}
	
	
	header("Location: ../index.php");
}
else
{
	echo "OpenID auth failed!<br><br>";
	print_r($response);
}
?>
