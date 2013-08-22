<?php
require_once("include.php");

if ( array_key_exists("logout", $_GET) )
{
	$session->logout();
	die("You are now logged out. <a href=\"index.php\">Login</a>");
}

$tickets = Ticket::getAllByProperty(PROPERTY_STUDENT, $session->getID());
?>
<!DOCTYPE html>
<html>
<head>
	<title>1:1</title>
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/manager.css" rel="stylesheet">
	
</head>
	<body>
		<div class="navbar navbar-static-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="./index.php">1:1</a>
					<ul class="nav">
						<li class="active"><a href="./index.php">Home</a></li>
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
		<br><br>
		<div class="container">
			<span class="sectionHeader">Tickets</span>
			<hr>
			<?php
			if ( count($tickets) == 0 )
			{
			?>
			<div class="alert">
				You do not have any tickets.
			</div>
			<?php
			}
			else
			{
			?>
			<div class="manager large" data-manager="tickets">
				<div class="navbar navbar-static-top">
					<div class="navbar-inner">
						<div class="pull-right">
							<button class="btn btn-inverse" data-manager-control="View">View</button>
						</div>
						
					</div>
				</div>
				<div class="manager-results">
					<table class="table">
						<thead>
							<tr>
								
								<th>Title</th>
								<th>Date</th>
								<th><input type="checkbox"></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($tickets as $ticket)
							{
								$properties = $ticket->getProperties();
							?>
								<tr>
									
									<td><?php echo $properties[PROPERTY_TITLE]."&nbsp;&nbsp;".$ticket->getStateLabel(); ?> </td>
									<td><?php echo date("M d, Y", $properties[PROPERTY_TIMESTAMP])." at ".date("g:i A", $properties[PROPERTY_TIMESTAMP]); ?></td>
									<td><input type="checkbox"></td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
			<?php
			}
			?>
		</div>
		<script src="js/jquery.js" type="text/javascript"></script>
		<script src="js/manager.js" type="text/javascript"></script>
		<script>
			var tickets = JSON.parse(<?php 
			$ticketList = Array();
			foreach($tickets as $ticket){
				$properties = $ticket->getProperties();
				array_push($ticketList, $properties[PROPERTY_ID]);
			}
			echo "\"".addslashes(json_encode($ticketList)). "\"";
			 ?>);
			$(document).ready(function(){
				$.fn.manager.defaults.selectedClass = "info";
				$("[data-manager-control=\"View\"]").hide()
				$("[data-manager=\"tickets\"]").manager({
					onSingleSelect : function(s){
						var row = s[0];
						$("[data-manager-control=\"View\"]").click(function(){
							window.location = "viewTicket.php?id=" + tickets[row];
						});
						$("[data-manager-control=\"View\"]").show()
					}, onNoneSelect : function(){
						$("[data-manager-control=\"View\"]").hide()
						$("[data-manager-control=\"View\"]").unbind("click");
					}, onMulitipleSelect : function(){
						$("[data-manager-control=\"View\"]").hide()
						$("[data-manager-control=\"View\"]").unbind("click");
					}
				
				});
			});
			
			
			
		</script>
	</body>

</html>