<?php
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
</head>

	<body>
		<div class="navbar navbar-static-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="../index.php">1:1</a>
					<ul class="nav">
						<li><a href="../index.php">Overview</a></li>
						<li><a href="../tickets">Tickets</a></li>
						<li class="active"><a href="../laptops">Laptops</a></li>
						<li><a href="../students">Students</a></li>
					</ul>
				
					<form class="navbar-search pull-right" action="./query.php">
					  <input type="text" class="search-query" name="query" placeholder="Search Laptops">
					</form>
				</div>
			</div>
		</div>
		<br><br>
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
			
			<span class="sectionHeader">All Laptops</span>
			<button class="btn btn-info pull-right" onclick="csvDL()">Download as CSV</button><button class="btn btn-info pull-right buttonSpacer" onclick="dhcpDL()">Download as DHCP config</button>
			<hr>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Hostname</th>
						<th>Asset Tag</th>
						<th>Serial #</th>
						<th>Ethernet MAC</th>
						<th>Wireless MAC</th>
						<th>Building</th>
						<th></th>
					</tr>
				</thead>
				
				<tbody>
					<?php
					foreach ( $laptops as $laptop )
					{
						$properties = $laptop->getProperties();
					?>
					<tr>
						<td><?php echo $properties[PROPERTY_HOSTNAME]; ?></td>
						<td><?php echo $properties[PROPERTY_ASSETTAG]; ?></td>
						<td><?php echo $properties[PROPERTY_SERIAL]; ?></td>
						<td><?php echo $properties[PROPERTY_EMAC]; ?></td>
						<td><?php echo $properties[PROPERTY_WMAC]; ?></td>
						<td><?php echo $buildingList[$properties[PROPERTY_BUILDING]]; ?></td>
						<td><button class="btn btn-inverse" onClick="handleDetailsClick(<?php echo $properties[PROPERTY_ID]; ?>)">Details</button></td>
					</tr>
					<?php
					}
					?>
				</tbody>
			</table>
			<div class="pagination pagination-centered">
			  <ul>
			    <li class="<?php if ( $pageNumber == 1 ) echo "disabled"; ?>"><a href="<?php if ( $pageNumber == 1 ) echo "#"; else echo "index.php?page=".($pageNumber-1); ?>">Prev</a></li>
				<?php 
				for ( $i = 0; $i < $nPages; $i++ )
				{
					$p = $i+1;
				?>
			    <li class="<?php if ( $pageNumber == $p ) echo "active"; ?>"><a href="index.php?page=<?php echo $p; ?>"><?php echo $p; ?></a></li>
				<?php
				}
				?>
			    <li class="<?php if ( $pageNumber == $nPages ) echo "disabled"; ?>"><a href="<?php if ( $pageNumber == $nPages ) echo "#"; else echo "index.php?page=".($pageNumber+1); ?>">Next</a></li>
			  </ul>
			</div>
			<br>
			
			<span class="sectionHeader">Add</span>
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
	</body>

</html>