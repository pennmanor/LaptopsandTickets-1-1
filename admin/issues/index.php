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
?>
<!DOCTYPE html>
<html>
<head>
	<title>1:1 Inventory</title>
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
					<li><a href="../tickets">Tickets</a></li>
					<li><a href="../laptops">Laptops</a></li>
					<li class="active"><a href="../issues">Issues</a></li>
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
		if ( $showBox == RESULT_SUCCESS )
		{
		?>
		<div class="alert alert-success">
			<strong>Success!</strong> The computer has been added to the inventory.
		</div>
		<?php
		}
		else if ( $showBox == RESULT_FAIL)
		{
		?>
		<div class="alert alert-error">
			<strong>Error</strong> Check that you have filled out all fields correctly.
		</div>
		<?php	
		}
		else if ( $showBox == RESULT_DUP)
		{
		?>
		<div class="alert alert-error">
			<strong>Error</strong> A computer with this information already exists.
		</div>
		<?php	
		}
		?>
		<h2>Issues</h2>
		<div class="manager large">
			<div class="navbar navbar-static-top">
				<div class="navbar-inner">
					<div class="pull-left">
						<button class="btn btn-primary" id="laptop-search"><i class="icon-search icon-white"></i> Search</button>
						<span id="searchQuery"></span>
					</div>
					<div class="pull-right">
						<button class="btn" id="laptop-refresh" data-loading-text="Refreshing..."><i class="icon-refresh"></i> Refresh Table</button>
					</div>
				</div>
			</div>
			<section class="manager-results">
				<div class="progressBar">
					<div id="laptopBar" class="progress">
						<div id="laptopBar-inner" class="bar" data-percentage="0"></div>
					</div>
				</div>
				<div id="laptopBar-content">
				</div>
			</section>
		</div>
	</div>
	<div id="search-modal" class="modal hide fade">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times;</button>
			<h3>Search</h3>
		</div>
		<form id="search-form">
			<div class="modal-body">
				<p class="text-error hide">Please fill out all of the required forms</p>
				<fieldset>
					<div class="form-item">
						<label>Search by:</label>
						<select id="search-field-by" name="by">
							<option value="body">Description</option>
						</select>
					</div>
					<div class="form-item">
						<label>Search for:</label>
						<input id="search-field-for" type="text" name="for">
					</div>
				</fieldset>
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
	var searching = false
	var issueList = <?php echo json_encode($issueTypes); ?>;
	var limits = [];
	var laptopBar = new Progress("#laptopBar-inner", "#laptopBar", "#laptopBar-content", 2, function(){
		$("#laptop-refresh").button("reset");
	});
	var laptopTable = new Table(["type", "assetTag", "body", "laptop"], ["Issue Type", "Asset Tag", "Description", ""]);
	laptopTable.setProperties("table", {"class" : "table"});
	laptopTable.setProperties("head-data", {"class" : "bold"});
	laptopTable.addAdvancedColumnProcessor("type", function(data){
		return issueList[data["issue"].subtype];
	});
	laptopTable.addAdvancedColumnProcessor("assetTag", function(data){
		return data["laptop"].assetTag;
	});
	laptopTable.addAdvancedColumnProcessor("body", function(data){
		return data["issue"].body;
	});
	laptopTable.addAdvancedColumnProcessor("laptop", function(data){
		return createElement("button", {"class":"btn btn-inverse pull-right", "onclick" : "window.location = \"../laptops/laptop.php?id=" + data["laptop"].id + "\""}, "View Laptop");
	});
	function init(){
		var data = {"action":"all"};
		getLaptops(JSON.stringify(data));
		laptopBar.init();
		laptopBar.step(1);
		$("#search-submit").click(function(){
			search();
		});
		$("#search-form").submit(function(e){
			e.preventDefault();
			search();
		})
		$("#laptop-search").click(function(){
			$("#search-modal").modal("show");
		});
		$("#laptop-refresh").click(function(){
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
		var byData = $("#search-field-by").val();
		var forData = $("#search-field-for").val() != "" ? $("#search-field-for").val() : " ";
		if($.trim(forData).length != 0 ){
			$("#searchItem").remove();
			var data = {"action":"search", "by":byData, "for":forData};
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
		laptopBar.reset();
		window.console&&console.log(JSON.stringify(data));
		getLaptops(JSON.stringify(data));
		$("#search-modal").modal("hide");
	}
	function refresh(){
		if(searching){
			var byData = $("#search-field-by").val();
			var forData = $("#search-field-for").val();
			var data = {"action":"search", "by":byData, "for":forData, "limit":limits};
		}
		else
			var data = {"action":"all", "limit":limits};
		
		laptopBar.reset();
		getLaptops(JSON.stringify(data));
	}
	function getLaptops(d){
		$("#laptop-refresh").button("loading");
		$.ajax({
			url : "../../api/issue.php",
			type : "POST",
			data : "data=" + d,
			success : proccessLaptops
		});
	}
	function proccessLaptops(d){
		window.console&&console.log(d);
		var data = JSON.parse(d);
		if(data.success == 1){
			$("#laptopBar-content").html(laptopTable.buildTable(data.result));
		}
		else{
			$("#laptopBar-content").html(createElement("p", {"class":"text-center lead"},"Error. There was a problem with the request"));
		}
		laptopBar.step(2);
	}
	function removeSearchQuery(){
		$("#search-field-by").val("");
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
	<script type="text/javascript">
	
		function csvDL()
		{
			window.location = "reportGen.php?fullListCSV=true";
		}
	
		function dhcpDL()
		{
			window.location = "reportGen.php?fullListDHCP=true";
		}
	
		function handleDetailsClick(id)
		{
			window.location ="laptop.php?id="+id;
		}
	</script>
</body>
</html>
