<?php
require_once("config.php");
require_once("db/include.php");
$laptops = Laptop::getAll();
$nAssigned = 0;

foreach ($laptops as $laptop)
{
	if ( $laptop->getOwner() )
		$nAssigned++;
}


$nLaptops = count($laptops);
$nUnassigned = $nLaptops-$nAssigned;

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
						<li class="active"><a href="./index.php">Overview</a></li>
						<li><a href="./tickets">Tickets</a></li>
						<li><a href="./laptops">Laptops</a></li>
					</ul>
				</div>
			</div>
		</div>
		<br><br>
		<div class="container">
			<span class="sectionHeader">Overview</span>
			<hr>
			<div class="row">
				<div class="span5">
				</div>
				
				<div class="span2">
					<table class="table table-bordered">
						<tr>
							<td><strong>Laptops</strong> <span class="badge badge-info pull-right"><?php echo $nLaptops; ?></span></td>
						</tr>
						
						<tr>
							<td><strong>Assigned</strong> <span class="badge badge-success pull-right"><?php echo $nAssigned; ?></span></td>
						</tr>
						
						<tr>
							<td><strong>Unassigned</strong> <span class="badge pull-right"><?php echo $nUnassigned; ?></span></td>
						</tr>
							
					</table>
				</div>
				
				<div class="span5">
					
				</div>
			</div>
		</div>
	</body>

</html>