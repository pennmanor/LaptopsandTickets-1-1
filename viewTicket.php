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
$ticket = Ticket::getByProperty(PROPERTY_ID, $_GET['id']);
$properties = $ticket->getProperties();

if ( array_key_exists("close", $_GET) )
{
	$ticket->close();
	header("Location: viewTicket.php?id=".$_GET['id']);
	die();
}
else if ( array_key_exists("open", $_GET) )
{
	$ticket->reopen();
	header("Location: viewTicket.php?id=".$_GET['id']);
	die();
}
else if ( array_key_exists("reply", $_GET) )
{
	htmlspecialcharsArray($_GET);
	$ticket->addReply($session->getID(), $_GET['reply']);
	header("Location: viewTicket.php?id=".$_GET['id']);
	die();
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
		</div>
		<br><br>
		<div class="container">
			<span class="sectionHeader">View Ticket: <?php echo $properties[PROPERTY_TITLE]; ?></span>
			<?php		
			if ( $properties[PROPERTY_STATE] == TICKETSTATE_OPEN )
			{
			?>
				<button class="btn btn-success pull-right" onClick="window.location = 'viewTicket.php?id=<?php echo $_GET['id']; ?>&close=true'">Mark as Solved</button>
			<?php
				}
			else
			{
			?>
				<button class="btn btn-success pull-right" onClick="window.location = 'viewTicket.php?id=<?php echo $_GET['id']; ?>&open=true'">Reopen Ticket</button>
			<?php
			}
			?>
			<hr>
			<?php 
			if ( $properties[PROPERTY_STATE] == TICKETSTATE_OPEN )
			{
			?>
			<form action="" method="get">
				<fieldset>
					<textarea class="notesBox" rows="5" name="reply" placeholder="Reply"></textarea><br>
					<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
					<input type="submit" class="btn btn-primary pull-right">
				</fieldset>
			</form>
			<hr>
			<?php
			}
			?>
			<?php echo Ticket::getHTMLForHistory($ticket->getHistory(SORT_DESC)); ?>
		</div>
	</body>
</html>
