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

$openID_request = $openID->begin($openIDproviderURL);

if ( !$openID_request )
	die("Failed to create OpenID request.");

$ax = new Auth_OpenID_AX_FetchRequest();
$ax->add(Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/email',2,1, 'email'));
$openID_request->addExtension($ax);

$redirectTo = $openID_request->redirectURL($openIDbaseURL, $openIDbaseURL.$openIDbasePath);

header("Location: ".$redirectTo);

?>