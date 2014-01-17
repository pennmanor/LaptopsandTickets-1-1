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

$laptop = false;
$assignedTo = false;
$history = false;

htmlspecialcharsArray($_GET);

if ( array_key_exists("delete", $_GET) )
{
	$laptop = Laptop::getByProperty(PROPERTY_ID, $_GET["delete"]);
	if ( $laptop )
		$laptop->remove();
	header("Location: index.php");
	die();
}

if ( array_key_exists("notesBox", $_GET) )
{
	$laptop = Laptop::getByProperty(PROPERTY_ID, $_GET["id"]);
	if ( $laptop )
		$laptop->setProperty(PROPERTY_NOTES, $_GET['notesBox']);
	header("Location: laptop.php?id=".$_GET['id']);
}

if ( array_key_exists("assign", $_GET) )
{
	$student = Student::getByProperty(PROPERTY_SID, $_GET['to']);
	if ( $student )
	{
		if ( ($l = $student->getLaptop()) )
		{
			header("Location: laptop.php?id=".$_GET['assign']."&studentAlreadyAssignedTo=".$l->getProperty(PROPERTY_ID));
			die();
		}
		else
			$student->setLaptop($_GET['assign']);
		header("Location: laptop.php?id=".$_GET['assign']);
		die();
	}
	else 
	{
		header("Location: laptop.php?id=".$_GET['assign']."&studentDoesNotExist=".$_GET['to']);
		die();
	}
}

if ( array_key_exists("unassign", $_GET) )
{
	$laptop = Laptop::getByProperty(PROPERTY_ID, $_GET["unassign"]);
	if ( $laptop )
	{
		$student = $laptop->getOwner();
		if ( $student )
		{
			$student->clearLaptop();
		}
	}
	header("Location: laptop.php?id=".$_GET['unassign']);
	die();
}

if ( array_key_exists("service", $_GET) )
{
	$laptop = Laptop::getByProperty(PROPERTY_ID, $_GET['service']);
	if ( $laptop )
	{
		$student = $laptop->getOwner();
		if ( !$student )
			$student = -1;
		
		addHistoryItem($laptop, $student, HISTORYEVENT_SERVICE, htmlspecialchars($_GET['serviceNotes']), $_GET['type']);
	}
	header("Location: laptop.php?id=".$_GET['service']);
	die();
}

if ( array_key_exists("id", $_GET) )
{
	$laptop = Laptop::getByProperty(PROPERTY_ID, $_GET['id']);
	if ( $laptop )
	{
		$assignedTo = $laptop->getOwner();
		$history = $laptop->getHistory();
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>1:1 Inventory</title>
	<script src="http://code.jquery.com/jquery.js"></script>
	<link href="../../css/bootstrap.css" rel="stylesheet">
	<link href="../../css/style.css" rel="stylesheet">
	<script type="text/javascript">
	var thisLaptopID = <?php echo $_GET['id']; ?>
	
	function handleAssign()
	{
		var newOwner = false;
		
		while ( !newOwner )
		{
			newOwner = prompt("Please enter a valid student ID for the new owner.");
			if ( !newOwner )
				return;
		}
		window.location = "laptop.php?assign="+thisLaptopID+"&to="+newOwner;
	}
	
	function handleUnassign()
	{
		var yes = confirm("Are you sure you want to unassign this computer from it's current owner?");
		if ( yes )
		{
			window.location = "laptop.php?unassign="+thisLaptopID;
		}
	}
	
	function confirmDelete()
	{
		var yes = confirm("Are you sure you want to remove this computer from the database? You can not undo this change.");
		if ( yes )
		{
			window.location = "laptop.php?delete="+thisLaptopID;
		}
	}
	
	function saveNotes()
	{
		$.ajax("laptop.php",
		{
			data:"notes="+document.getElementById("notesBox").value,
			type:"POST"
		});
	}
	
	function csvDL()
	{
		window.location = "reportGen.php?getHistoryCSV="+thisLaptopID;
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
		<br>
		<div class="container">
			<?php
			if ( array_key_exists("studentAlreadyAssignedTo", $_GET) ) 
			{
			?>
				<div class="alert alert-error">This student already has a <a href="laptop.php?id=<?php echo $_GET['studentAlreadyAssignedTo']; ?>">laptop</a> assigned</div>
			<?php
			}
			else if ( array_key_exists("studentDoesNotExist", $_GET) ) 
			{
			?>
				<div class="alert alert-error">Student ID <?php echo $_GET['studentDoesNotExist']; ?> does not exist in the database.</div>
			<?php
			}

			if ( $laptop )
			{
				$properties = $laptop->getProperties();
			?>
				<h1><?php echo $properties[PROPERTY_HOSTNAME]; ?></h1> 
				
				<?php
				if ( $assignedTo )
					echo "<span class=\"label label-success\">Assigned</span> <button class=\"btn btn-warning btn-mini\" onClick=\"handleUnassign()\">Unassign</button>";
				else
					echo "<span class=\"label\">Unassigned</span> <button class=\"btn btn-success btn-mini\" onClick=\"handleAssign()\">Assign</button>";
				?>
				<hr>
			
				<span class="sectionHeader">System Info</span>
				<hr>
				<table class="table table-bordered">
					<tr>
						<td><strong>Asset Tag</strong></td>
						<td><?php echo $properties[PROPERTY_ASSETTAG]; ?></td>
					</tr>
				
					<tr>
						<td><strong>Serial</strong></td>
						<td><?php echo $properties[PROPERTY_SERIAL]; ?></td>
					</tr>
				
					<tr>
						<td><strong>Ethernet MAC</strong></td>
						<td><?php echo $properties[PROPERTY_EMAC]; ?></td>
					</tr>
				
					<tr>
						<td><strong>Wireless MAC</strong></td>
						<td><?php echo $properties[PROPERTY_WMAC]; ?></td>
					</tr>
					
					<tr>
						<td><strong>Building</strong></td>
						<td><?php echo $buildingList[$properties[PROPERTY_BUILDING]]; ?></td>
					</tr>
				</table>
				<?php
				if ( $assignedTo )
				{
				?>
				<span class="sectionHeader">Current Owner</span>
				<hr>
				<table class="table table-bordered">
					<tr>
						<td><strong>Owner ID</strong></td>
						<td><a href="../students/student.php?sid=<?php echo $assignedTo->getID(); ?>"><?php echo $assignedTo->getID(); ?></a></td>
					</tr>
					<tr>
						<td><strong>Owner Name</strong></td>
						<td><?php echo $assignedTo->getName(); ?></td>
					</tr>

				</table>
				<?php
				}
				?>
				<br>
				
				<span class="sectionHeader">Notes</span>
				<hr> 
				<form action="" method="GET">
					<textarea rows="5" name="notesBox" class="notesBox"><?php echo stripcslashes($properties[PROPERTY_NOTES]); ?></textarea>
					<input type="hidden" name="id" value="<?php echo $properties[PROPERTY_ID]; ?>">
					<input type="submit" class="btn btn-primary pull-right" value="Save">
				</form>
				<br><br><br><br>
				<span class="sectionHeader">Service</span>
				<hr> 
				<form action="" method="GET">
					Issue Type<br>
					<select name="type">
						<?php 
						foreach ( $issueTypes as $k => $issue )
						{
						?>
						<option value="<?php echo $k; ?>"><?php echo $issue; ?></option>	
						<?php
						}
						?>
					</select>
					<br>
					<textarea rows="5" name="serviceNotes" class="notesBox" placeholder="Notes"></textarea>
					<input type="hidden" name="service" value="<?php echo $properties[PROPERTY_ID]; ?>">
					<input class="btn btn-primary pull-right" type="submit" value="Add">
				</form>
				<br><br><br><br>
				<span class="sectionHeader">History</span>
				<button class="btn btn-info pull-right" onclick="csvDL()">Download as CSV</button>
				<hr>
				<?php echo Laptop::getHTMLForHistory($history); ?>
				<button class="btn btn-danger pull-right" onClick='confirmDelete(<?php echo $properties[PROPERTY_ID]; ?>)'>Delete Laptop</button>
			<?php
			}
			else
			{
			?>
			<div class="alert alert-block">
				<h4>Not Found</h4>
				<?php
				if ( !empty($_GET['id']) )
					echo "The laptop with the ID ".$_GET['id']." could not be found in the database";
				?>
			</div>
			<?php
			}
			?>	
		</div>
	</body>

</html>