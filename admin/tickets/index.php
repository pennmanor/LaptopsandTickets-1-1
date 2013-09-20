<?php
$requiresAdmin = true;
require_once("../../include.php");
$showBox = RESULT_NONE;
if ( array_key_exists("create", $_POST) )
{
	htmlspecialcharsArray($_POST);
	$student = Student::getByProperty(PROPERTY_SID, $_POST['student']);
	if ( !$student )
		$showBox = RESULT_FAIL;
	else
	{
		$ticket = Ticket::create($_POST['student'], $_POST['title'], "Ticket automatically created");
		if ( $ticket )
			$ticket->assignHelper($session->getID());
		else
			$showBox = RESULT_FAIL;
	}
}

$tickets = Ticket::getAllByProperty("state", TICKETSTATE_OPEN);

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
					<a class="brandimg" href="../index.php"><img src="../../img/pmsd.png"></a>
					<ul class="nav">
						<li><a href="../index.php">Overview</a></li>
						<li class="active"><a href="../tickets">Tickets</a></li>
						<li><a href="../laptops">Laptops</a></li>
						<li><a href="../students">Students</a></li>
						<li><a href="../calendar">Calendar</a></li>
						<?php if ( $showFeedbackForm ) { ?><li><a href="../feedback">Feedback</a></li><?php } ?>
					</ul>
				
					<form class="navbar-search pull-right" action="./query.php">
					  <input type="text" class="search-query" name="query" placeholder="Search Tickets">
					</form>
				</div>
			</div>
		</div>
		<br>
		<div class="container">
			<?php
			if ( $showBox == RESULT_FAIL )
			{
			?>
			<div class="alert alert-error">
			  <strong>Error</strong>&nbsp;&nbsp;Invalid information was submitted
			</div>
			<br>
			<?php
			}
			?>
			
			<button class="btn btn-small btn-inverse" onclick="window.location = 'all.php'">View All Tickets</button><br><br>
			<span class="sectionHeader">Unassigned Tickets</span>
			<hr>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Creator</th>
						<th>Title</th>
						<th></th>
					</tr>
				</thead>
				<?php
				foreach ($tickets as $ticket)
				{
					$properties = $ticket->getProperties();
					if ( $properties[PROPERTY_HELPER] == 0 )
					{
				?>
					<tbody>
						<tr>
							<td><?php echo $ticket->getStudent()->getName(); ?></td>
							<td><?php echo $properties[PROPERTY_TITLE]; ?></td>
							<td><button class="btn btn-inverse" onClick="window.location = 'ticket.php?id=<?php echo $properties[PROPERTY_ID]; ?>'">View</button></td>
						</tr>
					</tbody>
				<?php
					}
				}
				?>
			</table>
			<br>
			
			<span class="sectionHeader">Tickets Assigned To Me</span>
			<hr>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Creator</th>
						<th>Title</th>
						<th></th>
					</tr>
				</thead>
				<?php
				foreach ($tickets as $ticket)
				{
					$properties = $ticket->getProperties();
					if ( $properties[PROPERTY_HELPER] == $session->getID() )
					{
						$mostRecentEntry = $ticket->getHistory();
						$mostRecentEntry = $mostRecentEntry[0];
				?>
					<tbody>
						<tr>
							<td><?php echo $ticket->getStudent()->getName(); ?></td>
							<td><?php echo $properties[PROPERTY_TITLE]."&nbsp;&nbsp;".$ticket->getStateLabel($ticket); ?></td>
							<td><button class="btn btn-inverse" onClick="window.location = 'ticket.php?id=<?php echo $properties[PROPERTY_ID]; ?>'">View</button></td>
						</tr>
					</tbody>
				<?php
					}
				}
				?>
			</table>
			<br>
			
			
			<span class="sectionHeader">Open Tickets</span>
			<hr>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Creator</th>
						<th>Title</th>
						<th>Helper</th>
						<th></th>
					</tr>
				</thead>
				<?php
				foreach ($tickets as $ticket)
				{
					$properties = $ticket->getProperties();
				?>
					<tbody>
						<tr>
							<td><?php echo $ticket->getStudent()->getName(); ?></td>
							<td><?php echo $properties[PROPERTY_TITLE]."&nbsp;&nbsp;".$ticket->getStateLabel($ticket); ?></td>
							<td><?php
								$helper = $ticket->getHelper();
								if ( $helper )
									echo $helper->getName();
								else
									echo "Unassigned";
							?></td>
							<td><button class="btn btn-inverse" onClick="window.location = 'ticket.php?id=<?php echo $properties[PROPERTY_ID]; ?>'">View</button></td>
						</tr>
					</tbody>
				<?php
				}
				?>
			</table>
			<br>
			
			<span class="sectionHeader">Create Ticket</span>
			<hr>
			<form action="" method="post">
				<input type="text" name="title" placeholder="Title"><br>
				<input type="text" name="student" placeholder="Student"><br>
				<input type="submit" name="create" value="Create" class="btn btn-success">
			</form>
			<br>
		</div>
	</body>
</html>