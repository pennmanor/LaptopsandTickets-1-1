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
					<a class="brandimg" href="./index.php"><img src="./img/pmsd.png"></a>
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
							<button class="btn btn-primary" id="ticket-search"><i class="icon-search icon-white"></i> Search</button>
							<span id="searchQuery"></span>
						</div>
						<div class="pull-right">
							<button class="btn" id="ticket-refresh" data-loading-text="Refreshing..."><i class="icon-refresh"></i> Refresh Table</button>
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
						<p class="text-error hide">Please fill out all of the required forms</p>
						<fieldset>
							<div class="form-item">
								<label>Search by:</label>
								<select id="search-field-by" name="by">
									<option value="title">Title</option>
								</select>
							</div>
							<div class="form-item">
								<label>Search for:</label>
								<input id="search-field-for" type="text" name="for" required>
							</div>
						</fieldset>
					</div>
				</form>
				<div class="modal-footer">
					<button class="btn" id="search-cancel" data-dismiss="modal">Cancel</button>
					<button class="btn btn-primary" id="search-submit">Search</button>
				</div>
			</div>
		<script src="js/jquery-1.9.1.js" type="text/javascript"></script>
		<script src="js/bootstrap.min.js" type="text/javascript"></script>
		<script src="js/object.js" type="text/javascript"></script>
		<script src="js/Progress.js" type="text/javascript"></script>
		<script src="js/Table.js" type="text/javascript"></script>
		<script>
		var searching = false;
		var ticketBar = new Progress("#ticketBar-inner", "#ticketBar", "#ticketBar-content", 2, function(){
			$("#ticket-refresh").button("reset");
		});
		var ticketTable = new Table(["title", "timestamp", "helper", "id"], ["Title", "Date", "Helper", ""]);
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
		ticketTable.addColumnProcessor("helper", function(data){
			return $.trim(data).length != 0 ? data:"Unassigned";
		});
		ticketTable.addColumnProcessor("id", function(data){
			return createElement("button", {"class":"btn btn-inverse pull-right", "onClick" : "window.location = \"viewTicket.php?id=" + data + "\""}, "View");
		});
		function init(){
			var data = {"action":"all", "limit":["my"]};
			getTickets(JSON.stringify(data));
			ticketBar.init();
			ticketBar.step(1);
			$("#search-submit").click(function(){
				search();
			});
			$("#search-form").submit(function(){
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
			var valid = true;
			$($("#search-form [required]").get().reverse()).each(function(key, ele){
				if($.trim($(this).val()).length == 0){
					$("#search-form .text-error").removeClass("hide");
					valid = false;
					$(this).focus();
				}
				else{
					$("#search-form .text-error").addClass("hide");
				}
			});
			if(!valid)
				return false;
			$("#search-modal").modal("hide");
			searching = true;
			var byData = $("#search-field-by").val();
			var forData = $("#search-field-for").val() != "" ? $("#search-field-for").val() : " ";
			var data = {"action":"search", "by":byData, "for":forData, "limit":["my"]};
			var group = createElement("div", {"class":"btn-group", "id":"searchItem"});
			var text = createElement("button", {"class":"btn"}, byData + ": " + forData);
			var close = createElement("button", {"class":"btn", "onclick":"removeSearch()"});
			close.innerHTML = "&times;";
			insertElementAt(text, group);
			insertElementAt(close, group);
			$("#searchQuery").html(group);
			ticketBar.reset();
			getTickets(JSON.stringify(data));
		}
		function refresh(){
			if(searching){
				var byData = $("#search-field-by").val();
				var forData = $("#search-field-for").val();
				var data = {"action":"search", "by":byData, "for":forData, "limit":["my"]};
			}
			else
				var data = {"action":"all", "limit":["my"]};
			
			ticketBar.reset();
			getTickets(JSON.stringify(data));
		}
		function getTickets(d){
			$("#ticket-refresh").button("loading");
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
			if(data.success == 1){
				$("#ticketBar-content").html(ticketTable.buildTable(data.result));
			}
			else{
				$("#ticketBar-content").html(createElement("p", {"class":"text-center lead"},"Error. There was a problem with the request"));
			}
			ticketBar.step(2);
		}
		function removeSearch(){
			$("#search-form")[0].reset();
			searching = false;
			removeElement($("#searchItem")[0]);
			refresh();
		}
		window.onload = init;
		</script>
	</body>

</html>