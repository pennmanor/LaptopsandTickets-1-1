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
if ( array_key_exists("file", $_FILES) )
{
	$data = file_get_contents($_FILES['file']['tmp_name']);
	if ( $data )
	{
		$data = trim($data);
		$data = str_replace("\r", "\n", $data);
		$lines = explode("\n", $data);
		foreach ($lines as $line)
		{
			$line = explode(",", $line);
			if ( count($line) == 6 )
			{
				if ( !(Laptop::getByProperty(PROPERTY_HOSTNAME, $line[0]) || Laptop::getByProperty(PROPERTY_ASSETTAG, $line[2]) || Laptop::getByProperty(PROPERTY_SERIAL, $line[1]) || Laptop::getByProperty(PROPERTY_WMAC, $line[3]) || Laptop::getByProperty(PROPERTY_EMAC, $line[4])) )
					Laptop::create($line[0], $line[1], $line[2], $line[3], $line[4], $line[5]);
			}
		}
		$showBox = RESULT_SUCCESS;
	}
	else
	{
		$showBox = RESULT_FAIL;
	}
}

if ( array_key_exists("submit", $_POST) )
{
	htmlspecialcharsArray($_POST);
	if ( empty($_POST['hostname']) || empty($_POST['serial']) || empty($_POST['asset']) || empty($_POST['building']) )
		$showBox = RESULT_FAIL;
	else if ( Laptop::getByProperty(PROPERTY_HOSTNAME, $_POST['hostname']) || Laptop::getByProperty(PROPERTY_ASSETTAG, $_POST['asset']) || Laptop::getByProperty(PROPERTY_SERIAL, $_POST['serial']) || Laptop::getByProperty(PROPERTY_WMAC, $_POST['wirelessMAC']) || Laptop::getByProperty(PROPERTY_EMAC, $_POST['ethernetMAC']))
		$showBox = RESULT_DUP;
	else if ( Laptop::create($_POST['hostname'], $_POST['serial'], $_POST['asset'], $_POST['wirelessMAC'], $_POST['ethernetMAC'], $_POST['building']) )
		$showBox = RESULT_SUCCESS;
	else
		$showBox = RESULT_FAIL;
}

$pageNumber = intval($_GET['page']);
if ( $pageNumber < 1 )
{
	$pageNumber = 1;
}

$laptops = Laptop::getAll();
$nPages = ceil(count($laptops)/$itemsPerPage);

$itemStart = ($pageNumber-1)*$itemsPerPage;
$itemEnd = $itemStart+$itemsPerPage;
$laptops = array_subset($laptops, $itemStart, $itemEnd);
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
					<li class="active"><a href="../laptops">Laptops</a></li>
					<li><a href="../students">Students</a></li>
					<li><a href="../calendar">Calendar</a></li>
					<?php if ( $showFeedbackForm ) { ?><li><a href="../feedback">Feedback</a></li><?php } ?>
				</ul>
			
				<form class="navbar-search pull-right" action="./query.php">
				  <input type="text" class="search-query" name="query" placeholder="Search Laptops">
				</form>
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
			<h2>Laptops</h2>
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
		<button class="btn btn-info pull-right" onclick="csvDL()">Download as CSV</button>
		<button class="btn btn-info pull-right buttonSpacer" onclick="dhcpDL()">Download as DHCP config</button><br>
		<h2>Add</h2>
		<hr>
		<form action="" method="POST">
			<table class="table table-bordered">
				<tr>
					<td><strong>Hostname</strong></td>
					<td><input type="text" name="hostname"></td>
				</tr>
			
				<tr>
					<td><strong>Serial #</strong></td>
					<td><input type="text" name="serial"></td>
				</tr>
			
				<tr>
					<td><strong>Asset Tag</strong></td>
					<td><input type="text" name="asset"></td>
				</tr>
			
				<tr>
					<td><strong>Wireless MAC</strong></td>
					<td><input type="text" name="wirelessMAC"></td>
				</tr>
			
				<tr>
					<td><strong>Ethernet MAC</strong></td>
					<td><input type="text" name="ethernetMAC"></td>
				</tr>
				
				<tr>
					<td><strong>Building</strong></td>
					<td>
						<select name="building">
							<?php
							foreach ( $buildingList as $buildingKey => $building )
							{
								echo "<option value=\"".$buildingKey."\">".$building."</option>\n";
							}
							?>
						</select>
					</td>
				</tr>
				
				<tr>
					<td></td>
					<td><input type="submit" name="submit" class="btn btn-success" value="Create"></td>
				</tr>
			</table>
		</form>
		<br>
		<span class="sectionHeader">Mass Upload</span>
		<hr>
		Upload a CSV containing laptop information to add to the database.<br>
		<form action="" method="post" enctype="multipart/form-data">
			<input type="file" name="file" id="file">
			<input type="submit" name="submitfile" class="btn btn-success" value="Upload">
		</form>
		<br>
		Format:<br>
		host,serial,assetTag,wMAC,eMAC,building
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
							<input type="text" class="offset1 span10" id="search-all-for" placeholder="Search Laptops">
						</div>
					</div>
					<div class="tab-pane" id="search-advanced">
						<fieldset>
							<div class="form-item">
								<label>Search by:</label>
								<select id="search-field-by" name="by">
									<option value="hostname">Host Name</option>
									<option value="serial">Serial Number</option>
									<option value="assetTag">Asset Tag</option>
									<option value="wirelessMAC">Wireless Mac</option>
									<option value="ethernetMAC">Ethernet MAC</option>
								</select>
							</div>
							<div class="form-item">
								<label>Search for:</label>
								<input id="search-field-for" type="text" name="for">
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
	var laptopBar = new Progress("#laptopBar-inner", "#laptopBar", "#laptopBar-content", 2, function(){
		$("#laptop-refresh").button("reset");
	});
	var laptopTable = new Table(["hostname", "assetTag", "serial", "ethernetMAC", "wirelessMAC", "building", "id"], ["Hostname", "Asset Tag", "Serial Number", "Ethernet MAC", "Wireless MAC", "Building", ""]);
	laptopTable.setProperties("table", {"class" : "table"});
	laptopTable.setProperties("head-data", {"class" : "bold"});
	laptopTable.addColumnProcessor("id", function(data){
		return createElement("button", {"class":"btn btn-inverse pull-right", "onclick" : "window.location = \"laptop.php?id=" + data + "\""}, "View");
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
	}
	
	function search(){
		var activeTab  = -1;
		$("#search-tabs li").each(function(index) {
		   if($(this).hasClass("active")){
			   activeTab  = index;
		   }
		});
		switch(activeTab){
		case 0:
			var valid = true;
			$($("#search-form search-simple [required]").get().reverse()).each(function(key, ele){
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
			break;
		}
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
			url : "../../api/laptop.php",
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
