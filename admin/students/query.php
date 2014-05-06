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
$showBox = RESULT_NONE;

$students = Student::search($_GET['query']);
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

</head>


<body>
	<div class="navbar navbar-static-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brandimg" href="../index.php"><img src="../../img/pmsd.png"></a>
				<ul class="nav">
					<li><a href="../index.php">Overview</a></li>
					<li><a href="../tickets">Tickets</a></li>
					<li><a href="../laptops">Laptops</a></li>
					<li class="active"><a href="../students">Students</a></li>
					<li><a href="../calendar">Logs</a></li>
					<?php if ( $showFeedbackForm ) { ?><li><a href="../feedback">Feedback</a></li><?php } ?>
				</ul>
				
				<form class="navbar-search pull-right" action="query.php">
				  <input type="text" class="search-query" name="query" placeholder="Search Students">
				</form>
			</div>
		</div>
	</div>
	<br><br>
	<div class="container">
		
		<?php if ( is_string($showBox) ) { ?>
			<div class="alert"><?php echo $showBox; ?></div>
		<?php } ?>
		
		<span class="sectionHeader">Search</span>
		<hr>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Student ID</th>
					<th>Name</th>
					<th>Grade</th>
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
						<td>
							<?php if ($laptop) { ?>
								<a href="../laptops/laptop.php?id=<?php echo $laptop->getID(); ?>" class="btn btn-inverse">Laptop</a>
							<?php } else { ?>
								<a href="#" class="btn btn-inverse disabled">Laptop</a>
							<?php } ?>
							<a href="student.php?sid=<?php echo $student->getID(); ?>" class="btn btn-inverse">History</a>
						</td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
		
		<div class="pagination pagination-centered">
		  <ul>
		    <li class="<?php if ( $pageNumber == 1 ) echo "disabled"; ?>"><a href="<?php if ( $pageNumber == 1 ) echo "#"; else echo "query.php?page=".($pageNumber-1); ?>">Prev</a></li>
			<?php 
			for ( $i = 0; $i < $nPages; $i++ )
			{
				$p = $i+1;
			?>
		    <li class="<?php if ( $pageNumber == $p ) echo "active"; ?>"><a href="query.php?page=<?php echo $p; ?>"><?php echo $p; ?></a></li>
			<?php
			}
			?>
		    <li class="<?php if ( $pageNumber == $nPages ) echo "disabled"; ?>"><a href="<?php if ( $pageNumber == $nPages ) echo "#"; else echo "query.php?page=".($pageNumber+1); ?>">Next</a></li>
		  </ul>
		</div>
	</div>
</body>
</html>
