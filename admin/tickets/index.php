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
$requiresAdmin = true;
require_once("../../include.php");
$showBox = RESULT_NONE;
if ( array_key_exists("create", $_POST) )
{
	htmlspecialcharsArray($_POST);
	$student = Student::getByProperty(PROPERTY_SID, $_POST['student']);
	if ( !$student )
		$showBox = RESULT_FAIL;
	else
	{
		$ticket = Ticket::create($_POST['student'], $_POST['title'], "Ticket automatically created");
		if ( $ticket )
			$ticket->assignHelper($session->getID());
		else
			$showBox = RESULT_FAIL;
	}
}

$tickets = Ticket::getAllByProperty("state", TICKETSTATE_OPEN);

?>
<!DOCTYPE html>
<html>
<head>
	<title>1:1</title>
	<meta charset="UTF-8">
	<link href="../../css/bootstrap.css" rel="stylesheet">
	<link href="../../css/style.css" rel="stylesheet">
	<link href="../../css/manager.css" rel="stylesheet">
	<link href="../../css/order-form.css" rel="stylesheet">
</head>
	<body>
		<div class="navbar navbar-static-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brandimg" href="../index.php"><img src="../../img/pmsd.png"></a>
					<ul class="nav">
						<li><a href="../index.php">Overview</a></li>
						<li class="active"><a href="../tickets">Tickets</a></li>
						<li><a href="../laptops">Laptops</a></li>
						<li><a href="../students">Students</a></li>
						<li><a href="../calendar">Calendar</a></li>
						<?php if ( $showFeedbackForm ) { ?><li><a href="../feedback">Feedback</a></li><?php } ?>
					</ul>
				</div>
			</div>
		</div>
		<br>
		<div class="container">
			<?php
			if ( $showBox == RESULT_FAIL )
			{
			?>
			<div class="alert alert-error">
			  <strong>Error</strong>&nbsp;&nbsp;Invalid information was submitted
			</div>
			<br>
			<?php
			}
			?>
			<h2>Tickets</h2>
			<div class="manager large">
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
			<h2>Create Ticket</h2>
			<hr>
			<form action="" method="post">
				<input type="text" name="title" placeholder="Title"><br>
				<input type="text" name="student" placeholder="Student"><br>
				<input type="submit" name="create" value="Create" class="btn btn-success">
			</form>
			<br>
		</div>
		<div id="search-modal" class="modal hide fade">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times;</button>
				<h3>Search</h3>
			</div>
			<form id="search-form">
				<div class="modal-body">
					<ul class="nav nav-tabs" id="search-tabs">
						<li class="active"><a href="#search-simple" data-toggle="tab">Simple</a></li>
						<li><a href="#search-advanced" data-toggle="tab">Advanced</a></li>
					</ul>
					<p class="text-error hide">Please fill out all of the required forms</p>
					<div class="tab-content">
						<div class="tab-pane active" id="search-simple">
							<div class="row-fluid">
								<input type="text" class="offset1 span10" id="search-all-for" placeholder="Search Tickets">
							</div>
						</div>
						<div class="tab-pane" id="search-advanced">
							<fieldset>
								<div class="form-item">
									<label>Search by:</label>
									<select id="search-field-by" name="by">
										<option value="title">Title</option>
										<option value="helper">Helper</option>
										<option value="state">State</option>
									</select>
								</div>
								<div class="form-item">
									<label>Search for:</label>
									<input id="search-field-for" type="text" name="for">
								</div>
								<div class="form-item">
									<label class="checkbox">
										<input name="limit-open" type="checkbox">Open Tickets
									</label>
								</div>
								<div class="form-item">
									<label class="checkbox">
										<input name="limit-unassigned" type="checkbox">Unassigned
									</label>
								</div>
								<div class="form-item">
									<label class="checkbox">
										<input name="limit-helper" type="checkbox">Assigned to Me
									</label>
								</div>
							</fieldset>
						</div>
					</div>
				</div>
			</form>
			<div class="modal-footer">
				<button class="btn" id="search-cancel" data-dismiss="modal">Cancel</button>
				<button class="btn btn-primary" id="search-submit">Search</button>
			</div>
		</div>
		<script src="../../js/jquery-1.9.1.js" type="text/javascript"></script>
		<script src="../../js/bootstrap.min.js" type="text/javascript"></script>
		<script src="../../js/object.js" type="text/javascript"></script>
		<script src="../../js/Progress.js" type="text/javascript"></script>
		<script src="../../js/Table.js" type="text/javascript"></script>
		<script>
		var searching = false;
		var limits = [];
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
			return createElement("button", {"class":"btn btn-inverse pull-right", "onclick" : "window.location = \"ticket.php?id=" + data + "\""}, "View");
		});
		function init(){
			var data = {"action":"all"};
			getTickets(JSON.stringify(data));
			ticketBar.init();
			ticketBar.step(1);
			$("#search-submit").click(function(){
				search();
			});
			$("#search-form").submit(function(e){
				e.preventDefault();
				search();
			})
			$("#ticket-search").click(function(){
				$("#search-modal").modal("show");
			});
			$("#ticket-refresh").click(function(){
				refresh();
			});
			$("#search-form input").keypress(function(e) {
			    if(e.which == 13) {
			        search();
			    }
			});
		}
		
		function search(){
			var valid = true;
			var activeTab  = -1;
			$("#search-tabs li").each(function(index) {
			   if($(this).hasClass("active")){
				   activeTab = index;
			   }
			});
			switch(activeTab){
			case 0:
				$($("#search-form search-advanced [required]").get().reverse()).each(function(key, ele){
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
				searching = true;
				var byData = "all";
				var forData = $("#search-all-for").val();
				break;	
			case 1:
				$($("#search-form search-advanced [required]").get().reverse()).each(function(key, ele){
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
				searching = true;
				limits = [];
				$("#searchItem").remove();
				$(".limit").remove();
				if($("#search-form [name=limit-open]").is(":checked"))
					addSearchLimit("open", "Open Tickets");
				else
					removeSearchLimit("open");
				if($("#search-form [name=limit-unassigned]").is(":checked"))
					addSearchLimit("unassigned", "Unassigned Tickets");
				else
					removeSearchLimit("unassigned");
				if($("#search-form [name=limit-helper]").is(":checked"))
					addSearchLimit("helper", "Assigned to me");
				else
					removeSearchLimit("helper");
				var byData = $("#search-field-by").val();
				var forData = $("#search-field-for").val() != "" ? $("#search-field-for").val() : " ";
			}
			$("#search-modal").modal("hide");
			if($.trim(forData).length != 0 ){
				var data = {"action":"search", "by":byData, "for":forData, "limit":limits};
				var group = createElement("div", {"class":"btn-group", "id":"searchItem"});
				var text = createElement("button", {"class":"btn"}, byData + ": " + forData);
				var close = createElement("button", {"class":"btn", "onclick":"removeSearchQuery()"});
				close.innerHTML = "&times;";
				insertElementAt(text, group);
				insertElementAt(close, group);
				$("#searchQuery").append(group);
			}
			else
				var data = {"action":"all", "by":byData, "for":forData, "limit":limits};
			ticketBar.reset();
			getTickets(JSON.stringify(data));
		}
		function refresh(){
			if(searching){
				var byData = $("#search-field-by").val();
				var forData = $("#search-field-for").val();
				var data = {"action":"search", "by":byData, "for":forData, "limit":limits};
			}
			else
				var data = {"action":"all", "limit":limits};
			
			ticketBar.reset();
			getTickets(JSON.stringify(data));
		}
		function getTickets(d){
			$("#ticket-refresh").button("loading");
			$.ajax({
				url : "../../api/ticket.php",
				type : "POST",
				data : "data=" + d,
				success : proccessTickets
			});
		}
		function proccessTickets(d){
			var data = JSON.parse(d);
			
			window.console&&console.log(data.result);
			if(data.success == 1){
				$("#ticketBar-content").html(ticketTable.buildTable(data.result));
			}
			else{
				$("#ticketBar-content").html(createElement("p", {"class":"text-center lead"},"Error. There was a problem with the request"));
			}
			ticketBar.step(2);
		}
		function removeSearchQuery(){
			$("#search-field-by").val("");
			$("#search-all-for").val("");
			$("#search-field-for").val("");
			searching = false;
			$("#searchItem").remove();
			refresh();
		}
		function addSearchLimit(limit, name){
			$("#search-form #check-" + limit).prop("checked", true);
			var group = createElement("div", {"class":"btn-group limit", "id":"limit-" + limit});
			var text = createElement("button", {"class":"btn"}, name);
			var close = createElement("button", {"class":"btn", "onclick":"removeSearchLimit(\"" + limit + "\")"});
			close.innerHTML = "&times;";
			insertElementAt(text, group);
			insertElementAt(close, group);
			$("#searchQuery").append(group);
			limits.push(limit);
		}
		function removeSearchLimit(limit){
			$("#search-form [name=limit-" + limit + "]").prop("checked", false);
			$("#limit-" + limit).remove();
			var index = limits.indexOf(limit);
			if(index > -1)
				limits.splice(index, 1);
			refresh();
		}
		window.onload = init;
		</script>
	</body>
</html>