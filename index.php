<?php
include_once("include.php");

if ( array_key_exists("logout", $_GET) )
{
	$session->logout();
	header("Location: ".$openIDlogoutURL);
	die();
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Student Help Desk</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/main.css">
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
	<div class="navbar navbar-static-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="./index.php">1:1</a>
				<ul class="nav">
					<li class="active"><a href="./index.php">Home</a></li>
					<li><a href="./allTickets.php">All Tickets</a></li>
					<li><a href="./newTicket.php">New Ticket</a></li>
				</ul>
				<button class="btn pull-right" onClick="window.location = 'index.php?logout=true'">Logout</button>
				<?php
				if ( $session->isHelper() )
				{
				?>
				<ul class="nav pull-right">
					<li class="pull-right"><a href="./admin">Admin</a></li>
				</ul>
				<?php
				}
				?>	
			</div>
		</div>
	</div>
	<br>
	<div class="container">
		<div class="hero-unit text-center">
			<h1>Student Help Desk</h1>
			<br>
			<?php
			$signedIn = Array();
			foreach($helpers as $h){
				$helper = new Helper($h);
				if($helper->IsSignedIn() == HISTORYEVENT_SIGNIN){
					$signedIn[] = $helper->getID();
				}
			}
			if(count($signedIn) > 0){
				echo "<p class=\"lead\">Current helpers at the Student Help Desk:</p>";
				echo "<ul class=\"unstyled\">";
				foreach($signedIn as $s){
					$student = Student::getByProperty(PROPERTY_SID, $s);
					if($student){
						echo "<li><p class=\"lead\">".$student->getName()."</p></li>";
					}
				}
				echo "</ul>";
			}
			else{
				echo "<p> No one is currently at the Student Help Desk. Why don't you try <a href=\"#\">submitting a ticket</a>?</p>";
			}
			?>
			<a href="newTicket.php" class="btn btn-large btn-primary">Submit a Ticket</a>
			<a href="http://blogs.pennmanor.net/1to1/" class="btn btn-large btn-primary">Read the Blog</a>
		</div>
		<hr>
		<div class="row-fluid">
			<div class="offset2 span8">
				<div class="row-fluid">
					<div class="span6">
						<h4><a href="newTicket.php">Ticket System</a></h4>
						<p>Submit a ticket about a problem or issue you are having with your computer.</p>
					</div>
					<div class="span6">
						<h4><a href="http://blogs.pennmanor.net/1to1/">Blog</a></h4>
						<p>Read useful articles about your computer and how it works.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>	
</html>
