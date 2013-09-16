<?php
$requiresAdmin = true;
require_once("../../include.php");

$tickets = Ticket::getAll(SORT_DSEC);
$nPages = ceil(count($tickets)/$itemsPerPage);

$pageNumber = intval($_GET['page']);
if ( $pageNumber < 1 )
{
	$pageNumber = 1;
}

$itemStart = ($pageNumber-1)*$itemsPerPage;
$itemEnd = $itemStart+$itemsPerPage;
$tickets = array_subset($tickets, $itemStart, $itemEnd);
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
						<li><a href="../calendar">Calendar</a></li>
						<?php if ( $showFeedbackForm ) { ?><li><a href="../feedback">Feedback</a></li><?php } ?>
					</ul>
				
					<form class="navbar-search pull-right" action="./query.php">
					  <input type="text" class="search-query" name="query" placeholder="Search Tickets">
					</form>
				</div>
			</div>
		</div>
		<br><br>
		<div class="container">
			<span class="sectionHeader">All Tickets</span>
			<hr>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Creator</th>
						<th>Title</th>
						<th>Helper</th>
						<th>Created</th>
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
							<td><?php echo date("M d, Y", $properties[PROPERTY_TIMESTAMP])." at ".date("g:i A", $properties[PROPERTY_TIMESTAMP]); ?></td>
							<td><button class="btn btn-inverse" onClick="window.location = 'ticket.php?id=<?php echo $properties[PROPERTY_ID]; ?>'">View</button></td>
						</tr>
					</tbody>
				<?php
				}
				?>
			</table>
			
			<div class="pagination pagination-centered">
			  <ul>
			    <li class="<?php if ( $pageNumber == 1 ) echo "disabled"; ?>"><a href="<?php if ( $pageNumber == 1 ) echo "#"; else echo "all.php?page=".($pageNumber-1); ?>">Prev</a></li>
				<?php 
				for ( $i = 0; $i < $nPages; $i++ )
				{
					$p = $i+1;
				?>
			    <li class="<?php if ( $pageNumber == $p ) echo "active"; ?>"><a href="all.php?page=<?php echo $p; ?>"><?php echo $p; ?></a></li>
				<?php
				}
				?>
			    <li class="<?php if ( $pageNumber == $nPages ) echo "disabled"; ?>"><a href="<?php if ( $pageNumber == $nPages ) echo "#"; else echo "all.php?page=".($pageNumber+1); ?>">Next</a></li>
			  </ul>
			</div>
			
			<br>
			
			
		</div>
	</body>
</html>