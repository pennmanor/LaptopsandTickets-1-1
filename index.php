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
$skipAuth = !array_key_exists("login", $_GET) ;

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
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/main.css">
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
	<div class="navbar navbar-static-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brandimg" href="./index.php"><img src="./img/pmsd.png"></a>
				<ul class="nav">
					<li class="active"><a href="./index.php">Home</a></li>
					<?php if ( $session->isAuthenticated() ) { ?>
						<li><a href="./allTickets.php">My Tickets</a></li>
						<li><a href="./newTicket.php">New Ticket</a></li>
						<?php if ( $showFeedbackForm ) { ?><li><a href="./feedbackForm.php">Feedback</a></li><?php } ?>
					<?php } ?>
				</ul>
				<?php
				if ( $session->isAuthenticated() )
					echo "<button class=\"btn pull-right\" onClick=\"window.location = 'index.php?logout=true'\">Logout</button>";
				else
					echo "<button class=\"btn pull-right\" onClick=\"window.location = 'index.php?login=true'\">Login</button>";

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
				echo "<p> No one is currently at the Student Help Desk. You are welcome to <a href=\"newTicket.php\">submit a ticket</a>.</p>";
			}
			?>
			<a href="newTicket.php" class="btn btn-large btn-primary">Submit a Ticket</a>
			<?php if ( $showFeedbackForm ) { ?><a href="feedbackForm.php" class="btn btn-large btn-primary">Send Feedback</a><?php } ?>
			<a href="http://blogs.pennmanor.net/1to1/" class="btn btn-large btn-primary">Read the Blog</a>
			<br><br>
			<p>Remember to login with your Penn Manor Google Docs user name and password</p>
		</div>
		<!-- <hr>
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
		</div> -->
	</div>
</body>	
</html>
