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

$student = false;
$studentProperties = false;
$laptop = false;
$laptopProperties = false;
$studentHistory = array();
if ( array_key_exists("sid", $_GET) )
{
	$student = Student::getByProperty(PROPERTY_SID, $_GET['sid']);
	$laptop = $student->getLaptop();
	if ( $laptop )
		$laptopProperties = $laptop->getProperties();
	$studentProperties = $student->getProperties();
	$history = $student->getHistory();
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>1:1 Inventory</title>
	<script src="http://code.jquery.com/jquery.js"></script>
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
				
					<form class="navbar-search pull-right" action="./query.php">
					  <input type="text" class="search-query" name="query" placeholder="Search Students">
					</form>
				</div>
			</div>
		</div>
		<br>
		<div class="container">
				<h1><?php echo $studentProperties[PROPERTY_NAME]; ?></h1> 
				<span><?php echo $student->getID(); ?></span>
				<hr>
			
				<span class="sectionHeader">Information</span>
				<hr>
				<table class="table table-bordered">
					<tr>
						<td><strong>Name</strong></td>
						<td><?php echo $studentProperties[PROPERTY_NAME]; ?></td>
					</tr>
				
					<tr>
						<td><strong>Student ID</strong></td>
						<td><?php echo $studentProperties[PROPERTY_SID]; ?></td>
					</tr>
				
					<tr>
						<td><strong>Grade</strong></td>
						<td><?php echo $studentProperties[PROPERTY_GRADE]; ?></td>
					</tr>
				
					<tr>
						<td><strong>Laptop</strong></td>
						<td>
						<?php
						if ( $laptop ) { 
						?>
						<a href="../laptops/laptop.php?id=<?php echo $laptop->getID(); ?>"><?php echo $laptop->getProperty(PROPERTY_HOSTNAME); ?>
						<?php
						} else {
							echo "No laptop assigned.";
						}
						?>	
						</td>
					</tr>
				</table>
				<br>
				<span class="sectionHeader">History</span>
				<hr>
				<?php echo Student::getHTMLForHistory($history); ?>
				<br>
		</div>
	</body>

</html>
