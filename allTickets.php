<?php
require_once("include.php");
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
						<li><a href="./index.php">Home</a></li>
						<li class="active"><a href="./allTickets.php">My Tickets</a></li>
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
			<div class="manager large" data-manager="orders">
				<div class="navbar navbar-static-top">
					<div class="navbar-inner">
						<div class="pull-right">
							<a id="ticket-refresh" class="btn" href="#"><i class="icon-refresh"></i>  Refresh Table</a>
						</div>
					</div>
				</div>
				<section class="manager-results">
					<div class="progressBar">
						<div id="ticketBar" class="progress">
							<div id="ticketBar-inner" class="bar" data-percentage="0"></div>
						</div>
					</div>
					<div id="ticketBar-content">
					</div>
				</section>
			</div>
				
			<?php
			}
			?>
		</div>
		<script src="js/jquery.js" type="text/javascript"></script>
		<script src="js/bootstrap.min.js" type="text/javascript"></script>
		<script src="js/object.js" type="text/javascript"></script>
		<script src="js/Progress.js" type="text/javascript"></script>
		<script src="js/Table.js" type="text/javascript"></script>
		<script>
		var ticketBar = new Progress("#ticketBar-inner", "#ticketBar", "#ticketBar-content", 2);
		var ticketTable = new Table(["title", "timestamp"], ["Title", "Date"]);
		ticketTable.setProperties("table", {"class" : "table"})
		ticketTable.addColumnProcessor("timestamp", function(data){
			var date = new Date(parseInt(data)*1000);
			return date.toDateString();
		});
		var data = "{\"action\": \"get\",\"by\" : \"student\",\"for\" : \"201864\"}";
		function init(){
			ticketBar.init();
			ticketBar.step(2);
			$("#ticket-refresh").click(function(){
				ticketBar.reset();
				setTimeout(function(){
					ticketBar.step(2);
				} , 1000);
			});
			getTickets();
		}
		function getTickets(){
			$.ajax({
				url : "./api/ticket.php",
				type : "POST",
				data : "key=1B7D5575BAD62F6BA3C6D1163A786&data=" + data,
				success : proccessTickets
			});
		}
		function proccessTickets(d){
			var data = JSON.parse(d);
			if(data.success = 1){
				window.console&&console.log(ticketTable.buildTable(data.result));
				$("#ticketBar-content").html(ticketTable.buildTable(data.result));
			}
			window.console&&console.log(data);
		}
		window.onload = init;
		</script>
	</body>

</html>