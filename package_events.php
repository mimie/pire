<html>
<head>
<title>Generate Package Events Bill</title>
<link rel="stylesheet" type="text/css" href="billingStyle.css">
<link rel="stylesheet" type="text/css" href="menu.css">
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script src="js/jquery-jPaginate.js"></script>
  <script src="js/jquery.tablesorter.js"></script>
<script type='text/javascript' language='javascript'>
function reloadPage()
  {
  location.reload();
  }
$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#packages').jPaginate({
                'max': 15,
                'page': 1,
                'links': 'buttons'
        });
//        $("table").tablesorter( {sortList: [[0,0], [1,0]]} ); 
});
$(function() {
    $( "#confirmation" ).dialog({
      resizable: false,
      width:500,
      modal: true,
      buttons: {
        "OK": function() {
          //$( this ).dialog( "close" );
          reloadPage();
        }
      }
    });
});
</script>
</head>
<body>
<?php

  include 'pdo_conn.php';
  include 'shared_functions.php';
  include 'login_functions.php';
  include 'bir_functions.php';
  include 'packages/packagebill_functions.php';
  include 'packages/package_functions.php';

  $dbh = civicrmConnect();
  $menu = logoutDiv($dbh);
  echo $menu;
  echo "<br>";
  $uid = $_GET['uid'];
  $pid = $_GET['pid'];

  $events = getEventsPerPackage($pid);
  $package_name = getPackageName($pid);
  $participants = getParticipantsPerPackage($pid);

  $display = "<table align='center'>"
           . "<tr><th colspan='4'>$package_name</th></tr>"
           . "<tr><th>Event Id</th><th>Event Name</th><th>Start Date</th><th>End Date</th></tr>";
  foreach($events as $key=>$field){
  	$display = $display."<tr>"
                 . "<td>".$field['event_id']."</td>"
                 . "<td>".$field['event_name']."</td>"
                 . "<td>".date_standard($field['start_date'])."</td>"
                 . "<td>".date_standard($field['end_date'])."</td>"
                 . "</tr>";
  }
  $display = $display."</table>";

  $display = $display."<table align='center'>"
           . "<thead><tr><td colspan='13'>LIST OF PARTICIPANTS</td></tr></thead><tbody>";
  
  //billing details for package events
  foreach($participants as $contact_id=>$details){
     $name = getContactName($contact_id);
     $display = $display."<tr><th colspan='13'>$name</th></tr>"
              . "<th>Event Name</th>"
              . "<th>Status</th>"
              . "<th>Organization</th>"
              . "<th>Fee</th>"
              . "<th>Total</th>"
              . "<th>Subtotal</th>"
              . "<th>VAT</th>"
              . "<th>Print Bill</th>"
              . "<th>Amount Paid</th>"
              . "<th>BS No.</th>"
              . "<th>Billing Date</th>"
              . "<th>Billing Address</th>"
              . "<th>Notes</th>";
     foreach($details as $key=>$field){
     	$display = $display."<tr>"
                 . "<td>".$field['event_name']."</td>"
                 . "<td></td>"
                 . "<td></td>"
                 . "<td></td>"
                 . "<td></td>"
                 . "<td></td>"
                 . "<td></td>"
                 . "<td></td>"
                 . "<td></td>"
                 . "<td></td>"
                 . "<td></td>"
                 . "<td></td>"
                 . "<td></td>";
     	
     }
  	
  }

  $display = $display."</tbody></table>";
  echo $display;


?>
</body>
</html>
