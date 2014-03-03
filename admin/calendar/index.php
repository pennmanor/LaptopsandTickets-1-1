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
include_once("../../include.php");

$from = array_key_exists("from",$_GET) && $_GET["from"] != "" ? $_GET["from"] : "Monday this week";
$to = array_key_exists("to",$_GET) && $_GET["to"] != "" ? $_GET["to"] : "Friday this week";

$fromStamp = strtotime($from);
$toStamp = strtotime($to);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Sign log</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<link rel="stylesheet" href="../../css/main.css">
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">
	<link rel="icon" href="../../favicon.ico" type="image/x-icon">
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
					<li><a href="../issues">Issues</a></li>
					<li><a href="../students">Students</a></li>
					<li class="active"><a href="#">Calendar</a></li>
					<?php if ( $showFeedbackForm ) { ?><li><a href="../feedback">Feedback</a></li><?php } ?>
				</ul>
			</div>
		</div>
	</div>
	<br>
	<div class="container">
		<p class="lead" id="filter">Help Desk Logs from <?php echo $from ?> to <?php echo $to ?>.</p>
		<form>
			<fieldset>
				<legend>Log Filter</legend>
				<label>From:</label>
				<input type="date" name="from" value="<?php echo $from; ?>">
				<label>To:</label>
				<input type="date" name="to" value="<?php echo $to; ?>"><br>
				<button type="submit" class="btn">Submit</button>
			</fieldset>
		</form>
	<?php
	
	$query = "SELECT `student`, `type`, `timestamp` FROM `history` WHERE `type` in (\"".HISTORYEVENT_SIGNIN."\",\" ".HISTORYEVENT_SIGNOUT."\") AND `timestamp` BETWEEN ".($fromStamp)." AND ".($toStamp);
	$result = $mysql->query($query);
	while($row = mysqli_fetch_array($result)) {
		$student = new Student($row["student"]);
		$status = $row["type"] == HISTORYEVENT_SIGNIN ? "signed in" : "signed out";
		$color = $row["type"] == HISTORYEVENT_SIGNIN ? "success" : "info";
		?>
		<div class="alert alert-<?php echo $color; ?>"><strong><?php echo $student->getName()?></strong> <?php echo $status;?> on <strong><?php echo date("M d, Y", $row['timestamp'])." at ".date("g:i A", $row['timestamp'])?></strong></div>
	<?php } ?>
	</div>
		<script type="text/javascript" src="../../js/jquery-1.9.1.js"></script>
		<script type="text/javascript" src="../../js/underscore-min.js"></script>
		<script src="../../js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript" src="../../js/calendar.js"></script>
		<script src="../../js/app.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript">
		</script>
</body>
</html>