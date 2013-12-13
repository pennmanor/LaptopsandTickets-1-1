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
require_once("include.php");
$showBox = RESULT_NONE;
if ( array_key_exists("create", $_POST) )
{
	if ( empty($_POST['like']) || empty($_POST['dislike']) )
		$showBox = RESULT_FAIL;
	else
	{
		Feedback::create($session->getID(), $_POST['like'], $_POST['dislike']);
		$showBox = RESULT_SUCCESS;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>1:1</title>
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
</head>

	<body>
		<div class="navbar navbar-static-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brandimg" href="./index.php"><img src="./img/pmsd.png"></img></a>
					<ul class="nav">
						<li><a href="./index.php">Home</a></li>
						<li><a href="./allTickets.php">My Tickets</a></li>
						<li><a href="./newTicket.php">New Ticket</a></li>
						<li class="active"><a href="./feedbackForm.php">Feedback</a></li>
						
					</ul>
					<button class="btn pull-right" onClick="window.location = 'index.php?logout=true'">Logout</button>
					<?php
					if ( $session->isHelper() )
					{
					?>
					<ul class="nav pull-right">
						<li class="pull-right"><a href="./admin">Admin</a></li>
					</ul>
					<?php
					}
					?>
					
				</div>
			</div>
		</div>
		<br><br>
		<div class="container">
			<span class="sectionHeader">Feedback</span>
			<hr>
			<?php
			if ( $showBox == RESULT_FAIL )
			{
			?>
			<div class="alert alert-error">Please make sure you have filled out both forms.</div>
			<?php
			}
			
			if ( $showBox == RESULT_SUCCESS )
			{
			?>
				<div class="alert alert-success">Thank you. Your feedback will help improve the 1:1 program.</div>
			<?php } else { ?>
			We are constantly working to improve the 1:1 program. Your feedback is important. Please tell us what you think:<br><br>
			<form action="" method="post">
				<textarea class="notesBox" name="like" rows="5" placeholder="What do you like about the 1:1 laptop?"><?php echo $_POST['like']; ?></textarea><br>
				<textarea class="notesBox" name="dislike" rows="5" placeholder="What do you dislike about the 1:1 laptop? What would you like to see improved?"><?php echo $_POST['dislike']; ?></textarea><br>
				<input type="submit" name="create" value="Submit" class="btn btn-success pull-right">
			</form>
			<?php } ?>
		</div>
	</body>

</html>
