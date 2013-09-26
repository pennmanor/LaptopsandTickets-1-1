<?php
$requiresAdmin = true;
require_once("../../include.php");
$tickets = Ticket::search($_GET['query']);
if ( count($tickets) == 1)
{
	header("Location: ticket.php?id=".$tickets[0]->getID());
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
					  <input type="text" class="search-query" name="query" value="<?php echo $_GET['query']; ?>" placeholder="Search Tickets">
					</form>
				</div>
			</div>
		</div>
		<br><br>
		<div class="container">
			<?php
			if ( empty($tickets) )
			{
				echo "<br><div class=\"alert alert-block\"><h4>Not Found</h4>No tickets matched your query.</div>";
			}
			else
			{
			?>
			<span class="sectionHeader">Search</span>
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
							<td><?php echo $properties[PROPERTY_TITLE]."&nbsp;&nbsp;".$ticket->getStateLabel(); ?></td>
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
			<?php
			}
			?>
			<br>
		</div>
	</body>
</html>