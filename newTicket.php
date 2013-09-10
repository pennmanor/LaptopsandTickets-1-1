<?php
require_once("include.php");
$showBox = RESULT_NONE;
if ( array_key_exists("create", $_POST) )
{
	htmlspecialcharsArray($_POST);
	if ( empty($_POST['title']) || empty($_POST['body']) )
		$showBox = RESULT_FAIL;
	else
	{
		$ticket = Ticket::create($session->getID(), $_POST['title'], $_POST['body']);
		if ( $ticket )
		{
			header("Location: viewTicket.php?id=".$ticket->getID());
			die();
		}
		else
			$showBox = RESULT_FAIL;
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
						<li class="active"><a href="./newTicket.php">New Ticket</a></li>
						
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
			<span class="sectionHeader">New Ticket</span>
			<hr>
			<?php
			if ( $showBox == RESULT_FAIL )
			{
			?>
			<div class="alert alert-error">Please make sure you have filled out both forms correctly</div>
			<?php
			}
			?>
			
			<form action="" method="post">
				<input class="notesBox" type="text" name="title" placeholder="Title" value="<?php echo $_POST['title']; ?>"><br>
				<textarea class="notesBox" name="body" rows="10" placeholder="Issue Description"><?php echo $_POST['body']; ?></textarea><br>
				<input type="submit" name="create" value="Create" class="btn btn-success pull-right">
			</form>

		</div>
	</body>

</html>