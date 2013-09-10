<?php
$requiresAdmin = true;
require_once("../../include.php");
$showBox = RESULT_NONE;
if ( array_key_exists("query", $_GET) )
{
	$student = Student::getByProperty(PROPERTY_SID, $_GET['query']);
	if ( !$student )
		$showBox = "The student ".$_GET['query']." does not exist in the database.";
	else
	{
		$laptop = $student->getLaptop();
		if ( $laptop )
		{
			header("Location: ../laptops/laptop.php?id=".$laptop->getID());
			die();
		}
		else
			$showBox = $student->getID()." does not have a laptop assigned.";
	}
}


$students = Student::getAllWithLaptop();
$nPages = ceil(count($students)/$itemsPerPage);

$pageNumber = intval($_GET['page']);
if ( $pageNumber < 1 )
{
	$pageNumber = 1;
}

$itemStart = ($pageNumber-1)*$itemsPerPage;
$itemEnd = $itemStart+$itemsPerPage;
$students = array_subset($students, $itemStart, $itemEnd);
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
		window.location = "../laptops/reportGen.php?fullListCSV=true";
	}
	</script>
</head>


<body>
	<div class="navbar navbar-static-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="../index.php">1:1</a>
				<ul class="nav">
					<li><a href="../index.php">Overview</a></li>
					<li><a href="../tickets">Tickets</a></li>
					<li><a href="../laptops">Laptops</a></li>
					<li class="active"><a href="../students">Students</a></li>
					<li><a href="../../calendar">Calendar</a></li>
				</ul>
				
				<form class="navbar-search pull-right" action="">
				  <input type="text" class="search-query" name="query" placeholder="Find by student ID">
				</form>
			</div>
		</div>
	</div>
	<br><br>
	<div class="container">
		
		<?php if ( is_string($showBox) ) { ?>
			<div class="alert"><?php echo $showBox; ?></div>
		<?php } ?>
		
		<span class="sectionHeader">All Students with Laptops Assigned</span>
		<button class="btn btn-info pull-right" onclick="csvDL()">Download as CSV</button>
		<hr>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Student ID</th>
					<th>Name</th>
					<th>Grade</th>
					<th>Building</th>
					<th></th>
				</tr>
			</thead>
			
			<tbody>
				<?php
				foreach ( $students as $student )
				{
					$properties = $student->getProperties();
					$laptop = $student->getLaptop();
				?>
					<tr>
						<td><?php echo $student->getID(); ?></td>
						<td><?php echo $properties[PROPERTY_NAME]; ?></td>
						<td><?php echo $properties[PROPERTY_GRADE]; ?></td>
						<td><?php echo $buildingList[$laptop->getProperty(PROPERTY_BUILDING)]; ?></td>
						<td><a href="../laptops/laptop.php?id=<?php echo $laptop->getID(); ?>" class="btn btn-inverse">Laptop</a></td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
		
		<div class="pagination pagination-centered">
		  <ul>
		    <li class="<?php if ( $pageNumber == 1 ) echo "disabled"; ?>"><a href="<?php if ( $pageNumber == 1 ) echo "#"; else echo "index.php?page=".($pageNumber-1); ?>">Prev</a></li>
			<?php 
			for ( $i = 0; $i < $nPages; $i++ )
			{
				$p = $i+1;
			?>
		    <li class="<?php if ( $pageNumber == $p ) echo "active"; ?>"><a href="index.php?page=<?php echo $p; ?>"><?php echo $p; ?></a></li>
			<?php
			}
			?>
		    <li class="<?php if ( $pageNumber == $nPages ) echo "disabled"; ?>"><a href="<?php if ( $pageNumber == $nPages ) echo "#"; else echo "index.php?page=".($pageNumber+1); ?>">Next</a></li>
		  </ul>
		</div>
	</div>
</body>
</html>