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
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/db/include.php");

$session = new UserSession();
if ( !$session->isAuthenticated() && !$skipAuth )
{
	header("Location: ".$openIDbaseURL."/auth/startLogin.php");
}

if ( @$requiresAdmin && !$session->isHelper() )
{
	die("You need to be an admin to view this page. You are not an admin.");
}
?>