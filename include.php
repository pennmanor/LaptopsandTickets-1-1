<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/db/include.php");

$session = new UserSession();
if ( !$session->isAuthenticated() && !$skipAuth )
{
	header("Location: ".$openIDbaseURL."/auth/startLogin.php");
}

if ( $requiresAdmin && !$session->isHelper() )
{
	die("You need to be an admin to view this page. You are not an admin.");
}
?>