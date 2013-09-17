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
	<link href="css/order-form.css" rel="stylesheet">
	
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
						<div class="pull-left">
							<button class="btn btn-primary" id="ticket-search"><i class="icon-search icon-white"></i>	Search</button>
						</div>
						<div class="pull-right">
							<button class="btn" id="ticket-refresh" data-loading-text="Refreshing..."><i class="icon-refresh"></i>	Refresh Table</button>
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
			<div id="search-modal" class="modal hide fade">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3>Search</h3>
				</div>
				<form id="search-form">
					<div class="modal-body">
						<fieldset>
							<div class="form-item">
								<label>Search by:</label>
								<select id="search-field-by" name="by">
									<option value="title">Title</option>
								</select>
							</div>
							<div class="form-item">
								<label>Search for:</label>
								<input id="search-field-for" type="text" name="for" />
							</div>
						</fieldset>
					</div>
				</form>
				<div class="modal-footer">
					<button class="btn" id="search-cancel" data-dismiss="modal">Cancel</button>
					<button class="btn btn-primary" data-dismiss="modal" id="search-submit">Search</button>
				</div>
			</div>
		<script src="js/jquery.js" type="text/javascript"></script>
		<script src="js/bootstrap.min.js" type="text/javascript"></script>
		<script src="js/object.js" type="text/javascript"></script>
		<script src="js/Progress.js" type="text/javascript"></script>
		<script src="js/Table.js" type="text/javascript"></script>
		<script>
		var searching = false;
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
		function init(){
			var data = {"action":"all"};
			getTickets(JSON.stringify(data));
			ticketBar.init();
			ticketBar.step(1);
			$("#ticket-refresh").button("reset");
			$("#search-submit").click(function(){
				search();
			});
			$("#search-form").submit(function(){
				$("#search-modal").modal("hide");
				search();
				return false;
			})
			$("#ticket-search").click(function(){
				$("#search-modal").modal("show");
			});
			$("#ticket-refresh").click(function(){
				refresh();
			});
		}
		
		function search(){
			searching = true;
			var byData = $("#search-field-by").val();
			var forData = $("#search-field-for").val();
			var data = {"action":"search", "by":byData, "for":forData};
			ticketBar.reset();
			getTickets(JSON.stringify(data));
		}
		
		function refresh(){
			if(searching){
				var byData = $("#search-field-by").val();
				var forData = $("#search-field-for").val();
				var data = {"action":"search", "by":byData, "for":forData};
			}
			else
				var data = {"action":"all"};
			$("#ticket-refresh").button("loading");
			ticketBar.reset();
			getTickets(JSON.stringify(data));
		}
		
		function getTickets(d){
			$.ajax({
				url : "./api/ticket.php",
				type : "POST",
				data : "data=" + d,
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