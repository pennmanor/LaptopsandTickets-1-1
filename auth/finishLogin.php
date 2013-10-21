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
