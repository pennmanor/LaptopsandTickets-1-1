<?php
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
					<a class="brand" href="./index.php">1:1</a>
					<ul class="nav">
						<li><a href="./index.php">Home</a></li>
						<li><a href="./newTicket.php">New Ticket</a></li>
						<li class="active"><a href="#">View Ticket</a></li>
						
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
				<button class="btn btn-danger pull-right" onClick="window.location = 'viewTicket.php?id=<?php echo $_GET['id']; ?>&close=true'">Close Ticket</button>
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
			
			<?php echo Ticket::getHTMLForHistory($ticket->getHistory(SORT_ASC)); ?>

			<?php 
			if ( $properties[PROPERTY_STATE] == TICKETSTATE_OPEN )
			{
			?>
			<form action="" method="get">
				<textarea class="notesBox" rows="5" name="reply" placeholder="Reply"></textarea><br>
				<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
				<input type="submit" class="btn btn-primary pull-right">
			</form>
			<br><br>
			<?php
			}
			?>
		</div>
	</body>
</html>