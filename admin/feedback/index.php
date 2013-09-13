<?php
$requiresAdmin = true;
require_once("../../include.php");
if ( !$showFeedbackForm )
	die("Feedback form is disabled in config.php");

if ( array_key_exists("removeFeedback", $_GET) )
{
	Feedback::reset();
	header("Location: index.php");
	die();
}
$entries = Feedback::getAll();
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
						<li><a href="../laptops">Laptops</a></li>
						<li><a href="../students">Students</a></li>
						<li><a href="../calendar">Calendar</a></li>
						<li class="active"><a href="../feedback">Feedback</a></li>
					</ul>
				</div>
			</div>
		</div>
		<br><br>
		<div class="container">
			<span class="sectionHeader">Feedback</span>
			<button class="btn btn-info pull-right" onclick="window.location='../laptops/reportGen.php?feedback=true'">Download as CSV</button>
			<button class="btn btn-danger pull-right buttonSpacer" onclick="if (confirm('Remove all feedback entries?')) window.location='index.php?removeFeedback=true'">Reset Feedback</button>
			<hr>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Student ID</th>
						<th>Liked</th>
						<th>Disliked</th>
					</tr>
				</thead>
				
				<tbody>
					<?php
					foreach ($entries as $entry)
					{
						$data = $entry->getProperties();
					?>
						<tr>
							<td><?php echo $data[PROPERTY_SID]; ?></td>
							<td><?php echo nl_fix(htmlspecialchars($data[PROPERTY_LIKE])); ?></td>
							<td><?php echo nl_fix(htmlspecialchars($data[PROPERTY_DISLIKE])); ?></td>
						</tr>
					<? 
					}
					?>
				</tbody>
			</table>
		</div>
	</body>

</html>