<?php
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