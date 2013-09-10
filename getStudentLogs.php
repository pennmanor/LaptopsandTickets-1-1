<?php
include_once("include.php");
$from = $_GET["from"];
$to = $_GET["to"];
$eventClasses = Array(0 => "event-success", 1 => "event-warning", 2 => "event-info", 3 => "event-inverse", 4 => "event-special");
$students = Array();
$i = 0;
foreach($helpers as $helper){
    if($i >= 5)
      $i = 0;
    $students[$helper] = $i;
    $i++;
}
$query = "SELECT `student`, `timestamp` FROM `history` WHERE `action` = \"".HISTORYEVENT_SIGNIN."\" AND `timestamp` BETWEEN ".($from/1000)." AND ".($to/1000);
$result = $mysql->query($query);
$first = true;
?>
{
  "success": 1,
	"result": [
  <?php while($row = mysqli_fetch_array($result)) { 
    if($first){?>
  		{
  			"id": "<?php echo $row["student"] ?>",
  			"title": "<?php echo $row["student"] ?>",
  			"url": "#",
  			"class": "<?php echo $eventClasses[$students[$row["student"]]] ?>",
  			"start": "<?php echo intval($row["timestamp"])."000" ?>",
  			"end":   "<?php echo intval($row["timestamp"])."000" ?>"
  		}
    <?php } else{?>
  		,{
  			"id": "<?php echo $row["student"] ?>",
  			"title": "<?php echo $row["student"] ?>",
  			"url": "#",
  			"class": "<?php echo $eventClasses[$students[$row["student"]]] ?>",
  			"start": "<?php echo intval($row["timestamp"])."000" ?>",
  			"end":   "<?php echo intval($row["timestamp"])."000" ?>"
  		}<?php
      } $first = false; } ?>
		
	]
}


