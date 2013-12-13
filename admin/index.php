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
$requiresAdmin = true;
require_once("../include.php");

$helper = new Helper($session->getID());
if ( array_key_exists("signin", $_GET) )
{
	$helper->signin(0,0);
	header("Location: index.php");
	die();
}
else if ( array_key_exists("signout", $_GET) )
{
	$helper->signout(0,0);
	header("Location: index.php");
	die();
}

$laptops = Laptop::getAll();
$nLaptopsAssigned = 0;

foreach ($laptops as $laptop)
{
	if ( $laptop->getOwner() )
		$nLaptopsAssigned++;
}


$nLaptops = count($laptops);
$nLaptopsUnassigned = $nLaptops-$nLaptopsAssigned;

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
					<a class="brandimg" href="./index.php"><img src="../img/pmsd.png"></a>
					<ul class="nav">
						<li class="active"><a href="./index.php">Overview</a></li>
						<li><a href="./tickets">Tickets</a></li>
						<li><a href="./laptops">Laptops</a></li>
						<li><a href="./students">Students</a></li>
						<li><a href="./calendar">Calendar</a></li>
						<?php if ( $showFeedbackForm ) { ?><li><a href="./feedback">Feedback</a></li><?php } ?>
					</ul>

					<ul class="nav pull-right">
						<li>
							<?php
							$helper = new Helper($session->getID());
							if ( $helper->isSignedIn() == HISTORYEVENT_SIGNIN )
							{
							?>
								<button class="btn" onClick="window.location='./index.php?signout=true'">Show as Unavailable</button>
							<?php
							}
							else
							{
							?>
								<button class="btn" onClick="window.location='./index.php?signin=true'">Show as Available</button>
							<?php
							}
							?>
						</li>
						<li><a href="../index.php">Exit</a></li>
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
							<td><span class="overviewHeader">Laptop Inventory</span></td>
						</tr>
						<tr>
							<td><strong>Total</strong> <span class="badge badge-info pull-right"><?php echo $nLaptops; ?></span></td>
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
							<td><span class="overviewHeader">Laptop Service Statistics</span></td>
						</tr>
						
						<?php
						$allHistory = Laptop::getAllHistory();
						$issueCounts = array();
						$highestIssueCount = -1;
						foreach ( $allHistory as $event )
						{
							if ( $event['action'] == HISTORYEVENT_SERVICE )
							{
								$issueCounts[$event['data']['type']]++;
							}
						}
						
						$issueMean = 0;
						foreach ( $issueTypes as $k => $issue )
						{
							if ( $issueCounts[$k] > $highestIssueCount )
								$highestIssueCount = $issueCounts[$k];
							$issueMean += $issueCounts[$k];
						}
						
						$issueMean /= count($issueTypes);
						
						foreach ( $issueTypes as $k => $issue )
						{
							if ( !array_key_exists($k, $issueCounts) )
								$issueCounts[$k] = 0;
							$count = $issueCounts[$k];
							$color = "badge-info";
							
							if ( $count == 0 )
								$color="";
							else if ( $count == $highestIssueCount )
								$color = "badge-important";
							else if ( $count >= $issueMean )
								$color="badge-warning";
						?>
						<tr>
							<td><a href="laptops/issueInfo.php?issueType=<?php echo $k; ?>"><strong><?php echo $issue; ?></strong></a> <span class="badge <?php echo $color; ?> pull-right"><?php echo $count; ?></span></td>
						</tr>
						<?php
						}
						?>

							
					</table>
				</div>
				
				<div class="span4">
					<table class="table table-bordered">
						<tr>
							<td><span class="overviewHeader">Tickets Statistics</span></td>
						</tr>
						
						<tr>
							<td><strong>Total</strong> <span class="badge badge-info pull-right"><?php echo $nTickets; ?></span></td>
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