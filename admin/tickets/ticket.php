<?php
$requiresAdmin = true;
require_once("../../include.php");
$ticket = Ticket::getByProperty(PROPERTY_ID, $_GET['id']);
$properties = $ticket->getProperties();

if ( array_key_exists("transfer", $_GET) )
{
	$ticket->assignHelper($_GET['transfer']);
	header("Location: ticket.php?id=".$_GET['id']);
	die();
}
else if ( array_key_exists("close", $_GET) )
{
	$ticket->close();
	header("Location: ticket.php?id=".$_GET['id']);
	die();
}
else if ( array_key_exists("open", $_GET) )
{
	$ticket->reopen();
	header("Location: ticket.php?id=".$_GET['id']);
	die();
}
else if ( array_key_exists("reply", $_GET) )
{
	htmlspecialcharsArray($_GET);
	$ticket->addReply($session->getID(), $_GET['reply']);
	header("Location: ticket.php?id=".$_GET['id']);
	die();
}


?>
<!DOCTYPE html>
<html>
<head>
	<title>1:1</title>
	<link href="../../css/bootstrap.css" rel="stylesheet">
	<link href="../../css/style.css" rel="stylesheet">
	
</head>

	<body>
		<div class="navbar navbar-static-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="../index.php">1:1</a>
					<ul class="nav">
						<li><a href="../index.php">Overview</a></li>
						<li class="active"><a href="../tickets">Tickets</a></li>
						<li><a href="../laptops">Laptops</a></li>
						<li><a href="../students">Students</a></li>
					</ul>
				
					<form class="navbar-search pull-right" action="./query.php">
					  <input type="text" class="search-query" name="query" placeholder="Search Tickets">
					</form>
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
				<button class="btn btn-danger pull-right" onClick="window.location = 'ticket.php?id=<?php echo $_GET['id']; ?>&close=true'">Close Ticket</button>
			<?php
				}
			else
			{
			?>
				<button class="btn btn-success pull-right" onClick="window.location = 'ticket.php?id=<?php echo $_GET['id']; ?>&open=true'">Reopen Ticket</button>
			<?php
			}
			?>
			
			<?php
			if ( !$properties[PROPERTY_HELPER] )
			{
			?>
				<button class="btn btn-info pull-right buttonSpacer" onClick="window.location = 'ticket.php?id=<?php echo $_GET['id']; ?>&transfer=<?php echo $session->getID(); ?>'">Assign to Me</button>
			<?php
			}
			
			$laptop = $ticket->getStudent()->getLaptop();
			if ( $laptop )
			{
			?>
			<button class="btn btn-info pull-right buttonSpacer" onClick="window.location = '../laptops/laptop.php?id=<?php echo $laptop->getID(); ?>'">View Laptop</button>
			<?php
			}
			?>
			<hr>

			<?php echo Ticket::getHTMLForHistory($ticket->getHistory(SORT_ASC)); ?>

			<hr>
			
			<form action="" method="get">
				<textarea class="notesBox" rows="5" name="reply" placeholder="Reply"></textarea><br>
				<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
				
				<div class="btn-group dropup">
				  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				    Transfer
				    <span class="caret"></span>
				  </a>
				  <ul class="dropdown-menu">
					  <?php
					  foreach ($helpers as $helper)
					  {
						  if ( $helper != $properties[PROPERTY_HELPER] )
						  {
						  	$student = new Student($helper);
					  ?>
				    	  	<li><a href="ticket.php?id=<?php echo $_GET['id']; ?>&transfer=<?php echo $student->getID(); ?>"><?php echo $student->getName(); ?></a></li>
					  <?php
				  		  }
				  	  }
					  ?>
				  </ul>
				</div>
				
				
				<input type="submit" class="btn btn-primary pull-right">
			</form>
			<br><br>
		</div>
		
		<script src="http://code.jquery.com/jquery.js"></script>
	  	<script src="../../js/bootstrap.min.js"></script>
	</body>
</html>