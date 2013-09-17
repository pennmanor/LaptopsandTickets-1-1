<?php
require_once("include.php");
$tickets = Ticket::getAllByProperty(PROPERTY_STUDENT, $session->getID());
?>
<!DOCTYPE html>
<html>
<head>
	<title>1:1</title>
	<meta charset="UTF-8">
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
						<?php if ( $showFeedbackForm ) { ?><li><a href="./feedbackForm.php">Feedback</a></li><?php } ?>
						
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
							<button class="btn" id="ticket-refresh" data-loading-text="Refreshing..."><i class="icon-refresh"></i>  Refresh Table</button>
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
		var ticketBar = new Progress("#ticketBar-inner", "#ticketBar", "#ticketBar-content", 2, function(){
			$("#ticket-refresh").button("reset");
		});
		var ticketTable = new Table(["title", "timestamp", "id"], ["Title", "Date", ""]);
		ticketTable.setProperties("table", {"class" : "table"});
		ticketTable.setProperties("head-data", {"class" : "bold"});
		ticketTable.addAdvancedColumnProcessor("title", function(data){
			var label = createElement("span", {"class" : "label " + (data["state"] == 1 ? "label-success" : "label-inverse")}, (data["state"] == 1 ? "Open" : "Closed"));
			return createElement("span", null, data["title"] + " ", label);
		});
		ticketTable.addColumnProcessor("timestamp", function(data){
			var date = new Date(parseInt(data)*1000);
			return date.toDateString();
		});
		ticketTable.addColumnProcessor("id", function(data){
			return createElement("button", {"class":"btn btn-inverse pull-right", "onClick" : "window.location = \"viewTicket.php?id=" + data + "\""}, "View");
		});
		var data = "{\"action\": \"get\",\"by\" : \"student\",\"for\" : \"201864\"}";
		function init(){
			ticketBar.init();
			ticketBar.step(1);
			$("#ticket-refresh").button("reset");
			$("#ticket-refresh").click(function(){
				$("#ticket-refresh").button("loading");
				ticketBar.reset();
				getTickets();
			});
			getTickets();
			
		}
		function getTickets(){
			$.ajax({
				url : "./api/ticket.php",
				type : "POST",
				data : "data=" + data,
				success : proccessTickets
			});
		}
		function proccessTickets(d){
			window.console&&console.log(d);
			var data = JSON.parse(d);
			window.console&&console.log(data);
			if(data.success == 1){
				$("#ticketBar-content").html(ticketTable.buildTable(data.result));
			}
			else{
				$("#ticketBar-content").html(createElement("p", {"class":"text-center lead"},"Error. There was a problem with the request"));
			}
			ticketBar.step(2);
		}
		window.onload = init;
		</script>
	</body>

</html>