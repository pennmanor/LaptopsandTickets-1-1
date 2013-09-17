<?php
require_once("include.php");
$showBox = RESULT_NONE;
if ( array_key_exists("create", $_POST) )
{
	if ( empty($_POST['like']) || empty($_POST['dislike']) )
		$showBox = RESULT_FAIL;
	else
	{
		Feedback::create($session->getID(), $_POST['like'], $_POST['dislike']);
		$showBox = RESULT_SUCCESS;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>1:1</title>
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
</head>

	<body>
		<div class="navbar navbar-static-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="./index.php">1:1</a>
					<ul class="nav">
						<li><a href="./index.php">Home</a></li>
						<li><a href="./allTickets.php">My Tickets</a></li>
						<li><a href="./newTicket.php">New Ticket</a></li>
						<li class="active"><a href="./feedbackForm.php">Feedback</a></li>
						
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
		<br><br>
		<div class="container">
			<span class="sectionHeader">Feedback</span>
			<hr>
			<?php
			if ( $showBox == RESULT_FAIL )
			{
			?>
			<div class="alert alert-error">Please make sure you have filled out both forms.</div>
			<?php
			}
			
			if ( $showBox == RESULT_SUCCESS )
			{
			?>
				<div class="alert alert-success">Thank you. Your feedback will help improve the 1:1 program.</div>
			<?php } else { ?>
			We are constantly working on trying to improve the 1:1 program. Your feedback is important. Tell us what you think:<br><br>
			<form action="" method="post">
				<textarea class="notesBox" name="like" rows="5" placeholder="What do you like about the 1:1 laptop?"><?php echo $_POST['like']; ?></textarea><br>
				<textarea class="notesBox" name="dislike" rows="5" placeholder="What do you dislike about the 1:1 laptop, or what do you think can be improved?"><?php echo $_POST['dislike']; ?></textarea><br>
				<input type="submit" name="create" value="Submit" class="btn btn-success pull-right">
			</form>
			<?php } ?>
		</div>
	</body>

</html>
