<html>
<head>
 <title>
 </title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="../billingStyle.css">
<link rel="stylesheet" type="text/css" href="../menu.css">
 <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
 <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
 <script src="../js/jquery-jPaginate.js"></script>
 <script src="../js/jquery-ui.js"></script>
 <script src="../js/jquery.tablesorter.js"></script>
<style>
#package
{
  text-align:center;
  padding: 20px 20px 20px 20px;
}
</style>
<script>
$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#events').jPaginate({
                'max': 20,
                'page': 1,
                'links': 'buttons'
        });
//        $("table").tablesorter( {sortList: [[0,0], [1,0]]} ); 
});

$(function() {
  $( "#confirmation" ).dialog({
    resizable: false,
    width: 500,
    modal: true,
    buttons: {
       "OK": function(){
           //$( this ).dialog("close");
           location.reload();
       }
    }
    
  });
});
</script>
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
<form action="ViewEventPackage.php?pid=<?=$pid?>" method="POST">
<div id='package'>
  <select name="eventTypeId">
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
<?php

  echo "<div align='center' style='padding: 8px 8px 8px 8px'>";
  $eventsPerPackage = displayEventsPerPackage($pid);
  echo $eventsPerPackage;
  echo "</div>";

  echo "<div align='center'>";
  if($_POST["search"]){
     $eventTypeId = $_POST["eventTypeId"];
     $eventName = $_POST["event"];
     $eventPackages = getEventsForPackages($eventTypeId,$eventName);
     $eventType = getEventTypeName($eventTypeId);
     echo "<div align='center'>$eventType</div>";
     $display = displayEventPackages($eventPackages);
     echo $display;

  }

  elseif($_POST["add"]){

    $selectedIds = $_POST["eventIds"];
    var_dump($selectedIds);
    insertPackageEvents($selectedIds,$pid);    
  }

  else{
    $eventPackages = getEventsForPackages(2,"");
    $eventType = getEventTypeName(2);
    echo "<div align='center' style='padding: 8px 8px 8px 8px;'>".$eventType." EVENT TYPE</div>";
    $display = displayEventPackages($eventPackages);
    echo $display;
  }

  echo "</div>";

?>
</form>
</body>
</html>
