<?php
$requiresAdmin = true;
require_once("../include.php");
$laptops = Laptop::getAll();
$nLaptopsAssigned = 0;

foreach ($laptops as $laptop)
{
	if ( $laptop->getOwner() )
		$nLaptopsAssigned++;
}


$nLaptops = count($laptops);
$nLaptopsUnassigned = $nLaptops-$nAssigned;
$tickets = Ticket::getAll();
$nTickets = count($tickets);
$nTicketsOpen = count(Ticket::getAllByProperty(PROPERTY_STATE, TICKETSTATE_OPEN));
$nTicketsClosed = $nTickets-$nTicketsOpen;
?>
<!DOCTYPE html>
<html>
<head>
	<title>1:1</title>
	<link href="../css/bootstrap.css" rel="stylesheet">
	<link href="../css/style.css" rel="stylesheet">
</head>
	<body>
		<div class="navbar navbar-static-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="./index.php">1:1</a>
					<ul class="nav">
						<li class="active"><a href="./index.php">Overview</a></li>
						<li><a href="./tickets">Tickets</a></li>
						<li><a href="./laptops">Laptops</a></li>
					</ul>

					<ul class="nav pull-right">
						<li class="pull-right"><a href="../index.php">Exit</a></li>
					</ul>
				</div>
			</div>
		</div>
		<br><br>
		<div class="container">
			<span class="sectionHeader">Overview</span>
			<hr>
			<div class="row">
				<div class="span4">
					<table class="table table-bordered">
						<tr>
							<td><span class="overviewHeader">Laptops</span></td>
						</tr>
						<tr>
							<td><strong>Laptops</strong> <span class="badge badge-info pull-right"><?php echo $nLaptops; ?></span></td>
						</tr>
						
						<tr>
							<td><strong>Assigned</strong> <span class="badge badge-success pull-right"><?php echo $nLaptopsAssigned; ?></span></td>
						</tr>
						
						<tr>
							<td><strong>Unassigned</strong> <span class="badge pull-right"><?php echo $nLaptopsUnassigned; ?></span></td>
						</tr>
							
					</table>
				</div>
				
				
				<div class="span4">
					<table class="table table-bordered">
						<tr>
							<td><span class="overviewHeader">Laptop Service</span></td>
						</tr>
						
						<?php
						$allHistory = Laptop::getAllHistory();
						$issueCounts = array();
						foreach ( $allHistory as $event )
						{
							if ( $event['action'] == HISTORYEVENT_SERVICE )
							{
								$issueCounts[$event['data']['type']]++;
							}
						}
						
						foreach ( $issueTypes as $k => $issue )
						{
							if ( !array_key_exists($k, $issueCounts) )
								$issueCounts[$k] = 0;
						?>
						<tr>
							<td><strong><?php echo $issue; ?></strong> <span class="badge badge-info pull-right"><?php echo $issueCounts[$k]; ?></span></td>
						</tr>
						<?php
						}
						?>

							
					</table>
				</div>
				
				<div class="span4">
					<table class="table table-bordered">
						<tr>
							<td><span class="overviewHeader">Tickets</span></td>
						</tr>
						
						<tr>
							<td><strong>Tickets</strong> <span class="badge badge-info pull-right"><?php echo $nTickets; ?></span></td>
						</tr>
						
						<tr>
							<td><strong>Open</strong> <span class="badge badge-warning pull-right"><?php echo $nTicketsOpen; ?></span></td>
						</tr>
						
						<tr>
							<td><strong>Resolved</strong> <span class="badge badge-success pull-right"><?php echo $nTicketsClosed; ?></span></td>
						</tr>
							
					</table>
				</div>
			</div>
		</div>
	</body>

</html>