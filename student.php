<?php
require_once("config.php");
$student = false;
$history = false;
$assignedTo = false;
if ( array_key_exists("id", $_GET) )
{
	$student = $db->getStudentName($_GET['id']);
	$history = $db->getHistoryForStudent($_GET['id']);
	$assignedTo = $db->getStudentAssignment($_GET['id']);
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>1:1 Inventory</title>
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<script type="text/javascript">
	var thisStudentID = <?php echo $_GET['id']; ?>;
	function csvDL()
	{
		window.location = "reportGen.php?getStudentHistoryCSV="+thisStudentID;
	}
	</script>
</head>

	<body>
		<div class="navbar navbar-static-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="./index.php">1:1 Inventory</a>
					<ul class="nav">
						<li><a href="./index.php">Overview</a></li>
						<li><a href="./add.php">Add</a></li>
						<li><a href="./list.php">List</a></li>
						<li class="active"><a href="#">Lookup</a></li>
					</ul>
				
					<form class="navbar-search pull-right" action="./query.php">
					  <input type="text" class="search-query" name="query" placeholder="Search">
					</form>
				</div>
			</div>
		</div>
		<br>
		<div class="container">
			<?php
			if ( $history )
			{
			?>
				<h1><?php echo $student; ?> (<?php echo $_GET['id']; ?>)</h1> 
				
				<?php
				if ( $assignedTo )
					echo "<span class=\"label label-success\">Assigned</span>";
				else
					echo "<span class=\"label\">Unassigned</span>";
				?>
				<hr>		
				<br>
				<span class="sectionHeader">History</span>
				<button class="btn btn-info pull-right" onclick="csvDL()">Download as CSV</button>
				<hr>
			
				<?php echo getHTMLForHistory($history, $db->getLaptops()); ?>
			<?php
			}
			else
			{
			?>
			<div class="alert alert-block">
				<h4>Not Found</h4>
				<?php
				if ( !empty($_GET['id']) )
					echo "The student with the ID ".$_GET['id']." could not be found in the database";
				?>
			</div>
			<?php
			}
			?>
		</div>
	</body>

</html>