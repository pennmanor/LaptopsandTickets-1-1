<?php
require_once "Auth/OpenID/Consumer.php";
require_once "Auth/OpenID/FileStore.php";
require_once "Auth/OpenID/AX.php";

$openIDbasePath = "auth/finishLogin.php";

$openID_filestore = new Auth_OpenID_FileStore("/tmp");
$openID = new Auth_OpenID_Consumer($openID_filestore);
?>