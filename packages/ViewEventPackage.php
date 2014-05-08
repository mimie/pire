<html>
<head>
 <title>
 </title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="../billingStyle.css">
<link rel="stylesheet" type="text/css" href="../menu.css">
 <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
 <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<style>
#package
{
  text-align:center;
  padding: 20px 20px 20px 20px;
}
</style>
</head>
<body>
<?php
   include '../login_functions.php';
   include '../pdo_conn.php';
   include 'package_functions.php';

   $dbh = civicrmConnect();
   $menu = logoutDiv($dbh);

   echo $menu;
   @$pid = $_GET["pid"];
?>
<form action="ViewEventPackage.php" method="POST">
<div id='package'>
  <select name="eventType">
<?php
   $eventTypes = getEventCategory();

   foreach($eventTypes as $field => $key){
      $type = $key["label"];
      $type_id = $key["value"];
      $selected = $type=='CIA' ? 'selected' : '';
      echo "<option value='$type_id' $selected>$type</option>";
    }
?>
  </select>
  <input type="text" name="event" placeholder="Type the search event.."/>
  <input type="submit" name="search" value="Search Event"/>
</div>
</form>
</body>
</html>
