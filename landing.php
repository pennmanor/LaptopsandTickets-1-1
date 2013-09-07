<?php
include_once("include.php");
include_once("mysqli-fix.php");
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
	<style>
	body{
		padding-top:10px;
		padding-left:20px;
		padding-right:20px;
	}
	</style>
</head>
<body>
	<div class="container">
		<div class="hero-unit text-center">
			<h1>Student Help Desk</h1>
			<br>
			<?php
			$signedIn = Array();
			foreach($helpers as $h){
				$helper = new Helper($h);
				if($helper->getStatus())
					$signedIn[] = $helper->getStudentId();
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
			<button class="btn btn-large btn-primary">Submit a Ticket</button>
			<button class="btn btn-large btn-primary">Read the Blog</button>
		</div>
		<hr>
		<div class="row-fluid">
			<div class="offset2 span8">
				<div class="row-fluid">
					<div class="span6">
						<h4><a href="#">Ticket System</a></h4>
						<p>Submit a ticket about a problem or issue you are having with your computer.</p>
					</div>
					<div class="span6">
						<h4><a href="#">Blog</a></h4>
						<p>Read useful articles about your computer and how it works.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- // <script src="js/jquery-1.9.1.js"></script>
	// <script src="js/bootstrap.min.js"></script> -->
</body>	
</html>
