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
$query = "SELECT `student`, `timestamp` FROM `history` WHERE `type` = \"".HISTORYEVENT_SIGNIN."\" AND `timestamp` BETWEEN ".($from/1000)." AND ".($to/1000);
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


