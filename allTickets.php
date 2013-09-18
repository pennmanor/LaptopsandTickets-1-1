<?php
require_once("include.php");
$tickets = Ticket::getAllByProperty(PROPERTY_STUDENT, $session->getID());
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
					<a class="brandimg" href="./index.php"><img src="./img/pmsd.png"></a>
					<ul class="nav">
						<li><a href="./index.php">Home</a></li>
						<li class="active"><a href="./allTickets.php">My Tickets</a></li>
						<li><a href="./newTicket.php">New Ticket</a></li>
						<?php if ( $showFeedbackForm ) { ?><li><a href="./feedbackForm.php">Feedback</a></li><?php } ?>
						
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
			<span class="sectionHeader">Tickets</span>
			<hr>
			<?php
			if ( count($tickets) == 0 )
			{
			?>
			<div class="alert">
				You do not have any tickets.
			</div>
			<?php
			}
			else
			{
			?>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Title</th>
							<th>Date</th>
							<th></th>
						</tr>
					</thead>
			
					<tbody>
						<?php
						foreach ($tickets as $ticket)
						{
							$properties = $ticket->getProperties();
						?>
							<tr>
								<td><?php echo $properties[PROPERTY_TITLE]."&nbsp;&nbsp;".$ticket->getStateLabel(); ?> </td>
								<td><?php echo date("M d, Y", $properties[PROPERTY_TIMESTAMP])." at ".date("g:i A", $properties[PROPERTY_TIMESTAMP]); ?></td>
								<td><button class="btn btn-inverse pull-right" onClick="window.location = 'viewTicket.php?id=<?php echo $properties[PROPERTY_ID]; ?>'">View</button></td>
							</tr>
						<?php
						}
						?>
					</tbody>
				</table>
			<?php
			}
			?>
		</div>
	</body>

</html>