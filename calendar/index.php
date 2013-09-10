<?php
include_once("../include.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign log</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/calendar.min.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
</head>
<body>
  <div class="container">
    <div class="page-header">
      <div class="pull-right form-inline">
        <div class="btn-group">
          <button class="btn btn-primary" data-calendar-nav="prev">Prev</button>
          <button class="btn" data-calendar-nav="today">Today</button>
          <button class="btn btn-primary" data-calendar-nav="next">Next</button>
        </div>
        <div class="btn-group">
          <button class="btn btn-warning" data-calendar-view="year">Year</button>
          <button class="btn btn-warning active" data-calendar-view="month">Month</button>
          <button class="btn btn-warning" data-calendar-view="week">Week</button>
          <button class="btn btn-warning" data-calendar-view="day">Day</button>
        </div>
      </div>
      <h3><span class="date"></span></h3>
    </div>
    <div class="row">
      <div class="span10">
        <div id="calendar"></div>
      </div>
      <div class="span2">
        <ul id="eventlist" class="cal-event-list unstyled"></ul>
      </div>
    </div>
    <br><br>
  </div>
    <script type="text/javascript" src="../js/jquery-1.9.1.js"></script>
    <script type="text/javascript" src="../js/underscore-min.js"></script>
    <script src="../js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" src="../js/calendar.js"></script>
    <script src="../js/app.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript">
    </script>
</body>
</html>