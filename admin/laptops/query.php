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

$laptops = Laptop::search(trim($_GET['query']));
if ( count($laptops) == 1 )
{
	header("Location: laptop.php?id=".$laptops[0]->getID());
	die();
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>1:1 Inventory</title>
	<link href="../../css/bootstrap.css" rel="stylesheet">
	<link href="../../css/style.css" rel="stylesheet">
	<script type="text/javascript">
	
	function csvDL()
	{
		window.location = "reportGen.php?searchListCSV=<?php echo $_GET['query']; ?>";
	}
	
	function dhcpDL()
	{
		window.location = "reportGen.php?searchListDHCP=<?php echo $_GET['query']; ?>";
	}
	
	function handleDetailsClick(id)
	{
		window.location ="laptop.php?id="+id;
	}
	</script>
</head>

	<body>
		<div class="navbar navbar-static-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brandimg" href="../index.php"><img src="../../img/pmsd.png"></a>
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
			<span class="sectionHeader">Search</span>
			<button class="btn btn-info pull-right" onclick="csvDL()">Download as CSV</button><button class="btn btn-info pull-right buttonSpacer" onclick="dhcpDL()">Download as DHCP config</button>
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
						<td><?php echo $laptop->getProperty(PROPERTY_ASSETTAG); ?></td>
						<td><?php echo $laptop->getProperty(PROPERTY_SERIAL); ?></td>
						<td><?php echo $laptop->getProperty(PROPERTY_EMAC); ?></td>
						<td><?php echo $laptop->getProperty(PROPERTY_WMAC); ?></td>
						<td><?php echo $buildingList[$laptop->getProperty(PROPERTY_BUILDING)]; ?></td>
						<td><button class="btn btn-inverse" onClick="handleDetailsClick(<?php echo $laptop->getProperty(PROPERTY_ID); ?>)">Details</button></td>
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
