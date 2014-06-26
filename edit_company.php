<html>
<head>
<title>Edit Company</title>
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
    //location.reload();
    window.location=window.location;
  }
$(function() {
        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        $('#billings').jPaginate({
                'max': 10,
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
	include 'billing_functions.php';
	include 'login_functions.php';
	include 'pdo_conn.php';
	include 'company_functions.php';
	include 'billingview_functions.php';
	include 'editbill_functions.php';

	$dbh = civicrmConnect();

	@$uid = $_GET["uid"]; 
	@$eventId = $_GET["eventId"];
	@$orgId = $_GET["orgId"];

	$eventDetails = getEventDetails($dbh,$eventId);
	$eventName = $eventDetails["event_name"];
	$eventStartDate = $eventDetails["start_date"];
	$eventEndDate = $eventDetails["end_date"];
	$eventTypeName = getEventTypeName($dbh,$eventId);
	$locationDetails = getEventLocation($dbh,$eventId);
	$eventLocation = formatEventLocation($locationDetails);
        $orgName = getCompanyNameByOrgId($orgId);

?>
<div id='eventDetails'>
 <table border = '1' width='100%'>
	<tr>
        	<th>Event Name</th><td><b><i><?=$eventName?></i></b></td>
	</tr>
	<tr>
		<th>Start Date</th><td><i><?=$eventStartDate?></i></td>
	</tr>
	<tr>
		<th>End Date</th><td><i><?=$eventEndDate?></i></td>
	</tr>
	<tr>
		<th>Event Type</th><td><i><?=$eventTypeName?></i></td>
	</tr>
	<tr>
		<th>Event Location</th><td><i><?=$eventLocation?></i></td>
	</tr>
	<tr>
		<th>Organization Name</th><td><i><?=$orgName?></i></td>
	</tr>
 </table>
</div>
</body>
</html>
