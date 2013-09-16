<?php
$requiresAdmin = true;
require_once("../../include.php");

$laptops = getLaptopsByIssueType($_GET['issueType']);

?>
<!DOCTYPE html>
<html>
<head>
	<title>1:1 Inventory</title>
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
						<li><a href="../tickets">Tickets</a></li>
						<li class="active"><a href="./index.php">Laptops</a></li>
						<li><a href="../students">Students</a></li>
						<li><a href="../calendar">Calendar</a></li>
						<?php if ( $showFeedbackForm ) { ?><li><a href="../feedback">Feedback</a></li><?php } ?>
					</ul>
				
					<form class="navbar-search pull-right" action="./query.php">
					  <input type="text" class="search-query" name="query" placeholder="Search Laptops">
					</form>
				</div>
			</div>
		</div>
		<br><br>
		<div class="container">
			<?php
			if ( empty($laptops) )
			{
				echo "<br><div class=\"alert alert-block\"><h4>Not Found</h4>No laptops matched your query.</div>";
			}
			else
			{
			?>
			<span class="sectionHeader">View by issue: <?php echo $issueTypes[$_GET['issueType']]; ?></span>
			<hr>
			
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Hostname</th>
						<th>Asset Tag</th>
						<th>Serial #</th>
						<th>Ethernet MAC</th>
						<th>Wireless MAC</th>
						<th>Building</th>
						<th></th>
					</tr>
				</thead>
				
				<tbody>
					<?php
					foreach ( $laptops as $laptop )
					{
					?>
					<tr>
						<td><?php echo $laptop->getProperty(PROPERTY_HOSTNAME); ?></td>
						<td><?php echo $laptop->getProperty(PROPERTY_SERIAL); ?></td>
						<td><?php echo $laptop->getProperty(PROPERTY_ASSETTAG); ?></td>
						<td><?php echo $laptop->getProperty(PROPERTY_EMAC); ?></td>
						<td><?php echo $laptop->getProperty(PROPERTY_WMAC); ?></td>
						<td><?php echo $buildingList[$laptop->getProperty(PROPERTY_BUILDING)]; ?></td>
						<td><a class="btn btn-inverse" href="laptop.php?id=<?php echo $laptop->getProperty(PROPERTY_ID); ?>">Details</a></td>
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
