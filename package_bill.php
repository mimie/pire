<html>
<head>
<title>Package Events</title>
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
  include 'packages/packagebill_functions.php';

  $dbh = civicrmConnect();
  $menu = logoutDiv($dbh);
  echo $menu;
  echo "<br>";
  $uid = $_GET['uid'];
?>
<div align='center'>
	<form action='' method='POST'>
          <input type='text' name='package' placeholder='Search package name...'>
 	  <input type='submit' name='search' value='SEARCH PACKAGE'>
	</form>
</div>

<?php
  $events = getAllPackageDetails();
  $packages = $_POST['search'] ? searchPackageName($_POST['package']) : getAllPackagesPerPackageId();

  $display = "<table id='packages' style='width:50%;' align='center'>"
           . "<thead>"
           . "<tr><th colspan='3'>PACKAGES</th></tr>"
           . "</thead><tbody>";

  foreach($packages as $pid=>$package_name){
  	$display = $display."<tr><td  bgcolor='#084B8A' colspan=3><button onclick=\"javascript=window.location='package_events.php?pid=$pid&uid=$uid'\">$package_name</a></button></td></tr>";
        $info = $events[$pid];
        $count = count($info);
        $display = $count ?  $display."<tr><td>Event</td><td>Start Date</td><td>End Date</td></tr>" : $display;
        foreach($info as $key=>$field){
            $display = $display."<tr><td>".$field['event_name']."</td>"
                     . "<td>".date_standard($field['start_date'])."</td>"
                     . "<td>".date_standard($field['end_date'])."</td></tr>";
        }

  }

  $display = $display."</tbody></table>";
  echo $display;


?>


</body>
</html>
