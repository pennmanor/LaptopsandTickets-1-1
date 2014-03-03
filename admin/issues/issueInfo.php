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
					<a class="brandimg" href="../index.php"><img src="../../img/pmsd.png"></a>
					<ul class="nav">
						<li><a href="../index.php">Overview</a></li>
						<li><a href="../tickets">Tickets</a></li>
						<li><a href="../laptops/index.php">Laptops</a></li>
						<li class="active"><a href="../issues">Issues</a></li>
						<li><a href="../students">Students</a></li>
						<li><a href="../calendar">Logs</a></li>
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
						<th># Issues</th>
						<th>Building</th>
						<th></th>
					</tr>
				</thead>
				
				<tbody>
					<?php
					foreach ( $laptops as $laptop )
					{
						$history = $laptop->getHistory();
						$numIssues = 0;
						foreach ( $history as $event )
						{
							if ( $event['type'] == HISTORYEVENT_SERVICE )
								$numIssues++;
						}
					?>
					<tr>
						<td><?php echo $laptop->getProperty(PROPERTY_HOSTNAME); ?></td>
						<td><?php echo $laptop->getProperty(PROPERTY_SERIAL); ?></td>
						<td><?php echo $laptop->getProperty(PROPERTY_ASSETTAG); ?></td>
						<td><?php echo $laptop->getProperty(PROPERTY_EMAC); ?></td>
						<td><?php echo $laptop->getProperty(PROPERTY_WMAC); ?></td>
						<td><?php echo $numIssues; ?></td>
						<td><?php echo $buildingList[$laptop->getProperty(PROPERTY_BUILDING)]; ?></td>
						<td><a class="btn btn-inverse" href="../laptops/laptop.php?id=<?php echo $laptop->getProperty(PROPERTY_ID); ?>">Details</a></td>
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
